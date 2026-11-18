<?php /** @var App\Models\Pembayaran $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="pembayaran_jenis" :options="$jenis" />
                <x-input col="6" name="pembayaran_nama" />
                <x-input col="6" name="pembayaran_nomor_pelanggan" />
                <x-select col="6" name="pembayaran_status" :options="$status" />
                <x-date col="6" name="pembayaran_tanggal_tagihan" />
                <x-date col="6" name="pembayaran_tanggal_jatuh_tempo" />
                <x-date col="6" name="pembayaran_tanggal_bayar" />
                <x-input col="6" type="number" name="pembayaran_nominal" />
                <x-input col="6" type="number" name="pembayaran_denda" />
                <x-textarea col="6" name="pembayaran_catatan" />

                <x-file
                    name="pembayaran_bukti"
                    label="Bukti Pembayaran"
                    col="12"
                    accept="image/*,.pdf"
                    helper="Foto/scan bukti bayar (maks 4MB)" />

                @if(!empty($model?->pembayaran_bukti))
                <div class="col-span-12 flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-lg text-on-surface-variant">description</span>
                    <a href="{{ $model->bukti_url }}" target="_blank" class="text-primary underline">Lihat bukti saat ini</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-on-surface-variant cursor-pointer ml-2">
                        <input type="checkbox" name="remove_pembayaran_bukti" value="1" class="rounded border-outline-variant"> Hapus file saat ini
                    </label>
                </div>
                @endif

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
