<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPeminjamanEnum extends Enum
{
    use EnumTrait;

    const DIMINTA = 'diminta';

    const DISETUJUI = 'disetujui';

    const BERJALAN = 'berjalan';

    const TERLAMBAT = 'terlambat';

    const DIKEMBALIKAN = 'dikembalikan';

    const DITOLAK = 'ditolak';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DIMINTA => 'Diminta',
            self::DISETUJUI => 'Disetujui',
            self::BERJALAN => 'Berjalan',
            self::TERLAMBAT => 'Terlambat',
            self::DIKEMBALIKAN => 'Dikembalikan',
            self::DITOLAK => 'Ditolak',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
