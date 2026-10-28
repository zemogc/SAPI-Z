<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Genera una cartera de demostración: 36 propiedades, 24 clientes y
     * transacciones repartidas en los últimos doce meses.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Agente Demo',
            'email' => 'demo@sapiz.co',
        ]);

        $propiedades = Propiedad::factory(36)->create();
        $clientes = Cliente::factory(24)->create();

        $propiedades->random(18)->each(function (Propiedad $propiedad) use ($clientes) {
            $arriendo = fake()->boolean(35);

            Transaccion::factory()->create([
                'propiedad_id' => $propiedad->id,
                'cliente_id' => $clientes->random()->id,
                'tipo_transaccion' => $arriendo ? 'arrendamiento' : 'venta',
                'monto_transaccion' => $arriendo
                    ? round($propiedad->precio * 0.006 / 50_000) * 50_000
                    : round($propiedad->precio * fake()->randomFloat(2, 0.92, 1.03) / 1_000_000) * 1_000_000,
                'fecha_transaccion' => fake()->dateTimeBetween('-11 months', 'now'),
            ]);
        });
    }
}
