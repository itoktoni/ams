<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\KategoriAset;
use App\Models\KategoriTeknisi;

class KategoriAsetController extends Controller
{
    use ControllerTrait;

    public function __construct(KategoriAset $model)
    {
        $this->model = $model::getModel();
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);
        $linkedTeknisiIds = KategoriTeknisi::where('kategori_id', $data->aset_kategori_id)->pluck('teknisi_id')->all();
        return $this->views($this->template(), ['model' => $data, 'linkedTeknisiIds' => $linkedTeknisiIds]);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['linkedTeknisiIds' => []]);
    }

    public function postCreate(GeneralRequest $request)
    {
        $response = \App\Actions\CreateAction::run($request, $this->model);
        if (! empty($response['status']) && ! empty($response['data']) && $response['data'] instanceof KategoriAset) {
            $this->syncTeknisi($response['data']->aset_kategori_id, $request->input('teknisi_ids', []));
        }
        return $this->response($response);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $response = \App\Actions\UpdateAction::run($request, $id, $this->model);
        if (! empty($response['status'])) $this->syncTeknisi((int) $id, $request->input('teknisi_ids', []));
        return $this->response($response);
    }

    private function syncTeknisi(int $kategoriId, array $teknisiIds): void
    {
        $teknisiIds = array_filter(array_map('intval', $teknisiIds));
        $existing = KategoriTeknisi::where('kategori_id', $kategoriId)->pluck('teknisi_id')->all();
        $toDelete = array_diff($existing, $teknisiIds);
        $toInsert = array_diff($teknisiIds, $existing);
        if ($toDelete) KategoriTeknisi::where('kategori_id', $kategoriId)->whereIn('teknisi_id', $toDelete)->delete();
        foreach ($toInsert as $tid) KategoriTeknisi::firstOrCreate(['kategori_id' => $kategoriId, 'teknisi_id' => $tid]);
    }
}
