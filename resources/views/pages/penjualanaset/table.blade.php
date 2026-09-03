<?php /** @var App\Models\PenjualanAset $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        {{-- Filters --}}
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach

                <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
                <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            </x-slot:advanced>
        </x-filter>

        {{-- Table --}}
        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <th class="whitespace-nowrap">Aset</th>
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td class="min-w-[220px]">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center shrink-0 overflow-hidden">
                                @if($table->hasAset?->aset_foto)
                                    <img src="{{ fileUrl($table->hasAset->aset_foto) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">inventory_2</span>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate max-w-[220px]">{{ $table->hasAset?->aset_nama ?? '—' }}</p>
                                <p class="text-[11px] text-on-surface-variant truncate max-w-[220px]">{{ $table->hasAset?->aset_kode ?? '' }} • {{ $table->hasAset?->hasKategori?->aset_kategori_nama ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    @foreach ($model::$sortColumns as $column)
                    <td>{{ $table->$column }}</td>
                    @endforeach
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm" data-id="{{ $table->field_primary }}">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center shrink-0 overflow-hidden">
                                @if($table->hasAset?->aset_foto)
                                    <img src="{{ fileUrl($table->hasAset->aset_foto) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <span class="material-symbols-outlined text-on-surface-variant">inventory_2</span>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-on-surface truncate">{{ $table->penjualan_aset_nomor }}</p>
                                <p class="text-xs font-medium text-primary truncate">{{ $table->hasAset?->aset_nama ?? '—' }}</p>
                                <p class="text-[11px] text-on-surface-variant truncate">{{ $table->hasAset?->aset_kode ?? '' }} • {{ $table->hasAset?->hasKategori?->aset_kategori_nama ?? '' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Status</p>
                                <p class="text-xs font-medium text-primary truncate">{{ $table->penjualan_aset_status ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Tanggal Request</p>
                                <p class="text-xs font-medium text-on-surface">{{ $table->penjualan_aset_tanggal_request ? \Carbon\Carbon::parse($table->penjualan_aset_tanggal_request)->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Harga Jual</p>
                                <p class="text-xs font-medium text-on-surface">Rp {{ $table->penjualan_aset_harga_jual ? number_format($table->penjualan_aset_harga_jual,0,',','.') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Appraisal</p>
                                <p class="text-xs font-medium text-on-surface">Rp {{ $table->penjualan_aset_harga_appraisal ? number_format($table->penjualan_aset_harga_appraisal,0,',','.') : '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->field_primary }}</span>
                            <div class="flex gap-1" onclick="event.stopPropagation()">
                                <x-table-action :model="$model" :id="$table->field_primary" />
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-slot:mobile>

        </x-table>

        <x-pagination :paginator="$data" />
        <x-action :model="$model" :action="['create', 'delete']"/>

    </div>

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
</x-layouts::app>
