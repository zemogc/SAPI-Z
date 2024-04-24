<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Editar Transacción</title>
</head>
<body>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Transacción') }}
            </h2>
        </x-slot>

        <div class="container">
            <h1>Editar Transacción</h1>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form method="POST" action="{{ route('transacciones.update', ['transaccion' => $transaccion->id]) }}">
                                @method('PUT')
                                @csrf
                                <div class="mb-3">
                                    <label for="propiedad_id" class="form-label">Propiedad</label>
                                    <select class="form-select" id="propiedad_id" name="propiedad_id" required>
                                        <option selected disabled value="">Seleccione una propiedad</option>
                                        @foreach ($propiedades as $propiedad)
                                            <option value="{{ $propiedad->id }}" {{ $transaccion->propiedad_id == $propiedad->id ? 'selected' : '' }}>{{ $propiedad->direccion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cliente_id" class="form-label">Cliente</label>
                                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                                        <option selected disabled value="">Seleccione un cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ $transaccion->cliente_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tipo_transaccion" class="form-label">Tipo de Transacción</label>
                                    <input type="text" class="form-control" id="tipo_transaccion" name="tipo_transaccion" required value="{{ $transaccion->tipo_transaccion }}">
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_transaccion" class="form-label">Fecha de Transacción</label>
                                    <input type="date" class="form-control" id="fecha_transaccion" name="fecha_transaccion" required value="{{ $transaccion->fecha_transaccion }}">
                                </div>
                                <div class="mb-3">
                                    <label for="monto_transaccion" class="form-label">Monto de Transacción</label>
                                    <input type="text" class="form-control" id="monto_transaccion" name="monto_transaccion" required value="{{ $transaccion->monto_transaccion }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                <a href="{{ route('transacciones.index') }}" class="btn btn-warning">Cancelar</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
