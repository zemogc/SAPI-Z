<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_completo_de_clientes(): void
    {
        $user = User::factory()->create();
        $datos = [
            'nombre' => 'Laura',
            'apellido' => 'Restrepo',
            'telefono' => '+57 315 555 1234',
            'email' => 'laura@correo.com',
            'tipo' => 'comprador',
        ];

        $this->actingAs($user)->post(route('clientes.store'), $datos)->assertRedirect(route('clientes.index'));
        $cliente = Cliente::sole();

        $this->actingAs($user)->get(route('clientes.index', ['q' => 'Restrepo']))->assertOk()->assertSee('Laura Restrepo');

        $this->actingAs($user)->patch(route('clientes.update', $cliente), [...$datos, 'tipo' => 'vendedor'])->assertRedirect();
        $this->assertSame('vendedor', $cliente->fresh()->tipo);

        $this->actingAs($user)->delete(route('clientes.destroy', $cliente))->assertRedirect(route('clientes.index'));
        $this->assertModelMissing($cliente);
    }

    public function test_rechaza_datos_invalidos(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('clientes.store'), ['email' => 'no-es-correo', 'telefono' => 'abc', 'tipo' => 'otro'])
            ->assertSessionHasErrors(['nombre', 'apellido', 'email', 'telefono', 'tipo']);
    }

    public function test_no_elimina_cliente_con_transacciones(): void
    {
        $transaccion = Transaccion::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('clientes.destroy', $transaccion->cliente))
            ->assertSessionHas('error');

        $this->assertModelExists($transaccion->cliente);
    }
}
