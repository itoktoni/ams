<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class SaluranNotifikasiEnum extends Enum
{
    use EnumTrait;

    const APLIKASI = 'aplikasi';

    const EMAIL = 'email';

    const WHATSAPP = 'whatsapp';

    const PUSH = 'push';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::APLIKASI => 'In-App',
            self::EMAIL => 'Email',
            self::WHATSAPP => 'WhatsApp',
            self::PUSH => 'Push Notification',
            default => parent::getDescription($value),
        };
    }
}
