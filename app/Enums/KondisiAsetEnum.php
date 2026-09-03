<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class KondisiAsetEnum extends Enum
{
    use EnumTrait;

    const BAIK = 'baik';

    const KURANG_BAIK = 'kurang_baik';

    const RUSAK_RINGAN = 'rusak_ringan';

    const RUSAK_BERAT = 'rusak_berat';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BAIK => 'Baik',
            self::KURANG_BAIK => 'Kurang Baik',
            self::RUSAK_RINGAN => 'Rusak Ringan',
            self::RUSAK_BERAT => 'Rusak Berat',
            default => parent::getDescription($value),
        };
    }
}
