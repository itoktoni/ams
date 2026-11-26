<?php

namespace App\Http\Controllers\Laporan;

use App\Enums\Bbm\JenisKendaraanBbmEnum;
use App\Enums\Bbm\StatusPengajuanBbmEnum;
use App\Http\Controllers\Controller;
use App\Models\BbmPengajuan;
use App\Models\User;
use App\Services\ReportKopService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Report BBM — pengajuan pembelian BBM + pemakaian kuota per periode.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel / PDF).
 * - getExportPdf: download PDF server-side (DomPDF) — tanpa dialog print browser.
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class BbmReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.bbm.table', [
            'statusOptions' => StatusPengajuanBbmEnum::getOptions(),
            'userOptions' => User::getOptions(),
            'jenisKendaraanOptions' => JenisKendaraanBbmEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-bbm.getExportExcel', $request->query());
        }

        $data = (clone $query)->get();

        return view('pages.laporan.bbm.print', array_merge([
            'data' => $data,
            'ringkasan' => $this->ringkasanPerOrang($data),
            'statusOptions' => StatusPengajuanBbmEnum::getOptions(),
            'jenisKendaraanOptions' => JenisKendaraanBbmEnum::getOptions(),
        ], ReportKopService::make('REPORT BBM', $request->only([
            'dari', 'sampai', 'periode', 'status',
        ]))));
    }

    public function getExportPdf(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $data = (clone $query)->get();

        $kop = ReportKopService::make('REPORT BBM', $request->only([
            'dari', 'sampai', 'periode', 'status',
        ]));

        $pdf = Pdf::loadView(
            'pages.laporan.bbm.pdf',
            array_merge([
                'data' => $data,
                'ringkasan' => $this->ringkasanPerOrang($data),
                'statusOptions' => StatusPengajuanBbmEnum::getOptions(),
                'jenisKendaraanOptions' => JenisKendaraanBbmEnum::getOptions(),
            ], $kop, ReportKopService::pdfImages())
        )->setPaper('A4', 'landscape');

        return $pdf->stream('report-bbm-'.now()->format('Ymd-His').'.pdf');
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            $kop = ReportKopService::make('REPORT BBM', request()->only([
                'dari', 'sampai', 'periode', 'status',
            ]));
            echo view('pages.laporan.bbm.excel-header', array_merge(compact('dari', 'sampai'), $kop))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.bbm.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.bbm.excel-footer', [
                'ringkasan' => $this->ringkasanPerOrang((clone $query)->get()),
            ])->render();
        }, 'report-bbm-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * Ringkasan kuota per orang per periode dari data hasil filter.
     * Kuota/terpakai/menunggu/tersedia dihitung penuh satu periode
     * (tidak ikut tersaring tanggal) agar sisa kuota akurat.
     */
    private function ringkasanPerOrang($data): array
    {
        $rows = [];
        $data->groupBy(fn ($r) => $r->bbm_pengajuan_id_user.'|'.$r->bbm_pengajuan_periode)
            ->each(function ($items) use (&$rows) {
                $first = $items->first();
                $info = BbmPengajuan::ringkasanUser($first->bbm_pengajuan_id_user, $first->bbm_pengajuan_periode);
                $rows[] = [
                    'user' => $first->hasUser,
                    'periode' => $first->bbm_pengajuan_periode,
                    'kuota' => (float) $info['kuota'],
                    'terpakai' => (float) $info['terpakai'],
                    'pending' => (float) $info['pending'],
                    'tersedia' => (float) $info['tersedia'],
                ];
            });

        usort($rows, fn ($a, $b) => strcmp(($a['user']?->name ?? '').$a['periode'], ($b['user']?->name ?? '').$b['periode']));

        return [
            'rows' => $rows,
            'totalKuota' => array_sum(array_column($rows, 'kuota')),
            'totalTerpakai' => array_sum(array_column($rows, 'terpakai')),
            'totalPending' => array_sum(array_column($rows, 'pending')),
            'totalTersedia' => array_sum(array_column($rows, 'tersedia')),
        ];
    }

    private function baseQuery(Request $request)
    {
        $query = BbmPengajuan::with(['hasUser', 'hasApprover'])
            ->orderBy('bbm_pengajuan_periode')
            ->orderBy('bbm_pengajuan_tanggal');

        if ($request->filled('dari')) {
            $query->whereDate('bbm_pengajuan_tanggal', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('bbm_pengajuan_tanggal', '<=', $request->input('sampai'));
        }
        if ($request->filled('periode')) {
            $query->where('bbm_pengajuan_periode', $request->input('periode'));
        }
        if ($request->filled('status')) {
            $query->where('bbm_pengajuan_status', $request->input('status'));
        }
        if ($request->filled('peminta')) {
            $query->where('bbm_pengajuan_id_user', $request->input('peminta'));
        }
        if ($request->filled('jenis_kendaraan')) {
            $query->where('bbm_pengajuan_jenis_kendaraan', $request->input('jenis_kendaraan'));
        }

        return $query;
    }
}
