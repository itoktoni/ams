<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Persetujuan;
use Illuminate\Http\Request;

/**
 * Report Persetujuan — approval perpindahan/penghapusan/PO.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PersetujuanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.persetujuan.table', [
            'statusOptions' => \App\Enums\Persetujuan\StatusPersetujuanEnum::getOptions(),
            'levelOptions' => \App\Enums\Persetujuan\LevelPersetujuanEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-persetujuan.getExportExcel', $request->query());
        }

        return view('pages.laporan.persetujuan.print', array_merge([
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\Persetujuan\StatusPersetujuanEnum::getOptions(),
            'levelOptions' => \App\Enums\Persetujuan\LevelPersetujuanEnum::getOptions(),
        ], \App\Services\ReportKopService::make('REPORT PERSETUJUAN', $request->only([
            'status', 'level',
        ]))));
    }

    public function getExportPdf(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $data = (clone $query)->get();

        $kop = \App\Services\ReportKopService::make('REPORT PERSETUJUAN', $request->only([
            'status', 'level',
        ]));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pages.laporan.persetujuan.pdf',
            array_merge(['data' => $data], $kop, \App\Services\ReportKopService::pdfImages())
        )->setPaper('A4', 'landscape');

        return $pdf->stream('report-persetujuan-'.now()->format('Ymd-His').'.pdf');
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        return response()->streamDownload(function () use ($query) {
            echo view('pages.laporan.persetujuan.excel-header')->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.persetujuan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.persetujuan.excel-footer')->render();
        }, 'report-persetujuan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = Persetujuan::with('hasUser')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('persetujuan_status', $request->input('status'));
        }
        if ($request->filled('level')) {
            $query->where('persetujuan_level', $request->input('level'));
        }

        return $query;
    }
}
