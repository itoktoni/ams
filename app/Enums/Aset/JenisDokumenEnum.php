<?php

namespace App\Enums\Aset;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisDokumenEnum extends Enum
{
    use EnumTrait;

    const BPKB = 'bpkb';

    const STNK = 'stnk';

    const SIM = 'sim';

    const FAKTUR = 'faktur';

    const GARANSI = 'garansi';

    const SERTIFIKAT = 'sertifikat';

    const LAINNYA = 'lainnya';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BPKB => 'BPKB',
            self::STNK => 'STNK',
            self::SIM => 'SIM',
            self::FAKTUR => 'Faktur',
            self::GARANSI => 'Garansi',
            self::SERTIFIKAT => 'Sertifikat',
            self::LAINNYA => 'Lainnya',
            default => parent::getDescription($value),
        };
    }
}
