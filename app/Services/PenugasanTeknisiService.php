<?php

namespace App\Services;

use App\Models\Teknisi;
use App\Models\Tiket;

/**
 * Penugasan teknisi ke tiket secara otomatis.
 *
 * Mode:
 *  - geo  : pilih teknisi tersedia terdekat (haversine) + load balancing.
 *  - fifo : pilih teknisi tersedia dengan total tiket terendah (antrian).
 */
class PenugasanTeknisiService
{
    /**
     * Tugaskan teknisi terbaik ke tiket yang belum punya teknisi.
     */
    public function tugaskanOtomatis(Tiket $tiket, string $mode = 'geo'): ?Teknisi
    {
        $kandidat = Teknisi::where('teknisi_status', 'tersedia')->get();

        if ($kandidat->isEmpty()) {
            return null;
        }

        $terbaik = null;
        $skorTerbaik = -INF;

        foreach ($kandidat as $teknisi) {
            $skor = 0;

            // Load balancing: teknisi dengan tiket lebih sedikit lebih diprioritaskan
            $skor -= (int) $teknisi->teknisi_total_tiket;

            // Keahlian: cocok dengan kategori aset mendapat bonus
            $skor += $this->skorKeahlian($tiket, $teknisi);

            // Jarak (mode geo)
            if ($mode === 'geo' && $tiket->tiket_latitude && $teknisi->teknisi_latitude) {
                $jarak = $this->jarak(
                    (float) $tiket->tiket_latitude,
                    (float) $tiket->tiket_longitude,
                    (float) $teknisi->teknisi_latitude,
                    (float) $teknisi->teknisi_longitude
                );
                $skor -= $jarak; // semakin dekat semakin tinggi skor
            }

            if ($skor > $skorTerbaik) {
                $skorTerbaik = $skor;
                $terbaik = $teknisi;
            }
        }

        if ($terbaik) {
            $tiket->update([
                'tiket_id_teknisi' => $terbaik->teknisi_id,
                'tiket_status' => 'ditugaskan',
                'tiket_tanggal_tugas' => now(),
            ]);
            $terbaik->increment('teknisi_total_tiket');
        }

        return $terbaik;
    }

    protected function skorKeahlian(Tiket $tiket, Teknisi $teknisi): int
    {
        if (empty($teknisi->teknisi_keahlian) || ! is_array($teknisi->teknisi_keahlian)) {
            return 0;
        }

        $aset = $tiket->hasAset ?? null;
        if (! $aset || empty($aset->aset_id_kategori)) {
            return 0;
        }

        // Keahlian diasumsikan berbasis kata kunci umum; beri bonus jika ada.
        return in_array('it', $teknisi->teknisi_keahlian, true) ? 5 : 0;
    }

    /**
     * Haversine distance in kilometers.
     */
    protected function jarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
