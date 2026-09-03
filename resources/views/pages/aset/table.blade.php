<?php /** @var App\Models\Aset $table */ ?>

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
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary">
                        @can('beritaAcara', $model)
                        <a href="{{ route('aset.getBeritaAcara', ['id' => $table->field_primary]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="Cetak Berita Acara">
                            <span class="material-symbols-outlined text-lg">print</span>
                        </a>
                        @endcan
                        <a href="{{ route('dokumen-aset.getTable') }}?filters[aset_dokumen_id_aset][$eq]={{ $table->field_primary }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-info/10 text-info hover:bg-info/20 transition-colors" title="Dokumen Terkait">
                            <span class="material-symbols-outlined text-lg">description</span>
                        </a>
                        @can('table', app(\App\Models\BukuPenyusutan::class))
                        <a href="{{ route('buku-penyusutan.getTable') }}?filters[buku_penyusutan_id_aset][$eq]={{ $table->field_primary }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-warning/10 text-warning hover:bg-warning/20 transition-colors" title="Detail Penyusutan">
                            <span class="material-symbols-outlined text-lg">trending_down</span>
                        </a>
                        @endcan
                        @can('recalc', $model)
                        <a href="{{ route('aset.getRecalc', ['id' => $table->field_primary]) }}" onclick="return confirm('Hapus kalkulasi lama & hitung ulang dari awal s/d bulan ini?')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-success/10 text-success hover:bg-success/20 transition-colors" title="Kalkulasi Ulang">
                            <span class="material-symbols-outlined text-lg">calculate</span>
                        </a>
                        @endcan
                    </x-table-action>
                    @foreach ($model::$sortColumns as $column)
                        @if ($column === 'aset_harga_perolehan')
                            <td class="text-right">{{ formatAngka((float) $table->$column) }}</td>
                        @elseif ($column === 'aset_tanggal_perolehan')
                            <td>{{ formatDate($table->$column) }}</td>
                        @elseif ($column === 'aset_kondisi')
                            <td>{{ $table->$column ? KondisiAsetEnum::getDescription($table->$column) : '-' }}</td>
                        @elseif ($column === 'aset_status')
                            <td>{{ $table->$column ? StatusAsetEnum::getDescription($table->$column) : '-' }}</td>
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
                    @php
                        $fotoUrl = $table->aset_foto ? fileUrl($table->aset_foto) : '';
                        $statusCfg = match($table->aset_status){
                            'aktif' => ['Aktif','bg-emerald-500','bg-emerald-50 text-emerald-700 border-emerald-200'],
                            'dipinjam' => ['Dipinjam','bg-amber-500','bg-amber-50 text-amber-700 border-amber-200'],
                            'maintenance' => ['Maintenance','bg-sky-500','bg-sky-50 text-sky-700 border-sky-200'],
                            'rusak' => ['Rusak','bg-red-500','bg-red-50 text-red-700 border-red-200'],
                            default => [$table->aset_status ? StatusAsetEnum::getDescription($table->aset_status) : '-', 'bg-zinc-400','bg-zinc-50 text-zinc-600 border-zinc-200']
                        };
                        $kondisiLabel = $table->aset_kondisi ? KondisiAsetEnum::getDescription($table->aset_kondisi) : '-';
                    @endphp
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            <div class="flex gap-3">
                                <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0 mt-1" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                    <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                </button>
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-surface-container to-surface-container-low border border-outline-variant/20 overflow-hidden shrink-0 flex items-center justify-center">
                                    @if($fotoUrl)
                                        <img src="{{ $fotoUrl }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-xl text-on-surface-variant/50">inventory_2</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0" onclick="event.stopPropagation(); window.location='{{ route('aset.getUpdate', ['id' => $table->field_primary]) }}'">
                                    <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->aset_nama }}</p>
                                    <p class="text-xs font-mono text-on-surface-variant truncate mt-1">{{ $table->aset_kode ?? '-' }}@if($table->aset_merek) <span class="text-on-surface-variant/30">•</span> {{ $table->aset_merek }}@endif</p>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20 shrink-0 mt-1">chevron_right</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-3 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold border {{ $statusCfg[2] }}" title="Status aset">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg[1] }}"></span>Status: {{ $statusCfg[0] }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-surface-container text-[10px] font-medium text-on-surface-variant border border-outline-variant/20" title="Kondisi fisik">
                                    Kondisi: {{ $kondisiLabel }}
                                </span>

                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Harga Perolehan</p>
                                    <p class="text-xs font-bold text-on-surface mt-1 leading-tight">{{ formatRupiah($table->aset_harga_perolehan) }}</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Nilai Sisa</p>
                                    <p class="text-xs font-bold text-on-surface mt-1 leading-tight">{{ formatRupiah($table->aset_nilai_sisa) }}</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2 col-span-2 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Tgl Perolehan</p>
                                        <p class="text-xs font-medium text-on-surface mt-1">{{ $table->aset_tanggal_perolehan ? formatDate($table->aset_tanggal_perolehan) : '-' }}</p>
                                    </div>
                                    <span class="text-[10px] font-medium text-on-surface-variant bg-white px-2 py-1 rounded-full border border-outline-variant/20">{{ $table->aset_masa_manfaat }} bln • {{ round($table->aset_masa_manfaat/12,1) }} th</span>
                                </div>
                            </div>
                            @php $cfs = getCustomFields($table->aset_custom_fields); @endphp
                            @if(!empty($cfs))
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach(array_slice($cfs, 0, 3) as $cfKey => $cfVal)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-primary/5 border border-primary/10 text-[11px] text-on-surface-variant"><span class="font-semibold text-on-surface">{{ customFieldLabel($cfKey) }}:</span>&nbsp;{{ is_array($cfVal) ? implode(', ', $cfVal) : $cfVal }}</span>
                                    @endforeach
                                    @if(count($cfs) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-surface-container text-[11px] text-on-surface-variant">+{{ count($cfs)-3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <a href="{{ route('aset.getBeritaAcara', ['id' => $table->field_primary]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary hover:bg-primary/20" title="Berita Acara"><span class="material-symbols-outlined text-lg">print</span></a>
                                <a href="{{ route('aset.getUpdate', ['id' => $table->field_primary]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant hover:text-primary" title="Detail"><span class="material-symbols-outlined text-lg">visibility</span></a>
                                <a href="{{ route('dokumen-aset.getTable') }}?filters[aset_dokumen_id_aset][$eq]={{ $table->field_primary }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-info/10 text-info" title="Dokumen"><span class="material-symbols-outlined text-lg">description</span></a>
                                <a href="{{ route('buku-penyusutan.getTable') }}?filters[buku_penyusutan_id_aset][$eq]={{ $table->field_primary }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-warning/10 text-warning" title="Penyusutan"><span class="material-symbols-outlined text-lg">trending_down</span></a>
                                <a href="{{ route('aset.getRecalc', ['id' => $table->field_primary]) }}" onclick="return confirm('Hapus & hitung ulang dari awal s/d bulan ini?')" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-success/10 text-success" title="Kalkulasi Ulang"><span class="material-symbols-outlined text-lg">calculate</span></a>
                                <span onclick="event.stopPropagation()"><x-table-action :model="$model" :id="$table->field_primary" /></span>
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
