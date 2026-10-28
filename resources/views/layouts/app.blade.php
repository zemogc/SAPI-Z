<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'SAPI-Z') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50">
    <div x-data="{ menu: false }" class="min-h-screen lg:flex">
        @include('layouts.navigation')

        <div class="flex-1 min-w-0">
            {{-- Barra superior móvil --}}
            <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur lg:hidden">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-8" />
                    <span class="font-extrabold tracking-tight">SAPI<span class="text-brand-600">-Z</span></span>
                </a>
                <button @click="menu = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100" aria-label="Abrir menú">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
                <x-flash />
                @isset($header)
                    <div class="mb-8">{{ $header }}</div>
                @endisset
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
