<?php

namespace App\Actions\Tiket;

use App\Concerns\PayloadTrait;
use App\Concerns\RulesTrait;
use App\Models\Alert;
use App\Models\Aset;
use App\Models\LokasiAset;
use App\Models\Tiket;
use App\Services\PenugasanTeknisiService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTiketAction
{
    use AsAction, PayloadTrait, RulesTrait;

    public function rules(): array
    {
        return $this->mergeRules($this->model);
    }

    public function handle(Request $request, $model)
    {
        $this->model = $model;

        $this->handleUploads($request, ['tiket_foto_sebelum', 'tiket_foto_sesudah'], 'tiket');

        $user = Auth::user() ?? $request->user();

        // paksa pelapor = user login (anti spoof)
        $request->merge(['tiket_id_pelapor' => $user?->id]);

        // validasi asset milik pengguna (jika pengguna_aset)
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $asetId = $request->input('tiket_id_aset');
            $allowed = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (! in_array((int)$asetId, $allowed, true) && ! in_array($asetId, $allowed, true)) {
                return $this->payload(TOAST_FAILED, 'Anda hanya bisa buat tiket untuk aset yang di-assign kepada Anda.');
            }
            $aset = Aset::find($asetId);
            if ($aset && $aset->aset_id_lokasi) {
                $request->merge(['tiket_id_lokasi' => $aset->aset_id_lokasi]);
            }
        }

        // fallback lokasi manual (aset tanpa lokasi)
        if (! $request->input('tiket_id_lokasi') && $request->filled('tiket_id_lokasi_manual')) {
            $request->merge(['tiket_id_lokasi' => $request->input('tiket_id_lokasi_manual')]);
        }
        // fallback otomatis dari aset (non-pengguna yang tidak memilih lokasi)
        if (! $request->input('tiket_id_lokasi') && $request->filled('tiket_id_aset')) {
            $aset = Aset::find($request->input('tiket_id_aset'));
            if ($aset && $aset->aset_id_lokasi) {
                $request->merge(['tiket_id_lokasi' => $aset->aset_id_lokasi]);
            }
        }

        // set default jika tidak diisi form: urgensi 'sedang', tanggal lapor now, nomor auto, status buka
        // jatuh_tempo, biaya, rating, batch sengaja dibiarkan kosong di create — sesuai permintaan hide, tempo diisi saat teknisi mengambil tiket
        if (! $request->input('tiket_tingkat_urgensi')) {
            $request->merge(['tiket_tingkat_urgensi' => 'sedang']);
        }
        if (! $request->input('tiket_tanggal_lapor')) {
            $request->merge(['tiket_tanggal_lapor' => now()]);
        }
        if (! $request->has('tiket_jatuh_tempo')) {
            $request->merge(['tiket_jatuh_tempo' => null]);
        }
        if (! $request->input('tiket_status')) {
            $request->merge(['tiket_status' => 'buka']);
        }
        if (! $request->input('tiket_nomor')) {
            $request->merge(['tiket_nomor' => 'T-'.now()->format('YmdHis').'-'.rand(100,999)]);
        }
        if (! $request->has('tiket_biaya')) $request->merge(['tiket_biaya' => 0]);
        if (! $request->has('tiket_rating')) $request->merge(['tiket_rating' => null]);
        if (! $request->has('tiket_id_batch')) $request->merge(['tiket_id_batch' => null]);

        // judul pattern: ASSET : [nama asset] DI LOKASI : [nama lokasi] TELAH TERJADI : [deskripsi] DI LAPORKAN OLEH : [pelapor]
        // selalu generate ulang saat create (judul di-hide, baru terbentuk saat simpan)
        $asetIdForJudul = $request->input('tiket_id_aset');
        $asetForJudul = $asetIdForJudul ? Aset::find($asetIdForJudul) : null;
        $asetNamaForJudul = $asetForJudul?->aset_nama ?? '-';
        $lokasiIdForJudul = $request->input('tiket_id_lokasi') ?: ($asetForJudul?->aset_id_lokasi ?? null);
        $lokasiForJudul = $lokasiIdForJudul ? LokasiAset::find($lokasiIdForJudul) : null;
        $lokasiNamaForJudul = $lokasiForJudul?->aset_lokasi_nama ?? '-';
        $deskripsiForJudul = trim((string) $request->input('tiket_deskripsi'));
        if ($deskripsiForJudul === '') $deskripsiForJudul = '-';
        elseif (mb_strlen($deskripsiForJudul) > 80) $deskripsiForJudul = mb_substr($deskripsiForJudul, 0, 80) . '…';
        $pelaporIdForJudul = $request->input('tiket_id_pelapor') ?: $user?->id;
        $pelaporForJudul = $pelaporIdForJudul ? \App\Models\User::find($pelaporIdForJudul) : $user;
        $pelaporNamaForJudul = $pelaporForJudul?->name ?? $user?->name ?? '-';
        $judulGenerated = "ASSET : {$asetNamaForJudul} DI LOKASI : {$lokasiNamaForJudul} TELAH TERJADI : {$deskripsiForJudul} DI LAPORKAN OLEH : {$pelaporNamaForJudul}";
        $request->merge(['tiket_judul' => $judulGenerated]);

        $data = $request->validate($this->rules());

        try {
            /** @var Tiket $tiket */
            $tiket = $model->create($data);
        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }

        // notifikasi ke teknisi — prioritas: group aset > auto-assign > kategori
        try {
            $svc = app(TelegramService::class);
            $asetForGroup = Aset::find($tiket->tiket_id_aset);
            $groupNotified = false;
            if ($asetForGroup && !empty($asetForGroup->aset_id_group)) {
                $sentGroup = $svc->kirimKeMasterGroup($tiket, (int) $asetForGroup->aset_id_group);
                $groupNotified = $sentGroup > 0;
            }
            if (! $groupNotified) {
                $assigned = app(PenugasanTeknisiService::class)->tugaskanOtomatis($tiket, 'geo');
                $tiket->refresh();
                if ($tiket->tiket_id_teknisi) {
                    $tek = \App\Models\User::find($tiket->tiket_id_teknisi);
                    if ($tek && $tek->telegram_id) $svc->kirimKeTeknisi($tek, $tiket);
                    else $svc->kirimNotifikasiTiketBaru($tiket);
                } else {
                    $sent = $svc->kirimKeKategori($tiket);
                    if ($sent === 0) $svc->kirimKeSemuaTeknisi($tiket);
                }
            }

            $judul = 'Tiket baru: '.($tiket->tiket_nomor ?? '#'.$tiket->tiket_id).' — '.($tiket->tiket_judul ?? '');
            $pesan = 'Aset '.($tiket->hasAset?->aset_nama ?? '').' ('.($tiket->hasAset?->aset_kode ?? '').') dilaporkan rusak: '.($tiket->tiket_deskripsi ?? $tiket->tiket_judul ?? '').' (oleh '.($user->name ?? '').')';
            $kunci = 'tiket-baru|tiket|'.$tiket->tiket_id;
            if (! Alert::where('alert_kunci_dedup', $kunci)->where('alert_status','!=','selesai')->exists()) {
                Alert::create([
                    'alert_tipe' => 'tiket',
                    'alert_id_referensi' => $tiket->tiket_id,
                    'alert_tipe_referensi' => 'tiket',
                    'alert_judul' => $judul,
                    'alert_pesan' => $pesan,
                    'alert_level' => match($tiket->tiket_tingkat_urgensi){'kritis'=>'kritis','tinggi'=>'peringatan',default=>'info'},
                    'alert_kunci_dedup' => $kunci,
                    'alert_jatuh_tempo' => $tiket->tiket_jatuh_tempo,
                    'alert_status' => 'terbuka',
                    'alert_level_eskalasi' => 0,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Tiket CreateTiketAction notify gagal: '.$e->getMessage());
        }

        return $this->payload(TOAST_SUCCESS, $tiket);
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
