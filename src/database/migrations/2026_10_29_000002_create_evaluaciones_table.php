<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('aspirante_id')
                ->unique()
                ->constrained('aspirantes')
                ->cascadeOnDelete();
            $table->decimal('conocimientos_tecnicos', 4, 2);
            $table->decimal('referencias', 4, 2);
            $table->decimal('habilidades_blandas', 4, 2);
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });

        // Defensa en profundidad: la BD también garantiza el rango 0-10.
        // SQLite (usado en pruebas) no aplica CHECK de la misma forma.
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement(
                'ALTER TABLE evaluaciones ADD CONSTRAINT chk_evaluaciones_conocimientos
                 CHECK (conocimientos_tecnicos BETWEEN 0 AND 10)'
            );
            DB::statement(
                'ALTER TABLE evaluaciones ADD CONSTRAINT chk_evaluaciones_referencias
                 CHECK (referencias BETWEEN 0 AND 10)'
            );
            DB::statement(
                'ALTER TABLE evaluaciones ADD CONSTRAINT chk_evaluaciones_habilidades
                 CHECK (habilidades_blandas BETWEEN 0 AND 10)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
