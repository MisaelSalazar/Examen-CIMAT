<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoCivil;
use App\Models\Aspirante;
use Illuminate\Database\Seeder;

class AspiranteSeeder extends Seeder
{
    /**
     * Datos de demostración. El seeder es idempotente: puede ejecutarse
     * varias veces sin duplicar registros.
     */
    public function run(): void
    {
        foreach ($this->aspirantes() as $datos) {
            $evaluacion = $datos['evaluacion'] ?? null;
            unset($datos['evaluacion']);

            $aspirante = Aspirante::query()->firstOrCreate(
                [
                    'nombre_completo' => $datos['nombre_completo'],
                    'puesto' => $datos['puesto'],
                ],
                $datos,
            );

            if ($evaluacion !== null) {
                $aspirante->evaluacion()->updateOrCreate([], $evaluacion);
            }
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function aspirantes(): array
    {
        return [
            [
                'nombre_completo' => 'Ana Sofía Ramírez Torres',
                'puesto' => 'Desarrolladora Backend',
                'estado_civil' => EstadoCivil::Soltero,
                'fecha_nacimiento' => '1996-04-12',
                'carrera' => 'Ingeniería en Sistemas Computacionales',
                'evaluacion' => [
                    'conocimientos_tecnicos' => 9.25,
                    'referencias' => 8.50,
                    'habilidades_blandas' => 9.00,
                    'comentarios' => 'Excelente dominio de Laravel y SQL. Comunicación clara y buena disposición para el trabajo en equipo.',
                ],
            ],
            [
                'nombre_completo' => 'Carlos Alberto Mendoza Ruiz',
                'puesto' => 'Ingeniero DevOps',
                'estado_civil' => EstadoCivil::Casado,
                'fecha_nacimiento' => '1989-11-03',
                'carrera' => 'Ingeniería en Software',
                'evaluacion' => [
                    'conocimientos_tecnicos' => 8.75,
                    'referencias' => 9.00,
                    'habilidades_blandas' => 7.50,
                    'comentarios' => 'Sólida experiencia en Docker y CI/CD. Puede mejorar la comunicación con áreas no técnicas.',
                ],
            ],
            [
                'nombre_completo' => 'María Fernanda López García',
                'puesto' => 'Analista de Datos',
                'estado_civil' => EstadoCivil::UnionLibre,
                'fecha_nacimiento' => '1998-07-21',
                'carrera' => 'Ciencias de la Computación',
                'evaluacion' => [
                    'conocimientos_tecnicos' => 7.00,
                    'referencias' => 8.25,
                    'habilidades_blandas' => 8.75,
                    'comentarios' => 'Buen pensamiento analítico. Requiere reforzar modelado de datos avanzado.',
                ],
            ],
            [
                'nombre_completo' => 'Jorge Luis Hernández Vega',
                'puesto' => 'Desarrollador Frontend',
                'estado_civil' => EstadoCivil::Soltero,
                'fecha_nacimiento' => '2000-02-09',
                'carrera' => 'Diseño Gráfico',
                'evaluacion' => [
                    'conocimientos_tecnicos' => 6.50,
                    'referencias' => 7.00,
                    'habilidades_blandas' => 9.25,
                    'comentarios' => 'Muy buena sensibilidad visual y trato con el cliente. En proceso de consolidar JavaScript avanzado.',
                ],
            ],
            [
                'nombre_completo' => 'Patricia Gómez Salazar',
                'puesto' => 'QA Engineer',
                'estado_civil' => EstadoCivil::Divorciado,
                'fecha_nacimiento' => '1992-09-30',
                'carrera' => 'Ingeniería Industrial',
                'evaluacion' => [
                    'conocimientos_tecnicos' => 8.00,
                    'referencias' => 8.75,
                    'habilidades_blandas' => 8.00,
                    'comentarios' => 'Metódica y detallista. Aporta estructura a los procesos de prueba.',
                ],
            ],
            [
                'nombre_completo' => 'Luis Ángel Morales Díaz',
                'puesto' => 'Project Manager',
                'estado_civil' => EstadoCivil::Casado,
                'fecha_nacimiento' => '1985-01-17',
                'carrera' => 'Licenciatura en Administración',
                'evaluacion' => [
                    'conocimientos_tecnicos' => 7.25,
                    'referencias' => 9.50,
                    'habilidades_blandas' => 9.00,
                    'comentarios' => 'Referencias sobresalientes y liderazgo comprobado en equipos distribuidos.',
                ],
            ],
            [
                'nombre_completo' => 'Daniela Ortega Flores',
                'puesto' => 'Diseñadora UX/UI',
                'estado_civil' => EstadoCivil::Soltero,
                'fecha_nacimiento' => '1997-12-05',
                'carrera' => 'Diseño Gráfico',
            ],
            [
                'nombre_completo' => 'Ricardo Núñez Castillo',
                'puesto' => 'Desarrollador Backend',
                'estado_civil' => EstadoCivil::Viudo,
                'fecha_nacimiento' => '1983-06-14',
                'carrera' => 'Ingeniería en Sistemas Computacionales',
            ],
        ];
    }
}
