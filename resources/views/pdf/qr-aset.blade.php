<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; text-align: center; color: #111; }
  .wrap { border: 2px solid #111; border-radius: 12px; padding: 18px; margin: 12px; }
  h1 { font-size: 18px; margin: 0; }
  .kode { font-size: 11px; color: #555; margin-top: 4px; }
  .qr { margin: 16px auto; }
  .qr img { width: 280px; height: 280px; }
  .url { font-size: 9px; color: #666; word-break: break-all; margin-top: 10px; }
  .foot { font-size: 8px; color: #888; margin-top: 14px; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
  <div class="wrap">
    <h1>{{ $aset->aset_nama }}</h1>
    <div class="kode">{{ $aset->aset_kode }} • {{ $aset->aset_kode_qr }}</div>
    <div class="qr">
      @if(str_starts_with($qrDataUri, 'data:'))
        <img src="{{ $qrDataUri }}" alt="QR">
      @else
        <img src="{{ $qrDataUri }}" alt="QR">
      @endif
    </div>
    <div class="url">{{ $qrText }}</div>
    <div class="foot">Scan → detail aset + riwayat service • Tiket: {{ $aset->tiket_qr_url }} — KIRO AMS — {{ now()->format('d/m/Y H:i') }}</div>
  </div>
</body>
</html>
