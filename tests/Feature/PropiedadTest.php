<?php

namespace Tests\Feature;

use App\Models\Propiedad;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropiedadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_invitados_son_redirigidos_al_login(): void
    {
        $this->get(route('propiedades.index'))->assertRedirect(route('login'));
    }

    public function test_listado_muestra_y_filtra_propiedades(): void
    {
        Propiedad::factory()->create(['direccion' => 'Calle 5 # 38-25, San Fernando', 'tipo' => 'casa']);
        Propiedad::factory()->create(['direccion' => 'Carrera 7 # 72-10, Chapinero', 'tipo' => 'apartamento']);

        $this->actingAs($this->user)
            ->get(route('propiedades.index', ['tipo' => 'casa']))
            ->assertOk()
            ->assertSee('San Fernando')
            ->assertDontSee('Chapinero');

        $this->actingAs($this->user)
            ->get(route('propiedades.index', ['q' => 'Chapinero']))
            ->assertSee('Chapinero')
            ->assertDontSee('San Fernando');
    }

    public function test_crea_propiedad_aceptando_precio_con_separadores(): void
    {
        $respuesta = $this->actingAs($this->user)->post(route('propiedades.store'), [
            'direccion' => 'Avenida 6N # 25-40, Granada',
            'ciudad' => 'Cali',
            'tipo' => 'Apartamento',
            'tamano' => '82',
            'numero_habitaciones' => 3,
            'banos' => 2,
            'precio' => '$ 420.000.000',
            'estado' => 'disponible',
        ]);

        $propiedad = Propiedad::sole();
        $respuesta->assertRedirect(route('propiedades.show', $propiedad));
        $this->assertSame('apartamento', $propiedad->tipo);
        $this->assertEquals(420000000, (float) $propiedad->precio);
    }

    public function test_valida_campos_obligatorios(): void
    {
        $this->actingAs($this->user)
            ->post(route('propiedades.store'), ['tipo' => 'castillo'])
            ->assertSessionHasErrors(['direccion', 'tipo', 'tamano', 'precio', 'estado']);

        $this->assertDatabaseCount('propiedades', 0);
    }

    public function test_muestra_detalle_y_actualiza(): void
    {
        $propiedad = Propiedad::factory()->create();

        $this->actingAs($this->user)->get(route('propiedades.show', $propiedad))
            ->assertOk()
            ->assertSee($propiedad->direccion);

        $datos = $propiedad->only(['direccion', 'ciudad', 'tipo', 'tamano', 'numero_habitaciones', 'banos', 'estado']);

        $this->actingAs($this->user)
            ->patch(route('propiedades.update', $propiedad), [...$datos, 'precio' => '500.000.000'])
            ->assertRedirect(route('propiedades.show', $propiedad));

        $this->assertEquals(500000000, (float) $propiedad->fresh()->precio);
    }

    public function test_no_elimina_propiedad_con_transacciones(): void
    {
        $transaccion = Transaccion::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('propiedades.destroy', $transaccion->propiedad))
            ->assertSessionHas('error');

        $this->assertModelExists($transaccion->propiedad);
    }

    public function test_elimina_propiedad_sin_transacciones(): void
    {
        $propiedad = Propiedad::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('propiedades.destroy', $propiedad))
            ->assertRedirect(route('propiedades.index'));

        $this->assertModelMissing($propiedad);
    }
}
