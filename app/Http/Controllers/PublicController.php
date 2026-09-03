<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\BukuPenyusutan;
use App\Models\JadwalService;
use App\Models\RiwayatService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
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
