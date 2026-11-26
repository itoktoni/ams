<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Penghapusan;
use Illuminate\Http\Request;

/**
 * Report Penghapusan — aset dihapus + nilai buku per periode.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportPdf: download PDF server-side (DomPDF).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PenghapusanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.penghapusan.table', [
            'statusOptions' => \App\Enums\Penghapusan\StatusPenghapusanEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-penghapusan.getExportExcel', $request->query());
        }

        return view('pages.laporan.penghapusan.print', array_merge([
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\Penghapusan\StatusPenghapusanEnum::getOptions(),
        ], \App\Services\ReportKopService::make('REPORT PENGHAPUSAN', $request->only([
            'dari', 'sampai', 'status',
        ]))));
    }

    public function getExportPdf(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $data = (clone $query)->get();

        $kop = \App\Services\ReportKopService::make('REPORT PENGHAPUSAN', $request->only([
            'dari', 'sampai', 'status',
        ]));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pages.laporan.penghapusan.pdf',
            array_merge(['data' => $data], $kop, \App\Services\ReportKopService::pdfImages())
        )->setPaper('A4', 'landscape');

        return $pdf->stream('report-penghapusan-'.now()->format('Ymd-His').'.pdf');
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.penghapusan.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.penghapusan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.penghapusan.excel-footer')->render();
        }, 'report-penghapusan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = Penghapusan::with('hasAset')->orderBy('penghapusan_tanggal_request');

        if ($request->filled('dari')) {
            $query->whereDate('penghapusan_tanggal_request', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('penghapusan_tanggal_request', '<=', $request->input('sampai'));
        }
        if ($request->filled('status')) {
            $query->where('penghapusan_status', $request->input('status'));
        }

        return $query;
    }
}
