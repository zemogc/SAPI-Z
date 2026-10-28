<x-app-layout title="Panel">
    @php
        // Slots 1-3 de la paleta categórica validada (azul, naranja, aqua).
        $coloresEstado = ['vendida' => '#2a78d6', 'arrendada' => '#eb6834', 'disponible' => '#1baf7a'];
        $totalEstados = max(1, $porEstado->sum());
    @endphp

    <x-page-header :title="'Hola, '.explode(' ', Auth::user()->name)[0]" subtitle="Así va tu negocio inmobiliario en los últimos 12 meses.">
        <x-slot name="actions">
            <a href="{{ route('transacciones.create') }}" class="btn-secondary"><x-icon name="banknotes" class="h-4 w-4" /> Registrar negocio</a>
            <a href="{{ route('propiedades.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nueva propiedad</a>
        </x-slot>
    </x-page-header>

    {{-- KPIs --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Ventas (12 meses)', cop($kpis['ventas'], true), $kpis['operaciones'].' operaciones cerradas', 'trend', 'bg-brand-50 text-brand-700'],
            ['Inventario disponible', cop($kpis['inventario'], true), $kpis['disponibles'].' de '.$kpis['propiedades'].' propiedades', 'building', 'bg-sky-50 text-sky-700'],
            ['Ticket promedio', cop($kpis['ticket'], true), 'por venta cerrada', 'banknotes', 'bg-violet-50 text-violet-700'],
            ['Cánones de arriendo', cop($kpis['canon'], true), $kpis['arriendos'].' contratos · '.$kpis['clientes'].' clientes', 'calendar', 'bg-amber-50 text-amber-700'],
        ] as [$etiqueta, $valor, $detalle, $icono, $color])
            <div class="card p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">{{ $etiqueta }}</p>
                    <span class="rounded-xl p-2 {{ $color }}"><x-icon :name="$icono" class="h-5 w-5" /></span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $valor }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $detalle }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Ventas por mes --}}
        <section class="card p-6 xl:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-slate-900">Volumen de ventas por mes</h2>
                    <p class="text-sm text-slate-500">Suma de ventas y compras cerradas, en pesos.</p>
                </div>
            </div>
            <div class="relative mt-6 h-72">
                <canvas id="grafica-ventas" role="img" aria-label="Volumen de ventas por mes"></canvas>
            </div>
        </section>

        {{-- Estado del inventario --}}
        <section class="card p-6">
            <h2 class="font-semibold text-slate-900">Estado del inventario</h2>
            <p class="text-sm text-slate-500">{{ $kpis['propiedades'] }} propiedades registradas</p>

            <div class="mt-6 flex h-3 gap-0.5 overflow-hidden rounded-full" role="img" aria-label="Distribución por estado">
                @foreach ($porEstado as $estado => $cantidad)
                    @if ($cantidad)
                        <div class="h-full first:rounded-l-full last:rounded-r-full" style="width: {{ $cantidad / $totalEstados * 100 }}%; background: {{ $coloresEstado[$estado] }}" title="{{ ucfirst($estado) }}: {{ $cantidad }}"></div>
                    @endif
                @endforeach
            </div>

            <ul class="mt-6 space-y-4">
                @foreach ($porEstado as $estado => $cantidad)
                    <li class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-sm" style="background: {{ $coloresEstado[$estado] }}"></span>
                        <span class="flex-1 text-sm font-medium capitalize text-slate-700">{{ $estado }}</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $cantidad }}</span>
                        <span class="w-12 text-right text-xs text-slate-500">{{ round($cantidad / $totalEstados * 100) }}%</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                <span class="font-semibold text-slate-900">{{ round(($porEstado['vendida'] + $porEstado['arrendada']) / $totalEstados * 100) }}%</span>
                del inventario ya tiene un negocio cerrado.
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Precio por m² --}}
        <section class="card p-6">
            <h2 class="font-semibold text-slate-900">Precio promedio por m²</h2>
            <p class="text-sm text-slate-500">Por ciudad, sobre todo el inventario.</p>
            <div class="relative mt-6" style="height: {{ max(160, $precioM2PorCiudad->count() * 44) }}px">
                <canvas id="grafica-m2" role="img" aria-label="Precio promedio por metro cuadrado por ciudad"></canvas>
            </div>
        </section>

        {{-- Actividad reciente --}}
        <section class="card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="font-semibold text-slate-900">Actividad reciente</h2>
                <a href="{{ route('transacciones.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Ver todo</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($recientes as $transaccion)
                    <li class="flex items-center gap-4 px-6 py-3.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ $transaccion->cliente?->iniciales }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $transaccion->cliente?->nombre_completo }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $transaccion->propiedad?->direccion }} · {{ $transaccion->propiedad?->ciudad }}</p>
                        </div>
                        <x-badge :value="$transaccion->tipo_transaccion" class="hidden sm:inline-flex" />
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">{{ cop($transaccion->monto_transaccion) }}</p>
                            <p class="text-xs text-slate-500">{{ $transaccion->fecha_transaccion->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-slate-500">Aún no hay transacciones.</li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- Propiedades destacadas --}}
    @if ($destacadas->isNotEmpty())
        <section class="mt-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Disponibles de mayor valor</h2>
                <a href="{{ route('propiedades.index', ['estado' => 'disponible']) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Ver inventario</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($destacadas as $propiedad)
                    @include('propiedades.partials.card', ['propiedad' => $propiedad])
                @endforeach
            </div>
        </section>
    @endif

    <script type="application/json" id="dashboard-data">
        {!! json_encode(['ventasPorMes' => $ventasPorMes, 'precioM2' => $precioM2PorCiudad]) !!}
    </script>
</x-app-layout>
