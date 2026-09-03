<?php

namespace App\Enums\Pesanan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TipePesananEnum extends Enum
{
    use EnumTrait;

    const ASET = 'aset';

    const SUKU_CADANG = 'suku_cadang';

    const JASA = 'jasa';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ASET => 'Aset',
            self::SUKU_CADANG => 'Suku Cadang',
            self::JASA => 'Jasa',
            default => parent::getDescription($value),
        };
    }
}
