<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => 'Report Penghapusan']]" />

    <div class="content mt-4 lg:mt-0">
        <form method="GET" action="{{ route('laporan-penghapusan.getPrint') }}" target="_blank">
            <x-card label="Filter — Report Penghapusan" icon="filter_alt">
                <x-input col="4" type="date" name="dari" label="Dari Tanggal" :value="now()->startOfMonth()->toDateString()" />
                <x-input col="4" type="date" name="sampai" label="Sampai Tanggal" :value="now()->toDateString()" />
                <x-select col="4" name="status" label="Status" :options="$statusOptions" placeholder="Semua" />
            </x-card>

            <x-action :action="['print']">
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg bg-primary px-4 text-sm font-semibold text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-xl">visibility</span> Tampilkan
                </button>
                <button type="submit" formaction="{{ route('laporan-penghapusan.getExportExcel') }}" formtarget="_self" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg border border-outline-variant px-4 text-sm font-semibold text-on-surface-variant transition-all hover:bg-surface-container">
                    <span class="material-symbols-outlined text-xl">download</span> Export Excel
                </button>
            </x-action>
        </form>

        <div class="h-16"></div>
    </div>
</x-layouts::app>
