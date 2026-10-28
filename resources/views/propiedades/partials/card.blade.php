@php
    $portadas = [
        'casa' => 'from-emerald-400 via-teal-500 to-cyan-600',
        'apartamento' => 'from-sky-400 via-indigo-500 to-violet-600',
        'local comercial' => 'from-amber-400 via-orange-500 to-rose-500',
    ];
@endphp

<article class="card group overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg">
    <a href="{{ route('propiedades.show', $propiedad) }}" class="relative block h-36 bg-gradient-to-br {{ $portadas[$propiedad->tipo] ?? 'from-slate-400 to-slate-600' }}">
        <x-icon :name="$propiedad->tipo === 'local comercial' ? 'building' : 'home'" class="absolute -bottom-4 -right-2 h-32 w-32 text-white/20" />
        <div class="absolute left-4 top-4 flex gap-2">
            <x-badge :value="$propiedad->estado" class="bg-white/95" />
            <span class="inline-flex items-center rounded-full bg-black/20 px-2.5 py-0.5 text-xs font-semibold capitalize text-white backdrop-blur">{{ $propiedad->tipo }}</span>
        </div>
        <p class="absolute bottom-3 left-4 text-2xl font-extrabold tracking-tight text-white drop-shadow">{{ cop($propiedad->precio) }}</p>
    </a>

    <div class="p-5">
        <a href="{{ route('propiedades.show', $propiedad) }}" class="block truncate font-semibold text-slate-900 hover:text-brand-700" title="{{ $propiedad->direccion }}">
            {{ $propiedad->direccion }}
        </a>
        <p class="mt-1 flex items-center gap-1 text-sm text-slate-500">
            <x-icon name="map-pin" class="h-4 w-4" /> {{ $propiedad->ciudad ?? 'Sin ciudad' }}
        </p>

        <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4 text-sm text-slate-600">
            <span class="flex items-center gap-1.5 whitespace-nowrap"><x-icon name="square" class="h-4 w-4 text-slate-400" /> {{ (float) $propiedad->tamano }} m²</span>
            @if ($propiedad->numero_habitaciones)
                <span class="flex items-center gap-1.5"><x-icon name="bed" class="h-4 w-4 text-slate-400" /> {{ $propiedad->numero_habitaciones }}</span>
            @endif
            @if ($propiedad->banos)
                <span class="flex items-center gap-1.5"><x-icon name="bath" class="h-4 w-4 text-slate-400" /> {{ $propiedad->banos }}</span>
            @endif
            <span class="-mr-2 ml-auto flex shrink-0">
                <a href="{{ route('propiedades.edit', $propiedad) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Editar">
                    <x-icon name="pencil" class="h-4 w-4" />
                </a>
                <x-delete-button :action="route('propiedades.destroy', $propiedad)" confirm="¿Eliminar esta propiedad?" />
            </span>
        </div>
    </div>
</article>
