<?php /** @var App\Models\LokasiAset $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="aset_lokasi_kode" />
                <x-input col="6" name="aset_lokasi_nama" />
                <x-select col="6" name="aset_lokasi_parent_id" :options="App\Models\LokasiAset::getOptions()" />
                <x-input col="6" name="aset_lokasi_zona" />
                <x-textarea col="12" name="aset_lokasi_alamat" />
                <x-input col="6" type="number" step="any" name="aset_lokasi_latitude" />
                <x-input col="6" type="number" step="any" name="aset_lokasi_longitude" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
