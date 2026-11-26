<?php

namespace App\Enums\Bbm;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisKendaraanBbmEnum extends Enum
{
    use EnumTrait;

    const MOBIL = 'mobil';

    const MOTOR = 'motor';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::MOBIL => 'Mobil',
            self::MOTOR => 'Motor',
            default => parent::getDescription($value),
        };
    }
}
