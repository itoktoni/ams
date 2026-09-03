<?php /** @var App\Models\Faktur $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="faktur_id_pesanan" :options="App\Models\PesananPembelian::getOptions()" />
                <x-input col="6" name="faktur_nomor" />
                <x-input col="6" type="date" name="faktur_tanggal" />
                <x-input col="6" type="number" step="0.01" name="faktur_total" />
                <x-select col="6" name="faktur_status" :options="App\Enums\Pesanan\StatusFakturEnum::getOptions()" />
                <x-file
                    name="faktur_file"
                    label="File Faktur"
                    col="12"
                    accept="application/pdf,image/*"
                    :preview="false"
                    :value="$model?->faktur_file_url"
                    helper="Unggah dokumen faktur (PDF atau gambar)" />
                <x-textarea col="12" name="faktur_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
