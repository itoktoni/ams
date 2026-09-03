<?php

namespace App\Enums\Perpindahan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPerpindahanEnum extends Enum
{
    use EnumTrait;

    const DIAJUKAN = 'diajukan';

    const DISETUJUI = 'disetujui';

    const TRANSIT = 'transit';

    const DITERIMA = 'diterima';

    const TERVERIFIKASI = 'terverifikasi';

    const DITOLAK = 'ditolak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DIAJUKAN => 'Diajukan',
            self::DISETUJUI => 'Disetujui',
            self::TRANSIT => 'In Transit',
            self::DITERIMA => 'Diterima',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::DITOLAK => 'Ditolak',
            default => parent::getDescription($value),
        };
    }
}
