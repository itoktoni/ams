<?php /** @var App\Models\TemplateService $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="template_service_kode" />
                <x-input col="6" name="template_service_nama" />
                <x-select col="6" name="template_service_id_kategori" :options="App\Models\KategoriAset::getOptions()" />
                <x-input col="6" type="number" name="template_service_interval_bulan" />
                <x-input col="6" type="number" step="0.01" name="template_service_interval_jam" />
                <x-input col="6" type="number" step="0.01" name="template_service_estimasi_jam" />
                <x-textarea col="12" name="template_service_keterangan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
