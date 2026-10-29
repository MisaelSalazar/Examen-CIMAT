<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluacionRequest;
use App\Models\Aspirante;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EvaluacionController extends Controller
{
    public function create(Aspirante $aspirante): View|RedirectResponse
    {
        if ($aspirante->evaluacion()->exists()) {
            return redirect()
                ->route('evaluaciones.edit', $aspirante)
                ->with('info', 'Este aspirante ya tiene una evaluación registrada; puedes actualizarla.');
        }

        return view('evaluaciones.create', [
            'aspirante' => $aspirante,
        ]);
    }

    public function store(StoreEvaluacionRequest $request, Aspirante $aspirante): RedirectResponse
    {
        if ($aspirante->evaluacion()->exists()) {
            return redirect()
                ->route('evaluaciones.edit', $aspirante)
                ->with('info', 'Este aspirante ya tiene una evaluación registrada; puedes actualizarla.');
        }

        try {
            $aspirante->evaluacion()->create($request->validated());
        } catch (QueryException) {
            // Violación del índice único: otra solicitud registró la evaluación
            // entre la comprobación anterior y este insert.
            return redirect()
                ->route('evaluaciones.edit', $aspirante)
                ->with('info', 'Este aspirante ya tiene una evaluación registrada; puedes actualizarla.');
        }

        return redirect()
            ->route('aspirantes.show', $aspirante)
            ->with('exito', 'Evaluación registrada correctamente.');
    }

    public function edit(Aspirante $aspirante): View
    {
        $evaluacion = $aspirante->evaluacion()->firstOrFail();

        return view('evaluaciones.edit', [
            'aspirante' => $aspirante,
            'evaluacion' => $evaluacion,
        ]);
    }

    public function update(StoreEvaluacionRequest $request, Aspirante $aspirante): RedirectResponse
    {
        $evaluacion = $aspirante->evaluacion()->firstOrFail();
        $evaluacion->update($request->validated());

        return redirect()
            ->route('aspirantes.show', $aspirante)
            ->with('exito', 'Evaluación actualizada correctamente.');
    }
}
