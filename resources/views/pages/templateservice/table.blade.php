<?php /** @var App\Models\TemplateService $table */ ?>

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
                <th class="text-left whitespace-nowrap">Daftar Aset</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                @php
                    $daftarAset = $table->template_service_id_kategori
                        ? \App\Models\Aset::where('aset_id_kategori', $table->template_service_id_kategori)->limit(5)->pluck('aset_nama', 'aset_id')->toArray()
                        : [];
                    $totalAset = $table->template_service_id_kategori ? \App\Models\Aset::where('aset_id_kategori', $table->template_service_id_kategori)->count() : 0;
                @endphp
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    @foreach ($model::$sortColumns as $column)
                    <td>
                        @if($column === 'template_service_id_kategori')
                            {{ $table->hasKategori?->aset_kategori_nama ?? ($table->$column ?? '-') }}@if($table->hasKategori?->aset_kategori_kode) <span class="text-xs text-on-surface-variant">({{ $table->hasKategori->aset_kategori_kode }})</span>@endif
                        @else
                            {{ $table->$column ?? '-' }}
                        @endif
                    </td>
                    @endforeach
                    <td class="min-w-[220px]">
                        @if(empty($daftarAset))
                            <span class="text-xs text-on-surface-variant">—</span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach($daftarAset as $aid => $anama)
                                    <a href="{{ route('aset.getUpdate', ['id' => $aid]) }}" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-primary/5 border border-primary/10 text-primary hover:bg-primary/10">{{ Str::limit($anama, 18) }}</a>
                                @endforeach
                                @if($totalAset > 5)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-surface-container border border-outline-variant/20 text-on-surface-variant">+{{ $totalAset - 5 }} lagi</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-on-surface-variant mt-1">{{ $totalAset }} aset di kategori ini</p>
                        @endif
                    </td>
                </tr>
                @endforeach
            </x-slot:body>

                        <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                        <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                    </button>
                                    @php $titleField = $model::field_name(); $titleVal = $table->{$titleField} ?? $table->field_primary; @endphp
                                    <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10 truncate max-w-[140px]">{{ Str::limit($titleVal, 24) }}</span>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20">chevron_right</span>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->{$model::field_name()} ?? $table->field_primary }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                @foreach(array_slice($model::$sortColumns, 0, 4) as $col)
                                    @php $val = $table->{$col} ?? null; @endphp
                                    <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">{{ formatLabel($col) }}</p>
                                        <p class="text-xs font-medium text-on-surface mt-1 truncate">
                                            @if($col === 'template_service_id_kategori')
                                                {{ $table->hasKategori?->aset_kategori_nama ?? ($val ?? '-') }}
                                            @elseif(str_contains($col, 'tanggal') && $val)
                                                {{ formatDate($val) }}
                                            @elseif(str_contains($col, 'harga') || str_contains($col, 'tarif') || str_contains($col, 'total') || str_contains($col, 'biaya') || str_contains($col, 'nominal'))
                                                {{ is_numeric($val) ? formatRupiah($val) : ($val ?? '-') }}
                                            @elseif(str_contains($col, 'status') || str_contains($col, 'kondisi') || str_contains($col, 'level') || str_contains($col, 'urgensi'))
                                                {{ $val ?? '-' }}
                                            @else
                                                {{ Str::limit($val ?? '-', 24) }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            @php
                                $daftarAsetM = $table->template_service_id_kategori ? \App\Models\Aset::where('aset_id_kategori', $table->template_service_id_kategori)->limit(4)->pluck('aset_nama','aset_id')->toArray() : [];
                                $totalAsetM = $table->template_service_id_kategori ? \App\Models\Aset::where('aset_id_kategori', $table->template_service_id_kategori)->count() : 0;
                            @endphp
                            @if(!empty($daftarAsetM))
                            <div class="mt-3">
                                <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Daftar Aset ({{ $totalAsetM }})</p>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    @foreach($daftarAsetM as $aid => $anama)
                                        <a href="{{ route('aset.getUpdate', ['id' => $aid]) }}" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-primary/5 border border-primary/10 text-primary">{{ Str::limit($anama, 16) }}</a>
                                    @endforeach
                                    @if($totalAsetM > 4)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-surface-container border border-outline-variant/20 text-on-surface-variant">+{{ $totalAsetM - 4 }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant/30"><span class="material-symbols-outlined text-lg">visibility</span></span>
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
