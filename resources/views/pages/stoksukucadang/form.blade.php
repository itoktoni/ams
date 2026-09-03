<?php /** @var App\Models\StokSukuCadang $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="stok_suku_cadang_id_suku_cadang" :options="App\Models\SukuCadang::getOptions()" />
                <x-select col="6" name="stok_suku_cadang_id_gudang" :options="App\Models\Gudang::getOptions()" />
                <x-input col="6" name="stok_suku_cadang_bin" />
                <x-input col="6" type="number" step="0.01" name="stok_suku_cadang_jumlah" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
