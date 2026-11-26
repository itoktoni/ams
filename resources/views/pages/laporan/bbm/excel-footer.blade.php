</tbody></table>
@if(!empty($ringkasan['rows']))
<br><table><tr><td colspan="8"><b>RINGKASAN KUOTA PER ORANG</b></td></tr></table>
<table border="1"><thead><tr><th>No</th><th>Peminta</th><th>Unit</th><th>Periode</th><th>Kuota (L)</th><th>Terpakai (L)</th><th>Menunggu (L)</th><th>Sisa (L)</th></tr></thead><tbody>
@foreach($ringkasan['rows'] as $i => $row)
<tr><td>{{ $i + 1 }}</td><td>{{ $row['user']?->name ?? '-' }}</td><td>{{ $row['user']?->unit_kode ?? '-' }}</td><td>{{ $row['periode'] }}</td><td>{{ $row['kuota'] }}</td><td>{{ $row['terpakai'] }}</td><td>{{ $row['pending'] }}</td><td>{{ $row['tersedia'] }}</td></tr>
@endforeach
<tr><td colspan="4"><b>TOTAL</b></td><td><b>{{ $ringkasan['totalKuota'] }}</b></td><td><b>{{ $ringkasan['totalTerpakai'] }}</b></td><td><b>{{ $ringkasan['totalPending'] }}</b></td><td><b>{{ $ringkasan['totalTersedia'] }}</b></td></tr>
</tbody></table>
@endif
</body></html>
