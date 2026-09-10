<?php

namespace App\Actions\Tiket;

use App\Concerns\PayloadTrait;
use App\Models\Tiket;
use App\Models\TiketLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ClaimTiketAction
{
    use AsAction, PayloadTrait;

    public function handle(int $tiketId, ?int $userId = null): array
    {
        $actorId = $userId ?? auth()->id();
        if (! $actorId) {
            return $this->payload(TOAST_FAILED, 'Unauthenticated.');
        }

        try {
            $tiket = DB::transaction(function () use ($tiketId, $actorId) {
                /** @var Tiket $tiket */
                $tiket = Tiket::where('tiket_id', $tiketId)->lockForUpdate()->first();
                if (! $tiket) {
                    throw new \InvalidArgumentException('Tiket tidak ditemukan.');
                }
                if ($tiket->tiket_status !== 'buka') {
                    throw new \InvalidArgumentException('Hanya tiket status Buka yang bisa diambil. Status saat ini: '.$tiket->tiket_status);
                }
                if (! empty($tiket->tiket_id_teknisi)) {
                    throw new \InvalidArgumentException('Tiket sudah diambil teknisi lain.');
                }

                // Group check: asset punya group teknisi → hanya anggota group boleh ambil
                $aset = \App\Models\Aset::find($tiket->tiket_id_aset);
                if ($aset && !empty($aset->aset_id_group)) {
                    $inGroup = DB::table('master_group_user')
                        ->where('master_group_user_id_group', $aset->aset_id_group)
                        ->where('master_group_user_id_user', $actorId)->exists();
                    if (! $inGroup) {
                        throw new \InvalidArgumentException('Anda bukan anggota group teknisi untuk aset ini, tidak bisa mengambil tiket.');
                    }
                }

                // WIP-limit: max 2 tiket aktif (ditugaskan/progres) per teknisi
                $wip = Tiket::where('tiket_id_teknisi', $actorId)
                    ->whereIn('tiket_status', ['ditugaskan', 'progres'])
                    ->lockForUpdate()
                    ->count();
                if ($wip >= 2) {
                    throw new \InvalidArgumentException('Batas WIP tercapai: Anda sudah memegang '.$wip.' tiket aktif (maks 2). Selesaikan dulu sebelum ambil baru.');
                }

                // hitung jatuh tempo jika masih kosong
                $jatuhTempo = $tiket->tiket_jatuh_tempo;
                if (empty($jatuhTempo)) {
                    $hours = match ($tiket->tiket_tingkat_urgensi) {
                        'kritis' => 4,
                        'tinggi' => 8,
                        'sedang' => 24,
                        'rendah' => 72,
                        default => 24,
                    };
                    $jatuhTempo = Carbon::now()->addHours($hours);
                }

                $tiket->update([
                    'tiket_id_teknisi' => $actorId,
                    'tiket_status' => 'ditugaskan',
                    'tiket_tanggal_tugas' => now(),
                    'tiket_tanggal_lease_expires' => now()->addHours(4),
                    'tiket_jatuh_tempo' => $jatuhTempo,
                ]);

                TiketLog::create([
                    'tiket_log_id_tiket' => $tiket->tiket_id,
                    'tiket_log_status_dari' => 'buka',
                    'tiket_log_status_ke' => 'ditugaskan',
                    'tiket_log_actor' => $actorId,
                    'tiket_log_catatan' => 'Klaim tiket oleh teknisi #'.$actorId,
                ]);

                return $tiket;
            });

            return $this->payload(TOAST_SUCCESS, $tiket->fresh());
        } catch (\InvalidArgumentException $e) {
            return $this->payload(TOAST_FAILED, $e->getMessage());
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }
}
