<x-app-layout>
    <style>
        .bg-img {
            position: relative; 
            background-image: url('/images/imgsapi.jpg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            height: 220vh;
            width: 220vh;
            color: black; /
        }

        .content-container {
            position: absolute; 
            top: 5%; 
            left: 40%; 
            transform: translate(-50%, -50%); 
            text-align: left; /
            max-width: 80%; 
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('SAPI es el sistema de Administración de Propiedades Inmobiliarias ') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-img">
        <div class="content-container">
            <h1 class="text-3xl font-bold mb-4">{{ __("Bienvenido") }}</h1>
            <p class="mb-4">Bienvenido a la web en la que podras realizar la gestión de propiedades inmobiliarias, facilitando operaciones CRUD para propiedades, clientes y transacciones.</p>
            <p class="mb-8">Desarrollado por Zemog Gómez y Juan González.</p>
        </div>
    </div>
</x-app-layout>
