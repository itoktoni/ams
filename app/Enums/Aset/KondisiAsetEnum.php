<?php

namespace App\Enums\Aset;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class KondisiAsetEnum extends Enum
{
    use EnumTrait;

    const BARU = 'baru';

    const BAIK = 'baik';

    const KURANG_BAIK = 'kurang_baik';

    const RUSAK = 'rusak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BARU => 'Baru',
            self::BAIK => 'Baik',
            self::KURANG_BAIK => 'Kurang Baik',
            self::RUSAK => 'Rusak',
            default => parent::getDescription($value),
        };
    }
}
