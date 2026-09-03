<?php

namespace App\Enums\Pesanan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusFakturEnum extends Enum
{
    use EnumTrait;

    const COCOK = 'cocok';

    const BELUM_COCOK = 'belum_cocok';

    const SELISIH = 'selisih';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::COCOK => 'Cocok',
            self::BELUM_COCOK => 'Belum Cocok',
            self::SELISIH => 'Selisih',
            default => parent::getDescription($value),
        };
    }
}
