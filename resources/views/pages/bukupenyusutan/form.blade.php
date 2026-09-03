<?php /** @var App\Models\BukuPenyusutan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="buku_penyusutan_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-input col="6" name="buku_penyusutan_periode" />
                <x-input col="6" type="datetime-local" name="buku_penyusutan_tanggal" />
                <x-input col="6" type="number" step="any" name="buku_penyusutan_nilai" label="Nilai Penyusutan (per Bulan)" />
                <x-input col="6" type="number" step="any" name="buku_penyusutan_akumulasi" />
                <x-input col="6" type="number" step="any" name="buku_penyusutan_nilai_buku" />
                <x-select col="6" name="buku_penyusutan_reversalisasi_dari" :options="App\Models\BukuPenyusutan::getOptions()" />
                <x-select col="6" name="buku_penyusutan_dibuat_oleh" :options="App\Models\User::getOptions()" />
                <x-input col="6" name="buku_penyusutan_hash" />
                <x-input col="6" name="buku_penyusutan_hash_sebelum" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
