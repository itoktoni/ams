<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Penyusutan</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        h2 { margin: 0 0 2px; }
        .sub { color: #555; margin-bottom: 12px; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; white-space: nowrap; }
        table.data th { background: #eee; }
        .no-print { position: fixed; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; background: #fff; border: 1px solid #ccc; border-radius: 999px; padding: 8px 12px; box-shadow: 0 4px 16px rgba(0,0,0,.15); }
        .no-print button, .no-print a { border: 0; border-radius: 999px; padding: 8px 16px; font-size: 13px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print { background: #00236f; color: #fff; }
        .btn-copy { background: #15803d; color: #fff; }
        .btn-excel { background: #e6eeff; color: #00236f; }
        @media print { .no-print { display: none; } @page { size: landscape; } }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PENYUSUTAN', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Periode</th><th>Aset</th><th>Beban Bulan</th><th>Akumulasi</th><th>Nilai Buku</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->buku_penyusutan_periode }}</td><td>{{ ($r->hasAset?->aset_nama ?? '#' . $r->buku_penyusutan_id_aset) . ' (' . ($r->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ formatRupiah($r->buku_penyusutan_nilai, false) }}</td><td>{{ formatRupiah($r->buku_penyusutan_akumulasi, false) }}</td><td>{{ formatRupiah($r->buku_penyusutan_nilai_buku, false) }}</td></tr>
            @empty
            <tr><td colspan="6" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
