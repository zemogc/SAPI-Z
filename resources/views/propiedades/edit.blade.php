<x-app-layout title="Editar propiedad">
    <x-page-header title="Editar propiedad" :subtitle="$propiedad->direccion" />

    <form method="POST" action="{{ route('propiedades.update', $propiedad) }}">
        @method('PATCH')
        @include('propiedades.partials.form')
    </form>
</x-app-layout>
