<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Enums\OtorisasiKeluar\JenisOtorisasiKeluarEnum;
use App\Enums\OtorisasiKeluar\StatusOtorisasiKeluarEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;

class OtorisasiKeluarController extends Controller
{
    use ControllerTrait {
        share as traitShare;
    }

    public function __construct(\App\Models\OtorisasiKeluar $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset', 'hasApprover']);
    }

    protected function share($data = [])
    {
        return $this->traitShare(array_merge([
            'asetOptions' => Aset::getOptions(),
            'jenisOptions' => JenisOtorisasiKeluarEnum::getOptions(),
            'statusOptions' => StatusOtorisasiKeluarEnum::getOptions(),
            'nowValue' => now()->format('Y-m-d\TH:i'),
        ], $data));
    }

    public function postCreate(GeneralRequest $request)
    {
        if (empty(trim((string) $request->input('otorisasi_keluar_nomor')))) {
            $request->merge(['otorisasi_keluar_nomor' => 'OTR-'.now()->format('YmdHis').'-'.rand(100, 999)]);
        }
        if (empty($request->input('otorisasi_keluar_status'))) {
            $request->merge(['otorisasi_keluar_status' => 'diajukan']);
        }

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        return $this->response(UpdateAction::run($request, $id, $this->model));
    }
}
