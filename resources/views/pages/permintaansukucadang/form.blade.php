<?php /** @var App\Models\PermintaanSukuCadang $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    @if(!empty($budgetInfo))
    <div class="mb-4 grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Department</p>
            <p class="text-sm font-bold text-on-surface mt-1">{{ $budgetInfo['department']->department_nama }} ({{ $budgetInfo['department']->department_kode }})</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Budget</p>
            <p class="text-sm font-bold text-on-surface mt-1">{{ formatRupiah($budgetInfo['department']->department_budget) }}</p>
            <p class="text-[11px] text-on-surface-variant">Periode {{ ucfirst($budgetInfo['department']->department_periode) }}</p>
        </div>
        <div class="rounded-xl border p-3 {{ $budgetInfo['sisa'] < 0 ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}">
            <p class="text-[10px] font-bold tracking-widest uppercase {{ $budgetInfo['sisa'] < 0 ? 'text-error' : 'text-primary' }}">Terpakai / Sisa</p>
            <p class="text-sm font-bold mt-1 {{ $budgetInfo['sisa'] < 0 ? 'text-error' : 'text-on-surface' }}">{{ formatRupiah($budgetInfo['terpakai']) }} / {{ formatRupiah($budgetInfo['sisa']) }}</p>
            <div class="mt-2 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                @php $pct = $budgetInfo['department']->department_budget > 0 ? min(100, ($budgetInfo['terpakai'] / $budgetInfo['department']->department_budget) * 100) : 0; @endphp
                <div class="h-full {{ $budgetInfo['sisa'] < 0 ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>
    @elseif(auth()->user()?->department_id === null)
    <div class="mb-4 rounded-xl border border-warning bg-warning-container p-3 text-sm text-on-warning-container">
        Akun Anda belum di-assign ke department — hubungi admin untuk assign department sebelum membuat permintaan.
    </div>
    @endif

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                {{-- Simple: hanya suku cadang + jumlah + catatan — nomor/peminta/harga/subtotal auto --}}
                <x-select col="6" name="permintaan_suku_cadang_id_suku_cadang" label="Suku Cadang" :options="App\Models\SukuCadang::getOptions()" class="search" />
                <x-input col="6" type="number" step="0.01" name="permintaan_suku_cadang_jumlah" label="Jumlah" placeholder="1" />
                <x-textarea col="12" name="permintaan_suku_cadang_catatan" label="Catatan" placeholder="Keperluan / keterangan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
