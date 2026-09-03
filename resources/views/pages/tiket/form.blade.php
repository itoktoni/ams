<?php /** @var App\Models\Tiket $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="tiket_nomor" />
                <x-input col="6" name="tiket_judul" />
                <x-select col="6" name="tiket_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="tiket_id_pelapor" :options="App\Models\User::getOptions()" />
                <x-select col="6" name="tiket_id_teknisi" :options="App\Models\Teknisi::getOptions()" />
                <x-select col="6" name="tiket_id_lokasi" :options="App\Models\LokasiAset::getOptions()" />
                <x-select col="6" name="tiket_tingkat_urgensi" :options="App\Enums\Tiket\TingkatUrgensiEnum::getOptions()" />
                <x-select col="6" name="tiket_status" :options="App\Enums\Tiket\StatusTiketEnum::getOptions()" />
                <x-select col="6" name="tiket_id_batch" :options="App\Models\BatchTiket::getOptions()" />

                <x-input col="6" type="datetime-local" name="tiket_tanggal_lapor" />
                <x-input col="6" type="datetime-local" name="tiket_jatuh_tempo" />
                <x-input col="6" type="number" step="0.01" name="tiket_biaya" />
                <x-input col="6" type="number" step="0.01" name="tiket_rating" />

                <x-textarea col="12" name="tiket_deskripsi" />
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

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
