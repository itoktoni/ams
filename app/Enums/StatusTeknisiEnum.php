<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusTeknisiEnum extends Enum
{
    use EnumTrait;

    const TERSEDIA = 'tersedia';

    const SIBUK = 'sibuk';

    const LIBUR = 'libur';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERSEDIA => 'Tersedia',
            self::SIBUK => 'Sibuk',
            self::LIBUR => 'Libur',
            default => parent::getDescription($value),
        };
    }
}
