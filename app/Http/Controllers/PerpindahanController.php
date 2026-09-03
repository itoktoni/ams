<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\Perpindahan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PerpindahanController extends Controller
{
    use ControllerTrait;

    public function __construct(Perpindahan $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset', 'hasLokasiAsal', 'hasLokasiTujuan']);
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['perpindahan_foto_keluar', 'perpindahan_foto_terima'], 'perpindahan');

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['perpindahan_foto_keluar', 'perpindahan_foto_terima'], 'perpindahan', $m->toArray());
        $prevStatus = $m->perpindahan_status;
        $response = UpdateAction::run($request, $id, $this->model);
        if (! empty($response['status'])) {
            $fresh = $this->model->find($id);
            if ($prevStatus !== 'disetujui' && $fresh && $fresh->perpindahan_status === 'disetujui') {
                $this->terapkanPerpindahan($fresh);
            }
        }
        return $this->response($response);
    }

    private function terapkanPerpindahan(Perpindahan $p): void
    {
        DB::transaction(function () use ($p) {
            $aset = Aset::find($p->perpindahan_id_aset);
            if (! $aset) return;
            $dari = $aset->aset_id_lokasi;
            $ke = $p->perpindahan_id_lokasi_tujuan;
            if ($ke) $aset->update(['aset_id_lokasi' => $ke]);
            \App\Models\LogStatusAset::create([
                'log_status_aset_id_aset' => $aset->aset_id,
                'log_status_aset_status_dari' => $dari ? (string) $dari : 'perpindahan',
                'log_status_aset_status_ke' => $ke ? (string) $ke : 'perpindahan',
                'log_status_aset_actor' => auth()->id() ?? $p->perpindahan_id_aset,
                'log_status_aset_catatan' => 'Perpindahan '.$p->perpindahan_nomor.' disetujui: '.($dari ?? '-').' → '.$ke,
            ]);
            $p->update(['perpindahan_tanggal_kirim' => $p->perpindahan_tanggal_kirim ?? now(), 'perpindahan_tanggal_terima' => $p->perpindahan_tanggal_terima ?? now()]);
        });
    }

    public function getApprove(GeneralRequest $request, $id)
    {
        $p = $this->model->findOrFail($id);
        if ($p->perpindahan_status === 'disetujui') return $this->response(['status'=>false,'message'=>'Sudah disetujui.','data'=>null]);
        $p->update(['perpindahan_status' => 'disetujui']);
        $this->terapkanPerpindahan($p);
        return $this->response(['status'=>true,'message'=>'Perpindahan disetujui — aset dipindahkan & log tercatat.','data'=>$p->fresh()]);
    }

    public function getBeritaAcara(GeneralRequest $request, $id)
    {
        $p = $this->model->with(['hasAset.hasKategori','hasAset.hasLokasi','hasLokasiAsal','hasLokasiTujuan'])->findOrFail($id);
        $aset = $p->hasAset;
        if (! $aset) abort(404, 'Aset perpindahan tidak ditemukan.');
        $asal = $p->hasLokasiAsal?->aset_lokasi_nama ?? '-';
        $tujuan = $p->hasLokasiTujuan?->aset_lokasi_nama ?? $aset->hasLokasi?->aset_lokasi_nama ?? '-';
        $pdf = Pdf::loadView('pdf.berita-acara-perpindahan', ['p' => $p, 'aset' => $aset, 'asal' => $asal, 'tujuan' => $tujuan, 'petugas' => auth()->user()])->setPaper('A4', 'portrait');
        return $pdf->download('BA-Pindah-'.$p->perpindahan_nomor.'.pdf');
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
