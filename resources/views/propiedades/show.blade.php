<x-app-layout :title="$propiedad->direccion">
    <a href="{{ route('propiedades.index') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800">
        <x-icon name="arrow-left" class="h-4 w-4" /> Volver a propiedades
    </a>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="card overflow-hidden">
                <div class="relative bg-gradient-to-br from-ink-800 via-ink-700 to-brand-900 p-8 text-white">
                    <x-icon :name="$propiedad->tipo === 'local comercial' ? 'building' : 'home'" class="absolute -right-6 -top-6 h-48 w-48 text-white/5" />
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge :value="$propiedad->estado" class="bg-white" />
                        <span class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-semibold capitalize">{{ $propiedad->tipo }}</span>
                    </div>
                    <h1 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">{{ $propiedad->direccion }}</h1>
                    <p class="mt-1 flex items-center gap-1 text-slate-300"><x-icon name="map-pin" class="h-4 w-4" /> {{ $propiedad->ciudad ?? 'Sin ciudad' }}</p>
                    <p class="mt-6 text-4xl font-extrabold tracking-tight text-brand-300">{{ cop($propiedad->precio) }}</p>
                    @if ($propiedad->precio_m2)
                        <p class="text-sm text-slate-400">{{ cop($propiedad->precio_m2) }} por m²</p>
                    @endif
                </div>

                <dl class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100 text-center">
                    <div class="p-4"><dt class="text-xs text-slate-500">Área</dt><dd class="mt-1 text-lg font-bold">{{ (float) $propiedad->tamano }} m²</dd></div>
                    <div class="p-4"><dt class="text-xs text-slate-500">Habitaciones</dt><dd class="mt-1 text-lg font-bold">{{ $propiedad->numero_habitaciones }}</dd></div>
                    <div class="p-4"><dt class="text-xs text-slate-500">Baños</dt><dd class="mt-1 text-lg font-bold">{{ $propiedad->banos ?? '—' }}</dd></div>
                </dl>

                <div class="p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Descripción</h2>
                    @if ($propiedad->descripcion)
                        <p class="mt-3 whitespace-pre-line leading-relaxed text-slate-700">{{ $propiedad->descripcion }}</p>
                    @else
                        <p class="mt-3 text-sm italic text-slate-400">Esta propiedad aún no tiene descripción.</p>
                    @endif
                </div>
            </section>

            <section class="card">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="font-semibold text-slate-900">Historial de transacciones</h2>
                    <a href="{{ route('transacciones.create', ['propiedad' => $propiedad->id]) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">+ Registrar</a>
                </div>
                @forelse ($propiedad->transacciones as $transaccion)
                    <div class="flex items-center gap-4 px-6 py-4 [&:not(:last-child)]:border-b [&:not(:last-child)]:border-slate-100">
                        <x-badge :value="$transaccion->tipo_transaccion" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800">{{ $transaccion->cliente?->nombre_completo }}</p>
                            <p class="text-xs text-slate-500">{{ $transaccion->fecha_transaccion->translatedFormat('d M Y') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">{{ cop($transaccion->monto_transaccion) }}</p>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-slate-500">Sin transacciones registradas.</p>
                @endforelse
            </section>
        </div>

        <aside class="space-y-6">
            <div class="card flex gap-3 p-4">
                <a href="{{ route('propiedades.edit', $propiedad) }}" class="btn-secondary flex-1"><x-icon name="pencil" class="h-4 w-4" /> Editar</a>
                <form method="POST" action="{{ route('propiedades.destroy', $propiedad) }}" x-data @submit="if (! confirm('¿Eliminar esta propiedad?')) $event.preventDefault()">
                    @csrf @method('DELETE')
                    <button class="btn-danger"><x-icon name="trash" class="h-4 w-4" /></button>
                </form>
            </div>

            @stack('show-aside')

            @if ($similares->isNotEmpty())
                <div class="card p-5">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500">Similares</h2>
                    <ul class="space-y-3">
                        @foreach ($similares as $similar)
                            <li>
                                <a href="{{ route('propiedades.show', $similar) }}" class="flex items-center justify-between gap-3 rounded-xl p-2 hover:bg-slate-50">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium text-slate-800">{{ $similar->direccion }}</span>
                                        <span class="text-xs text-slate-500">{{ $similar->ciudad }} · {{ (float) $similar->tamano }} m²</span>
                                    </span>
                                    <span class="shrink-0 text-sm font-semibold text-brand-700">{{ cop($similar->precio, true) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
</x-app-layout>
