<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisJurnalEnum extends Enum
{
    use EnumTrait;

    const PENYUSUTAN = 'penyusutan';

    const PENYESUAIAN = 'penyesuaian';

    const KOREKSI = 'koreksi';

    const PEMBALIK = 'pembalik';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PENYUSUTAN => 'Penyusutan',
            self::PENYESUAIAN => 'Penyesuaian',
            self::KOREKSI => 'Koreksi',
            self::PEMBALIK => 'Jurnal Pembalik',
            default => parent::getDescription($value),
        };
    }
}
