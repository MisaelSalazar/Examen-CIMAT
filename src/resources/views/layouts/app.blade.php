<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Registro, evaluación y consulta de aspirantes del proceso de selección.">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>@yield('titulo', 'Aspirantes') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-slate-50 text-slate-800 antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-indigo-700 focus:shadow">
        Saltar al contenido
    </a>

    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <a href="{{ route('aspirantes.index') }}" class="group flex items-center gap-3 rounded-xl">
                <span class="grid size-10 place-items-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/20 transition group-hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-indigo-600">{{ config('app.name') }}</span>
                    <span class="block text-sm font-medium text-slate-500">Proceso de selección</span>
                </span>
            </a>

            <nav class="flex items-center gap-1.5" aria-label="Navegación principal">
                <a href="{{ route('aspirantes.index') }}"
                   @class([
                       'rounded-lg px-3 py-2 text-sm font-medium transition',
                       'bg-indigo-50 text-indigo-700' => request()->routeIs('aspirantes.index', 'aspirantes.show'),
                       'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('aspirantes.index', 'aspirantes.show'),
                   ])
                   @if (request()->routeIs('aspirantes.index', 'aspirantes.show')) aria-current="page" @endif>
                    Aspirantes
                </a>
                <a href="{{ route('aspirantes.create') }}"
                   @class([
                       'inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-semibold shadow-sm transition',
                       'bg-indigo-600 text-white hover:bg-indigo-700' => ! request()->routeIs('aspirantes.create'),
                       'bg-indigo-700 text-white' => request()->routeIs('aspirantes.create'),
                   ])
                   @if (request()->routeIs('aspirantes.create')) aria-current="page" @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                    Nuevo aspirante
                </a>
            </nav>
        </div>
    </header>

    <main id="contenido" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6">
        @if (session('exito'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-600" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                <p>{{ session('exito') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800" role="status">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-5 shrink-0 text-sky-600" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                </svg>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        @yield('contenido')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p>{{ config('app.name') }} · Examen práctico CIMAT</p>
            <p>Laravel {{ Illuminate\Foundation\Application::VERSION }} · PHP {{ PHP_VERSION }}</p>
        </div>
    </footer>
</body>
</html>
