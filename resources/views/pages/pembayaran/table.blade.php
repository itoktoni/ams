<?php /** @var App\Models\Pembayaran $table */ ?>

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
                        @if ($column === 'pembayaran_jenis')
                            <td>{{ \App\Enums\JenisPembayaranEnum::getDescription($table->$column) }}</td>
                        @elseif ($column === 'pembayaran_status')
                            <td>
                                @php
                                    $sc = match($table->$column){
                                        'sudah_bayar' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'terlambat' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center text-[11px] font-semibold rounded-full px-2 py-0.5 border {{ $sc }}">{{ \App\Enums\StatusPembayaranEnum::getDescription($table->$column) }}</span>
                            </td>
                        @elseif ($column === 'pembayaran_tanggal_jatuh_tempo')
                            <td>{{ $table->$column ? formatDate($table->$column) : '-' }}</td>
                        @elseif ($column === 'pembayaran_nominal')
                            <td class="text-right">{{ formatRupiah($table->$column) }}</td>
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
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm" data-id="{{ $table->field_primary }}">
                        <p class="text-sm font-bold text-on-surface truncate mb-3">{{ $table->pembayaran_nama }}</p>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Jenis</p>
                                <p class="text-xs font-medium text-primary truncate">{{ \App\Enums\JenisPembayaranEnum::getDescription($table->pembayaran_jenis) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Status</p>
                                <p class="text-xs font-medium truncate">{{ \App\Enums\StatusPembayaranEnum::getDescription($table->pembayaran_status) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Jatuh Tempo</p>
                                <p class="text-xs font-medium truncate">{{ $table->pembayaran_tanggal_jatuh_tempo ? formatDate($table->pembayaran_tanggal_jatuh_tempo) : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Nominal</p>
                                <p class="text-xs font-medium truncate">{{ formatRupiah($table->pembayaran_nominal) }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">No. Pelanggan</p>
                                <p class="text-xs font-medium truncate">{{ $table->pembayaran_nomor_pelanggan ?? '-' }}</p>
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
