<?php /** @var App\Models\Aset $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="aset_kode" />
                <x-input col="6" name="aset_nama" />
                <x-select col="6" name="aset_id_kategori" :options="App\Models\KategoriAset::getOptions()" onchange="renderCustomFields()" />
                <x-select col="6" name="aset_id_lokasi" :options="App\Models\LokasiAset::getOptions()" />
                <x-select col="6" name="aset_id_penanggung_jawab" :options="App\Models\User::getOptions()" />
                <x-select col="6" name="aset_id_vendor" :options="App\Models\Vendor::getOptions()" />
                <x-input col="6" name="aset_merek" />
                <x-input col="6" name="aset_model" />
                <x-input col="6" name="aset_nomor_seri" />
                <x-input col="6" type="date" name="aset_tanggal_perolehan" />
                <x-input col="6" type="number" step="any" name="aset_harga_perolehan" />
                <x-input col="6" type="number" step="any" name="aset_nilai_sisa" />
                <x-input col="6" type="number" name="aset_masa_manfaat" />
                <x-select col="6" name="aset_metode_penyusutan" :options="App\Enums\Aset\MetodePenyusutanEnum::getOptions()" />
                <x-input col="6" type="date" name="aset_tanggal_mulai_susut" />
                <x-select col="6" name="aset_status" :options="App\Enums\Aset\StatusAsetEnum::getOptions()" />
                <x-select col="6" name="aset_kondisi" :options="App\Enums\Aset\KondisiAsetEnum::getOptions()" />
                <x-input col="6" name="aset_kode_qr" />
                <x-input col="6" type="number" step="any" name="aset_jam_pakai" />
                <x-file
                    name="aset_foto"
                    label="Foto Aset"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->foto_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />
                <x-textarea col="12" name="aset_catatan" />

            @endbind
        </x-card>

        <x-card label="Custom Field Aset" icon="tune" noGrid>
            <p class="text-sm text-on-surface-variant mb-4">
                Field berikut muncul sesuai kategori aset yang dipilih.
            </p>
            <div id="custom-fields-wrapper" class="grid grid-cols-12 gap-5"></div>
            <p id="custom-fields-empty" class="text-sm text-on-surface-variant">Belum ada custom field untuk kategori ini.</p>
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    <script>
    (() => {
        window.AMS_KATEGORI_FIELDS = {!! json_encode($kategoriFields ?? []) !!};
        window.AMS_ASSET_FIELDS = {!! json_encode(($model->aset_custom_fields ?? [])) !!};

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        function buildCustomField(def, value) {
            const key = def.key;
            const label = def.label || def.key;
            const type = def.type || 'text';
            const name = 'aset_custom_fields[' + key + ']';

            const wrap = document.createElement('div');
            wrap.className = 'col-span-12 md:col-span-6';
            wrap.innerHTML = '<label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">' + escapeHtml(label) + '</label>';

            const baseClass = 'w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none font-body-sm';
            let control;

            if (type === 'textarea') {
                control = document.createElement('textarea');
                control.name = name;
                control.rows = 3;
                control.className = 'w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none font-body-sm';
                control.value = value;
            } else if (type === 'select') {
                control = document.createElement('select');
                control.name = name;
                control.className = baseClass + ' appearance-none cursor-pointer pr-10';
                const opts = String(def.options || '').split(',').map((o) => o.trim()).filter((o) => o !== '');
                let html = '<option value="">-- Silahkan Pilih --</option>';
                opts.forEach((o) => {
                    const sel = o === value ? ' selected' : '';
                    html += '<option value="' + escapeHtml(o) + '"' + sel + '>' + escapeHtml(o) + '</option>';
                });
                control.innerHTML = html;
            } else {
                control = document.createElement('input');
                control.name = name;
                control.type = type === 'number' ? 'number' : (type === 'date' ? 'date' : 'text');
                if (type === 'number') {
                    control.step = 'any';
                }
                control.className = baseClass;
                control.value = value;
            }

            wrap.appendChild(control);

            return wrap;
        }

        function renderCustomFields() {
            const sel = document.querySelector('[name="aset_id_kategori"]');
            const catId = sel ? sel.value : '';
            const defs = (catId && window.AMS_KATEGORI_FIELDS[catId]) ? window.AMS_KATEGORI_FIELDS[catId] : [];
            const wrap = document.getElementById('custom-fields-wrapper');
            const empty = document.getElementById('custom-fields-empty');
            if (!wrap || !empty) return;
            wrap.innerHTML = '';
            if (!defs.length) {
                empty.style.display = 'block';
                return;
            }
            empty.style.display = 'none';
            defs.forEach((def) => {
                const value = window.AMS_ASSET_FIELDS[def.key] ?? '';
                wrap.appendChild(buildCustomField(def, value));
            });
        }
        // expose for inline onchange="renderCustomFields()"
        window.renderCustomFields = renderCustomFields;

        document.addEventListener('DOMContentLoaded', renderCustomFields);
        document.addEventListener('livewire:navigated', renderCustomFields);
        if (document.readyState !== 'loading') renderCustomFields();
    })();
    </script>
</x-layouts::app>
