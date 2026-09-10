<?php /** @var App\Models\Department $table */ ?>

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
                <x-table-sort field="department_kode" label="Kode" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="department_nama" label="Department" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="department_periode" label="Periode" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="department_budget" label="Anggaran" :sortField="$sortField" :sortDir="$sortDir" />
                <th class="text-right whitespace-nowrap">Terpakai</th>
                <th class="text-right whitespace-nowrap">Sisa</th>
                <th class="text-right whitespace-nowrap">Tersedia</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                @php
                    $sisa = $table->sisa;
                    $tersedia = $table->tersedia;
                    $pending = $table->pending;
                    $pct = $table->department_budget > 0 ? min(100, (($table->department_budget_terpakai + $pending) / $table->department_budget) * 100) : 0;
                    $over = $tersedia < 0;
                @endphp
                <tr class="hover:bg-surface-container-low/50">
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td class="font-mono font-semibold text-primary whitespace-nowrap">{{ $table->department_kode }}</td>
                    <td class="font-medium whitespace-nowrap">{{ $table->department_nama }}</td>
                    <td class="whitespace-nowrap"><span class="inline-flex px-2 py-0.5 rounded-full text-xs border bg-surface-container">{{ ucfirst($table->department_periode ?? '-') }}</span></td>
                    <td class="text-right font-mono whitespace-nowrap">{{ formatRupiah($table->department_budget) }}</td>
                    <td class="text-right font-mono whitespace-nowrap">{{ formatRupiah($table->department_budget_terpakai) }}</td>
                    <td class="text-right font-mono whitespace-nowrap {{ $sisa < 0 ? 'text-error font-bold' : '' }}">{{ formatRupiah($sisa) }}</td>
                    <td class="text-right">
                        <div class="flex flex-col items-end gap-1">
                            <span class="font-mono font-semibold {{ $over ? 'text-error' : 'text-primary' }}">{{ formatRupiah($tersedia) }}</span>
                            <div class="w-20 h-1.5 bg-outline-variant/20 rounded-full overflow-hidden">
                                <div class="h-full {{ $over ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    @php
                        $sisa = $table->sisa;
                        $tersedia = $table->tersedia;
                        $pct = $table->department_budget > 0 ? min(100, (($table->department_budget_terpakai + $table->pending) / $table->department_budget) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-on-surface truncate">{{ $table->department_nama }}</p>
                                    <p class="text-xs font-mono text-primary mt-0.5">{{ $table->department_kode }} • {{ ucfirst($table->department_periode ?? '-') }}</p>
                                </div>
                                <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                    <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Anggaran</p>
                                    <p class="text-xs font-bold font-mono text-on-surface mt-1">{{ formatRupiah($table->department_budget) }}</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Terpakai</p>
                                    <p class="text-xs font-bold font-mono text-on-surface mt-1">{{ formatRupiah($table->department_budget_terpakai) }}</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Sisa</p>
                                    <p class="text-xs font-bold font-mono mt-1 {{ $sisa < 0 ? 'text-error' : 'text-on-surface' }}">{{ formatRupiah($sisa) }}</p>
                                </div>
                                <div class="rounded-xl px-2.5 py-2 border {{ $tersedia < 0 ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}">
                                    <p class="text-[10px] font-bold tracking-widest uppercase {{ $tersedia < 0 ? 'text-error' : 'text-primary' }}">Tersedia</p>
                                    <p class="text-xs font-bold font-mono mt-1 {{ $tersedia < 0 ? 'text-error' : 'text-primary' }}">{{ formatRupiah($tersedia) }}</p>
                                    <div class="mt-1.5 h-1 bg-outline-variant/20 rounded-full overflow-hidden">
                                        <div class="h-full {{ $tersedia < 0 ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <a href="{{ route('department.getUpdate', ['id' => $table->field_primary]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined text-lg">visibility</span></a>
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
