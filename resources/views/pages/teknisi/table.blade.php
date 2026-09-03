<?php /** @var App\Models\Teknisi $table */ ?>

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
                    <td>{{ $table->$column }}</td>
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
                                    <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10">{{ $table->teknisi_kode ?? '-' }}</span>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20" onclick="event.stopPropagation(); window.location='{{ route('teknisi.getUpdate', ['id' => $table->field_primary]) }}'">chevron_right</span>
                            </div>
                            <div class="mt-3" onclick="event.stopPropagation(); window.location='{{ route('teknisi.getUpdate', ['id' => $table->field_primary]) }}'">
                                <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->teknisi_nama }}</p>
                                @if($table->teknisi_telepon)
                                    <p class="text-xs text-on-surface-variant mt-1">{{ $table->teknisi_telepon }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @php $st = $table->teknisi_status; $stColor = match($st){'tersedia'=>'bg-emerald-50 text-emerald-700 border-emerald-200','sibuk'=>'bg-amber-50 text-amber-700 border-amber-200','offline'=>'bg-zinc-50 text-zinc-600 border-zinc-200',default=>'bg-surface-container text-on-surface-variant border-outline-variant/20'}; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold border {{ $stColor }}"><span class="w-1.5 h-1.5 rounded-full {{ str_contains($stColor,'emerald')?'bg-emerald-500':(str_contains($stColor,'amber')?'bg-amber-500':'bg-zinc-400') }}"></span>{{ $st ? \Illuminate\Support\Str::title($st) : '-' }}</span>
                                @if($table->teknisi_rating)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold"><span class="material-symbols-outlined text-xs">star</span>{{ number_format((float)$table->teknisi_rating,1) }}</span>
                                @endif
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-surface-container text-[10px] font-medium text-on-surface-variant border border-outline-variant/20">{{ $table->teknisi_total_tiket ?? 0 }} tiket</span>
                                @if($table->teknisi_zona)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-medium">{{ is_array($table->teknisi_zona) ? implode(', ', $table->teknisi_zona) : $table->teknisi_zona }}</span>
                                @endif
                            </div>
                            @if($table->teknisi_keahlian)
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach((is_array($table->teknisi_keahlian) ? $table->teknisi_keahlian : explode(',',$table->teknisi_keahlian)) as $sk)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-primary/5 border border-primary/10 text-[11px] text-on-surface-variant">{{ trim($sk) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <a href="{{ route('teknisi.getUpdate', ['id' => $table->field_primary]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant hover:text-primary" title="Detail"><span class="material-symbols-outlined text-lg">visibility</span></a>
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
