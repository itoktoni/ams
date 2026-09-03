<?php /** @var App\Models\PergerakanStok $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="pergerakan_stok_id_suku_cadang" :options="App\Models\SukuCadang::getOptions()" />
                <x-select col="6" name="pergerakan_stok_id_gudang" :options="App\Models\Gudang::getOptions()" />
                <x-select col="6" name="pergerakan_stok_tipe" :options="App\Enums\Stok\TipePergerakanStokEnum::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="pergerakan_stok_jumlah" />
                <x-input col="6" name="pergerakan_stok_referensi" />
                <x-textarea col="12" name="pergerakan_stok_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
