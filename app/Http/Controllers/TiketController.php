<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Alert;
use App\Models\Aset;
use App\Models\Tiket;
use App\Services\PenugasanTeknisiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TiketController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
        getData as traitGetData;
        share as traitShare;
    }

    public function __construct(Tiket $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $user = auth()->user();
        $asetOptions = Aset::getOptions();
        // pengguna hanya lihat aset miliknya
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $myAsetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (!empty($myAsetIds)) {
                $asetOptions = Aset::whereIn('aset_id', $myAsetIds)->pluck('aset_nama', 'aset_id')->toArray();
                // fallback to getOptions format if empty
                if (empty($asetOptions)) $asetOptions = Aset::whereIn('aset_id', $myAsetIds)->get()->mapWithKeys(fn($a)=>[$a->aset_id=>$a->aset_nama.' — '.$a->aset_kode])->toArray();
            } else {
                $asetOptions = [];
            }
        }

        return $this->traitShare(array_merge([
            'asetOptions' => $asetOptions,
        ], $data));
    }

    protected function getData()
    {
        $query = $this->traitGetData();
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            // pengguna hanya lihat tiket yang dia laporkan atau untuk aset miliknya
            $myAsetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            $query->where(function($q) use ($user, $myAsetIds) {
                $q->where('tiket_id_pelapor', $user->id);
                if (!empty($myAsetIds)) $q->orWhereIn('tiket_id_aset', $myAsetIds);
            });
        }
        // teknisi: hanya tiket yang di-assign ke dia + yang belum di-assign (buka) biar bisa pick
        if ($user && $user->role === 'teknisi') {
            $teknisi = \App\Models\Teknisi::where('teknisi_telepon', $user->phone)->orWhere('teknisi_nama', $user->name)->first();
            // fallback: cari teknisi by user id if linked
            if (! $teknisi) $teknisi = \App\Models\Teknisi::where('teknisi_id_user', $user->id)->first();
            if ($teknisi) {
                $query->where(function($q) use ($teknisi) {
                    $q->where('tiket_id_teknisi', $teknisi->teknisi_id)
                      ->orWhereNull('tiket_id_teknisi');
                });
            }
        }
        return $query;
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['tiket_foto_sebelum', 'tiket_foto_sesudah'], 'tiket');

        $user = auth()->user();
        // paksa pelapor = user login (anti spoof)
        $request->merge(['tiket_id_pelapor' => $user?->id]);

        // validasi asset milik pengguna (jika pengguna_aset)
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $asetId = $request->input('tiket_id_aset');
            $allowed = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (! in_array((int)$asetId, $allowed, true) && ! in_array($asetId, $allowed, true)) {
                return $this->response(['status'=>false,'message'=>'Anda hanya bisa buat tiket untuk aset yang di-assign kepada Anda.','data'=>null]);
            }
            // Lokasi mengikuti aset (pengguna tidak pilih manual)
            $aset = Aset::find($asetId);
            if ($aset && $aset->aset_id_lokasi) {
                $request->merge(['tiket_id_lokasi' => $aset->aset_id_lokasi]);
            }
        }

        // set default jika tidak diisi form: tanggal lapor now, jatuh tempo dari urgensi, nomor auto
        if (! $request->input('tiket_tanggal_lapor')) {
            $request->merge(['tiket_tanggal_lapor' => now()]);
        }
        if (! $request->input('tiket_jatuh_tempo') && $request->input('tiket_tingkat_urgensi')) {
            $hours = match($request->input('tiket_tingkat_urgensi')) {
                'kritis' => 4,
                'tinggi' => 8,
                'sedang' => 24,
                'rendah' => 72,
                default => 24,
            };
            $request->merge(['tiket_jatuh_tempo' => Carbon::now()->addHours($hours)]);
        }
        if (! $request->input('tiket_status')) {
            $request->merge(['tiket_status' => 'buka']);
        }
        if (! $request->input('tiket_nomor')) {
            $request->merge(['tiket_nomor' => 'T-'.now()->format('YmdHis').'-'.rand(100,999)]);
        }

        $response = CreateAction::run($request, $this->model);

        // jika sukses buat, notifikasi ke teknisi
        if (! empty($response['status']) && ! empty($response['data']) && $response['data'] instanceof \App\Models\Tiket) {
            $tiket = $response['data'];
            try {
                // coba auto-assign ke teknisi terdekat (geo)
                $assigned = app(PenugasanTeknisiService::class)->tugaskanOtomatis($tiket, 'geo');
                // buat alert untuk teknisi (atau semua teknisi jika belum ter-assign)
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
                \Illuminate\Support\Facades\Log::warning('Tiket postCreate notify gagal: '.$e->getMessage());
            }
        }

        return $this->response($response);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['tiket_foto_sebelum', 'tiket_foto_sesudah'], 'tiket', $m->toArray());

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    protected function handleUploads(GeneralRequest $request, array $fields, string $folder, ?array $existing = null): void
    {
        foreach ($fields as $f) {
            if ($request->hasFile($f)) {
                $request->merge([$f => uploadFile($request->file($f), $folder, ['max_size' => 4096])]);
            } elseif ($request->boolean('remove_'.$f)) {
                $request->merge([$f => null]);
            } else {
                $request->merge([$f => $existing[$f] ?? null]);
            }
        }
    }
}
