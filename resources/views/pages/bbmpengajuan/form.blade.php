<?php /** @var App\Models\BbmPengajuan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    {{-- Ringkasan kuota BBM — mengikuti peminta yang dipilih --}}
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
            <p class="text-sm font-bold text-on-surface mt-1" id="bbm-widget-periode">{{ $kuotaInfo['periode'] }}</p>
            <p class="text-[11px] text-on-surface-variant">Kuota bulanan peminta</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Kuota</p>
            <p class="text-sm font-bold text-on-surface mt-1" id="bbm-widget-kuota">{{ formatQty($kuota) }} L</p>
            <p class="text-[11px] text-on-surface-variant">Sisa <span id="bbm-widget-sisa">{{ formatQty($kuotaInfo['sisa']) }} L</span></p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Terpakai (disetujui)</p>
            <p class="text-sm font-bold text-on-surface mt-1" id="bbm-widget-terpakai">{{ formatQty($terpakai) }} L</p>
            <p class="text-[11px] text-on-surface-variant">Menunggu <span id="bbm-widget-menunggu">{{ formatQty($menunggu) }} L</span></p>
        </div>
        <div class="rounded-xl border p-3 {{ $overKuota || $kuota <= 0 ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}" id="bbm-widget-card">
            <p class="text-[10px] font-bold tracking-widest uppercase {{ $overKuota || $kuota <= 0 ? 'text-error' : 'text-primary' }}">Tersedia</p>
            <p class="text-sm font-bold mt-1 {{ $overKuota || $kuota <= 0 ? 'text-error' : 'text-on-surface' }}" id="bbm-widget-tersedia">{{ formatQty($tersedia) }} L</p>
            <div class="mt-2 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                @php $pct = $kuota > 0 ? min(100, (($terpakai + $menunggu) / $kuota) * 100) : 100; @endphp
                <div class="h-full {{ $overKuota || $kuota <= 0 ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pct }}%" id="bbm-widget-bar"></div>
            </div>
        </div>
    </div>
    @if($kuota <= 0)
    <div class="mb-4 rounded-xl border border-warning bg-warning-container p-3 text-sm text-on-warning-container" id="bbm-widget-warning">
        Kuota BBM periode <span id="bbm-widget-warning-periode">{{ $kuotaInfo['periode'] }}</span> belum ditetapkan untuk peminta ini.
    </div>
    @endif
    @endif

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php
                    $pemilik = (isset($model) && $model->exists) ? $model->hasUser : null;
                    $jenis = $pemilik?->jenis_kendaraan ?? $kendaraanUser['jenis'] ?? null;
                    $jenisLabel = $pemilik?->jenis_kendaraan_label ?? $kendaraanUser['label'] ?? null;
                    $namaKendaraan = $pemilik?->kendaraan ?? $kendaraanUser['nama'] ?? null;
                    $bisaApprove = in_array(auth()->user()->role ?? '', ['developer','admin'], true)
                        && (int) ($model->bbm_pengajuan_id_user ?? 0) !== (int) (auth()->id() ?? -1);
                    $terkunci = isset($model) && $model->exists && $model->bbm_pengajuan_status !== 'menunggu';
                    $isAdminForm = in_array(auth()->user()->role ?? '', ['developer','admin'], true);
                    $bisaPilihUser = $isAdminForm && (!isset($model) || !$model->exists || !$terkunci);
                @endphp

                @if($bisaPilihUser)
                <x-select col="6" name="bbm_pengajuan_id_user" label="Peminta" :options="$userOptions" class="search" />
                <x-select col="6" name="bbm_pengajuan_periode" label="Periode" :options="$periodeOptions" :default="now()->format('Y-m')" />
                @else
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Peminta</label>
                    <div class="h-12 px-4 flex items-center bg-surface-container rounded-lg border border-outline-variant text-sm font-bold text-on-surface">{{ $pemilik?->name ?? auth()->user()->name }}</div>
                    <input type="hidden" name="bbm_pengajuan_id_user" value="{{ $model->bbm_pengajuan_id_user ?? auth()->id() }}">
                </div>
                @endif

                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Kendaraan (dari data user)</label>
                    @if($jenisLabel)
                    <div class="h-12 px-4 flex items-center gap-2 bg-surface-container rounded-lg border border-outline-variant text-sm">
                        <span class="material-symbols-outlined text-primary">{{ $jenis === 'motor' ? 'two_wheeler' : 'directions_car' }}</span>
                        <span class="font-bold text-on-surface" id="bbm-kendaraan-display">{{ $jenisLabel }}@if($namaKendaraan) — {{ $namaKendaraan }}@endif</span>
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-1">Kendaraan diambil otomatis dari data user (1 orang = 1 kendaraan). Ubah di <a href="{{ route('profile.edit') }}" class="text-primary underline">Settings → Profile</a>.</p>
                    @else
                    <div class="rounded-xl border border-warning bg-warning-container p-3 text-sm text-on-warning-container">
                        Jenis kendaraan Anda belum di-set.
                        <a href="{{ route('profile.edit') }}" class="font-bold underline">Isi di Settings → Profile</a>
                        (pilih Mobil/Motor) atau hubungi admin.
                    </div>
                    @endif
                </div>

                @if($terkunci)
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Jumlah BBM (Liter)</label>
                    <div class="h-12 px-4 flex items-center bg-surface-container rounded-lg border border-outline-variant text-sm font-bold text-on-surface">{{ formatQty($model->bbm_pengajuan_liter) }} L</div>
                    <input type="hidden" name="bbm_pengajuan_liter" value="{{ $model->bbm_pengajuan_liter }}">
                    <p class="text-[11px] text-on-surface-variant mt-1">Jumlah BBM tidak dapat diubah setelah pengajuan diproses.</p>
                </div>
                @else
                <x-input col="6" type="number" step="0.01" name="bbm_pengajuan_liter" label="Jumlah BBM (Liter)" placeholder="mis. 100" />
                @endif
                <x-textarea col="12" name="bbm_pengajuan_catatan" label="Catatan" placeholder="Keperluan / keterangan transfer pembelian" />

                @if(isset($model) && $model->exists && ($model->bbm_pengajuan_foto_sebelum || $model->bbm_pengajuan_foto_sesudah || $model->bbm_pengajuan_foto_struk))
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Foto Tersimpan (klik untuk memperbesar)</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach([['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sebelum_url', 'Foto BBM Sebelum Diisi'], ['bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_sesudah_url', 'Foto BBM Sesudah Diisi'], ['bbm_pengajuan_foto_struk', 'bbm_pengajuan_foto_struk_url', 'Foto Struk']] as [$field, $urlAttr, $judul])
                        @if($model->$field)
                        <div class="text-center">
                            <img src="{{ $model->$urlAttr }}" alt="{{ $judul }}" loading="lazy"
                                onclick="openImageViewer('{{ $model->$urlAttr }}', '{{ $judul }} — {{ $model->bbm_pengajuan_nomor }}')"
                                class="w-24 h-24 rounded-xl object-cover cursor-pointer border border-outline-variant hover:opacity-80 transition-opacity" title="Klik untuk memperbesar">
                            <p class="text-[10px] font-semibold text-on-surface-variant mt-1 max-w-24 truncate">{{ $judul }}</p>
                            <a href="{{ $model->$urlAttr }}" download="{{ basename($model->$field) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary hover:underline">
                                <span class="material-symbols-outlined text-sm">download</span> Download
                            </a>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <x-file col="4" name="bbm_pengajuan_foto_sebelum" label="Foto BBM Sebelum Diisi (opsional)" accept="image/*" capture="environment" :preview="true" :value="$model?->bbm_pengajuan_foto_sebelum_url" helper="Foto indikator BBM sebelum pengisian" />
                <x-file col="4" name="bbm_pengajuan_foto_sesudah" label="Foto BBM Sesudah Diisi (opsional)" accept="image/*" capture="environment" :preview="true" :value="$model?->bbm_pengajuan_foto_sesudah_url" helper="Foto indikator BBM sesudah pengisian" />
                <x-file col="4" name="bbm_pengajuan_foto_struk" label="Foto Struk (opsional)" accept="image/*" capture="environment" :preview="true" :value="$model?->bbm_pengajuan_foto_struk_url" helper="Foto struk pembelian BBM" />

                @include('partials.image-viewer')

                @if(isset($model) && $model->exists)
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Status</label>
                    @if($bisaApprove)
                    <select name="bbm_pengajuan_status" class="w-full h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg font-body-sm">
                        @foreach($statusOptions as $val => $label)
                        <option value="{{ $val }}" {{ ($model->bbm_pengajuan_status ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @else
                    <div class="h-12 px-4 flex items-center bg-surface-container rounded-lg border border-outline-variant text-sm font-bold">{{ $statusOptions[$model->bbm_pengajuan_status] ?? $model->bbm_pengajuan_status }}</div>
                    <input type="hidden" name="bbm_pengajuan_status" value="{{ $model->bbm_pengajuan_status }}">
                    @endif
                </div>
                @endif

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" :plain="true" :cancel="moduleRoute('getTable')"/>
    </x-form>

    @if(isset($model) && $model->exists && $model->bbm_pengajuan_status === 'menunggu' && $bisaApprove)
    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ route('bbm-pengajuan.getApprove', ['id' => $model->bbm_pengajuan_id]) }}" onclick="return confirm('Setujui pengajuan BBM ini? Kuota terpakai akan diperbarui.')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-success text-white text-sm font-semibold hover:bg-success/90">
            <span class="material-symbols-outlined">check_circle</span> Approve
        </a>
        <a href="{{ route('bbm-pengajuan.getReject', ['id' => $model->bbm_pengajuan_id]) }}" onclick="return confirm('Tolak pengajuan BBM ini?')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-error/90">
            <span class="material-symbols-outlined">cancel</span> Tolak
        </a>
    </div>
    @endif

    @if($bisaPilihUser)
    <script>
    (function () {
        var selectUser = document.getElementById('select-bbm_pengajuan_id_user');
        var selectPeriode = document.getElementById('select-bbm_pengajuan_periode');
        var display = document.getElementById('bbm-kendaraan-display');
        var kendaraanMap = @json($kendaraanMap);
        var kuotaMapAll = @json($kuotaMapAll);
        function setText(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; }
        function update() {
            if (!selectUser) return;
            var uid = selectUser.value;
            var periode = selectPeriode ? selectPeriode.value : null;
            if (display) display.textContent = kendaraanMap[uid] || '-';
            var row = (kuotaMapAll[uid] || {})[periode] || (kuotaMapAll[uid] ? Object.values(kuotaMapAll[uid])[0] : null);
            if (!row) return;
            setText('bbm-widget-periode', row.periode);
            setText('bbm-widget-kuota', row.kuota);
            setText('bbm-widget-sisa', row.sisa);
            setText('bbm-widget-terpakai', row.terpakai);
            setText('bbm-widget-menunggu', row.menunggu);
            setText('bbm-widget-tersedia', row.tersedia);
            var bar = document.getElementById('bbm-widget-bar');
            if (bar) bar.style.width = row.pct + '%';
        }
        if (selectUser) selectUser.addEventListener('change', update);
        if (selectPeriode) selectPeriode.addEventListener('change', update);
        update();
    })();
    </script>
    @endif
</x-layouts::app>
