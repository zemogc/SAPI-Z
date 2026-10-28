@csrf

<div class="grid gap-6 lg:grid-cols-3">
    <div class="card space-y-5 p-6 lg:col-span-2">
        <h2 class="text-base font-semibold text-slate-900">Datos del inmueble</h2>

        <div class="grid gap-5 sm:grid-cols-3">
            <x-field label="Dirección" name="direccion" class="sm:col-span-2">
                <input id="direccion" name="direccion" type="text" required value="{{ old('direccion', $propiedad->direccion) }}" placeholder="Calle 5 # 38-25, San Fernando" class="input">
            </x-field>
            <x-field label="Ciudad" name="ciudad">
                <input id="ciudad" name="ciudad" type="text" value="{{ old('ciudad', $propiedad->ciudad) }}" placeholder="Cali" class="input" list="ciudades">
                <datalist id="ciudades">
                    @foreach (\App\Models\Propiedad::CIUDADES as $ciudad)
                        <option value="{{ $ciudad }}">
                    @endforeach
                </datalist>
            </x-field>
        </div>

        <div class="grid gap-5 sm:grid-cols-4">
            <x-field label="Tipo" name="tipo" class="sm:col-span-2">
                <select id="tipo" name="tipo" required class="input">
                    <option value="" disabled @selected(! old('tipo', $propiedad->tipo))>Selecciona…</option>
                    @foreach (\App\Models\Propiedad::TIPOS as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo', $propiedad->tipo) === $tipo)>{{ ucfirst($tipo) }}</option>
                    @endforeach
                </select>
            </x-field>
            <x-field label="Habitaciones" name="numero_habitaciones">
                <input id="numero_habitaciones" name="numero_habitaciones" type="number" min="0" max="50" required value="{{ old('numero_habitaciones', $propiedad->numero_habitaciones ?? 0) }}" class="input">
            </x-field>
            <x-field label="Baños" name="banos">
                <input id="banos" name="banos" type="number" min="0" max="30" value="{{ old('banos', $propiedad->banos) }}" class="input">
            </x-field>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Área (m²)" name="tamano">
                <input id="tamano" name="tamano" type="number" step="0.01" min="1" required value="{{ old('tamano', $propiedad->tamano ? (float) $propiedad->tamano : null) }}" placeholder="85" class="input">
            </x-field>
            <x-field label="Precio (COP)" name="precio" hint="Puedes escribirlo con puntos: 350.000.000">
                <input id="precio" name="precio" type="text" inputmode="numeric" required value="{{ old('precio', $propiedad->precio ? number_format((float) $propiedad->precio, 0, ',', '.') : null) }}" placeholder="350.000.000" class="input">
            </x-field>
        </div>

        <x-field label="Descripción" name="descripcion">
            <textarea id="descripcion" name="descripcion" rows="6" class="input" placeholder="Cuenta lo que hace especial a este inmueble…">{{ old('descripcion', $propiedad->descripcion) }}</textarea>
        </x-field>
    </div>

    <div class="space-y-6">
        <div class="card space-y-5 p-6">
            <h2 class="text-base font-semibold text-slate-900">Estado</h2>
            <div class="space-y-2">
                @foreach (\App\Models\Propiedad::ESTADOS as $estado)
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                        <input type="radio" name="estado" value="{{ $estado }}" class="text-brand-600 focus:ring-brand-500" @checked(old('estado', $propiedad->estado) === $estado)>
                        <span class="text-sm font-medium capitalize text-slate-700">{{ $estado }}</span>
                    </label>
                @endforeach
            </div>
            @error('estado') <p class="text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
            <p class="text-xs text-slate-500">El estado se actualiza solo al registrar una venta o arrendamiento.</p>
        </div>

        @includeWhen(Route::has('asistente.describir'), 'propiedades.partials.asistente')

        <div class="flex gap-3">
            <button type="submit" class="btn-primary flex-1">Guardar</button>
            <a href="{{ url()->previous() === url()->current() ? route('propiedades.index') : url()->previous() }}" class="btn-secondary">Cancelar</a>
        </div>
    </div>
</div>
