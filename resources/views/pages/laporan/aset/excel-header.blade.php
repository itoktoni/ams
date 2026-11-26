<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="UTF-8"></head><body>
<table><tr><td colspan="13"><b>{{ $judulReport ?? 'REPORT ASET' }}</b></td></tr>
<tr><td colspan="13"><b>{{ $filterTitle ?? ('PERIODE : ' . ($dari ?? '-') . ' s/d ' . ($sampai ?? '-')) }}</b></td></tr>
<tr><td colspan="13">{{ $kop['instansi'] ?? '' }} @if(! empty($kop['kop_alamat'])) — {{ $kop['kop_alamat'] }}@endif</td></tr></table><br>
<table border="1"><thead><tr><th>No</th><th>Kode</th><th>Kode Lama</th><th>No. Label</th><th>Nama</th><th>Merek / Model</th><th>Kategori</th><th>Ruangan</th><th>Divisi</th><th>Tgl Perolehan</th><th>Harga</th><th>Status</th><th>Kondisi</th></tr></thead><tbody>
