<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EvaluacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $aspirante_id
 * @property string $conocimientos_tecnicos
 * @property string $referencias
 * @property string $habilidades_blandas
 * @property string|null $comentarios
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Aspirante $aspirante
 * @property-read float $promedio
 */
class Evaluacion extends Model
{
    /** @use HasFactory<EvaluacionFactory> */
    use HasFactory;

    protected $table = 'evaluaciones';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'conocimientos_tecnicos',
        'referencias',
        'habilidades_blandas',
        'comentarios',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conocimientos_tecnicos' => 'decimal:2',
            'referencias' => 'decimal:2',
            'habilidades_blandas' => 'decimal:2',
        ];
    }

    public function aspirante(): BelongsTo
    {
        return $this->belongsTo(Aspirante::class);
    }

    public function promedio(): float
    {
        $suma = (float) $this->conocimientos_tecnicos
            + (float) $this->referencias
            + (float) $this->habilidades_blandas;

        return round($suma / 3, 2);
    }

    /**
     * @return array<string, float>
     */
    public function criterios(): array
    {
        return [
            'Conocimientos técnicos' => (float) $this->conocimientos_tecnicos,
            'Referencias' => (float) $this->referencias,
            'Habilidades blandas' => (float) $this->habilidades_blandas,
        ];
    }
}
