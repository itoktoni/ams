<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TriaseEnum extends Enum
{
    use EnumTrait;

    const PERBAIKI = 'perbaiki';

    const KANIBAL = 'kanibal';

    const BUANG = 'buang';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PERBAIKI => 'Refurbish',
            self::KANIBAL => 'Kanibal Sparepart',
            self::BUANG => 'Disposal',
            default => parent::getDescription($value),
        };
    }
}
