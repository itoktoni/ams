@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->pesanan_pembelian_nomor }}</td><td>{{ $row['r']->hasVendor?->vendor_nama ?? '-' }}</td><td>{{ formatDate($row['r']->pesanan_pembelian_tanggal) }}</td><td>{{ $row['r']->pesanan_pembelian_tipe }}</td><td>{{ $row['r']->hasItem->count() . ' item' }}</td><td>{{ formatRupiah($row['r']->pesanan_pembelian_total, false) }}</td><td>{{ $row['r']->pesanan_pembelian_status }}</td></tr>
@endforeach
