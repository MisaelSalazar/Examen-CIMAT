<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación
    |--------------------------------------------------------------------------
    |
    | Traducciones al español de las reglas utilizadas por la aplicación.
    | Las claves ausentes se resuelven con el locale de respaldo (en).
    |
    */

    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'array' => 'El campo :attribute debe ser una lista.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'url' => 'El campo :attribute debe ser una URL válida.',
    'enum' => 'El valor seleccionado en :attribute no es válido.',

    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],

    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe superar los :max caracteres.',
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
    ],

    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],

    'decimal' => 'El campo :attribute debe tener :decimal decimales.',

    'regex' => 'El formato del campo :attribute no es válido.',

    'after' => 'El campo :attribute debe ser una fecha posterior al :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual al :date.',
    'before' => 'El campo :attribute debe ser una fecha anterior al :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual al :date.',

    'unique' => 'El valor del campo :attribute ya está registrado.',
    'exists' => 'El valor seleccionado en :attribute no existe.',

    'attributes' => [],

];
