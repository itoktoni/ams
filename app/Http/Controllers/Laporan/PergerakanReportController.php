<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\PergerakanStok;
use Illuminate\Http\Request;

/**
 * Report Pergerakan Stok — mutasi masuk/keluar suku cadang.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PergerakanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.pergerakan.table', [
            'tipeOptions' => \App\Enums\Stok\TipePergerakanStokEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-pergerakan.getExportExcel', $request->query());
        }

        return view('pages.laporan.pergerakan.print', [
            'data' => (clone $query)->get(),
            'tipeOptions' => \App\Enums\Stok\TipePergerakanStokEnum::getOptions(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.pergerakan.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.pergerakan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.pergerakan.excel-footer')->render();
        }, 'report-pergerakan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = PergerakanStok::with(['hasSukuCadang', 'hasGudang'])->orderBy('created_at');

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->input('sampai'));
        }
        if ($request->filled('tipe')) {
            $query->where('pergerakan_stok_tipe', $request->input('tipe'));
        }

        return $query;
    }
}
