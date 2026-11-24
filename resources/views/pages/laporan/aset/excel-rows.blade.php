@foreach($data as $row)
@php
    $rr = $row['r'];
    $divisi = $rr->hasDepartment?->department_kode;
    if (!$divisi && $rr->aset_nomor_label) {
        $seg = explode('/', $rr->aset_nomor_label);
        $divisi = $seg[2] ?? null;
    }
@endphp
<tr><td>{{ $row['no'] }}</td><td>{{ $rr->aset_kode }}</td><td>{{ $rr->aset_label_lama ?? '-' }}</td><td>{{ $rr->aset_nomor_label ?? '-' }}</td><td>{{ $rr->aset_nama }}</td><td>{{ trim(($rr->aset_merek ?? '-') . ' ' . ($rr->aset_model ?? '')) }}</td><td>{{ $rr->hasKategori?->aset_kategori_nama ?? '-' }}</td><td>{{ $rr->hasLokasi?->aset_lokasi_nama ?? '-' }}</td><td>{{ $divisi ?? '-' }}</td><td>{{ $rr->aset_tanggal_perolehan ? formatDate($rr->aset_tanggal_perolehan) : '-' }}</td><td>{{ formatRupiah($rr->aset_harga_perolehan, false) }}</td><td>{{ $rr->aset_status ?? '-' }}</td><td>{{ $rr->aset_kondisi ?? '-' }}</td></tr>
@endforeach
