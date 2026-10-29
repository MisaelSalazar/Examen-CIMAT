<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoCivil;
use App\Models\Aspirante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistroAspiranteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function datosValidos(array $overrides = []): array
    {
        return array_merge([
            'nombre_completo' => 'Ana Sofía Ramírez Torres',
            'puesto' => 'Desarrolladora Backend',
            'estado_civil' => EstadoCivil::Soltero->value,
            'fecha_nacimiento' => '1996-04-12',
            'carrera' => 'Ingeniería en Sistemas Computacionales',
        ], $overrides);
    }

    public function test_un_aspirante_puede_registrarse(): void
    {
        $respuesta = $this->post(route('aspirantes.store'), $this->datosValidos());

        $aspirante = Aspirante::query()->firstWhere('nombre_completo', 'Ana Sofía Ramírez Torres');

        $this->assertNotNull($aspirante);
        $respuesta->assertRedirect(route('aspirantes.show', $aspirante));
        $respuesta->assertSessionHas('exito');

        $this->assertDatabaseHas('aspirantes', [
            'nombre_completo' => 'Ana Sofía Ramírez Torres',
            'puesto' => 'Desarrolladora Backend',
            'estado_civil' => EstadoCivil::Soltero->value,
            'carrera' => 'Ingeniería en Sistemas Computacionales',
        ]);
    }

    public function test_los_campos_obligatorios_son_validados(): void
    {
        $respuesta = $this->post(route('aspirantes.store'), []);

        $respuesta->assertSessionHasErrors([
            'nombre_completo',
            'puesto',
            'estado_civil',
            'fecha_nacimiento',
            'carrera',
        ]);

        $this->assertDatabaseCount('aspirantes', 0);
    }

    public function test_la_fecha_de_nacimiento_no_puede_ser_futura(): void
    {
        $respuesta = $this->post(route('aspirantes.store'), $this->datosValidos([
            'fecha_nacimiento' => now()->addDay()->format('Y-m-d'),
        ]));

        $respuesta->assertSessionHasErrors('fecha_nacimiento');
        $this->assertDatabaseCount('aspirantes', 0);
    }

    public function test_el_estado_civil_debe_ser_un_valor_valido(): void
    {
        $respuesta = $this->post(route('aspirantes.store'), $this->datosValidos([
            'estado_civil' => 'inventado',
        ]));

        $respuesta->assertSessionHasErrors('estado_civil');
        $this->assertDatabaseCount('aspirantes', 0);
    }

    public function test_el_nombre_no_admite_caracteres_invalidos(): void
    {
        $respuesta = $this->post(route('aspirantes.store'), $this->datosValidos([
            'nombre_completo' => 'Ana 123 <script>',
        ]));

        $respuesta->assertSessionHasErrors('nombre_completo');
        $this->assertDatabaseCount('aspirantes', 0);
    }

    public function test_el_formulario_de_registro_es_accesible(): void
    {
        $this->get(route('aspirantes.create'))
            ->assertOk()
            ->assertSee('Registro de aspirante')
            ->assertSee('Nombre completo');
    }
}
