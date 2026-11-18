<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisPembayaranEnum extends Enum
{
    use EnumTrait;

    const AIR = 'air';

    const LISTRIK = 'listrik';

    const INTERNET = 'internet';

    const TELEPON = 'telepon';

    const SEWA = 'sewa';

    const LAINNYA = 'lainnya';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::AIR => 'Air',
            self::LISTRIK => 'Listrik',
            self::INTERNET => 'Internet',
            self::TELEPON => 'Telepon',
            self::SEWA => 'Sewa',
            self::LAINNYA => 'Lainnya',
            default => parent::getDescription($value),
        };
    }
}
