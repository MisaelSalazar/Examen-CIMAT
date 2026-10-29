<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Aspirante;
use App\Models\Evaluacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EvaluacionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function datosValidos(array $overrides = []): array
    {
        return array_merge([
            'conocimientos_tecnicos' => 8.75,
            'referencias' => 9,
            'habilidades_blandas' => 7.5,
            'comentarios' => 'Buen desempeño general.',
        ], $overrides);
    }

    public function test_se_puede_registrar_una_evaluacion_con_decimales(): void
    {
        $aspirante = Aspirante::factory()->create();

        $respuesta = $this->post(route('evaluaciones.store', $aspirante), $this->datosValidos());

        $respuesta->assertRedirect(route('aspirantes.show', $aspirante));
        $respuesta->assertSessionHas('exito');

        $evaluacion = $aspirante->evaluacion()->first();

        $this->assertNotNull($evaluacion);
        $this->assertSame('8.75', $evaluacion->conocimientos_tecnicos);
        $this->assertSame('9.00', $evaluacion->referencias);
        $this->assertSame('7.50', $evaluacion->habilidades_blandas);
        $this->assertSame('Buen desempeño general.', $evaluacion->comentarios);
    }

    /**
     * @return array<string, array{float}>
     */
    public static function calificacionesFueraDeRango(): array
    {
        return [
            'negativa' => [-0.01],
            'mayor a diez' => [10.01],
            'muy alta' => [25],
        ];
    }

    #[DataProvider('calificacionesFueraDeRango')]
    public function test_las_calificaciones_fuera_de_rango_son_rechazadas(float $calificacion): void
    {
        $aspirante = Aspirante::factory()->create();

        $respuesta = $this->post(route('evaluaciones.store', $aspirante), $this->datosValidos([
            'conocimientos_tecnicos' => $calificacion,
        ]));

        $respuesta->assertSessionHasErrors('conocimientos_tecnicos');
        $this->assertDatabaseCount('evaluaciones', 0);
    }

    public function test_las_calificaciones_admiten_como_maximo_dos_decimales(): void
    {
        $aspirante = Aspirante::factory()->create();

        $respuesta = $this->post(route('evaluaciones.store', $aspirante), $this->datosValidos([
            'referencias' => 8.333,
        ]));

        $respuesta->assertSessionHasErrors('referencias');
        $this->assertDatabaseCount('evaluaciones', 0);
    }

    public function test_los_comentarios_son_opcionales(): void
    {
        $aspirante = Aspirante::factory()->create();

        $this->post(route('evaluaciones.store', $aspirante), $this->datosValidos([
            'comentarios' => null,
        ]))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('evaluaciones', [
            'aspirante_id' => $aspirante->id,
            'comentarios' => null,
        ]);
    }

    public function test_no_se_puede_registrar_una_segunda_evaluacion(): void
    {
        $aspirante = Aspirante::factory()->create();
        Evaluacion::factory()->for($aspirante)->create();

        $respuesta = $this->post(route('evaluaciones.store', $aspirante), $this->datosValidos());

        $respuesta->assertRedirect(route('evaluaciones.edit', $aspirante));
        $respuesta->assertSessionHas('info');
        $this->assertDatabaseCount('evaluaciones', 1);
    }

    public function test_se_puede_actualizar_la_evaluacion(): void
    {
        $aspirante = Aspirante::factory()->create();
        Evaluacion::factory()->for($aspirante)->create([
            'conocimientos_tecnicos' => 5,
            'referencias' => 5,
            'habilidades_blandas' => 5,
        ]);

        $respuesta = $this->put(route('evaluaciones.update', $aspirante), $this->datosValidos());

        $respuesta->assertRedirect(route('aspirantes.show', $aspirante));
        $respuesta->assertSessionHas('exito');

        $this->assertDatabaseHas('evaluaciones', [
            'aspirante_id' => $aspirante->id,
            'conocimientos_tecnicos' => 8.75,
        ]);
        $this->assertDatabaseCount('evaluaciones', 1);
    }

    public function test_la_forma_de_evaluacion_solo_es_accesible_si_no_existe_evaluacion(): void
    {
        $aspirante = Aspirante::factory()->create();
        Evaluacion::factory()->for($aspirante)->create();

        $this->get(route('evaluaciones.create', $aspirante))
            ->assertRedirect(route('evaluaciones.edit', $aspirante));

        $this->get(route('evaluaciones.edit', $aspirante))->assertOk();
    }
}
