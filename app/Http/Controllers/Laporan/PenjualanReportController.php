<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\PenjualanAset;
use Illuminate\Http\Request;

/**
 * Report Penjualan Aset — lelang/penjualan + penawaran masuk.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PenjualanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.penjualan.table', [
            'statusOptions' => \App\Enums\Penjualan\StatusPenjualanEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-penjualan.getExportExcel', $request->query());
        }

        return view('pages.laporan.penjualan.print', [
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\Penjualan\StatusPenjualanEnum::getOptions(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.penjualan.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.penjualan.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.penjualan.excel-footer')->render();
        }, 'report-penjualan-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = PenjualanAset::with(['hasAset', 'hasPenawaran'])->orderBy('penjualan_aset_tanggal_request');

        if ($request->filled('dari')) {
            $query->whereDate('penjualan_aset_tanggal_request', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('penjualan_aset_tanggal_request', '<=', $request->input('sampai'));
        }
        if ($request->filled('status')) {
            $query->where('penjualan_aset_status', $request->input('status'));
        }

        return $query;
    }
}
