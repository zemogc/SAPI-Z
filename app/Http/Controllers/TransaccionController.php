<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransaccionRequest;
use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    public function index(Request $request)
    {
        $filtrar = fn ($query) => $query
            ->when($request->query('tipo'), fn ($q, $tipo) => $q->where('tipo_transaccion', $tipo))
            ->when($request->query('desde'), fn ($q, $desde) => $q->whereDate('fecha_transaccion', '>=', $desde))
            ->when($request->query('hasta'), fn ($q, $hasta) => $q->whereDate('fecha_transaccion', '<=', $hasta));

        $transacciones = $filtrar(Transaccion::with(['propiedad', 'cliente']))
            ->latest('fecha_transaccion')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $total = $filtrar(Transaccion::query())->sum('monto_transaccion');

        return view('transacciones.index', compact('transacciones', 'total'));
    }

    public function create(Request $request)
    {
        return view('transacciones.new', [
            'transaccion' => new Transaccion([
                'propiedad_id' => $request->query('propiedad'),
                'fecha_transaccion' => now(),
            ]),
            'propiedades' => Propiedad::orderBy('direccion')->get(),
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }

    public function store(TransaccionRequest $request)
    {
        Transaccion::create($request->validated());

        return redirect()->route('transacciones.index')->with('success', 'Transacción registrada correctamente.');
    }

    public function edit(Transaccion $transaccion)
    {
        return view('transacciones.edit', [
            'transaccion' => $transaccion,
            'propiedades' => Propiedad::orderBy('direccion')->get(),
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }

    public function update(TransaccionRequest $request, Transaccion $transaccion)
    {
        $transaccion->update($request->validated());

        return redirect()->route('transacciones.index')->with('success', 'Transacción actualizada correctamente.');
    }

    public function destroy(Transaccion $transaccion)
    {
        $transaccion->delete();

        return redirect()->route('transacciones.index')->with('success', 'Transacción eliminada correctamente.');
    }
}
