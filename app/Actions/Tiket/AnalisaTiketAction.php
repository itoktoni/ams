<?php

namespace App\Actions\Tiket;

use App\Concerns\PayloadTrait;
use App\Models\Tiket;
use App\Services\TiketTransitionService;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class AnalisaTiketAction
{
    use AsAction, PayloadTrait;

    public function handle(Request $request, int $id): array
    {
        $tiket = Tiket::findOrFail($id);
        $user = $request->user() ?? auth()->user();

        // hanya teknisi pemilik atau admin/developer
        if ($user && $user->role === 'teknisi' && (int) $tiket->tiket_id_teknisi !== (int) $user->id) {
            return $this->payload(TOAST_FAILED, 'Hanya teknisi pemilik tiket yang bisa mengisi analisa.');
        }
        if (! in_array($tiket->tiket_status, ['ditugaskan', 'progres'], true)) {
            return $this->payload(TOAST_FAILED, 'Analisa hanya untuk tiket Ditugaskan/Progres. Status: '.$tiket->tiket_status);
        }

        $request->validate([
            'tiket_analisa' => 'required|string|min:10',
            'tiket_foto_sebelum' => 'nullable|string|max:255',
        ]);

        // handle upload foto sebelum jika ada file
        if ($request->hasFile('tiket_foto_sebelum')) {
            $path = uploadFile($request->file('tiket_foto_sebelum'), 'tiket', ['max_size' => 4096]);
            $request->merge(['tiket_foto_sebelum' => $path]);
        } elseif ($request->boolean('remove_tiket_foto_sebelum')) {
            $request->merge(['tiket_foto_sebelum' => null, 'tiket_foto_sebelum_hash' => null]);
        }

        $extra = [
            'tiket_analisa' => $request->input('tiket_analisa'),
            'tiket_tanggal_analisa' => now(),
            'tiket_tanggal_lease_expires' => null, // clear lease karena sudah ada progres
        ];
        if ($request->has('tiket_foto_sebelum')) {
            $extra['tiket_foto_sebelum'] = $request->input('tiket_foto_sebelum');
            $extra['tiket_foto_sebelum_hash'] = TiketTransitionService::hashFoto($request->input('tiket_foto_sebelum'));
        }

        try {
            $originalStatus = $tiket->tiket_status;
            if ($originalStatus === 'ditugaskan') {
                $t = TiketTransitionService::transition($tiket, 'progres', $user?->id, $request->input('tiket_analisa'), $extra);
            } else {
                // sudah progres → hanya update analisa tanpa ganti status, catat log update
                $tiket->update($extra);
                \App\Models\TiketLog::create([
                    'tiket_log_id_tiket' => $tiket->tiket_id,
                    'tiket_log_status_dari' => 'progres',
                    'tiket_log_status_ke' => 'progres',
                    'tiket_log_actor' => $user?->id,
                    'tiket_log_catatan' => 'Update analisa: '.substr($request->input('tiket_analisa'), 0, 200),
                ]);
                $t = $tiket->fresh();
            }
            return $this->payload(TOAST_SUCCESS, $t);
        } catch (\InvalidArgumentException $e) {
            return $this->payload(TOAST_FAILED, $e->getMessage());
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }
}
