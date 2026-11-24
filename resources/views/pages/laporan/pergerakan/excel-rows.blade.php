@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ formatDate($row['r']->created_at) }}</td><td>{{ ($row['r']->hasSukuCadang?->suku_cadang_nama ?? '-') . ' (' . ($row['r']->hasSukuCadang?->suku_cadang_kode ?? '-') . ')' }}</td><td>{{ $row['r']->hasGudang?->gudang_nama ?? '-' }}</td><td>{{ $row['r']->pergerakan_stok_tipe ?? '-' }}</td><td>{{ formatQty($row['r']->pergerakan_stok_jumlah) }}</td><td>{{ $row['r']->pergerakan_stok_referensi ?? '-' }}</td></tr>
@endforeach
