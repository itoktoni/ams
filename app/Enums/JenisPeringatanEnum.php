<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisPeringatanEnum extends Enum
{
    use EnumTrait;

    const DOKUMEN = 'dokumen';

    const SERVIS = 'servis';

    const PEMINJAMAN = 'peminjaman';

    const SLA = 'sla';

    const STOK = 'stok';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DOKUMEN => 'Dokumen',
            self::SERVIS => 'Servis Berkala',
            self::PEMINJAMAN => 'Peminjaman',
            self::SLA => 'SLA Tiket',
            self::STOK => 'Stok Sparepart',
            default => parent::getDescription($value),
        };
    }
}
