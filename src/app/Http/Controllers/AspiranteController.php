<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EstadoCivil;
use App\Http\Requests\StoreAspiranteRequest;
use App\Models\Aspirante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AspiranteController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado_civil' => ['nullable', Rule::enum(EstadoCivil::class)],
        ]);

        $aspirantes = Aspirante::query()
            ->with('evaluacion')
            ->filtrar(
                buscar: $filtros['buscar'] ?? null,
                estadoCivil: isset($filtros['estado_civil'])
                    ? EstadoCivil::from($filtros['estado_civil'])
                    : null,
            )
            ->orderBy('nombre_completo')
            ->paginate(10)
            ->withQueryString();

        return view('aspirantes.index', [
            'aspirantes' => $aspirantes,
            'estadosCiviles' => EstadoCivil::options(),
            'buscar' => $filtros['buscar'] ?? null,
            'estadoCivilSeleccionado' => $filtros['estado_civil'] ?? null,
            'totalAspirantes' => Aspirante::query()->count(),
        ]);
    }

    public function create(): View
    {
        return view('aspirantes.create', [
            'estadosCiviles' => EstadoCivil::options(),
        ]);
    }

    public function store(StoreAspiranteRequest $request): RedirectResponse
    {
        $aspirante = Aspirante::query()->create($request->validated());

        return redirect()
            ->route('aspirantes.show', $aspirante)
            ->with('exito', "Aspirante «{$aspirante->nombre_completo}» registrado correctamente.");
    }

    public function show(Aspirante $aspirante): View
    {
        $aspirante->loadMissing('evaluacion');

        return view('aspirantes.show', [
            'aspirante' => $aspirante,
        ]);
    }
}
