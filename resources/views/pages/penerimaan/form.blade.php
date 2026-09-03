<?php /** @var App\Models\Penerimaan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="penerimaan_id_pesanan" :options="App\Models\PesananPembelian::getOptions()" />
                <x-input col="6" name="penerimaan_nomor" />
                <x-input col="6" type="date" name="penerimaan_tanggal" />
                <x-input col="6" name="penerimaan_penerima" />
                <x-file
                    name="penerimaan_foto"
                    label="Foto Penerimaan"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->penerimaan_foto_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />
                <x-textarea col="12" name="penerimaan_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
