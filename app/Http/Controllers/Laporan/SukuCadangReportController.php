<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\SukuCadang;
use Illuminate\Http\Request;

/**
 * Report Suku Cadang — master + posisi stok vs minimum.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class SukuCadangReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.sukucadang.table');
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-sukucadang.getExportExcel', $request->query());
        }

        return view('pages.laporan.sukucadang.print', [
            'data' => (clone $query)->get(),
            'stokmap' => $this->stokMap(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $stokmap = $this->stokMap();

        return response()->streamDownload(function () use ($query, $stokmap) {
            echo view('pages.laporan.sukucadang.excel-header')->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no, $stokmap) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.sukucadang.excel-rows', ['data' => $rows, 'stokmap' => $stokmap])->render();
                flush();
            });

            echo view('pages.laporan.sukucadang.excel-footer')->render();
        }, 'report-sukucadang-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function stokMap()
    {
        return \App\Models\StokSukuCadang::selectRaw('stok_suku_cadang_id_suku_cadang, SUM(stok_suku_cadang_jumlah) jml')
            ->groupBy('stok_suku_cadang_id_suku_cadang')
            ->pluck('jml', 'stok_suku_cadang_id_suku_cadang');
    }

    private function baseQuery(Request $request)
    {
        return SukuCadang::with(['hasGudang', 'hasVendor'])->orderBy('suku_cadang_nama');
    }
}
