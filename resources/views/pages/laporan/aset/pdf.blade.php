<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Aset</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT ASET', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    <table class="data">
        <thead><tr><th>No</th><th>Kode</th><th>Kode Lama</th><th>No. Label</th><th>Nama</th><th>Merek / Model</th><th>Kategori</th><th>Ruangan</th><th>Divisi</th><th>Tgl Perolehan</th><th>Harga</th><th>Status</th><th>Kondisi</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            @php
                $divisi = $r->hasDepartment?->department_kode;
                if (!$divisi && $r->aset_nomor_label) {
                    $seg = explode('/', $r->aset_nomor_label);
                    $divisi = $seg[2] ?? null;
                }
            @endphp
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->aset_kode }}</td><td>{{ $r->aset_label_lama ?? '-' }}</td><td>{{ $r->aset_nomor_label ?? '-' }}</td><td>{{ $r->aset_nama }}</td><td>{{ trim(($r->aset_merek ?? '-') . ' ' . ($r->aset_model ?? '')) }}</td><td>{{ $r->hasKategori?->aset_kategori_nama ?? '-' }}</td><td>{{ $r->hasLokasi?->aset_lokasi_nama ?? '-' }}</td><td>{{ $divisi ?? '-' }}</td><td>{{ $r->aset_tanggal_perolehan ? formatDate($r->aset_tanggal_perolehan) : '-' }}</td><td>{{ formatRupiah($r->aset_harga_perolehan, false) }}</td><td>{{ $r->aset_status ?? '-' }}</td><td>{{ $r->aset_kondisi ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="13" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
