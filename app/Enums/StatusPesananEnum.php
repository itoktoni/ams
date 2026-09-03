<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPesananEnum extends Enum
{
    use EnumTrait;

    const DRAF = 'draf';

    const MENUNGGU = 'menunggu';

    const DISETUJUI = 'disetujui';

    const DIKIRIM = 'dikirim';

    const DITERIMA_SEBAGIAN = 'diterima_sebagian';

    const DITERIMA = 'diterima';

    const SELESAI = 'selesai';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAF => 'Draf',
            self::MENUNGGU => 'Menunggu Persetujuan',
            self::DISETUJUI => 'Disetujui',
            self::DIKIRIM => 'Dikirim',
            self::DITERIMA_SEBAGIAN => 'Diterima Sebagian',
            self::DITERIMA => 'Diterima',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
