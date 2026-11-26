<?php /** @var App\Models\BbmPengajuan $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        {{-- Ringkasan kuota BBM bulan ini (user login) --}}
        @if(!empty($kuotaInfo))
        @php
            $kuota = (float) $kuotaInfo['kuota'];
            $terpakai = (float) $kuotaInfo['terpakai'];
            $menunggu = (float) $kuotaInfo['pending'];
            $tersedia = (float) $kuotaInfo['tersedia'];
            $overKuota = $tersedia < 0;
        @endphp
        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
                <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Periode</p>
                <p class="text-sm font-bold text-on-surface mt-1">{{ $kuotaInfo['periode'] }}</p>
                <p class="text-[11px] text-on-surface-variant">Kuota bulanan Anda</p>
            </div>
            <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
                <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Kuota</p>
                <p class="text-sm font-bold text-on-surface mt-1">{{ formatQty($kuota) }} L</p>
                <p class="text-[11px] text-on-surface-variant">Sisa {{ formatQty($kuotaInfo['sisa']) }} L</p>
            </div>
            <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
                <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Terpakai (disetujui)</p>
                <p class="text-sm font-bold text-on-surface mt-1">{{ formatQty($terpakai) }} L</p>
                <p class="text-[11px] text-on-surface-variant">Menunggu {{ formatQty($menunggu) }} L</p>
            </div>
            <div class="rounded-xl border p-3 {{ $overKuota || $kuota <= 0 ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}">
                <p class="text-[10px] font-bold tracking-widest uppercase {{ $overKuota || $kuota <= 0 ? 'text-error' : 'text-primary' }}">Tersedia</p>
                <p class="text-sm font-bold mt-1 {{ $overKuota || $kuota <= 0 ? 'text-error' : 'text-on-surface' }}">{{ formatQty($tersedia) }} L</p>
                <div class="mt-2 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                    @php $pct = $kuota > 0 ? min(100, (($terpakai + $menunggu) / $kuota) * 100) : 100; @endphp
                    <div class="h-full {{ $overKuota || $kuota <= 0 ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>
        @if($kuota <= 0)
        <div class="mb-4 rounded-xl border border-warning bg-warning-container p-3 text-sm text-on-warning-container">
            Kuota BBM periode {{ $kuotaInfo['periode'] }} belum ditetapkan untuk akun Anda. Hubungi admin untuk menetapkan kuota sebelum mengajukan.
        </div>
        @endif
        @endif

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
                <th>Peminta</th>
                <th>Kendaraan</th>
                <th>Foto</th>
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" :editable="$table->bbm_pengajuan_status === 'menunggu'" />
                    <td>{{ $table->hasUser?->name ?? '-' }}</td>
                    <td>
                        {{ $jenisKendaraanOptions[$table->bbm_pengajuan_jenis_kendaraan] ?? $table->bbm_pengajuan_jenis_kendaraan ?? '-' }}
                        @if($table->bbm_pengajuan_kendaraan) — {{ $table->bbm_pengajuan_kendaraan }}@endif
                    </td>
                    <td>
                        <div class="flex gap-1.5">
                            @foreach([['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sebelum_url', 'Foto BBM Sebelum Diisi'], ['bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_sesudah_url', 'Foto BBM Sesudah Diisi'], ['bbm_pengajuan_foto_struk', 'bbm_pengajuan_foto_struk_url', 'Foto Struk']] as [$field, $urlAttr, $judul])
                            @if($table->$field)
                            <img src="{{ $table->$urlAttr }}" alt="{{ $judul }}" loading="lazy" title="{{ $judul }} — klik untuk memperbesar"
                                onclick="openImageViewer('{{ $table->$urlAttr }}', '{{ $judul }} — {{ $table->bbm_pengajuan_nomor }}')"
                                class="w-10 h-10 rounded-lg object-cover cursor-pointer border border-outline-variant hover:opacity-80 transition-opacity">
                            @endif
                            @endforeach
                            @if(!$table->bbm_pengajuan_foto_sebelum && !$table->bbm_pengajuan_foto_sesudah && !$table->bbm_pengajuan_foto_struk)
                            <span class="text-on-surface-variant/50">-</span>
                            @endif
                        </div>
                    </td>
                    @foreach ($model::$sortColumns as $column)
                    <td>
                        @if(str_contains($column, 'tanggal') && $table->$column)
                            {{ formatDate($table->$column) }}
                        @elseif(str_contains($column, 'liter'))
                            {{ formatQty($table->$column) }} L
                        @elseif(str_contains($column, 'status'))
                            <span class="text-xs font-semibold {{ $table->$column === 'disetujui' ? 'text-success' : ($table->$column === 'ditolak' ? 'text-error' : 'text-on-surface-variant') }}">
                                {{ $statusOptions[$table->$column] ?? $table->$column }}
                            </span>
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
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="p-3.5">
                            {{-- Nomor + checkbox select --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <button class="w-6 h-6 rounded-full border border-outline-variant/30 flex items-center justify-center shrink-0" onclick="event.stopPropagation(); mToggle(this.closest('[data-id]'))">
                                        <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                    </button>
                                    <span class="text-xs font-mono font-bold text-primary px-2 py-1 rounded-full bg-primary/5 border border-primary/10 truncate max-w-[160px]">{{ Str::limit($table->bbm_pengajuan_nomor, 24) }}</span>
                                </div>
                                <span class="material-symbols-outlined text-lg text-on-surface-variant/20">chevron_right</span>
                            </div>

                            {{-- Peminta + kendaraan --}}
                            <div class="mt-3">
                                <p class="text-sm font-bold text-on-surface leading-tight line-clamp-2 break-words">{{ $table->hasUser?->name ?? '-' }}</p>
                                <p class="text-xs text-primary font-medium mt-1 truncate">
                                    {{ $jenisKendaraanOptions[$table->bbm_pengajuan_jenis_kendaraan] ?? $table->bbm_pengajuan_jenis_kendaraan ?? '-' }}
                                    @if($table->bbm_pengajuan_kendaraan) — {{ $table->bbm_pengajuan_kendaraan }}@endif
                                </p>
                            </div>

                            {{-- Detail: periode, jumlah, status, tanggal --}}
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">Periode</p>
                                    <p class="text-xs font-medium text-on-surface mt-1 truncate">{{ $table->bbm_pengajuan_periode }}</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">Jumlah BBM</p>
                                    <p class="text-xs font-bold text-on-surface mt-1 truncate">{{ formatQty($table->bbm_pengajuan_liter) }} L</p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">Status</p>
                                    <p class="mt-1 truncate">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $table->bbm_pengajuan_status === 'disetujui' ? 'bg-success/20 text-success' : ($table->bbm_pengajuan_status === 'ditolak' ? 'bg-error/20 text-error' : 'bg-warning/20 text-warning') }}">{{ $statusOptions[$table->bbm_pengajuan_status] ?? $table->bbm_pengajuan_status }}</span>
                                    </p>
                                </div>
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">Tanggal</p>
                                    <p class="text-xs font-medium text-on-surface mt-1 truncate">{{ $table->bbm_pengajuan_tanggal ? formatDate($table->bbm_pengajuan_tanggal) : '-' }}</p>
                                </div>
                                @if($table->bbm_pengajuan_foto_sebelum || $table->bbm_pengajuan_foto_sesudah || $table->bbm_pengajuan_foto_struk)
                                <div class="bg-surface-container-low/70 rounded-xl px-2.5 py-2 col-span-2">
                                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase truncate">Foto (klik untuk memperbesar)</p>
                                    <div class="flex gap-1.5 mt-1.5" onclick="event.stopPropagation()">
                                        @foreach([['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sebelum_url', 'Foto BBM Sebelum Diisi'], ['bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_sesudah_url', 'Foto BBM Sesudah Diisi'], ['bbm_pengajuan_foto_struk', 'bbm_pengajuan_foto_struk_url', 'Foto Struk']] as [$field, $urlAttr, $judul])
                                        @if($table->$field)
                                        <img src="{{ $table->$urlAttr }}" alt="{{ $judul }}" loading="lazy" title="{{ $judul }}"
                                            onclick="openImageViewer('{{ $table->$urlAttr }}', '{{ $judul }} — {{ $table->bbm_pengajuan_nomor }}')"
                                            class="w-12 h-12 rounded-lg object-cover cursor-pointer border border-outline-variant/30">
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer: ID + aksi --}}
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-surface-container-low/40 border-t border-outline-variant/10" onclick="event.stopPropagation()">
                            <span class="text-[10px] font-mono text-on-surface-variant/60">ID {{ $table->field_primary }}</span>
                            <div class="flex gap-1.5">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-outline-variant/30 text-on-surface-variant/30"><span class="material-symbols-outlined text-lg">visibility</span></span>
                                <span onclick="event.stopPropagation()"><x-table-action :model="$model" :id="$table->field_primary" :editable="$table->bbm_pengajuan_status === 'menunggu'" /></span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-slot:mobile>

        </x-table>

        <x-pagination :paginator="$data" />
        <x-action :model="$model" :action="['create', 'delete']" :plain="true"/>

    </div>

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
    @include('partials.image-viewer')
</x-layouts::app>
