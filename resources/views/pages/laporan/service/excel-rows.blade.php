@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ formatDate($row['r']->riwayat_service_tanggal) }}</td><td>{{ ($row['r']->hasAset?->aset_nama ?? '-') . ' (' . ($row['r']->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ $row['r']->hasTeknisi?->name ?? '-' }}</td><td>{{ $row['r']->riwayat_service_jenis }}</td><td>{{ formatRupiah($row['r']->riwayat_service_biaya, false) }}</td><td>{{ \Illuminate\Support\Str::limit($row['r']->riwayat_service_catatan ?? '-', 50) }}</td></tr>
@endforeach
