<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => 'Report Persetujuan']]" />

    <div class="content mt-4 lg:mt-0">
        <form method="GET" action="{{ route('laporan-persetujuan.getPrint') }}" target="_blank">
            <x-card label="Filter — Report Persetujuan" icon="filter_alt">
                <x-select col="6" name="status" label="Status" :options="$statusOptions" placeholder="Semua" />
                <x-select col="6" name="level" label="Level" :options="$levelOptions" placeholder="Semua" />
            </x-card>

            <x-action :action="['print']">
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg bg-primary px-4 text-sm font-semibold text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-xl">visibility</span> Tampilkan
                </button>
                <button type="submit" formaction="{{ route('laporan-persetujuan.getExportExcel') }}" formtarget="_self" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg border border-outline-variant px-4 text-sm font-semibold text-on-surface-variant transition-all hover:bg-surface-container">
                    <span class="material-symbols-outlined text-xl">download</span> Export Excel
                </button>
                <button type="submit" formaction="{{ route('laporan-persetujuan.getExportPdf') }}" formtarget="_blank" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg bg-error px-4 text-sm font-semibold text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-xl">picture_as_pdf</span> Lihat PDF
                </button>

            </x-action>
        </form>

        <div class="h-16"></div>
    </div>
</x-layouts::app>
