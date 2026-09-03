<?php

namespace App\Enums\Stok;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TipePergerakanStokEnum extends Enum
{
    use EnumTrait;

    const MASUK = 'masuk';

    const KELUAR = 'keluar';

    const OPNAME = 'opname';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::MASUK => 'Masuk',
            self::KELUAR => 'Keluar',
            self::OPNAME => 'Opname',
            default => parent::getDescription($value),
        };
    }
}
