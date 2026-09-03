<?php /** @var App\Models\PesananPembelian $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="pesanan_pembelian_nomor" />
                <x-select col="6" name="pesanan_pembelian_id_vendor" :options="App\Models\Vendor::getOptions()" />
                <x-input col="6" type="date" name="pesanan_pembelian_tanggal" />
                <x-input col="6" type="date" name="pesanan_pembelian_tanggal_kirim" />
                <x-select col="6" name="pesanan_pembelian_tipe" :options="App\Enums\Pesanan\TipePesananEnum::getOptions()" />
                <x-select col="6" name="pesanan_pembelian_status" :options="App\Enums\Pesanan\StatusPesananEnum::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="pesanan_pembelian_total" />
                <x-input col="6" name="pesanan_pembelian_kode_budget" />
                <x-input col="6" name="pesanan_pembelian_level_approve" />
                <x-textarea col="12" name="pesanan_pembelian_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
