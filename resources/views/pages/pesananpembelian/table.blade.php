<?php /** @var App\Models\PesananPembelian $table */ ?>

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
            $statusColors = [
                'draft'      => 'bg-zinc-100 text-zinc-700 border-zinc-200',
                'disetujui'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dikirim'    => 'bg-sky-50 text-sky-700 border-sky-200',
                'sebagian'   => 'bg-amber-50 text-amber-700 border-amber-200',
                'diterima'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'ditutup'    => 'bg-violet-50 text-violet-700 border-violet-200',
                'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
            ];
            $tipeColors = [
                'aset'        => 'bg-primary/10 text-primary border-primary/20',
                'suku_cadang' => 'bg-info/10 text-info border-info/20',
                'jasa'        => 'bg-warning/10 text-warning border-warning/20',
            ];
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <th class="text-left whitespace-nowrap">Vendor</th>
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td class="whitespace-nowrap text-sm">{{ $table->hasVendor?->vendor_nama ?? '-' }}</td>
                    @foreach ($model::$sortColumns as $column)
                        @if ($column === 'pesanan_pembelian_nomor')
                            <td class="font-mono font-semibold text-primary whitespace-nowrap">{{ $table->$column }}</td>
                        @elseif ($column === 'pesanan_pembelian_tanggal' || $column === 'pesanan_pembelian_tanggal_kirim')
                            <td class="whitespace-nowrap">{{ $table->$column ? formatDate($table->$column) : '-' }}</td>
                        @elseif ($column === 'pesanan_pembelian_total')
                            <td class="text-right font-mono font-semibold whitespace-nowrap">{{ formatRupiah($table->$column) }}</td>
                        @elseif ($column === 'pesanan_pembelian_status')
                            @php
                                $sc = $statusColors[$table->$column] ?? 'bg-zinc-100 text-zinc-600 border-zinc-200';
                                $label = $table->$column ? \App\Enums\Pesanan\StatusPesananEnum::getDescription($table->$column) : '-';
                            @endphp
                            <td><span class="inline-flex items-center text-[11px] font-semibold rounded-full px-2.5 py-0.5 border {{ $sc }}">{{ $label }}</span></td>
                        @elseif ($column === 'pesanan_pembelian_tipe')
                            @php
                                $tc = $tipeColors[$table->$column] ?? 'bg-zinc-100 text-zinc-600 border-zinc-200';
                                $tlabel = $table->$column ? \App\Enums\Pesanan\TipePesananEnum::getDescription($table->$column) : '-';
                            @endphp
                            <td><span class="inline-flex items-center text-[11px] font-medium rounded-full px-2 py-0.5 border {{ $tc }}">{{ $tlabel }}</span></td>
                        @else
                            <td>{{ $table->$column ?? '-' }}</td>
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
                        $statusVal = $table->pesanan_pembelian_status;
                        $sc = $statusColors[$statusVal] ?? 'bg-zinc-100 text-zinc-600 border-zinc-200';
                        $statusLabel = $statusVal ? \App\Enums\Pesanan\StatusPesananEnum::getDescription($statusVal) : '-';
                        $tipeVal = $table->pesanan_pembelian_tipe ?? null;
                        $tc = $tipeColors[$tipeVal] ?? 'bg-zinc-100 text-zinc-600 border-zinc-200';
                        $tipeLabel = $tipeVal ? \App\Enums\Pesanan\TipePesananEnum::getDescription($tipeVal) : '-';
                        $vendorName = $table->has_vendor->vendor_nama ?? ($table->hasVendor->vendor_nama ?? null);
                        // fallback via relation loading: has_vendor vs hasVendor
                        if (!$vendorName && isset($table->pesanan_pembelian_id_vendor)) {
                            // try direct relation if not eager loaded
                            $vendorName = $table->hasVendor ? $table->hasVendor->vendor_nama : null;
                        }
                        $totalFmt = formatRupiah($table->pesanan_pembelian_total);
                        $tglFmt = $table->pesanan_pembelian_tanggal ? formatDate($table->pesanan_pembelian_tanggal) : '-';
                        $tglKirimFmt = $table->pesanan_pembelian_tanggal_kirim ? formatDate($table->pesanan_pembelian_tanggal_kirim) : null;
                        $dotColor = match($statusVal){
                            'draft' => 'bg-zinc-400',
                            'disetujui' => 'bg-emerald-500',
                            'dikirim' => 'bg-sky-500',
                            'sebagian' => 'bg-amber-500',
                            'diterima' => 'bg-emerald-600',
                            'ditutup' => 'bg-violet-500',
                            'dibatalkan' => 'bg-red-500',
                            default => 'bg-zinc-400'
                        };
                    @endphp
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            <div class="flex gap-3">
                                <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0 mt-1" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                    <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                </button>
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/10 shrink-0 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl text-primary">shopping_cart</span>
                                </div>
                                <div class="flex-1 min-w-0" onclick="event.stopPropagation(); window.location='{{ moduleRoute('getUpdate', ['id' => $table->field_primary]) }}'">
                                    <p class="text-sm font-bold font-mono text-primary leading-tight truncate">{{ $table->pesanan_pembelian_nomor }}</p>
                                    <p class="text-xs text-on-surface-variant truncate mt-0.5">{{ $vendorName ?? 'Tanpa Vendor' }}@if($tipeVal) <span class="text-on-surface-variant/30">•</span> {{ $tipeLabel }}@endif</p>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20 shrink-0 mt-1">chevron_right</span>
                            </div>

                            <div class="flex items-center gap-1.5 mt-3 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $sc }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>{{ $statusLabel }}
                                </span>
                                @if($tipeVal)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium border {{ $tc }}">{{ $tipeLabel }}</span>
                                @endif
                                @if($tglKirimFmt)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-surface-container text-[10px] font-medium text-on-surface-variant border border-outline-variant/20">
                                    <span class="material-symbols-outlined text-[14px]">local_shipping</span> {{ $tglKirimFmt }}
                                </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Tanggal Pesanan</p>
                                    <p class="text-xs font-semibold text-on-surface mt-1">{{ $tglFmt }}</p>
                                </div>
                                <div class="bg-primary/5 rounded-xl px-2.5 py-2 border border-primary/10">
                                    <p class="text-[10px] font-bold tracking-widest text-primary/70 uppercase">Total</p>
                                    <p class="text-sm font-bold font-mono text-primary mt-1 leading-tight">{{ $totalFmt }}</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2 col-span-2 flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Vendor</p>
                                        <p class="text-xs font-medium text-on-surface mt-1 truncate">{{ $vendorName ?? '-' }}</p>
                                    </div>
                                    @if($table->pesanan_pembelian_kode_budget)
                                    <span class="text-[10px] font-mono font-medium text-on-surface-variant bg-white px-2 py-1 rounded-full border border-outline-variant/20 shrink-0 ml-2">{{ $table->pesanan_pembelian_kode_budget }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <a href="{{ moduleRoute('getUpdate', ['id' => $table->field_primary]) }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant hover:text-primary" title="Detail"><span class="material-symbols-outlined text-lg">visibility</span></a>
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
