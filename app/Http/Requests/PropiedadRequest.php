<?php

namespace App\Http\Requests;

use App\Models\Propiedad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropiedadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo' => $this->tipo ? mb_strtolower($this->tipo) : null,
            'precio' => static::limpiarNumero($this->precio),
            'tamano' => static::limpiarNumero($this->tamano),
        ]);
    }

    public function rules(): array
    {
        return [
            'direccion' => ['required', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:120'],
            'tipo' => ['required', Rule::in(Propiedad::TIPOS)],
            'tamano' => ['required', 'numeric', 'min:1', 'max:100000'],
            'numero_habitaciones' => ['required', 'integer', 'min:0', 'max:50'],
            'banos' => ['nullable', 'integer', 'min:0', 'max:30'],
            'precio' => ['required', 'numeric', 'min:1'],
            'estado' => ['required', Rule::in(Propiedad::ESTADOS)],
            'descripcion' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'direccion' => 'dirección',
            'tamano' => 'tamaño (m²)',
            'numero_habitaciones' => 'número de habitaciones',
            'banos' => 'baños',
            'descripcion' => 'descripción',
        ];
    }

    /**
     * Acepta montos escritos como "350.000.000" o "$ 350,000,000".
     */
    public static function limpiarNumero(mixed $valor): mixed
    {
        if (! is_string($valor)) {
            return $valor;
        }

        $limpio = preg_replace('/[^0-9]/', '', $valor);

        return $limpio === '' ? null : $limpio;
    }
}
