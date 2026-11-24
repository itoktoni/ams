@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->perpindahan_nomor }}</td><td>{{ ($row['r']->hasAset?->aset_nama ?? '-') . ' (' . ($row['r']->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ $row['r']->hasLokasiAsal?->aset_lokasi_nama ?? '-' }}</td><td>{{ $row['r']->hasLokasiTujuan?->aset_lokasi_nama ?? '-' }}</td><td>{{ formatDate($row['r']->perpindahan_tanggal_request) }}</td><td>{{ $row['r']->perpindahan_status }}</td></tr>
@endforeach
