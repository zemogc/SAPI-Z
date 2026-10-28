<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SAPI-Z') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">
            <div class="relative hidden overflow-hidden bg-ink-900 p-12 lg:flex lg:flex-col lg:justify-between">
                <div class="pointer-events-none absolute -left-20 -top-20 h-96 w-96 rounded-full bg-brand-500/25 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 right-0 h-80 w-80 rounded-full bg-fuchsia-500/10 blur-3xl"></div>

                <a href="/" class="relative flex items-center gap-3">
                    <x-application-logo class="h-11 w-11" />
                    <span class="text-2xl font-extrabold tracking-tight text-white">SAPI<span class="text-brand-400">-Z</span></span>
                </a>

                <div class="relative">
                    <p class="text-3xl font-bold leading-tight tracking-tight text-white">
                        Propiedades, clientes y negocios<br>en un solo lugar.
                    </p>
                    <ul class="mt-8 space-y-3 text-slate-300">
                        @foreach (['Panel con métricas de ventas e inventario', 'Estado del inmueble sincronizado con cada negocio', 'Asistente de IA para anuncios y precios'] as $punto)
                            <li class="flex items-center gap-3"><x-icon name="check" class="h-5 w-5 text-brand-400" /> {{ $punto }}</li>
                        @endforeach
                    </ul>
                </div>

                <p class="relative text-xs text-slate-500">Sistema de Administración de Propiedades Inmobiliarias</p>
            </div>

            <div class="flex flex-col items-center justify-center bg-slate-50 px-6 py-12">
                <a href="/" class="mb-8 flex items-center gap-3 lg:hidden">
                    <x-application-logo class="h-10 w-10" />
                    <span class="text-xl font-extrabold tracking-tight">SAPI<span class="text-brand-600">-Z</span></span>
                </a>
                <div class="card w-full max-w-md p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
