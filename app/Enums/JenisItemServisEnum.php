<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisItemServisEnum extends Enum
{
    use EnumTrait;

    const PERIKSA = 'periksa';

    const GANTI = 'ganti';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PERIKSA => 'Periksa',
            self::GANTI => 'Ganti',
            default => parent::getDescription($value),
        };
    }
}
