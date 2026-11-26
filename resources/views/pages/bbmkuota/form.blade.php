<?php /** @var App\Models\BbmKuota $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="bbm_kuota_id_user" label="User" :options="$userOptions" class="search" />
                <x-input col="6" type="month" name="bbm_kuota_periode" label="Periode (YYYY-MM)" placeholder="2026-11" />
                <x-input col="6" type="number" step="0.01" name="bbm_kuota_liter" label="Kuota (Liter)" placeholder="mis. 100" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
