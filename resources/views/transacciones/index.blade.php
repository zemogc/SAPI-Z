<x-app-layout title="Transacciones">
    <x-page-header title="Transacciones" subtitle="Ventas, compras y arrendamientos registrados.">
        <x-slot name="actions">
            @if (Route::has('transacciones.exportar'))
                <a href="{{ route('transacciones.exportar', request()->query()) }}" class="btn-secondary"><x-icon name="download" class="h-4 w-4" /> Exportar CSV</a>
            @endif
            <a href="{{ route('transacciones.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nueva transacción</a>
        </x-slot>
    </x-page-header>

    <div class="card overflow-hidden">
        <form method="GET" class="grid gap-3 border-b border-slate-100 p-4 sm:grid-cols-[auto_auto_auto_auto_1fr] sm:items-end">
            <div>
                <label for="f-tipo" class="label">Tipo</label>
                <select id="f-tipo" name="tipo" class="input sm:w-44">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Transaccion::TIPOS as $tipo)
                        <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="f-desde" class="label">Desde</label>
                <input id="f-desde" type="date" name="desde" value="{{ request('desde') }}" class="input">
            </div>
            <div>
                <label for="f-hasta" class="label">Hasta</label>
                <input id="f-hasta" type="date" name="hasta" value="{{ request('hasta') }}" class="input">
            </div>
            <button class="btn-secondary">Filtrar</button>
            <div class="text-left sm:text-right">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Total del periodo</p>
                <p class="text-xl font-extrabold text-slate-900">{{ cop($total) }}</p>
            </div>
        </form>

        @if ($transacciones->isEmpty())
            <x-empty-state icon="banknotes" title="Sin transacciones" action="Registrar transacción" :href="route('transacciones.create')">
                Cuando registres una venta o arrendamiento aparecerá aquí.
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="table-head">Fecha</th>
                            <th class="table-head">Propiedad</th>
                            <th class="table-head">Cliente</th>
                            <th class="table-head">Tipo</th>
                            <th class="table-head text-right">Monto</th>
                            <th class="table-head"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transacciones as $transaccion)
                            <tr class="hover:bg-slate-50/60">
                                <td class="table-cell text-slate-500">{{ $transaccion->fecha_transaccion->translatedFormat('d M Y') }}</td>
                                <td class="table-cell max-w-xs">
                                    @if ($transaccion->propiedad)
                                        <a href="{{ route('propiedades.show', $transaccion->propiedad) }}" class="block truncate font-medium text-slate-900 hover:text-brand-700">{{ $transaccion->propiedad->direccion }}</a>
                                        <span class="text-xs text-slate-500">{{ $transaccion->propiedad->ciudad }}</span>
                                    @endif
                                </td>
                                <td class="table-cell">{{ $transaccion->cliente?->nombre_completo }}</td>
                                <td class="table-cell"><x-badge :value="$transaccion->tipo_transaccion" /></td>
                                <td class="table-cell text-right font-semibold text-slate-900">{{ cop($transaccion->monto_transaccion) }}</td>
                                <td class="table-cell text-right">
                                    <a href="{{ route('transacciones.edit', $transaccion) }}" class="inline-flex rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Editar"><x-icon name="pencil" class="h-4 w-4" /></a>
                                    <x-delete-button :action="route('transacciones.destroy', $transaccion)" confirm="¿Eliminar esta transacción? El estado de la propiedad se recalculará." />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">{{ $transacciones->links() }}</div>
        @endif
    </div>
</x-app-layout>
