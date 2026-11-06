<?php

namespace App\Enums\OtorisasiKeluar;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusOtorisasiKeluarEnum extends Enum
{
    use EnumTrait;

    const PENGAJUAN = 'diajukan';
    const ALLOW = 'allow';
    const DENY = 'deny';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PENGAJUAN => 'Diajukan',
            self::ALLOW => 'Allow (Diizinkan Keluar)',
            self::DENY => 'Deny (Ditolak)',
            default => parent::getDescription($value),
        };
    }
}
