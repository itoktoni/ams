<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPenghapusanEnum extends Enum
{
    use EnumTrait;

    const DRAF = 'draf';

    const DIMINTA = 'diminta';

    const DISETUJUI_MANAJER = 'disetujui_manajer';

    const DISETUJUI_KEUANGAN = 'disetujui_keuangan';

    const DISETUJUI_DIREKSI = 'disetujui_direksi';

    const KARANTINA = 'karantina';

    const DIHAPUS = 'dihapus';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAF => 'Draf',
            self::DIMINTA => 'Diminta',
            self::DISETUJUI_MANAJER => 'Disetujui Manajer',
            self::DISETUJUI_KEUANGAN => 'Disetujui Keuangan',
            self::DISETUJUI_DIREKSI => 'Disetujui Direksi',
            self::KARANTINA => 'Karantina',
            self::DIHAPUS => 'Dihapus',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
