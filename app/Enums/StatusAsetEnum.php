<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusAsetEnum extends Enum
{
    use EnumTrait;

    const TERSEDIA = 'tersedia';

    const DIPINJAM = 'dipinjam';

    const PERBAIKAN = 'perbaikan';

    const RUSAK = 'rusak';

    const KARANTINA = 'karantina';

    const AFKIR = 'afkir';

    const DIHAPUS = 'dihapus';

    const DIJUAL = 'dijual';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERSEDIA => 'Tersedia',
            self::DIPINJAM => 'Dipinjam',
            self::PERBAIKAN => 'Dalam Perbaikan',
            self::RUSAK => 'Rusak',
            self::KARANTINA => 'Karantina',
            self::AFKIR => 'Afkir',
            self::DIHAPUS => 'Dihapus',
            self::DIJUAL => 'Dijual',
            default => parent::getDescription($value),
        };
    }
}
