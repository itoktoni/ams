<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
h1 { font-size: 16px; margin: 0 0 4px; }
h2 { font-size: 12px; margin: 12px 0 6px; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; font-size: 10px; }
th { background: #f2f2f2; }
.meta { font-size: 11px; color: #555; margin-bottom: 8px; }
.badge { display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 10px; background: #eee; }
.footer { margin-top: 16px; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
<h1>Report Bulanan Tiket — {{ $periode }}</h1>
<div class="meta">
    Teknisi: {{ $teknisiId ? (\App\Models\User::find($teknisiId)?->name ?? $teknisiId) : 'Semua Teknisi' }} &nbsp;|&nbsp;
    Total: {{ $total }} &nbsp;|&nbsp; Selesai: {{ $selesai }} &nbsp;|&nbsp; Progres: {{ $progres }} &nbsp;|&nbsp; Ditugaskan: {{ $ditugaskan }}<br>
    Dicetak: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()?->name }}
</div>

<table>
<thead>
<tr>
    <th>Nomor</th><th>Aset</th><th>Status</th><th>Teknisi</th><th>Dibuat</th><th>Diambil</th><th>Selesai</th><th>Analisa</th><th>Kesimpulan</th>
</tr>
</thead>
<tbody>
@foreach($data as $t)
<tr>
    <td>{{ $t->tiket_nomor }}</td>
    <td>{{ $t->hasAset?->aset_nama }} ({{ $t->hasAset?->aset_kode }})</td>
    <td>{{ $t->tiket_status }}</td>
    <td>{{ $t->hasTeknisi?->name }}</td>
    <td>{{ $t->tiket_tanggal_lapor ? $t->tiket_tanggal_lapor->format('d/m/Y H:i') : '-' }}</td>
    <td>{{ $t->tiket_tanggal_tugas ? $t->tiket_tanggal_tugas->format('d/m/Y H:i') : '-' }}</td>
    <td>{{ $t->tiket_tanggal_selesai ? $t->tiket_tanggal_selesai->format('d/m/Y H:i') : '-' }}</td>
    <td>{{ \Illuminate\Support\Str::limit($t->tiket_analisa ?? '-', 120) }}</td>
    <td>{{ \Illuminate\Support\Str::limit($t->tiket_kesimpulan ?? '-', 120) }}</td>
</tr>
@endforeach
</tbody>
</table>

<div class="footer">
    Dokumen ini adalah bukti pekerjaan harian teknisi periode {{ $periode }}. Halaman 1 dari 1. Sistem AMS.
</div>
</body>
</html>
