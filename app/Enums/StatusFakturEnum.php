<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusFakturEnum extends Enum
{
    use EnumTrait;

    const BELUM_COCOK = 'belum_cocok';

    const COCOK = 'cocok';

    const SELISIH = 'selisih';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BELUM_COCOK => 'Belum Dicocokkan',
            self::COCOK => 'Cocok',
            self::SELISIH => 'Ada Selisih',
            default => parent::getDescription($value),
        };
    }
}
