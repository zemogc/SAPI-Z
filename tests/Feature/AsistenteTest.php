<?php

namespace Tests\Feature;

use App\Models\Propiedad;
use App\Models\User;
use App\Services\IA\ErrorIA;
use App\Services\IA\ProveedorIA;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function proveedorFalso(bool $disponible = true, ?\Throwable $falla = null): ProveedorFalso
    {
        $proveedor = new ProveedorFalso($disponible, $falla);
        $this->app->instance(ProveedorIA::class, $proveedor);

        return $proveedor;
    }

    private function crearComparables(): void
    {
        foreach ([80 => 400_000_000, 100 => 520_000_000, 120 => 600_000_000, 90 => 450_000_000] as $tamano => $precio) {
            Propiedad::factory()->create(['tipo' => 'apartamento', 'ciudad' => 'Cali', 'tamano' => $tamano, 'precio' => $precio]);
        }
    }

    public function test_la_pagina_del_asistente_indica_el_modo(): void
    {
        $this->proveedorFalso(disponible: false);

        $this->actingAs($this->user)->get(route('asistente.index'))
            ->assertOk()
            ->assertSee('Modo local');
    }

    public function test_redacta_la_descripcion_con_ia(): void
    {
        $proveedor = $this->proveedorFalso();
        $proveedor->respuestaTexto = 'Apartamento luminoso en Granada.';

        $this->actingAs($this->user)->postJson(route('asistente.describir'), [
            'tipo' => 'Apartamento', 'ciudad' => 'Cali', 'tamano' => 85, 'numero_habitaciones' => 3,
            'direccion' => 'Avenida 6N # 25-40, Granada',
        ])->assertOk()->assertJson(['descripcion' => 'Apartamento luminoso en Granada.', 'ia' => true]);

        $this->assertStringContainsString('Granada', $proveedor->ultimoMensaje);
        $this->assertStringContainsString('85 m²', $proveedor->ultimoMensaje);
    }

    public function test_redacta_la_descripcion_en_modo_local(): void
    {
        $this->proveedorFalso(disponible: false);

        $this->actingAs($this->user)->postJson(route('asistente.describir'), [
            'tipo' => 'casa', 'ciudad' => 'Medellín', 'tamano' => 180, 'numero_habitaciones' => 4, 'banos' => 3,
            'direccion' => 'Calle 10 # 43-12, El Poblado',
        ])->assertOk()
            ->assertJsonPath('ia', false)
            ->assertJsonPath('descripcion', fn ($texto) => str_contains($texto, 'El Poblado, Medellín') && str_contains($texto, '4 habitaciones'));
    }

    public function test_estima_precio_localmente_con_comparables(): void
    {
        $this->proveedorFalso(disponible: false);
        $this->crearComparables();

        // Precios por m²: 5.000.000, 5.000.000, 5.200.000 y 5.000.000 => mediana 5.000.000
        $this->actingAs($this->user)->postJson(route('asistente.estimar'), [
            'tipo' => 'apartamento', 'ciudad' => 'Cali', 'tamano' => 100,
        ])->assertOk()->assertJson([
            'comparables' => 4,
            'sugerido' => 500_000_000,
            'fuente' => 'local',
        ]);
    }

    public function test_estima_precio_con_ia_usando_salida_estructurada(): void
    {
        $proveedor = $this->proveedorFalso();
        $proveedor->respuestaJson = ['minimo' => 480_400_000, 'sugerido' => 505_000_000, 'maximo' => 530_000_000, 'explicacion' => 'Similar a los de Cali.'];
        $this->crearComparables();

        $this->actingAs($this->user)->postJson(route('asistente.estimar'), [
            'tipo' => 'apartamento', 'ciudad' => 'Cali', 'tamano' => 100,
        ])->assertOk()->assertJson([
            'minimo' => 480_000_000,
            'sugerido' => 505_000_000,
            'fuente' => 'ia',
            'explicacion' => 'Similar a los de Cali.',
        ]);

        $this->assertSame(['minimo', 'sugerido', 'maximo', 'explicacion'], $proveedor->ultimoEsquema['required']);
    }

    public function test_estimar_requiere_tipo_y_area(): void
    {
        $this->proveedorFalso();

        $this->actingAs($this->user)->postJson(route('asistente.estimar'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tipo', 'tamano']);
    }

    public function test_responde_preguntas_con_el_contexto_de_la_cartera(): void
    {
        $proveedor = $this->proveedorFalso();
        $proveedor->respuestaTexto = "Tienes **1** propiedad en Cali.";
        Propiedad::factory()->create(['direccion' => 'Calle 5 # 38-25, San Fernando', 'ciudad' => 'Cali']);

        $this->actingAs($this->user)->postJson(route('asistente.preguntar'), [
            'pregunta' => '¿Cuántas propiedades tengo en Cali?',
            'historial' => [
                ['role' => 'user', 'content' => 'Hola'],
                ['role' => 'assistant', 'content' => '¡Hola! ¿En qué te ayudo?'],
            ],
        ])->assertOk()
            ->assertJsonPath('html', "<p>Tienes <strong>1</strong> propiedad en Cali.</p>\n");

        $this->assertStringContainsString('San Fernando', $proveedor->ultimoContexto);
        $this->assertCount(3, $proveedor->ultimosMensajes);
        $this->assertSame('user', $proveedor->ultimosMensajes[2]['role']);
    }

    public function test_el_markdown_de_la_respuesta_no_permite_html(): void
    {
        $proveedor = $this->proveedorFalso();
        $proveedor->respuestaTexto = '<script>alert(1)</script> hola';

        $this->actingAs($this->user)->postJson(route('asistente.preguntar'), ['pregunta' => 'x'])
            ->assertOk()
            ->assertJsonPath('html', fn ($html) => ! str_contains($html, '<script>'));
    }

    public function test_informa_cuando_el_proveedor_falla(): void
    {
        $this->proveedorFalso(falla: new ErrorIA('El asistente está saturado.'));

        $this->actingAs($this->user)->postJson(route('asistente.preguntar'), ['pregunta' => 'Hola'])
            ->assertStatus(503)
            ->assertJson(['message' => 'El asistente está saturado.']);
    }

    public function test_invitados_no_pueden_usar_el_asistente(): void
    {
        $this->postJson(route('asistente.preguntar'), ['pregunta' => 'Hola'])->assertUnauthorized();
    }
}

class ProveedorFalso implements ProveedorIA
{
    public string $respuestaTexto = 'Respuesta de prueba';

    public array $respuestaJson = [];

    public string $ultimoMensaje = '';

    public string $ultimoContexto = '';

    public array $ultimosMensajes = [];

    public array $ultimoEsquema = [];

    public function __construct(private bool $disponible = true, private ?\Throwable $falla = null)
    {
    }

    public function disponible(): bool
    {
        return $this->disponible;
    }

    public function texto(string $instrucciones, array $mensajes, string $contexto = '', int $maxTokens = 4000): string
    {
        if ($this->falla) {
            throw $this->falla;
        }

        $this->ultimosMensajes = $mensajes;
        $this->ultimoMensaje = end($mensajes)['content'];
        $this->ultimoContexto = $contexto;

        return $this->respuestaTexto;
    }

    public function json(string $instrucciones, string $prompt, array $esquema, int $maxTokens = 4000): array
    {
        $this->ultimoMensaje = $prompt;
        $this->ultimoEsquema = $esquema;

        return $this->respuestaJson;
    }
}
