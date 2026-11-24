<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\PermintaanSukuCadang;
use Illuminate\Http\Request;

/**
 * Report Permintaan Suku Cadang — request + status approval + budget dept.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PermintaanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.permintaan.table', [
            'statusOptions' => \App\Enums\SukuCadang\StatusPermintaanEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-permintaan.getExportExcel', $request->query());
        }

        return view('pages.laporan.permintaan.print', [
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\SukuCadang\StatusPermintaanEnum::getOptions(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.permintaan.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.permintaan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.permintaan.excel-footer')->render();
        }, 'report-permintaan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = PermintaanSukuCadang::with(['hasSukuCadang', 'hasDepartment', 'hasPeminta'])->orderBy('permintaan_suku_cadang_tanggal_permintaan');

        if ($request->filled('dari')) {
            $query->whereDate('permintaan_suku_cadang_tanggal_permintaan', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('permintaan_suku_cadang_tanggal_permintaan', '<=', $request->input('sampai'));
        }
        if ($request->filled('status')) {
            $query->where('permintaan_suku_cadang_status', $request->input('status'));
        }

        return $query;
    }
}
