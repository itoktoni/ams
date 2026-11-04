<?php /** @var array $cards */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => 'Laporan']]" />
    <div class="content mt-4 lg:mt-0">
        <x-card label="Pusat Laporan" icon="assessment">
            <div class="col-span-12 grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($cards as $c)
                <a href="{{ $c['url'] }}" class="p-4 rounded-xl border border-outline-variant bg-surface-container-low hover:bg-surface-container transition block">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-3xl">{{ $c['icon'] }}</span>
                        <div class="flex-1">
                            <p class="font-bold text-sm">{{ $c['title'] }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $c['desc'] }}</p>
                        </div>
                        @if($c['count'] !== null)
                        <span class="text-xl font-bold text-primary">{{ $c['count'] }}</span>
                        @endif
                    </div>
                </a>
                @endforeach
                <a href="{{ route('tiket.getReportBulanan') }}" class="p-4 rounded-xl border border-outline-variant bg-surface-container-low hover:bg-surface-container transition block">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-3xl">engineering</span>
                        <div class="flex-1">
                            <p class="font-bold text-sm">Report Perbaikan Teknisi</p>
                            <p class="text-xs text-on-surface-variant">Bukti pekerjaan harian teknisi</p>
                        </div>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</x-layouts::app>
