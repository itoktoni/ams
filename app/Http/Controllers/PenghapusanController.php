<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Penghapusan;
use Barryvdh\DomPDF\Facade\Pdf;

class PenghapusanController extends Controller
{
    use ControllerTrait;

    public function __construct(Penghapusan $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset']);
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['penghapusan_foto', 'penghapusan_berita_acara'], 'penghapusan');
        if (empty(trim((string) $request->input('penghapusan_nomor')))) {
            $request->merge(['penghapusan_nomor' => 'HPS-'.now()->format('YmdHis').'-'.rand(100,999)]);
        }
        if (empty($request->input('penghapusan_tanggal_request'))) $request->merge(['penghapusan_tanggal_request' => now()->format('Y-m-d H:i:s')]);
        if (empty($request->input('penghapusan_status'))) $request->merge(['penghapusan_status' => 'diajukan']);
        // nilai buku/sisa/gain_loss tidak boleh null (DB default 0) — kosongkan jadi 0
        foreach (['penghapusan_nilai_buku','penghapusan_nilai_sisa','penghapusan_gain_loss'] as $f) {
            if ($request->input($f) === '' || $request->input($f) === null) $request->merge([$f => 0]);
        }

        $res = CreateAction::run($request, $this->model);
        // auto buat persetujuan menunggu agar muncul di list Persetujuan
        if (!empty($res['status']) && $res['data'] instanceof \App\Models\Penghapusan) {
            \App\Models\Persetujuan::firstOrCreate([
                'persetujuan_modul' => 'penghapusan',
                'persetujuan_id_referensi' => $res['data']->penghapusan_id,
                'persetujuan_status' => 'menunggu',
            ], [
                'persetujuan_level' => 'supervisor',
                'persetujuan_id_user' => auth()->id() ?? $res['data']->penghapusan_id,
                'persetujuan_catatan' => 'Pengajuan penghapusan '.$res['data']->penghapusan_nomor,
            ]);
        }
        return $this->response($res);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['penghapusan_foto', 'penghapusan_berita_acara'], 'penghapusan', $m->toArray());
        foreach (['penghapusan_nilai_buku','penghapusan_nilai_sisa','penghapusan_gain_loss'] as $f) {
            if ($request->input($f) === '' || $request->input($f) === null) $request->merge([$f => 0]);
        }

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    public function getApprove(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403, 'Hanya admin/supervisor yang bisa approve.');
        $r = $this->model->findOrFail($id);
        if ($r->penghapusan_status === 'disetujui') return $this->response(['status'=>false,'message'=>'Sudah disetujui.','data'=>null]);
        $r->update(['penghapusan_status' => 'disetujui']);
        // catat persetujuan
        \App\Models\Persetujuan::create([
            'persetujuan_modul' => 'penghapusan',
            'persetujuan_id_referensi' => $r->penghapusan_id,
            'persetujuan_level' => 'supervisor',
            'persetujuan_id_user' => auth()->id(),
            'persetujuan_status' => 'disetujui',
            'persetujuan_catatan' => 'Disetujui via penghapusan '.$r->penghapusan_nomor,
        ]);
        return $this->response(['status'=>true,'message'=>'Penghapusan disetujui.','data'=>$r->fresh()]);
    }

    public function getReject(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403, 'Hanya admin/supervisor yang bisa reject.');
        $r = $this->model->findOrFail($id);
        $catatan = trim((string) $request->input('catatan', $request->input('penghapusan_catatan','')));
        if (! $catatan) return $this->response(['status'=>false,'message'=>'Catatan alasan penolakan wajib diisi.','data'=>null]);
        $r->update(['penghapusan_status' => 'ditolak', 'penghapusan_catatan' => $catatan]);
        \App\Models\Persetujuan::create([
            'persetujuan_modul' => 'penghapusan',
            'persetujuan_id_referensi' => $r->penghapusan_id,
            'persetujuan_level' => 'supervisor',
            'persetujuan_id_user' => auth()->id(),
            'persetujuan_status' => 'ditolak',
            'persetujuan_catatan' => $catatan,
        ]);
        return $this->response(['status'=>true,'message'=>'Penghapusan ditolak.','data'=>$r->fresh()]);
    }

    public function getBeritaAcara(GeneralRequest $request, $id)
    {
        $p = $this->model->with(['hasAset.hasKategori','hasAset.hasLokasi','hasAset.hasPenanggungJawab'])->findOrFail($id);
        $aset = $p->hasAset;
        if (! $aset) abort(404, 'Aset penghapusan tidak ditemukan.');
        // pengaju = user yang buat? fallback auth
        $pengaju = \App\Models\User::find($p->penghapusan_id ?? null) ?? auth()->user();
        $pdf = Pdf::loadView('pdf.berita-acara-penghapusan', ['p' => $p, 'aset' => $aset, 'pengaju' => $pengaju, 'petugas' => auth()->user()])->setPaper('A4', 'portrait');
        return $pdf->download('BA-Hapus-'.$p->penghapusan_nomor.'.pdf');
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
