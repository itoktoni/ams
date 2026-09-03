<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPenerimaanEnum extends Enum
{
    use EnumTrait;

    const DITERIMA = 'diterima';

    const SEBAGIAN = 'sebagian';

    const DITOLAK = 'ditolak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DITERIMA => 'Diterima',
            self::SEBAGIAN => 'Diterima Sebagian',
            self::DITOLAK => 'Ditolak',
            default => parent::getDescription($value),
        };
    }
}
