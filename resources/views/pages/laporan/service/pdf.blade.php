<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Service</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT SERVICE', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris'])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Tanggal</th><th>Aset</th><th>Teknisi</th><th>Jenis</th><th>Biaya</th><th>Catatan</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ formatDate($r->riwayat_service_tanggal) }}</td><td>{{ ($r->hasAset?->aset_nama ?? '-') . ' (' . ($r->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ $r->hasTeknisi?->name ?? '-' }}</td><td>{{ $r->riwayat_service_jenis }}</td><td>{{ formatRupiah($r->riwayat_service_biaya, false) }}</td><td>{{ \Illuminate\Support\Str::limit($r->riwayat_service_catatan ?? '-', 50) }}</td></tr>
            @empty
            <tr><td colspan="7" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
