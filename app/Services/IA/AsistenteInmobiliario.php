<?php

namespace App\Services\IA;

use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use Illuminate\Support\Collection;

/**
 * Asistente inmobiliario: redacta anuncios, estima precios y responde
 * preguntas sobre la cartera. Si no hay proveedor de IA configurado,
 * cada función tiene un modo local que sigue siendo útil.
 */
class AsistenteInmobiliario
{
    private const PERSONA = 'Eres el asistente de SAPI-Z, un sistema de gestión inmobiliaria en Colombia. '
        .'Escribes en español neutro de Colombia, con tono profesional y cercano. '
        .'Los precios están en pesos colombianos (COP) y se escriben con puntos de miles, por ejemplo $ 350.000.000.';

    public function __construct(private ProveedorIA $proveedor)
    {
    }

    public function usaIA(): bool
    {
        return $this->proveedor->disponible();
    }

    /**
     * Redacta la descripción de anuncio para un inmueble.
     *
     * @param  array<string, mixed>  $datos  Campos del formulario de propiedad.
     */
    public function describir(array $datos): string
    {
        if (! $this->usaIA()) {
            return $this->describirLocal($datos);
        }

        $instrucciones = self::PERSONA.' Redactas descripciones de anuncios inmobiliarios atractivas y honestas. '
            .'No inventes características que no estén en los datos (piscina, vista, parqueadero, etc.); '
            .'si un dato falta, simplemente no lo menciones. Entrega solo el texto del anuncio, sin títulos ni markdown, '
            .'en dos párrafos cortos (máximo 110 palabras en total).';

        $prompt = "Datos del inmueble:\n".$this->fichaTexto($datos);

        return trim($this->proveedor->texto($instrucciones, [['role' => 'user', 'content' => $prompt]], maxTokens: 2000));
    }

    /**
     * Estima un rango de precio a partir de comparables del inventario.
     *
     * @param  array<string, mixed>  $datos
     * @return array{minimo: int, sugerido: int, maximo: int, explicacion: string, comparables: int, precio_m2: int, fuente: string}
     */
    public function estimarPrecio(array $datos, ?int $excluirId = null): array
    {
        $tamano = (float) ($datos['tamano'] ?? 0);
        $comparables = $this->comparables($datos, $excluirId);
        $preciosM2 = $comparables->map(fn (Propiedad $p) => $p->precio / $p->tamano)->sort()->values();

        $base = [
            'comparables' => $comparables->count(),
            'precio_m2' => (int) round($this->percentil($preciosM2, 0.5)),
        ];

        $local = [
            'minimo' => $this->redondear($this->percentil($preciosM2, 0.25) * $tamano),
            'sugerido' => $this->redondear($this->percentil($preciosM2, 0.5) * $tamano),
            'maximo' => $this->redondear($this->percentil($preciosM2, 0.75) * $tamano),
        ];

        if ($comparables->isEmpty() || $tamano <= 0) {
            return [...$base, 'minimo' => 0, 'sugerido' => 0, 'maximo' => 0, 'fuente' => 'local',
                'explicacion' => 'No hay suficientes propiedades comparables en el inventario para estimar un precio.'];
        }

        if (! $this->usaIA()) {
            return [...$base, ...$local, 'fuente' => 'local', 'explicacion' => sprintf(
                'Rango calculado con %d propiedades comparables (%s). El precio sugerido usa la mediana de %s por m².',
                $comparables->count(),
                $this->descripcionComparables($datos, $comparables),
                cop($base['precio_m2'])
            )];
        }

        $tabla = $comparables->map(fn (Propiedad $p) => sprintf(
            '- %s, %s | %s | %s m² | %d hab | %s baños | %s | %s/m² | %s',
            $p->direccion, $p->ciudad, $p->tipo, (float) $p->tamano, $p->numero_habitaciones, $p->banos ?? '?',
            cop($p->precio), cop($p->precio / $p->tamano), $p->estado
        ))->implode("\n");

        $instrucciones = self::PERSONA.' Eres analista de avalúos. Estimas precios de venta usando solo los comparables que se te dan. '
            .'Ajusta por tamaño, habitaciones, baños y barrio cuando los datos lo justifiquen. '
            .'La explicación debe tener máximo 3 frases y mencionar los comparables más relevantes.';

        $prompt = "Inmueble a valorar:\n".$this->fichaTexto($datos)
            ."\n\nComparables del inventario:\n{$tabla}"
            ."\n\nReferencia estadística: percentil 25 = ".cop($local['minimo']).', mediana = '.cop($local['sugerido']).', percentil 75 = '.cop($local['maximo']).'.'
            ."\nDevuelve el rango en pesos (números enteros, sin puntos).";

        $respuesta = $this->proveedor->json($instrucciones, $prompt, [
            'type' => 'object',
            'properties' => [
                'minimo' => ['type' => 'integer'],
                'sugerido' => ['type' => 'integer'],
                'maximo' => ['type' => 'integer'],
                'explicacion' => ['type' => 'string'],
            ],
            'required' => ['minimo', 'sugerido', 'maximo', 'explicacion'],
            'additionalProperties' => false,
        ]);

        return [
            ...$base,
            'minimo' => $this->redondear($respuesta['minimo']),
            'sugerido' => $this->redondear($respuesta['sugerido']),
            'maximo' => $this->redondear($respuesta['maximo']),
            'explicacion' => $respuesta['explicacion'],
            'fuente' => 'ia',
        ];
    }

    /**
     * Responde una pregunta sobre la cartera usando sus datos como contexto.
     *
     * @param  array<int, array{role: string, content: string}>  $historial
     */
    public function responder(string $pregunta, array $historial = []): string
    {
        if (! $this->usaIA()) {
            return $this->responderLocal($pregunta);
        }

        $instrucciones = self::PERSONA.' Respondes preguntas del agente sobre su cartera inmobiliaria usando únicamente '
            .'los datos de la cartera incluidos en este mensaje. Si la respuesta no está en los datos, dilo con claridad. '
            .'Sé breve y concreto; usa listas o tablas en markdown cuando ayuden a comparar. '
            .'Cuando recomiendes acciones, básalas en los números.';

        $mensajes = collect($historial)
            ->filter(fn ($m) => in_array($m['role'] ?? null, ['user', 'assistant'], true) && filled($m['content'] ?? null))
            ->take(-10)
            ->map(fn ($m) => ['role' => $m['role'], 'content' => (string) $m['content']])
            ->values()
            ->push(['role' => 'user', 'content' => $pregunta])
            ->all();

        return trim($this->proveedor->texto($instrucciones, $mensajes, $this->contextoCartera()));
    }

    /**
     * Resumen de la cartera en texto plano para dar contexto al modelo.
     */
    public function contextoCartera(): string
    {
        $propiedades = Propiedad::orderBy('id')->get();
        $transacciones = Transaccion::with(['propiedad', 'cliente'])->orderBy('fecha_transaccion')->get();

        $lineasPropiedades = $propiedades->map(fn (Propiedad $p) => implode(' | ', [
            "#{$p->id}", $p->direccion, $p->ciudad ?? '-', $p->tipo, (float) $p->tamano.' m²',
            $p->numero_habitaciones.' hab', ($p->banos ?? '-').' baños', cop($p->precio), $p->estado,
        ]))->implode("\n");

        $lineasTransacciones = $transacciones->map(fn (Transaccion $t) => implode(' | ', [
            $t->fecha_transaccion->toDateString(), $t->tipo_transaccion, '#'.$t->propiedad_id.' '.($t->propiedad?->direccion ?? ''),
            $t->propiedad?->ciudad ?? '-', $t->cliente?->nombre_completo ?? '-', cop($t->monto_transaccion),
        ]))->implode("\n");

        $clientes = Cliente::selectRaw('tipo, count(*) as total')->groupBy('tipo')->pluck('total', 'tipo')
            ->map(fn ($total, $tipo) => "{$tipo}: {$total}")->implode(', ');

        return "<cartera fecha_actual=\"".now()->toDateString()."\">\n"
            ."<propiedades formato=\"id | dirección | ciudad | tipo | área | habitaciones | baños | precio | estado\">\n{$lineasPropiedades}\n</propiedades>\n"
            ."<transacciones formato=\"fecha | tipo | propiedad | ciudad | cliente | monto\">\n{$lineasTransacciones}\n</transacciones>\n"
            ."<clientes>{$clientes}</clientes>\n"
            .'</cartera>';
    }

    /**
     * Propiedades parecidas: mismo tipo y ciudad; si son pocas, se amplía a todo el tipo.
     *
     * @return Collection<int, Propiedad>
     */
    public function comparables(array $datos, ?int $excluirId = null): Collection
    {
        $consulta = fn () => Propiedad::query()
            ->where('tamano', '>', 0)
            ->when($excluirId, fn ($q) => $q->whereKeyNot($excluirId))
            ->when($datos['tipo'] ?? null, fn ($q, $tipo) => $q->where('tipo', mb_strtolower($tipo)));

        $mismaCiudad = filled($datos['ciudad'] ?? null)
            ? $consulta()->where('ciudad', $datos['ciudad'])->get()
            : collect();

        return $mismaCiudad->count() >= 3 ? $mismaCiudad : $consulta()->get();
    }

    private function describirLocal(array $datos): string
    {
        $tipo = mb_strtolower($datos['tipo'] ?? 'inmueble');
        $articulo = $tipo === 'casa' ? 'Hermosa casa' : ($tipo === 'apartamento' ? 'Cómodo apartamento' : 'Amplio local comercial');
        $ubicacion = collect([$this->barrio($datos['direccion'] ?? ''), $datos['ciudad'] ?? null])->filter()->implode(', ');

        $partes = [$articulo.($ubicacion ? " ubicado en {$ubicacion}," : '').' con '.((float) ($datos['tamano'] ?? 0)).' m² de área.'];

        $contar = fn (int $n, string $singular, string $plural) => $n.' '.($n === 1 ? $singular : $plural);

        if (($datos['numero_habitaciones'] ?? 0) > 0) {
            $partes[] = 'Cuenta con '.$contar((int) $datos['numero_habitaciones'], 'habitación', 'habitaciones')
                .(($datos['banos'] ?? 0) > 0 ? ' y '.$contar((int) $datos['banos'], 'baño', 'baños') : '').', ideal para tu familia.';
        } elseif ($tipo === 'local comercial') {
            $partes[] = 'Espacio versátil para tu negocio, con buena visibilidad y fácil acceso.';
        }

        $partes[] = 'Agenda tu visita y conoce todos sus detalles.';

        return implode(' ', $partes);
    }

    private function responderLocal(string $pregunta): string
    {
        $disponibles = Propiedad::disponibles()->count();
        $ventas = Transaccion::whereIn('tipo_transaccion', ['venta', 'compra'])->whereDate('fecha_transaccion', '>=', now()->subYear());

        return "El asistente de IA no está configurado, así que no puedo interpretar tu pregunta. "
            ."Agrega `ANTHROPIC_API_KEY` en el archivo `.env` para activarlo.\n\n"
            ."Mientras tanto, este es un resumen de tu cartera:\n\n"
            .'- **Propiedades:** '.Propiedad::count()." ({$disponibles} disponibles)\n"
            .'- **Ventas del último año:** '.(clone $ventas)->count().' por '.cop((clone $ventas)->sum('monto_transaccion'))."\n"
            .'- **Clientes:** '.Cliente::count();
    }

    private function fichaTexto(array $datos): string
    {
        return collect([
            'Tipo' => $datos['tipo'] ?? null,
            'Dirección' => $datos['direccion'] ?? null,
            'Ciudad' => $datos['ciudad'] ?? null,
            'Área' => isset($datos['tamano']) ? (float) $datos['tamano'].' m²' : null,
            'Habitaciones' => $datos['numero_habitaciones'] ?? null,
            'Baños' => $datos['banos'] ?? null,
            'Precio' => ! empty($datos['precio']) ? cop($datos['precio']) : null,
            'Notas del agente' => $datos['descripcion'] ?? null,
        ])->filter(fn ($v) => filled($v))->map(fn ($v, $k) => "- {$k}: {$v}")->implode("\n");
    }

    private function descripcionComparables(array $datos, Collection $comparables): string
    {
        $ciudades = $comparables->pluck('ciudad')->unique();

        return ($datos['tipo'] ?? 'mismo tipo').($ciudades->count() === 1 ? ' en '.$ciudades->first() : ' en varias ciudades');
    }

    private function barrio(string $direccion): ?string
    {
        return str_contains($direccion, ',') ? trim(last(explode(',', $direccion))) : null;
    }

    private function percentil(Collection $ordenados, float $p): float
    {
        if ($ordenados->isEmpty()) {
            return 0;
        }

        $posicion = ($ordenados->count() - 1) * $p;
        $abajo = (int) floor($posicion);
        $arriba = (int) ceil($posicion);

        return $ordenados[$abajo] + ($ordenados[$arriba] - $ordenados[$abajo]) * ($posicion - $abajo);
    }

    private function redondear(float|int $valor): int
    {
        return (int) (round($valor / 1_000_000) * 1_000_000);
    }
}
