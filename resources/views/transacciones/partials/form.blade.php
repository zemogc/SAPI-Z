@csrf

<div class="card max-w-3xl space-y-5 p-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <x-field label="Propiedad" name="propiedad_id" class="sm:col-span-2">
            <select id="propiedad_id" name="propiedad_id" required class="input">
                <option value="" disabled @selected(! old('propiedad_id', $transaccion->propiedad_id))>Selecciona una propiedad…</option>
                @foreach ($propiedades as $propiedad)
                    <option value="{{ $propiedad->id }}" @selected((string) old('propiedad_id', $transaccion->propiedad_id) === (string) $propiedad->id)>
                        {{ $propiedad->direccion }} · {{ $propiedad->ciudad }} · {{ cop($propiedad->precio) }} ({{ $propiedad->estado }})
                    </option>
                @endforeach
            </select>
        </x-field>

        <x-field label="Cliente" name="cliente_id" class="sm:col-span-2">
            <select id="cliente_id" name="cliente_id" required class="input">
                <option value="" disabled @selected(! old('cliente_id', $transaccion->cliente_id))>Selecciona un cliente…</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id', $transaccion->cliente_id) === (string) $cliente->id)>
                        {{ $cliente->nombre_completo }} ({{ $cliente->tipo }})
                    </option>
                @endforeach
            </select>
        </x-field>

        <div class="sm:col-span-2">
            <span class="label">Tipo de transacción</span>
            <div class="grid gap-3 sm:grid-cols-3">
                @foreach (\App\Models\Transaccion::TIPOS as $tipo)
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                        <input type="radio" name="tipo_transaccion" value="{{ $tipo }}" required class="text-brand-600 focus:ring-brand-500" @checked(old('tipo_transaccion', $transaccion->tipo_transaccion) === $tipo)>
                        <span class="text-sm font-medium capitalize text-slate-700">{{ $tipo }}</span>
                    </label>
                @endforeach
            </div>
            @error('tipo_transaccion') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
        </div>

        <x-field label="Fecha" name="fecha_transaccion">
            <input id="fecha_transaccion" name="fecha_transaccion" type="date" required max="{{ now()->toDateString() }}" value="{{ old('fecha_transaccion', $transaccion->fecha_transaccion?->toDateString()) }}" class="input">
        </x-field>

        <x-field label="Monto (COP)" name="monto_transaccion" hint="Para arrendamientos, el canon mensual.">
            <input id="monto_transaccion" name="monto_transaccion" type="text" inputmode="numeric" required value="{{ old('monto_transaccion', $transaccion->monto_transaccion ? number_format((float) $transaccion->monto_transaccion, 0, ',', '.') : null) }}" placeholder="350.000.000" class="input">
        </x-field>

        <x-field label="Notas" name="notas" class="sm:col-span-2">
            <textarea id="notas" name="notas" rows="3" class="input" placeholder="Condiciones, forma de pago, observaciones…">{{ old('notas', $transaccion->notas) }}</textarea>
        </x-field>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button type="submit" class="btn-primary">Guardar transacción</button>
        <a href="{{ route('transacciones.index') }}" class="btn-secondary">Cancelar</a>
    </div>
</div>
