<?php

namespace App\Enums\Peminjaman;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPeminjamanEnum extends Enum
{
    use EnumTrait;

    const DIAJUKAN = 'diajukan';

    const DISETUJUI = 'disetujui';

    const AKTIF = 'aktif';

    const TERLAMBAT = 'terlambat';

    const DIKEMBALIKAN = 'dikembalikan';

    const DIBATALKAN = 'dibatalkan';

    const DITOLAK = 'ditolak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DIAJUKAN => 'Diajukan',
            self::DISETUJUI => 'Disetujui',
            self::AKTIF => 'Aktif',
            self::TERLAMBAT => 'Terlambat',
            self::DIKEMBALIKAN => 'Dikembalikan',
            self::DIBATALKAN => 'Dibatalkan',
            self::DITOLAK => 'Ditolak',
            default => parent::getDescription($value),
        };
    }
}
