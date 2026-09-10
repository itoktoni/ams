<?php

namespace App\Console\Commands;

use App\Models\Tiket;
use App\Models\TiketLog;
use Illuminate\Console\Command;

class ReleaseTiketLease extends Command
{
    protected $signature = 'tiket:release-expired-leases';
    protected $description = 'Lepas tiket yang di-claim tapi tidak ada progres/analisa dalam 4 jam (lease expired)';

    public function handle(): int
    {
        $count = 0;
        foreach (Tiket::where('tiket_status', 'ditugaskan')->whereNotNull('tiket_tanggal_lease_expires')->where('tiket_tanggal_lease_expires', '<', now())->cursor() as $tiket) {
            // jika sudah ada analisa, extend jangan release
            if (! empty($tiket->tiket_analisa)) continue;
            $actor = $tiket->tiket_id_teknisi;
            $tiket->update([
                'tiket_id_teknisi' => null,
                'tiket_status' => 'buka',
                'tiket_tanggal_lease_expires' => null,
                'tiket_tanggal_tugas' => null,
            ]);
            TiketLog::create([
                'tiket_log_id_tiket' => $tiket->tiket_id,
                'tiket_log_status_dari' => 'ditugaskan',
                'tiket_log_status_ke' => 'buka',
                'tiket_log_actor' => $actor,
                'tiket_log_catatan' => 'Auto-release: lease 4 jam habis tanpa analisa',
            ]);
            $count++;
        }
        $this->info("Released {$count} expired leases.");
        return 0;
    }
}
