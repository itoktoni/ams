<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusOpnameEnum extends Enum
{
    use EnumTrait;

    const DRAF = 'draf';

    const BERJALAN = 'berjalan';

    const SELESAI = 'selesai';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAF => 'Draf',
            self::BERJALAN => 'Berjalan',
            self::SELESAI => 'Selesai',
            default => parent::getDescription($value),
        };
    }
}
