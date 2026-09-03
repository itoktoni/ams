<?php /** @var App\Models\Vendor $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="vendor_kode" />
                <x-input col="6" name="vendor_nama" />
                <x-input col="6" name="vendor_telepon" />
                <x-input col="6" name="vendor_email" />
                <x-input col="6" name="vendor_kategori" />
                <x-input col="6" type="number" step="0.01" name="vendor_rating" />
                <x-textarea col="12" name="vendor_alamat" />
                <x-textarea col="12" name="vendor_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
