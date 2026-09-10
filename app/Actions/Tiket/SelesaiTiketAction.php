<?php

namespace App\Actions\Tiket;

use App\Concerns\PayloadTrait;
use App\Models\Tiket;
use App\Services\TiketTransitionService;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class SelesaiTiketAction
{
    use AsAction, PayloadTrait;

    public function handle(Request $request, int $id): array
    {
        $tiket = Tiket::findOrFail($id);
        $user = $request->user() ?? auth()->user();

        if ($user && $user->role === 'teknisi' && (int) $tiket->tiket_id_teknisi !== (int) $user->id) {
            return $this->payload(TOAST_FAILED, 'Hanya teknisi pemilik tiket yang bisa menyelesaikan.');
        }
        if (! in_array($tiket->tiket_status, ['progres', 'menunggu_part'], true)) {
            return $this->payload(TOAST_FAILED, 'Hanya tiket Progres/Menunggu Part yang bisa diselesaikan. Status: '.$tiket->tiket_status);
        }

        $request->validate([
            'tiket_kesimpulan' => 'required|string|min:10',
            'tiket_foto_sesudah' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('tiket_foto_sesudah')) {
            $path = uploadFile($request->file('tiket_foto_sesudah'), 'tiket', ['max_size' => 4096]);
            $request->merge(['tiket_foto_sesudah' => $path]);
        } elseif ($request->boolean('remove_tiket_foto_sesudah')) {
            $request->merge(['tiket_foto_sesudah' => null, 'tiket_foto_sesudah_hash' => null]);
        }

        $extra = [
            'tiket_kesimpulan' => $request->input('tiket_kesimpulan'),
            'tiket_tanggal_kesimpulan' => now(),
        ];
        if ($request->has('tiket_foto_sesudah')) {
            $extra['tiket_foto_sesudah'] = $request->input('tiket_foto_sesudah');
            $extra['tiket_foto_sesudah_hash'] = TiketTransitionService::hashFoto($request->input('tiket_foto_sesudah'));
        }

        try {
            $t = TiketTransitionService::transition($tiket, 'selesai', $user?->id, $request->input('tiket_kesimpulan'), $extra);
            return $this->payload(TOAST_SUCCESS, $t);
        } catch (\InvalidArgumentException $e) {
            return $this->payload(TOAST_FAILED, $e->getMessage());
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }
}
