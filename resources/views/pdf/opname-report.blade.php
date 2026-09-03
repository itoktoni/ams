<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
  .header { text-align: center; border-bottom: 3px double #111; padding-bottom: 10px; margin-bottom: 12px; }
  .header h1 { font-size: 14px; margin: 0; }
  .header p { font-size: 9px; color: #555; margin: 2px 0 0; }
  .grid { width: 100%; border-collapse: collapse; }
  .grid th, .grid td { border: 1px solid #999; padding: 4px 6px; text-align: left; font-size: 9px; }
  .grid th { background: #eee; font-weight: bold; }
  .badge-ok { background: #e8f5e9; color: #2e7d32; padding: 1px 6px; border-radius: 999px; font-size: 8px; }
  .badge-miss { background: #ffebee; color: #c62828; padding: 1px 6px; border-radius: 999px; font-size: 8px; }
  .footer { text-align: center; font-size: 7px; color: #888; margin-top: 12px; border-top: 1px solid #ddd; padding-top: 6px; }
</style>
</head>
<body>
  <div class="header">
    <h1>LAPORAN OPNAME — {{ $opname->opname_nomor }}</h1>
    <p>{{ $opname->hasLokasi?->aset_lokasi_nama ?? '-' }} • {{ formatDate($opname->opname_tanggal_mulai) }} s/d {{ formatDate($opname->opname_tanggal_selesai) }} • {{ $opname->opname_status }}</p>
    <p>Total {{ $details->count() }} | Ditemukan {{ $found->count() }} | Belum {{ $missing->count() }}</p>
  </div>

  <p><strong>Ditemukan</strong> (kapan ketemu) — {{ $found->count() }} item</p>
  <table class="grid">
    <tr><th>Aset</th><th>Kode</th><th>Waktu Scan</th><th>Petugas</th></tr>
    @foreach($found as $d)
    <tr><td>{{ $d->hasAset?->aset_nama ?? 'Aset #'.$d->opname_detail_id_aset }}</td><td>{{ $d->hasAset?->aset_kode ?? '-' }}</td><td>{{ $d->opname_detail_waktu_scan ? formatDate($d->opname_detail_waktu_scan, true) : '-' }}</td><td>{{ $d->hasPetugasScan?->name ?? '-' }}</td></tr>
    @endforeach
  </table>

  <p style="margin-top:12px"><strong>Belum Ditemukan / Kosong</strong> — {{ $missing->count() }} item</p>
  <table class="grid">
    <tr><th>Aset</th><th>Kode</th><th>Status Sistem</th><th>Status</th></tr>
    @foreach($missing as $d)
    <tr><td>{{ $d->hasAset?->aset_nama ?? 'Aset #'.$d->opname_detail_id_aset }}</td><td>{{ $d->hasAset?->aset_kode ?? '-' }}</td><td>{{ $d->opname_detail_status_sistem ?? '-' }}</td><td><span class="badge-miss">Belum</span></td></tr>
    @endforeach
  </table>

  <div class="footer">KIRO AMS — {{ now()->format('d/m/Y H:i') }} — Opname {{ $opname->opname_nomor }}</div>
</body>
</html>
