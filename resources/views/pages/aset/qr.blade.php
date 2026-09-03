<?php /** @var App\Models\Aset $aset */ ?>
<x-layouts::app>
    <x-breadcrumb :items="[['url' => route('aset.getTable'), 'label' => 'Aset'], ['url' => '', 'label' => 'QR — '.$aset->aset_nama]]" />

    <div class="max-w-lg mx-auto">
        <x-card label="QR Code Aset" icon="qr_code_2">
            <div class="col-span-12 flex flex-col items-center text-center">
                <p class="text-sm font-bold text-on-surface">{{ $aset->aset_nama }}</p>
                <p class="text-xs font-mono text-on-surface-variant">{{ $aset->aset_kode }} • {{ $aset->aset_kode_qr }}</p>
                <div class="mt-4 p-4 bg-white border border-outline-variant rounded-xl">
                    @if(str_starts_with($qrDataUri, 'data:'))
                        <img src="{{ $qrDataUri }}" alt="QR" class="w-[260px] h-[260px] object-contain">
                    @else
                        <img src="{{ $qrDataUri }}" alt="QR" class="w-[260px] h-[260px] object-contain">
                    @endif
                </div>
                <p class="text-[11px] font-mono text-on-surface-variant mt-3 break-all">{{ $qrText }}</p>
                <p class="text-xs text-on-surface-variant mt-1">Scan → detail aset + riwayat service • <a href="{{ $aset->tiket_qr_url }}" class="text-primary underline">Buat tiket langsung</a></p>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('aset.getQrPrint', ['id' => $aset->aset_id]) }}" class="inline-flex items-center gap-2 h-10 px-4 bg-primary text-on-primary rounded-xl text-sm font-semibold hover:bg-primary/90">
                        <span class="material-symbols-outlined text-lg">print</span> Print PDF
                    </a>
                    <a href="{{ route('aset.getUpdate', ['id' => $aset->aset_id]) }}" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-outline-variant text-on-surface rounded-xl text-sm font-semibold">
                        <span class="material-symbols-outlined text-lg">arrow_back</span> Kembali
                    </a>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts::app>
