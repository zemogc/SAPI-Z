<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Propiedad; 
use Illuminate\Support\Facades\DB;

class PropiedadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $propiedades = Propiedad::all(); 
        return view('propiedades.index', ['propiedades' => $propiedades]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $propiedades = Propiedad::all(); 
        return view('propiedades.new');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //almacena camhoso en proppiedad
        $propiedad = new Propiedad();
        $propiedad->direccion = $request->direccion;
        $propiedad->tipo = $request->tipo;
        $propiedad->tamano = $request->tamaño;
        $propiedad->numero_habitaciones = $request->habitaciones;
        $propiedad->precio = $request->precio;
        $propiedad->estado = $request->estado;
        $propiedad->save();
        
        return redirect()->route('propiedades.index');
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $propiedad = Propiedad::find($id);
        return view('propiedades.edit', ['propiedad' => $propiedad]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $propiedad = Propiedad::find($id);
        $propiedad->direccion = $request->direccion;
        $propiedad->tipo = $request->tipo;
        $propiedad->tamano = $request->tamaño;
        $propiedad->numero_habitaciones = $request->habitaciones;
        $propiedad->precio = $request->precio;
        $propiedad->estado = $request->estado;


        $propiedad->save();

        $propiedad = DB::table('propiedades')
            ->select('propiedades.*')
            ->get();


        return redirect()->route('propiedades.index')->with('success', 'Propiedad actualizada correctamente.');
    }

    /**
     */
    public function destroy(string $id)
    {
        $propiedad = Propiedad::find($id);
        $propiedad->delete();

        $paquetes = DB::table('propiedades')
            ->select('propiedades.*')
            ->get();


        return redirect()->route('propiedades.index')->with('success', 'Propiedad eliminada correctamente.');
    }
}
