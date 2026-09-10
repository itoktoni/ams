<?php /** @var \Illuminate\Support\Collection $data */ ?>
<x-layouts::app>
    <x-breadcrumb :items="[['url' => route('tiket.getTable'), 'label' => 'Tiket'], ['url' => '', 'label' => 'Report Bulanan']]" />

    <x-card label="Report Bulanan Teknisi — Bukti Pekerjaan Harian" icon="assessment">
        <form method="GET" action="{{ route('tiket.getReportBulanan') }}" class="col-span-12 grid grid-cols-12 gap-3">
            @if(!$isTeknisi)
                <div class="col-span-12 md:col-span-4">
                    <label class="text-xs font-medium text-on-surface-variant">Teknisi</label>
                    <select name="teknisi_id" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
                        <option value="">Semua Teknisi</option>
                        @foreach($teknisiOptions as $id => $name)
                            <option value="{{ $id }}" @selected((string)$teknisiId === (string)$id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="teknisi_id" value="{{ $teknisiId }}">
                <div class="col-span-12 md:col-span-4 p-3 bg-primary/5 rounded-xl text-sm">Teknisi: <b>{{ auth()->user()->name }}</b></div>
            @endif
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Periode (YYYY-MM)</label>
                <input type="month" name="periode" value="{{ $periode }}" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
            </div>
            <div class="col-span-6 md:col-span-2 flex items-end">
                <button type="submit" class="h-10 px-5 rounded-xl bg-primary text-on-primary font-semibold w-full">Tampilkan</button>
            </div>
            <div class="col-span-12 md:col-span-3 flex items-end gap-2">
                <a href="{{ route('tiket.getReportBulanan', ['teknisi_id' => $teknisiId, 'periode' => $periode, 'export' => 'excel']) }}" class="h-10 px-3 rounded-xl bg-white border border-outline-variant text-sm inline-flex items-center gap-1">Excel/CSV</a>
                <a href="{{ route('tiket.getReportBulanan', ['teknisi_id' => $teknisiId, 'periode' => $periode, 'export' => 'pdf']) }}" class="h-10 px-3 rounded-xl bg-white border border-outline-variant text-sm inline-flex items-center gap-1">PDF</a>
            </div>
        </form>

        {{-- Agregasi --}}
        <div class="col-span-12 grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
            <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/40">
                <p class="text-xs text-on-surface-variant">Total Tiket Bulan Ini</p>
                <p class="text-2xl font-bold">{{ $total }}</p>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                <p class="text-xs text-on-surface-variant">Selesai/Terverifikasi</p>
                <p class="text-2xl font-bold text-emerald-700">{{ $selesai }}</p>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                <p class="text-xs text-on-surface-variant">Progres</p>
                <p class="text-2xl font-bold text-blue-700">{{ $progres }}</p>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                <p class="text-xs text-on-surface-variant">Ditugaskan</p>
                <p class="text-2xl font-bold text-amber-700">{{ $ditugaskan }}</p>
            </div>
        </div>
    </x-card>

    <x-card label="Detail Pekerjaan ({{ $periode }})" icon="list_alt">
        @if($data->isEmpty())
            <div class="col-span-12 p-6 text-center text-sm text-on-surface-variant">Tidak ada tiket untuk periode ini.</div>
        @else
            <div class="col-span-12 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-on-surface-variant border-b">
                            <th class="text-left py-2 px-2">Nomor</th>
                            <th class="text-left py-2 px-2">Aset</th>
                            <th class="text-left py-2 px-2">Status</th>
                            <th class="text-left py-2 px-2">Teknisi</th>
                            <th class="text-left py-2 px-2">Tgl Dibuat</th>
                            <th class="text-left py-2 px-2">Tgl Diambil</th>
                            <th class="text-left py-2 px-2">Tgl Selesai</th>
                            <th class="text-left py-2 px-2">Analisa</th>
                            <th class="text-left py-2 px-2">Kesimpulan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $t)
                        <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50">
                            <td class="py-2 px-2 font-mono text-xs"><a href="{{ route('tiket.getShow', $t->tiket_id) }}" class="underline">{{ $t->tiket_nomor }}</a></td>
                            <td class="py-2 px-2">{{ $t->hasAset?->aset_nama }}<br><span class="text-xs text-on-surface-variant">{{ $t->hasAset?->aset_kode }}</span></td>
                            <td class="py-2 px-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-surface-container">{{ $t->tiket_status }}</span></td>
                            <td class="py-2 px-2">{{ $t->hasTeknisi?->name ?? '-' }}</td>
                            <td class="py-2 px-2 text-xs">{{ $t->tiket_tanggal_lapor ? formatDate($t->tiket_tanggal_lapor) : '-' }}</td>
                            <td class="py-2 px-2 text-xs">{{ $t->tiket_tanggal_tugas ? formatDate($t->tiket_tanggal_tugas) : '-' }}</td>
                            <td class="py-2 px-2 text-xs">{{ $t->tiket_tanggal_selesai ? formatDate($t->tiket_tanggal_selesai) : '-' }}</td>
                            <td class="py-2 px-2 text-xs max-w-[220px] truncate" title="{{ $t->tiket_analisa }}">{{ Str::limit($t->tiket_analisa ?? '-', 80) }}</td>
                            <td class="py-2 px-2 text-xs max-w-[220px] truncate" title="{{ $t->tiket_kesimpulan }}">{{ Str::limit($t->tiket_kesimpulan ?? '-', 80) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts::app>
