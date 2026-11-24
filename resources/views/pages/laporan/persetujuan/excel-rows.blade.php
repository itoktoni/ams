@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->persetujuan_modul }}</td><td>{{ $row['r']->persetujuan_id_referensi }}</td><td>{{ $row['r']->persetujuan_level }}</td><td>{{ $row['r']->hasUser?->name ?? '-' }}</td><td>{{ $row['r']->persetujuan_status }}</td><td>{{ \Illuminate\Support\Str::limit($row['r']->persetujuan_catatan ?? '-', 40) }}</td></tr>
@endforeach
