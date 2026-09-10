<?php /** @var App\Models\PesananPembelian $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    {{-- Budget info: tampil saat department dipilih (klik department → update otomatis) --}}
    @php
        $hasBudget = !empty($budgetInfo);
        $bi = $budgetInfo ?? null;
        $biDept = $bi['department'] ?? null;
        $biBudget = $bi['budget'] ?? 0;
        $biTerpakai = $bi['terpakai'] ?? 0;
        $biPending = $bi['pending'] ?? 0;
        $biSisa = $bi['sisa'] ?? 0;
        $biTersedia = $bi['tersedia'] ?? 0;
        $biPct = $biBudget > 0 ? min(100, (($biTerpakai + $biPending) / $biBudget) * 100) : 0;
        $biOver = $biTersedia < 0;
    @endphp
    <div id="budget-info" class="{{ $hasBudget ? '' : 'hidden' }} mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Department</p>
            <p id="bi-dept" class="text-sm font-bold text-on-surface mt-1">{{ $biDept ? $biDept->department_nama.' ('.$biDept->department_kode.')' : '-' }}</p>
            <p id="bi-periode" class="text-[11px] text-on-surface-variant">{{ $biDept ? 'Periode '.ucfirst($biDept->department_periode) : '' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Anggaran</p>
            <p id="bi-budget" class="text-sm font-bold text-on-surface mt-1">{{ $hasBudget ? formatRupiah($biBudget) : '-' }}</p>
            <p class="text-[11px] text-on-surface-variant">Sisa <span id="bi-sisa">{{ $hasBudget ? formatRupiah($biSisa) : '-' }}</span></p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Terpakai (disetujui)</p>
            <p id="bi-terpakai" class="text-sm font-bold text-on-surface mt-1">{{ $hasBudget ? formatRupiah($biTerpakai) : '-' }}</p>
            <p class="text-[11px] text-on-surface-variant">Menunggu <span id="bi-pending">{{ $hasBudget ? formatRupiah($biPending) : '-' }}</span></p>
        </div>
        <div id="bi-tersedia-card" class="rounded-xl border p-3 {{ $biOver ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}">
            <p id="bi-tersedia-label" class="text-[10px] font-bold tracking-widest uppercase {{ $biOver ? 'text-error' : 'text-primary' }}">Tersedia</p>
            <p id="bi-tersedia" class="text-sm font-bold mt-1 {{ $biOver ? 'text-error' : 'text-on-surface' }}">{{ $hasBudget ? formatRupiah($biTersedia) : '-' }}</p>
            <div class="mt-2 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                <div id="bi-bar" class="h-full {{ $biOver ? 'bg-error' : 'bg-primary' }}" style="width: {{ $biPct }}%"></div>
            </div>
            <p id="bi-warning" class="hidden mt-2 text-[11px] font-semibold text-error">Total PO melebihi tersedia!</p>
        </div>
    </div>
    <div id="budget-empty" class="{{ $hasBudget ? 'hidden' : '' }} mb-4 rounded-xl border border-dashed border-outline-variant/40 bg-surface-container-low/50 p-3 text-sm text-on-surface-variant">
        Pilih Department untuk melihat anggaran, terpakai, menunggu, dan sisa tersedia.
    </div>

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isCreate = !isset($model) || !$model->exists; @endphp
                @if($isCreate)
                    <input type="hidden" name="pesanan_pembelian_nomor" value="">
                @else
                    <x-input col="6" name="pesanan_pembelian_nomor" helper="Otomatis jika kosong" />
                @endif
                <x-select col="12" name="pesanan_pembelian_id_vendor" :options="App\Models\Vendor::getOptions()" label="Vendor" helper="Pilih vendor suku cadang" class="search" />
                <x-select col="12" name="pesanan_pembelian_kode_budget" label="Department (Anggaran)" :options="App\Models\Department::getOptions()" helper="Wajib pilih department → anggaran & sisa tampil di atas" onchange="onDepartmentChange(this)" />
                <x-textarea col="12" name="pesanan_pembelian_catatan" label="Catatan" placeholder="Keperluan / keterangan PO" />

                {{-- tanggal/tipe hidden, status editable hanya untuk developer/admin/supervisor --}}
                <input type="hidden" name="pesanan_pembelian_tanggal" value="{{ old('pesanan_pembelian_tanggal', $model->pesanan_pembelian_tanggal ? \Carbon\Carbon::parse($model->pesanan_pembelian_tanggal)->format('Y-m-d') : now()->format('Y-m-d')) }}">
                <input type="hidden" name="pesanan_pembelian_tanggal_kirim" value="{{ old('pesanan_pembelian_tanggal_kirim', $model->pesanan_pembelian_tanggal_kirim ? \Carbon\Carbon::parse($model->pesanan_pembelian_tanggal_kirim)->format('Y-m-d') : '') }}">
                <input type="hidden" name="pesanan_pembelian_tipe" value="{{ old('pesanan_pembelian_tipe', $model->pesanan_pembelian_tipe ?? 'suku_cadang') }}">
                @php $canEditStatus = in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true); @endphp
                @if($canEditStatus)
                    <x-select col="6" name="pesanan_pembelian_status" :options="App\Enums\Pesanan\StatusPesananEnum::getOptions()" label="Status" helper="Hanya admin/developer bisa ubah — akan sync dengan penerimaan" />
                @else
                    <input type="hidden" name="pesanan_pembelian_status" value="{{ old('pesanan_pembelian_status', $model->pesanan_pembelian_status ?? 'draft') }}">
                @endif
                <input type="hidden" name="pesanan_pembelian_level_approve" value="{{ old('pesanan_pembelian_level_approve', $model->pesanan_pembelian_level_approve ?? '') }}">
                @if(!$isCreate)
                    <div class="col-span-12 p-3 rounded-xl bg-surface-container-low border border-outline-variant/20 text-sm flex flex-wrap gap-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-white"><span class="w-2 h-2 rounded-full bg-primary"></span>{{ $model->pesanan_pembelian_tipe ?? 'suku_cadang' }}</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-white">{{ $model->pesanan_pembelian_status ?? 'draft' }}</span>
                        <span class="text-xs text-on-surface-variant">Tanggal: {{ $model->pesanan_pembelian_tanggal ? formatDate($model->pesanan_pembelian_tanggal) : '-' }}</span>
                    </div>
                @endif

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

    {{-- Korelasi: Penerimaan per Barang + Invoice PDF (tanpa approval) --}}
    @if(isset($model) && $model->exists)
    <div class="grid grid-cols-12 gap-4 mt-4">
        <div class="col-span-12 lg:col-span-7">
            <x-card label="Penerimaan per Barang" icon="inventory">
                <div class="col-span-12 overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-on-surface-variant border-b">
                                <th class="text-left py-2">Barang</th>
                                <th class="text-right py-2">Pesan</th>
                                <th class="text-right py-2">Diterima</th>
                                <th class="text-right py-2">Sisa</th>
                                <th class="text-center py-2">Status</th>
                                <th class="text-center py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($items ?? collect()) as $it)
                                @php
                                    $dipesan = (float)($it->pesanan_item_jumlah ?? 0);
                                    $diterima = (float)($it->pesanan_item_diterima ?? 0);
                                    $sisa = max(0, $dipesan - $diterima);
                                    $st = $diterima <= 0 ? 'belum' : ($diterima >= $dipesan ? 'lengkap' : 'sebagian');
                                    $badge = $st === 'lengkap' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($st === 'sebagian' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-zinc-100 text-zinc-600 border-zinc-200');
                                @endphp
                                <tr class="border-b border-outline-variant/20">
                                    <td class="py-2 font-medium">{{ $it->pesanan_item_nama }}</td>
                                    <td class="py-2 text-right font-mono">{{ $dipesan }}</td>
                                    <td class="py-2 text-right font-mono text-primary">{{ $diterima }}</td>
                                    <td class="py-2 text-right font-mono">{{ $sisa }}</td>
                                    <td class="py-2 text-center"><span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $badge }}">{{ ucfirst($st) }}</span></td>
                                    <td class="py-2 text-center">
                                        @if($sisa > 0)
                                            <button type="button" onclick="openTerimaModal('{{ $it->pesanan_item_id }}','{{ addslashes($it->pesanan_item_nama) }}','{{ $sisa }}')" class="h-7 px-2.5 rounded-lg bg-primary text-on-primary text-[11px] font-semibold">Terima {{ $sisa }}</button>
                                        @else
                                            <span class="text-emerald-600 text-[11px] font-bold">✓</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-4 text-center text-on-surface-variant">Belum ada item.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Modal Terima per Barang — pilih gudang tujuan dulu lalu Save -->
                <div id="terima-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50" onclick="closeTerimaModal()"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-5">
                        <h3 class="text-sm font-bold text-on-surface">Terima Barang</h3>
                        <p id="terima-barang-nama" class="text-xs text-on-surface-variant mt-1"></p>
                        <form id="terima-form" method="POST" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs font-bold text-on-surface-variant">Gudang Tujuan *</label>
                                <select name="penerimaan_id_gudang" required class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-sm">
                                    <option value="">-- Pilih Gudang --</option>
                                    @foreach(App\Models\Gudang::orderBy('gudang_nama')->get(['gudang_id','gudang_nama','gudang_kode']) as $g)
                                        <option value="{{ $g->gudang_id }}">{{ $g->gudang_nama }} — {{ $g->gudang_kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-on-surface-variant">Jumlah Diterima *</label>
                                <input type="number" name="jumlah" step="0.01" min="0.01" required class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-sm">
                            </div>
                            <div class="flex gap-2 justify-end pt-2">
                                <button type="button" onclick="closeTerimaModal()" class="h-9 px-4 rounded-lg bg-white border border-outline-variant text-sm font-semibold">Batal</button>
                                <button type="submit" class="h-9 px-4 rounded-lg bg-primary text-on-primary text-sm font-semibold">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                function openTerimaModal(itemId, nama, sisa){
                    var modal = document.getElementById('terima-modal');
                    var form = document.getElementById('terima-form');
                    var namaEl = document.getElementById('terima-barang-nama');
                    if(!modal || !form) return;
                    namaEl.textContent = nama + ' — Sisa ' + sisa;
                    form.action = "{{ url('/pesanan-pembelian') }}/{{ $model->pesanan_pembelian_id }}/terima/" + itemId;
                    form.querySelector('input[name="jumlah"]').value = sisa;
                    modal.classList.remove('hidden');
                }
                function closeTerimaModal(){
                    var modal = document.getElementById('terima-modal');
                    if(modal) modal.classList.add('hidden');
                }
                </script>
                @if(($penerimaans ?? collect())->isNotEmpty())
                <div class="col-span-12 mt-3 space-y-2">
                    <p class="text-[11px] font-bold tracking-widest text-on-surface-variant uppercase">Riwayat Penerimaan PO</p>
                    @foreach($penerimaans as $rcv)
                    <div class="p-2 rounded-xl border border-outline-variant/20 bg-white flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold font-mono text-primary">{{ $rcv->penerimaan_nomor }}</p>
                            <p class="text-[11px] text-on-surface-variant">{{ $rcv->penerimaan_tanggal ? formatDate($rcv->penerimaan_tanggal) : '-' }} • {{ $rcv->penerimaan_penerima }}</p>
                        </div>
                        <a href="{{ route('penerimaan.getUpdate', ['id' => $rcv->penerimaan_id]) }}" class="text-xs text-primary underline">Detail</a>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="col-span-12 mt-3 flex gap-2">
                    <a href="{{ route('penerimaan.getCreate') }}?penerimaan_id_pesanan={{ $model->pesanan_pembelian_id }}" class="inline-flex items-center gap-1 h-9 px-3 rounded-lg bg-primary/10 text-primary text-xs font-semibold">+ Penerimaan Baru</a>
                    <a href="{{ route('penerimaan.getTable') }}?filters[penerimaan_id_pesanan][$eq]={{ $model->pesanan_pembelian_id }}" class="inline-flex items-center h-9 px-3 rounded-lg bg-white border border-outline-variant text-xs font-semibold">Lihat List</a>
                </div>
            </x-card>
        </div>
        <div class="col-span-12 lg:col-span-5">
            <x-card label="Invoice (Status & PDF)" icon="receipt">
                @php
                    $invStatus = ($fakturs ?? collect())->first()?->faktur_status ?? ($model->pesanan_pembelian_status ?? 'draft');
                    $hasInv = ($fakturs ?? collect())->isNotEmpty();
                @endphp
                <div class="col-span-12 p-3 rounded-xl border bg-white flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold tracking-widest text-on-surface-variant uppercase">Status Invoice</p>
                        <p class="text-sm font-bold mt-1"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border bg-surface-container">{{ ucfirst($invStatus) }}</span></p>
                    </div>
                    <a href="{{ route('pesanan-pembelian.invoicePdf', ['id' => $model->pesanan_pembelian_id]) }}" class="inline-flex items-center gap-1 h-9 px-4 rounded-lg bg-primary text-on-primary text-xs font-semibold">
                        <span class="material-symbols-outlined text-lg">picture_as_pdf</span> PDF Invoice
                    </a>
                </div>
                @if(($fakturs ?? collect())->isNotEmpty())
                <div class="col-span-12 mt-3 space-y-2">
                    <p class="text-[11px] font-bold tracking-widest text-on-surface-variant uppercase">Riwayat Faktur</p>
                    @foreach($fakturs as $inv)
                    <div class="p-2 rounded-xl border border-outline-variant/20 bg-white flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold font-mono text-primary">{{ $inv->faktur_nomor }}</p>
                            <p class="text-[11px] text-on-surface-variant">{{ $inv->faktur_tanggal ? formatDate($inv->faktur_tanggal) : '-' }} • {{ formatRupiah($inv->faktur_total) }}</p>
                        </div>
                        <a href="{{ route('faktur.invoicePdf', ['id' => $inv->faktur_id]) }}" class="text-xs text-primary underline">PDF</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </x-card>
        </div>
    </div>
    @endif

    @php
        $scOptions = App\Models\SukuCadang::orderBy('suku_cadang_nama')->get(['suku_cadang_id','suku_cadang_nama','suku_cadang_kode','suku_cadang_harga'])->map(fn($s) => ['id'=>$s->suku_cadang_id,'label'=>$s->suku_cadang_nama.' — '.$s->suku_cadang_kode,'harga'=>(float)$s->suku_cadang_harga, 'hargaFmt'=>formatRupiah($s->suku_cadang_harga)])->values();
        // departmentBudgets dari controller sudah lengkap (budget/terpakai/pending/sisa/tersedia); fallback untuk view tanpa controller (mis. test)
        if (!isset($departmentBudgets)) {
            $departmentBudgets = App\Models\Department::all(['department_id','department_kode','department_nama','department_budget','department_budget_terpakai','department_periode'])
                ->mapWithKeys(function ($d) {
                    $terpakai = (float) $d->department_budget_terpakai;
                    $pending = App\Models\PermintaanSukuCadang::pendingDepartment($d->department_id);
                    $budget = (float) $d->department_budget;
                    return [$d->department_id => [
                        'id'=>$d->department_id,'kode'=>$d->department_kode,'nama'=>$d->department_nama,'periode'=>$d->department_periode,
                        'budget'=>$budget,'terpakai'=>$terpakai,'pending'=>$pending,'sisa'=>$budget-$terpakai,'tersedia'=>$budget-$terpakai-$pending,
                        'budgetFmt'=>formatRupiah($budget),'terpakaiFmt'=>formatRupiah($terpakai),'pendingFmt'=>formatRupiah($pending),'sisaFmt'=>formatRupiah($budget-$terpakai),'tersediaFmt'=>formatRupiah($budget-$terpakai-$pending),
                    ]];
                })->toArray();
        }
        $existingItems = isset($items) && $items instanceof \Illuminate\Support\Collection ? $items->map(fn($i) => ['suku_cadang_id'=>$i->pesanan_item_id_referensi,'jumlah'=>(float)$i->pesanan_item_jumlah,'harga'=>(float)$i->pesanan_item_harga])->values() : (isset($items) && is_array($items) ? collect($items)->map(fn($i) => ['suku_cadang_id'=>$i['pesanan_item_id_referensi'] ?? $i['suku_cadang_id'] ?? null,'jumlah'=>(float)($i['pesanan_item_jumlah'] ?? $i['jumlah'] ?? 1),'harga'=>(float)($i['pesanan_item_harga'] ?? $i['harga'] ?? 0)])->values() : collect([]));
    @endphp
    <script>
    (() => {
        const scOptions = @json($scOptions);
        const deptBudgets = @json($departmentBudgets);
        const existing = @json($existingItems);
        let idx = 0;
        const container = document.getElementById('po-items');
        const totalEl = document.getElementById('po-total');

        function fmtRupiah(n){
            return new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(n);
        }

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
            if (totalEl) totalEl.textContent = fmtRupiah(sum);
            updateOverBudget(sum);
            return sum;
        }

        function updateOverBudget(poTotal){
            const deptSel = document.querySelector('[name="pesanan_pembelian_kode_budget"]');
            const deptId = deptSel ? deptSel.value : '';
            const budgetEmpty = document.getElementById('budget-empty');
            const budgetInfo = document.getElementById('budget-info');
            // jika belum pilih department, tidak ada peringatan over-budget
            if (!deptId || !deptBudgets[deptId]) {
                const warn = document.getElementById('bi-warning');
                if (warn) warn.classList.add('hidden');
                return;
            }
            const d = deptBudgets[deptId];
            const warn = document.getElementById('bi-warning');
            if (!warn) return;
            if (poTotal > d.tersedia) {
                warn.classList.remove('hidden');
                warn.textContent = 'Total PO ' + fmtRupiah(poTotal) + ' melebihi tersedia ' + d.tersediaFmt + '!';
            } else if (poTotal > d.sisa) {
                warn.classList.remove('hidden');
                warn.textContent = 'Total PO melebihi sisa (tapi masih dalam pending reserve) — sisa ' + d.sisaFmt;
                warn.classList.remove('text-error');
                warn.classList.add('text-warning');
            } else {
                warn.classList.add('hidden');
            }
        }

        function renderBudget(deptId){
            const info = document.getElementById('budget-info');
            const empty = document.getElementById('budget-empty');
            if (!deptId || !deptBudgets[deptId]) {
                if (info) info.classList.add('hidden');
                if (empty) empty.classList.remove('hidden');
                const warn = document.getElementById('bi-warning');
                if (warn) warn.classList.add('hidden');
                return;
            }
            const d = deptBudgets[deptId];
            if (info) info.classList.remove('hidden');
            if (empty) empty.classList.add('hidden');
            const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            setText('bi-dept', d.nama + ' (' + d.kode + ')');
            setText('bi-periode', d.periode ? 'Periode ' + d.periode.charAt(0).toUpperCase() + d.periode.slice(1) : '');
            setText('bi-budget', d.budgetFmt);
            setText('bi-sisa', d.sisaFmt);
            setText('bi-terpakai', d.terpakaiFmt);
            setText('bi-pending', d.pendingFmt);
            setText('bi-tersedia', d.tersediaFmt);
            const pct = d.budget > 0 ? Math.min(100, ((d.terpakai + d.pending) / d.budget) * 100) : 0;
            const bar = document.getElementById('bi-bar');
            if (bar) bar.style.width = pct + '%';
            const over = d.tersedia < 0;
            const card = document.getElementById('bi-tersedia-card');
            const label = document.getElementById('bi-tersedia-label');
            const tersediaEl = document.getElementById('bi-tersedia');
            if (card) card.className = 'rounded-xl border p-3 ' + (over ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20');
            if (label) label.className = 'text-[10px] font-bold tracking-widest uppercase ' + (over ? 'text-error' : 'text-primary');
            if (tersediaEl) tersediaEl.className = 'text-sm font-bold mt-1 ' + (over ? 'text-error' : 'text-on-surface');
            if (bar) bar.className = 'h-full ' + (over ? 'bg-error' : 'bg-primary');
            // cek apakah PO total saat ini over budget
            calcTotal();
        }

        window.onDepartmentChange = function(sel){
            renderBudget(sel.value);
        };

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
        // sync budget display on load (update page sudah terpilih department)
        const initDept = document.querySelector('[name="pesanan_pembelian_kode_budget"]');
        if (initDept && initDept.value) renderBudget(initDept.value);
    })();
    </script>
</x-layouts::app>
