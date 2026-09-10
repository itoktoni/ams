<?php /** @var App\Models\RiwayatService $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isUpdate = isset($model) && $model->exists; @endphp
                <x-select col="12" name="riwayat_service_id_aset" :options="App\Models\Aset::getOptions()" label="Aset" helper="Pilih aset yang diservice" class="search" />
                <x-select col="6" name="riwayat_service_id_teknisi" :options="App\Models\User::where('role','teknisi')->pluck('name','id')->toArray()" label="Teknisi" helper="Teknisi pelaksana" class="search" />
                <x-select col="6" name="riwayat_service_id_tiket" :options="App\Models\Tiket::getOptions()" label="Tiket Terkait" helper="Opsional — link ke tiket jika ada" class="search" />
                <x-input col="6" type="date" name="riwayat_service_tanggal" label="Tanggal Service" helper="Default hari ini jika kosong" />
                <x-select col="6" name="riwayat_service_jenis" label="Jenis" :options="['berkala'=>'Berkala','perbaikan'=>'Perbaikan','darurat'=>'Darurat']" />
                <x-input col="6" type="number" step="0.01" name="riwayat_service_biaya" label="Biaya" helper="Biaya service (Rp)" />

                <x-textarea col="12" name="riwayat_service_catatan" label="Catatan" placeholder="Hasil service, temuan, tindak lanjut" />
                <x-file
                    name="riwayat_service_ttd"
                    label="Tanda Tangan"
                    col="12"
                    accept="image/*"
                    :preview="true"
                    :value="$model?->riwayat_service_ttd_url"
                    helper="Unggah foto/scan tanda tangan serah terima" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
    @if(isset($model) && $model->exists)
    <div class="mt-4">
        <x-card label="Follow-up Jadwal" icon="event_repeat">
            <div class="col-span-12 p-3 rounded-xl bg-surface-container-low text-sm">
                @php
                    $follow = App\Models\JadwalService::where('jadwal_service_id_aset', $model->riwayat_service_id_aset)
                        ->where('jadwal_service_tanggal_mulai','>',$model->riwayat_service_tanggal)
                        ->orderBy('jadwal_service_tanggal_mulai')->first();
                @endphp
                @if($follow)
                    <p class="font-medium">Follow-up: {{ $follow->hasTemplate?->template_service_nama ?? 'Jadwal' }} — {{ formatDate($follow->jadwal_service_tanggal_mulai) }} <span class="inline-flex px-2 py-0.5 rounded-full text-xs border bg-white">{{ $follow->jadwal_service_status }}</span></p>
                    <a href="{{ route('jadwal-service.getUpdate', ['id' => $follow->jadwal_service_id]) }}" class="text-xs text-primary underline mt-1 inline-block">Lihat Jadwal</a>
                @else
                    <p class="text-on-surface-variant">Belum ada jadwal follow-up setelah service ini.</p>
                    <form method="POST" action="{{ route('riwayat-service.buatFollowUp', ['id' => $model->riwayat_service_id]) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="h-9 px-4 rounded-lg bg-primary text-on-primary text-xs font-semibold">Buat Follow-up Jadwal</button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
    @endif
</x-layouts::app>
