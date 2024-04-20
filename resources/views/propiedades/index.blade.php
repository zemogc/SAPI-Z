<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Propiedades</title>
</head>
<body>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Propiedades') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Listado de Propiedades</h1>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <a href="{{ route('propiedades.create') }}"
                            class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded ml-2">Agregar propiedad</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Tamaño</th>
                                    <th scope="col">Número de Habitaciones</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($propiedades as $propiedad)
                                    <tr>
                                        <th scope="row">{{ $propiedad->id }}</th>
                                        <td>{{ $propiedad->dirección }}</td>
                                        <td>{{ $propiedad->tipo }}</td>
                                        <td>{{ $propiedad->tamaño }}</td>
                                        <td>{{ $propiedad->número_de_habitaciones }}</td>
                                        <td>{{ $propiedad->precio }}</td>
                                        <td>{{ $propiedad->estado }}</td>
                                        <td>
                                            <a href="{{ route('propiedades.edit', ['propiedad' => $propiedad->id]) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Editar propiedad
                                            </a>
                                            <form action="{{ route('propiedades.destroy', ['propiedad' => $propiedad->id]) }}"
                                                method="POST" style="display: inline-block">
                                                @method('delete')
                                                @csrf
                                                <input
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2"
                                                    type="submit" value="Eliminar">
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
</body>
</html>
