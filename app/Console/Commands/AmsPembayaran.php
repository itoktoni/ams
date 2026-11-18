<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AmsPembayaran extends Command
{
    protected $signature = 'ams:pembayaran';

    protected $description = 'Kirim notifikasi WA (Fonnte) untuk pembayaran yang jatuh tempo hari ini';

    public function handle(): int
    {
        $fonnte = app(FonnteService::class);
        $today = Carbon::now()->format('Y-m-d');

        // Tandai terlambat: lewat jatuh tempo & belum bayar
        Pembayaran::where('pembayaran_status', '!=', 'sudah_bayar')
            ->whereNotNull('pembayaran_tanggal_jatuh_tempo')
            ->where('pembayaran_tanggal_jatuh_tempo', '<', $today)
            ->update(['pembayaran_status' => 'terlambat']);

        // Informasi: jatuh tempo tepat hari ini & belum bayar
        $items = Pembayaran::whereIn('pembayaran_status', ['belum_bayar', 'terlambat'])
            ->where('pembayaran_tanggal_jatuh_tempo', $today)
            ->orderBy('pembayaran_jenis')
            ->get();

        $sent = 0;
        foreach ($items as $bayar) {
            $cacheKey = 'fonnte_pembayaran_'.$bayar->pembayaran_id.'_'.$today;
            if (Cache::has($cacheKey)) continue;

            try {
                $total = (float) $bayar->pembayaran_nominal + (float) $bayar->pembayaran_denda;
                $wa = "💰 *PEMBAYARAN JATUH TEMPO HARI INI*\n"
                    ."Jenis: ".\App\Enums\JenisPembayaranEnum::getDescription($bayar->pembayaran_jenis)."\n"
                    ."Nama: ".$bayar->pembayaran_nama."\n"
                    .($bayar->pembayaran_nomor_pelanggan ? "No. Pelanggan: ".$bayar->pembayaran_nomor_pelanggan."\n" : '')
                    ."Nominal: ".formatRupiah($total)."\n"
                    ."Jatuh tempo: ".formatDate($bayar->pembayaran_tanggal_jatuh_tempo)."\n"
                    ."Buka: ".url('/pembayaran/update/'.$bayar->pembayaran_id);
                if ($fonnte->sendToGroup($wa)) $sent++;
                Cache::put($cacheKey, true, 86400);
            } catch (\Throwable $e) {
                Log::warning('AmsPembayaran gagal #'.$bayar->pembayaran_id.': '.$e->getMessage());
            }
        }

        $this->info("Jatuh tempo hari ini: {$items->count()}, WA terkirim: {$sent}");

        return self::SUCCESS;
    }
}
