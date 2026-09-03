<?php /** @var App\Models\Department $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="department_kode" label="Kode" placeholder="IT" />
                <x-input col="6" name="department_nama" label="Nama Department" placeholder="Information Technology" />
                <x-input col="6" type="number" step="0.01" name="department_budget" label="Budget" helper="Budget maksimal permintaan suku cadang" />
                <x-select col="6" name="department_periode" label="Periode" :options="['bulanan' => 'Bulanan', 'tahunan' => 'Tahunan']" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
