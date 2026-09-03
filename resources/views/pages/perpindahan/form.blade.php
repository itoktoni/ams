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
                <x-input col="6" name="perpindahan_ttd_hash" />
                <x-input col="6" type="number" step="any" name="perpindahan_latitude" />
                <x-input col="6" type="number" step="any" name="perpindahan_longitude" />
                <x-textarea col="12" name="perpindahan_catatan" />

                <x-file name="perpindahan_foto_keluar" label="Foto Keluar" col="6" accept="image/*"
                    :preview="true" :value="$model?->perpindahan_foto_keluar_url" helper="Foto saat aset keluar" />
                <x-file name="perpindahan_foto_terima" label="Foto Terima" col="6" accept="image/*"
                    :preview="true" :value="$model?->perpindahan_foto_terima_url" helper="Foto saat aset diterima" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
