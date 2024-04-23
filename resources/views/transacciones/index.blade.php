<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Transacciones') }}
        </h2>
    </x-slot>
    
    <div class="container">
        <h1>Lista de Transacciones</h1>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <a href="{{ route('transacciones.create') }}"
                            class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded ml-2">Agregar transacción</a>
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
                                        <td>{{ $transaccion->propiedad->direccion }}</td>
                                        <td>{{ $transaccion->cliente->nombre }}</td>
                                        <td>{{ $transaccion->tipo_transaccion }}</td>
                                        <td>{{ $transaccion->fecha_transaccion }}</td>
                                        <td>{{ $transaccion->monto_transaccion }}</td>
                                        <td>
                                            <a href="{{ route('transacciones.edit', ['transaccion' => $transaccion->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Editar
                                            </a>
                                            <form action="{{ route('transacciones.destroy', ['transaccion' => $transaccion->id]) }}" method='POST' style="display: inline-block">
                                                @method('delete')
                                                @csrf
                                                <input class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2" type="submit" value="Eliminar">
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
