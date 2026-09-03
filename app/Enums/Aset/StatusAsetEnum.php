<?php

namespace App\Enums\Aset;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusAsetEnum extends Enum
{
    use EnumTrait;

    const AKTIF = 'aktif';

    const DIPINJAM = 'dipinjam';

    const MAINTENANCE = 'maintenance';

    const RUSAK = 'rusak';

    const DIHAPUS = 'dihapus';

    const AFKIR = 'afkir';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::AKTIF => 'Aktif',
            self::DIPINJAM => 'Dipinjam',
            self::MAINTENANCE => 'Maintenance',
            self::RUSAK => 'Rusak',
            self::DIHAPUS => 'Dihapus',
            self::AFKIR => 'Afkir',
            default => parent::getDescription($value),
        };
    }
}
