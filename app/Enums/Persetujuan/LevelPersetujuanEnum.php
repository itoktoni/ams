<?php

namespace App\Enums\Persetujuan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class LevelPersetujuanEnum extends Enum
{
    use EnumTrait;

    const SUPERVISOR = 'supervisor';

    const MANAGER = 'manager';

    const ADMIN_ASET = 'admin_aset';

    const KEUANGAN = 'keuangan';

    const DIREKSI = 'direksi';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::SUPERVISOR => 'Supervisor',
            self::MANAGER => 'Manager',
            self::ADMIN_ASET => 'Admin Aset',
            self::KEUANGAN => 'Keuangan',
            self::DIREKSI => 'Direksi',
            default => parent::getDescription($value),
        };
    }
}
