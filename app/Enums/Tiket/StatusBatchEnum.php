<?php

namespace App\Enums\Tiket;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusBatchEnum extends Enum
{
    use EnumTrait;

    const DRAFT = 'draft';

    const DITAWARKAN = 'ditawarkan';

    const DITERIMA = 'diterima';

    const DITOLAK = 'ditolak';

    const SELESAI = 'selesai';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAFT => 'Draft',
            self::DITAWARKAN => 'Ditawarkan',
            self::DITERIMA => 'Diterima',
            self::DITOLAK => 'Ditolak',
            self::SELESAI => 'Selesai',
            default => parent::getDescription($value),
        };
    }
}
