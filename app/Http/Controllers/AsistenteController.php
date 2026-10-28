<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropiedadRequest;
use App\Models\Propiedad;
use App\Services\IA\AsistenteInmobiliario;
use App\Services\IA\ErrorIA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AsistenteController extends Controller
{
    public function __construct(private AsistenteInmobiliario $asistente)
    {
    }

    public function index()
    {
        return view('asistente.index', [
            'usaIA' => $this->asistente->usaIA(),
            'sugerencias' => [
                '¿Qué propiedades llevan más tiempo disponibles y cuál bajarías de precio?',
                'Resume las ventas del último trimestre por ciudad.',
                '¿Cuál es el precio promedio por m² de los apartamentos en Bogotá?',
                '¿Qué clientes han cerrado más negocios?',
            ],
        ]);
    }

    public function preguntar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'pregunta' => ['required', 'string', 'max:1000'],
            'historial' => ['array', 'max:20'],
            'historial.*.role' => ['required', Rule::in(['user', 'assistant'])],
            'historial.*.content' => ['required', 'string', 'max:8000'],
        ]);

        return $this->responder(function () use ($datos) {
            $respuesta = $this->asistente->responder($datos['pregunta'], $datos['historial'] ?? []);

            return [
                'respuesta' => $respuesta,
                'html' => Str::markdown($respuesta, ['html_input' => 'strip', 'allow_unsafe_links' => false]),
            ];
        });
    }

    public function describir(Request $request): JsonResponse
    {
        $datos = $this->datosPropiedad($request);

        return $this->responder(fn () => ['descripcion' => $this->asistente->describir($datos)]);
    }

    public function estimar(Request $request): JsonResponse
    {
        $datos = $this->datosPropiedad($request, requiereArea: true);

        return $this->responder(fn () => $this->asistente->estimarPrecio($datos, $request->integer('propiedad_id') ?: null));
    }

    /**
     * Valida los campos del formulario de propiedad que necesita el asistente.
     *
     * @return array<string, mixed>
     */
    private function datosPropiedad(Request $request, bool $requiereArea = false): array
    {
        $request->merge([
            'tipo' => $request->tipo ? mb_strtolower($request->tipo) : null,
            'precio' => PropiedadRequest::limpiarNumero($request->precio),
        ]);

        return $request->validate([
            'tipo' => ['required', Rule::in(Propiedad::TIPOS)],
            'tamano' => [$requiereArea ? 'required' : 'nullable', 'numeric', 'min:1'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:120'],
            'numero_habitaciones' => ['nullable', 'integer', 'min:0', 'max:50'],
            'banos' => ['nullable', 'integer', 'min:0', 'max:30'],
            'precio' => ['nullable', 'numeric'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
        ], [], ['tamano' => 'área', 'tipo' => 'tipo de inmueble']);
    }

    private function responder(callable $accion): JsonResponse
    {
        try {
            return response()->json([...$accion(), 'ia' => $this->asistente->usaIA()]);
        } catch (ErrorIA $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
