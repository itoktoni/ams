<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisPesananEnum extends Enum
{
    use EnumTrait;

    const ASET = 'aset';

    const SPAREPART = 'sparepart';

    const SERVIS = 'servis';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ASET => 'Aset',
            self::SPAREPART => 'Sparepart',
            self::SERVIS => 'Servis',
            default => parent::getDescription($value),
        };
    }
}
