<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class NamaBinEnum extends Enum
{
    use EnumTrait;

    const AKTIF = 'aktif';

    const CADANGAN = 'cadangan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::AKTIF => 'Bin Aktif',
            self::CADANGAN => 'Bin Cadangan',
            default => parent::getDescription($value),
        };
    }
}
