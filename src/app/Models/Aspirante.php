<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoCivil;
use Database\Factories\AspiranteFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre_completo
 * @property string $puesto
 * @property EstadoCivil $estado_civil
 * @property Carbon $fecha_nacimiento
 * @property string $carrera
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Evaluacion|null $evaluacion
 */
class Aspirante extends Model
{
    /** @use HasFactory<AspiranteFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nombre_completo',
        'puesto',
        'estado_civil',
        'fecha_nacimiento',
        'carrera',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estado_civil' => EstadoCivil::class,
            'fecha_nacimiento' => 'date',
        ];
    }

    public function evaluacion(): HasOne
    {
        return $this->hasOne(Evaluacion::class);
    }

    public function edad(): int
    {
        return (int) $this->fecha_nacimiento->age;
    }

    /**
     * Filtra por nombre o puesto y, opcionalmente, por estado civil.
     *
     * @param  Builder<Aspirante>  $query
     * @return Builder<Aspirante>
     */
    public function scopeFiltrar(Builder $query, ?string $buscar, ?EstadoCivil $estadoCivil): Builder
    {
        return $query
            ->when(
                filled($buscar),
                fn (Builder $query): Builder => $query->where(
                    fn (Builder $query): Builder => $query
                        ->where('nombre_completo', 'like', "%{$buscar}%")
                        ->orWhere('puesto', 'like', "%{$buscar}%")
                        ->orWhere('carrera', 'like', "%{$buscar}%"),
                ),
            )
            ->when(
                $estadoCivil !== null,
                fn (Builder $query): Builder => $query->where('estado_civil', $estadoCivil->value),
            );
    }
}
