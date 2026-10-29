@extends('layouts.app')

@section('titulo', 'Aspirantes')

@section('contenido')
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Aspirantes</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $totalAspirantes }} {{ $totalAspirantes === 1 ? 'aspirante registrado' : 'aspirantes registrados' }}
                    en el proceso de selección.
                </p>
            </div>
            <a href="{{ route('aspirantes.create') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-indigo-600" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                </svg>
                Registrar aspirante
            </a>
        </div>

        <form method="GET" action="{{ route('aspirantes.index') }}"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_14rem_auto]">
                <div>
                    <label for="buscar" class="sr-only">Buscar aspirante</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                        </svg>
                        <input id="buscar" name="buscar" type="search" value="{{ $buscar }}"
                               placeholder="Buscar por nombre, puesto o carrera"
                               class="block w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                </div>

                <div>
                    <label for="estado_civil" class="sr-only">Estado civil</label>
                    <select id="estado_civil" name="estado_civil"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">Todos los estados civiles</option>
                        @foreach ($estadosCiviles as $valor => $texto)
                            <option value="{{ $valor }}" @selected($estadoCivilSeleccionado === $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 sm:flex-none">
                        Filtrar
                    </button>
                    @if (filled($buscar) || filled($estadoCivilSeleccionado))
                        <a href="{{ route('aspirantes.index') }}"
                           class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if ($aspirantes->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto grid size-12 place-items-center rounded-full bg-slate-100 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-slate-900">
                        {{ filled($buscar) || filled($estadoCivilSeleccionado) ? 'Sin resultados' : 'Aún no hay aspirantes' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ filled($buscar) || filled($estadoCivilSeleccionado)
                            ? 'Prueba con otro término de búsqueda o quita los filtros.'
                            : 'Registra al primer aspirante para comenzar el proceso.' }}
                    </p>
                    <a href="{{ route('aspirantes.create') }}"
                       class="mt-5 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        Registrar aspirante
                    </a>
                </div>
            @else
                <table class="hidden w-full text-left text-sm md:table">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold">Aspirante</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Puesto</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Estado civil</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Edad</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Evaluación</th>
                            <th scope="col" class="px-5 py-3 text-right font-semibold"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($aspirantes as $aspirante)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <a href="{{ route('aspirantes.show', $aspirante) }}" class="font-semibold text-slate-900 hover:text-indigo-700">
                                        {{ $aspirante->nombre_completo }}
                                    </a>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $aspirante->carrera }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $aspirante->puesto }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $aspirante->estado_civil->label() }}</td>
                                <td class="px-5 py-4 tabular-nums text-slate-600">{{ $aspirante->edad() }} años</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <x-estado-evaluacion :evaluado="$aspirante->evaluacion !== null" />
                                        @if ($aspirante->evaluacion)
                                            <span class="text-sm font-semibold tabular-nums text-slate-900">
                                                {{ number_format($aspirante->evaluacion->promedio(), 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('aspirantes.show', $aspirante) }}"
                                       class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">
                                        Ver ficha
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <ul class="divide-y divide-slate-100 md:hidden">
                    @foreach ($aspirantes as $aspirante)
                        <li>
                            <a href="{{ route('aspirantes.show', $aspirante) }}" class="block px-4 py-4 transition hover:bg-slate-50">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-900">{{ $aspirante->nombre_completo }}</p>
                                        <p class="mt-0.5 text-sm text-slate-600">{{ $aspirante->puesto }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ $aspirante->estado_civil->label() }} · {{ $aspirante->edad() }} años
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <x-estado-evaluacion :evaluado="$aspirante->evaluacion !== null" />
                                        @if ($aspirante->evaluacion)
                                            <p class="mt-1.5 text-sm font-semibold tabular-nums text-slate-900">
                                                {{ number_format($aspirante->evaluacion->promedio(), 2) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if ($aspirantes->hasPages())
            <div>
                {{ $aspirantes->links() }}
            </div>
        @endif
    </div>
@endsection
