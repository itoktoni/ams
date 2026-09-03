<?php

namespace App\Enums\Alert;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TipeAlertEnum extends Enum
{
    use EnumTrait;

    const SIM_STNK = 'sim_stnk';

    const LANGGANAN = 'langganan';

    const SERVICE = 'service';

    const PEMINJAMAN = 'peminjaman';

    const SLA = 'sla';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::SIM_STNK => 'SIM / STNK',
            self::LANGGANAN => 'Langganan',
            self::SERVICE => 'Service Berkala',
            self::PEMINJAMAN => 'Peminjaman',
            self::SLA => 'SLA Tiket',
            default => parent::getDescription($value),
        };
    }
}
