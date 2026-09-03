<?php /** @var App\Models\Opname $opname */ ?>
<x-layouts::app>
    <x-breadcrumb :items="[['url' => route('opname.getTable'), 'label' => 'Opname'], ['url' => route('opname.getUpdate', ['id' => $opname->opname_id]), 'label' => $opname->opname_nomor], ['url' => '', 'label' => 'Report']]" />

    <div class="bg-white border border-outline-variant rounded-2xl p-4 mb-4">
        <h2 class="font-bold text-base">{{ $opname->opname_nomor }} — {{ $opname->hasLokasi?->aset_lokasi_nama ?? '-' }}</h2>
        <p class="text-xs text-on-surface-variant">Periode {{ formatDate($opname->opname_tanggal_mulai) }} s/d {{ formatDate($opname->opname_tanggal_selesai) }} • Status {{ $opname->opname_status }}</p>
        <div class="grid grid-cols-3 gap-3 mt-3 text-center">
            <div class="bg-surface-container rounded-xl py-2"><p class="text-lg font-bold">{{ $details->count() }}</p><p class="text-[11px]">Total Sistem</p></div>
            <div class="bg-success/10 rounded-xl py-2"><p class="text-lg font-bold text-success">{{ $found->count() }}</p><p class="text-[11px]">Ditemukan</p></div>
            <div class="bg-error/10 rounded-xl py-2"><p class="text-lg font-bold text-error">{{ $missing->count() }}</p><p class="text-[11px]">Belum / Hilang</p></div>
        </div>
        <div class="mt-3 flex gap-2">
            <a href="{{ route('opname.getReportPrint', ['id' => $opname->opname_id]) }}" class="inline-flex items-center gap-2 h-9 px-3 rounded-lg bg-primary text-on-primary text-sm font-semibold">Cetak PDF</a>
            <a href="{{ route('opname.getUpdate', ['id' => $opname->opname_id]) }}" class="inline-flex items-center gap-2 h-9 px-3 rounded-lg bg-white border border-outline-variant text-sm font-semibold">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-outline-variant rounded-2xl overflow-hidden">
            <div class="px-4 py-3 bg-success/10 border-b border-success/20 font-semibold text-sm text-success">Ditemukan ({{ $found->count() }}) — kapan ketemu</div>
            <div class="divide-y divide-outline-variant/20 max-h-[420px] overflow-auto">
                @forelse($found as $d)
                <div class="p-3 flex items-center gap-3 text-sm">
                    <span class="w-2 h-2 rounded-full bg-success shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $d->hasAset?->aset_nama ?? 'Aset #'.$d->opname_detail_id_aset }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $d->hasAset?->aset_kode ?? '' }} • {{ formatDate($d->opname_detail_waktu_scan, true) }}</p>
                    </div>
                    <span class="text-xs text-on-surface-variant">{{ $d->hasPetugasScan?->name ?? '' }}</span>
                </div>
                @empty
                <p class="p-4 text-center text-sm text-on-surface-variant">Belum ada</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white border border-outline-variant rounded-2xl overflow-hidden">
            <div class="px-4 py-3 bg-error/10 border-b border-error/20 font-semibold text-sm text-error">Belum Ditemukan / Kosong ({{ $missing->count() }})</div>
            <div class="divide-y divide-outline-variant/20 max-h-[420px] overflow-auto">
                @forelse($missing as $d)
                <div class="p-3 flex items-center gap-3 text-sm">
                    <span class="w-2 h-2 rounded-full bg-error shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $d->hasAset?->aset_nama ?? 'Aset #'.$d->opname_detail_id_aset }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $d->hasAset?->aset_kode ?? '' }} • {{ $d->opname_detail_status_sistem ?? '-' }}</p>
                    </div>
                    <span class="text-xs text-error font-bold">Belum</span>
                </div>
                @empty
                <p class="p-4 text-center text-sm text-success">Semua ditemukan ✓</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts::app>
