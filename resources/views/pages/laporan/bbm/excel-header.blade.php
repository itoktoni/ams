<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="UTF-8"></head><body>
<table><tr><td colspan="11"><b>{{ $judulReport ?? 'REPORT BBM' }}</b></td></tr>
<tr><td colspan="11"><b>{{ $filterTitle ?? ('PERIODE : ' . ($dari ?? '-') . ' s/d ' . ($sampai ?? '-')) }}</b></td></tr>
<tr><td colspan="11">{{ $kop['instansi'] ?? '' }} @if(! empty($kop['kop_alamat'])) — {{ $kop['kop_alamat'] }}@endif</td></tr></table><br>
<table border="1"><thead><tr><th>No</th><th>Nomor</th><th>Periode</th><th>Peminta</th><th>Unit</th><th>Kendaraan</th><th>Liter</th><th>Status</th><th>Tgl Pengajuan</th><th>Approver</th><th>Catatan</th></tr></thead><tbody>
