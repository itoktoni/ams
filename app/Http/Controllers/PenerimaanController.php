<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Penerimaan;

class PenerimaanController extends Controller
{
    use ControllerTrait;

    public function __construct(Penerimaan $model)
    {
        $this->model = $model::getModel();
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['penerimaan_foto'], 'penerimaan');
        if (empty(trim((string) $request->input('penerimaan_nomor')))) {
            $request->merge(['penerimaan_nomor' => 'RCV-'.now()->format('YmdHis').'-'.rand(100,999)]);
        }
        if (empty($request->input('penerimaan_tanggal'))) $request->merge(['penerimaan_tanggal' => now()->format('Y-m-d')]);
        if (empty($request->input('penerimaan_penerima'))) $request->merge(['penerimaan_penerima' => auth()->user()->name ?? 'Gudang']);
        $res = CreateAction::run($request, $this->model);
        if (!empty($res['status']) && $res['data'] instanceof Penerimaan) {
            $this->syncStok($res['data']);
        }
        return $this->response($res);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['penerimaan_foto'], 'penerimaan', $m->toArray());

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

    protected function syncStok(Penerimaan $rcv): void
    {
        if (empty($rcv->penerimaan_id_gudang) || empty($rcv->penerimaan_id_pesanan)) return;
        $items = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $rcv->penerimaan_id_pesanan)->get();
        foreach ($items as $it) {
            $scId = $it->pesanan_item_id_referensi;
            if (!$scId) continue;
            $stok = \App\Models\StokSukuCadang::firstOrCreate(
                ['stok_suku_cadang_id_gudang' => $rcv->penerimaan_id_gudang, 'stok_suku_cadang_id_suku_cadang' => $scId, 'stok_suku_cadang_bin' => null],
                ['stok_suku_cadang_jumlah' => 0]
            );
            $stok->increment('stok_suku_cadang_jumlah', (float) $it->pesanan_item_jumlah);
            \App\Models\PergerakanStok::create([
                'pergerakan_stok_id_suku_cadang' => $scId,
                'pergerakan_stok_id_gudang' => $rcv->penerimaan_id_gudang,
                'pergerakan_stok_tipe' => 'masuk',
                'pergerakan_stok_jumlah' => (float) $it->pesanan_item_jumlah,
                'pergerakan_stok_referensi' => 'penerimaan:'.$rcv->penerimaan_id,
                'pergerakan_stok_catatan' => 'Penerimaan '.$rcv->penerimaan_nomor.' PO '.$rcv->penerimaan_id_pesanan.' gudang '.$rcv->penerimaan_id_gudang,
            ]);
        }
    }
}
