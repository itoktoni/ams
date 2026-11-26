<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report BBM</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT BBM', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris', 'pdf' => true])
    @if(!empty($ringkasan['rows']))
    <h3>RINGKASAN KUOTA PER ORANG</h3>
    <table class="data">
        <thead><tr><th>No</th><th>Peminta</th><th>Unit</th><th>Periode</th><th>Kuota (L)</th><th>Terpakai (L)</th><th>Menunggu (L)</th><th>Sisa (L)</th></tr></thead>
        <tbody>
            @foreach($ringkasan['rows'] as $i => $row)
            <tr><td>{{ $i + 1 }}</td><td>{{ $row['user']?->name ?? '-' }}</td><td>{{ $row['user']?->unit_kode ?? '-' }}</td><td>{{ $row['periode'] }}</td><td>{{ formatQty($row['kuota']) }}</td><td>{{ formatQty($row['terpakai']) }}</td><td>{{ formatQty($row['pending']) }}</td><td>{{ formatQty($row['tersedia']) }}</td></tr>
            @endforeach
            <tr><td colspan="4" style="text-align:right;font-weight:bold;">TOTAL</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalKuota']) }}</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalTerpakai']) }}</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalPending']) }}</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalTersedia']) }}</td></tr>
        </tbody>
    </table>
    <br>
    @endif
    <h3>DETAIL PENGAJUAN</h3>
    <table class="data">
        <thead><tr><th>No</th><th>Nomor</th><th>Periode</th><th>Peminta</th><th>Unit</th><th>Kendaraan</th><th>Liter</th><th>Status</th><th>Tgl Pengajuan</th><th>Approver</th><th>Catatan</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            @php
                $kendaraan = ($jenisKendaraanOptions[$r->bbm_pengajuan_jenis_kendaraan] ?? $r->bbm_pengajuan_jenis_kendaraan ?? '-');
                if ($r->bbm_pengajuan_kendaraan) $kendaraan .= ' — '.$r->bbm_pengajuan_kendaraan;
            @endphp
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->bbm_pengajuan_nomor }}</td><td>{{ $r->bbm_pengajuan_periode }}</td><td>{{ $r->hasUser?->name ?? '-' }}</td><td>{{ $r->hasUser?->unit_kode ?? '-' }}</td><td>{{ $kendaraan }}</td><td>{{ formatQty($r->bbm_pengajuan_liter) }}</td><td>{{ $statusOptions[$r->bbm_pengajuan_status] ?? $r->bbm_pengajuan_status }}</td><td>{{ $r->bbm_pengajuan_tanggal ? formatDate($r->bbm_pengajuan_tanggal) : '-' }}</td><td>{{ $r->hasApprover?->name ?? '-' }}</td><td>{{ $r->bbm_pengajuan_catatan ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="11" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
