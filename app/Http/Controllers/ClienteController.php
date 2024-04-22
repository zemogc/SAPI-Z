<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', ['clientes' => $clientes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.new');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $cliente = new Cliente();
    $cliente->nombre = $request->nombre;
    $cliente->apellido = $request->apellido;
    $cliente->telefono = $request->telefono;
    $cliente->email = $request->email;
    $cliente->tipo = $request->tipo;
    $cliente->save();

    
    return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'tipo' => 'required|in:comprador,vendedor,arrendatario',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente eliminado correctamente.');
    }
}
