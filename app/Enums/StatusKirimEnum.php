<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusKirimEnum extends Enum
{
    use EnumTrait;

    const TERKIRIM = 'terkirim';

    const GAGAL = 'gagal';

    const TERTUNDA = 'tertunda';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERKIRIM => 'Terkirim',
            self::GAGAL => 'Gagal',
            self::TERTUNDA => 'Tertunda',
            default => parent::getDescription($value),
        };
    }
}
