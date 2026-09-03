<?php

namespace App\Enums\Tiket;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisKeahlianEnum extends Enum
{
    use EnumTrait;

    const ELEKTRIKAL = 'elektrikal';

    const HVAC = 'hvac';

    const IT = 'it';

    const MEKANIKAL = 'mekanikal';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ELEKTRIKAL => 'Elektrikal',
            self::HVAC => 'HVAC',
            self::IT => 'IT',
            self::MEKANIKAL => 'Mekanikal',
            default => parent::getDescription($value),
        };
    }
}
