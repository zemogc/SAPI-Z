@props(['value'])

@php
    $estilos = [
        'disponible' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'vendida' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'arrendada' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'venta' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'compra' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'arrendamiento' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'comprador' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'vendedor' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'arrendatario' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize ring-1 ring-inset '.($estilos[$value] ?? 'bg-slate-50 text-slate-600 ring-slate-500/20')]) }}>
    {{ $value }}
</span>
