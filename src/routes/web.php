<?php

declare(strict_types=1);

use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\EvaluacionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/aspirantes');

Route::get('/aspirantes', [AspiranteController::class, 'index'])
    ->name('aspirantes.index');
Route::get('/aspirantes/crear', [AspiranteController::class, 'create'])
    ->name('aspirantes.create');
Route::post('/aspirantes', [AspiranteController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('aspirantes.store');
Route::get('/aspirantes/{aspirante}', [AspiranteController::class, 'show'])
    ->whereNumber('aspirante')
    ->name('aspirantes.show');

Route::get('/aspirantes/{aspirante}/evaluacion/crear', [EvaluacionController::class, 'create'])
    ->whereNumber('aspirante')
    ->name('evaluaciones.create');
Route::post('/aspirantes/{aspirante}/evaluacion', [EvaluacionController::class, 'store'])
    ->whereNumber('aspirante')
    ->middleware('throttle:30,1')
    ->name('evaluaciones.store');
Route::get('/aspirantes/{aspirante}/evaluacion/editar', [EvaluacionController::class, 'edit'])
    ->whereNumber('aspirante')
    ->name('evaluaciones.edit');
Route::put('/aspirantes/{aspirante}/evaluacion', [EvaluacionController::class, 'update'])
    ->whereNumber('aspirante')
    ->middleware('throttle:30,1')
    ->name('evaluaciones.update');
