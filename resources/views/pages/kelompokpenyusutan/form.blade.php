<?php /** @var App\Models\KelompokPenyusutan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="kelompok_penyusutan_kode" />
                <x-input col="6" name="kelompok_penyusutan_nama" />
                <x-input col="6" type="number" name="kelompok_penyusutan_masa_manfaat" />
                <x-select col="6" name="kelompok_penyusutan_metode" :options="App\Enums\Aset\MetodePenyusutanEnum::getOptions()" />
                <x-input col="6" type="number" step="any" name="kelompok_penyusutan_tarif" />
                <x-textarea col="12" name="kelompok_penyusutan_keterangan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
