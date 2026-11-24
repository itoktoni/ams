@foreach($data as $row)
<tr><td>{{ $row['no'] }}</td><td>{{ $row['r']->buku_penyusutan_periode }}</td><td>{{ ($row['r']->hasAset?->aset_nama ?? '#' . $row['r']->buku_penyusutan_id_aset) . ' (' . ($row['r']->hasAset?->aset_kode ?? '-') . ')' }}</td><td>{{ formatRupiah($row['r']->buku_penyusutan_nilai, false) }}</td><td>{{ formatRupiah($row['r']->buku_penyusutan_akumulasi, false) }}</td><td>{{ formatRupiah($row['r']->buku_penyusutan_nilai_buku, false) }}</td></tr>
@endforeach
