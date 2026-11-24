<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => 'Cetak Label Aset']]" />

    <x-card :label="'Cetak Label Aset (PDF A4)'" icon="print">
        <form method="GET" action="{{ route('cetak-label.getIndex') }}" class="col-span-12 grid grid-cols-12 gap-3">
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Department (PJ)</label>
                <select name="department_id" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
                    <option value="">Semua</option>
                    @foreach($departments as $val => $label)
                    <option value="{{ $val }}" @selected((string)($filters['department_id'] ?? '') === (string)$val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Kategori</label>
                <select name="kategori_id" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
                    <option value="">Semua</option>
                    @foreach($kategoris as $val => $label)
                    <option value="{{ $val }}" @selected((string)($filters['kategori_id'] ?? '') === (string)$val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Lokasi</label>
                <select name="lokasi_id" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
                    <option value="">Semua</option>
                    @foreach($lokasis as $val => $label)
                    <option value="{{ $val }}" @selected((string)($filters['lokasi_id'] ?? '') === (string)$val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Status</label>
                <select name="status" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
                    <option value="">Semua</option>
                    @foreach($statuses as $val => $label)
                    <option value="{{ $val }}" @selected((string)($filters['status'] ?? '') === (string)$val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Tanggal Buat (Dari)</label>
                <input type="date" name="tanggal_dari" value="{{ $filters['tanggal_dari'] ?? '' }}" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
            </div>
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Tanggal Buat (Sampai)</label>
                <input type="date" name="tanggal_sampai" value="{{ $filters['tanggal_sampai'] ?? '' }}" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
            </div>
            <div class="col-span-6 md:col-span-2 flex items-end">
                <button type="submit" class="h-10 px-5 rounded-xl bg-primary text-on-primary font-semibold w-full">Tampilkan</button>
            </div>
            <div class="col-span-6 md:col-span-3 flex items-end">
                <a href="{{ route('cetak-label.getPdf', request()->only(['department_id', 'kategori_id', 'lokasi_id', 'status', 'tanggal_dari', 'tanggal_sampai'])) }}" target="_blank" class="h-10 px-5 rounded-xl bg-emerald-600 text-white font-semibold w-full inline-flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-lg">print</span> Cetak PDF A4
                </a>
            </div>
        </form>
        <p class="col-span-12 text-xs text-on-surface-variant mt-1">Label A4: 5 kolom per baris, garis potong putus-putus tipis. Preview di bawah (maks 100), PDF maks 500 baris.</p>
    </x-card>

    <x-card :label="'Preview — ' . count($items) . ' aset'" icon="list_alt">
        @if(count($items) === 0)
        <div class="col-span-12 p-6 text-center text-sm text-on-surface-variant">Tidak ada data untuk filter ini.</div>
        @else
        <div class="col-span-12 grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach($items as $aset)
            <div class="border border-dashed border-outline-variant rounded-xl p-3 text-center">
                <img src="{{ $aset->qr_data_uri }}" alt="QR" class="w-20 h-20 mx-auto">
                <p class="text-sm font-bold mt-1 truncate">{{ $aset->aset_nama }}</p>
                <p class="text-sm font-mono font-bold tracking-wider">{{ $aset->nomor_label_kode }}</p>
                <p class="text-[11px] text-on-surface-variant font-mono break-all">{{ $aset->nomor_label_rest }}</p>
                <p class="text-xs text-on-surface-variant font-mono">{{ $aset->aset_kode }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </x-card>
</x-layouts::app>
