<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusAntreanEnum extends Enum
{
    use EnumTrait;

    const MENUNGGU = 'menunggu';

    const DIALOKASIKAN = 'dialokasikan';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::MENUNGGU => 'Menunggu',
            self::DIALOKASIKAN => 'Dialokasikan',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
