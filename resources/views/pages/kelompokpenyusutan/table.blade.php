<?php /** @var App\Models\KelompokPenyusutan $table */ ?>

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
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    @foreach ($model::$sortColumns as $column)
                        @if ($column === 'kelompok_penyusutan_masa_manfaat')
                            <td>{{ $table->$column !== null ? $table->$column.' Tahun' : '-' }}</td>
                        @elseif ($column === 'kelompok_penyusutan_tarif')
                            <td>{{ $table->$column !== null ? rtrim(rtrim(number_format((float)$table->$column, 2, ',', '.'), '0'), ',').'%' : '-' }}</td>
                        @elseif ($column === 'kelompok_penyusutan_metode')
                            <td>{{ $table->$column ? MetodePenyusutanEnum::getDescription($table->$column) : '-' }}</td>
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
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                        <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                    </button>
                                    <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10">{{ $table->kelompok_penyusutan_kode ?? '-' }}</span>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20" onclick="event.stopPropagation(); window.location='{{ route('kelompok-penyusutan.getUpdate', ['id' => $table->field_primary]) }}'">chevron_right</span>
                            </div>
                            <div class="mt-3" onclick="event.stopPropagation(); window.location='{{ route('kelompok-penyusutan.getUpdate', ['id' => $table->field_primary]) }}'">
                                <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->kelompok_penyusutan_nama }}</p>
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-surface-container text-[10px] font-medium text-on-surface-variant border border-outline-variant/20">{{ $table->kelompok_penyusutan_metode ? MetodePenyusutanEnum::getDescription($table->kelompok_penyusutan_metode) : '-' }}</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold">{{ $table->kelompok_penyusutan_tarif !== null ? rtrim(rtrim(number_format((float)$table->kelompok_penyusutan_tarif,2,',','.'),'0'),',').'%' : '-' }}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-surface-container-low text-[10px] font-medium text-on-surface-variant"><span class="material-symbols-outlined text-xs">schedule</span>{{ $table->kelompok_penyusutan_masa_manfaat ?? '-' }} Tahun</span>
                            </div>
                            @if($table->kelompok_penyusutan_keterangan)
                                <p class="text-xs text-on-surface-variant leading-relaxed mt-3 line-clamp-2 break-words">{{ $table->kelompok_penyusutan_keterangan }}</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <a href="{{ route('kelompok-penyusutan.getUpdate', ['id' => $table->field_primary]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant hover:text-primary" title="Detail"><span class="material-symbols-outlined text-lg">visibility</span></a>
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
