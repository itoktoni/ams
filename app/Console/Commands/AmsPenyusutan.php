<?php

namespace App\Console\Commands;

use App\Models\Aset;
use App\Services\PenyusutanService;
use Illuminate\Console\Command;

class AmsPenyusutan extends Command
{
    protected $signature = 'ams:penyusutan {--aset= : ID aset spesifik} {--sampai= : periode Y-m terakhir}';

    protected $description = 'Jalankan penyusutan bulanan & isi buku penyusutan (hash-chain)';

    public function handle(): int
    {
        $service = new PenyusutanService;
        $sampai = $this->option('sampai');

        if ($id = $this->option('aset')) {
            $aset = Aset::findOrFail($id);
            $n = $service->jalankan($aset, $sampai);
            $this->info("Penyusutan aset #{$id}: {$n} entri dibuat.");

            return self::SUCCESS;
        }

        $total = 0;
        $counter = 0;
        foreach (Aset::whereNotNull('aset_tanggal_mulai_susut')->cursor() as $aset) {
            $total += $service->jalankan($aset, $sampai);
            $counter++;
        }

        $this->info("Diproses {$counter} aset, total {$total} entri penyusutan dibuat.");

        return self::SUCCESS;
    }
}
