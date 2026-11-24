@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->opname_nomor }}</td><td>{{ $row['r']->hasLokasi?->aset_lokasi_nama ?? '-' }}</td><td>{{ formatDate($row['r']->opname_tanggal) }}</td><td>{{ $row['r']->hasPetugas?->name ?? '-' }}</td><td>{{ $row['r']->opname_total_sistem }}</td><td>{{ $row['r']->opname_total_fisik }}</td><td>{{ $row['r']->opname_total_selisih }}</td><td>{{ $row['r']->opname_status }}</td></tr>
@endforeach
