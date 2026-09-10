<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\LokasiAset;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
        getData as traitGetData;
        share as traitShare;
    }

    public function __construct(Tiket $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $user = auth()->user();
        $asetOptions = Aset::getOptions();
        // pengguna hanya lihat aset miliknya
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $myAsetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (!empty($myAsetIds)) {
                $asetOptions = Aset::whereIn('aset_id', $myAsetIds)->pluck('aset_nama', 'aset_id')->toArray();
                if (empty($asetOptions)) $asetOptions = Aset::whereIn('aset_id', $myAsetIds)->get()->mapWithKeys(fn($a)=>[$a->aset_id=>$a->aset_nama.' — '.$a->aset_kode])->toArray();
            } else {
                $asetOptions = [];
            }
        }

        $lokasiMap = LokasiAset::pluck('aset_lokasi_nama', 'aset_lokasi_id')->toArray();
        $asetLokasiMap = [];
        foreach (Aset::pluck('aset_id_lokasi', 'aset_id') as $asetId => $lokasiId) {
            $asetLokasiMap[$asetId] = ($lokasiId && isset($lokasiMap[$lokasiId])) ? ['id' => (string) $lokasiId, 'nama' => $lokasiMap[$lokasiId]] : null;
        }

        $qrAsetId = null;
        $qrAsetLabel = null;
        if (request()->filled('aset_qr')) {
            $kode = trim(request()->input('aset_qr'));
            $asetQr = Aset::where('aset_kode_qr', $kode)->orWhere('aset_kode', $kode)->first(['aset_id','aset_nama','aset_kode']);
            if ($asetQr) { $qrAsetId = $asetQr->aset_id; $qrAsetLabel = $asetQr->aset_nama.' — '.$asetQr->aset_kode; }
        }

        return $this->traitShare(array_merge([
            'asetOptions'    => $asetOptions,
            'asetLokasiMap'  => $asetLokasiMap,
            'qrAsetId'       => $qrAsetId,
            'qrAsetLabel'    => $qrAsetLabel,
        ], $data));
    }

    protected function getData()
    {
        $query = $this->traitGetData()->with(['hasAset', 'hasLokasi', 'hasPelapor', 'hasTeknisi']);
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            // pengguna hanya lihat tiket yang dia laporkan atau untuk aset miliknya
            $myAsetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            $query->where(function($q) use ($user, $myAsetIds) {
                $q->where('tiket_id_pelapor', $user->id);
                if (!empty($myAsetIds)) $q->orWhereIn('tiket_id_aset', $myAsetIds);
            });
        }
        // teknisi: hanya tiket yang di-assign ke dia + yang belum di-assign (buka) biar bisa pick — sekarang 1 table users
        if ($user && $user->role === 'teknisi') {
            $query->where(function($q) use ($user) {
                $q->where('tiket_id_teknisi', $user->id)
                  ->orWhereNull('tiket_id_teknisi');
            });
        }
        return $query;
    }

    public function postCreate(GeneralRequest $request)
    {
        return $this->response(\App\Actions\Tiket\CreateTiketAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        return $this->response(\App\Actions\Tiket\UpdateTiketAction::run($request, $id, $this->model));
    }

    // Ambil tiket (claim) — teknisi
    public function postClaim(GeneralRequest $request, $id)
    {
        $res = \App\Actions\Tiket\ClaimTiketAction::run((int) $id, auth()->id());
        return $this->response($res);
    }

    // Simpan analisa → progres
    public function postAnalisa(Request $request, $id)
    {
        $res = \App\Actions\Tiket\AnalisaTiketAction::run($request, (int) $id);
        return $this->response($res);
    }

    // Simpan kesimpulan → selesai
    public function postSelesai(Request $request, $id)
    {
        $res = \App\Actions\Tiket\SelesaiTiketAction::run($request, (int) $id);
        return $this->response($res);
    }

    // Detail + timeline — dengan pembatasan akses untuk pengguna
    public function getShow(GeneralRequest $request, $id)
    {
        $tiket = $this->model->with(['hasAset', 'hasLokasi', 'hasPelapor', 'hasTeknisi', 'hasLogTiket'])->findOrFail($id);
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $myAsetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            $isOwner = (int) $tiket->tiket_id_pelapor === (int) $user->id || (!empty($myAsetIds) && in_array($tiket->tiket_id_aset, $myAsetIds, true));
            if (! $isOwner) {
                abort(403, 'Anda tidak berhak melihat tiket ini.');
            }
        }
        if ($user && $user->role === 'teknisi') {
            // teknisi hanya boleh lihat tiket miliknya atau yang masih buka (pool)
            if (!empty($tiket->tiket_id_teknisi) && (int) $tiket->tiket_id_teknisi !== (int) $user->id) {
                abort(403, 'Tiket ini sudah diambil teknisi lain.');
            }
        }
        return $this->views('pages.tiket.show', ['tiket' => $tiket]);
    }

    // Report bulanan teknisi — bukti pekerjaan harian (hanya teknisi/admin/developer/supervisor)
    public function getReportBulanan(Request $request)
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','customer','user'], true)) {
            abort(403, 'Report hanya untuk teknisi.');
        }
        $isTeknisi = $user && $user->role === 'teknisi';

        // filter
        $teknisiId = $request->input('teknisi_id', $isTeknisi ? $user->id : null);
        $periode = $request->input('periode', now()->format('Y-m'));
        // periode format YYYY-MM
        if (! preg_match('/^\d{4}-\d{2}$/', $periode)) $periode = now()->format('Y-m');
        [$tahun, $bulan] = explode('-', $periode);

        $query = Tiket::with(['hasAset', 'hasLokasi', 'hasPelapor', 'hasTeknisi'])
            ->whereNotNull('tiket_id_teknisi');
        if ($teknisiId) $query->where('tiket_id_teknisi', $teknisiId);
        // bukti pekerjaan = tiket yang pernah dikerjakan teknisi di bulan tersebut (berdasarkan tanggal selesai/analisa/tugas)
        $query->where(function ($q) use ($tahun, $bulan) {
            $q->whereYear('tiket_tanggal_selesai', $tahun)->whereMonth('tiket_tanggal_selesai', $bulan)
              ->orWhere(function ($q2) use ($tahun, $bulan) {
                  $q2->whereYear('tiket_tanggal_analisa', $tahun)->whereMonth('tiket_tanggal_analisa', $bulan);
              })
              ->orWhere(function ($q3) use ($tahun, $bulan) {
                  $q3->whereYear('tiket_tanggal_tugas', $tahun)->whereMonth('tiket_tanggal_tugas', $bulan)
                     ->whereIn('tiket_status', ['ditugaskan', 'progres', 'menunggu_part', 'selesai', 'terverifikasi']);
              });
        });

        $data = $query->orderBy('tiket_tanggal_selesai', 'desc')->orderBy('tiket_tanggal_tugas', 'desc')->get();

        // agregasi
        $total = $data->count();
        $selesai = $data->whereIn('tiket_status', ['selesai', 'terverifikasi'])->count();
        $progres = $data->where('tiket_status', 'progres')->count();
        $ditugaskan = $data->where('tiket_status', 'ditugaskan')->count();
        $terlambat = $data->where('tiket_terlambat_sla', true)->count();
        $avgDurasi = $total ? round($data->avg('tiket_durasi_menit') ?? 0) : 0;

        $teknisiOptions = User::where('role', 'teknisi')->pluck('name', 'id')->toArray();

        if ($request->input('export') === 'pdf') {
            return $this->exportReportPdf($data, compact('teknisiId', 'periode', 'total', 'selesai', 'progres', 'ditugaskan', 'terlambat', 'avgDurasi'));
        }
        if ($request->input('export') === 'excel') {
            return $this->exportReportExcel($data, $periode, $teknisiId);
        }

        return $this->views('pages.tiket.report-bulanan', [
            'data' => $data,
            'teknisiId' => $teknisiId,
            'periode' => $periode,
            'teknisiOptions' => $teknisiOptions,
            'total' => $total,
            'selesai' => $selesai,
            'progres' => $progres,
            'ditugaskan' => $ditugaskan,
            'terlambat' => $terlambat,
            'avgDurasi' => $avgDurasi,
            'isTeknisi' => $isTeknisi,
        ]);
    }

    private function exportReportPdf($data, $meta)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-bulanan', array_merge(['data' => $data], $meta));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('report-tiket-'.$meta['periode'].'.pdf');
    }

    private function exportReportExcel($data, $periode, $teknisiId)
    {
        $filename = 'report-tiket-'.$periode.($teknisiId ? '-teknisi-'.$teknisiId : '').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];
        $callback = function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nomor', 'Aset', 'Status', 'Teknisi', 'Tgl Dibuat', 'Tgl Diambil', 'Tgl Selesai', 'Analisa', 'Kesimpulan']);
            foreach ($data as $t) {
                fputcsv($out, [
                    $t->tiket_nomor,
                    $t->hasAset?->aset_nama.' - '.$t->hasAset?->aset_kode,
                    $t->tiket_status,
                    $t->hasTeknisi?->name,
                    $t->tiket_tanggal_lapor,
                    $t->tiket_tanggal_tugas,
                    $t->tiket_tanggal_selesai,
                    str_replace(["\r", "\n"], ' ', (string) $t->tiket_analisa),
                    str_replace(["\r", "\n"], ' ', (string) $t->tiket_kesimpulan),
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }
}
