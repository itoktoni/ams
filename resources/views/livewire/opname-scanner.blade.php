<div>
    <div class="flex gap-2">
        <input type="text" wire:model="kode" wire:keydown.enter="scan" placeholder="Scan / ketik kode QR lalu Enter" autofocus
            class="flex-1 h-10 px-4 bg-white border {{ $isError ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm font-mono">
        <button type="button" wire:click="scan" wire:loading.attr="disabled" class="h-10 px-4 bg-success text-white rounded-xl text-sm font-semibold disabled:opacity-50">
            <span wire:loading.remove wire:target="scan">Submit</span>
            <span wire:loading wire:target="scan">Memproses...</span>
        </button>
    </div>
    @if($message)
        <div class="mt-3 rounded-xl px-4 py-3 flex items-center gap-3 {{ $isSuccess ? 'bg-success/10 border border-success/30 text-success' : ($isError ? 'bg-error/10 border border-error/30 text-error' : 'bg-surface-container border border-outline-variant text-on-surface-variant') }}">
            <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $isSuccess ? 'bg-success text-white' : ($isError ? 'bg-error text-white' : 'bg-outline-variant') }}">
                <span class="material-symbols-outlined text-lg">{{ $isSuccess ? 'check_circle' : ($isError ? 'error' : 'info') }}</span>
            </span>
            <p class="text-sm font-bold leading-tight">{{ $message }}</p>
        </div>
    @endif

    {{-- Realtime detail table — auto updates after scan without reload --}}
    <div class="mt-4 bg-white border border-outline-variant rounded-2xl overflow-hidden">
        <div class="px-4 py-3 border-b border-outline-variant flex items-center justify-between">
            <h3 class="font-semibold text-sm">Detail Aset di Lokasi — {{ $opname->hasLokasi?->aset_lokasi_nama ?? '-' }}</h3>
            <span class="text-xs text-on-surface-variant">{{ $progress['found'] }}/{{ $progress['total'] }} ditemukan</span>
        </div>
        <div class="px-4 py-2 border-b border-outline-variant/50 grid grid-cols-3 gap-2 text-center text-xs">
            <div class="bg-surface-container rounded-lg py-1.5"><span class="font-bold">{{ $progress['total'] }}</span> total</div>
            <div class="bg-success/10 rounded-lg py-1.5 text-success"><span class="font-bold">{{ $progress['found'] }}</span> ditemukan</div>
            <div class="bg-error/10 rounded-lg py-1.5 text-error"><span class="font-bold">{{ $progress['missing'] }}</span> belum</div>
        </div>
        <div class="overflow-auto max-h-[420px]">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-surface-container text-xs">
                    <tr><th class="text-left p-2">Aset</th><th class="text-left p-2">Status</th><th class="text-left p-2">Ditemukan</th><th class="text-left p-2">Waktu Scan</th></tr>
                </thead>
                <tbody>
                @foreach($details as $d)
                    <tr class="border-t border-outline-variant/20 {{ $d->opname_detail_ditemukan ? 'bg-success/5' : 'bg-error/5' }}">
                        <td class="p-2">{{ $d->hasAset?->aset_nama ?? 'Aset #'.$d->opname_detail_id_aset }} <span class="text-xs text-on-surface-variant">{{ $d->hasAset?->aset_kode ?? '' }}</span></td>
                        <td class="p-2 text-xs">{{ $d->opname_detail_status_sistem ?? '-' }}</td>
                        <td class="p-2">@if($d->opname_detail_ditemukan)<span class="px-2 py-0.5 rounded-full bg-success text-white text-xs">Ya</span>@else<span class="px-2 py-0.5 rounded-full bg-error text-white text-xs">Belum</span>@endif</td>
                        <td class="p-2 text-xs">{{ $d->opname_detail_waktu_scan ? formatDate($d->opname_detail_waktu_scan, true) : '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-outline-variant flex gap-2">
            <a href="{{ route('opname.getReport', ['id' => $opname->opname_id]) }}" class="inline-flex items-center gap-2 h-9 px-3 rounded-lg bg-primary text-on-primary text-sm font-semibold">Report</a>
            <a href="{{ route('opname.getReportPrint', ['id' => $opname->opname_id]) }}" class="inline-flex items-center gap-2 h-9 px-3 rounded-lg bg-white border border-outline-variant text-sm font-semibold">Cetak PDF</a>
        </div>
    </div>
</div>
