<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisMutasiEnum extends Enum
{
    use EnumTrait;

    const MASUK = 'masuk';

    const KELUAR = 'keluar';

    const PENYESUAIAN = 'penyesuaian';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::MASUK => 'Masuk',
            self::KELUAR => 'Keluar',
            self::PENYESUAIAN => 'Penyesuaian',
            default => parent::getDescription($value),
        };
    }
}
