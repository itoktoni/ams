<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

/**
 * Report Peminjaman — peminjaman aset + denda per periode.
 * - getTable: form filter, hasil dibuka di tab baru (getPrint).
 * - getPrint: halaman print standalone (Cetak / Salin TSV / Export Excel).
 * - getExportExcel: streaming Excel (.xls via HTML table), chunk 1000.
 */
class PeminjamanReportController extends Controller
{
    public function getTable(Request $request)
    {
        return view('pages.laporan.peminjaman.table', [
            'statusOptions' => \App\Enums\StatusPeminjamanEnum::getOptions(),
        ]);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return redirect()->route('laporan-peminjaman.getExportExcel', $request->query());
        }

        return view('pages.laporan.peminjaman.print', [
            'data' => (clone $query)->get(),
            'statusOptions' => \App\Enums\StatusPeminjamanEnum::getOptions(),
        ]);
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        return response()->streamDownload(function () use ($query, $dari, $sampai) {
            echo view('pages.laporan.peminjaman.excel-header', compact('dari', 'sampai'))->render();

            $no = 0;
            $query->chunk(1000, function ($records) use (&$no) {
                $rows = [];
                foreach ($records as $r) {
                    $rows[] = ['no' => ++$no, 'r' => $r];
                }
                echo view('pages.laporan.peminjaman.excel-rows', ['data' => $rows])->render();
                flush();
            });

            echo view('pages.laporan.peminjaman.excel-footer')->render();
        }, 'report-peminjaman-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = Peminjaman::with(['hasAset', 'hasPeminjam'])->orderBy('peminjaman_tanggal_pinjam');

        if ($request->filled('dari')) {
            $query->whereDate('peminjaman_tanggal_pinjam', '>=', $request->input('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('peminjaman_tanggal_pinjam', '<=', $request->input('sampai'));
        }
        if ($request->filled('status')) {
            $query->where('peminjaman_status', $request->input('status'));
        }

        return $query;
    }
}
