<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class ModeBatchEnum extends Enum
{
    use EnumTrait;

    const GEO = 'geo';

    const FIFO = 'fifo';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::GEO => 'Geo Batching',
            self::FIFO => 'FIFO Langsung',
            default => parent::getDescription($value),
        };
    }
}
