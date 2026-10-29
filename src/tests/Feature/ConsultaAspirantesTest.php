<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoCivil;
use App\Models\Aspirante;
use App\Models\Evaluacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaAspirantesTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_listado_muestra_a_los_aspirantes(): void
    {
        $aspirante = Aspirante::factory()->create([
            'nombre_completo' => 'María Fernanda López García',
            'puesto' => 'Analista de Datos',
            'estado_civil' => EstadoCivil::UnionLibre,
        ]);

        $this->get(route('aspirantes.index'))
            ->assertOk()
            ->assertSee('María Fernanda López García')
            ->assertSee('Analista de Datos')
            ->assertSee('Unión libre')
            ->assertSee('Pendiente');
    }

    public function test_el_listado_se_puede_filtrar_por_estado_civil(): void
    {
        Aspirante::factory()->create([
            'nombre_completo' => 'Aspirante Soltero',
            'estado_civil' => EstadoCivil::Soltero,
        ]);
        Aspirante::factory()->create([
            'nombre_completo' => 'Aspirante Casado',
            'estado_civil' => EstadoCivil::Casado,
        ]);

        $this->get(route('aspirantes.index', ['estado_civil' => EstadoCivil::Casado->value]))
            ->assertOk()
            ->assertSee('Aspirante Casado')
            ->assertDontSee('Aspirante Soltero');
    }

    public function test_el_listado_se_puede_buscar_por_nombre_o_puesto(): void
    {
        Aspirante::factory()->create([
            'nombre_completo' => 'Búsqueda Coincidente',
            'puesto' => 'Ingeniero DevOps',
        ]);
        Aspirante::factory()->create([
            'nombre_completo' => 'Otro Registro',
            'puesto' => 'Diseñador UX/UI',
        ]);

        $this->get(route('aspirantes.index', ['buscar' => 'Coincidente']))
            ->assertOk()
            ->assertSee('Búsqueda Coincidente')
            ->assertDontSee('Otro Registro');
    }

    public function test_la_ficha_muestra_datos_y_evaluacion(): void
    {
        $aspirante = Aspirante::factory()->create([
            'nombre_completo' => 'Carlos Alberto Mendoza Ruiz',
            'puesto' => 'Ingeniero DevOps',
            'carrera' => 'Ingeniería en Software',
        ]);
        Evaluacion::factory()->for($aspirante)->create([
            'conocimientos_tecnicos' => 9,
            'referencias' => 8.5,
            'habilidades_blandas' => 7.25,
            'comentarios' => 'Referencias sobresalientes.',
        ]);

        $this->get(route('aspirantes.show', $aspirante))
            ->assertOk()
            ->assertSee('Carlos Alberto Mendoza Ruiz')
            ->assertSee('Ingeniería en Software')
            ->assertSee('Referencias sobresalientes.')
            ->assertSee('8.25')
            ->assertSee('Evaluado');
    }

    public function test_la_ficha_de_un_aspirante_sin_evaluacion_muestra_estado_pendiente(): void
    {
        $aspirante = Aspirante::factory()->create();

        $this->get(route('aspirantes.show', $aspirante))
            ->assertOk()
            ->assertSee('Evaluación pendiente')
            ->assertSee('Registrar evaluación');
    }

    public function test_un_aspirante_inexistente_devuelve_404(): void
    {
        $this->get('/aspirantes/999999')->assertNotFound();
    }

    public function test_la_raiz_redirige_al_listado(): void
    {
        $this->get('/')->assertRedirect(route('aspirantes.index'));
    }
}
