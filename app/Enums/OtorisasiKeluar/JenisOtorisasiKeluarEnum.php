<?php

namespace App\Enums\OtorisasiKeluar;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class JenisOtorisasiKeluarEnum extends Enum
{
    use EnumTrait;

    const PEMINJAMAN = 'peminjaman';
    const PERPINDAHAN = 'perpindahan';
    const PENGHAPUSAN = 'penghapusan';
    const PENJUALAN = 'penjualan';
    const LAINNYA = 'lainnya';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PEMINJAMAN => 'Peminjaman',
            self::PERPINDAHAN => 'Perpindahan',
            self::PENGHAPUSAN => 'Penghapusan',
            self::PENJUALAN => 'Penjualan',
            self::LAINNYA => 'Lainnya',
            default => parent::getDescription($value),
        };
    }
}
