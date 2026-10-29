@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de paginación"
         class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        <p class="text-sm text-slate-600">
            Mostrando
            <span class="font-medium text-slate-900">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium text-slate-900">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-medium text-slate-900">{{ $paginator->total() }}</span>
            {{ $paginator->total() === 1 ? 'resultado' : 'resultados' }}
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-400"
                      aria-disabled="true">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Anterior
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-slate-400" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex min-w-9 items-center justify-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex min-w-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
                               aria-label="Ir a la página {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Siguiente
                </a>
            @else
                <span class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-400"
                      aria-disabled="true">
                    Siguiente
                </span>
            @endif
        </div>
    </nav>
@endif
