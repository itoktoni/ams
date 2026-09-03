<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class RoleEnum extends Enum
{
    use EnumTrait;

    const USER = 'user';

    const EDITOR = 'editor';

    const ADMIN = 'admin';

    const DEVELOPER = 'developer';

    const TEKNISI = 'teknisi';

    const PENGGUNA_ASET = 'pengguna_aset';

    const SUPERVISOR = 'supervisor';

    const CUSTOMER = 'customer';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ADMIN => 'Administrator Utama',
            self::EDITOR => 'Editor',
            self::DEVELOPER => 'Developer',
            self::USER => 'Pengguna Biasa',
            self::TEKNISI => 'Teknisi',
            self::PENGGUNA_ASET => 'Pengguna Aset',
            self::SUPERVISOR => 'Supervisor',
            self::CUSTOMER => 'Customer Lelang',
            default => parent::getDescription($value),
        };
    }
}
