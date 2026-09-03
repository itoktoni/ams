<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TingkatPersetujuanEnum extends Enum
{
    use EnumTrait;

    const PENYELIA = 'penyelia';

    const MANAJER = 'manajer';

    const KEUANGAN = 'keuangan';

    const DIREKSI = 'direksi';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PENYELIA => 'Penyelia',
            self::MANAJER => 'Manajer',
            self::KEUANGAN => 'Keuangan',
            self::DIREKSI => 'Direksi',
            default => parent::getDescription($value),
        };
    }
}
