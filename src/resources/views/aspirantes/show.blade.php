@extends('layouts.app')

@section('titulo', $aspirante->nombre_completo)

@section('contenido')
    <div class="space-y-6">
        <nav class="flex items-center gap-1.5 text-sm text-slate-500" aria-label="Migas de pan">
            <a href="{{ route('aspirantes.index') }}" class="rounded transition hover:text-indigo-700">Aspirantes</a>
            <span aria-hidden="true">/</span>
            <span class="truncate font-medium text-slate-700">{{ $aspirante->nombre_completo }}</span>
        </nav>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $aspirante->nombre_completo }}</h1>
                    <x-estado-evaluacion :evaluado="$aspirante->evaluacion !== null" />
                </div>
                <p class="mt-1 text-sm text-slate-600">{{ $aspirante->puesto }}</p>
                <p class="mt-1 text-xs text-slate-500">
                    Registrado el {{ $aspirante->created_at?->format('d/m/Y') }} · {{ $aspirante->edad() }} años
                </p>
            </div>

            @if ($aspirante->evaluacion)
                <a href="{{ route('evaluaciones.edit', $aspirante) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-indigo-600" aria-hidden="true">
                        <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                    </svg>
                    Editar evaluación
                </a>
            @else
                <a href="{{ route('evaluaciones.create', $aspirante) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                    Registrar evaluación
                </a>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-1" aria-labelledby="datos-generales">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 id="datos-generales" class="text-sm font-semibold uppercase tracking-wide text-slate-500">Datos generales</h2>
                </div>
                <dl class="divide-y divide-slate-100">
                    <div class="px-5 py-3.5">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nombre completo</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900">{{ $aspirante->nombre_completo }}</dd>
                    </div>
                    <div class="px-5 py-3.5">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Puesto al que aplica</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900">{{ $aspirante->puesto }}</dd>
                    </div>
                    <div class="px-5 py-3.5">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Estado civil</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900">{{ $aspirante->estado_civil->label() }}</dd>
                    </div>
                    <div class="px-5 py-3.5">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Fecha de nacimiento</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900">
                            {{ $aspirante->fecha_nacimiento->format('d/m/Y') }}
                            <span class="font-normal text-slate-500">({{ $aspirante->edad() }} años)</span>
                        </dd>
                    </div>
                    <div class="px-5 py-3.5">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Carrera o formación académica</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900">{{ $aspirante->carrera }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2" aria-labelledby="evaluacion">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <h2 id="evaluacion" class="text-sm font-semibold uppercase tracking-wide text-slate-500">Evaluación</h2>
                    @if ($aspirante->evaluacion)
                        <span class="text-xs text-slate-500">
                            Actualizada el {{ $aspirante->evaluacion->updated_at?->format('d/m/Y') }}
                        </span>
                    @endif
                </div>

                @if ($aspirante->evaluacion)
                    @php($evaluacion = $aspirante->evaluacion)

                    <div class="space-y-6 p-5">
                        <div class="flex flex-wrap items-center gap-5">
                            <div class="grid size-24 shrink-0 place-items-center rounded-full border-4 border-indigo-100 bg-indigo-50">
                                <div class="text-center">
                                    <span class="block text-2xl font-bold tabular-nums leading-none text-indigo-700">{{ number_format($evaluacion->promedio(), 2) }}</span>
                                    <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wide text-indigo-500">Promedio</span>
                                </div>
                            </div>

                            <div class="min-w-0 flex-1 space-y-3.5">
                                @foreach ($evaluacion->criterios() as $etiqueta => $valor)
                                    <x-score-bar :etiqueta="$etiqueta" :valor="$valor" />
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-medium uppercase tracking-wide text-slate-400">Comentarios generales</h3>
                            @if (filled($evaluacion->comentarios))
                                <p class="mt-2 whitespace-pre-line rounded-xl bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-700">{{ $evaluacion->comentarios }}</p>
                            @else
                                <p class="mt-2 text-sm italic text-slate-400">Sin comentarios registrados.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto grid size-12 place-items-center rounded-full bg-amber-50 text-amber-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-slate-900">Evaluación pendiente</h3>
                        <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">
                            Este aspirante aún no tiene calificaciones registradas.
                        </p>
                        <a href="{{ route('evaluaciones.create', $aspirante) }}"
                           class="mt-5 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Registrar evaluación
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
