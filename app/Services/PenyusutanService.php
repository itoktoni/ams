<?php

namespace App\Services;

use App\Models\Aset;
use App\Models\BukuPenyusutan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Penyusutan aset + buku penyusutan dengan hash-chain (audit trail).
 *
 * Metode yang didukung: garis_lurus (straight-line). Metode lain
 * (saldo_menurun / unit_produksi) di-fallback ke garis lurus agar ledger tetap konsisten.
 */
class PenyusutanService
{
    /**
     * Generate entri buku penyusutan bulanan sampai periode tertentu.
     *
     * @return int jumlah entri baru yang dibuat
     */
    public function jalankan(Aset $aset, ?string $sampaiPeriode = null): int
    {
        if (empty($aset->aset_tanggal_mulai_susut) || (int) $aset->aset_masa_manfaat <= 0) {
            return 0;
        }

        $mulai = Carbon::parse($aset->aset_tanggal_mulai_susut)->startOfMonth();
        $sampai = $sampaiPeriode
            ? Carbon::createFromFormat('Y-m', $sampaiPeriode)->endOfMonth()
            : now()->startOfMonth();

        $harga = (float) $aset->aset_harga_perolehan;
        $nilaiSisa = (float) $aset->aset_nilai_sisa;
        $bulan = (int) $aset->aset_masa_manfaat;
        $penyusutanPerBulan = $bulan > 0 ? ($harga - $nilaiSisa) / $bulan : 0;

        $prev = BukuPenyusutan::where('buku_penyusutan_id_aset', $aset->aset_id)
            ->orderByDesc('buku_penyusutan_periode')
            ->first();

        $hashSebelum = $prev?->buku_penyusutan_hash ?? '0';
        $akumulasi = (float) ($prev?->buku_penyusutan_akumulasi ?? 0);

        $maksAkumulasi = $harga - $nilaiSisa;
        $count = 0;
        $periode = $mulai->copy();

        while ($periode->lte($sampai)) {
            $kode = $periode->format('Y-m');

            $sudahAda = BukuPenyusutan::where('buku_penyusutan_id_aset', $aset->aset_id)
                ->where('buku_penyusutan_periode', $kode)
                ->exists();

            if ($sudahAda) {
                $periode->addMonth();

                continue;
            }

            $akumulasi += $penyusutanPerBulan;
            if ($akumulasi > $maksAkumulasi) {
                $akumulasi = $maksAkumulasi;
            }
            $nilaiBuku = $harga - $akumulasi;

            $payload = $this->hashPayload($kode, $penyusutanPerBulan, $akumulasi, $nilaiBuku);
            $hash = hash('sha256', $hashSebelum.'|'.$payload);

            BukuPenyusutan::create([
                'buku_penyusutan_id_aset' => $aset->aset_id,
                'buku_penyusutan_periode' => $kode,
                'buku_penyusutan_tanggal' => $periode->copy()->endOfMonth(),
                'buku_penyusutan_nilai' => $penyusutanPerBulan,
                'buku_penyusutan_akumulasi' => $akumulasi,
                'buku_penyusutan_nilai_buku' => $nilaiBuku,
                'buku_penyusutan_hash' => $hash,
                'buku_penyusutan_hash_sebelum' => $hashSebelum,
                'buku_penyusutan_dibuat_oleh' => Auth::id(),
            ]);

            $hashSebelum = $hash;
            $count++;
            $periode->addMonth();
        }

        return $count;
    }

    /**
     * Verifikasi integritas hash-chain untuk sebuah aset.
     *
     * @return bool true bila seluruh rantai hash valid
     */
    public function verifikasi(Aset $aset): bool
    {
        $rows = BukuPenyusutan::where('buku_penyusutan_id_aset', $aset->aset_id)
            ->orderBy('buku_penyusutan_periode')
            ->get();

        $hashSebelum = '0';
        foreach ($rows as $row) {
            $payload = $this->hashPayload(
                $row->buku_penyusutan_periode,
                (float) ($row->buku_penyusutan_nilai ?? $row->buku_penyusutan_debet ?? 0),
                (float) $row->buku_penyusutan_akumulasi,
                (float) $row->buku_penyusutan_nilai_buku
            );
            $expected = hash('sha256', $hashSebelum.'|'.$payload);

            if ($row->buku_penyusutan_hash_sebelum !== $hashSebelum || $row->buku_penyusutan_hash !== $expected) {
                return false;
            }

            $hashSebelum = $row->buku_penyusutan_hash;
        }

        return true;
    }

    /**
     * Build the canonical hash payload (deterministic formatting).
     */
    private function hashPayload(string $kode, float $nilai, float $akumulasi, float $nilaiBuku): string
    {
        return implode('|', [
            $kode,
            number_format($nilai, 2, '.', ''),
            number_format($akumulasi, 2, '.', ''),
            number_format($nilaiBuku, 2, '.', ''),
        ]);
    }
}
