<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'conocimientos_tecnicos' => ['required', 'numeric', 'between:0,10', 'decimal:0,2'],
            'referencias' => ['required', 'numeric', 'between:0,10', 'decimal:0,2'],
            'habilidades_blandas' => ['required', 'numeric', 'between:0,10', 'decimal:0,2'],
            'comentarios' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'conocimientos_tecnicos' => 'conocimientos técnicos',
            'referencias' => 'referencias',
            'habilidades_blandas' => 'habilidades blandas',
            'comentarios' => 'comentarios generales',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'conocimientos_tecnicos.between' => 'La calificación de conocimientos técnicos debe estar entre 0 y 10.',
            'referencias.between' => 'La calificación de referencias debe estar entre 0 y 10.',
            'habilidades_blandas.between' => 'La calificación de habilidades blandas debe estar entre 0 y 10.',
            'conocimientos_tecnicos.decimal' => 'La calificación de conocimientos técnicos admite como máximo 2 decimales.',
            'referencias.decimal' => 'La calificación de referencias admite como máximo 2 decimales.',
            'habilidades_blandas.decimal' => 'La calificación de habilidades blandas admite como máximo 2 decimales.',
        ];
    }
}
