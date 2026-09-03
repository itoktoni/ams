<?php /** @var App\Models\KategoriAset $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="aset_kategori_kode" />
                <x-input col="6" name="aset_kategori_nama" />
                <x-input col="6" type="number" name="aset_kategori_masa_manfaat" />
                <x-select col="6" name="aset_kategori_metode_penyusutan" :options="App\Enums\Aset\MetodePenyusutanEnum::getOptions()" />
                <x-textarea col="12" name="aset_kategori_keterangan" />

            @endbind
        </x-card>

        <x-card label="Custom Field Kategori" icon="tune" noGrid>
            <p class="text-sm text-on-surface-variant mb-4">
                Tentukan field tambahan yang akan muncul saat mengisi aset pada kategori ini
                (misal: kategori <strong>Mobil</strong> &rarr; No STNK, No KIR, No Rangka).
            </p>

            <div id="kategori-custom-fields" class="space-y-3"></div>

            <button type="button" onclick="addKategoriFieldRow()" class="mt-4 inline-flex items-center gap-2 h-11 px-4 rounded-lg bg-primary/10 text-primary font-medium hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-lg">add</span> Tambah Field
            </button>
        </x-card>

        <x-card label="Teknisi Terkait" icon="engineering">
            @php $linkedTek = $linkedTeknisiIds ?? []; @endphp
            <div class="col-span-12">
                <p class="text-xs text-on-surface-variant mb-2">Pilih teknisi yang menangani kategori ini — tiket untuk aset kategori ini akan ternotif ke mereka via Telegram.</p>
                <select name="teknisi_ids[]" multiple id="select-teknisi_ids" class="w-full h-12 bg-transparent font-body-sm search">
                    @foreach(App\Models\Teknisi::orderBy('teknisi_nama')->get(['teknisi_id','teknisi_nama','teknisi_kode']) as $tek)
                    <option value="{{ $tek->teknisi_id }}" {{ in_array($tek->teknisi_id, $linkedTek, true) ? 'selected' : '' }}>{{ $tek->teknisi_nama }} — {{ $tek->teknisi_kode }}</option>
                    @endforeach
                </select>
            </div>
            @if(!empty($linkedTek))
            <div class="col-span-12 mt-2 flex flex-wrap gap-1.5">
                @foreach(App\Models\Teknisi::whereIn('teknisi_id', $linkedTek)->get(['teknisi_nama','teknisi_kode']) as $tek)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-medium">{{ $tek->teknisi_nama }} ({{ $tek->teknisi_kode }})</span>
                @endforeach
            </div>
            @endif
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    <script>
    (() => {
        window.AMS_KATEGORI_DEFS = {!! json_encode(($model->aset_kategori_custom_fields ?? [])) !!};
        let __kfIdx = 0;

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        const KF_TYPES = [
            ['text', 'Text'],
            ['number', 'Number'],
            ['date', 'Tanggal'],
            ['textarea', 'Text Area'],
            ['select', 'Pilihan (Select)'],
        ];

        function kategoriFieldRow(def = {}) {
            const i = __kfIdx++;
            const label = def.label || '';
            const type = def.type || 'text';
            const options = def.options || '';

            const row = document.createElement('div');
            row.setAttribute('data-kf-row', '');
            row.className = 'grid grid-cols-12 gap-3 items-end border border-outline-variant rounded-lg p-3 bg-surface-container';

            const typeOpts = KF_TYPES.map(([v, t]) =>
                `<option value="${v}" ${v === type ? 'selected' : ''}>${t}</option>`).join('');

            row.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Nama Field</label>
                    <input name="aset_kategori_custom_fields[${i}][label]" value="${escapeHtml(label)}"
                        class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none"
                        placeholder="cth: No STNK">
                </div>
                <div class="col-span-6 md:col-span-3">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Tipe</label>
                    <select name="aset_kategori_custom_fields[${i}][type]"
                        class="w-full h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none">
                        ${typeOpts}
                    </select>
                </div>
                <div class="col-span-5 md:col-span-3">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Opsi (untuk Select)</label>
                    <input name="aset_kategori_custom_fields[${i}][options]" value="${escapeHtml(options)}"
                        class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none"
                        placeholder="Pisahkan dgn koma">
                </div>
                <div class="col-span-1 flex justify-end">
                    <button type="button" onclick="this.closest('[data-kf-row]').remove()"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-error/10 text-error hover:bg-error/20 transition-colors"
                        title="Hapus">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            `;

            return row;
        }

        function addKategoriFieldRow(def) {
            document.getElementById('kategori-custom-fields').appendChild(kategoriFieldRow(def || {}));
        }
        // expose for inline onclick="addKategoriFieldRow()"
        window.addKategoriFieldRow = addKategoriFieldRow;

        function initKategoriFields() {
            const container = document.getElementById('kategori-custom-fields');
            if (!container) return;
            container.innerHTML = '';
            __kfIdx = 0;
            const defs = window.AMS_KATEGORI_DEFS || [];
            if (defs.length === 0) {
                addKategoriFieldRow();
            } else {
                defs.forEach((d) => addKategoriFieldRow(d));
            }
        }
        document.addEventListener('DOMContentLoaded', initKategoriFields);
        document.addEventListener('livewire:navigated', initKategoriFields);
        if (document.readyState !== 'loading') initKategoriFields();
    })();
    </script>
</x-layouts::app>
