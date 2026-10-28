<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropiedadRequest;
use App\Models\Propiedad;
use Illuminate\Http\Request;

class PropiedadController extends Controller
{
    public function index(Request $request)
    {
        $propiedades = Propiedad::query()
            ->buscar($request->query('q'))
            ->when($request->query('tipo'), fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->when($request->query('estado'), fn ($q, $estado) => $q->where('estado', $estado))
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('propiedades.index', compact('propiedades'));
    }

    public function create()
    {
        return view('propiedades.new', ['propiedad' => new Propiedad(['estado' => 'disponible'])]);
    }

    public function store(PropiedadRequest $request)
    {
        $propiedad = Propiedad::create($request->validated());

        return redirect()->route('propiedades.show', $propiedad)
            ->with('success', 'Propiedad creada correctamente.');
    }

    public function show(Propiedad $propiedad)
    {
        $propiedad->load(['transacciones' => fn ($q) => $q->with('cliente')->latest('fecha_transaccion')]);

        $similares = Propiedad::where('tipo', $propiedad->tipo)
            ->whereKeyNot($propiedad->id)
            ->latest('id')
            ->take(3)
            ->get();

        return view('propiedades.show', compact('propiedad', 'similares'));
    }

    public function edit(Propiedad $propiedad)
    {
        return view('propiedades.edit', compact('propiedad'));
    }

    public function update(PropiedadRequest $request, Propiedad $propiedad)
    {
        $propiedad->update($request->validated());

        return redirect()->route('propiedades.show', $propiedad)
            ->with('success', 'Propiedad actualizada correctamente.');
    }

    public function destroy(Propiedad $propiedad)
    {
        if ($propiedad->transacciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: la propiedad tiene transacciones registradas.');
        }

        $propiedad->delete();

        return redirect()->route('propiedades.index')
            ->with('success', 'Propiedad eliminada correctamente.');
    }
}
