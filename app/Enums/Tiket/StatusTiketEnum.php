<?php

namespace App\Enums\Tiket;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusTiketEnum extends Enum
{
    use EnumTrait;

    const BUKA = 'buka';

    const DITUGASKAN = 'ditugaskan';

    const PROGRES = 'progres';

    const MENUNGGU_PART = 'menunggu_part';

    const SELESAI = 'selesai';

    const TERVERIFIKASI = 'terverifikasi';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BUKA => 'Buka',
            self::DITUGASKAN => 'Ditugaskan',
            self::PROGRES => 'Progres',
            self::MENUNGGU_PART => 'Menunggu Part',
            self::SELESAI => 'Selesai',
            self::TERVERIFIKASI => 'Terverifikasi',
            default => parent::getDescription($value),
        };
    }
}
