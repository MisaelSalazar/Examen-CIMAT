<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoCivil;
use App\Models\Aspirante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Aspirante>
 */
class AspiranteFactory extends Factory
{
    protected $model = Aspirante::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_completo' => fake()->name(),
            'puesto' => fake()->randomElement([
                'Desarrollador Backend',
                'Desarrollador Frontend',
                'Analista de Datos',
                'Ingeniero DevOps',
                'Diseñador UX/UI',
                'QA Engineer',
                'Project Manager',
            ]),
            'estado_civil' => fake()->randomElement(EstadoCivil::cases()),
            'fecha_nacimiento' => fake()->dateTimeBetween('-55 years', '-22 years'),
            'carrera' => fake()->randomElement([
                'Ingeniería en Sistemas Computacionales',
                'Ingeniería en Software',
                'Ciencias de la Computación',
                'Ingeniería Industrial',
                'Licenciatura en Administración',
                'Diseño Gráfico',
            ]),
        ];
    }
}
