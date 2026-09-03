<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
  .header { text-align: center; border-bottom: 3px double #111; padding-bottom: 12px; margin-bottom: 18px; }
  .header h1 { font-size: 15px; margin: 0; letter-spacing: 0.06em; }
  .header p { font-size: 10px; color: #555; margin: 4px 0 0; }
  .meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  .meta td { padding: 4px 6px; vertical-align: top; }
  .meta td:first-child { width: 170px; font-weight: bold; color: #333; }
  .box { border: 1px solid #bbb; border-radius: 6px; padding: 12px; margin-bottom: 12px; background: #fafafa; }
  .box h3 { font-size: 11px; margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #0a4a6b; }
  .grid { width: 100%; border-collapse: collapse; }
  .grid th, .grid td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 10px; }
  .grid th { background: #f0f0f0; font-weight: bold; }
  .sig { width: 100%; margin-top: 24px; border-collapse: collapse; }
  .sig td { width: 50%; text-align: center; vertical-align: top; }
  .sig .line { margin-top: 56px; border-top: 1px solid #111; width: 170px; margin-left: auto; margin-right: auto; padding-top: 4px; font-size: 10px; }
  .footer { text-align: center; font-size: 8px; color: #888; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
</style>
</head>
<body>
  <div class="header">
    <h1>BERITA ACARA PERPINDAHAN ASET</h1>
    <p>No: {{ $p->perpindahan_nomor }} — {{ formatDate($p->perpindahan_tanggal_request) }} — KIRO AMS</p>
  </div>

  <p>Pada hari <strong>{{ \Carbon\Carbon::parse($p->perpindahan_tanggal_request)->translatedFormat('l, d F Y') }}</strong>, telah disetujui perpindahan aset:</p>

  <table class="meta">
    <tr><td>Nomor Perpindahan</td><td>: <strong>{{ $p->perpindahan_nomor }}</strong> <span class="badge">{{ $p->perpindahan_status }}</span></td></tr>
    <tr><td>Aset</td><td>: <strong>{{ $aset->aset_kode }}</strong> — {{ $aset->aset_nama }} ({{ $aset->hasKategori?->aset_kategori_nama ?? '-' }})</td></tr>
    <tr><td>Asal → Tujuan</td><td>: {{ $asal }} → <strong>{{ $tujuan }}</strong></td></tr>
    <tr><td>Alasan / Catatan</td><td>: {{ $p->perpindahan_alasan ?? '-' }} {{ $p->perpindahan_catatan ? '— '.$p->perpindahan_catatan : '' }}</td></tr>
    <tr><td>Tanggal Kirim / Terima</td><td>: {{ $p->perpindahan_tanggal_kirim ? formatDate($p->perpindahan_tanggal_kirim, true) : '-' }} / {{ $p->perpindahan_tanggal_terima ? formatDate($p->perpindahan_tanggal_terima, true) : '-' }}</td></tr>
  </table>

  <div class="box">
    <h3>Detail Aset</h3>
    <table class="meta" style="margin-bottom:0">
      <tr><td>Merek / Model / SN</td><td>: {{ $aset->aset_merek ?? '-' }} / {{ $aset->aset_model ?? '-' }} / {{ $aset->aset_nomor_seri ?? '-' }}</td></tr>
      <tr><td>Harga / Sisa / Kondisi</td><td>: {{ formatRupiah($aset->aset_harga_perolehan) }} / {{ formatRupiah($aset->aset_nilai_sisa) }} / {{ $aset->aset_kondisi ?? '-' }}</td></tr>
    </table>
  </div>

  <p>Dengan ini aset dinyatakan telah berpindah lokasi sesuai persetujuan. Pihak terkait wajib mencatat & menjaga aset di lokasi tujuan.</p>

  <table class="sig">
    <tr>
      <td>
        Mengetahui / Menyetujui<br>
        <div class="line">{{ $petugas->name ?? 'Petugas' }}<br><span style="font-weight:normal;color:#555;">{{ $petugas->email ?? '' }}</span></div>
      </td>
      <td>
        Penerima / Penanggung Jawab<br>
        <div class="line">{{ $aset->hasPenanggungJawab?->name ?? '-' }}<br><span style="font-weight:normal;color:#555;">{{ $aset->hasPenanggungJawab?->email ?? '' }}</span></div>
      </td>
    </tr>
  </table>

  <div class="footer">
    KIRO AMS — {{ now()->format('d/m/Y H:i') }} — Perpindahan {{ $p->perpindahan_nomor }} — Hash {{ substr(hash('sha256', $p->perpindahan_id.'|'.$aset->aset_id.'|'.now()->format('Y-m-d')),0,16) }}
  </div>
</body>
</html>
