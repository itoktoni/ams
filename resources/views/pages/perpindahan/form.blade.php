<?php /** @var App\Models\Perpindahan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="perpindahan_nomor" />
                <x-select col="6" name="perpindahan_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="perpindahan_id_lokasi_asal" :options="App\Models\LokasiAset::getOptions()" />
                <x-select col="6" name="perpindahan_id_lokasi_tujuan" :options="App\Models\LokasiAset::getOptions()" />
                <x-input col="6" type="datetime-local" name="perpindahan_tanggal_request" />
                <x-input col="6" type="date" name="perpindahan_tanggal_estimasi" />
                <x-input col="6" type="datetime-local" name="perpindahan_tanggal_kirim" />
                <x-input col="6" type="datetime-local" name="perpindahan_tanggal_terima" />
                <x-select col="6" name="perpindahan_status" :options="App\Enums\Perpindahan\StatusPerpindahanEnum::getOptions()" />
                <x-select col="6" name="perpindahan_level_approve" :options="App\Enums\Persetujuan\LevelPersetujuanEnum::getOptions()" />
                <x-textarea col="12" name="perpindahan_alasan" />
                <x-textarea col="12" name="perpindahan_catatan" />

                <x-file name="perpindahan_foto_keluar" label="Foto Keluar" col="6" accept="image/*"
                    :preview="true" :value="$model?->perpindahan_foto_keluar_url" helper="Foto saat aset keluar" />
                <x-file name="perpindahan_foto_terima" label="Foto Terima" col="6" accept="image/*"
                    :preview="true" :value="$model?->perpindahan_foto_terima_url" helper="Foto saat aset diterima" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @if(isset($model) && $model->exists)
    <div class="mt-4 flex flex-wrap gap-2">
        @if($model->perpindahan_status !== 'disetujui')
        <a href="{{ route('perpindahan.getApprove', ['id' => $model->perpindahan_id]) }}" onclick="return confirm('Setujui perpindahan? Aset akan pindah ke lokasi tujuan & tercatat di log.')" class="inline-flex items-center gap-2 h-10 px-4 bg-success text-white rounded-xl text-sm font-semibold hover:bg-success/90">
            <span class="material-symbols-outlined text-lg">check_circle</span> Approve & Pindahkan
        </a>
        @else
        <span class="inline-flex items-center gap-2 h-10 px-4 bg-success/10 text-success rounded-xl text-sm font-semibold border border-success/20">✓ Sudah disetujui — aset dipindah</span>
        @endif
        <a href="{{ route('perpindahan.getBeritaAcara', ['id' => $model->perpindahan_id]) }}" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-outline-variant text-on-surface rounded-xl text-sm font-semibold hover:bg-surface-container">
            <span class="material-symbols-outlined text-lg">description</span> Berita Acara (PDF)
        </a>
    </div>
    @endif
</x-layouts::app>
