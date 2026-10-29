@extends('layouts.app')

@section('titulo', 'Nueva evaluación')

@section('contenido')
    <div class="mx-auto max-w-3xl space-y-6">
        <nav class="flex items-center gap-1.5 text-sm text-slate-500" aria-label="Migas de pan">
            <a href="{{ route('aspirantes.index') }}" class="rounded transition hover:text-indigo-700">Aspirantes</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('aspirantes.show', $aspirante) }}" class="truncate rounded transition hover:text-indigo-700">{{ $aspirante->nombre_completo }}</a>
            <span aria-hidden="true">/</span>
            <span class="font-medium text-slate-700">Evaluación</span>
        </nav>

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Registrar evaluación</h1>
            <p class="mt-1 text-sm text-slate-500">
                Aspirante: <span class="font-medium text-slate-700">{{ $aspirante->nombre_completo }}</span>
                · {{ $aspirante->puesto }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @include('evaluaciones._form', [
                'aspirante' => $aspirante,
                'evaluacion' => null,
                'accion' => route('evaluaciones.store', $aspirante),
                'metodo' => 'POST',
                'textoBoton' => 'Guardar evaluación',
            ])
        </div>
    </div>
@endsection
