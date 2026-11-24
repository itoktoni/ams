<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\BukuPenyusutan;
use Illuminate\Http\Request;

/**
 * Report Penyusutan — beban penyusutan + nilai buku per periode.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PenyusutanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.penyusutan.table');
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-penyusutan.getExportExcel', $request->query());
        }

        return view('pages.laporan.penyusutan.print', [
            'data' => (clone $query)->get(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.penyusutan.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.penyusutan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.penyusutan.excel-footer')->render();
        }, 'report-penyusutan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = BukuPenyusutan::with('hasAset')->orderBy('buku_penyusutan_periode');

        if ($request->filled('dari')) {
            $query->whereDate('buku_penyusutan_tanggal', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('buku_penyusutan_tanggal', '<=', $request->input('sampai'));
        }

        return $query;
    }
}
