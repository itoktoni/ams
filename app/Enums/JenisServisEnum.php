<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisServisEnum extends Enum
{
    use EnumTrait;

    const BERKALA = 'berkala';

    const PERBAIKAN = 'perbaikan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BERKALA => 'Servis Berkala',
            self::PERBAIKAN => 'Perbaikan',
            default => parent::getDescription($value),
        };
    }
}
