<x-app-layout title="Nueva propiedad">
    <x-page-header title="Nueva propiedad" subtitle="Registra un inmueble en el inventario." />

    <form method="POST" action="{{ route('propiedades.store') }}">
        @include('propiedades.partials.form')
    </form>
</x-app-layout>
