@php
    $enlaces = [
        ['dashboard', 'dashboard', 'chart', 'Panel'],
        ['propiedades.index', 'propiedades.*', 'building', 'Propiedades'],
        ['clientes.index', 'clientes.*', 'users', 'Clientes'],
        ['transacciones.index', 'transacciones.*', 'banknotes', 'Transacciones'],
    ];
    if (Route::has('asistente.index')) {
        $enlaces[] = ['asistente.index', 'asistente.*', 'sparkles', 'Asistente IA'];
    }
@endphp

{{-- Fondo del menú móvil --}}
<div x-show="menu" x-cloak x-transition.opacity @click="menu = false" class="fixed inset-0 z-40 bg-ink-900/60 lg:hidden"></div>

<aside :class="menu ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-ink-900 text-slate-300 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
    <div class="flex items-center justify-between px-6 py-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-application-logo class="h-10 w-10" />
            <div>
                <p class="text-lg font-extrabold leading-none tracking-tight text-white">SAPI<span class="text-brand-400">-Z</span></p>
                <p class="mt-1 text-[11px] font-medium uppercase tracking-widest text-slate-500">Gestión inmobiliaria</p>
            </div>
        </a>
        <button @click="menu = false" class="rounded-lg p-1 text-slate-400 hover:text-white lg:hidden" aria-label="Cerrar menú">
            <x-icon name="x" class="h-6 w-6" />
        </button>
    </div>

    <nav class="flex-1 space-y-1 px-4">
        <p class="px-3 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-widest text-slate-500">Menú</p>
        @foreach ($enlaces as [$ruta, $patron, $icono, $texto])
            @php $activo = request()->routeIs($patron); @endphp
            <a href="{{ route($ruta) }}"
               @class([
                   'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                   'bg-white/10 text-white' => $activo,
                   'hover:bg-white/5 hover:text-white' => ! $activo,
               ])>
                <x-icon :name="$icono" @class(['h-5 w-5', 'text-brand-400' => $activo, 'text-slate-500 group-hover:text-slate-300' => ! $activo]) />
                {{ $texto }}
                @if ($icono === 'sparkles')
                    <span class="ml-auto rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 px-2 py-0.5 text-[10px] font-bold text-white">IA</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-xl px-2 py-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-500/20 text-sm font-bold text-brand-300">
                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div class="mt-2 grid grid-cols-2 gap-2">
            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-medium hover:bg-white/5 hover:text-white">
                <x-icon name="user" class="h-4 w-4" /> Perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-medium hover:bg-white/5 hover:text-white">
                    <x-icon name="logout" class="h-4 w-4" /> Salir
                </button>
            </form>
        </div>
    </div>
</aside>
