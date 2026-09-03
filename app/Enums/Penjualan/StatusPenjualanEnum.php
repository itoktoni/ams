<?php

namespace App\Enums\Penjualan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPenjualanEnum extends Enum
{
    use EnumTrait;

    const DRAFT = 'draft';

    const DIAJUKAN = 'diajukan';

    const TERVERIFIKASI = 'terverifikasi';

    const DISETUJUI = 'disetujui';

    const DITAWARKAN = 'ditawarkan';

    const NEGOSIASI = 'negosiasi';

    const DISEPAKATI = 'disepakati';

    const DISETUJUI_HARGA = 'disetujui_harga';

    const TERJUAL = 'terjual';

    const SERAH_TERIMA = 'serah_terima';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAFT => 'Draft',
            self::DIAJUKAN => 'Diajukan',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::DISETUJUI => 'Disetujui',
            self::DITAWARKAN => 'Ditawarkan',
            self::NEGOSIASI => 'Negosiasi',
            self::DISEPAKATI => 'Disepakati',
            self::DISETUJUI_HARGA => 'Disetujui Harga',
            self::TERJUAL => 'Terjual',
            self::SERAH_TERIMA => 'Serah Terima',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
