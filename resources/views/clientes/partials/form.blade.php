@csrf

<div class="card max-w-3xl space-y-5 p-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <x-field label="Nombre" name="nombre">
            <input id="nombre" name="nombre" type="text" required value="{{ old('nombre', $cliente->nombre) }}" class="input" autofocus>
        </x-field>
        <x-field label="Apellido" name="apellido">
            <input id="apellido" name="apellido" type="text" required value="{{ old('apellido', $cliente->apellido) }}" class="input">
        </x-field>
        <x-field label="Teléfono" name="telefono">
            <input id="telefono" name="telefono" type="tel" required value="{{ old('telefono', $cliente->telefono) }}" placeholder="+57 300 123 4567" class="input">
        </x-field>
        <x-field label="Email" name="email">
            <input id="email" name="email" type="email" required value="{{ old('email', $cliente->email) }}" placeholder="cliente@correo.com" class="input">
        </x-field>
    </div>

    <div>
        <span class="label">Tipo de cliente</span>
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach (\App\Models\Cliente::TIPOS as $tipo)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                    <input type="radio" name="tipo" value="{{ $tipo }}" required class="text-brand-600 focus:ring-brand-500" @checked(old('tipo', $cliente->tipo) === $tipo)>
                    <span class="text-sm font-medium capitalize text-slate-700">{{ $tipo }}</span>
                </label>
            @endforeach
        </div>
        @error('tipo') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button type="submit" class="btn-primary">Guardar cliente</button>
        <a href="{{ route('clientes.index') }}" class="btn-secondary">Cancelar</a>
    </div>
</div>
