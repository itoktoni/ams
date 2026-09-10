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
        $this->normalizeChecklist($request);
        if (empty($request->input('riwayat_service_tanggal'))) $request->merge(['riwayat_service_tanggal' => now()->format('Y-m-d')]);
        $res = CreateAction::run($request, $this->model);
        if (!empty($res['status']) && $res['data'] instanceof RiwayatService) {
            \App\Jobs\CreateFollowUpJadwalServiceJob::dispatch($res['data']->riwayat_service_id);
        }
        return $this->response($res);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['riwayat_service_ttd'], 'service', $m->toArray());
        $this->normalizeChecklist($request);

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    public function postBuatFollowUp(GeneralRequest $request, $id)
    {
        $riwayat = $this->model->findOrFail($id);
        \App\Jobs\CreateFollowUpJadwalServiceJob::dispatchSync($riwayat->riwayat_service_id);
        // ambil jadwal terbaru
        $follow = \App\Models\JadwalService::where('jadwal_service_id_aset', $riwayat->riwayat_service_id_aset)
            ->where('jadwal_service_tanggal_mulai','>',$riwayat->riwayat_service_tanggal ?? now())
            ->orderBy('jadwal_service_tanggal_mulai')->first();
        $msg = $follow ? 'Follow-up dibuat: '.formatDate($follow->jadwal_service_tanggal_mulai).' — '.$follow->jadwal_service_id : 'Sudah ada follow-up atau template tidak ditemukan.';
        return $this->response(['status'=>true,'message'=>$msg,'data'=>$follow]);
    }

    protected function normalizeChecklist(GeneralRequest $request): void
    {
        $val = $request->input('riwayat_service_checklist');
        if (is_string($val) && $val !== '') {
            // jika sudah json valid, biarkan
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $request->merge(['riwayat_service_checklist' => json_encode($decoded)]);
            } else {
                // koma separated -> array
                $arr = array_filter(array_map('trim', explode(',', $val)));
                $request->merge(['riwayat_service_checklist' => json_encode($arr)]);
            }
        } elseif (is_array($val)) {
            $request->merge(['riwayat_service_checklist' => json_encode($val)]);
        } elseif (empty($val)) {
            $request->merge(['riwayat_service_checklist' => null]);
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
