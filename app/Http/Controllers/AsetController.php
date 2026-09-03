<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\BukuPenyusutan;
use App\Models\JadwalService;
use App\Models\KategoriAset;
use App\Models\RiwayatService;
use App\Models\Tiket;
use App\Models\User;
use App\Services\PenyusutanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AsetController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
        getData as traitGetData;
    }

    public function __construct(Aset $model)
    {
        $this->model = $model::getModel();
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), [
            'kategoriFields' => $this->kategoriFields(),
            'linkedSukuCadangIds' => [],
        ]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);
        $linkedSukuCadangIds = \App\Models\AsetSukuCadang::where('aset_suku_cadang_id_aset', $data->aset_id)->pluck('aset_suku_cadang_id_suku_cadang')->all();

        return $this->views($this->template(), [
            'model' => $data,
            'kategoriFields' => $this->kategoriFields(),
            'linkedSukuCadangIds' => $linkedSukuCadangIds,
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
        $response = $this->traitPostCreate($request);
        if (! empty($response['data']) && $response['data'] instanceof \App\Models\Aset) {
            $this->syncSukuCadang($response['data']->aset_id, $request->input('suku_cadang_ids', []));
        } elseif (is_object($response) && method_exists($response, 'getData')) {
            $data = $response->getData(true);
            if (! empty($data['data']['aset_id'])) $this->syncSukuCadang($data['data']['aset_id'], $request->input('suku_cadang_ids', []));
        }
        return $response;
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $m = $this->model->findOrFail($id);
        $this->handleUploads($request, ['aset_foto'], 'aset', $m->toArray());
        $response = $this->traitPostUpdate($request, $id);
        $this->syncSukuCadang((int) $id, $request->input('suku_cadang_ids', []));
        return $response;
    }

    private function syncSukuCadang(int $asetId, array $scIds): void
    {
        $scIds = array_filter(array_map('intval', $scIds));
        $existing = \App\Models\AsetSukuCadang::where('aset_suku_cadang_id_aset', $asetId)->pluck('aset_suku_cadang_id_suku_cadang')->all();
        $toDelete = array_diff($existing, $scIds);
        $toInsert = array_diff($scIds, $existing);
        if ($toDelete) \App\Models\AsetSukuCadang::where('aset_suku_cadang_id_aset', $asetId)->whereIn('aset_suku_cadang_id_suku_cadang', $toDelete)->delete();
        foreach ($toInsert as $sid) {
            \App\Models\AsetSukuCadang::firstOrCreate(['aset_suku_cadang_id_aset' => $asetId, 'aset_suku_cadang_id_suku_cadang' => $sid], ['aset_suku_cadang_jumlah' => 1]);
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

    protected function getData()
    {
        $query = $this->traitGetData()->with(['hasKategori', 'hasLokasi', 'hasPeminjamanAktif']);

        $user = auth()->user();
        if (! $user) return $query;

        // pengguna_aset / customer / user biasa: hanya lihat aset yang di-assign ke dia
        // admin, developer, supervisor, teknisi: lihat semua
        $restrictedRoles = ['pengguna_aset', 'customer', 'user'];
        if (in_array($user->role, $restrictedRoles, true)) {
            $query->where('aset_id_penanggung_jawab', $user->id);
        }

        return $query;
    }

    public function getBeritaAcara(GeneralRequest $request, $id)
    {
        $aset = $this->model->with(['hasKategori','hasLokasi','hasPenanggungJawab'])->findOrFail($id);
        $penerima = $aset->hasPenanggungJawab ?? User::where('role', 'pengguna_aset')->first() ?? $aset->hasPenanggungJawab;
        if (! $penerima) {
            abort(404, 'Aset belum di-assign ke pengguna (aset_id_penanggung_jawab kosong). Assign dulu via form aset.');
        }
        $pemberi = auth()->user();

        $pdf = Pdf::loadView('pdf.berita-acara', [
            'aset' => $aset,
            'penerima' => $penerima,
            'pemberi' => $pemberi,
        ])->setPaper('A4', 'portrait');

        $filename = 'BA-'.$aset->aset_kode.'-'.$penerima->id.'.pdf';

        return $pdf->download($filename);
    }

    public function getQr(\Illuminate\Http\Request $request, $id)
    {
        $aset = $this->model->findOrFail($id);
        if (empty($aset->aset_kode_qr)) {
            $aset->update(['aset_kode_qr' => strtoupper(\Illuminate\Support\Str::random(10))]);
            $aset->refresh();
        }
        $qrText = $aset->qr_url;
        $qrDataUri = $this->qrDataUri($qrText, 300);
        return view('pages.aset.qr', ['aset' => $aset, 'qrText' => $qrText, 'qrDataUri' => $qrDataUri, 'model' => $aset]);
    }

    public function getQrPrint(\Illuminate\Http\Request $request, $id)
    {
        $aset = $this->model->findOrFail($id);
        if (empty($aset->aset_kode_qr)) {
            $aset->update(['aset_kode_qr' => strtoupper(\Illuminate\Support\Str::random(10))]);
            $aset->refresh();
        }
        $qrText = $aset->qr_url;
        $qrDataUri = $this->qrDataUri($qrText, 400);
        $pdf = Pdf::loadView('pdf.qr-aset', ['aset' => $aset, 'qrText' => $qrText, 'qrDataUri' => $qrDataUri])->setPaper('A4', 'portrait');
        return $pdf->download('QR-'.$aset->aset_kode.'.pdf');
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
