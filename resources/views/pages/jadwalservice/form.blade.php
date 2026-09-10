<?php /** @var App\Models\JadwalService $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isCreate = !isset($model) || !$model->exists; @endphp
                <x-select col="12" name="jadwal_service_id_aset" :options="App\Models\Aset::getOptions()" label="Aset" helper="Pilih aset yang akan dijadwalkan service" class="search" />
                <x-select col="12" name="jadwal_service_id_template" :options="App\Models\TemplateService::getOptions()" label="Template Service" helper="Pilih paket service (opsional, interval otomatis)" class="search" />
                <x-input col="12" type="date" name="jadwal_service_tanggal_mulai" label="Tanggal Mulai" helper="Tanggal jadwal service dimulai" />

                {{-- disembunyikan: jatuh tempo/status/interval auto --}}
                <input type="hidden" name="jadwal_service_tanggal_jatuh_tempo" value="{{ old('jadwal_service_tanggal_jatuh_tempo', $model->jadwal_service_tanggal_jatuh_tempo ? \Carbon\Carbon::parse($model->jadwal_service_tanggal_jatuh_tempo)->format('Y-m-d') : '') }}">
                <input type="hidden" name="jadwal_service_status" value="{{ old('jadwal_service_status', $model->jadwal_service_status ?? 'aktif') }}">
                <input type="hidden" name="jadwal_service_interval_bulan" value="{{ old('jadwal_service_interval_bulan', $model->jadwal_service_interval_bulan ?? '') }}">
                <input type="hidden" name="jadwal_service_interval_jam" value="{{ old('jadwal_service_interval_jam', $model->jadwal_service_interval_jam ?? '') }}">
                <input type="hidden" name="jadwal_service_odometer_terakhir" value="{{ old('jadwal_service_odometer_terakhir', $model->jadwal_service_odometer_terakhir ?? '') }}">
                <input type="hidden" name="jadwal_service_jam_terakhir" value="{{ old('jadwal_service_jam_terakhir', $model->jadwal_service_jam_terakhir ?? '') }}">
                <input type="hidden" name="jadwal_service_tanggal_terakhir" value="{{ old('jadwal_service_tanggal_terakhir', $model->jadwal_service_tanggal_terakhir ? \Carbon\Carbon::parse($model->jadwal_service_tanggal_terakhir)->format('Y-m-d') : '') }}">
                @if(!$isCreate)
                    <div class="col-span-12 p-3 rounded-xl bg-surface-container-low border border-outline-variant/20 text-sm flex flex-wrap gap-2">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border bg-white">{{ $model->jadwal_service_status ?? 'aktif' }}</span>
                        <span class="text-xs text-on-surface-variant">Jatuh tempo: {{ $model->jadwal_service_tanggal_jatuh_tempo ? formatDate($model->jadwal_service_tanggal_jatuh_tempo) : '—' }}</span>
                    </div>
                @endif

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
