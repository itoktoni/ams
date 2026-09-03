<?php

namespace App\Enums\Penghapusan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPenghapusanEnum extends Enum
{
    use EnumTrait;

    const DRAFT = 'draft';

    const DIAJUKAN = 'diajukan';

    const SETUJU_MANAGER = 'setuju_manager';

    const SETUJU_KEUANGAN = 'setuju_keuangan';

    const SETUJU_DIREKSI = 'setuju_direksi';

    const KARANTINA = 'karantina';

    const DIBUANG = 'dibuang';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAFT => 'Draft',
            self::DIAJUKAN => 'Diajukan',
            self::SETUJU_MANAGER => 'Setuju Manager',
            self::SETUJU_KEUANGAN => 'Setuju Keuangan',
            self::SETUJU_DIREKSI => 'Setuju Direksi',
            self::KARANTINA => 'Karantina',
            self::DIBUANG => 'Dibuang',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
