<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Propiedad;
use App\Models\Transaccion;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $inicio = now()->startOfMonth()->subMonths(11);

        $transacciones = Transaccion::with('propiedad')
            ->whereDate('fecha_transaccion', '>=', $inicio)
            ->get();

        $ventas = $transacciones->whereIn('tipo_transaccion', ['venta', 'compra']);
        $arriendos = $transacciones->where('tipo_transaccion', 'arrendamiento');

        // Volumen de ventas por mes en los últimos 12 meses (incluye meses sin ventas).
        $meses = collect(range(0, 11))->map(fn ($i) => $inicio->copy()->addMonths($i));
        $ventasPorMes = $meses->map(fn (Carbon $mes) => [
            'mes' => ucfirst($mes->translatedFormat('M y')),
            'total' => (float) $ventas->filter(fn ($t) => $t->fecha_transaccion->isSameMonth($mes))->sum('monto_transaccion'),
            'operaciones' => $ventas->filter(fn ($t) => $t->fecha_transaccion->isSameMonth($mes))->count(),
        ]);

        $porEstado = Propiedad::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');

        $precioM2PorCiudad = Propiedad::whereNotNull('ciudad')->where('tamano', '>', 0)
            ->get(['ciudad', 'precio', 'tamano'])
            ->groupBy('ciudad')
            ->map(fn ($grupo) => round($grupo->avg(fn ($p) => $p->precio / $p->tamano)))
            ->sortDesc();

        $disponibles = Propiedad::disponibles();

        return view('dashboard', [
            'kpis' => [
                'inventario' => (float) (clone $disponibles)->sum('precio'),
                'disponibles' => (clone $disponibles)->count(),
                'propiedades' => $porEstado->sum(),
                'ventas' => (float) $ventas->sum('monto_transaccion'),
                'operaciones' => $ventas->count(),
                'ticket' => $ventas->count() ? (float) $ventas->avg('monto_transaccion') : 0,
                'canon' => (float) $arriendos->sum('monto_transaccion'),
                'arriendos' => $arriendos->count(),
                'clientes' => Cliente::count(),
            ],
            'porEstado' => collect(['vendida', 'arrendada', 'disponible'])->mapWithKeys(fn ($e) => [$e => (int) ($porEstado[$e] ?? 0)]),
            'ventasPorMes' => $ventasPorMes,
            'precioM2PorCiudad' => $precioM2PorCiudad,
            'recientes' => Transaccion::with(['propiedad', 'cliente'])->latest('fecha_transaccion')->latest('id')->take(6)->get(),
            'destacadas' => Propiedad::disponibles()->orderByDesc('precio')->take(4)->get(),
        ]);
    }
}
