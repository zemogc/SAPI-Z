@props(['icon' => 'building', 'title', 'action' => null, 'href' => null])

<div class="flex flex-col items-center justify-center px-6 py-16 text-center">
    <div class="mb-4 rounded-2xl bg-brand-50 p-4 text-brand-600">
        <x-icon :name="$icon" class="h-8 w-8" />
    </div>
    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $slot }}</p>
    @if ($action && $href)
        <a href="{{ $href }}" class="btn-primary mt-6"><x-icon name="plus" class="h-4 w-4" /> {{ $action }}</a>
    @endif
</div>
