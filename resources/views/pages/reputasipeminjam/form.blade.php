<?php /** @var App\Models\ReputasiPeminjam $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="reputasi_peminjam_id_user" :options="App\Models\User::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="reputasi_peminjam_skor" />

                <x-input col="6" type="number" name="reputasi_peminjam_total_pinjam" />
                <x-input col="6" type="number" name="reputasi_peminjam_terlambat" />

                <x-input col="6" type="number" name="reputasi_peminjam_limit_pinjam" />
                <x-input col="6" type="number" name="reputasi_peminjam_durasi_maks" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
