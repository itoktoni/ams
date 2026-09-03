<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\Opname;
use App\Models\OpnameDetail;
use Illuminate\Http\Request;

class OpnameController extends Controller
{
    use ControllerTrait {
        getData as traitGetData;
    }

    public function __construct(Opname $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->traitGetData()->with(['hasLokasi', 'hasPetugas']);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template());
    }

    public function postCreate(GeneralRequest $request)
    {
        if (! $request->input('opname_nomor')) {
            $request->merge(['opname_nomor' => 'OPN-'.now()->format('YmdHis').'-'.rand(100, 999)]);
        }
        if (! $request->input('opname_id_petugas')) $request->merge(['opname_id_petugas' => auth()->id()]);
        if (! $request->input('opname_tanggal')) $request->merge(['opname_tanggal' => $request->input('opname_tanggal_mulai') ?? now()->format('Y-m-d')]);
        if (! $request->input('opname_tanggal_mulai')) $request->merge(['opname_tanggal_mulai' => $request->input('opname_tanggal') ?? now()->format('Y-m-d')]);
        if (! $request->input('opname_tanggal_selesai')) $request->merge(['opname_tanggal_selesai' => \Carbon\Carbon::parse($request->input('opname_tanggal_mulai'))->addDays(7)->format('Y-m-d')]);
        $response = \App\Actions\CreateAction::run($request, $this->model);
        if (! empty($response['status']) && $response['data'] instanceof Opname) {
            $this->generateDetails($response['data']);
        }
        return $this->response($response);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->with(['hasLokasi', 'hasPetugas'])->findOrFail($id);
        $details = OpnameDetail::with(['hasAset'])->where('opname_detail_id_opname', $data->opname_id)->orderBy('opname_detail_ditemukan', 'desc')->orderBy('opname_detail_id_aset')->get();
        return $this->views($this->template(), ['model' => $data, 'details' => $details]);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $response = \App\Actions\UpdateAction::run($request, $id, $this->model);
        return $this->response($response);
    }

    private function generateDetails(Opname $opname): void
    {
        $asetIds = Aset::where('aset_id_lokasi', $opname->opname_id_lokasi)->pluck('aset_id')->all();
        $opname->update(['opname_total_sistem' => count($asetIds)]);
        foreach ($asetIds as $asetId) {
            $aset = Aset::find($asetId);
            OpnameDetail::firstOrCreate(
                ['opname_detail_id_opname' => $opname->opname_id, 'opname_detail_id_aset' => $asetId],
                ['opname_detail_status_sistem' => $aset?->aset_status, 'opname_detail_kondisi' => $aset?->aset_kondisi, 'opname_detail_ditemukan' => false]
            );
        }
    }

    // Scan QR aset → flag ditemukan (tidak hilang)
    public function postScan(Request $request, $id)
    {
        $opname = $this->model->findOrFail($id);
        if ($opname->opname_status === 'selesai') {
            return response()->json(['status' => false, 'message' => 'Opname sudah selesai.'], 422);
        }
        $raw = trim($request->input('kode', ''));
        if (! $raw) return response()->json(['status' => false, 'message' => 'Kode QR kosong.'], 422);
        $kode = $raw;
        if (str_contains($kode, '://') || str_contains($kode, '/')) {
            if (preg_match('/aset\/scan\/([A-Za-z0-9_-]+)/i', $kode, $m)) $kode = $m[1];
            elseif (preg_match('/aset_qr=([A-Za-z0-9_-]+)/i', $kode, $m)) $kode = $m[1];
            else { $parts = explode('/', trim($kode, '/')); $last = end($parts); $kode = explode('?', $last)[0] ?: $kode; }
            $kode = trim($kode);
        }
        $aset = Aset::where('aset_kode_qr', $kode)->orWhere('aset_kode', $kode)->first();
        if (! $aset) return response()->json(['status' => false, 'message' => 'Aset tidak ditemukan: '.$kode], 404);
        $detail = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_id_aset', $aset->aset_id)->first();
        if (! $detail) return response()->json(['status' => false, 'message' => 'Aset ini tidak termasuk opname lokasi ini: '.$aset->aset_nama], 404);
        if ($detail->opname_detail_ditemukan) {
            return response()->json(['status' => true, 'message' => 'Sudah discan sebelumnya.', 'data' => $detail->fresh()->load('hasAset'), 'already' => true]);
        }
        $detail->update(['opname_detail_ditemukan' => true, 'opname_detail_waktu_scan' => now(), 'opname_detail_id_petugas_scan' => auth()->id(), 'opname_detail_status_fisik' => $aset->aset_status, 'opname_detail_kondisi' => $aset->aset_kondisi]);
        $opname->update(['opname_total_fisik' => OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ditemukan', true)->count()]);
        return response()->json(['status' => true, 'message' => 'Berhasil scan: '.$aset->aset_nama, 'data' => $detail->fresh()->load('hasAset')]);
    }

    // Report perbandingan — perlu auth tapi tanpa GeneralRequest (bypass policy report)
    public function getReport(Request $request, $id)
    {
        $opname = $this->model->with(['hasLokasi', 'hasPetugas'])->findOrFail($id);
        $details = OpnameDetail::with(['hasAset', 'hasPetugasScan'])->where('opname_detail_id_opname', $opname->opname_id)->orderBy('opname_detail_ditemukan', 'desc')->get();
        $found = $details->where('opname_detail_ditemukan', true);
        $missing = $details->where('opname_detail_ditemukan', false);
        return view('pages.opname.report', compact('opname', 'details', 'found', 'missing'));
    }

    public function getReportPrint(Request $request, $id)
    {
        $opname = $this->model->with(['hasLokasi', 'hasPetugas'])->findOrFail($id);
        $details = OpnameDetail::with(['hasAset', 'hasPetugasScan'])->where('opname_detail_id_opname', $opname->opname_id)->orderBy('opname_detail_ditemukan', 'desc')->get();
        $found = $details->where('opname_detail_ditemukan', true);
        $missing = $details->where('opname_detail_ditemukan', false);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.opname-report', compact('opname', 'details', 'found', 'missing'))->setPaper('A4', 'landscape');
        return $pdf->download('OPNAME-'.$opname->opname_nomor.'.pdf');
    }
}
