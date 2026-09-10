<?php

namespace App\Actions\Tiket;

use App\Concerns\PayloadTrait;
use App\Concerns\RulesTrait;
use App\Models\Aset;
use App\Models\LokasiAset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTiketAction
{
    use AsAction, PayloadTrait, RulesTrait;

    public function rules(): array
    {
        return $this->mergeRules($this->model);
    }

    public function handle(Request $request, $id, $model)
    {
        $this->model = $model;

        $m = $model->findOrFail($id);

        $this->handleUploads($request, ['tiket_foto_sebelum', 'tiket_foto_sesudah'], 'tiket', $m->toArray());

        // judul selalu regenerate saat update juga (agar ikut terupdate)
        $asetIdForJudul = $request->input('tiket_id_aset', $m->tiket_id_aset);
        $asetForJudul = $asetIdForJudul ? Aset::find($asetIdForJudul) : null;
        $asetNamaForJudul = $asetForJudul?->aset_nama ?? $m->hasAset?->aset_nama ?? '-';
        $lokasiIdForJudul = $request->input('tiket_id_lokasi', $m->tiket_id_lokasi) ?: ($asetForJudul?->aset_id_lokasi ?? $m->tiket_id_lokasi);
        $lokasiForJudul = $lokasiIdForJudul ? LokasiAset::find($lokasiIdForJudul) : null;
        $lokasiNamaForJudul = $lokasiForJudul?->aset_lokasi_nama ?? $m->hasLokasi?->aset_lokasi_nama ?? '-';
        $deskripsiForJudul = trim((string) ($request->input('tiket_deskripsi', $m->tiket_deskripsi) ?? ''));
        if ($deskripsiForJudul === '') $deskripsiForJudul = '-';
        elseif (mb_strlen($deskripsiForJudul) > 80) $deskripsiForJudul = mb_substr($deskripsiForJudul, 0, 80) . '…';
        $pelaporIdForJudul = $request->input('tiket_id_pelapor', $m->tiket_id_pelapor) ?: $m->tiket_id_pelapor;
        $pelaporForJudul = $pelaporIdForJudul ? \App\Models\User::find($pelaporIdForJudul) : null;
        $pelaporNamaForJudul = $pelaporForJudul?->name ?? $m->hasPelapor?->name ?? '-';
        $judulGenerated = "ASSET : {$asetNamaForJudul} DI LOKASI : {$lokasiNamaForJudul} TELAH TERJADI : {$deskripsiForJudul} DI LAPORKAN OLEH : {$pelaporNamaForJudul}";
        $request->merge(['tiket_judul' => $judulGenerated]);

        // Jika teknisi mengambil/mengisi tiket dan tempo masih kosong, isi sekarang berdasarkan urgensi
        $urgensi = $request->input('tiket_tingkat_urgensi', $m->tiket_tingkat_urgensi);
        $teknisiId = $request->input('tiket_id_teknisi', $m->tiket_id_teknisi);
        $tempo = $request->input('tiket_jatuh_tempo', $m->tiket_jatuh_tempo);
        if (empty($tempo) && !empty($teknisiId)) {
            $hours = match($urgensi) {
                'kritis' => 4,
                'tinggi' => 8,
                'sedang' => 24,
                'rendah' => 72,
                default => 24,
            };
            $request->merge(['tiket_jatuh_tempo' => Carbon::now()->addHours($hours)]);
        }

        $data = $request->validate($this->rules());

        try {
            $m->update($data);
            return $this->payload(TOAST_SUCCESS, $m->fresh() ?? $m);
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    protected function handleUploads(Request $request, array $fields, string $folder, ?array $existing = null): void
    {
        foreach ($fields as $f) {
            if ($request->hasFile($f)) {
                $request->merge([$f => uploadFile($request->file($f), $folder, ['max_size' => 4096])]);
            } elseif ($request->boolean('remove_'.$f)) {
                $request->merge([$f => null]);
            } elseif ($existing && array_key_exists($f, $existing)) {
                $request->merge([$f => $existing[$f] ?? null]);
            }
        }
    }
}
