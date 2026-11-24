<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Perpindahan;
use Illuminate\Http\Request;

/**
 * Report Perpindahan Barang — mutasi lokasi aset per periode.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PerpindahanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.perpindahan.table', [
            'statusOptions' => \App\Enums\Perpindahan\StatusPerpindahanEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-perpindahan.getExportExcel', $request->query());
        }

        return view('pages.laporan.perpindahan.print', [
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\Perpindahan\StatusPerpindahanEnum::getOptions(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.perpindahan.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.perpindahan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.perpindahan.excel-footer')->render();
        }, 'report-perpindahan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = Perpindahan::with(['hasAset', 'hasLokasiAsal', 'hasLokasiTujuan'])->orderBy('perpindahan_tanggal_request');

        if ($request->filled('dari')) {
            $query->whereDate('perpindahan_tanggal_request', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('perpindahan_tanggal_request', '<=', $request->input('sampai'));
        }
        if ($request->filled('status')) {
            $query->where('perpindahan_status', $request->input('status'));
        }

        return $query;
    }
}
