<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\DaftarTunggu;

class DaftarTungguController extends Controller
{
    use ControllerTrait {
        getData as traitGetData;
    }

    public function __construct(DaftarTunggu $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->traitGetData()->with(['hasAset', 'hasPeminjam']);
    }

    public function getConvert(\Illuminate\Http\Request $request, $id)
    {
        $dt = $this->model->findOrFail($id);
        if ($dt->daftar_tunggu_status !== 'menunggu') {
            return $this->response(['status'=>false,'message'=>'Hanya yang menunggu bisa di-convert.','data'=>null]);
        }
        $asetId = $dt->daftar_tunggu_id_aset;
        $busy = \App\Models\Peminjaman::where('peminjaman_id_aset', $asetId)->where('peminjaman_status', 'aktif')->whereNull('peminjaman_tanggal_kembali')->exists();
        if ($busy) {
            return $this->response(['status'=>false,'message'=>'Aset masih dipinjam — tunggu dikembalikan dulu.','data'=>null]);
        }
        $p = \App\Models\Peminjaman::create([
            'peminjaman_nomor' => 'P-'.now()->format('YmdHis').'-'.rand(100,999),
            'peminjaman_id_aset' => $asetId,
            'peminjaman_id_peminjam' => $dt->daftar_tunggu_id_peminjam,
            'peminjaman_tujuan' => 'Dari daftar tunggu #'.$dt->daftar_tunggu_id,
            'peminjaman_tanggal_pinjam' => now(),
            'peminjaman_jatuh_tempo' => now()->addDays((int) ($dt->daftar_tunggu_durasi ?? 3)),
            'peminjaman_status' => 'aktif',
            'peminjaman_id_approver' => auth()->id(),
        ]);
        $dt->update(['daftar_tunggu_status' => 'selesai', 'daftar_tunggu_id_peminjaman' => $p->peminjaman_id]);
        return $this->response(['status'=>true,'message'=>'Berhasil convert → Peminjaman '.$p->peminjaman_nomor,'data'=>$p]);
    }
}
