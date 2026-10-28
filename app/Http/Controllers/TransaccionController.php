<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransaccionRequest;
use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransaccionController extends Controller
{
    public function index(Request $request)
    {
        $transacciones = $this->filtrar(Transaccion::with(['propiedad', 'cliente']), $request)
            ->latest('fecha_transaccion')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $total = $this->filtrar(Transaccion::query(), $request)->sum('monto_transaccion');

        return view('transacciones.index', compact('transacciones', 'total'));
    }

    /**
     * Descarga en CSV las transacciones que coinciden con los filtros del listado.
     */
    public function exportar(Request $request): StreamedResponse
    {
        $transacciones = $this->filtrar(Transaccion::with(['propiedad', 'cliente']), $request)
            ->latest('fecha_transaccion')
            ->get();

        return response()->streamDownload(function () use ($transacciones) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF"); // BOM para que Excel respete las tildes
            fputcsv($salida, ['ID', 'Fecha', 'Tipo', 'Propiedad', 'Ciudad', 'Cliente', 'Email', 'Monto', 'Notas'], ';');

            foreach ($transacciones as $t) {
                fputcsv($salida, [
                    $t->id,
                    $t->fecha_transaccion->toDateString(),
                    $t->tipo_transaccion,
                    $t->propiedad?->direccion,
                    $t->propiedad?->ciudad,
                    $t->cliente?->nombre_completo,
                    $t->cliente?->email,
                    (int) $t->monto_transaccion,
                    $t->notas,
                ], ';');
            }

            fclose($salida);
        }, 'transacciones-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filtrar(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->query('tipo'), fn ($q, $tipo) => $q->where('tipo_transaccion', $tipo))
            ->when($request->query('desde'), fn ($q, $desde) => $q->whereDate('fecha_transaccion', '>=', $desde))
            ->when($request->query('hasta'), fn ($q, $hasta) => $q->whereDate('fecha_transaccion', '<=', $hasta));
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
