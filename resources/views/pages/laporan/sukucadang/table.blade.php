<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => 'Report Suku Cadang']]" />

    <div class="content mt-4 lg:mt-0">
        <form method="GET" action="{{ route('laporan-sukucadang.getPrint') }}" target="_blank">
            <x-card label="Filter — Report Suku Cadang" icon="filter_alt">
                <p class="text-sm text-on-surface-variant col-span-12">Laporan ini menampilkan seluruh data suku cadang. Klik Tampilkan untuk melihat, atau Export Excel untuk mengunduh.</p>
            </x-card>

            <x-action :action="['print']">
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg bg-primary px-4 text-sm font-semibold text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-xl">visibility</span> Tampilkan
                </button>
                <button type="submit" formaction="{{ route('laporan-sukucadang.getExportExcel') }}" formtarget="_self" class="inline-flex h-10 items-center justify-center gap-1 rounded-lg border border-outline-variant px-4 text-sm font-semibold text-on-surface-variant transition-all hover:bg-surface-container">
                    <span class="material-symbols-outlined text-xl">download</span> Export Excel
                </button>
            </x-action>
        </form>

        <div class="h-16"></div>
    </div>
</x-layouts::app>
