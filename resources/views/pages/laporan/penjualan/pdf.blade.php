<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Penjualan</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PENJUALAN', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Nomor</th><th>Aset</th><th>Tgl Request</th><th>Nilai Buku</th><th>Appraisal</th><th>Penawaran</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->penjualan_aset_nomor }}</td><td>{{ ($r->hasAset?->aset_nama ?? '-') . ' (' . ($r->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ formatDate($r->penjualan_aset_tanggal_request) }}</td><td>{{ formatRupiah($r->penjualan_aset_nilai_buku, false) }}</td><td>{{ formatRupiah($r->penjualan_aset_harga_appraisal, false) }}</td><td>{{ $r->hasPenawaran->count() . ' (' . formatRupiah($r->hasPenawaran->max('penawaran_penjualan_harga') ?? 0, false) . ' max)' }}</td><td>{{ $r->penjualan_aset_status }}</td></tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
