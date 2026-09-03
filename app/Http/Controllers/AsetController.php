<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\BukuPenyusutan;
use App\Models\KategoriAset;
use App\Services\PenyusutanService;
use Illuminate\Support\Facades\DB;

class AsetController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
    }

    public function __construct(Aset $model)
    {
        $this->model = $model::getModel();
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), [
            'kategoriFields' => $this->kategoriFields(),
        ]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);

        return $this->views($this->template(), [
            'model' => $data,
            'kategoriFields' => $this->kategoriFields(),
        ]);
    }

    /**
     * Map of kategori_id => custom field DEFINITIONS for that category.
     * Consumed by the asset form to render the right fields dynamically.
     */
    protected function kategoriFields(): array
    {
        return KategoriAset::query()
            ->whereNotNull('aset_kategori_custom_fields')
            ->get(['aset_kategori_id', 'aset_kategori_custom_fields'])
            ->mapWithKeys(function ($row) {
                $defs = $row->aset_kategori_custom_fields;

                return [$row->aset_kategori_id => is_array($defs) ? $defs : []];
            })
            ->toArray();
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['aset_foto'], 'aset');

        return $this->traitPostCreate($request);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['aset_foto'], 'aset', $m->toArray());

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

    /**
     * Manual kalkulasi ulang penyusutan: hapus semua buku lalu hitung dari awal s/d bulan berjalan.
     * Fallback jika scheduler/job mati. GET /aset/recalc/{id}
     */
    public function getRecalc(GeneralRequest $request, $id)
    {
        $aset = $this->model->findOrFail($id);

        if (empty($aset->aset_tanggal_mulai_susut) || (int) $aset->aset_masa_manfaat <= 0) {
            return $this->response(['status' => false, 'message' => 'Aset tidak punya tanggal mulai/masa manfaat — atur di form aset dulu.', 'data' => null]);
        }

        $deleted = 0;
        $created = 0;

        DB::transaction(function () use ($aset, &$deleted, &$created) {
            $deleted = BukuPenyusutan::where('buku_penyusutan_id_aset', $aset->aset_id)->delete();
            $created = app(PenyusutanService::class)->jalankan($aset); // sampai bulan ini (now)
        });

        return $this->response([
            'status' => true,
            'message' => "Kalkulasi ulang selesai: hapus {$deleted}, buat {$created} entri s/d ".now()->format('Y-m'),
            'data' => ['deleted' => $deleted, 'created' => $created, 'redirect' => route('buku-penyusutan.getTable').'?filters[buku_penyusutan_id_aset][$eq]='.$aset->aset_id],
        ]);
    }
}
