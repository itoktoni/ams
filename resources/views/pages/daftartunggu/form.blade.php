<?php /** @var App\Models\DaftarTunggu $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="daftar_tunggu_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="daftar_tunggu_id_peminjam" :options="App\Models\User::getOptions()" />

                <x-input col="6" type="datetime-local" name="daftar_tunggu_tanggal_mulai" />
                <x-input col="6" type="number" name="daftar_tunggu_durasi" />

                <x-input col="6" name="daftar_tunggu_status" />
                <x-select col="6" name="daftar_tunggu_id_peminjaman" :options="App\Models\Peminjaman::getOptions()" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
