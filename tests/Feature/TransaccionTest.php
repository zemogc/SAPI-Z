<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_venta_marca_la_propiedad_como_vendida(): void
    {
        $propiedad = Propiedad::factory()->create();
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())->post(route('transacciones.store'), [
            'propiedad_id' => $propiedad->id,
            'cliente_id' => $cliente->id,
            'tipo_transaccion' => 'venta',
            'fecha_transaccion' => now()->toDateString(),
            'monto_transaccion' => '380.000.000',
        ])->assertRedirect(route('transacciones.index'));

        $this->assertSame('vendida', $propiedad->fresh()->estado);
        $this->assertEquals(380000000, (float) Transaccion::sole()->monto_transaccion);
    }

    public function test_arrendamiento_y_eliminacion_recalculan_el_estado(): void
    {
        $transaccion = Transaccion::factory()->create(['tipo_transaccion' => 'arrendamiento']);
        $propiedad = $transaccion->propiedad;

        $this->assertSame('arrendada', $propiedad->fresh()->estado);

        $this->actingAs(User::factory()->create())->delete(route('transacciones.destroy', $transaccion));

        $this->assertSame('disponible', $propiedad->fresh()->estado);
    }

    public function test_cambiar_de_propiedad_libera_la_anterior(): void
    {
        $transaccion = Transaccion::factory()->create();
        $anterior = $transaccion->propiedad;
        $nueva = Propiedad::factory()->create();

        $this->actingAs(User::factory()->create())->put(route('transacciones.update', $transaccion), [
            'propiedad_id' => $nueva->id,
            'cliente_id' => $transaccion->cliente_id,
            'tipo_transaccion' => 'venta',
            'fecha_transaccion' => $transaccion->fecha_transaccion->toDateString(),
            'monto_transaccion' => 200000000,
        ])->assertRedirect();

        $this->assertSame('disponible', $anterior->fresh()->estado);
        $this->assertSame('vendida', $nueva->fresh()->estado);
    }

    public function test_no_acepta_fechas_futuras_ni_tipos_invalidos(): void
    {
        $this->actingAs(User::factory()->create())->post(route('transacciones.store'), [
            'fecha_transaccion' => now()->addWeek()->toDateString(),
            'tipo_transaccion' => 'permuta',
        ])->assertSessionHasErrors(['propiedad_id', 'cliente_id', 'tipo_transaccion', 'fecha_transaccion', 'monto_transaccion']);
    }

    public function test_listado_filtra_por_tipo_y_suma_el_total(): void
    {
        Transaccion::factory()->create(['tipo_transaccion' => 'venta', 'monto_transaccion' => 300000000]);
        Transaccion::factory()->create(['tipo_transaccion' => 'arrendamiento', 'monto_transaccion' => 2500000]);

        $this->actingAs(User::factory()->create())
            ->get(route('transacciones.index', ['tipo' => 'arrendamiento']))
            ->assertOk()
            ->assertViewHas('total', fn ($total) => (float) $total === 2500000.0);
    }
}
