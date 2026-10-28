@props(['action', 'confirm' => '¿Seguro que deseas eliminar este registro?'])

<form method="POST" action="{{ $action }}" class="inline" x-data @submit="if (! confirm({{ Js::from($confirm) }})) $event.preventDefault()">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600']) }} title="Eliminar">
        <x-icon name="trash" class="h-4 w-4" />
    </button>
</form>
