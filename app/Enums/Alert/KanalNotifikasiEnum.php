<?php

namespace App\Enums\Alert;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class KanalNotifikasiEnum extends Enum
{
    use EnumTrait;

    const EMAIL = 'email';

    const WHATSAPP = 'whatsapp';

    const PUSH = 'push';

    const IN_APP = 'in_app';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::EMAIL => 'Email',
            self::WHATSAPP => 'WhatsApp',
            self::PUSH => 'Push Notification',
            self::IN_APP => 'In-App',
            default => parent::getDescription($value),
        };
    }
}
