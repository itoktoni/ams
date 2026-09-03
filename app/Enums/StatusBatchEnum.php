<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusBatchEnum extends Enum
{
    use EnumTrait;

    const DITAWARKAN = 'ditawarkan';

    const DITERIMA = 'diterima';

    const DITOLAK = 'ditolak';

    const SELESAI = 'selesai';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DITAWARKAN => 'Ditawarkan',
            self::DITERIMA => 'Diterima',
            self::DITOLAK => 'Ditolak',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
