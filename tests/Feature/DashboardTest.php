<?php

namespace Tests\Feature;

use App\Models\Propiedad;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_calcula_los_indicadores_del_panel(): void
    {
        Propiedad::factory()->create(['precio' => 300000000]);
        Transaccion::factory()->create(['tipo_transaccion' => 'venta', 'monto_transaccion' => 500000000, 'fecha_transaccion' => now()]);
        Transaccion::factory()->create(['tipo_transaccion' => 'arrendamiento', 'monto_transaccion' => 3000000, 'fecha_transaccion' => now()]);

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('kpis', fn ($kpis) => $kpis['ventas'] === 500000000.0
                && $kpis['canon'] === 3000000.0
                && $kpis['disponibles'] === 1
                && $kpis['inventario'] === 300000000.0)
            ->assertViewHas('ventasPorMes', fn ($meses) => $meses->count() === 12 && $meses->last()['total'] === 500000000.0)
            ->assertViewHas('porEstado', fn ($estados) => $estados->all() === ['vendida' => 1, 'arrendada' => 1, 'disponible' => 1]);
    }

    public function test_el_panel_funciona_sin_datos(): void
    {
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk();
    }

    public function test_exporta_transacciones_filtradas_en_csv(): void
    {
        Transaccion::factory()->create(['tipo_transaccion' => 'venta', 'notas' => 'Pago de contado']);
        Transaccion::factory()->create(['tipo_transaccion' => 'arrendamiento', 'notas' => 'Contrato a un año']);

        $respuesta = $this->actingAs(User::factory()->create())
            ->get(route('transacciones.exportar', ['tipo' => 'venta']));

        $respuesta->assertOk()->assertDownload();
        $csv = $respuesta->streamedContent();
        $this->assertStringContainsString('Pago de contado', $csv);
        $this->assertStringNotContainsString('Contrato a un año', $csv);
    }

    public function test_la_portada_es_publica(): void
    {
        $this->get('/')->assertOk()->assertSee('SAPI');
    }
}
