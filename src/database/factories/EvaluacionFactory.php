<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Aspirante;
use App\Models\Evaluacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluacion>
 */
class EvaluacionFactory extends Factory
{
    protected $model = Evaluacion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'aspirante_id' => Aspirante::factory(),
            'conocimientos_tecnicos' => fake()->randomFloat(2, 5, 10),
            'referencias' => fake()->randomFloat(2, 5, 10),
            'habilidades_blandas' => fake()->randomFloat(2, 5, 10),
            'comentarios' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
