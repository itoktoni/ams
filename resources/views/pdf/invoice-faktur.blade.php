<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
  .header { text-align: center; border-bottom: 3px double #111; padding-bottom: 12px; margin-bottom: 18px; }
  .header h1 { font-size: 16px; margin: 0; letter-spacing: 0.06em; }
  .header p { font-size: 10px; color: #555; margin: 4px 0 0; }
  .meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  .meta td { padding: 4px 6px; vertical-align: top; }
  .meta td:first-child { width: 160px; font-weight: bold; color: #333; }
  .grid { width: 100%; border-collapse: collapse; }
  .grid th, .grid td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 10px; }
  .grid th { background: #f0f0f0; font-weight: bold; }
  .grid td.num { text-align: right; }
  .sig { width: 100%; margin-top: 24px; border-collapse: collapse; }
  .sig td { width: 50%; text-align: center; vertical-align: top; }
  .sig .line { margin-top: 56px; border-top: 1px solid #111; width: 170px; margin-left: auto; margin-right: auto; padding-top: 4px; font-size: 10px; }
  .footer { text-align: center; font-size: 8px; color: #888; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
</style>
</head>
<body>
  <div class="header">
    <h1>INVOICE / FAKTUR</h1>
    <p>No: {{ $f->faktur_nomor }} — {{ $f->faktur_tanggal ? formatDate($f->faktur_tanggal) : formatDate(now()) }} — KIRO AMS</p>
  </div>
  <table class="meta">
    <tr><td>Nomor Faktur</td><td>: <strong>{{ $f->faktur_nomor }}</strong> <span class="badge">{{ $f->faktur_status }}</span></td></tr>
    <tr><td>Tanggal</td><td>: {{ $f->faktur_tanggal ? formatDate($f->faktur_tanggal, true) : '-' }}</td></tr>
    <tr><td>Pesanan (PO)</td><td>: {{ $pesanan?->pesanan_pembelian_nomor ?? '-' }} — Vendor: {{ $pesanan?->hasVendor?->vendor_nama ?? '-' }}</td></tr>
    <tr><td>Total</td><td>: <strong>{{ formatRupiah($f->faktur_total) }}</strong></td></tr>
    <tr><td>Catatan</td><td>: {{ $f->faktur_catatan ?? '-' }}</td></tr>
  </table>
  @if($items->isNotEmpty())
  <table class="grid">
    <tr><th>#</th><th>Barang</th><th class="num">Jumlah</th><th class="num">Harga</th><th class="num">Subtotal</th><th class="num">Diterima</th></tr>
    @foreach($items as $idx => $it)
    <tr>
      <td>{{ $idx+1 }}</td>
      <td>{{ $it->pesanan_item_nama }}</td>
      <td class="num">{{ $it->pesanan_item_jumlah }}</td>
      <td class="num">{{ formatRupiah($it->pesanan_item_harga) }}</td>
      <td class="num">{{ formatRupiah($it->pesanan_item_subtotal) }}</td>
      <td class="num">{{ $it->pesanan_item_diterima ?? 0 }}</td>
    </tr>
    @endforeach
    <tr><th colspan="4" style="text-align:right">Total PO</th><th class="num">{{ formatRupiah($pesanan?->pesanan_pembelian_total ?? $items->sum('pesanan_item_subtotal')) }}</th><th class="num">{{ $items->sum('pesanan_item_diterima') }}</th></tr>
  </table>
  @endif
  <table class="sig">
    <tr>
      <td>Vendor<br><div class="line">{{ $pesanan?->hasVendor?->vendor_nama ?? 'Vendor' }}</div></td>
      <td>Finance<br><div class="line">{{ auth()->user()->name ?? 'Petugas' }}</div></td>
    </tr>
  </table>
  <div class="footer">KIRO AMS — {{ now()->format('d/m/Y H:i') }} — Faktur {{ $f->faktur_nomor }} — Hash {{ substr(hash('sha256', $f->faktur_id.'|'.now()->format('Y-m-d')),0,16) }}</div>
</body>
</html>
