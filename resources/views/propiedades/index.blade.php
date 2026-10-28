<x-app-layout title="Propiedades">
    <x-page-header title="Propiedades" subtitle="Inventario de inmuebles, su estado y precio.">
        <x-slot name="actions">
            <a href="{{ route('propiedades.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nueva propiedad</a>
        </x-slot>
    </x-page-header>

    <form method="GET" class="card mb-6 grid gap-3 p-4 sm:grid-cols-[1fr_auto_auto_auto]">
        <label class="relative">
            <span class="sr-only">Buscar</span>
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por dirección, ciudad o descripción…" class="input pl-9">
        </label>
        <select name="tipo" class="input sm:w-44" aria-label="Tipo">
            <option value="">Todos los tipos</option>
            @foreach (\App\Models\Propiedad::TIPOS as $tipo)
                <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>
            @endforeach
        </select>
        <select name="estado" class="input sm:w-40" aria-label="Estado">
            <option value="">Todos los estados</option>
            @foreach (\App\Models\Propiedad::ESTADOS as $estado)
                <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>
            @endforeach
        </select>
        <button class="btn-secondary">Filtrar</button>
    </form>

    @if ($propiedades->isEmpty())
        <div class="card">
            <x-empty-state title="No hay propiedades para mostrar" action="Agregar propiedad" :href="route('propiedades.create')">
                Registra tu primer inmueble o ajusta los filtros de búsqueda.
            </x-empty-state>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($propiedades as $propiedad)
                @include('propiedades.partials.card', ['propiedad' => $propiedad])
            @endforeach
        </div>

        <div class="mt-8">{{ $propiedades->links() }}</div>
    @endif
</x-app-layout>
