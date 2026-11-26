<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Suku Cadang</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
        @php $stok = fn($id) => (float) ($stokmap[$id] ?? 0); @endphp
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT SUKUCADANG', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris'])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Gudang</th><th>Harga</th><th>Stok</th><th>Min</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->suku_cadang_kode }}</td><td>{{ $r->suku_cadang_nama }}</td><td>{{ $r->hasGudang?->gudang_nama ?? '-' }}</td><td>{{ formatRupiah($r->suku_cadang_harga, false) }}</td><td>{{ formatQty($stokmap[$r->suku_cadang_id] ?? 0) }}</td><td>{{ formatQty($r->suku_cadang_stok_minimum) }}</td><td>{{ ((float) ($stokmap[$r->suku_cadang_id] ?? 0) < (float) $r->suku_cadang_stok_minimum) ? 'MENIPIS' : 'Aman' }}</td></tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
