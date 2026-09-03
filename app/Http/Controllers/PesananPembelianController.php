<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\PesananPembelian;

class PesananPembelianController extends Controller
{
    use ControllerTrait;

    public function __construct(PesananPembelian $model)
    {
        $this->model = $model::getModel();
    }

    public function getCreate(\App\Http\Requests\GeneralRequest $request)
    {
        return $this->views($this->template(), ['items' => []]);
    }

    public function getUpdate(\App\Http\Requests\GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);
        $items = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $data->pesanan_pembelian_id)->get();
        return $this->views($this->template(), ['model' => $data, 'items' => $items]);
    }

    public function postCreate(\App\Http\Requests\GeneralRequest $request)
    {
        if (! $request->input('pesanan_pembelian_nomor')) $request->merge(['pesanan_pembelian_nomor' => 'PO-'.now()->format('YmdHis').'-'.rand(100,999)]);
        if (! $request->input('pesanan_pembelian_tanggal')) $request->merge(['pesanan_pembelian_tanggal' => now()->format('Y-m-d')]);
        $items = $request->input('items', []);
        $total = collect($items)->sum(fn($r) => (float)($r['jumlah'] ?? 0) * (float)($r['harga'] ?? 0));
        $request->merge(['pesanan_pembelian_total' => $total]);
        $response = \App\Actions\CreateAction::run($request, $this->model);
        if (! empty($response['status']) && $response['data'] instanceof \App\Models\PesananPembelian) {
            $this->syncItems($response['data']->pesanan_pembelian_id, $items);
        }
        return $this->response($response);
    }

    public function postUpdate(\App\Http\Requests\GeneralRequest $request, $id)
    {
        $items = $request->input('items', []);
        $total = collect($items)->sum(fn($r) => (float)($r['jumlah'] ?? 0) * (float)($r['harga'] ?? 0));
        $request->merge(['pesanan_pembelian_total' => $total]);
        $response = \App\Actions\UpdateAction::run($request, $id, $this->model);
        if (! empty($response['status'])) $this->syncItems((int) $id, $items);
        return $this->response($response);
    }

    private function syncItems(int $pesananId, array $items): void
    {
        \App\Models\PesananItem::where('pesanan_item_id_pesanan', $pesananId)->delete();
        foreach ($items as $row) {
            $scId = $row['suku_cadang_id'] ?? null;
            if (! $scId) continue;
            $sc = \App\Models\SukuCadang::find($scId);
            $jumlah = (float) ($row['jumlah'] ?? 1);
            $harga = (float) ($row['harga'] ?? $sc?->suku_cadang_harga ?? 0);
            \App\Models\PesananItem::create([
                'pesanan_item_id_pesanan' => $pesananId,
                'pesanan_item_tipe' => 'suku_cadang',
                'pesanan_item_id_referensi' => $scId,
                'pesanan_item_nama' => $sc?->suku_cadang_nama ?? 'Item',
                'pesanan_item_jumlah' => $jumlah,
                'pesanan_item_harga' => $harga,
                'pesanan_item_subtotal' => $jumlah * $harga,
            ]);
        }
        $total = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $pesananId)->sum('pesanan_item_subtotal');
        \App\Models\PesananPembelian::where('pesanan_pembelian_id', $pesananId)->update(['pesanan_pembelian_total' => $total]);
    }
}
