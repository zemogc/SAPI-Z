<x-app-layout title="Nueva transacción">
    <x-page-header title="Nueva transacción" subtitle="Al guardar, el estado de la propiedad se actualiza automáticamente." />

    <form method="POST" action="{{ route('transacciones.store') }}">
        @include('transacciones.partials.form')
    </form>
</x-app-layout>
