<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoCivil: string
{
    case Soltero = 'soltero';
    case Casado = 'casado';
    case Divorciado = 'divorciado';
    case Viudo = 'viudo';
    case UnionLibre = 'union_libre';

    public function label(): string
    {
        return match ($this) {
            self::Soltero => 'Soltero(a)',
            self::Casado => 'Casado(a)',
            self::Divorciado => 'Divorciado(a)',
            self::Viudo => 'Viudo(a)',
            self::UnionLibre => 'Unión libre',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $estado): array => [$estado->value => $estado->label()])
            ->all();
    }
}
