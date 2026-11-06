<?php /** @var App\Models\OtorisasiKeluar $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isCreate = !isset($model) || !$model->exists; @endphp
                @if($isCreate)
                    <input type="hidden" name="otorisasi_keluar_nomor" value="">
                @else
                    <x-input col="6" name="otorisasi_keluar_nomor" />
                @endif
                <x-select col="6" name="otorisasi_keluar_id_aset" label="Aset" :options="$asetOptions" class="search" />
                <x-select col="6" name="otorisasi_keluar_jenis" label="Jenis Keluar" :options="$jenisOptions" helper="Peminjaman / perpindahan / penghapusan / penjualan / lainnya" />
                <x-select col="6" name="otorisasi_keluar_status" label="Status" :options="$statusOptions" helper="Allow = diizinkan keluar saat scan" />
                <x-textarea col="12" name="otorisasi_keluar_catatan" label="Catatan" placeholder="Mis. Izin bawa keluar untuk servis di vendor X" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
