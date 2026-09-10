<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\JadwalService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorJadwalServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $telegram = app(TelegramService::class);
        $now = Carbon::now();
        $thresholds = [
            7 => '7 hari lagi',
            3 => '3 hari lagi',
            0 => 'hari ini',
        ];

        // Jadwal aktif yang jatuh tempo dalam 7 hari atau sudah lewat (overdue)
        $jadwals = JadwalService::with(['hasAset','hasTemplate'])
            ->where('jadwal_service_status', 'aktif')
            ->whereNotNull('jadwal_service_tanggal_jatuh_tempo')
            ->where('jadwal_service_tanggal_jatuh_tempo', '<=', $now->copy()->addDays(7))
            ->get();

        foreach ($jadwals as $j) {
            $jatuh = Carbon::parse($j->jadwal_service_tanggal_jatuh_tempo);
            $diffDays = $now->diffInDays($jatuh, false); // negative = overdue
            $level = $diffDays < 0 ? 'kritis' : ($diffDays <= 3 ? 'peringatan' : 'info');
            $kapan = $diffDays < 0 ? 'TERLEWAT '.abs($diffDays).' hari' : ($diffDays == 0 ? 'HARI INI' : $diffDays.' hari lagi');
            $kunci = 'service|jadwal_service|'.$j->jadwal_service_id;

            // Dedup: jangan buat alert duplikat jika sudah ada yang terbuka
            $sudahAda = Alert::where('alert_kunci_dedup', $kunci)->where('alert_status', '!=', 'selesai')->exists();
            if (!$sudahAda) {
                $asetNama = $j->hasAset?->aset_nama ?? 'Aset #'.$j->jadwal_service_id_aset;
                $templateNama = $j->hasTemplate?->template_service_nama ?? 'Service';
                Alert::create([
                    'alert_tipe' => 'service',
                    'alert_id_referensi' => $j->jadwal_service_id,
                    'alert_tipe_referensi' => 'jadwal_service',
                    'alert_judul' => 'Service jatuh tempo '.$kapan.': '.$asetNama,
                    'alert_pesan' => 'Jadwal service '.$templateNama.' untuk '.$asetNama.' jatuh tempo pada '.formatDate($j->jadwal_service_tanggal_jatuh_tempo).' ('.$kapan.').',
                    'alert_level' => $level,
                    'alert_kunci_dedup' => $kunci,
                    'alert_jatuh_tempo' => $j->jadwal_service_tanggal_jatuh_tempo,
                    'alert_status' => 'terbuka',
                    'alert_level_eskalasi' => $diffDays < 0 ? 1 : 0,
                ]);
            }

            // Jika sudah lewat, tandai status terlewat
            if ($diffDays < 0 && $j->jadwal_service_status === 'aktif') {
                $j->update(['jadwal_service_status' => 'terlewat']);
            }

            // Kirim Telegram (dedup via cache / jangan spam tiap run — cek sudah kirim hari ini)
            $cacheKey = 'telegram_service_'.$j->jadwal_service_id.'_'.$now->format('Y-m-d');
            if (!Cache::has($cacheKey)) {
                try {
                    $asetNama = $j->hasAset?->aset_nama ?? 'Aset #'.$j->jadwal_service_id_aset;
                    $msg = "🔧 <b>SERVICE JATUH TEMPO</b>\n"
                        ."<b>Aset:</b> ".e($asetNama)."\n"
                        ."<b>Jadwal:</b> #".$j->jadwal_service_id." • ".e($j->hasTemplate?->template_service_nama ?? 'Service')."\n"
                        ."<b>Jatuh tempo:</b> ".formatDate($j->jadwal_service_tanggal_jatuh_tempo, true)." (".$kapan.")\n"
                        ."<b>Status:</b> ".e($j->jadwal_service_status)."\n"
                        ."<a href=\"".url('/jadwal-service/update/'.$j->jadwal_service_id)."\">🔗 Buka Jadwal</a>";
                    $telegram->sendToGroup($msg);
                    Cache::put($cacheKey, true, 86400);
                } catch (\Throwable $e) {
                    Log::warning('MonitorJadwalServiceJob telegram gagal: '.$e->getMessage());
                }
            }
        }
    }
}
