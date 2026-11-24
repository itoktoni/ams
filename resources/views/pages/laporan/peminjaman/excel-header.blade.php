<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="UTF-8"></head><body>
<table><tr><td colspan="10"><b>REPORT PEMINJAMAN</b></td></tr>
<tr><td colspan="10"><b>PERIODE : {{ ($dari ?? '-') . ' s/d ' . ($sampai ?? '-') }}</b></td></tr></table><br>
<table border="1"><thead><tr><th>No</th><th>Nomor</th><th>Aset</th><th>Peminjam</th><th>Tujuan</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Kembali</th><th>Status</th><th>Denda</th></tr></thead><tbody>
