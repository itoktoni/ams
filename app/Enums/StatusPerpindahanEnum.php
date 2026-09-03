<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPerpindahanEnum extends Enum
{
    use EnumTrait;

    const DIMINTA = 'diminta';

    const DISETUJUI = 'disetujui';

    const DALAM_PERJALANAN = 'dalam_perjalanan';

    const DITERIMA = 'diterima';

    const TERVERIFIKASI = 'terverifikasi';

    const DITOLAK = 'ditolak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DIMINTA => 'Diminta',
            self::DISETUJUI => 'Disetujui',
            self::DALAM_PERJALANAN => 'Dalam Perjalanan',
            self::DITERIMA => 'Diterima',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::DITOLAK => 'Ditolak',
            default => parent::getDescription($value),
        };
    }
}
