<?php /** @var App\Models\JadwalService $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="jadwal_service_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="jadwal_service_id_template" :options="App\Models\TemplateService::getOptions()" />
                <x-input col="6" type="date" name="jadwal_service_tanggal_mulai" />
                <x-input col="6" type="date" name="jadwal_service_tanggal_jatuh_tempo" />
                <x-input col="6" type="number" name="jadwal_service_interval_bulan" />
                <x-input col="6" type="number" step="0.01" name="jadwal_service_interval_jam" />
                <x-input col="6" type="number" step="0.01" name="jadwal_service_odometer_terakhir" />
                <x-input col="6" type="number" step="0.01" name="jadwal_service_jam_terakhir" />
                <x-select col="6" name="jadwal_service_status" :options="App\Enums\Service\StatusServiceEnum::getOptions()" />
                <x-input col="6" type="date" name="jadwal_service_tanggal_terakhir" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
