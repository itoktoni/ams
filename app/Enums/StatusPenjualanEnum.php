<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPenjualanEnum extends Enum
{
    use EnumTrait;

    const DRAF = 'draf';

    const DIMINTA = 'diminta';

    const TERVERIFIKASI = 'terverifikasi';

    const DISETUJUI = 'disetujui';

    const DITAWARKAN = 'ditawarkan';

    const NEGOSIASI = 'negosiasi';

    const DISEPAKATI = 'disepakati';

    const HARGA_DISETUJUI = 'harga_disetujui';

    const TERJUAL = 'terjual';

    const SERAH_TERIMA = 'serah_terima';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAF => 'Draf',
            self::DIMINTA => 'Diminta',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::DISETUJUI => 'Disetujui',
            self::DITAWARKAN => 'Ditawarkan',
            self::NEGOSIASI => 'Negosiasi',
            self::DISEPAKATI => 'Disepakati',
            self::HARGA_DISETUJUI => 'Harga Disetujui',
            self::TERJUAL => 'Terjual',
            self::SERAH_TERIMA => 'Serah Terima',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
