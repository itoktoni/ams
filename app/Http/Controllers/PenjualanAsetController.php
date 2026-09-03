<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\PenjualanAset;

class PenjualanAsetController extends Controller
{
    use ControllerTrait;

    public function __construct(PenjualanAset $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->with(['hasAset.hasKategori'])->filter()->sort();
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->with(['hasAset', 'hasPenawaran.hasUser'])->findOrFail($id);
        $penawaran = $data->hasPenawaran()->with('hasUser')->orderByDesc('penawaran_penjualan_harga')->orderBy('penawaran_penjualan_waktu')->get();
        $highest = $penawaran->max('penawaran_penjualan_harga');
        $minBid = $highest ? (float) $highest + 1000 : (float) ($data->penjualan_aset_harga_appraisal ?? 0);
        return $this->views($this->template(), [
            'model' => $data,
            'penawaran' => $penawaran,
            'highest' => $highest,
            'minBid' => $minBid,
            'winner' => $penawaran->first(),
        ]);
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['penjualan_aset_foto_serah_terima'], 'penjualan');

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['penjualan_aset_foto_serah_terima'], 'penjualan', $m->toArray());

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
