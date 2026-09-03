<?php

namespace App\Services;

use App\Models\Aset;
use App\Models\DokumenAset;
use App\Models\LogStatusAset;
use Illuminate\Support\Facades\DB;

class AsetService
{
    /**
     * Nomor kode aset otomatis: AST/{YYYYMM}/{urut 5 digit}.
     */
    public static function kodeAset(): string
    {
        $urut = ((int) Aset::max('aset_id')) + 1;
        $periode = now()->format('Ym');

        for ($i = 0; $i < 20; $i++) {
            $kode = 'AST/'.$periode.'/'.str_pad((string) ($urut + $i), 5, '0', STR_PAD_LEFT);

            if (! Aset::where('aset_kode', $kode)->exists()) {
                return $kode;
            }
        }

        return 'AST/'.$periode.'/'.unicNumber(5);
    }

    /**
     * Catat perubahan status aset ke log_status_aset.
     */
    public static function catatStatus(Aset $aset, ?string $statusLama, string $statusBaru, ?string $keterangan = null): LogStatusAset
    {
        return LogStatusAset::create([
            'log_status_aset_id_aset' => $aset->aset_id,
            'log_status_aset_status_lama' => $statusLama,
            'log_status_aset_status_baru' => $statusBaru,
            'log_status_aset_id_pengguna' => auth()->id(),
            'log_status_aset_keterangan' => $keterangan,
        ]);
    }

    /**
     * Ubah status aset sekaligus mencatat lognya.
     */
    public static function ubahStatus(Aset $aset, string $statusBaru, ?string $keterangan = null): Aset
    {
        $statusLama = $aset->aset_status;

        return DB::transaction(function () use ($aset, $statusLama, $statusBaru, $keterangan) {
            $aset->update(['aset_status' => $statusBaru]);
            self::catatStatus($aset, $statusLama, $statusBaru, $keterangan);

            return $aset->fresh() ?? $aset;
        });
    }

    /**
     * Lengkapi data registrasi aset baru: kode, QR, tanggal mulai susut, nilai buku awal.
     */
    public static function registrasi(Aset $aset): Aset
    {
        if (empty($aset->aset_kode)) {
            $aset->aset_kode = self::kodeAset();
        }

        if (empty($aset->aset_kode_qr)) {
            $aset->aset_kode_qr = $aset->aset_kode;
        }

        if (empty($aset->aset_tanggal_mulai_susut)) {
            $aset->aset_tanggal_mulai_susut = $aset->aset_tanggal_perolehan;
        }

        if ((float) ($aset->aset_nilai_buku ?? 0) === 0.0) {
            $aset->aset_nilai_buku = $aset->aset_harga_perolehan;
        }

        $aset->save();

        self::catatStatus($aset, null, $aset->aset_status ?: 'tersedia', 'Registrasi aset');

        return $aset->fresh() ?? $aset;
    }

    /**
     * Dokumen aset yang akan kadaluarsa dalam $hari hari.
     */
    public static function dokumenMenjelangKadaluarsa(int $hari = 30)
    {
        return DokumenAset::menjelangKadaluarsa($hari)->with('hasAset')->get();
    }

    /**
     * Ringkasan aset untuk dashboard.
     */
    public static function ringkasan(): array
    {
        $perKategori = Aset::query()
            ->select('aset_id_kategori', DB::raw('count(*) as total'))
            ->whereNotNull('aset_id_kategori')
            ->groupBy('aset_id_kategori')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hasKategori')
            ->get()
            ->map(fn ($row) => [
                'kategori' => $row->hasKategori?->kategori_aset_nama ?? 'Tanpa Kategori',
                'total' => (int) $row->total,
            ])
            ->values()
            ->all();

        $perLokasi = Aset::query()
            ->select('aset_id_lokasi', DB::raw('count(*) as total'))
            ->whereNotNull('aset_id_lokasi')
            ->groupBy('aset_id_lokasi')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hasLokasi')
            ->get()
            ->map(fn ($row) => [
                'lokasi' => $row->hasLokasi?->lokasi_aset_nama ?? 'Tanpa Lokasi',
                'total' => (int) $row->total,
            ])
            ->values()
            ->all();

        // Sisa manfaat dihitung dari tanggal mulai susut, jadi difilter di PHP.
        $menipis = Aset::query()
            ->whereNotNull('aset_tanggal_mulai_susut')
            ->get(['aset_id', 'aset_masa_manfaat', 'aset_tanggal_mulai_susut'])
            ->filter(fn (Aset $aset) => $aset->sisa_manfaat_bulan <= 12)
            ->count();

        return [
            'total_aset' => Aset::count(),
            'per_status' => Aset::query()
                ->select('aset_status', DB::raw('count(*) as total'))
                ->groupBy('aset_status')
                ->pluck('total', 'aset_status')
                ->map(fn ($total) => (int) $total)
                ->all(),
            'per_kategori' => $perKategori,
            'per_lokasi' => $perLokasi,
            'sisa_manfaat_menipis' => $menipis,
        ];
    }
}
