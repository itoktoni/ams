<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\AsetSukuCadang;
use App\Models\SukuCadang;

class SukuCadangController extends Controller
{
    use ControllerTrait;

    public function __construct(SukuCadang $model)
    {
        $this->model = $model::getModel();
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['linkedAsetIds' => []]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);
        $linkedAsetIds = AsetSukuCadang::where('aset_suku_cadang_id_suku_cadang', $data->suku_cadang_id)->pluck('aset_suku_cadang_id_aset')->all();
        return $this->views($this->template(), ['model' => $data, 'linkedAsetIds' => $linkedAsetIds]);
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['suku_cadang_foto'], 'suku_cadang');
        $response = CreateAction::run($request, $this->model);
        if (! empty($response['status']) && ! empty($response['data']) && $response['data'] instanceof SukuCadang) {
            $this->syncAset($response['data']->suku_cadang_id, $request->input('aset_ids', []));
        }
        return $this->response($response);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['suku_cadang_foto'], 'suku_cadang', $m->toArray());
        $response = UpdateAction::run($request, $id, $this->model);
        if (! empty($response['status'])) {
            $this->syncAset($id, $request->input('aset_ids', []));
        }
        return $this->response($response);
    }

    private function syncAset(int $sukuCadangId, array $asetIds): void
    {
        $asetIds = array_filter(array_map('intval', $asetIds));
        $existing = AsetSukuCadang::where('aset_suku_cadang_id_suku_cadang', $sukuCadangId)->pluck('aset_suku_cadang_id_aset')->all();
        $toDelete = array_diff($existing, $asetIds);
        $toInsert = array_diff($asetIds, $existing);
        if ($toDelete) AsetSukuCadang::where('aset_suku_cadang_id_suku_cadang', $sukuCadangId)->whereIn('aset_suku_cadang_id_aset', $toDelete)->delete();
        foreach ($toInsert as $aid) {
            AsetSukuCadang::firstOrCreate(['aset_suku_cadang_id_aset' => $aid, 'aset_suku_cadang_id_suku_cadang' => $sukuCadangId], ['aset_suku_cadang_jumlah' => 1]);
        }
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
