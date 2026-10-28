<x-app-layout title="Editar transacción">
    <x-page-header title="Editar transacción" :subtitle="'#'.$transaccion->id.' · '.$transaccion->fecha_transaccion->translatedFormat('d M Y')" />

    <form method="POST" action="{{ route('transacciones.update', $transaccion) }}">
        @method('PUT')
        @include('transacciones.partials.form')
    </form>
</x-app-layout>
