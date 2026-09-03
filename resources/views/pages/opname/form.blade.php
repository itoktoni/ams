<?php /** @var App\Models\Opname $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="opname_nomor" helper="Kosongkan = auto-generate" />
                <x-select col="6" name="opname_id_lokasi" :options="App\Models\LokasiAset::getOptions()" label="Lokasi" />
                <x-input col="6" type="date" name="opname_tanggal_mulai" label="Tanggal Mulai" />
                <x-input col="6" type="date" name="opname_tanggal_selesai" label="Tanggal Selesai" />
                <x-select col="6" name="opname_id_petugas" :options="App\Models\User::getOptions()" label="Petugas" />
                <x-select col="6" name="opname_status" :options="App\Enums\Opname\StatusOpnameEnum::getOptions()" />
                <x-textarea col="12" name="opname_catatan" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @if(isset($model) && $model->exists)
    <div class="mt-4 flex gap-2">
        <a href="{{ route('opname.getScan', ['id' => $model->opname_id]) }}" class="inline-flex items-center gap-2 h-11 px-5 rounded-xl bg-primary text-on-primary text-sm font-semibold">
            <span class="material-symbols-outlined">qr_code_scanner</span> Buka Halaman Scan
        </a>
        <a href="{{ route('opname.getReport', ['id' => $model->opname_id]) }}" class="inline-flex items-center gap-2 h-11 px-5 rounded-xl bg-white border border-outline-variant text-sm font-semibold">Report</a>
    </div>
    @endif
</x-layouts::app>
