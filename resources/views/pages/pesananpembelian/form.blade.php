<?php /** @var App\Models\PesananPembelian $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="pesanan_pembelian_nomor" helper="Kosongkan = auto" />
                <x-select col="6" name="pesanan_pembelian_id_vendor" :options="App\Models\Vendor::getOptions()" label="Vendor" />
                <x-input col="6" type="date" name="pesanan_pembelian_tanggal" label="Tanggal" />
                <x-input col="6" type="date" name="pesanan_pembelian_tanggal_kirim" label="Tgl Kirim" />
                <x-select col="6" name="pesanan_pembelian_tipe" :options="App\Enums\Pesanan\TipePesananEnum::getOptions()" />
                <x-select col="6" name="pesanan_pembelian_status" :options="App\Enums\Pesanan\StatusPesananEnum::getOptions()" />
                <x-select col="6" name="pesanan_pembelian_kode_budget" label="Department (Budget)" :options="App\Models\Department::getOptions()" helper="Budget cek otomatis — sisa & total terlihat di bawah" />
                <x-select col="6" name="pesanan_pembelian_level_approve" label="Level Approve" :options="App\Enums\Persetujuan\LevelPersetujuanEnum::getOptions()" />
                <x-textarea col="12" name="pesanan_pembelian_catatan" />

            @endbind
        </x-card>

        <x-card label="Item — Multiple Suku Cadang" icon="inventory_2">
            <div class="col-span-12">
                <div id="po-items" class="space-y-2"></div>
                <button type="button" onclick="addPoItem()" class="mt-3 inline-flex items-center gap-2 h-9 px-3 rounded-lg bg-primary/10 text-primary text-sm font-semibold">
                    <span class="material-symbols-outlined text-lg">add</span> Tambah Suku Cadang
                </button>
                <p class="text-xs text-on-surface-variant mt-2">Total dihitung otomatis dari jumlah × harga</p>
                <p class="text-sm font-bold mt-2">Total: <span id="po-total">Rp 0</span></p>
            </div>
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @php
        $scOptions = App\Models\SukuCadang::orderBy('suku_cadang_nama')->get(['suku_cadang_id','suku_cadang_nama','suku_cadang_kode','suku_cadang_harga'])->map(fn($s) => ['id'=>$s->suku_cadang_id,'label'=>$s->suku_cadang_nama.' — '.$s->suku_cadang_kode,'harga'=>(float)$s->suku_cadang_harga, 'hargaFmt'=>formatRupiah($s->suku_cadang_harga)])->values();
        $deptBudgetMap = App\Models\Department::all(['department_id','department_nama','department_kode','department_budget'])->mapWithKeys(fn($d) => [$d->department_id => ['id'=>$d->department_id,'label'=>$d->department_nama.' ('.$d->department_kode.')','budget'=>(float)$d->department_budget,'budgetFmt'=>formatRupiah($d->department_budget)]]);
        $existingItems = isset($items) && $items instanceof \Illuminate\Support\Collection ? $items->map(fn($i) => ['suku_cadang_id'=>$i->pesanan_item_id_referensi,'jumlah'=>(float)$i->pesanan_item_jumlah,'harga'=>(float)$i->pesanan_item_harga])->values() : (isset($items) && is_array($items) ? collect($items)->map(fn($i) => ['suku_cadang_id'=>$i['pesanan_item_id_referensi'] ?? $i['suku_cadang_id'] ?? null,'jumlah'=>(float)($i['pesanan_item_jumlah'] ?? $i['jumlah'] ?? 1),'harga'=>(float)($i['pesanan_item_harga'] ?? $i['harga'] ?? 0)])->values() : collect([]));
    @endphp
    <script>
    (() => {
        const scOptions = @json($scOptions);
        const deptBudgetMap = @json($deptBudgetMap);
        const existing = @json($existingItems);
        let idx = 0;
        const container = document.getElementById('po-items');
        const totalEl = document.getElementById('po-total');

        function scOptsHtml(selected){
            let h = '<option value="">-- Pilih Suku Cadang --</option>';
            scOptions.forEach(o => {
                const sel = String(o.id) === String(selected) ? ' selected' : '';
                h += `<option value="${o.id}" data-harga="${o.harga}"${sel}>${o.label}</option>`;
            });
            return h;
        }

        function calcTotal(){
            let sum = 0;
            container.querySelectorAll('[data-po-row]').forEach(row => {
                const j = parseFloat(row.querySelector('[name$="[jumlah]"]')?.value || 0);
                const h = parseFloat(row.querySelector('[name$="[harga]"]')?.value || 0);
                sum += j * h;
            });
            if (totalEl) totalEl.textContent = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(sum);
        }

        window.addPoItem = function(prefill){
            const i = idx++;
            const scId = prefill?.suku_cadang_id ?? '';
            const jml = prefill?.jumlah ?? 1;
            const hrga = prefill?.harga ?? '';
            const row = document.createElement('div');
            row.setAttribute('data-po-row','');
            row.className = 'grid grid-cols-12 gap-2 items-start p-3 bg-surface-container rounded-xl border border-outline-variant';
            row.innerHTML = `
                <div class="col-span-12 md:col-span-5 flex flex-col">
                    <label class="text-xs font-bold text-on-surface-variant">Suku Cadang</label>
                    <select name="items[${i}][suku_cadang_id]" class="w-full h-10 px-2 bg-white border border-outline-variant rounded-lg text-sm shrink-0" onchange="onScChange(this)">
                        ${scOptsHtml(scId)}
                    </select>
                    <span class="text-[11px] text-on-surface-variant min-h-[16px] mt-1" data-hint-harga></span>
                </div>
                <div class="col-span-5 md:col-span-2 flex flex-col">
                    <label class="text-xs font-bold text-on-surface-variant">Jumlah</label>
                    <input type="number" step="0.01" name="items[${i}][jumlah]" value="${jml}" class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-sm shrink-0" oninput="calcPoTotal()">
                    <span class="min-h-[16px] mt-1"></span>
                </div>
                <div class="col-span-5 md:col-span-3 flex flex-col">
                    <label class="text-xs font-bold text-on-surface-variant">Harga</label>
                    <input type="number" step="0.01" name="items[${i}][harga]" value="${hrga}" class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-sm shrink-0" placeholder="Auto dari master" oninput="calcPoTotal()">
                    <span class="min-h-[16px] mt-1"></span>
                </div>
                <div class="col-span-2 md:col-span-2 flex flex-col items-end">
                    <label class="text-xs font-bold text-transparent select-none">Aksi</label>
                    <button type="button" onclick="this.closest('[data-po-row]').remove(); calcPoTotal()" class="w-9 h-9 rounded-lg bg-error/10 text-error hover:bg-error/20 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-lg">delete</span></button>
                    <span class="min-h-[16px] mt-1"></span>
                </div>
            `;
            container.appendChild(row);
            const sel = row.querySelector('select');
            if (sel && sel.value) onScChange(sel);
            else if (sel && !hrga) {
                const opt = sel.options[sel.selectedIndex];
                const hint = row.querySelector('[data-hint-harga]');
                if (hint) hint.textContent = '';
            }
            calcPoTotal();
        };
        window.onScChange = function(sel){
            const row = sel.closest('[data-po-row]');
            const hargaInp = row?.querySelector('[name$="[harga]"]');
            const hint = row?.querySelector('[data-hint-harga]');
            const opt = sel.options[sel.selectedIndex];
            const harga = opt?.dataset?.harga || '';
            if (hargaInp) hargaInp.value = harga;
            if (hint) {
                if (harga) {
                    const fmt = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(parseFloat(harga));
                    hint.textContent = 'Harga: ' + fmt;
                } else hint.textContent = '';
            }
            calcPoTotal();
        };
        window.calcPoTotal = calcTotal;

        if (existing.length) existing.forEach(e => addPoItem(e));
        else addPoItem();
        calcTotal();
    })();
    </script>
</x-layouts::app>
