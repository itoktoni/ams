<?php /** @var App\Models\Persetujuan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="persetujuan_modul" />
                <x-input col="6" type="number" name="persetujuan_id_referensi" />
                <x-select col="6" name="persetujuan_level" :options="App\Enums\Persetujuan\LevelPersetujuanEnum::getOptions()" />
                <x-select col="6" name="persetujuan_id_user" :options="App\Models\User::getOptions()" />
                <x-select col="6" name="persetujuan_status" :options="App\Enums\Persetujuan\StatusPersetujuanEnum::getOptions()" />
                <x-textarea col="12" name="persetujuan_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
