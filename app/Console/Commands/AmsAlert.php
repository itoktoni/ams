<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class AmsAlert extends Command
{
    protected $signature = 'ams:alert';

    protected $description = 'Pindai kondisi sistem & buat alert otomatis (STNK, SLA, peminjaman, service)';

    public function handle(): int
    {
        $service = new AlertService;
        $n = $service->cekDanBuat();

        $this->info("Alert baru dibuat: {$n}");

        return self::SUCCESS;
    }
}
