<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Penjualan Aset</title>
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
@include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PENJUALAN', 'filterTitle' => $filterTitle ?? ''])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Nomor</th><th>Aset</th><th>Tgl Request</th><th>Nilai Buku</th><th>Appraisal</th><th>Penawaran</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->penjualan_aset_nomor }}</td><td>{{ ($r->hasAset?->aset_nama ?? '-') . ' (' . ($r->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ formatDate($r->penjualan_aset_tanggal_request) }}</td><td>{{ formatRupiah($r->penjualan_aset_nilai_buku, false) }}</td><td>{{ formatRupiah($r->penjualan_aset_harga_appraisal, false) }}</td><td>{{ $r->hasPenawaran->count() . ' (' . formatRupiah($r->hasPenawaran->max('penawaran_penjualan_harga') ?? 0, false) . ' max)' }}</td><td>{{ $r->penjualan_aset_status }}</td></tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="no-print">
        <button type="button" class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <button type="button" class="btn-copy" onclick="copyTsv()">Salin TSV (ke Google Sheet)</button>
        <a class="btn-excel" href="{{ route('laporan-penjualan.getExportExcel', request()->query()) }}">Export Excel</a>
        <a class="btn-print" style="background:#c62828;" target="_blank" href="{{ route('laporan-penjualan.getExportPdf', request()->query()) }}">Lihat PDF</a>
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
