<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class SumberTiketEnum extends Enum
{
    use EnumTrait;

    const LAPORAN = 'laporan';

    const SERVIS = 'servis';

    const SISTEM = 'sistem';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::LAPORAN => 'Laporan',
            self::SERVIS => 'Servis Berkala',
            self::SISTEM => 'Sistem',
            default => parent::getDescription($value),
        };
    }
}
