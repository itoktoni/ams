<?php /** @var App\Models\BukuPenyusutan $table */ ?>

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

        @if (! empty($summary))
            @php $aset = $summary['aset']; @endphp
            <div class="bg-white border border-outline-variant/20 rounded-2xl p-5 mb-6 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="flex-1">
                        <p class="text-[11px] font-bold tracking-[0.14em] text-primary uppercase">Aset</p>
                        <a href="{{ route('aset.getUpdate', ['id' => $aset->aset_id]) }}" class="inline-flex items-center gap-1.5 group">
                            <h2 class="text-lg font-bold text-on-surface mt-1 group-hover:text-primary transition-colors">{{ $aset->aset_nama }} <span class="text-sm font-normal text-on-surface-variant">— {{ $aset->aset_kode }}</span> <span class="material-symbols-outlined text-base align-middle opacity-60 group-hover:opacity-100">open_in_new</span></h2>
                        </a>
                        <p class="text-xs text-on-surface-variant mt-1">{{ $aset->hasKategori?->aset_kategori_nama ?? '-' }} • {{ $aset->hasLokasi?->aset_lokasi_nama ?? '-' }} • {{ $aset->aset_merek ?? '-' }}</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-xs font-medium">Harga {{ formatRupiah($aset->aset_harga_perolehan) }}</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-xs font-medium">Sisa {{ formatRupiah($aset->aset_nilai_sisa) }}</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold">{{ $aset->aset_masa_manfaat }} bln ({{ round($aset->aset_masa_manfaat/12,1) }} th) • {{ $aset->aset_metode_penyusutan ? MetodePenyusutanEnum::getDescription($aset->aset_metode_penyusutan) : '-' }}</span>
                        </div>
                    </div>
                    <a href="{{ route('aset.getRecalc', ['id' => $aset->aset_id]) }}" onclick="return confirm('Hapus & hitung ulang dari awal s/d bulan ini?')" class="inline-flex items-center gap-2 h-10 px-4 bg-success text-white rounded-xl text-sm font-semibold hover:bg-success/90 shrink-0">
                        <span class="material-symbols-outlined text-lg">calculate</span> Kalkulasi Ulang
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-5">
                    <div class="bg-surface-container-low rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Per Bulan</p>
                        <p class="text-sm font-bold text-on-surface mt-1">{{ formatRupiah($summary['perBulan']) }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Akumulasi</p>
                        <p class="text-sm font-bold text-primary mt-1">{{ formatRupiah($summary['akumulasi']) }}</p>
                        <p class="text-[11px] text-on-surface-variant">{{ $summary['total'] }} / {{ $aset->aset_masa_manfaat }} bulan ({{ $summary['progress'] }}%)</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-3 text-center border-2 border-primary/20">
                        <p class="text-[10px] font-bold tracking-widest text-primary uppercase">Nilai Buku Sekarang</p>
                        <p class="text-base font-bold text-on-surface mt-1">{{ formatRupiah($summary['nilaiBuku']) }}</p>
                        <p class="text-[11px] text-on-surface-variant">Periode {{ $summary['lastPeriode'] ?? '-' }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Sisa</p>
                        <p class="text-sm font-bold text-on-surface mt-1">{{ $summary['sisaBulan'] }} bulan</p>
                        <p class="text-[11px] text-on-surface-variant">Maks {{ formatRupiah((float)$aset->aset_harga_perolehan - (float)$aset->aset_nilai_sisa) }}</p>
                    </div>
                </div>
                <div class="mt-4 h-2 bg-surface-container rounded-full overflow-hidden">
                    <div class="h-full bg-primary transition-all" style="width: {{ min(100, $summary['progress']) }}%"></div>
                </div>
            </div>
        @endif

        {{-- Table --}}
        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        @php $canAct = auth()->user()?->can('update', $model) || auth()->user()?->can('delete', $model); @endphp
        <x-table>
            <x-slot:head>
                @if($canAct)
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <th class="text-left whitespace-nowrap">Aset</th>
                @endif
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    @if($canAct)
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td class="min-w-[160px]">
                        @php $aset = $table->hasAset; @endphp
                        @if($aset)
                            <a href="{{ route('aset.getUpdate', ['id' => $aset->aset_id]) }}" class="inline-flex items-center gap-1.5 hover:text-primary transition-colors">
                                <span class="font-medium text-on-surface truncate">{{ $aset->aset_nama }}</span>
                                <span class="text-xs font-mono text-on-surface-variant">— {{ $aset->aset_kode }}</span>
                            </a>
                        @else
                            <span class="text-on-surface-variant">#{{ $table->buku_penyusutan_id_aset }}</span>
                        @endif
                    </td>
                    @endif
                    @foreach ($model::$sortColumns as $column)
                        @if (in_array($column, ['buku_penyusutan_nilai','buku_penyusutan_akumulasi','buku_penyusutan_nilai_buku']))
                            <td class="text-right tabular-nums">{{ formatRupiah($table->$column) }}</td>
                        @else
                            <td>{{ $table->$column }}</td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    @php $asetM = $table->hasAset; @endphp
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                        <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                    </button>
                                    <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10">{{ $table->buku_penyusutan_periode ?? 'Buku' }}</span>
                                    @if(empty($summary) && $asetM)
                                        <span class="text-xs text-on-surface-variant truncate hidden sm:inline">{{ $asetM->aset_nama }} — {{ $asetM->aset_kode }}</span>
                                    @endif
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20" onclick="event.stopPropagation(); window.location='{{ route('buku-penyusutan.getUpdate', ['id' => $table->field_primary]) }}'">chevron_right</span>
                            </div>
                            @if(empty($summary) && $asetM)
                                <a href="{{ route('aset.getUpdate', ['id' => $asetM->aset_id]) }}" class="flex items-center gap-2 mt-3" onclick="event.stopPropagation()">
                                    <span class="material-symbols-outlined text-sm text-primary">inventory_2</span>
                                    <span class="text-xs font-bold text-primary truncate">{{ $asetM->aset_nama }} — {{ $asetM->aset_kode }}</span>
                                </a>
                            @endif
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Periode</p>
                                <p class="text-xs font-medium text-primary truncate">{{ $table->buku_penyusutan_periode ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Nilai Buku</p>
                                <p class="text-xs font-medium text-on-surface">{{ isset($table->buku_penyusutan_nilai_buku) ? formatRupiah($table->buku_penyusutan_nilai_buku) : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Nilai Penyusutan</p>
                                <p class="text-xs font-medium text-on-surface">{{ formatRupiah($table->buku_penyusutan_nilai ?? $table->buku_penyusutan_debet ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Akumulasi</p>
                                <p class="text-xs font-medium text-on-surface">{{ formatRupiah($table->buku_penyusutan_akumulasi) }}</p>
                            </div>
                        </div>
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
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
