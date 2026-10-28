<x-app-layout title="Clientes">
    <x-page-header title="Clientes" subtitle="Compradores, vendedores y arrendatarios de tu cartera.">
        <x-slot name="actions">
            <a href="{{ route('clientes.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nuevo cliente</a>
        </x-slot>
    </x-page-header>

    <div class="card overflow-hidden">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row">
            <label class="relative flex-1">
                <span class="sr-only">Buscar</span>
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, email o teléfono…" class="input pl-9">
            </label>
            <div class="flex gap-1 rounded-xl bg-slate-100 p-1 text-sm font-medium">
                @foreach (['' => 'Todos'] + array_combine(\App\Models\Cliente::TIPOS, array_map('ucfirst', \App\Models\Cliente::TIPOS)) as $valor => $texto)
                    <button name="tipo" value="{{ $valor }}" @class([
                        'rounded-lg px-3 py-1.5 transition',
                        'bg-white text-slate-900 shadow-sm' => request('tipo', '') === $valor,
                        'text-slate-500 hover:text-slate-800' => request('tipo', '') !== $valor,
                    ])>{{ $texto }}</button>
                @endforeach
            </div>
        </form>

        @if ($clientes->isEmpty())
            <x-empty-state icon="users" title="No hay clientes" action="Agregar cliente" :href="route('clientes.create')">
                Registra a tus clientes para asociarlos a ventas y arrendamientos.
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="table-head">Cliente</th>
                            <th class="table-head">Contacto</th>
                            <th class="table-head">Tipo</th>
                            <th class="table-head text-right">Negocios</th>
                            <th class="table-head text-right">Volumen</th>
                            <th class="table-head"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($clientes as $cliente)
                            <tr class="hover:bg-slate-50/60">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">{{ $cliente->iniciales }}</div>
                                        <span class="font-semibold text-slate-900">{{ $cliente->nombre_completo }}</span>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <a href="mailto:{{ $cliente->email }}" class="flex items-center gap-1.5 hover:text-brand-700"><x-icon name="mail" class="h-4 w-4 text-slate-400" />{{ $cliente->email }}</a>
                                    <span class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500"><x-icon name="phone" class="h-3.5 w-3.5 text-slate-400" />{{ $cliente->telefono }}</span>
                                </td>
                                <td class="table-cell"><x-badge :value="$cliente->tipo" /></td>
                                <td class="table-cell text-right font-medium">{{ $cliente->transacciones_count }}</td>
                                <td class="table-cell text-right font-semibold text-slate-900">{{ $cliente->transacciones_sum_monto_transaccion ? cop($cliente->transacciones_sum_monto_transaccion, true) : '—' }}</td>
                                <td class="table-cell text-right">
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="inline-flex rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Editar"><x-icon name="pencil" class="h-4 w-4" /></a>
                                    <x-delete-button :action="route('clientes.destroy', $cliente)" confirm="¿Eliminar este cliente?" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">{{ $clientes->links() }}</div>
        @endif
    </div>
</x-app-layout>
