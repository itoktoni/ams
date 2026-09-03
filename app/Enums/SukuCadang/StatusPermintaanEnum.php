<?php

namespace App\Enums\SukuCadang;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPermintaanEnum extends Enum
{
    use EnumTrait;

    const DRAFT = 'draft';

    const MENUNGGU = 'menunggu';

    const DISETUJUI = 'disetujui';

    const DITOLAK = 'ditolak';

    const SEBAGIAN = 'sebagian';

    const SELESAI = 'selesai';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAFT => 'Draft',
            self::MENUNGGU => 'Menunggu',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::SEBAGIAN => 'Sebagian',
            self::SELESAI => 'Selesai',
            default => parent::getDescription($value),
        };
    }
}
