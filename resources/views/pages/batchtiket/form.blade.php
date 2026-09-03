<?php /** @var App\Models\BatchTiket $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="batch_tiket_kode" />
                <x-select col="6" name="batch_tiket_id_teknisi" :options="App\Models\Teknisi::getOptions()" />
                <x-input col="6" type="date" name="batch_tiket_tanggal" />
                <x-input col="6" name="batch_tiket_zona" />
                <x-select col="6" name="batch_tiket_mode" :options="App\Enums\Tiket\ModeBatchEnum::getOptions()" />
                <x-select col="6" name="batch_tiket_status" :options="App\Enums\Tiket\StatusBatchEnum::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="batch_tiket_total_eta" />
                <x-input col="6" type="number" step="0.01" name="batch_tiket_total_jarak" />
                <x-input col="12" name="batch_tiket_urutan" placeholder="['1','2','3']" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
