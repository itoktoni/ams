<?php /** @var App\Models\MasterGroup $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach
                <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
                <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            </x-slot:advanced>
        </x-filter>

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
                <th>Anggota</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    @foreach ($model::$sortColumns as $column)
                        @if($column === 'master_group_status')
                            @php $sc = $table->$column === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-zinc-100 text-zinc-600 border-zinc-200'; @endphp
                            <td><span class="inline-flex items-center text-[11px] font-semibold rounded-full px-2 py-0.5 border {{ $sc }}">{{ ucfirst($table->$column) }}</span></td>
                        @else
                            <td>{{ $table->$column ?? '-' }}</td>
                        @endif
                    @endforeach
                    <td>
                        @php $cntUsers = $table->has_users_count ?? $table->hasUsers()->count(); $cntTek = $table->has_teknisi_count ?? $table->hasTeknisi()->count(); $cnt = $cntUsers > 0 ? $cntUsers : $cntTek; $label = $cntUsers > 0 ? 'user' : 'teknisi'; @endphp
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                            <span class="material-symbols-outlined text-sm">engineering</span>
                            {{ $cnt }} {{ $label }}
                        </span>
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
                                    <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10 truncate max-w-[140px]">{{ Str::limit($table->master_group_kode, 24) }}</span>
                                    @php $sc = $table->master_group_status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-zinc-100 text-zinc-600 border-zinc-200'; @endphp
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $sc }}">{{ ucfirst($table->master_group_status) }}</span>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20">chevron_right</span>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->master_group_nama }}</p>
                                @if($table->master_group_keterangan)
                                <p class="text-xs text-on-surface-variant mt-1 line-clamp-2">{{ $table->master_group_keterangan }}</p>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Kode</p>
                                    <p class="text-xs font-mono font-semibold text-on-surface mt-1 truncate">{{ $table->master_group_kode }}</p>
                                </div>
                                <div class="bg-primary/5 rounded-xl px-2.5 py-2 border border-primary/10">
                                    <p class="text-[10px] font-bold tracking-widest text-primary/70 uppercase">Anggota</p>
                                    @php $cntU = $table->has_users_count ?? $table->hasUsers()->count(); $cntT = $table->has_teknisi_count ?? $table->hasTeknisi()->count(); $cntM = $cntU > 0 ? $cntU : $cntT; @endphp
                                    <p class="text-xs font-bold text-primary mt-1">{{ $cntM }} {{ $cntU > 0 ? 'user' : 'teknisi' }}</p>
                                </div>
                            </div>
                            @php $users = $table->hasUsers; $teknisis = $table->hasTeknisi; $list = ($users && $users->count()) ? $users : $teknisis; @endphp
                            @if($list && $list->count())
                            <div class="flex flex-wrap gap-1 mt-3">
                                @foreach($list->take(4) as $item)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-surface-container border border-outline-variant/20 text-[11px]"><span class="material-symbols-outlined text-sm">person</span>{{ $item->name ?? $item->teknisi_nama }}</span>
                                @endforeach
                                @if($list->count() > 4)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-surface-container text-[11px]">+{{ $list->count()-4 }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <a href="{{ moduleRoute('getUpdate', ['id' => $table->field_primary]) }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant hover:text-primary" title="Edit"><span class="material-symbols-outlined text-lg">edit</span></a>
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
