<?php /** @var App\Models\DaftarTunggu $table */ ?>

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
                        @if($table->daftar_tunggu_status === 'menunggu')
                        <a href="{{ route('daftar-tunggu.getConvert', ['id' => $table->field_primary]) }}" onclick="return confirm('Convert jadi peminjaman?')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-success/10 text-success hover:bg-success/20" title="Convert ke Peminjaman">
                            <span class="material-symbols-outlined text-lg">swap_horiz</span>
                        </a>
                        @endif
                    </x-table-action>
                    @foreach ($model::$sortColumns as $column)
                        @if($column === 'daftar_tunggu_id_aset')
                            <td>
                                @if($table->hasAset)
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="font-medium text-on-surface">{{ $table->hasAset->aset_nama }} — {{ $table->hasAset->aset_kode }}</span>
                                    </span>
                                @else
                                    <span class="text-on-surface-variant">{{ $table->$column }}</span>
                                @endif
                            </td>
                        @elseif($column === 'daftar_tunggu_status')
                            <td>
                                @php
                                    $statusColor = match($table->daftar_tunggu_status) {
                                        'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'selesai' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center text-[11px] font-semibold rounded-full px-2 py-0.5 border {{ $statusColor }}">{{ ucfirst($table->$column) }}</span>
                            </td>
                        @elseif($column === 'daftar_tunggu_tanggal_mulai')
                            <td>{{ $table->$column ? formatDate($table->$column) : '-' }}</td>
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
                                    @if($table->hasAset)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10 truncate max-w-[160px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>{{ $table->hasAset->aset_nama }}
                                        </span>
                                    @else
                                        <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10 truncate max-w-[140px]">{{ $table->field_primary }}</span>
                                    @endif
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20">chevron_right</span>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->hasAset?->aset_nama ?? 'Aset #' . $table->field_primary }}</p>
                                @if($table->hasAset && $table->hasAset->aset_kode)
                                    <p class="text-xs font-mono text-on-surface-variant mt-1">{{ $table->hasAset->aset_kode }}</p>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                @foreach(array_slice($model::$sortColumns, 0, 4) as $col)
                                    @if($col === 'daftar_tunggu_id_aset') @continue @endif
                                    @php $val = $table->{$col} ?? null; @endphp
                                    <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">{{ formatLabel($col) }}</p>
                                        <p class="text-xs font-medium text-on-surface mt-1 truncate">
                                            @if(str_contains($col, 'tanggal') && $val)
                                                {{ formatDate($val) }}
                                            @elseif($col === 'daftar_tunggu_status')
                                                {{ ucfirst($val ?? '-') }}
                                            @else
                                                {{ Str::limit($val ?? '-', 24) }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">Antrian #{{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
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
