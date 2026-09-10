<?php /** @var App\Models\Perpindahan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php
                    $isCreate = !isset($model) || !$model->exists;
                    $asetId = old('perpindahan_id_aset', $model->perpindahan_id_aset ?? '');
                @endphp
                @if($isCreate)
                    <input type="hidden" name="perpindahan_nomor" value="">
                @else
                    <x-input col="6" name="perpindahan_nomor" helper="Otomatis jika kosong" />
                @endif
                <x-select col="12" name="perpindahan_id_aset" :options="App\Models\Aset::getOptions()" label="Aset" helper="Pilih aset yang akan dipindahkan" class="search" />
                <div class="col-span-12 hidden" id="perpindahan-asal-info">
                    <div class="flex items-center gap-2 p-3 rounded-xl text-sm bg-primary/5 border border-primary/10">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        <span>Asal: <b id="perpindahan-asal-nama" class="text-on-surface"></b> (otomatis dari aset)</span>
                    </div>
                </div>
                <x-select col="12" name="perpindahan_id_lokasi_tujuan" :options="App\Models\LokasiAset::getOptions()" label="Tujuan" helper="Pilih lokasi tujuan" class="search" />

                {{-- disembunyikan: asal otomatis dari aset, tanggal/status default --}}
                <input type="hidden" name="perpindahan_id_lokasi_asal" value="{{ old('perpindahan_id_lokasi_asal', $model->perpindahan_id_lokasi_asal ?? '') }}" id="perpindahan_id_lokasi_asal">
                <input type="hidden" name="perpindahan_tanggal_request" value="{{ old('perpindahan_tanggal_request', $model->perpindahan_tanggal_request ? \Carbon\Carbon::parse($model->perpindahan_tanggal_request)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                <input type="hidden" name="perpindahan_status" value="{{ old('perpindahan_status', $model->perpindahan_status ?? 'diajukan') }}">
                <input type="hidden" name="perpindahan_tanggal_estimasi" value="{{ old('perpindahan_tanggal_estimasi', $model->perpindahan_tanggal_estimasi ? \Carbon\Carbon::parse($model->perpindahan_tanggal_estimasi)->format('Y-m-d') : '') }}">
                <input type="hidden" name="perpindahan_tanggal_kirim" value="{{ old('perpindahan_tanggal_kirim', $model->perpindahan_tanggal_kirim ? \Carbon\Carbon::parse($model->perpindahan_tanggal_kirim)->format('Y-m-d\TH:i') : '') }}">
                <input type="hidden" name="perpindahan_tanggal_terima" value="{{ old('perpindahan_tanggal_terima', $model->perpindahan_tanggal_terima ? \Carbon\Carbon::parse($model->perpindahan_tanggal_terima)->format('Y-m-d\TH:i') : '') }}">
                <input type="hidden" name="perpindahan_level_approve" value="{{ old('perpindahan_level_approve', $model->perpindahan_level_approve ?? '') }}">

                <x-textarea col="12" name="perpindahan_alasan" label="Alasan Perpindahan" placeholder="Mis. Butuh di lokasi X untuk project Y" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
    @php
        $asalMap = App\Models\Aset::pluck('aset_id_lokasi','aset_id')->toArray();
        $lokasiNama = App\Models\LokasiAset::pluck('aset_lokasi_nama','aset_lokasi_id')->toArray();
        $asalMapJson = json_encode($asalMap, JSON_UNESCAPED_UNICODE);
        $lokasiNamaJson = json_encode($lokasiNama, JSON_UNESCAPED_UNICODE);
    @endphp
    <script>
        (function(){
            var asalMap = {!! $asalMapJson !!};
            var namaMap = {!! $lokasiNamaJson !!};
            var selAset = document.getElementById('select-perpindahan_id_aset');
            var hidAsal = document.getElementById('perpindahan_id_lokasi_asal');
            var box = document.getElementById('perpindahan-asal-info');
            var namaEl = document.getElementById('perpindahan-asal-nama');
            function apply(){
                var id = selAset ? selAset.value : '';
                var lokId = asalMap[id] || '';
                if(hidAsal) hidAsal.value = lokId;
                if(box){
                    if(lokId && namaMap[lokId]){
                        if(namaEl) namaEl.textContent = namaMap[lokId];
                        box.classList.remove('hidden');
                    } else box.classList.add('hidden');
                }
            }
            if(selAset){ selAset.addEventListener('change', apply); apply(); }
        })();
    </script>

    @if(isset($model) && $model->exists)
    <div class="mt-4 flex flex-wrap gap-2">
        @if($model->perpindahan_status !== 'disetujui')
        <a href="{{ route('perpindahan.getApprove', ['id' => $model->perpindahan_id]) }}" onclick="return confirm('Setujui perpindahan? Aset akan pindah ke lokasi tujuan & tercatat di log.')" class="inline-flex items-center gap-2 h-10 px-4 bg-success text-white rounded-xl text-sm font-semibold hover:bg-success/90">
            <span class="material-symbols-outlined text-lg">check_circle</span> Approve & Pindahkan
        </a>
        @else
        <span class="inline-flex items-center gap-2 h-10 px-4 bg-success/10 text-success rounded-xl text-sm font-semibold border border-success/20">✓ Sudah disetujui — aset dipindah</span>
        @endif
        @if($model->perpindahan_status === 'disetujui')
        <a href="{{ route('perpindahan.getBeritaAcara', ['id' => $model->perpindahan_id]) }}" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-outline-variant text-on-surface rounded-xl text-sm font-semibold hover:bg-surface-container">
            <span class="material-symbols-outlined text-lg">description</span> Berita Acara (PDF)
        </a>
        @endif
    </div>
    @endif
</x-layouts::app>
