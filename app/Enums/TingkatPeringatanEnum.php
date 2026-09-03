<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TingkatPeringatanEnum extends Enum
{
    use EnumTrait;

    const INFO = 'info';

    const WASPADA = 'waspada';

    const KRITIS = 'kritis';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::INFO => 'Info',
            self::WASPADA => 'Waspada',
            self::KRITIS => 'Kritis',
            default => parent::getDescription($value),
        };
    }
}
