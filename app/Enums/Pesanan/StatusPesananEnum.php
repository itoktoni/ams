<?php

namespace App\Enums\Pesanan;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusPesananEnum extends Enum
{
    use EnumTrait;

    const DRAFT = 'draft';

    const DISETUJUI = 'disetujui';

    const DIKIRIM = 'dikirim';

    const SEBAGIAN = 'sebagian';

    const DITERIMA = 'diterima';

    const DITUTUP = 'ditutup';

    const DIBATALKAN = 'dibatalkan';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::DRAFT => 'Draft',
            self::DISETUJUI => 'Disetujui',
            self::DIKIRIM => 'Dikirim',
            self::SEBAGIAN => 'Partial Received',
            self::DITERIMA => 'Diterima',
            self::DITUTUP => 'Ditutup',
            self::DIBATALKAN => 'Dibatalkan',
            default => parent::getDescription($value),
        };
    }
}
