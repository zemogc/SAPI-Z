<?php

namespace App\Http\Requests;

use App\Models\Transaccion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransaccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'monto_transaccion' => PropiedadRequest::limpiarNumero($this->monto_transaccion),
        ]);
    }

    public function rules(): array
    {
        return [
            'propiedad_id' => ['required', 'exists:propiedades,id'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'tipo_transaccion' => ['required', Rule::in(Transaccion::TIPOS)],
            'fecha_transaccion' => ['required', 'date', 'before_or_equal:today'],
            'monto_transaccion' => ['required', 'numeric', 'min:1'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'propiedad_id' => 'propiedad',
            'cliente_id' => 'cliente',
            'tipo_transaccion' => 'tipo de transacción',
            'fecha_transaccion' => 'fecha',
            'monto_transaccion' => 'monto',
        ];
    }
}
