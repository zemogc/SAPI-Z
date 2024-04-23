<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transacciones = Transaccion::all(); 
        return view('transacciones.index', ['transacciones' => $transacciones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $transaccion = Transaccion::findOrFail($id);
        // Obtener propiedades y clientes disponibles
        $propiedades = Propiedad::all();
        $clientes = Cliente::all();
        return view('transacciones.edit', compact('transaccion', 'propiedades', 'clientes'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaccion = Transaccion::findOrFail($id);
    $transaccion->propiedad_id = $request->propiedad_id;
    $transaccion->cliente_id = $request->cliente_id;
    $transaccion->tipo_transaccion = $request->tipo_transaccion;
    $transaccion->fecha_transaccion = $request->fecha_transaccion;
    $transaccion->monto_transaccion = $request->monto_transaccion;
    $transaccion->save();
    return redirect()->route('transacciones.index')->with('success', 'Transacción actualizada correctamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
