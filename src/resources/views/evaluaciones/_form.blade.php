@php
    $evaluacion ??= null;
@endphp

<form method="POST" action="{{ $accion }}" class="space-y-6">
    @csrf
    @if ($metodo !== 'POST')
        @method($metodo)
    @endif

    <x-errores />

    <fieldset class="space-y-4">
        <legend class="text-sm font-semibold uppercase tracking-wide text-slate-500">Calificaciones</legend>
        <p class="-mt-1 text-xs text-slate-500">Escala de 0 a 10. Se admiten hasta 2 decimales.</p>

        <div class="grid gap-5 sm:grid-cols-3">
            <x-form.input
                name="conocimientos_tecnicos"
                label="Conocimientos técnicos"
                type="number"
                :value="old('conocimientos_tecnicos', $evaluacion?->conocimientos_tecnicos)"
                step="0.01"
                min="0"
                max="10"
                inputmode="decimal"
                required
            />

            <x-form.input
                name="referencias"
                label="Referencias"
                type="number"
                :value="old('referencias', $evaluacion?->referencias)"
                step="0.01"
                min="0"
                max="10"
                inputmode="decimal"
                required
            />

            <x-form.input
                name="habilidades_blandas"
                label="Habilidades blandas"
                type="number"
                :value="old('habilidades_blandas', $evaluacion?->habilidades_blandas)"
                step="0.01"
                min="0"
                max="10"
                inputmode="decimal"
                required
            />
        </div>
    </fieldset>

    <x-form.textarea
        name="comentarios"
        label="Comentarios generales"
        :value="old('comentarios', $evaluacion?->comentarios)"
        rows="5"
        maxlength="2000"
        hint="Opcional. Máximo 2000 caracteres."
        placeholder="Observaciones sobre el desempeño, aptitudes y áreas de mejora del aspirante…"
    />

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('aspirantes.show', $aspirante) }}"
           class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            Cancelar
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
            </svg>
            {{ $textoBoton }}
        </button>
    </div>
</form>
