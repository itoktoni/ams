<?php

namespace App\Enums\Bbm;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPengajuanBbmEnum extends Enum
{
    use EnumTrait;

    const MENUNGGU = 'menunggu';

    const DISETUJUI = 'disetujui';

    const DITOLAK = 'ditolak';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::MENUNGGU => 'Menunggu',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            default => parent::getDescription($value),
        };
    }
}
