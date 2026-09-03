<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPeringatanEnum extends Enum
{
    use EnumTrait;

    const TERBUKA = 'terbuka';

    const DIAKUI = 'diakui';

    const SELESAI = 'selesai';

    const DIABAIKAN = 'diabaikan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERBUKA => 'Terbuka',
            self::DIAKUI => 'Diakui',
            self::SELESAI => 'Selesai',
            self::DIABAIKAN => 'Diabaikan',
            default => parent::getDescription($value),
        };
    }
}
