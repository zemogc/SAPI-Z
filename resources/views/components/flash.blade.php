@php
    $tipos = [
        'success' => ['check', 'bg-emerald-50 text-emerald-800 ring-emerald-200'],
        'error' => ['alert', 'bg-rose-50 text-rose-800 ring-rose-200'],
    ];
@endphp

@foreach ($tipos as $tipo => [$icono, $clases])
    @if (session($tipo))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
             class="mb-6 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium ring-1 {{ $clases }}" role="status">
            <x-icon :name="$icono" class="h-5 w-5 shrink-0" />
            <span class="flex-1">{{ session($tipo) }}</span>
            <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Cerrar">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    @endif
@endforeach
