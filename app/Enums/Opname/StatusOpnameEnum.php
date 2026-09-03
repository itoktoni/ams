<?php

namespace App\Enums\Opname;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusOpnameEnum extends Enum
{
    use EnumTrait;

    const DRAFT = 'draft';

    const PROSES = 'proses';

    const SELESAI = 'selesai';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAFT => 'Draft',
            self::PROSES => 'Proses',
            self::SELESAI => 'Selesai',
            default => parent::getDescription($value),
        };
    }
}
