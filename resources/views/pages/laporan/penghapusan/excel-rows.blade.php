@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->penghapusan_nomor }}</td><td>{{ ($row['r']->hasAset?->aset_nama ?? '-') . ' (' . ($row['r']->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ \Illuminate\Support\Str::limit($row['r']->penghapusan_alasan ?? '-', 40) }}</td><td>{{ formatDate($row['r']->penghapusan_tanggal_request) }}</td><td>{{ formatRupiah($row['r']->penghapusan_nilai_buku, false) }}</td><td>{{ $row['r']->penghapusan_triase ?? '-' }}</td><td>{{ $row['r']->penghapusan_status }}</td></tr>
@endforeach
