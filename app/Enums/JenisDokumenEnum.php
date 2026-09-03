<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisDokumenEnum extends Enum
{
    use EnumTrait;

    const BPKB = 'bpkb';

    const STNK = 'stnk';

    const SIM = 'sim';

    const INVOICE = 'invoice';

    const GARANSI = 'garansi';

    const ASURANSI = 'asuransi';

    const SERTIFIKAT = 'sertifikat';

    const KONTRAK = 'kontrak';

    const LAINNYA = 'lainnya';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::BPKB => 'BPKB',
            self::STNK => 'STNK',
            self::SIM => 'SIM',
            self::INVOICE => 'Invoice',
            self::GARANSI => 'Garansi',
            self::ASURANSI => 'Asuransi',
            self::SERTIFIKAT => 'Sertifikat',
            self::KONTRAK => 'Kontrak',
            self::LAINNYA => 'Lainnya',
            default => parent::getDescription($value),
        };
    }
}
