<?php /** @var App\Models\PermintaanSukuCadang $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    @if(!empty($budgetInfo))
    @php
        $deptBudget = (float) $budgetInfo['department']->department_budget;
        $terpakai = (float) $budgetInfo['terpakai'];
        $menunggu = (float) $budgetInfo['pending'];
        $tersedia = (float) $budgetInfo['tersedia'];
        $overBudget = $tersedia < 0;
    @endphp
    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Department</p>
            <p class="text-sm font-bold text-on-surface mt-1">{{ $budgetInfo['department']->department_nama }} ({{ $budgetInfo['department']->department_kode }})</p>
            <p class="text-[11px] text-on-surface-variant">Periode {{ ucfirst($budgetInfo['department']->department_periode) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Budget</p>
            <p class="text-sm font-bold text-on-surface mt-1">{{ formatRupiah($deptBudget) }}</p>
            <p class="text-[11px] text-on-surface-variant">Sisa {{ formatRupiah($budgetInfo['sisa']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Terpakai (disetujui)</p>
            <p class="text-sm font-bold text-on-surface mt-1">{{ formatRupiah($terpakai) }}</p>
            <p class="text-[11px] text-on-surface-variant">Menunggu {{ formatRupiah($menunggu) }}</p>
        </div>
        <div class="rounded-xl border p-3 {{ $overBudget ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}">
            <p class="text-[10px] font-bold tracking-widest uppercase {{ $overBudget ? 'text-error' : 'text-primary' }}">Tersedia</p>
            <p class="text-sm font-bold mt-1 {{ $overBudget ? 'text-error' : 'text-on-surface' }}">{{ formatRupiah($tersedia) }}</p>
            <div class="mt-2 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                @php $pct = $deptBudget > 0 ? min(100, (($terpakai + $menunggu) / $deptBudget) * 100) : 0; @endphp
                <div class="h-full {{ $overBudget ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
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
                @if(isset($model) && $model->exists)
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Status</label>
                    @if(in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true))
                    <select name="permintaan_suku_cadang_status" class="w-full h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg font-body-sm">
                        @foreach($statusOptions as $val => $label)
                        <option value="{{ $val }}" {{ ($model->permintaan_suku_cadang_status ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @else
                    <div class="h-12 px-4 flex items-center bg-surface-container rounded-lg border border-outline-variant text-sm font-bold">{{ $statusOptions[$model->permintaan_suku_cadang_status] ?? $model->permintaan_suku_cadang_status }}</div>
                    <input type="hidden" name="permintaan_suku_cadang_status" value="{{ $model->permintaan_suku_cadang_status }}">
                    @endif
                </div>
                @endif

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @if(isset($model) && $model->exists && $model->permintaan_suku_cadang_status === 'menunggu' && in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true))
    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ route('permintaan-suku-cadang.getApprove', ['id' => $model->permintaan_suku_cadang_id]) }}" onclick="return confirm('Setujui permintaan ini?')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-success text-white text-sm font-semibold hover:bg-success/90">
            <span class="material-symbols-outlined">check_circle</span> Approve
        </a>
        <a href="{{ route('permintaan-suku-cadang.getReject', ['id' => $model->permintaan_suku_cadang_id]) }}" onclick="return confirm('Tolak permintaan ini?')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-error/90">
            <span class="material-symbols-outlined">cancel</span> Tolak
        </a>
    </div>
    @endif
</x-layouts::app>
