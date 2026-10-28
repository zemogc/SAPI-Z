<x-app-layout title="Nuevo cliente">
    <x-page-header title="Nuevo cliente" subtitle="Agrega un contacto a tu cartera." />

    <form method="POST" action="{{ route('clientes.store') }}">
        @include('clientes.partials.form')
    </form>
</x-app-layout>
