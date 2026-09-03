<?php

namespace App\Enums\Tiket;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusTeknisiEnum extends Enum
{
    use EnumTrait;

    const TERSEDIA = 'tersedia';

    const SIBUK = 'sibuk';

    const OFFLINE = 'offline';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::TERSEDIA => 'Tersedia',
            self::SIBUK => 'Sibuk',
            self::OFFLINE => 'Offline',
            default => parent::getDescription($value),
        };
    }
}
