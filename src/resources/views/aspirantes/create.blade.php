@extends('layouts.app')

@section('titulo', 'Nuevo aspirante')

@section('contenido')
    <div class="mx-auto max-w-3xl space-y-6">
        <nav class="flex items-center gap-1.5 text-sm text-slate-500" aria-label="Migas de pan">
            <a href="{{ route('aspirantes.index') }}" class="rounded transition hover:text-indigo-700">Aspirantes</a>
            <span aria-hidden="true">/</span>
            <span class="font-medium text-slate-700">Nuevo aspirante</span>
        </nav>

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Registro de aspirante</h1>
            <p class="mt-1 text-sm text-slate-500">
                Captura los datos de la persona que participa en el proceso de selección.
                Los campos marcados con <span class="text-rose-600">*</span> son obligatorios.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('aspirantes.store') }}" class="space-y-6">
                @csrf

                <x-errores />

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form.input
                            name="nombre_completo"
                            label="Nombre completo"
                            :value="old('nombre_completo')"
                            placeholder="Ej. Ana Sofía Ramírez Torres"
                            autocomplete="name"
                            maxlength="150"
                            required
                        />
                    </div>

                    <x-form.input
                        name="puesto"
                        label="Puesto al que aplica"
                        :value="old('puesto')"
                        placeholder="Ej. Desarrollador Backend"
                        maxlength="120"
                        required
                    />

                    <x-form.select
                        name="estado_civil"
                        label="Estado civil"
                        :options="$estadosCiviles"
                        :selected="old('estado_civil')"
                        placeholder="Selecciona una opción"
                        required
                    />

                    <x-form.input
                        name="fecha_nacimiento"
                        label="Fecha de nacimiento"
                        type="date"
                        :value="old('fecha_nacimiento')"
                        min="1900-01-01"
                        :max="now()->format('Y-m-d')"
                        hint="Debe ser una fecha anterior al día de hoy."
                        required
                    />

                    <x-form.input
                        name="carrera"
                        label="Carrera o formación académica"
                        :value="old('carrera')"
                        placeholder="Ej. Ingeniería en Sistemas Computacionales"
                        maxlength="150"
                        required
                    />
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('aspirantes.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                        Guardar aspirante
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
