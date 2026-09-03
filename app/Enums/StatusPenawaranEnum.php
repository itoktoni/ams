<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPenawaranEnum extends Enum
{
    use EnumTrait;

    const DIAJUKAN = 'diajukan';

    const NEGOSIASI = 'negosiasi';

    const DITERIMA = 'diterima';

    const DITOLAK = 'ditolak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DIAJUKAN => 'Diajukan',
            self::NEGOSIASI => 'Negosiasi',
            self::DITERIMA => 'Diterima',
            self::DITOLAK => 'Ditolak',
            default => parent::getDescription($value),
        };
    }
}
