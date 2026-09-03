<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\DokumenAset;

class DokumenAsetController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
        getData as traitGetData;
    }

    public function __construct(DokumenAset $model)
    {
        $this->model = $model::getModel();
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['aset_dokumen_file'], 'aset_dokumen');

        return $this->traitPostCreate($request);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['aset_dokumen_file'], 'aset_dokumen', $m->toArray());

        return $this->traitPostUpdate($request, $id);
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

    protected function getData()
    {
        $query = $this->traitGetData();
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $asetIds = \App\Models\Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (empty($asetIds)) {
                $query->whereRaw('1=0');
            } else {
                $query->whereIn('aset_dokumen_id_aset', $asetIds);
            }
        }
        return $query;
    }
}
