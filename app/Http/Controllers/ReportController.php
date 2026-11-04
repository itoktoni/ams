<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\BukuPenyusutan;
use App\Models\Opname;
use App\Models\Peminjaman;
use App\Models\PenjualanAset;
use App\Models\PergerakanStok;
use App\Models\PermintaanSukuCadang;
use App\Models\Perpindahan;
use App\Models\Penghapusan;
use App\Models\Persetujuan;
use App\Models\PesananPembelian;
use App\Models\RiwayatService;
use App\Models\SukuCadang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

// Pusat laporan: 1 controller + 1 view generik + 1 template PDF.
// Tambah report baru = 1 entri di definitions() + 1 method getX() 1 baris + 1 menu.
class ReportController extends Controller
{
    public function getIndex()
    {
        $cards = [];
        foreach ($this->definitions() as $key => $def) {
            $cards[] = [
                'title' => $def['title'], 'desc' => $def['desc'] ?? '',
                'icon' => $def['icon'] ?? 'assessment',
                'url' => route('laporan.get'.ucfirst($key)),
                'count' => $def['count'] ? (int) $def['count']() : null,
            ];
        }

        return view('pages.laporan.index', compact('cards'));
    }

    // ── 1 method per report (dipakai Route::auto → /laporan/{nama}) ──
    public function getAset(Request $request) { return $this->render($request, 'aset'); }
    public function getPenyusutan(Request $request) { return $this->render($request, 'penyusutan'); }
    public function getPeminjaman(Request $request) { return $this->render($request, 'peminjaman'); }
    public function getPerpindahan(Request $request) { return $this->render($request, 'perpindahan'); }
    public function getOpname(Request $request) { return $this->render($request, 'opname'); }
    public function getPenghapusan(Request $request) { return $this->render($request, 'penghapusan'); }
    public function getPersetujuan(Request $request) { return $this->render($request, 'persetujuan'); }
    public function getPurchase(Request $request) { return $this->render($request, 'purchase'); }
    public function getSukucadang(Request $request) { return $this->render($request, 'sukucadang'); }
    public function getPergerakan(Request $request) { return $this->render($request, 'pergerakan'); }
    public function getPermintaan(Request $request) { return $this->render($request, 'permintaan'); }
    public function getService(Request $request) { return $this->render($request, 'service'); }
    public function getPenjualan(Request $request) { return $this->render($request, 'penjualan'); }

    private function render(Request $request, string $key)
    {
        $defs = $this->definitions();
        abort_if(! isset($defs[$key]), 404);
        $def = $defs[$key];

        $query = $def['query']();

        $periode = null;
        if (! empty($def['periode'])) {
            $periode = $request->input('periode', now()->format('Y-m'));
            if (! preg_match('/^\d{4}-\d{2}$/', (string) $periode)) {
                $periode = now()->format('Y-m');
            }
            [$th, $bl] = explode('-', $periode);
            $query->whereYear($def['periode'], $th)->whereMonth($def['periode'], $bl);
        }

        $activeFilters = [];
        foreach ($def['selects'] ?? [] as $sel) {
            $val = $request->input($sel['param']);
            if ($val !== null && $val !== '') {
                $query->where($sel['column'], $val);
                $activeFilters[$sel['param']] = $val;
            }
        }

        $data = $query->get();
        $aggs = collect($def['aggregates'] ?? [])->map(fn ($a) => [
            'label' => $a[0], 'value' => $a[1]($data), 'color' => $a[2] ?? 'bg-surface-container-low border-outline-variant/40',
        ]);
        $rows = $data->map(fn ($r) => collect($def['columns'])->map(fn ($c) => $c[1]($r))->all())->all();
        $headers = collect($def['columns'])->map(fn ($c) => $c[0])->all();

        $routeName = 'laporan.get'.ucfirst($key);
        $queryParams = array_merge(['periode' => $periode], $activeFilters);

        if ($request->input('export') === 'csv') {
            $filename = 'report-'.$key.'-'.($periode ?? 'all').'.csv';

            return response()->stream(function () use ($headers, $rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $headers);
                foreach ($rows as $row) {
                    fputcsv($out, array_map(fn ($v) => strip_tags((string) $v), $row));
                }
                fclose($out);
            }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"']);
        }

        if ($request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('pdf.laporan', [
                'title' => $def['title'], 'periode' => $periode,
                'headers' => $headers, 'rows' => $rows, 'aggs' => $aggs,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('report-'.$key.'-'.($periode ?? 'all').'.pdf');
        }

        return view('pages.laporan.show', [
            'title' => $def['title'], 'routeName' => $routeName,
            'periode' => $periode, 'hasPeriode' => ! empty($def['periode']),
            'selects' => $def['selects'] ?? [], 'activeFilters' => $activeFilters,
            'aggs' => $aggs, 'headers' => $headers, 'rows' => $rows,
            'queryParams' => $queryParams,
        ]);
    }

    private function definitions(): array
    {
        return [
            'aset' => [
                'title' => 'Report Aset', 'icon' => 'precision_manufacturing',
                'desc' => 'Daftar aset + nilai perolehan per periode',
                'count' => fn () => Aset::count(),
                'query' => fn () => Aset::with(['hasKategori', 'hasLokasi'])->orderBy('aset_tanggal_perolehan'),
                'periode' => 'aset_tanggal_perolehan',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'aset_status', 'options' => \App\Enums\Aset\StatusAsetEnum::getOptions()],
                    ['param' => 'kondisi', 'label' => 'Kondisi', 'column' => 'aset_kondisi', 'options' => \App\Enums\Aset\KondisiAsetEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Aset', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Nilai Perolehan', fn ($d) => formatRupiah($d->sum('aset_harga_perolehan'), false), 'bg-blue-50 border-blue-200'],
                    ['Aktif', fn ($d) => $d->where('aset_status', 'aktif')->count(), 'bg-emerald-50 border-emerald-200'],
                    ['Rusak', fn ($d) => $d->where('aset_kondisi', 'rusak')->count(), 'bg-red-50 border-red-200'],
                ],
                'columns' => [
                    ['Kode', fn ($r) => $r->aset_kode],
                    ['Nama', fn ($r) => $r->aset_nama],
                    ['Merek / Model', fn ($r) => trim(($r->aset_merek ?? '-').' '.$r->aset_model)],
                    ['Kategori', fn ($r) => $r->hasKategori?->aset_kategori_nama ?? '-'],
                    ['Lokasi', fn ($r) => $r->hasLokasi?->aset_lokasi_nama ?? '-'],
                    ['Tgl Perolehan', fn ($r) => $r->aset_tanggal_perolehan ? formatDate($r->aset_tanggal_perolehan) : '-'],
                    ['Harga', fn ($r) => formatRupiah($r->aset_harga_perolehan, false)],
                    ['Status', fn ($r) => $r->aset_status ?? '-'],
                    ['Kondisi', fn ($r) => $r->aset_kondisi ?? '-'],
                ],
            ],
            'penyusutan' => [
                'title' => 'Report Penyusutan', 'icon' => 'trending_down',
                'desc' => 'Beban penyusutan + nilai buku per periode',
                'count' => fn () => BukuPenyusutan::count(),
                'query' => fn () => BukuPenyusutan::with('hasAset')->orderBy('buku_penyusutan_periode'),
                'periode' => 'buku_penyusutan_tanggal',
                'aggregates' => [
                    ['Baris Jurnal', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Beban Periode', fn ($d) => formatRupiah($d->sum('buku_penyusutan_nilai'), false), 'bg-amber-50 border-amber-200'],
                    ['Nilai Buku Akhir', fn ($d) => formatRupiah($d->where('buku_penyusutan_periode', $d->max('buku_penyusutan_periode'))->sum('buku_penyusutan_nilai_buku'), false), 'bg-blue-50 border-blue-200'],
                ],
                'columns' => [
                    ['Periode', fn ($r) => $r->buku_penyusutan_periode],
                    ['Aset', fn ($r) => ($r->hasAset?->aset_nama ?? '#'.$r->buku_penyusutan_id_aset).' ('.($r->hasAset?->aset_kode ?? '-').')'],
                    ['Beban Bulan', fn ($r) => formatRupiah($r->buku_penyusutan_nilai, false)],
                    ['Akumulasi', fn ($r) => formatRupiah($r->buku_penyusutan_akumulasi, false)],
                    ['Nilai Buku', fn ($r) => formatRupiah($r->buku_penyusutan_nilai_buku, false)],
                ],
            ],
            'peminjaman' => [
                'title' => 'Report Peminjaman', 'icon' => 'swap_horiz',
                'desc' => 'Peminjaman aset + denda per periode',
                'count' => fn () => Peminjaman::count(),
                'query' => fn () => Peminjaman::with(['hasAset', 'hasPeminjam'])->orderBy('peminjaman_tanggal_pinjam'),
                'periode' => 'peminjaman_tanggal_pinjam',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'peminjaman_status', 'options' => \App\Enums\StatusPeminjamanEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Pinjam', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Berjalan', fn ($d) => $d->whereIn('peminjaman_status', ['aktif', 'berjalan'])->count(), 'bg-blue-50 border-blue-200'],
                    ['Terlambat', fn ($d) => $d->where('peminjaman_status', 'terlambat')->count(), 'bg-red-50 border-red-200'],
                    ['Total Denda', fn ($d) => formatRupiah($d->sum('peminjaman_denda'), false), 'bg-amber-50 border-amber-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->peminjaman_nomor],
                    ['Aset', fn ($r) => ($r->hasAset?->aset_nama ?? '-').' ('.($r->hasAset?->aset_kode ?? '-').')'],
                    ['Peminjam', fn ($r) => $r->hasPeminjam?->name ?? '-'],
                    ['Tujuan', fn ($r) => \Illuminate\Support\Str::limit($r->peminjaman_tujuan ?? '-', 40)],
                    ['Tgl Pinjam', fn ($r) => formatDate($r->peminjaman_tanggal_pinjam)],
                    ['Jatuh Tempo', fn ($r) => formatDate($r->peminjaman_jatuh_tempo)],
                    ['Kembali', fn ($r) => $r->peminjaman_tanggal_kembali ? formatDate($r->peminjaman_tanggal_kembali) : '-'],
                    ['Status', fn ($r) => $r->peminjaman_status],
                    ['Denda', fn ($r) => formatRupiah($r->peminjaman_denda, false)],
                ],
            ],
            'perpindahan' => [
                'title' => 'Report Perpindahan Barang', 'icon' => 'local_shipping',
                'desc' => 'Mutasi lokasi aset per periode',
                'count' => fn () => Perpindahan::count(),
                'query' => fn () => Perpindahan::with(['hasAset', 'hasLokasiAsal', 'hasLokasiTujuan'])->orderBy('perpindahan_tanggal_request'),
                'periode' => 'perpindahan_tanggal_request',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'perpindahan_status', 'options' => \App\Enums\Perpindahan\StatusPerpindahanEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Pindah', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Disetujui', fn ($d) => $d->where('perpindahan_status', 'disetujui')->count(), 'bg-emerald-50 border-emerald-200'],
                    ['Diajukan', fn ($d) => $d->where('perpindahan_status', 'diajukan')->count(), 'bg-amber-50 border-amber-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->perpindahan_nomor],
                    ['Aset', fn ($r) => ($r->hasAset?->aset_nama ?? '-').' ('.($r->hasAset?->aset_kode ?? '-').')'],
                    ['Asal', fn ($r) => $r->hasLokasiAsal?->aset_lokasi_nama ?? '-'],
                    ['Tujuan', fn ($r) => $r->hasLokasiTujuan?->aset_lokasi_nama ?? '-'],
                    ['Tgl Request', fn ($r) => formatDate($r->perpindahan_tanggal_request)],
                    ['Status', fn ($r) => $r->perpindahan_status],
                ],
            ],
            'opname' => [
                'title' => 'Report Opname', 'icon' => 'qr_code_scanner',
                'desc' => 'Hasil stock opname per lokasi',
                'count' => fn () => Opname::count(),
                'query' => fn () => Opname::with(['hasLokasi', 'hasPetugas'])->orderBy('opname_tanggal'),
                'periode' => 'opname_tanggal',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'opname_status', 'options' => \App\Enums\Opname\StatusOpnameEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Opname', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Total Sistem', fn ($d) => (int) $d->sum('opname_total_sistem'), 'bg-blue-50 border-blue-200'],
                    ['Ditemukan', fn ($d) => (int) $d->sum('opname_total_fisik'), 'bg-emerald-50 border-emerald-200'],
                    ['Selisih', fn ($d) => (int) $d->sum('opname_total_selisih'), 'bg-red-50 border-red-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->opname_nomor],
                    ['Lokasi', fn ($r) => $r->hasLokasi?->aset_lokasi_nama ?? '-'],
                    ['Tanggal', fn ($r) => formatDate($r->opname_tanggal)],
                    ['Petugas', fn ($r) => $r->hasPetugas?->name ?? '-'],
                    ['Sistem', fn ($r) => $r->opname_total_sistem],
                    ['Fisik', fn ($r) => $r->opname_total_fisik],
                    ['Selisih', fn ($r) => $r->opname_total_selisih],
                    ['Status', fn ($r) => $r->opname_status],
                ],
            ],
            'penghapusan' => [
                'title' => 'Report Penghapusan', 'icon' => 'delete_forever',
                'desc' => 'Aset dihapus + nilai buku per periode',
                'count' => fn () => Penghapusan::count(),
                'query' => fn () => Penghapusan::with('hasAset')->orderBy('penghapusan_tanggal_request'),
                'periode' => 'penghapusan_tanggal_request',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'penghapusan_status', 'options' => \App\Enums\Penghapusan\StatusPenghapusanEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Hapus', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Nilai Buku', fn ($d) => formatRupiah($d->sum('penghapusan_nilai_buku'), false), 'bg-red-50 border-red-200'],
                    ['Disetujui', fn ($d) => $d->where('penghapusan_status', 'disetujui')->count(), 'bg-emerald-50 border-emerald-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->penghapusan_nomor],
                    ['Aset', fn ($r) => ($r->hasAset?->aset_nama ?? '-').' ('.($r->hasAset?->aset_kode ?? '-').')'],
                    ['Alasan', fn ($r) => \Illuminate\Support\Str::limit($r->penghapusan_alasan ?? '-', 40)],
                    ['Tgl Request', fn ($r) => formatDate($r->penghapusan_tanggal_request)],
                    ['Nilai Buku', fn ($r) => formatRupiah($r->penghapusan_nilai_buku, false)],
                    ['Triase', fn ($r) => $r->penghapusan_triase ?? '-'],
                    ['Status', fn ($r) => $r->penghapusan_status],
                ],
            ],
            'persetujuan' => [
                'title' => 'Report Persetujuan', 'icon' => 'how_to_reg',
                'desc' => 'Approval perpindahan/penghapusan/PO',
                'count' => fn () => Persetujuan::count(),
                'query' => fn () => Persetujuan::with('hasUser')->orderByDesc('created_at'),
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'persetujuan_status', 'options' => \App\Enums\Persetujuan\StatusPersetujuanEnum::getOptions()],
                    ['param' => 'level', 'label' => 'Level', 'column' => 'persetujuan_level', 'options' => \App\Enums\Persetujuan\LevelPersetujuanEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Menunggu', fn ($d) => $d->where('persetujuan_status', 'menunggu')->count(), 'bg-amber-50 border-amber-200'],
                    ['Disetujui', fn ($d) => $d->where('persetujuan_status', 'disetujui')->count(), 'bg-emerald-50 border-emerald-200'],
                ],
                'columns' => [
                    ['Modul', fn ($r) => $r->persetujuan_modul],
                    ['Ref ID', fn ($r) => $r->persetujuan_id_referensi],
                    ['Level', fn ($r) => $r->persetujuan_level],
                    ['Approver', fn ($r) => $r->hasUser?->name ?? '-'],
                    ['Status', fn ($r) => $r->persetujuan_status],
                    ['Catatan', fn ($r) => \Illuminate\Support\Str::limit($r->persetujuan_catatan ?? '-', 40)],
                ],
            ],
            'purchase' => [
                'title' => 'Report Purchase (PO)', 'icon' => 'receipt_long',
                'desc' => 'Purchase order + penerimaan + faktur',
                'count' => fn () => PesananPembelian::count(),
                'query' => fn () => PesananPembelian::with(['hasVendor', 'hasItem'])->orderBy('pesanan_pembelian_tanggal'),
                'periode' => 'pesanan_pembelian_tanggal',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'pesanan_pembelian_status', 'options' => \App\Enums\Pesanan\StatusPesananEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total PO', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Nilai PO', fn ($d) => formatRupiah($d->sum('pesanan_pembelian_total'), false), 'bg-blue-50 border-blue-200'],
                    ['Diterima', fn ($d) => $d->where('pesanan_pembelian_status', 'diterima')->count(), 'bg-emerald-50 border-emerald-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->pesanan_pembelian_nomor],
                    ['Vendor', fn ($r) => $r->hasVendor?->vendor_nama ?? '-'],
                    ['Tanggal', fn ($r) => formatDate($r->pesanan_pembelian_tanggal)],
                    ['Tipe', fn ($r) => $r->pesanan_pembelian_tipe],
                    ['Item', fn ($r) => $r->hasItem->count().' item'],
                    ['Total', fn ($r) => formatRupiah($r->pesanan_pembelian_total, false)],
                    ['Status', fn ($r) => $r->pesanan_pembelian_status],
                ],
            ],
            'sukucadang' => [
                'title' => 'Report Suku Cadang', 'icon' => 'build',
                'desc' => 'Master + posisi stok vs minimum',
                'count' => fn () => SukuCadang::count(),
                'query' => function () {
                    $stok = \App\Models\StokSukuCadang::selectRaw('stok_suku_cadang_id_suku_cadang, SUM(stok_suku_cadang_jumlah) jml')->groupBy('stok_suku_cadang_id_suku_cadang')->pluck('jml', 'stok_suku_cadang_id_suku_cadang');
                    $q = SukuCadang::with(['hasGudang', 'hasVendor'])->orderBy('suku_cadang_nama');
                    // tempel stok via map setelah get — pakai closure di columns lewat static cache
                    app()->instance('report.stokmap', $stok);

                    return $q;
                },
                'aggregates' => [
                    ['Total Item', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Stok Menipis', fn ($d) => $d->filter(fn ($r) => (float) (app('report.stokmap')[$r->suku_cadang_id] ?? 0) < (float) $r->suku_cadang_stok_minimum)->count(), 'bg-red-50 border-red-200'],
                    ['Nilai Stok', fn ($d) => formatRupiah($d->sum(fn ($r) => (float) (app('report.stokmap')[$r->suku_cadang_id] ?? 0) * (float) $r->suku_cadang_harga), false), 'bg-blue-50 border-blue-200'],
                ],
                'columns' => [
                    ['Kode', fn ($r) => $r->suku_cadang_kode],
                    ['Nama', fn ($r) => $r->suku_cadang_nama],
                    ['Gudang', fn ($r) => $r->hasGudang?->gudang_nama ?? '-'],
                    ['Harga', fn ($r) => formatRupiah($r->suku_cadang_harga, false)],
                    ['Stok', fn ($r) => formatQty(app('report.stokmap')[$r->suku_cadang_id] ?? 0)],
                    ['Min', fn ($r) => formatQty($r->suku_cadang_stok_minimum)],
                    ['Status', fn ($r) => ((float) (app('report.stokmap')[$r->suku_cadang_id] ?? 0) < (float) $r->suku_cadang_stok_minimum) ? 'MENIPIS' : 'Aman'],
                ],
            ],
            'pergerakan' => [
                'title' => 'Report Pergerakan Stok', 'icon' => 'sync',
                'desc' => 'Mutasi masuk/keluar suku cadang',
                'count' => fn () => PergerakanStok::count(),
                'query' => fn () => PergerakanStok::with(['hasSukuCadang', 'hasGudang'])->orderBy('created_at'),
                'periode' => 'created_at',
                'selects' => [
                    ['param' => 'tipe', 'label' => 'Tipe', 'column' => 'pergerakan_stok_tipe', 'options' => \App\Enums\Stok\TipePergerakanStokEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Baris', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Qty Masuk', fn ($d) => formatQty($d->where('pergerakan_stok_tipe', 'masuk')->sum('pergerakan_stok_jumlah')), 'bg-emerald-50 border-emerald-200'],
                    ['Qty Keluar', fn ($d) => formatQty($d->where('pergerakan_stok_tipe', 'keluar')->sum('pergerakan_stok_jumlah')), 'bg-red-50 border-red-200'],
                ],
                'columns' => [
                    ['Tanggal', fn ($r) => formatDate($r->created_at)],
                    ['Sparepart', fn ($r) => ($r->hasSukuCadang?->suku_cadang_nama ?? '-').' ('.($r->hasSukuCadang?->suku_cadang_kode ?? '-').')'],
                    ['Gudang', fn ($r) => $r->hasGudang?->gudang_nama ?? '-'],
                    ['Tipe', fn ($r) => $r->pergerakan_stok_tipe ?? '-'],
                    ['Qty', fn ($r) => formatQty($r->pergerakan_stok_jumlah)],
                    ['Referensi', fn ($r) => $r->pergerakan_stok_referensi ?? '-'],
                ],
            ],
            'permintaan' => [
                'title' => 'Report Permintaan Suku Cadang', 'icon' => 'inventory_2',
                'desc' => 'Request + status approval + budget dept',
                'count' => fn () => PermintaanSukuCadang::count(),
                'query' => fn () => PermintaanSukuCadang::with(['hasSukuCadang', 'hasDepartment', 'hasPeminta'])->orderBy('permintaan_suku_cadang_tanggal_permintaan'),
                'periode' => 'permintaan_suku_cadang_tanggal_permintaan',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'permintaan_suku_cadang_status', 'options' => \App\Enums\SukuCadang\StatusPermintaanEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total Minta', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Nilai', fn ($d) => formatRupiah($d->sum('permintaan_suku_cadang_subtotal'), false), 'bg-blue-50 border-blue-200'],
                    ['Menunggu', fn ($d) => $d->where('permintaan_suku_cadang_status', 'menunggu')->count(), 'bg-amber-50 border-amber-200'],
                    ['Terpakai (budget)', fn ($d) => formatRupiah($d->whereIn('permintaan_suku_cadang_status', ['disetujui', 'sebagian', 'selesai'])->sum('permintaan_suku_cadang_subtotal'), false), 'bg-emerald-50 border-emerald-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->permintaan_suku_cadang_nomor],
                    ['Sparepart', fn ($r) => $r->hasSukuCadang?->suku_cadang_nama ?? '-'],
                    ['Peminta', fn ($r) => $r->hasPeminta?->name ?? '-'],
                    ['Dept', fn ($r) => $r->hasDepartment?->department_nama ?? '-'],
                    ['Qty', fn ($r) => formatQty($r->permintaan_suku_cadang_jumlah)],
                    ['Subtotal', fn ($r) => formatRupiah($r->permintaan_suku_cadang_subtotal, false)],
                    ['Tanggal', fn ($r) => $r->permintaan_suku_cadang_tanggal_permintaan ? formatDate($r->permintaan_suku_cadang_tanggal_permintaan) : '-'],
                    ['Status', fn ($r) => $r->permintaan_suku_cadang_status],
                ],
            ],
            'service' => [
                'title' => 'Report Service', 'icon' => 'history',
                'desc' => 'Riwayat service + biaya teknisi',
                'count' => fn () => RiwayatService::count(),
                'query' => fn () => RiwayatService::with(['hasAset', 'hasTeknisi'])->orderBy('riwayat_service_tanggal'),
                'periode' => 'riwayat_service_tanggal',
                'aggregates' => [
                    ['Total Service', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Total Biaya', fn ($d) => formatRupiah($d->sum('riwayat_service_biaya'), false), 'bg-blue-50 border-blue-200'],
                ],
                'columns' => [
                    ['Tanggal', fn ($r) => formatDate($r->riwayat_service_tanggal)],
                    ['Aset', fn ($r) => ($r->hasAset?->aset_nama ?? '-').' ('.($r->hasAset?->aset_kode ?? '-').')'],
                    ['Teknisi', fn ($r) => $r->hasTeknisi?->name ?? '-'],
                    ['Jenis', fn ($r) => $r->riwayat_service_jenis],
                    ['Biaya', fn ($r) => formatRupiah($r->riwayat_service_biaya, false)],
                    ['Catatan', fn ($r) => \Illuminate\Support\Str::limit($r->riwayat_service_catatan ?? '-', 50)],
                ],
            ],
            'penjualan' => [
                'title' => 'Report Penjualan Aset', 'icon' => 'sell',
                'desc' => 'Lelang/penjualan + penawaran masuk',
                'count' => fn () => PenjualanAset::count(),
                'query' => fn () => PenjualanAset::with(['hasAset', 'hasPenawaran'])->orderBy('penjualan_aset_tanggal_request'),
                'periode' => 'penjualan_aset_tanggal_request',
                'selects' => [
                    ['param' => 'status', 'label' => 'Status', 'column' => 'penjualan_aset_status', 'options' => \App\Enums\Penjualan\StatusPenjualanEnum::getOptions()],
                ],
                'aggregates' => [
                    ['Total', fn ($d) => $d->count(), 'bg-surface-container-low border-outline-variant/40'],
                    ['Nilai Buku', fn ($d) => formatRupiah($d->sum('penjualan_aset_nilai_buku'), false), 'bg-amber-50 border-amber-200'],
                    ['Appraisal', fn ($d) => formatRupiah($d->sum('penjualan_aset_harga_appraisal'), false), 'bg-blue-50 border-blue-200'],
                ],
                'columns' => [
                    ['Nomor', fn ($r) => $r->penjualan_aset_nomor],
                    ['Aset', fn ($r) => ($r->hasAset?->aset_nama ?? '-').' ('.($r->hasAset?->aset_kode ?? '-').')'],
                    ['Tgl Request', fn ($r) => formatDate($r->penjualan_aset_tanggal_request)],
                    ['Nilai Buku', fn ($r) => formatRupiah($r->penjualan_aset_nilai_buku, false)],
                    ['Appraisal', fn ($r) => formatRupiah($r->penjualan_aset_harga_appraisal, false)],
                    ['Penawaran', fn ($r) => $r->hasPenawaran->count().' ('.formatRupiah($r->hasPenawaran->max('penawaran_penjualan_harga') ?? 0, false).' max)'],
                    ['Status', fn ($r) => $r->penjualan_aset_status],
                ],
            ],
        ];
    }
}
