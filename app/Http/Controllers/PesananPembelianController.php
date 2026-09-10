<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Department;
use App\Models\PermintaanSukuCadang;
use App\Models\PesananPembelian;
use Barryvdh\DomPDF\Facade\Pdf;

class PesananPembelianController extends Controller
{
    use ControllerTrait {
        getData as traitGetData;
    }

    public function __construct(PesananPembelian $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->traitGetData()->with(['hasVendor']);
    }

    public function getCreate(\App\Http\Requests\GeneralRequest $request)
    {
        return $this->views($this->template(), [
            'items' => [],
            'budgetInfo' => null,
            'departmentBudgets' => $this->departmentBudgets(),
        ]);
    }

    public function getUpdate(\App\Http\Requests\GeneralRequest $request, $id)
    {
        $data = $this->model->with(['hasVendor'])->findOrFail($id);
        $items = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $data->pesanan_pembelian_id)->get();
        $kode = $data->pesanan_pembelian_kode_budget;
        $budgetInfo = null;
        if (!empty($kode) && is_numeric($kode)) {
            $dept = Department::find((int) $kode);
            if ($dept) $budgetInfo = $this->budgetInfoForDepartment($dept);
        }
        // Korelasi: penerimaan per barang + faktur (invoice PDF) — tanpa persetujuan (sesuai request)
        $penerimaans = \App\Models\Penerimaan::where('penerimaan_id_pesanan', $data->pesanan_pembelian_id)->orderBy('penerimaan_tanggal','desc')->get();
        $fakturs = \App\Models\Faktur::where('faktur_id_pesanan', $data->pesanan_pembelian_id)->orderBy('faktur_tanggal','desc')->get();
        return $this->views($this->template(), [
            'model' => $data,
            'items' => $items,
            'budgetInfo' => $budgetInfo,
            'departmentBudgets' => $this->departmentBudgets(),
            'penerimaans' => $penerimaans,
            'fakturs' => $fakturs,
        ]);
    }

    private function budgetInfoForDepartment(Department $dept): array
    {
        $terpakai = (float) $dept->department_budget_terpakai;
        // pending dari permintaan menunggu (reserve)
        $pending = PermintaanSukuCadang::pendingDepartment($dept->department_id);
        $budget = (float) $dept->department_budget;
        return [
            'department' => $dept,
            'budget' => $budget,
            'terpakai' => $terpakai,
            'pending' => $pending,
            'sisa' => $budget - $terpakai,
            'tersedia' => $budget - $terpakai - $pending,
        ];
    }

    private function departmentBudgets(): array
    {
        return Department::all(['department_id','department_kode','department_nama','department_budget','department_budget_terpakai','department_periode'])
            ->mapWithKeys(function ($d) {
                $terpakai = (float) $d->department_budget_terpakai;
                $pending = PermintaanSukuCadang::pendingDepartment($d->department_id);
                $budget = (float) $d->department_budget;
                $sisa = $budget - $terpakai;
                $tersedia = $budget - $terpakai - $pending;
                return [$d->department_id => [
                    'id' => $d->department_id,
                    'kode' => $d->department_kode,
                    'nama' => $d->department_nama,
                    'periode' => $d->department_periode,
                    'budget' => $budget,
                    'terpakai' => $terpakai,
                    'pending' => $pending,
                    'sisa' => $sisa,
                    'tersedia' => $tersedia,
                    'budgetFmt' => formatRupiah($budget),
                    'terpakaiFmt' => formatRupiah($terpakai),
                    'pendingFmt' => formatRupiah($pending),
                    'sisaFmt' => formatRupiah($sisa),
                    'tersediaFmt' => formatRupiah($tersedia),
                ]];
            })->toArray();
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

    public function postTerimaBarang(\App\Http\Requests\GeneralRequest $request, $id, $itemId)
    {
        $item = \App\Models\PesananItem::where('pesanan_item_id', $itemId)->where('pesanan_item_id_pesanan', $id)->firstOrFail();
        $jumlah = (float) $request->input('jumlah', 0);
        if ($jumlah <= 0) return $this->response(['status'=>false,'message'=>'Jumlah harus >0','data'=>null]);
        $sisa = (float)$item->pesanan_item_jumlah - (float)($item->pesanan_item_diterima ?? 0);
        $ambil = min($jumlah, $sisa);
        // gudang wajib dipilih terlebih dahulu
        $gudangId = $request->input('penerimaan_id_gudang');
        if (!$gudangId) return $this->response(['status'=>false,'message'=>'Pilih gudang tujuan terlebih dahulu.','data'=>null]);
        if (!\App\Models\Gudang::where('gudang_id', $gudangId)->exists()) return $this->response(['status'=>false,'message'=>'Gudang tidak valid.','data'=>null]);
        $item->increment('pesanan_item_diterima', $ambil);
        // buat penerimaan per barang dengan gudang
        $pesanan = $this->model->findOrFail($id);
        $rcv = \App\Models\Penerimaan::create([
            'penerimaan_id_pesanan' => $pesanan->pesanan_pembelian_id,
            'penerimaan_id_gudang' => $gudangId,
            'penerimaan_nomor' => 'RCV-'.now()->format('YmdHis').'-'.rand(100,999),
            'penerimaan_tanggal' => now()->format('Y-m-d'),
            'penerimaan_penerima' => auth()->user()->name ?? 'Gudang',
            'penerimaan_catatan' => 'Terima '.$item->pesanan_item_nama.' sebanyak '.$ambil.' dari PO '.$pesanan->pesanan_pembelian_nomor,
        ]);
        // update stok untuk item ini saja di gudang tujuan
        $stok = \App\Models\StokSukuCadang::firstOrCreate(
            ['stok_suku_cadang_id_gudang' => $gudangId, 'stok_suku_cadang_id_suku_cadang' => $item->pesanan_item_id_referensi, 'stok_suku_cadang_bin' => null],
            ['stok_suku_cadang_jumlah' => 0]
        );
        $stok->increment('stok_suku_cadang_jumlah', $ambil);
        \App\Models\PergerakanStok::create([
            'pergerakan_stok_id_suku_cadang' => $item->pesanan_item_id_referensi,
            'pergerakan_stok_id_gudang' => $gudangId,
            'pergerakan_stok_tipe' => 'masuk',
            'pergerakan_stok_jumlah' => $ambil,
            'pergerakan_stok_referensi' => 'penerimaan:'.$rcv->penerimaan_id.'|item:'.$item->pesanan_item_id,
            'pergerakan_stok_catatan' => 'Penerimaan per barang '.$item->pesanan_item_nama.' ('.$ambil.') PO '.$pesanan->pesanan_pembelian_nomor.' gudang '.$gudangId,
        ]);
        // jika semua item sudah diterima, update PO status jadi diterima
        $totalDipesan = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $id)->sum('pesanan_item_jumlah');
        $totalDiterima = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $id)->sum('pesanan_item_diterima');
        if ($totalDiterima >= $totalDipesan) {
            $pesanan->update(['pesanan_pembelian_status' => 'diterima']);
        } elseif ($totalDiterima > 0) {
            $pesanan->update(['pesanan_pembelian_status' => 'sebagian']);
        }
        return $this->response(['status'=>true,'message'=>'Barang diterima '.$ambil.' unit.','data'=>$item->fresh()]);
    }

    public function getInvoicePdf(\App\Http\Requests\GeneralRequest $request, $id)
    {
        $pesanan = $this->model->with(['hasVendor'])->findOrFail($id);
        $items = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $pesanan->pesanan_pembelian_id)->get();
        $faktur = \App\Models\Faktur::where('faktur_id_pesanan', $pesanan->pesanan_pembelian_id)->first();
        $pdf = Pdf::loadView('pdf.invoice-pesanan', ['pesanan' => $pesanan, 'items' => $items, 'faktur' => $faktur])->setPaper('A4', 'portrait');
        return $pdf->download('INV-PO-'.$pesanan->pesanan_pembelian_nomor.'.pdf');
    }

    private function syncItems(int $pesananId, array $items): void
    {
        // simpan diterima yang sudah ada agar tidak hilang saat edit
        $existingDiterima = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $pesananId)->pluck('pesanan_item_diterima','pesanan_item_id_referensi')->toArray();
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
                'pesanan_item_diterima' => $existingDiterima[$scId] ?? 0,
            ]);
        }
        $total = \App\Models\PesananItem::where('pesanan_item_id_pesanan', $pesananId)->sum('pesanan_item_subtotal');
        \App\Models\PesananPembelian::where('pesanan_pembelian_id', $pesananId)->update(['pesanan_pembelian_total' => $total]);
    }
}
