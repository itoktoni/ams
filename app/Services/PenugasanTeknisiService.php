<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tiket;

/**
 * Penugasan teknisi ke tiket secara otomatis — sekarang dari users role=teknisi (1 table).
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
    public function tugaskanOtomatis(Tiket $tiket, string $mode = 'geo'): ?User
    {
        // Simplified: teknisi fields (status/keahlian/geo) sudah dihapus — pilih teknisi dengan load paling ringan (WIP paling sedikit)
        $kandidats = User::where('role', 'teknisi')->get();
        if ($kandidats->isEmpty()) return null;

        $terbaik = null;
        $minWip = PHP_INT_MAX;
        foreach ($kandidats as $tek) {
            $wip = Tiket::where('tiket_id_teknisi', $tek->id)->whereIn('tiket_status', ['ditugaskan','progres'])->count();
            if ($wip < $minWip) {
                $minWip = $wip;
                $terbaik = $tek;
            }
        }
        if (!$terbaik) $terbaik = $kandidats->first();

        if ($terbaik) {
            $tiket->update([
                'tiket_id_teknisi' => $terbaik->id,
                'tiket_status' => 'ditugaskan',
                'tiket_tanggal_tugas' => now(),
            ]);
        }
        return $terbaik;
    }

    protected function skorKeahlian(Tiket $tiket, User $teknisi): int { return 0; }

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
