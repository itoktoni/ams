<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\RiwayatService;

class RiwayatServiceController extends Controller
{
    use ControllerTrait;

    public function __construct(RiwayatService $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset', 'hasTeknisi', 'hasTiket']);
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['riwayat_service_ttd'], 'service');

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['riwayat_service_ttd'], 'service', $m->toArray());

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
