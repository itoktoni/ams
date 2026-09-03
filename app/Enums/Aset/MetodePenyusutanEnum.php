<?php

namespace App\Enums\Aset;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class MetodePenyusutanEnum extends Enum
{
    use EnumTrait;

    const GARIS_LURUS = 'garis_lurus';

    const SALDO_MENURUN = 'saldo_menurun';

    const UNIT_PRODUKSI = 'unit_produksi';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::GARIS_LURUS => 'Garis Lurus',
            self::SALDO_MENURUN => 'Saldo Menurun',
            self::UNIT_PRODUKSI => 'Unit Produksi',
            default => parent::getDescription($value),
        };
    }
}
