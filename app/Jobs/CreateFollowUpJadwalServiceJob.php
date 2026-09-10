<?php

namespace App\Jobs;

use App\Models\JadwalService;
use App\Models\RiwayatService;
use App\Models\TemplateService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateFollowUpJadwalServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $riwayatId) {}

    public function handle(): void
    {
        $riwayat = RiwayatService::find($this->riwayatId);
        if (!$riwayat || !$riwayat->riwayat_service_id_aset) return;

        $asetId = $riwayat->riwayat_service_id_aset;
        // cari template terkait via jadwal terakhir atau via kategori aset
        $lastJadwal = JadwalService::where('jadwal_service_id_aset', $asetId)->orderByDesc('jadwal_service_tanggal_mulai')->first();
        $template = null;
        if ($lastJadwal && $lastJadwal->jadwal_service_id_template) {
            $template = TemplateService::find($lastJadwal->jadwal_service_id_template);
        }
        if (!$template) {
            $aset = \App\Models\Aset::find($asetId);
            if ($aset && $aset->aset_id_kategori) {
                $template = TemplateService::where('template_service_id_kategori', $aset->aset_id_kategori)->first();
            }
        }
        if (!$template) $template = TemplateService::first();
        if (!$template) return;

        // cek sudah ada follow-up setelah tanggal riwayat?
        $exists = JadwalService::where('jadwal_service_id_aset', $asetId)
            ->where('jadwal_service_tanggal_mulai', '>', $riwayat->riwayat_service_tanggal ?? now())
            ->exists();
        if ($exists) return;

        $mulai = $riwayat->riwayat_service_tanggal ? Carbon::parse($riwayat->riwayat_service_tanggal) : now();
        // interval dari template
        $bulan = $template->template_service_interval_bulan ?? 1;
        $jatuh = $mulai->copy()->addMonths((int) $bulan);
        if (empty($bulan) || $bulan <= 0) $jatuh = $mulai->copy()->addMonth();

        JadwalService::create([
            'jadwal_service_id_aset' => $asetId,
            'jadwal_service_id_template' => $template->template_service_id,
            'jadwal_service_tanggal_mulai' => $jatuh->format('Y-m-d'),
            'jadwal_service_tanggal_jatuh_tempo' => $jatuh->copy()->addDays(7)->format('Y-m-d'),
            'jadwal_service_interval_bulan' => $template->template_service_interval_bulan,
            'jadwal_service_interval_jam' => $template->template_service_interval_jam,
            'jadwal_service_status' => 'aktif',
        ]);
    }
}
