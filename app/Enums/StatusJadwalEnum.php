<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusJadwalEnum extends Enum
{
    use EnumTrait;

    const AKTIF = 'aktif';

    const SELESAI = 'selesai';

    const TERLEWAT = 'terlewat';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::AKTIF => 'Aktif',
            self::SELESAI => 'Selesai',
            self::TERLEWAT => 'Terlewat',
            default => parent::getDescription($value),
        };
    }
}
