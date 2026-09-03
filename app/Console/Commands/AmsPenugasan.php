<?php

namespace App\Console\Commands;

use App\Models\Tiket;
use App\Services\PenugasanTeknisiService;
use Illuminate\Console\Command;

class AmsPenugasan extends Command
{
    protected $signature = 'ams:penugasan {--mode=geo : geo|fifo}';

    protected $description = 'Tugaskan teknisi secara otomatis ke tiket yang belum memiliki teknisi';

    public function handle(): int
    {
        $service = new PenugasanTeknisiService;
        $mode = $this->option('mode');
        $n = 0;

        foreach (Tiket::whereNull('tiket_id_teknisi')->where('tiket_status', 'buka')->cursor() as $tiket) {
            if ($service->tugaskanOtomatis($tiket, $mode)) {
                $n++;
            }
        }

        $this->info("Tiket ditugaskan: {$n} (mode: {$mode})");

        return self::SUCCESS;
    }
}
