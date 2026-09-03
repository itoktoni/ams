<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusTiketEnum extends Enum
{
    use EnumTrait;

    const TERBUKA = 'terbuka';

    const DITUGASKAN = 'ditugaskan';

    const DIKERJAKAN = 'dikerjakan';

    const MENUNGGU_SPAREPART = 'menunggu_sparepart';

    const SELESAI = 'selesai';

    const DIVERIFIKASI = 'diverifikasi';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERBUKA => 'Terbuka',
            self::DITUGASKAN => 'Ditugaskan',
            self::DIKERJAKAN => 'Dikerjakan',
            self::MENUNGGU_SPAREPART => 'Menunggu Sparepart',
            self::SELESAI => 'Selesai',
            self::DIVERIFIKASI => 'Diverifikasi',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
