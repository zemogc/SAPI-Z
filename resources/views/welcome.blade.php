<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SAPI-Z') }} · Gestión inmobiliaria inteligente</title>
    <meta name="description" content="SAPI-Z: administra propiedades, clientes y negocios inmobiliarios con un asistente de IA.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-ink-900 text-slate-300">
    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute -top-40 left-1/2 h-[36rem] w-[60rem] -translate-x-1/2 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-0 top-64 h-80 w-80 rounded-full bg-fuchsia-500/10 blur-3xl"></div>

        <header class="relative mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="/" class="flex items-center gap-3">
                <x-application-logo class="h-10 w-10" />
                <span class="text-xl font-extrabold tracking-tight text-white">SAPI<span class="text-brand-400">-Z</span></span>
            </a>
            <nav class="flex items-center gap-2 text-sm font-semibold">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Ir al panel</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-slate-300 hover:text-white">Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary">Crear cuenta</a>
                    @endif
                @endauth
            </nav>
        </header>

        <main class="relative mx-auto max-w-6xl px-6 pb-24 pt-16 text-center lg:pt-24">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold text-brand-300">
                <x-icon name="sparkles" class="h-4 w-4" /> Ahora con asistente de IA
            </span>
            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-extrabold tracking-tight text-white sm:text-6xl">
                Tu inmobiliaria, <span class="bg-gradient-to-r from-brand-300 to-emerald-500 bg-clip-text text-transparent">organizada</span> y un paso adelante.
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-slate-400">
                SAPI-Z centraliza propiedades, clientes y transacciones, mide tu negocio en un panel en tiempo real
                y te ayuda a redactar anuncios y estimar precios con inteligencia artificial.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <a href="{{ route(auth()->check() ? 'dashboard' : 'login') }}" class="btn-primary px-6 py-3 text-base">Empezar ahora</a>
                <a href="https://github.com/zemogc/SAPI-Z" class="btn rounded-xl border border-white/10 px-6 py-3 text-base text-white hover:bg-white/5">Ver en GitHub</a>
            </div>

            <div class="mt-20 grid gap-4 text-left sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['building', 'Inventario', 'Casas, apartamentos y locales con búsqueda, filtros y ficha detallada.'],
                    ['users', 'Clientes', 'Compradores, vendedores y arrendatarios con su historial de negocios.'],
                    ['banknotes', 'Transacciones', 'Ventas y arriendos que actualizan solos el estado del inmueble.'],
                    ['sparkles', 'Asistente IA', 'Descripciones de anuncio, estimación de precio y preguntas sobre tu cartera.'],
                ] as [$icono, $titulo, $texto])
                    <div class="rounded-2xl border border-white/10 bg-white/[.03] p-6 backdrop-blur">
                        <span class="inline-flex rounded-xl bg-brand-500/10 p-2.5 text-brand-300"><x-icon :name="$icono" class="h-6 w-6" /></span>
                        <h2 class="mt-4 font-semibold text-white">{{ $titulo }}</h2>
                        <p class="mt-1 text-sm leading-relaxed text-slate-400">{{ $texto }}</p>
                    </div>
                @endforeach
            </div>
        </main>

        <footer class="relative border-t border-white/5 py-8 text-center text-xs text-slate-500">
            SAPI-Z · Sistema de Administración de Propiedades Inmobiliarias · Hecho con Laravel
        </footer>
    </div>
</body>
</html>
