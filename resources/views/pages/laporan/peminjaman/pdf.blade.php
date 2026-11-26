<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Peminjaman</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PEMINJAMAN', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Nomor</th><th>Aset</th><th>Peminjam</th><th>Tujuan</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Kembali</th><th>Status</th><th>Denda</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->peminjaman_nomor }}</td><td>{{ ($r->hasAset?->aset_nama ?? '-') . ' (' . ($r->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ $r->hasPeminjam?->name ?? '-' }}</td><td>{{ \Illuminate\Support\Str::limit($r->peminjaman_tujuan ?? '-', 40) }}</td><td>{{ formatDate($r->peminjaman_tanggal_pinjam) }}</td><td>{{ formatDate($r->peminjaman_jatuh_tempo) }}</td><td>{{ $r->peminjaman_tanggal_kembali ? formatDate($r->peminjaman_tanggal_kembali) : '-' }}</td><td>{{ $r->peminjaman_status }}</td><td>{{ formatRupiah($r->peminjaman_denda, false) }}</td></tr>
            @empty
            <tr><td colspan="10" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
