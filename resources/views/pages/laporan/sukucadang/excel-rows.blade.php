@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->suku_cadang_kode }}</td><td>{{ $row['r']->suku_cadang_nama }}</td><td>{{ $row['r']->hasGudang?->gudang_nama ?? '-' }}</td><td>{{ formatRupiah($row['r']->suku_cadang_harga, false) }}</td><td>{{ formatQty($stokmap[$row['r']->suku_cadang_id] ?? 0) }}</td><td>{{ formatQty($row['r']->suku_cadang_stok_minimum) }}</td><td>{{ ((float) ($stokmap[$row['r']->suku_cadang_id] ?? 0) < (float) $row['r']->suku_cadang_stok_minimum) ? 'MENIPIS' : 'Aman' }}</td></tr>
@endforeach
