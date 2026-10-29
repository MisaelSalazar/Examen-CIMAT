<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirantes', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre_completo', 150);
            $table->string('puesto', 120)->index();
            $table->string('estado_civil', 20)->index();
            $table->date('fecha_nacimiento');
            $table->string('carrera', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspirantes');
    }
};
