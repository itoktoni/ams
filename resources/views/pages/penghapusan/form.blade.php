<?php /** @var App\Models\Penghapusan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="penghapusan_nomor" />
                <x-select col="6" name="penghapusan_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-textarea col="12" name="penghapusan_alasan" />
                <x-input col="6" type="datetime-local" name="penghapusan_tanggal_request" />
                <x-input col="6" type="date" name="penghapusan_tanggal_akhir_karantina" />
                <x-select col="6" name="penghapusan_status" :options="App\Enums\Penghapusan\StatusPenghapusanEnum::getOptions()" />
                <x-select col="6" name="penghapusan_triase" :options="App\Enums\Penghapusan\TriasePenghapusanEnum::getOptions()" />
                <x-input col="6" type="number" step="any" name="penghapusan_nilai_buku" />
                <x-input col="6" type="number" step="any" name="penghapusan_nilai_sisa" />
                <x-input col="6" type="number" step="any" name="penghapusan_gain_loss" />
                <x-textarea col="12" name="penghapusan_catatan" />

                <x-file name="penghapusan_foto" label="Foto" col="6" accept="image/*"
                    :preview="true" :value="$model?->penghapusan_foto_url" helper="Foto aset yang dihapus" />
                <x-file name="penghapusan_berita_acara" label="Berita Acara" col="6" accept="image/*"
                    :preview="true" :value="$model?->penghapusan_berita_acara_url" helper="Dokumentasi berita acara" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
