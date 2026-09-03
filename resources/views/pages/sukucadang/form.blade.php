<?php /** @var App\Models\SukuCadang $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="suku_cadang_kode" />
                <x-input col="6" name="suku_cadang_nama" />
                <x-select col="6" name="suku_cadang_id_vendor" :options="App\Models\Vendor::getOptions()" />
                <x-select col="6" name="suku_cadang_id_gudang" :options="App\Models\Gudang::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_harga" />
                <x-input col="6" name="suku_cadang_satuan" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_stok_minimum" />
                <x-input col="6" type="number" step="0.01" name="suku_cadang_stok_maksimum" />
                <x-input col="6" name="suku_cadang_bin_aktif" label="Kode Rak Aktif" placeholder="Mis. A-01" />
                <x-input col="6" name="suku_cadang_bin_buffer" label="Kode Rak Buffer" placeholder="Mis. B-02" />
                <x-textarea col="12" name="suku_cadang_spesifikasi" />

                {{-- Link ke Aset (compatible / BOM) --}}
                @php $linked = $linkedAsetIds ?? []; @endphp
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Aset Kompatibel</label>
                    <p class="text-xs text-on-surface-variant mb-2">Pilih aset yang kompatibel dengan suku cadang ini — akan tampil di Aset > Update juga.</p>
                    <select name="aset_ids[]" multiple id="select-aset_ids" class="w-full h-12 bg-transparent font-body-sm search">
                        @foreach(App\Models\Aset::orderBy('aset_nama')->get(['aset_id','aset_nama','aset_kode']) as $a)
                        <option value="{{ $a->aset_id }}" {{ in_array($a->aset_id, $linked, true) ? 'selected' : '' }}>{{ $a->aset_nama }} — {{ $a->aset_kode }}</option>
                        @endforeach
                    </select>
                </div>

                <x-file
                    name="suku_cadang_foto"
                    label="Foto Suku Cadang"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->suku_cadang_foto_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
