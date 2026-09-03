<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class KeahlianEnum extends Enum
{
    use EnumTrait;

    const LISTRIK = 'listrik';

    const HVAC = 'hvac';

    const IT = 'it';

    const MEKANIK = 'mekanik';

    const SIPIL = 'sipil';

    const KENDARAAN = 'kendaraan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::LISTRIK => 'Listrik',
            self::HVAC => 'HVAC',
            self::IT => 'IT',
            self::MEKANIK => 'Mekanik',
            self::SIPIL => 'Sipil',
            self::KENDARAAN => 'Kendaraan',
            default => parent::getDescription($value),
        };
    }
}
