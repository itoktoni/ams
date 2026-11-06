<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\BukuPenyusutan;
use App\Models\JadwalService;
use App\Models\LogScanAset;
use App\Models\OtorisasiKeluar;
use App\Models\RiwayatService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function asetScanApi(Request $request, string $kode)
    {
        $kode = trim($kode);
        $aset = Aset::with(['hasKategori', 'hasLokasi', 'hasPenanggungJawab', 'hasVendor'])
            ->where('aset_kode_qr', $kode)
            ->orWhere('aset_kode', $kode)
            ->first();

        // shortcut: riwayat scan append-only; dedup kode+IP 60 detik agar burst inventory tidak meledakkan tabel
        try {
            $dupe = LogScanAset::where('log_scan_aset_kode', $kode)
                ->where('log_scan_aset_ip', $request->ip())
                ->where('created_at', '>', now()->subSeconds(60))
                ->exists();
            if (! $dupe) {
                LogScanAset::create([
                    'log_scan_aset_id_aset' => $aset?->aset_id,
                    'log_scan_aset_kode' => mb_substr($kode, 0, 100),
                    'log_scan_aset_sumber' => 'rfid',
                    'log_scan_aset_ip' => $request->ip(),
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if (! $aset) {
            return response()->json(['found' => false, 'query' => $kode], 404);
        }

        // Otorisasi keluar: status allow → hijau, selain itu merah + label.
        $now = now();
        $otorisasi = OtorisasiKeluar::where('otorisasi_keluar_id_aset', $aset->aset_id)
            ->where('otorisasi_keluar_status', 'allow')
            ->where(function ($q) use ($now) {
                $q->whereNull('otorisasi_keluar_tanggal_mulai')
                    ->orWhere('otorisasi_keluar_tanggal_mulai', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('otorisasi_keluar_tanggal_selesai')
                    ->orWhere('otorisasi_keluar_tanggal_selesai', '>=', $now);
            })
            ->orderByDesc('otorisasi_keluar_tanggal_mulai')
            ->first();

        return response()->json([
            'found' => true,
            'query' => $kode,
            'otorisasi' => $otorisasi ? [
                'allowed' => true,
                'jenis' => $otorisasi->otorisasi_keluar_jenis,
                'nomor' => $otorisasi->otorisasi_keluar_nomor,
                'mulai' => $otorisasi->otorisasi_keluar_tanggal_mulai?->toIso8601String(),
                'selesai' => $otorisasi->otorisasi_keluar_tanggal_selesai?->toIso8601String(),
                'catatan' => $otorisasi->otorisasi_keluar_catatan,
                'label' => 'DIIZINKAN KELUAR ('.$otorisasi->otorisasi_keluar_jenis.')'
                    .($otorisasi->otorisasi_keluar_tanggal_selesai ? ' s/d '.$otorisasi->otorisasi_keluar_tanggal_selesai->format('d/m/Y H:i') : ''),
            ] : [
                'allowed' => false,
                'jenis' => null,
                'nomor' => null,
                'mulai' => null,
                'selesai' => null,
                'catatan' => null,
                'label' => 'TIDAK ADA OTORISASI KELUAR YANG BERLAKU — DILARANG KELUAR',
            ],
            'aset' => [
                'nama' => $aset->aset_nama,
                'kode' => $aset->aset_kode,
                'kode_qr' => $aset->aset_kode_qr,
                'kategori' => $aset->hasKategori?->aset_kategori_nama,
                'lokasi' => $aset->hasLokasi?->aset_lokasi_nama,
                'status' => $aset->aset_status,
                'kondisi' => $aset->aset_kondisi,
                'merek' => $aset->aset_merek,
                'model' => $aset->aset_model,
                'nomor_seri' => $aset->aset_nomor_seri,
                'foto' => $aset->aset_foto ? fileUrl($aset->aset_foto) : null,
                'penanggung_jawab' => $aset->hasPenanggungJawab?->name,
            ],
            'web_url' => $aset->scan_qr_url,
        ]);
    }

    public function asetScan(Request $request, string $kode)
    {
        $aset = Aset::with(['hasKategori', 'hasLokasi', 'hasPenanggungJawab', 'hasVendor'])
            ->where('aset_kode_qr', $kode)
            ->orWhere('aset_kode', $kode)
            ->firstOrFail();

        $riwayat = RiwayatService::with(['hasTeknisi'])->where('riwayat_service_id_aset', $aset->aset_id)->orderByDesc('riwayat_service_tanggal')->limit(10)->get();
        $jadwal = JadwalService::with(['hasTemplate'])->where('jadwal_service_id_aset', $aset->aset_id)->orderBy('jadwal_service_tanggal_jatuh_tempo')->limit(5)->get();
        $tiket = \App\Models\Tiket::where('tiket_id_aset', $aset->aset_id)->orderByDesc('tiket_id')->limit(5)->get();

        return view('pages.aset.scan', compact('aset', 'riwayat', 'jadwal', 'tiket'));
    }
}
