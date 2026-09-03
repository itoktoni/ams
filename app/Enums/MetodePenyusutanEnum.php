<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class MetodePenyusutanEnum extends Enum
{
    use EnumTrait;

    const GARIS_LURUS = 'garis_lurus';

    const SALDO_MENURUN = 'saldo_menurun';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::GARIS_LURUS => 'Garis Lurus',
            self::SALDO_MENURUN => 'Saldo Menurun',
            default => parent::getDescription($value),
        };
    }
}
