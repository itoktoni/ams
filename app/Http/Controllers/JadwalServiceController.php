<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\JadwalService;

class JadwalServiceController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
    }

    public function __construct(JadwalService $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset', 'hasTemplate']);
    }

    public function postCreate(\App\Http\Requests\GeneralRequest $request)
    {
        if (empty($request->input('jadwal_service_tanggal_mulai'))) $request->merge(['jadwal_service_tanggal_mulai' => now()->format('Y-m-d')]);
        if (empty($request->input('jadwal_service_status'))) $request->merge(['jadwal_service_status' => 'aktif']);
        // auto jatuh tempo dari template interval
        if (empty($request->input('jadwal_service_tanggal_jatuh_tempo'))) {
            $tgl = $request->input('jadwal_service_tanggal_mulai') ? \Carbon\Carbon::parse($request->input('jadwal_service_tanggal_mulai')) : now();
            $tpl = $request->input('jadwal_service_id_template') ? \App\Models\TemplateService::find($request->input('jadwal_service_id_template')) : null;
            $bulan = $tpl?->template_service_interval_bulan ?? $request->input('jadwal_service_interval_bulan');
            if (!empty($bulan) && is_numeric($bulan)) $tgl = $tgl->copy()->addMonths((int)$bulan);
            else $tgl = $tgl->copy()->addMonth();
            $request->merge(['jadwal_service_tanggal_jatuh_tempo' => $tgl->format('Y-m-d')]);
        }
        return $this->traitPostCreate($request);
    }

    public function postUpdate(\App\Http\Requests\GeneralRequest $request, $id)
    {
        if (empty($request->input('jadwal_service_status'))) $request->merge(['jadwal_service_status' => 'aktif']);
        return $this->traitPostUpdate($request, $id);
    }
}
