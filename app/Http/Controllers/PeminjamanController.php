<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanController extends Controller
{
    use ControllerTrait {
        share as traitShare;
    }

    public function __construct(Peminjaman $model)
    {
        $this->model = $model::getModel();
    }

    protected function autoApprover(): ?User
    {
        return User::whereIn('role', ['admin', 'supervisor'])->orderBy('id')->first();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset', 'hasPeminjam', 'hasApprover']);
    }

    protected function share($data = [])
    {
        $user = auth()->user();
        $default = ['model' => $this->model];

        $lokasiMap = \App\Models\LokasiAset::pluck('aset_lokasi_nama', 'aset_lokasi_id')->toArray();
        $asetLokasiMap = [];
        foreach (Aset::pluck('aset_id_lokasi', 'aset_id') as $asetId => $lokasiId) {
            $asetLokasiMap[$asetId] = ($lokasiId && isset($lokasiMap[$lokasiId])) ? ['id' => (string) $lokasiId, 'nama' => $lokasiMap[$lokasiId]] : null;
        }
        $budgetInfo = null;
        if ($user && $user->department_id) {
            $dept = \App\Models\Department::find($user->department_id);
            if ($dept) {
                $terpakai = (float) \App\Models\PermintaanSukuCadang::where('department_id', $dept->department_id)->whereNotIn('permintaan_suku_cadang_status', ['ditolak'])->sum('permintaan_suku_cadang_subtotal');
                $budgetInfo = ['department' => $dept, 'terpakai' => $terpakai, 'sisa' => (float) $dept->department_budget - $terpakai];
            }
        }

        $isPengguna = $user && in_array($user->role, ['pengguna_aset', 'user'], true);
        $allAsetIds = Aset::pluck('aset_id')->all();
        $sedangDipinjamAll = \App\Models\Peminjaman::whereIn('peminjaman_id_aset', $allAsetIds)->where('peminjaman_status', 'aktif')->whereNull('peminjaman_tanggal_kembali')->pluck('peminjaman_id_aset')->all();
        $availabilityMapAll = [];
        foreach ($allAsetIds as $aid) $availabilityMapAll[$aid] = !in_array($aid, $sedangDipinjamAll, true);
        if ($isPengguna) {
            $asetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            $asetOptions = Aset::where('aset_id_penanggung_jawab', $user->id)->orderBy('aset_nama')->pluck('aset_nama', 'aset_id')->toArray();
            $availMap = [];
            foreach ($asetIds as $aid) $availMap[$aid] = $availabilityMapAll[$aid] ?? true;
            $approver = $this->autoApprover();
            $default = array_merge($default, [
                'asetOptions' => $asetOptions,
                'approverId' => $approver?->id,
                'nowValue' => now()->format('Y-m-d\TH:i'),
                'availabilityMap' => $availMap,
                'asetLokasiMap' => $asetLokasiMap,
                'budgetInfo' => $budgetInfo,
            ]);
        } else {
            $approver = $this->autoApprover();
            $default = array_merge($default, [
                'asetLokasiMap' => $asetLokasiMap,
                'budgetInfo' => $budgetInfo,
                'approverId' => $approver?->id,
                'nowValue' => now()->format('Y-m-d\TH:i'),
                'availabilityMap' => $availabilityMapAll,
            ]);
        }

        return $this->traitShare(array_merge($default, $data));
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['peminjaman_foto_kembali'], 'peminjaman');
        $user = auth()->user();
        $isPengguna = $user && in_array($user->role, ['pengguna_aset', 'user'], true);
        if ($isPengguna) {
            $allowed = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (! in_array((int) $request->input('peminjaman_id_aset'), $allowed, true)) {
                return $this->response(['status' => false, 'message' => 'Anda hanya bisa mengajukan peminjaman aset milik Anda.', 'data' => null]);
            }
        }
        $asetId = (int) $request->input('peminjaman_id_aset');
        if ($asetId) {
            $sedangDipinjam = Peminjaman::where('peminjaman_id_aset', $asetId)->where('peminjaman_status', 'aktif')->whereNull('peminjaman_tanggal_kembali')->exists();
            if ($sedangDipinjam) {
                $asetNama = Aset::where('aset_id', $asetId)->value('aset_nama') ?? 'Aset';
                return $this->response(['status' => false, 'message' => $asetNama.' sedang dipinjam — silakan masuk Daftar Tunggu.', 'data' => null]);
            }
        }
        if (! $request->input('peminjaman_id_peminjam')) $request->merge(['peminjaman_id_peminjam' => $user?->id]);
        if (! $request->input('peminjaman_id_approver')) $request->merge(['peminjaman_id_approver' => $isPengguna ? $this->autoApprover()?->id : $user?->id]);
        if (! $request->input('peminjaman_tanggal_pinjam')) $request->merge(['peminjaman_tanggal_pinjam' => now()->format('Y-m-d H:i:s')]);
        if (! $request->input('peminjaman_status')) $request->merge(['peminjaman_status' => $isPengguna ? 'diajukan' : 'aktif']);
        if (! $request->input('peminjaman_nomor')) $request->merge(['peminjaman_nomor' => 'P-'.now()->format('YmdHis').'-'.rand(100, 999)]);
        if ($isPengguna) {
            $request->merge(['peminjaman_grace_jam' => 0, 'peminjaman_denda' => 0, 'peminjaman_perpanjang_ke' => 0, 'peminjaman_tanggal_kembali' => null, 'peminjaman_kondisi_kembali' => null]);
        }

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['peminjaman_foto_kembali'], 'peminjaman', $m->toArray());

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    public function getApprove(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403, 'Hanya admin/supervisor yang bisa approve.');
        $r = $this->model->findOrFail($id);
        $r->update(['peminjaman_status' => 'aktif', 'peminjaman_tanggal_pinjam' => $r->peminjaman_tanggal_pinjam ?? now()]);
        return $this->response(['status'=>true,'message'=>'Peminjaman disetujui.','data'=>$r->fresh()]);
    }

    public function getReject(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403, 'Hanya admin/supervisor yang bisa reject.');
        $r = $this->model->findOrFail($id);
        $catatan = trim((string) $request->input('catatan', $request->input('peminjaman_catatan', '')));
        if (! $catatan) {
            return $this->response(['status'=>false,'message'=>'Catatan alasan penolakan wajib diisi.','data'=>null]);
        }
        $r->update(['peminjaman_status' => 'ditolak', 'peminjaman_catatan' => $catatan]);
        return $this->response(['status'=>true,'message'=>'Peminjaman ditolak.','data'=>$r->fresh()]);
    }

    public function getReturn(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403, 'Hanya admin/supervisor yang bisa.');
        $r = $this->model->findOrFail($id);
        $r->update(['peminjaman_status' => 'selesai', 'peminjaman_tanggal_kembali' => now()]);
        return $this->response(['status'=>true,'message'=>'Aset dikembalikan.','data'=>$r->fresh()]);
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
