<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Persetujuan</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PERSETUJUAN', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris'])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Modul</th><th>Ref ID</th><th>Level</th><th>Approver</th><th>Status</th><th>Catatan</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->persetujuan_modul }}</td><td>{{ $r->persetujuan_id_referensi }}</td><td>{{ $r->persetujuan_level }}</td><td>{{ $r->hasUser?->name ?? '-' }}</td><td>{{ $r->persetujuan_status }}</td><td>{{ \Illuminate\Support\Str::limit($r->persetujuan_catatan ?? '-', 40) }}</td></tr>
            @empty
            <tr><td colspan="7" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
