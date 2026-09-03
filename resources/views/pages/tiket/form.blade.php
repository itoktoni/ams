<?php /** @var App\Models\Tiket $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isPengguna = auth()->user()?->role === 'pengguna_aset'; $hasNomor = !empty($model?->tiket_nomor); @endphp
                @if($isPengguna && ! $hasNomor && ! ($model?->exists))
                    {{-- Nomor hide jika kosong (pengguna) — auto generate di controller --}}
                    <input type="hidden" name="tiket_nomor" value="">
                @else
                    <x-input col="6" name="tiket_nomor" :helper="$isPengguna ? 'Otomatis terisi jika kosong' : null" />
                @endif
                <x-input col="6" name="tiket_judul" />
                <x-select col="6" name="tiket_id_aset" :options="$asetOptions ?? App\Models\Aset::getOptions()" :helper="$isPengguna ? 'Hanya aset yang di-assign kepada Anda' : null" />
                @if($isPengguna)
                    <input type="hidden" name="tiket_id_pelapor" value="{{ auth()->id() }}">
                    <div class="col-span-6 md:col-span-6 p-3 bg-primary/5 border border-primary/10 rounded-xl text-sm">
                        <span class="font-semibold">Pelapor:</span> {{ auth()->user()->name }} (otomatis)
                    </div>
                @else
                    <x-select col="6" name="tiket_id_pelapor" :options="App\Models\User::getOptions()" />
                    <x-select col="6" name="tiket_id_teknisi" :options="App\Models\Teknisi::getOptions()" />
                @endif
                <x-select col="6" name="tiket_tingkat_urgensi" :options="App\Enums\Tiket\TingkatUrgensiEnum::getOptions()" />
                @if($isPengguna)
                    <input type="hidden" name="tiket_status" value="buka">
                    <div class="col-span-6 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm">Status: <b>Buka</b> — akan dinotifikasi ke teknisi & di-assign otomatis.</div>
                @else
                    <x-select col="6" name="tiket_status" :options="App\Enums\Tiket\StatusTiketEnum::getOptions()" />
                @endif
                @if(! $isPengguna)
                    <x-select col="6" name="tiket_id_batch" :options="App\Models\BatchTiket::getOptions()" />
                @endif

                @if($isPengguna)
                    {{-- Lokasi mengikuti aset — auto dari aset_id_lokasi --}}
                    <input type="hidden" name="tiket_id_lokasi" value="{{ $model?->tiket_id_lokasi ?? '' }}" id="tiket_id_lokasi_auto">
                    <div class="col-span-6 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant">Lokasi: <span class="font-medium text-on-surface">otomatis mengikuti aset</span> — tidak perlu pilih manual.</div>
                    {{-- Lapor/tempo/biaya/rating/catatan/foto disembunyikan untuk pengguna — otomatis di controller --}}
                    <input type="hidden" name="tiket_tanggal_lapor" value="{{ now()->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="tiket_jatuh_tempo" value="">
                    <input type="hidden" name="tiket_biaya" value="0">
                    <input type="hidden" name="tiket_rating" value="">
                    <input type="hidden" name="tiket_catatan" value="">
                @else
                    <x-select col="6" name="tiket_id_lokasi" :options="App\Models\LokasiAset::getOptions()" />
                    <x-input col="6" type="datetime-local" name="tiket_tanggal_lapor" />
                    <x-input col="6" type="datetime-local" name="tiket_jatuh_tempo" />
                    <x-input col="6" type="number" step="0.01" name="tiket_biaya" />
                    <x-input col="6" type="number" step="0.01" name="tiket_rating" />
                @endif

                <x-textarea col="12" name="tiket_deskripsi" label="Deskripsi Kerusakan" helper="Jelaskan kerusakan aset Anda — mis. Mobil tidak bisa start, AC tidak dingin" />

                @if($isPengguna)
                    {{-- catatan & foto disembunyikan untuk pengguna --}}
                @else
                    <x-textarea col="12" name="tiket_catatan" />
                    <x-file
                        name="tiket_foto_sebelum"
                        label="Foto Sebelum"
                        col="6"
                        accept="image/*"
                        capture="environment"
                        :preview="true"
                        :value="$model?->tiket_foto_sebelum_url"
                        helper="Ambil foto via kamera di HP atau pilih dari galeri" />

                    <x-file
                        name="tiket_foto_sesudah"
                        label="Foto Sesudah"
                        col="6"
                        accept="image/*"
                        capture="environment"
                        :preview="true"
                        :value="$model?->tiket_foto_sesudah_url"
                        helper="Ambil foto via kamera di HP atau pilih dari galeri" />
                @endif

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
