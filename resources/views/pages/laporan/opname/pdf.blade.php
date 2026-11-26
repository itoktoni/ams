<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Opname</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT OPNAME', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Nomor</th><th>Lokasi</th><th>Tanggal</th><th>Petugas</th><th>Sistem</th><th>Fisik</th><th>Selisih</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->opname_nomor }}</td><td>{{ $r->hasLokasi?->aset_lokasi_nama ?? '-' }}</td><td>{{ formatDate($r->opname_tanggal) }}</td><td>{{ $r->hasPetugas?->name ?? '-' }}</td><td>{{ $r->opname_total_sistem }}</td><td>{{ $r->opname_total_fisik }}</td><td>{{ $r->opname_total_selisih }}</td><td>{{ $r->opname_status }}</td></tr>
            @empty
            <tr><td colspan="9" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
