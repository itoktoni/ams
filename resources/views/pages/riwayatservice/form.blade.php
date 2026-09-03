<?php /** @var App\Models\RiwayatService $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="riwayat_service_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="riwayat_service_id_tiket" :options="App\Models\Tiket::getOptions()" />
                <x-select col="6" name="riwayat_service_id_teknisi" :options="App\Models\Teknisi::getOptions()" />
                <x-input col="6" type="date" name="riwayat_service_tanggal" />
                <x-input col="6" name="riwayat_service_jenis" />
                <x-input col="6" type="number" step="0.01" name="riwayat_service_biaya" />
                <x-textarea col="12" name="riwayat_service_catatan" />
                <x-textarea col="12" name="riwayat_service_checklist" />
                <x-file
                    name="riwayat_service_ttd"
                    label="Tanda Tangan"
                    col="12"
                    accept="image/*"
                    :preview="true"
                    :value="$model?->riwayat_service_ttd_url"
                    helper="Unggah foto/scan tanda tangga serah terima" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
