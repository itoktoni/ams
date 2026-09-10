<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Faktur;
use Barryvdh\DomPDF\Facade\Pdf;

class FakturController extends Controller
{
    use ControllerTrait;

    public function __construct(Faktur $model)
    {
        $this->model = $model::getModel();
    }

    public function postCreate(GeneralRequest $request)
    {
        $this->handleUploads($request, ['faktur_file'], 'faktur');

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['faktur_file'], 'faktur', $m->toArray());

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    public function getInvoicePdf(\App\Http\Requests\GeneralRequest $request, $id)
    {
        $f = $this->model->with(['hasPesanan.hasVendor'])->findOrFail($id);
        $pesanan = $f->hasPesanan;
        $items = $pesanan ? \App\Models\PesananItem::where('pesanan_item_id_pesanan', $pesanan->pesanan_pembelian_id)->get() : collect([]);
        $pdf = Pdf::loadView('pdf.invoice-faktur', ['f' => $f, 'pesanan' => $pesanan, 'items' => $items])->setPaper('A4', 'portrait');
        return $pdf->download('INV-'.$f->faktur_nomor.'.pdf');
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
