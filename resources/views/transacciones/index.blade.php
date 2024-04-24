<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transacciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transacciones') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Listado de Transacciones</h1>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <a href="{{ route('transacciones.create') }}" class="btn btn-success">Agregar Transacción</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Propiedad</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Tipo de Transacción</th>
                                    <th scope="col">Fecha de Transacción</th>
                                    <th scope="col">Monto de Transacción</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transacciones as $transaccion)
                                <tr>
                                    <td>{{ $transaccion->id }}</td>
                                    <td>{{ optional($transaccion->propiedad)->direccion }}</td>
                                    <td>{{ optional($transaccion->cliente)->nombre }}</td>
                                    <td>{{ $transaccion->tipo_transaccion }}</td>
                                    <td>{{ $transaccion->fecha_transaccion }}</td>
                                    <td>{{ $transaccion->monto_transaccion }}</td>
                                    <td>
                                        <a href="{{ route('transacciones.edit', $transaccion->id) }}" class="btn btn-primary">Editar</a>
                                        <form action="{{ route('transacciones.destroy', $transaccion->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
