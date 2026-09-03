<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisLokasiEnum extends Enum
{
    use EnumTrait;

    const KANTOR_PUSAT = 'kantor_pusat';

    const CABANG = 'cabang';

    const GUDANG = 'gudang';

    const RUANGAN = 'ruangan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::KANTOR_PUSAT => 'Kantor Pusat',
            self::CABANG => 'Cabang',
            self::GUDANG => 'Gudang',
            self::RUANGAN => 'Ruangan',
            default => parent::getDescription($value),
        };
    }
}
