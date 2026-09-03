<?php

namespace App\Enums\Alert;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusAlertEnum extends Enum
{
    use EnumTrait;

    const TERBUKA = 'terbuka';

    const DIAKUI = 'diakui';

    const SELESAI = 'selesai';

    const ESKALASI = 'eskalasi';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERBUKA => 'Terbuka',
            self::DIAKUI => 'Diakui',
            self::SELESAI => 'Selesai',
            self::ESKALASI => 'Eskalasi',
            default => parent::getDescription($value),
        };
    }
}
