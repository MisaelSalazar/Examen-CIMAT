@extends('layouts.app')

@section('titulo', 'Página no encontrada')

@section('contenido')
    <div class="mx-auto max-w-lg py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Error 404</p>
        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">No encontramos esta página</h1>
        <p class="mt-3 text-sm text-slate-500">
            El recurso que buscas no existe o fue movido. Verifica la dirección o regresa al listado de aspirantes.
        </p>
        <a href="{{ route('aspirantes.index') }}"
           class="mt-7 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
            Ir al listado de aspirantes
        </a>
    </div>
@endsection
