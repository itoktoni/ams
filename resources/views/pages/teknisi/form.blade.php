<?php /** @var App\Models\Teknisi $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="teknisi_id_user" :options="App\Models\User::getOptions()" />
                <x-input col="6" name="teknisi_kode" />
                <x-input col="6" name="teknisi_nama" />
                <x-input col="6" name="teknisi_telepon" />
                <x-input col="6" name="teknisi_keahlian" placeholder="['elektrikal','it']" />
                <x-input col="6" name="teknisi_zona" placeholder="['zonaiselatan','zonautara']" />
                <x-input col="6" name="teknisi_sertifikasi" placeholder="['sertifikat-a','sertifikat-b']" />
                <x-input col="6" type="number" step="0.01" name="teknisi_rating" />
                <x-select col="6" name="teknisi_status" :options="App\Enums\Tiket\StatusTeknisiEnum::getOptions()" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
