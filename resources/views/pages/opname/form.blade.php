<?php /** @var App\Models\Opname $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="opname_nomor" />
                <x-select col="6" name="opname_id_lokasi" :options="App\Models\LokasiAset::getOptions()" />
                <x-input col="6" type="date" name="opname_tanggal" />
                <x-select col="6" name="opname_id_petugas" :options="App\Models\User::getOptions()" />
                <x-select col="6" name="opname_status" :options="App\Enums\Opname\StatusOpnameEnum::getOptions()" />
                <x-input col="4" type="number" name="opname_total_sistem" />
                <x-input col="4" type="number" name="opname_total_fisik" />
                <x-input col="4" type="number" name="opname_total_selisih" />
                <x-textarea col="12" name="opname_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
