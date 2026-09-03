<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\DokumenAset;
use App\Models\JadwalService;
use App\Models\LogAlert;
use App\Models\Peminjaman;
use App\Models\Tiket;

/**
 * Deteksi kondisi yang membutuhkan perhatian & pembentukan Alert + LogAlert.
 */
class AlertService
{
    /**
     * Pindai seluruh sumber & buat alert bila perlu (dedup via alert_kunci_dedup).
     *
     * @return int jumlah alert baru
     */
    public function cekDanBuat(): int
    {
        $created = 0;

        // 1. Dokumen aset (STNK/SIM/garansi) akan/expired dalam 30 hari
        foreach (DokumenAset::whereNotNull('aset_dokumen_tanggal_expired')
            ->where('aset_dokumen_tanggal_expired', '<=', now()->addDays(30))
            ->cursor() as $doc) {
            $created += $this->buat(
                'sim_stnk',
                $doc->aset_dokumen_id,
                'aset_dokumen',
                'Dokumen akan expired: '.($doc->aset_dokumen_jenis ?? 'dokumen'),
                'Dokumen '.$doc->aset_dokumen_jenis.' expired pada '.$doc->aset_dokumen_tanggal_expired.'.',
                'kritis',
                $doc->aset_dokumen_tanggal_expired
            );
        }

        // 2. Tiket melewati SLA
        foreach (Tiket::whereNotIn('tiket_status', ['selesai', 'terverifikasi'])
            ->whereNotNull('tiket_jatuh_tempo')
            ->where('tiket_jatuh_tempo', '<', now())
            ->cursor() as $t) {
            $created += $this->buat(
                'sla',
                $t->tiket_id,
                'tiket',
                'SLA terlewat: '.($t->tiket_nomor ?? '#'.$t->tiket_id),
                'Tiket '.$t->tiket_nomor.' melewati batas SLA.',
                'peringatan',
                $t->tiket_jatuh_tempo
            );
        }

        // 3. Peminjaman jatuh tempo
        foreach (Peminjaman::where('peminjaman_status', 'aktif')
            ->where('peminjaman_jatuh_tempo', '<=', now()->addDays(1))
            ->cursor() as $p) {
            $created += $this->buat(
                'peminjaman',
                $p->peminjaman_id,
                'peminjaman',
                'Pengembalian jatuh tempo: '.($p->peminjaman_nomor ?? '#'.$p->peminjaman_id),
                'Peminjaman '.$p->peminjaman_nomor.' jatuh tempo pengembalian.',
                'info',
                $p->peminjaman_jatuh_tempo
            );
        }

        // 4. Jadwal service jatuh tempo
        foreach (JadwalService::where('jadwal_service_status', 'aktif')
            ->whereNotNull('jadwal_service_tanggal_jatuh_tempo')
            ->where('jadwal_service_tanggal_jatuh_tempo', '<=', now()->addDays(7))
            ->cursor() as $j) {
            $created += $this->buat(
                'service',
                $j->jadwal_service_id,
                'jadwal_service',
                'Service jatuh tempo',
                'Jadwal service untuk aset jatuh tempo pada '.$j->jadwal_service_tanggal_jatuh_tempo.'.',
                'peringatan',
                $j->jadwal_service_tanggal_jatuh_tempo
            );
        }

        return $created;
    }

    /**
     * Kirim alert ke semua kanal & catat di log_alert.
     */
    public function kirim(Alert $alert): void
    {
        $channels = ['in_app', 'email'];

        foreach ($channels as $channel) {
            LogAlert::create([
                'log_alert_id_alert' => $alert->alert_id,
                'log_alert_kanal' => $channel,
                'log_alert_tujuan' => $alert->alert_id_pic ? (string) $alert->alert_id_pic : 'system',
                'log_alert_status' => 'terkirim',
                'log_alert_dibuka' => false,
                'log_alert_pesan' => $alert->alert_pesan,
            ]);
        }

        $alert->update(['alert_terakhir_kirim' => now()]);
    }

    /**
     * Buat alert bila belum ada alert aktif dengan kunci dedup yang sama.
     */
    protected function buat(
        string $tipe,
        int $idReferensi,
        string $tipeReferensi,
        string $judul,
        string $pesan,
        string $level,
        $jatuhTempo
    ): int {
        $kunci = $tipe.'|'.$tipeReferensi.'|'.$idReferensi;

        $sudahAda = Alert::where('alert_kunci_dedup', $kunci)
            ->where('alert_status', '!=', 'selesai')
            ->exists();

        if ($sudahAda) {
            return 0;
        }

        Alert::create([
            'alert_tipe' => $tipe,
            'alert_id_referensi' => $idReferensi,
            'alert_tipe_referensi' => $tipeReferensi,
            'alert_judul' => $judul,
            'alert_pesan' => $pesan,
            'alert_level' => $level,
            'alert_kunci_dedup' => $kunci,
            'alert_jatuh_tempo' => $jatuhTempo,
            'alert_status' => 'terbuka',
            'alert_level_eskalasi' => 0,
        ]);

        return 1;
    }
}
