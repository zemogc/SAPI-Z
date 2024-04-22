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
            color: black; /* Cambiar color de texto a negro */
        }

        .content-container {
            position: absolute; /* Posicionar contenido de manera absoluta dentro del contenedor */
            top: 5%; /* Mover el contenido hacia arriba */
            left: 40%; /* Mover el contenido hacia la izquierda */
            transform: translate(-50%, -50%); /* Centrar completamente el contenido */
            text-align: left; /* Alinear el texto a la izquierda */
            max-width: 80%; /* Establecer un ancho máximo */
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sistema de Administración de Propiedades Inmobiliarias (SAPI)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-img">
        <div class="content-container">
            <h1 class="text-3xl font-bold mb-4">{{ __("Bienvenido") }}</h1>
            <p class="mb-4">Desarrolla una aplicación en Laravel para la gestión de propiedades inmobiliarias, facilitando operaciones CRUD para propiedades, clientes y transacciones.</p>
            <p class="mb-8">Desarrollado por Zemog Gómez y Juan González.</p>
        </div>
    </div>
</x-app-layout>
