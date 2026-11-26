<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Penghapusan</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PENGHAPUSAN', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Nomor</th><th>Aset</th><th>Alasan</th><th>Tgl Request</th><th>Nilai Buku</th><th>Triase</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->penghapusan_nomor }}</td><td>{{ ($r->hasAset?->aset_nama ?? '-') . ' (' . ($r->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ \Illuminate\Support\Str::limit($r->penghapusan_alasan ?? '-', 40) }}</td><td>{{ formatDate($r->penghapusan_tanggal_request) }}</td><td>{{ formatRupiah($r->penghapusan_nilai_buku, false) }}</td><td>{{ $r->penghapusan_triase ?? '-' }}</td><td>{{ $r->penghapusan_status }}</td></tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
