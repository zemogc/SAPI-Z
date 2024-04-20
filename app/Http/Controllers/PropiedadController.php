<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\propiedad; 
use Illuminate\Support\Facades\DB;

class PropiedadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $propiedades = DB::table('propiedades')
            ->select('propiedades.*')
            ->get();
        return view('propiedades.index', ['propiedades'=>$propiedades]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('propiedades.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Propiedad::create($request->all());
        return redirect()->route('propiedades.index')->with('success', 'Propiedad creada correctamente.');
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
        $propiedad->update($request->all());
        return redirect()->route('propiedades.index')->with('success', 'Propiedad actualizada correctamente.');
    }

    /**
     */
    public function destroy(string $id)
    {
        $propiedad = Propiedad::find($id);
        $propiedad->delete();
        return redirect()->route('propiedades.index')->with('success', 'Propiedad eliminada correctamente.');
    }
}
