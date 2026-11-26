<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Purchase (PO)</title>
    <style>
        @page { margin: 110px 25px 75px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #111; margin: 0; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th, table.data td { border: 1px solid #999; padding: 3px 5px; }
        table.data th { background: #eee; }
    </style>
</head>
<body>
    @include('partials.report-kop', ['kop' => $kop ?? null, 'judul' => $judulReport ?? 'REPORT PURCHASE', 'filterTitle' => ($filterTitle ?? '').' • '.($data->count()).' baris'])
    <table class="data" id="reportTable">
        <thead><tr><th>No</th><th>Nomor</th><th>Vendor</th><th>Tanggal</th><th>Tipe</th><th>Item</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $r)
            <tr><td>{{ $i + 1 }}</td><td>{{ $r->pesanan_pembelian_nomor }}</td><td>{{ $r->hasVendor?->vendor_nama ?? '-' }}</td><td>{{ formatDate($r->pesanan_pembelian_tanggal) }}</td><td>{{ $r->pesanan_pembelian_tipe }}</td><td>{{ $r->hasItem->count() . ' item' }}</td><td>{{ formatRupiah($r->pesanan_pembelian_total, false) }}</td><td>{{ $r->pesanan_pembelian_status }}</td></tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.report-footer', ['footer' => $footer ?? null, 'pdf' => true])
</body>
</html>
