<?php /** @var App\Models\TemplateService $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="template_service_kode" label="Kode" />
                <x-input col="6" name="template_service_nama" label="Nama Service" />
                <x-select col="6" name="template_service_id_kategori" :options="App\Models\KategoriAset::getOptions()" label="Kategori Aset" />
                <x-input col="6" type="number" name="template_service_interval_bulan" label="Interval Bulan" helper="Jarak bulan antar service (cth. 6)" />
                <x-textarea col="12" name="template_service_keterangan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
