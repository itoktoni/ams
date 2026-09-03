<?php

namespace App\Enums\Alert;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class LevelAlertEnum extends Enum
{
    use EnumTrait;

    const INFO = 'info';

    const PERINGATAN = 'peringatan';

    const KRITIS = 'kritis';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::INFO => 'Info',
            self::PERINGATAN => 'Peringatan',
            self::KRITIS => 'Kritis',
            default => parent::getDescription($value),
        };
    }
}
