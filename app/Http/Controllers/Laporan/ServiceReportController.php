<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\RiwayatService;
use Illuminate\Http\Request;

/**
 * Report Service — riwayat service + biaya teknisi.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class ServiceReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.service.table');
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-service.getExportExcel', $request->query());
        }

        return view('pages.laporan.service.print', [
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
            echo view('pages.laporan.service.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.service.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.service.excel-footer')->render();
        }, 'report-service-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = RiwayatService::with(['hasAset', 'hasTeknisi'])->orderBy('riwayat_service_tanggal');

        if ($request->filled('dari')) {
            $query->whereDate('riwayat_service_tanggal', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('riwayat_service_tanggal', '<=', $request->input('sampai'));
        }

        return $query;
    }
}
