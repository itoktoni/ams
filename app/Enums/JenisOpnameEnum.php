<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisOpnameEnum extends Enum
{
    use EnumTrait;

    const ASET = 'aset';

    const SPAREPART = 'sparepart';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ASET => 'Opname Aset',
            self::SPAREPART => 'Opname Sparepart',
            default => parent::getDescription($value),
        };
    }
}
