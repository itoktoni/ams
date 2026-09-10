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
  .box h3 { font-size: 11px; margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #7a1a1a; }
  .grid { width: 100%; border-collapse: collapse; }
  .grid th, .grid td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 10px; }
  .grid th { background: #f0f0f0; font-weight: bold; }
  .sig { width: 100%; margin-top: 24px; border-collapse: collapse; }
  .sig td { width: 33%; text-align: center; vertical-align: top; }
  .sig .line { margin-top: 56px; border-top: 1px solid #111; width: 150px; margin-left: auto; margin-right: auto; padding-top: 4px; font-size: 10px; }
  .footer { text-align: center; font-size: 8px; color: #888; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; background: #fbe9e7; color: #b71c1c; border: 1px solid #ef9a9a; }
</style>
</head>
<body>
  <div class="header">
    <h1>BERITA ACARA PENGHAPUSAN ASET</h1>
    <p>No: {{ $p->penghapusan_nomor }} — {{ $p->penghapusan_tanggal_request ? formatDate($p->penghapusan_tanggal_request) : formatDate(now()) }} — KIRO AMS</p>
  </div>

  <p>Pada hari <strong>{{ \Carbon\Carbon::parse($p->penghapusan_tanggal_request ?? now())->translatedFormat('l, d F Y') }}</strong>, telah diajukan penghapusan aset:</p>

  <table class="meta">
    <tr><td>Nomor Penghapusan</td><td>: <strong>{{ $p->penghapusan_nomor }}</strong> <span class="badge">{{ $p->penghapusan_status }}</span> {{ $p->penghapusan_triase ? '• '.$p->penghapusan_triase : '' }}</td></tr>
    <tr><td>Aset</td><td>: <strong>{{ $aset->aset_kode }}</strong> — {{ $aset->aset_nama }} ({{ $aset->hasKategori?->aset_kategori_nama ?? '-' }})</td></tr>
    <tr><td>Lokasi</td><td>: {{ $aset->hasLokasi?->aset_lokasi_nama ?? '-' }}</td></tr>
    <tr><td>Alasan</td><td>: {{ $p->penghapusan_alasan ?? '-' }}</td></tr>
    <tr><td>Tanggal Request</td><td>: {{ $p->penghapusan_tanggal_request ? formatDate($p->penghapusan_tanggal_request, true) : '-' }}</td></tr>
    @if($p->penghapusan_tanggal_akhir_karantina)
    <tr><td>Akhir Karantina</td><td>: {{ formatDate($p->penghapusan_tanggal_akhir_karantina) }}</td></tr>
    @endif
    <tr><td>Catatan</td><td>: {{ $p->penghapusan_catatan ?? '-' }}</td></tr>
  </table>

  <div class="box">
    <h3>Detail Aset</h3>
    <table class="meta" style="margin-bottom:0">
      <tr><td>Merek / Model / SN</td><td>: {{ $aset->aset_merek ?? '-' }} / {{ $aset->aset_model ?? '-' }} / {{ $aset->aset_nomor_seri ?? '-' }}</td></tr>
      <tr><td>Harga Perolehan / Nilai Buku</td><td>: {{ formatRupiah($aset->aset_harga_perolehan) }} / {{ formatRupiah($p->penghapusan_nilai_buku ?? $aset->aset_nilai_sisa) }}</td></tr>
      <tr><td>Nilai Sisa / Gain Loss</td><td>: {{ formatRupiah($p->penghapusan_nilai_sisa ?? 0) }} / {{ formatRupiah($p->penghapusan_gain_loss ?? 0) }}</td></tr>
      <tr><td>Kondisi / Status</td><td>: {{ $aset->aset_kondisi ?? '-' }} / {{ $aset->aset_status ?? '-' }}</td></tr>
    </table>
  </div>

  <p>Dengan ini aset dinyatakan diajukan untuk dihapus dari daftar inventaris sesuai alasan di atas. Proses selanjutnya menunggu verifikasi dan persetujuan pihak berwenang.</p>

  <table class="sig">
    <tr>
      <td>
        Pengaju<br>
        <div class="line">{{ $pengaju?->name ?? 'Pengaju' }}<br><span style="font-weight:normal;color:#555;">{{ $pengaju?->email ?? '' }}</span></div>
      </td>
      <td>
        Mengetahui<br>
        <div class="line">{{ $petugas?->name ?? 'Petugas' }}<br><span style="font-weight:normal;color:#555;">{{ $petugas?->email ?? '' }}</span></div>
      </td>
      <td>
        Menyetujui<br>
        <div class="line"><br><span style="font-weight:normal;color:#555;"></span></div>
      </td>
    </tr>
  </table>

  <div class="footer">
    KIRO AMS — {{ now()->format('d/m/Y H:i') }} — Penghapusan {{ $p->penghapusan_nomor }} — Hash {{ substr(hash('sha256', $p->penghapusan_id.'|'.$aset->aset_id.'|'.now()->format('Y-m-d')),0,16) }}
  </div>
</body>
</html>
