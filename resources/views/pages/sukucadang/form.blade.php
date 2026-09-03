<?php /** @var App\Models\SukuCadang $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="suku_cadang_kode" />
                <x-input col="6" name="suku_cadang_nama" />
                <x-select col="6" name="suku_cadang_id_vendor" :options="App\Models\Vendor::getOptions()" />
                <x-select col="6" name="suku_cadang_id_gudang" :options="App\Models\Gudang::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_harga" />
                <x-input col="6" name="suku_cadang_satuan" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_stok_minimum" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_stok_maksimum" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_bin_aktif" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_bin_buffer" />
                <x-textarea col="12" name="suku_cadang_spesifikasi" />
                <x-file
                    name="suku_cadang_foto"
                    label="Foto Suku Cadang"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->suku_cadang_foto_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
