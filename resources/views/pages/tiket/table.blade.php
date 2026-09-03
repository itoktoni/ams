<?php /** @var App\Models\Tiket $table */ ?>

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
                <th class="text-left whitespace-nowrap">Aset</th>
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td class="min-w-[160px] whitespace-nowrap">{{ $table->hasAset?->aset_nama ? $table->hasAset->aset_nama.' — '.$table->hasAset->aset_kode : ($table->tiket_id_aset ?? '-') }}</td>
                    @foreach ($model::$sortColumns as $column)
                    <td>
                        @if(str_contains($column, 'tanggal') && $table->$column)
                            {{ formatDate($table->$column) }}
                        @elseif(str_contains($column, 'id_aset') && $table->hasAset)
                            {{ $table->hasAset->aset_nama }} — {{ $table->hasAset->aset_kode }}
                        @else
                            {{ $table->$column ?? '-' }}
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </x-slot:body>

                        <x-slot:mobile>
    <x-table-mobile-select :model="$model" :total="$data"/>
    <div class="bg-white rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.05)] overflow-hidden divide-y divide-outline-variant/40" id="mBody">
        @foreach($data as $table)
        @php
            $sMap = [
                'buka'          => ['bg-sky-500', 'text-sky-600'],
                'ditugaskan'    => ['bg-indigo-500', 'text-indigo-600'],
                'progres'       => ['bg-blue-500', 'text-blue-600'],
                'menunggu_part' => ['bg-amber-500', 'text-amber-600'],
                'selesai'       => ['bg-emerald-500', 'text-emerald-600'],
                'terverifikasi' => ['bg-green-500', 'text-green-600'],
            ];
            [$scDot, $scText] = $sMap[$table->tiket_status] ?? ['bg-gray-400', 'text-gray-600'];
            $statusLabel = \App\Enums\Tiket\StatusTiketEnum::getDescription($table->tiket_status);
            $urgensiLabel = ucfirst($table->tiket_tingkat_urgensi ?? '-');
            $telat = $table->tiket_jatuh_tempo && now()->gt($table->tiket_jatuh_tempo)
                     && ! in_array($table->tiket_status, ['selesai', 'terverifikasi'], true);
            $aset = $table->has_aset;
            $lokasi = $table->has_lokasi;
        @endphp
        <div data-id="{{ $table->field_primary }}" onclick="mToggle(this)"
             class="bg-white p-4">
            {{-- judul + checkbox --}}
            <div class="flex items-start justify-between gap-3">
                <p class="text-[15px] font-semibold text-on-surface leading-snug line-clamp-2">{{ $table->tiket_judul }}</p>
                <button class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                        onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                    <span data-check class="icon-[tabler--circle] size-4 text-base-content/20"></span>
                </button>
            </div>

            {{-- nomor · urgensi · telat --}}
            <div class="mt-1 flex items-center gap-1.5 text-xs text-on-surface-variant">
                <span class="font-mono">{{ $table->tiket_nomor }}</span>
                <span class="text-on-surface-variant/30">·</span>
                <span>{{ $urgensiLabel }}</span>
                @if($telat)<span>· Telat</span>@endif
            </div>

            {{-- aset · lokasi --}}
            <div class="mt-2 flex items-center gap-1 text-xs text-on-surface-variant">
                <span class="truncate">{{ $aset?->aset_nama ?? '-' }}</span>
                @if($lokasi)
                <span class="text-on-surface-variant/30 shrink-0">·</span>
                <span class="truncate">{{ $lokasi->aset_lokasi_nama }}</span>
                @endif
            </div>

            {{-- footer: status kiri, tanggal + aksi kanan --}}
            <div class="mt-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $scDot }}"></span>
                    <span class="text-xs font-medium {{ $scText }}">{{ $statusLabel }}</span>
                </div>
                <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                    <span class="text-xs text-on-surface-variant/60">{{ $table->tiket_tanggal_lapor ? formatDate($table->tiket_tanggal_lapor) : '-' }}</span>
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
