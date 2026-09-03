<?php /** @var App\Models\DokumenAset $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="aset_dokumen_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="aset_dokumen_jenis" :options="App\Enums\Aset\JenisDokumenEnum::getOptions()" />
                <x-input col="6" name="aset_dokumen_nomor" />
                <x-input col="6" type="date" name="aset_dokumen_tanggal_terbit" />
                <x-input col="6" type="date" name="aset_dokumen_tanggal_expired" />
                <x-file
                    name="aset_dokumen_file"
                    label="Berkas Dokumen"
                    col="12"
                    accept="*"
                    :preview="false"
                    :value="$model?->file_url"
                    helper="Unggah berkas dokumen aset" />
                <x-textarea col="12" name="aset_dokumen_keterangan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
