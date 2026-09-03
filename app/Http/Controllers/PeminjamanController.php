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

    protected function isUserMode(): bool
    {
        $user = auth()->user();

        return $user && in_array($user->role, ['pengguna_aset', 'user'], true);
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
        $default = ['model' => $this->model];

        if ($this->isUserMode()) {
            $user = auth()->user();
            $approver = $this->autoApprover();
            $asetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            // availability map: aset_id => true (bisa dipinjam) / false (sedang dipinjam)
            $asetOptions = Aset::where('aset_id_penanggung_jawab', $user->id)->orderBy('aset_nama')->pluck('aset_nama', 'aset_id')->toArray();
            $availabilityMap = [];
            if (!empty($asetIds)) {
                $sedangDipinjamIds = \App\Models\Peminjaman::whereIn('peminjaman_id_aset', $asetIds)
                    ->where('peminjaman_status', 'aktif')
                    ->whereNull('peminjaman_tanggal_kembali')
                    ->pluck('peminjaman_id_aset')
                    ->all();
                foreach ($asetIds as $aid) {
                    $availabilityMap[$aid] = !in_array($aid, $sedangDipinjamIds, true);
                }
            }
            $default = array_merge($default, [
                'isUserMode'      => true,
                'asetOptions'     => $asetOptions,
                'approverName'    => $approver?->name ?? '-',
                'approverId'      => $approver?->id,
                'nowValue'        => now()->format('Y-m-d\TH:i'),
                'availabilityMap' => $availabilityMap,
            ]);
        }

        return $this->traitShare(array_merge($default, $data));
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['peminjaman_foto_kembali'], 'peminjaman');

        if ($this->isUserMode()) {
            $user = auth()->user();

            // validasi: hanya aset miliknya
            $allowed = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (! in_array((int) $request->input('peminjaman_id_aset'), $allowed, true)) {
                return $this->response(['status' => false, 'message' => 'Anda hanya bisa mengajukan peminjaman aset milik Anda.', 'data' => null]);
            }

            // isi otomatis: peminjam, approver, tanggal pinjam = now, status diajukan
            $request->merge([
                'peminjaman_id_peminjam'     => $user->id,
                'peminjaman_id_approver'     => $this->autoApprover()?->id,
                'peminjaman_tanggal_pinjam'  => now()->format('Y-m-d H:i:s'),
                'peminjaman_status'          => 'diajukan',
                'peminjaman_grace_jam'       => 0,
                'peminjaman_denda'           => 0,
                'peminjaman_perpanjang_ke'   => 0,
                'peminjaman_catatan'         => null,
                'peminjaman_tanggal_kembali' => null,
                'peminjaman_kondisi_kembali' => null,
            ]);
            if (! $request->input('peminjaman_nomor')) {
                $request->merge(['peminjaman_nomor' => 'P-'.now()->format('YmdHis').'-'.rand(100, 999)]);
            }
        }

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['peminjaman_foto_kembali'], 'peminjaman', $m->toArray());

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
