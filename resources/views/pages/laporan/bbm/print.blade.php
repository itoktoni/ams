<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report BBM {{ request('dari') }} - {{ request('sampai') }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; margin: 16px; }
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
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT BBM', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris'])
    @if(!empty($ringkasan['rows']))
    <h3 style="margin:12px 0 4px;">RINGKASAN KUOTA PER ORANG</h3>
    <table class="data" id="reportSummary">
        <thead><tr><th>No</th><th>Peminta</th><th>Unit</th><th>Periode</th><th>Kuota (L)</th><th>Terpakai (L)</th><th>Menunggu (L)</th><th>Sisa (L)</th></tr></thead>
        <tbody>
            @foreach($ringkasan['rows'] as $i => $row)
            <tr><td>{{ $i + 1 }}</td><td>{{ $row['user']?->name ?? '-' }}</td><td>{{ $row['user']?->unit_kode ?? '-' }}</td><td>{{ $row['periode'] }}</td><td>{{ formatQty($row['kuota']) }}</td><td>{{ formatQty($row['terpakai']) }}</td><td>{{ formatQty($row['pending']) }}</td><td>{{ formatQty($row['tersedia']) }}</td></tr>
            @endforeach
            <tr><td colspan="4" style="text-align:right;font-weight:bold;">TOTAL</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalKuota']) }}</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalTerpakai']) }}</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalPending']) }}</td><td style="font-weight:bold;">{{ formatQty($ringkasan['totalTersedia']) }}</td></tr>
        </tbody>
    </table>
    @endif
    <h3 style="margin:12px 0 4px;">DETAIL PENGAJUAN</h3>
    <table class="data" id="reportTable">
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
    <div class="no-print">
        <button type="button" class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <button type="button" class="btn-copy" onclick="copyTsv()">Salin TSV (ke Google Sheet)</button>
        <a class="btn-excel" href="{{ route('laporan-bbm.getExportExcel', request()->query()) }}">Export Excel</a>
        <a class="btn-print" style="background:#c62828;" href="{{ route('laporan-bbm.getExportPdf', request()->query()) }}">Download PDF</a>
    </div>
    @include('partials.report-footer', ['footer' => $footer ?? null])
    <script>
    function copyTsv() {
        const rows = [];
        document.querySelectorAll('#reportTable tr').forEach(tr => {
            const cells = [...tr.querySelectorAll('th, td')].map(td => td.innerText.trim().replace(/\t/g, ' '));
            rows.push(cells.join('\t'));
        });
        const text = rows.join('\n');
        const done = () => alert('Berhasil disalin: ' + (rows.length - 1) + ' baris. Paste ke Google Sheet.');
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done).catch(() => fallbackCopy(text, done));
        } else {
            fallbackCopy(text, done);
        }
    }
    function fallbackCopy(text, done) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        ta.setSelectionRange(0, ta.value.length);
        try {
            document.execCommand('copy');
            done();
        } catch (e) {
            alert('Gagal menyalin otomatis. Blokir tabel manual lalu Ctrl+C.');
        }
        document.body.removeChild(ta);
    }
    </script>
</body>
</html>
