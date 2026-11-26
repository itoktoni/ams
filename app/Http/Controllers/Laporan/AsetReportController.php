<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use Illuminate\Http\Request;

/**
 * Report Aset — daftar aset + nilai perolehan per periode.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel / PDF).
 * - getExportPdf: download PDF server-side (DomPDF) — tanpa dialog print browser.
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class AsetReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.aset.table', [
            'statusOptions' => \App\Enums\Aset\StatusAsetEnum::getOptions(),
            'kondisiOptions' => \App\Enums\Aset\KondisiAsetEnum::getOptions(),
            'divisiOptions' => \App\Models\Department::getOptions(),
            'ruanganOptions' => \App\Models\LokasiAset::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-aset.getExportExcel', $request->query());
        }

        return view('pages.laporan.aset.print', array_merge([
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\Aset\StatusAsetEnum::getOptions(),
            'kondisiOptions' => \App\Enums\Aset\KondisiAsetEnum::getOptions(),
        ], \App\Services\ReportKopService::make('REPORT ASET', $request->only([
            'dari', 'sampai', 'divisi', 'ruangan', 'status', 'kondisi',
        ]))));
    }

    public function getExportPdf(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $data = (clone $query)->get();

        $kop = \App\Services\ReportKopService::make('REPORT ASET', $request->only([
            'dari', 'sampai', 'divisi', 'ruangan', 'status', 'kondisi',
        ]));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pages.laporan.aset.pdf',
            array_merge(['data' => $data], $kop, \App\Services\ReportKopService::pdfImages())
        )->setPaper('A4', 'landscape');

        return $pdf->stream('report-aset-'.now()->format('Ymd-His').'.pdf');
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            $kop = \App\Services\ReportKopService::make('REPORT ASET', request()->only([
                'dari', 'sampai', 'divisi', 'ruangan', 'status', 'kondisi',
            ]));
            echo view('pages.laporan.aset.excel-header', array_merge(compact('dari', 'sampai'), $kop))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.aset.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.aset.excel-footer')->render();
        }, 'report-aset-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = Aset::with(['hasKategori', 'hasLokasi', 'hasDepartment'])->orderBy('aset_tanggal_perolehan');

        if ($request->filled('dari') || $request->filled('sampai')) {
            // Aset tanpa Tgl Perolehan tetap tampil (tracking kode lama/baru tidak boleh hilang kesaring).
            $query->where(function ($q) use ($request) {
                $q->whereNull('aset_tanggal_perolehan');
                $q->orWhere(function ($w) use ($request) {
                    if ($request->filled('dari')) {
                        $w->whereDate('aset_tanggal_perolehan', '>=', $request->input('dari'));
                    }
                    if ($request->filled('sampai')) {
                        $w->whereDate('aset_tanggal_perolehan', '<=', $request->input('sampai'));
                    }
                });
            });
        }
        if ($request->filled('status')) {
            $query->where('aset_status', $request->input('status'));
        }
        if ($request->filled('kondisi')) {
            $query->where('aset_kondisi', $request->input('kondisi'));
        }
        if ($request->filled('divisi')) {
            $query->where('aset_id_department', $request->input('divisi'));
        }
        if ($request->filled('ruangan')) {
            $query->where('aset_id_lokasi', $request->input('ruangan'));
        }

        return $query;
    }
}
