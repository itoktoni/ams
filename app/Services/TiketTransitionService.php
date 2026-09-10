<?php

namespace App\Services;

use App\Models\Tiket;
use App\Models\TiketLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TiketTransitionService
{
    public const ALLOWED = [
        'buka' => ['ditugaskan'],
        'ditugaskan' => ['progres', 'buka'], // buka = release lease
        'progres' => ['selesai', 'menunggu_part', 'buka'],
        'menunggu_part' => ['progres', 'selesai'],
        'selesai' => ['terverifikasi', 'progres'], // re-open if needed
        'terverifikasi' => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::ALLOWED[$from] ?? [], true);
    }

    /**
     * Transisi status dengan guard + tulis TiketLog append-only.
     * Return Tiket fresh atau throw.
     */
    public static function transition(Tiket $tiket, string $to, ?int $actorId = null, ?string $catatan = null, array $extra = []): Tiket
    {
        $from = $tiket->tiket_status;
        if (! self::canTransition($from, $to)) {
            throw new \InvalidArgumentException("Transisi {$from} → {$to} tidak diizinkan.");
        }

        // Guard bukti harian
        if ($from === 'ditugaskan' && $to === 'progres' && empty($extra['tiket_analisa']) && empty($tiket->tiket_analisa)) {
            throw new \InvalidArgumentException('Analisa wajib diisi saat mulai pengerjaan (progres).');
        }
        if ($to === 'selesai' && empty($extra['tiket_kesimpulan']) && empty($tiket->tiket_kesimpulan)) {
            throw new \InvalidArgumentException('Kesimpulan wajib diisi saat menyelesaikan tiket.');
        }

        return DB::transaction(function () use ($tiket, $from, $to, $actorId, $catatan, $extra) {
            $tiket->refresh();
            // cegah race jika status sudah berubah di transaksi lain
            if ($tiket->tiket_status !== $from) {
                throw new \InvalidArgumentException("Status sudah berubah menjadi {$tiket->tiket_status}.");
            }

            $payload = array_merge(['tiket_status' => $to], $extra);

            // auto timestamps per status
            if ($to === 'ditugaskan' && empty($tiket->tiket_tanggal_tugas)) {
                $payload['tiket_tanggal_tugas'] = now();
            }
            if ($to === 'progres' && empty($tiket->tiket_tanggal_mulai)) {
                $payload['tiket_tanggal_mulai'] = now();
            }
            if ($to === 'selesai') {
                $payload['tiket_tanggal_selesai'] = now();
                // hitung durasi menit
                $mulai = $tiket->tiket_tanggal_mulai ?? $tiket->tiket_tanggal_tugas ?? $tiket->tiket_tanggal_lapor;
                if ($mulai) {
                    $payload['tiket_durasi_menit'] = (int) Carbon::parse($mulai)->diffInMinutes(now());
                }
                // SLA terlambat?
                if ($tiket->tiket_jatuh_tempo && now()->gt($tiket->tiket_jatuh_tempo)) {
                    $payload['tiket_terlambat_sla'] = true;
                }
            }
            if ($to === 'terverifikasi') {
                $payload['tiket_tanggal_verifikasi'] = now();
            }
            // clear lease saat sudah progres/selesai
            if (in_array($to, ['progres', 'selesai', 'terverifikasi'])) {
                $payload['tiket_tanggal_lease_expires'] = null;
            }

            $tiket->update($payload);

            TiketLog::create([
                'tiket_log_id_tiket' => $tiket->tiket_id,
                'tiket_log_status_dari' => $from,
                'tiket_log_status_ke' => $to,
                'tiket_log_actor' => $actorId,
                'tiket_log_catatan' => $catatan ?? ($extra['tiket_analisa'] ?? $extra['tiket_kesimpulan'] ?? null),
            ]);

            return $tiket->fresh();
        });
    }

    public static function hashFoto(?string $path): ?string
    {
        if (empty($path)) return null;
        $full = storage_path('app/public/'.$path);
        if (! file_exists($full)) return null;
        return hash_file('sha256', $full);
    }
}
