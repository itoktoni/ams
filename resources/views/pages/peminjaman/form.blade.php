<?php /** @var App\Models\Peminjaman $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="peminjaman_nomor" />
                <x-select col="6" name="peminjaman_status" :options="App\Enums\Peminjaman\StatusPeminjamanEnum::getOptions()" />

                <x-select col="6" name="peminjaman_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-select col="6" name="peminjaman_id_peminjam" :options="App\Models\User::getOptions()" />
                <x-select col="6" name="peminjaman_id_approver" :options="App\Models\User::getOptions()" />

                <x-input col="6" type="datetime-local" name="peminjaman_tanggal_pinjam" />
                <x-input col="6" type="datetime-local" name="peminjaman_jatuh_tempo" />
                <x-input col="6" type="datetime-local" name="peminjaman_tanggal_kembali" />

                <x-input col="4" type="number" name="peminjaman_grace_jam" />
                <x-input col="4" type="number" step="0.01" name="peminjaman_denda" />
                <x-input col="4" name="peminjaman_kondisi_kembali" />

                <x-input col="6" type="number" name="peminjaman_perpanjang_ke" />

                <x-textarea col="12" name="peminjaman_tujuan" />
                <x-textarea col="12" name="peminjaman_catatan" />

                <x-file
                    name="peminjaman_foto_kembali"
                    label="Foto Kembali"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->peminjaman_foto_kembali_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
