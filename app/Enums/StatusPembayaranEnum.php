<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPembayaranEnum extends Enum
{
    use EnumTrait;

    const BELUM_BAYAR = 'belum_bayar';

    const SUDAH_BAYAR = 'sudah_bayar';

    const TERLAMBAT = 'terlambat';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BELUM_BAYAR => 'Belum Bayar',
            self::SUDAH_BAYAR => 'Sudah Bayar',
            self::TERLAMBAT => 'Terlambat',
            default => parent::getDescription($value),
        };
    }
}
