<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisVendorEnum extends Enum
{
    use EnumTrait;

    const ASET = 'aset';

    const SPAREPART = 'sparepart';

    const SERVIS = 'servis';

    const LAINNYA = 'lainnya';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ASET => 'Aset',
            self::SPAREPART => 'Sparepart',
            self::SERVIS => 'Servis',
            self::LAINNYA => 'Lainnya',
            default => parent::getDescription($value),
        };
    }
}
