@foreach($data as $row)
@php
    $rr = $row['r'];
    $kendaraan = ($rr->bbm_pengajuan_jenis_kendaraan === 'motor' ? 'Motor' : 'Mobil');
    if ($rr->bbm_pengajuan_kendaraan) $kendaraan .= ' — '.$rr->bbm_pengajuan_kendaraan;
@endphp
<tr><td>{{ $row['no'] }}</td><td>{{ $rr->bbm_pengajuan_nomor }}</td><td>{{ $rr->bbm_pengajuan_periode }}</td><td>{{ $rr->hasUser?->name ?? '-' }}</td><td>{{ $rr->hasUser?->unit_kode ?? '-' }}</td><td>{{ $kendaraan }}</td><td>{{ $rr->bbm_pengajuan_liter }}</td><td>{{ $rr->bbm_pengajuan_status ?? '-' }}</td><td>{{ $rr->bbm_pengajuan_tanggal ? $rr->bbm_pengajuan_tanggal->format('d/m/Y') : '-' }}</td><td>{{ $rr->hasApprover?->name ?? '-' }}</td><td>{{ $rr->bbm_pengajuan_catatan ?? '-' }}</td></tr>
@endforeach
