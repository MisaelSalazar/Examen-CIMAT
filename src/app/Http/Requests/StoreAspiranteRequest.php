<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EstadoCivil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAspiranteRequest extends FormRequest
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
            'nombre_completo' => [
                'required',
                'string',
                'min:3',
                'max:150',
                "regex:/^[\p{L}\s'.-]+$/u",
            ],
            'puesto' => ['required', 'string', 'min:3', 'max:120'],
            'estado_civil' => ['required', Rule::enum(EstadoCivil::class)],
            'fecha_nacimiento' => [
                'required',
                'date',
                'after:1900-01-01',
                'before:today',
            ],
            'carrera' => ['required', 'string', 'min:3', 'max:150'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'puesto' => 'puesto',
            'estado_civil' => 'estado civil',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'carrera' => 'carrera o formación académica',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre_completo.regex' => 'El nombre completo solo puede contener letras, espacios, apóstrofes, puntos y guiones.',
        ];
    }
}
