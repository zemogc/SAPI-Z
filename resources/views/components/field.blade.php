@props(['label', 'name', 'hint' => null])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    {{ $slot }}
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
