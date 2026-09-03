<?php /** @var App\Models\Gudang $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="gudang_kode" />
                <x-input col="6" name="gudang_nama" />
                <x-select col="6" name="gudang_id_lokasi" :options="App\Models\LokasiAset::getOptions()" />
                <x-textarea col="12" name="gudang_alamat" />
                <x-textarea col="12" name="gudang_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
