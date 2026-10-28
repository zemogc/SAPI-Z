<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaccion>
 */
class TransaccionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'propiedad_id' => Propiedad::factory(),
            'cliente_id' => Cliente::factory(),
            'tipo_transaccion' => 'venta',
            'fecha_transaccion' => fake()->dateTimeBetween('-11 months', 'now'),
            'monto_transaccion' => fake()->numberBetween(150, 900) * 1_000_000,
        ];
    }
}
