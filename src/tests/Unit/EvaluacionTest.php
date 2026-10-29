<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\EstadoCivil;
use App\Models\Evaluacion;
use PHPUnit\Framework\TestCase;

class EvaluacionTest extends TestCase
{
    public function test_el_promedio_se_calcula_con_dos_decimales(): void
    {
        $evaluacion = new Evaluacion([
            'conocimientos_tecnicos' => 9.25,
            'referencias' => 8.5,
            'habilidades_blandas' => 9,
        ]);

        $this->assertEqualsWithDelta(8.92, $evaluacion->promedio(), 0.0001);
    }

    public function test_los_criterios_devuelven_las_tres_calificaciones(): void
    {
        $evaluacion = new Evaluacion([
            'conocimientos_tecnicos' => 6,
            'referencias' => 7,
            'habilidades_blandas' => 8,
        ]);

        $this->assertSame([
            'Conocimientos técnicos' => 6.0,
            'Referencias' => 7.0,
            'Habilidades blandas' => 8.0,
        ], $evaluacion->criterios());
    }

    public function test_el_enum_de_estado_civil_expone_sus_etiquetas(): void
    {
        $this->assertSame('Unión libre', EstadoCivil::UnionLibre->label());
        $this->assertCount(5, EstadoCivil::options());
        $this->assertSame('Soltero(a)', EstadoCivil::options()['soltero']);
    }
}
