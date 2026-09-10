<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1e293b; line-height: 1.6; margin: 0; }
  .wrap { padding: 28px 32px; }
  .header { width: 100%; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 20px; }
  .header td { vertical-align: top; }
  .brand h1 { font-size: 18px; margin: 0; letter-spacing: 0.08em; color: #0f172a; font-weight: 700; }
  .brand p { font-size: 8.5px; margin: 3px 0 0; color: #94a3b8; letter-spacing: 0.06em; text-transform: uppercase; }
  .inv-title { text-align: right; }
  .inv-title h2 { font-size: 26px; margin: 0; color: #0f172a; font-weight: 300; letter-spacing: 0.06em; }
  .inv-title p { font-size: 9px; color: #64748b; margin: 4px 0 0; letter-spacing: 0.06em; }
  .meta-grid { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
  .meta-grid td { vertical-align: top; padding: 0 12px; }
  .meta-grid td:first-child { padding-left: 0; }
  .meta-grid td:last-child { padding-right: 0; }
  .box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 16px; background: #fff; }
  .box h3 { font-size: 8.5px; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; margin: 0 0 10px; font-weight: 700; }
  .box p { margin: 4px 0; font-size: 10.5px; }
  .box strong { color: #0f172a; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
  .items { width: 100%; border-collapse: collapse; margin-top: 6px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
  .items th { background: #f8fafc; color: #475569; font-size: 8.5px; letter-spacing: 0.07em; text-transform: uppercase; text-align: left; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-weight: 700; }
  .items th.num { text-align: right; }
  .items td { padding: 10px 12px; font-size: 10.5px; border-top: 1px solid #f1f5f9; }
  .items td.num { text-align: right; font-variant-numeric: tabular-nums; }
  .items tr:last-child td { border-bottom: none; }
  .totals { width: 100%; border-collapse: collapse; margin-top: 14px; }
  .totals td { padding: 5px 12px; font-size: 10.5px; }
  .totals td.label { text-align: right; color: #64748b; width: 75%; }
  .totals td.val { text-align: right; font-weight: 600; width: 25%; }
  .totals tr.total td { border-top: 1px solid #0f172a; padding-top: 10px; font-size: 12px; font-weight: 700; color: #0f172a; }
  .note { border-left: 2px solid #e2e8f0; padding: 10px 14px; margin-top: 18px; background: #f8fafc; border-radius: 0 6px 6px 0; }
  .note h4 { font-size: 8.5px; letter-spacing: 0.08em; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; }
  .note p { font-size: 10.5px; margin: 0; color: #334155; }
  .sig { width: 100%; margin-top: 36px; border-collapse: collapse; }
  .sig td { width: 33%; text-align: center; vertical-align: top; padding: 0 12px; }
  .sig .line { margin-top: 48px; border-top: 1px solid #cbd5e1; padding-top: 8px; }
  .sig .name { font-size: 10.5px; font-weight: 600; color: #0f172a; }
  .sig .role { font-size: 8.5px; color: #94a3b8; letter-spacing: 0.05em; text-transform: uppercase; margin-top: 2px; }
  .footer { text-align: center; font-size: 7.5px; color: #94a3b8; margin-top: 28px; padding-top: 12px; border-top: 1px solid #f1f5f9; letter-spacing: 0.04em; }
</style>
</head>
<body>
<div class="wrap">
  {{-- Header --}}
  <table class="header" style="width:100%">
    <tr>
      <td class="brand" style="width:60%">
        <h1>KIRO</h1>
        <p>Asset Management System</p>
        <p style="color:#475569; margin-top:6px; font-size:9px;">Jl. Contoh No. 123 · Jakarta · hello@kiro-ams.test</p>
      </td>
      <td class="inv-title" style="width:40%">
        <h2>INVOICE</h2>
        <p style="font-size:11px; color:#0f172a; font-weight:700; letter-spacing:0.04em;">{{ $pesanan->pesanan_pembelian_nomor }}</p>
        <p>{{ $pesanan->pesanan_pembelian_tanggal ? \Carbon\Carbon::parse($pesanan->pesanan_pembelian_tanggal)->translatedFormat('d F Y') : formatDate(now()) }} · <span class="badge">{{ $pesanan->pesanan_pembelian_status }}</span></p>
      </td>
    </tr>
  </table>

  {{-- Vendor + PO meta --}}
  <table class="meta-grid">
    <tr>
      <td style="width:50%">
        <div class="box">
          <h3>Vendor</h3>
          <p style="font-size:13px; font-weight:700; color:#0f172a;">{{ $pesanan->hasVendor?->vendor_nama ?? '-' }}</p>
          <p style="color:#64748b;">{{ $pesanan->hasVendor?->vendor_alamat ?? $pesanan->hasVendor?->vendor_email ?? '—' }}</p>
          <p style="margin-top:6px;">{{ $pesanan->hasVendor?->vendor_telepon ?? '-' }} &nbsp;·&nbsp; {{ $pesanan->hasVendor?->vendor_email ?? '-' }}</p>
        </div>
      </td>
      <td style="width:50%">
        <div class="box">
          <h3>Purchase Order</h3>
          <p><span style="color:#94a3b8;">No.</span> <strong>{{ $pesanan->pesanan_pembelian_nomor }}</strong></p>
          <p><span style="color:#94a3b8;">Tanggal</span> {{ $pesanan->pesanan_pembelian_tanggal ? formatDate($pesanan->pesanan_pembelian_tanggal) : '-' }} · Jatuh tempo {{ $pesanan->pesanan_pembelian_tanggal_kirim ? formatDate($pesanan->pesanan_pembelian_tanggal_kirim) : '-' }}</p>
          <p><span style="color:#94a3b8;">Department</span> {{ $pesanan->pesanan_pembelian_kode_budget ? (\App\Models\Department::find($pesanan->pesanan_pembelian_kode_budget)?->department_nama ?? $pesanan->pesanan_pembelian_kode_budget) : '-' }}</p>
          @if($faktur)
          <p><span style="color:#94a3b8;">Faktur</span> {{ $faktur->faktur_nomor }} · {{ $faktur->faktur_status }} · {{ $faktur->faktur_tanggal ? formatDate($faktur->faktur_tanggal) : '-' }}</p>
          @endif
        </div>
      </td>
    </tr>
  </table>

  {{-- Items --}}
  <table class="items">
    <thead>
      <tr>
        <th style="width:6%">#</th>
        <th style="width:42%">Barang</th>
        <th class="num" style="width:12%">Qty</th>
        <th class="num" style="width:12%">Diterima</th>
        <th class="num" style="width:14%">Harga</th>
        <th class="num" style="width:14%">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $idx => $it)
      <tr>
        <td style="text-align:center; color:#94a3b8;">{{ $idx+1 }}</td>
        <td><strong style="color:#0f172a;">{{ $it->pesanan_item_nama }}</strong></td>
        <td class="num">{{ number_format((float)$it->pesanan_item_jumlah, 0) }}</td>
        <td class="num" style="color:#0f172a; font-weight:600;">{{ number_format((float)($it->pesanan_item_diterima ?? 0), 0) }}</td>
        <td class="num">{{ formatRupiah($it->pesanan_item_harga) }}</td>
        <td class="num">{{ formatRupiah($it->pesanan_item_subtotal) }}</td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:20px;">Tidak ada item</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- Totals --}}
  <table class="totals">
    <tr>
      <td class="label">Subtotal</td>
      <td class="val">{{ formatRupiah($items->sum('pesanan_item_subtotal')) }}</td>
    </tr>
    <tr>
      <td class="label" style="color:#94a3b8;">Pajak / Biaya</td>
      <td class="val" style="color:#94a3b8;">—</td>
    </tr>
    <tr class="total">
      <td class="label">TOTAL</td>
      <td class="val">{{ formatRupiah($pesanan->pesanan_pembelian_total ?? $items->sum('pesanan_item_subtotal')) }}</td>
    </tr>
  </table>

  @if($pesanan->pesanan_pembelian_catatan)
  <div class="note">
    <h4>Catatan</h4>
    <p>{{ $pesanan->pesanan_pembelian_catatan }}</p>
  </div>
  @endif

  {{-- Signatures --}}
  <table class="sig">
    <tr>
      <td>
        <div class="line">
          <div class="name">{{ auth()->user()->name ?? 'Admin Gudang' }}</div>
          <div class="role">Dibuat oleh · Admin</div>
        </div>
      </td>
      <td>
        <div class="line">
          <div class="name">{{ $pesanan->hasVendor?->vendor_nama ?? 'Vendor' }}</div>
          <div class="role">Vendor / Supplier</div>
        </div>
      </td>
      <td>
        <div class="line">
          <div class="name">&nbsp;</div>
          <div class="role">Finance · Disetujui</div>
        </div>
      </td>
    </tr>
  </table>

  <div class="footer">
    KIRO AMS · Dokumen otomatis · {{ now()->format('d/m/Y H:i') }} · PO {{ $pesanan->pesanan_pembelian_nomor }} · Hash {{ substr(hash('sha256', $pesanan->pesanan_pembelian_id.'|'.$pesanan->pesanan_pembelian_nomor.'|'.now()->format('Y-m-d')),0,12) }}
  </div>
</div>
</body>
</html>
