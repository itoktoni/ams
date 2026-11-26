<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => 'Report Aset']]" />

    <div class="content mt-4 lg:mt-0">
        <form method="GET" action="{{ route('laporan-aset.getPrint') }}" target="_blank">
            <x-card label="Filter — Report Aset" icon="filter_alt">
                <x-input col="4" type="date" name="dari" label="Dari Tanggal" :value="now()->startOfMonth()->toDateString()" />
                <x-input col="4" type="date" name="sampai" label="Sampai Tanggal" :value="now()->toDateString()" />
                <x-select col="4" name="status" label="Status" :options="$statusOptions" placeholder="Semua" />
                <x-select col="4" name="kondisi" label="Kondisi" :options="$kondisiOptions" placeholder="Semua" />
                <x-select col="4" name="divisi" label="Divisi" :options="$divisiOptions" placeholder="Semua Divisi" />
                <x-select col="4" name="ruangan" label="Ruangan" :options="$ruanganOptions" placeholder="Semua Ruangan" />
            </x-card>

            <x-action :action="['print']">
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg bg-primary px-4 text-sm font-semibold text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-xl">visibility</span> Tampilkan
                </button>
                <button type="submit" formaction="{{ route('laporan-aset.getExportExcel') }}" formtarget="_self" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg border border-outline-variant px-4 text-sm font-semibold text-on-surface-variant transition-all hover:bg-surface-container">
                    <span class="material-symbols-outlined text-xl">download</span> Export Excel
                </button>
                <button type="submit" formaction="{{ route('laporan-aset.getExportPdf') }}" formtarget="_blank" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg bg-error px-4 text-sm font-semibold text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-xl">picture_as_pdf</span> Lihat PDF
                </button>
            </x-action>
        </form>

        <div class="h-16"></div>
    </div>
</x-layouts::app>
