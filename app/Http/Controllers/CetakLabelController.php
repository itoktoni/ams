<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\Department;
use App\Models\KategoriAset;
use App\Models\LokasiAset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

// Modul cetak label aset: form filter (department PJ, kategori, lokasi, status)
// + preview + export PDF A4 dengan garis potong putus-putus tipis.
class CetakLabelController extends Controller
{
    private function filteredQuery(GeneralRequest $request)
    {
        $query = Aset::query()->with(['hasKategori', 'hasLokasi', 'hasPenanggungJawab.hasDepartment'])->orderBy('aset_nama');

        if ($request->filled('department_id')) {
            $query->whereHas('hasPenanggungJawab', fn ($q) => $q->where('department_id', $request->input('department_id')));
        }
        if ($request->filled('kategori_id')) {
            $query->where('aset_id_kategori', $request->input('kategori_id'));
        }
        if ($request->filled('lokasi_id')) {
            $query->where('aset_id_lokasi', $request->input('lokasi_id'));
        }
        if ($request->filled('status')) {
            $query->where('aset_status', $request->input('status'));
        }

        return $query;
    }

    private function withQr($items)
    {
        $logoUri = \App\Services\LabelAsetService::logoDataUri();
        foreach ($items as $aset) {
            if (empty($aset->aset_kode_qr)) {
                $aset->update(['aset_kode_qr' => strtoupper(Str::random(10))]);
                $aset->refresh();
            }
            // Nomor label APN/IX/SDM/DST1/078 (dibuat sekali, lalu dipakai permanen) + QR berlogo
            $aset->nomor_label = \App\Services\LabelAsetService::nomorLabel($aset);
            $segments = explode('/', $aset->nomor_label);
            $aset->nomor_label_kode = end($segments) ?: $aset->nomor_label;
            $aset->nomor_label_rest = count($segments) > 1 ? implode('/', array_slice($segments, 0, -1)) : '';
            $aset->qr_data_uri = \App\Services\LabelAsetService::qrWithLogoDataUri($aset->qr_url, 400);
            if (empty($aset->qr_data_uri)) {
                $aset->qr_data_uri = $this->qrDataUri($aset->qr_url, 300);
            }
            $aset->logo_data_uri = $logoUri;
        }

        return $items;
    }

    public function getIndex(GeneralRequest $request)
    {
        $items = $this->withQr($this->filteredQuery($request)->limit(100)->get());

        return view('pages.cetak-label.index', [
            'items' => $items,
            'filters' => $request->only(['department_id', 'kategori_id', 'lokasi_id', 'status']),
            'departments' => Department::getOptions(),
            'kategoris' => KategoriAset::getOptions(),
            'lokasis' => LokasiAset::getOptions(),
            'statuses' => \App\Enums\Aset\StatusAsetEnum::getOptions(),
        ]);
    }

    public function getPdf(GeneralRequest $request)
    {
        $items = $this->withQr($this->filteredQuery($request)->limit(500)->get());

        // View memakai baris per 5 label
        $rows = $items->chunk(5);

        // Preview HTML mentah (cek CSS) — ?format=html
        if ($request->input('format') === 'html') {
            return view('pdf.cetak-label', ['items' => $rows]);
        }

        $pdf = Pdf::loadView('pdf.cetak-label', ['items' => $rows])->setPaper('A4', 'portrait');


        return $pdf->stream('Label-Aset-A4.pdf');
    }

    private function qrDataUri(string $text, int $size = 300): string
    {
        try {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size), new \BaconQrCode\Renderer\Image\SvgImageBackEnd());
            $writer = new \BaconQrCode\Writer($renderer);
            $svg = $writer->writeString($text);
            return 'data:image/svg+xml;base64,'.base64_encode($svg);
        } catch (\Throwable $e) {
            $url = 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.urlencode($text);
            return $url;
        }
    }
}
