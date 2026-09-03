<?php

namespace App\Enums\Tiket;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TingkatUrgensiEnum extends Enum
{
    use EnumTrait;

    const KRITIS = 'kritis';

    const TINGGI = 'tinggi';

    const SEDANG = 'sedang';

    const RENDAH = 'rendah';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::KRITIS => 'Kritis',
            self::TINGGI => 'Tinggi',
            self::SEDANG => 'Sedang',
            self::RENDAH => 'Rendah',
            default => parent::getDescription($value),
        };
    }
}
