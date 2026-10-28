<x-app-layout title="Editar cliente">
    <x-page-header title="Editar cliente" :subtitle="$cliente->nombre_completo" />

    <form method="POST" action="{{ route('clientes.update', $cliente) }}">
        @method('PATCH')
        @include('clientes.partials.form')
    </form>
</x-app-layout>
