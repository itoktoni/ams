<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
  .header { text-align: center; border-bottom: 3px double #111; padding-bottom: 12px; margin-bottom: 18px; }
  .header h1 { font-size: 16px; margin: 0; letter-spacing: 0.08em; }
  .header p { font-size: 10px; color: #555; margin: 4px 0 0; }
  .meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  .meta td { padding: 4px 6px; vertical-align: top; }
  .meta td:first-child { width: 160px; font-weight: bold; color: #333; }
  .box { border: 1px solid #bbb; border-radius: 6px; padding: 12px; margin-bottom: 14px; background: #fafafa; }
  .box h3 { font-size: 11px; margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #005f3b; }
  .grid { width: 100%; border-collapse: collapse; }
  .grid th, .grid td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 10px; }
  .grid th { background: #f0f0f0; font-weight: bold; }
  .sig { width: 100%; margin-top: 28px; border-collapse: collapse; }
  .sig td { width: 50%; text-align: center; vertical-align: top; padding-top: 8px; }
  .sig .line { margin-top: 64px; border-top: 1px solid #111; width: 180px; margin-left: auto; margin-right: auto; padding-top: 4px; font-size: 10px; }
  .footer { text-align: center; font-size: 8px; color: #888; margin-top: 24px; border-top: 1px solid #ddd; padding-top: 8px; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
</style>
</head>
<body>
  <div class="header">
    <h1>BERITA ACARA SERAH TERIMA ASET</h1>
    <p>KIRO Asset Management — No: BA/{{ $aset->aset_kode }}/{{ date('Y/m') }}/{{ str_pad($aset->aset_id, 5, '0', STR_PAD_LEFT) }}</p>
  </div>

  <p>Pada hari ini, <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>, telah dilakukan serah terima aset sebagai berikut:</p>

  <table class="meta">
    <tr><td>Kode / Nama Aset</td><td>: <strong>{{ $aset->aset_kode }}</strong> — {{ $aset->aset_nama }}</td></tr>
    <tr><td>Kategori / Lokasi</td><td>: {{ $aset->has_kategori?->aset_kategori_nama ?? '-' }} / {{ $aset->has_lokasi?->aset_lokasi_nama ?? '-' }}</td></tr>
    <tr><td>Merek / Model / SN</td><td>: {{ $aset->aset_merek ?? '-' }} / {{ $aset->aset_model ?? '-' }} / {{ $aset->aset_nomor_seri ?? '-' }}</td></tr>
    <tr><td>Harga Perolehan / Sisa</td><td>: {{ formatRupiah($aset->aset_harga_perolehan) }} / {{ formatRupiah($aset->aset_nilai_sisa) }}</td></tr>
    <tr><td>Tgl Perolehan / Masa</td><td>: {{ $aset->aset_tanggal_perolehan ? formatDate($aset->aset_tanggal_perolehan) : '-' }} — {{ $aset->aset_masa_manfaat }} bulan ({{ round($aset->aset_masa_manfaat/12,1) }} th) • {{ $aset->aset_metode_penyusutan ?? '-' }}</td></tr>
    <tr><td>Kondisi / Status</td><td>: <span class="badge">{{ $aset->aset_kondisi ?? '-' }} / {{ $aset->aset_status ?? '-' }}</span></td></tr>
  </table>

  <div class="box">
    <h3>Penerima (Penanggung Jawab)</h3>
    <table class="meta" style="margin-bottom:0">
      <tr><td>Nama</td><td>: {{ $penerima->name ?? '-' }} ({{ $penerima->email ?? '-' }})</td></tr>
      <tr><td>Role / Telepon</td><td>: {{ $penerima->role ?? '-' }} / {{ $penerima->phone ?? '-' }}</td></tr>
      <tr><td>Tanggal Masuk</td><td>: {{ $penerima->created_at ? formatDate($penerima->created_at) : '-' }}</td></tr>
    </table>
  </div>

  @if(!empty($aset->aset_custom_fields) && is_array($aset->aset_custom_fields))
  <div class="box">
    <h3>Spesifikasi / Custom Field</h3>
    <table class="grid">
      <tr><th>Field</th><th>Nilai</th></tr>
      @foreach(getCustomFields($aset->aset_custom_fields) as $k => $v)
        <tr><td>{{ customFieldLabel($k) }}</td><td>{{ is_array($v) ? implode(', ', $v) : $v }}</td></tr>
      @endforeach
    </table>
  </div>
  @endif

  <p>Dengan ditandatanganinya berita acara ini, penerima menyatakan telah menerima aset dalam kondisi sebagaimana tercatat, bertanggung jawab atas perawatan, dan wajib melaporkan kerusakan/kehilangan. Aset tercatat sebagai <strong>{{ $aset->aset_kode }}</strong> pada sistem KIRO AMS.</p>

  <table class="sig">
    <tr>
      <td>
        Pemberi / Admin Aset<br>
        <div class="line">{{ $pemberi->name ?? 'Admin AMS' }}<br><span style="font-weight:normal; color:#555;">{{ $pemberi->email ?? '' }}</span></div>
      </td>
      <td>
        Penerima<br>
        <div class="line">{{ $penerima->name }}<br><span style="font-weight:normal; color:#555;">{{ $penerima->email }}</span></div>
      </td>
    </tr>
  </table>

  <div class="footer">
    Dicetak otomatis oleh KIRO AMS — {{ now()->format('d/m/Y H:i') }} — Hash: {{ substr(hash('sha256', $aset->aset_id.'|'.$penerima->id.'|'.now()->format('Y-m-d')),0,16) }} • Halaman 1/1
  </div>
</body>
</html>
