<?php /** @var App\Models\Peminjaman $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    @if(!empty($budgetInfo))
    <div class="mb-4 grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Department</p>
            <p class="text-sm font-bold mt-1">{{ $budgetInfo['department']->department_nama }} ({{ $budgetInfo['department']->department_kode }})</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/20 p-3">
            <p class="text-[10px] font-bold tracking-widest text-on-surface-variant/70 uppercase">Budget</p>
            <p class="text-sm font-bold mt-1">{{ formatRupiah($budgetInfo['department']->department_budget) }}</p>
        </div>
        <div class="rounded-xl border p-3 {{ $budgetInfo['sisa'] < 0 ? 'bg-error-container border-error' : 'bg-primary/5 border-primary/20' }}">
            <p class="text-[10px] font-bold tracking-widest uppercase {{ $budgetInfo['sisa'] < 0 ? 'text-error' : 'text-primary' }}">Sisa</p>
            <p class="text-sm font-bold mt-1 {{ $budgetInfo['sisa'] < 0 ? 'text-error' : 'text-on-surface' }}">{{ formatRupiah($budgetInfo['sisa']) }}</p>
        </div>
    </div>
    @endif

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                {{-- Simple untuk semua role — seperti pengguna_aset --}}
                <x-select col="12" name="peminjaman_id_aset" :options="$asetOptions ?? App\Models\Aset::getOptions()" label="Aset" helper="Pilih aset yang ingin dipinjam" class="search" />
                <div class="col-span-12 hidden" id="aset-lokasi-info">
                    <div class="flex items-center gap-2 p-3 rounded-xl text-sm bg-primary/5 border border-primary/10">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        <span>Asal: <b id="aset-lokasi-nama" class="text-on-surface"></b></span>
                    </div>
                </div>
                <div class="col-span-12 hidden" id="aset-availability-info">
                    <div class="flex items-center gap-2 p-3 rounded-xl text-sm" id="aset-availability-box">
                        <span id="aset-availability-dot" class="w-2.5 h-2.5 rounded-full shrink-0"></span>
                        <span id="aset-availability-text" class="font-medium"></span>
                    </div>
                </div>
                <x-input col="12" type="datetime-local" name="peminjaman_jatuh_tempo" label="Rencana Tanggal Kembali" />
                <x-input col="12" name="peminjaman_tujuan" label="Keperluan" placeholder="Untuk apa meminjam?" />
                <div class="col-span-12 hidden" id="peminjaman-waiting-info">
                    <div class="flex items-center gap-2 p-3 rounded-xl text-sm bg-amber-50 border border-amber-200">
                        <span class="material-symbols-outlined text-amber-600">hourglass_top</span>
                        <span><b id="waiting-count">0</b> orang menunggu — <a href="#" id="waiting-link" class="text-primary underline">lihat Daftar Tunggu</a></span>
                    </div>
                </div>
                <div class="col-span-12 hidden" id="peminjaman-active-info">
                    <div class="p-3 rounded-xl text-sm bg-surface-container border border-outline-variant">
                        <p class="font-semibold">Sedang dipinjam oleh <span id="active-peminjam">-</span></p>
                        <p class="text-xs text-on-surface-variant">Jatuh tempo <span id="active-jatuh-tempo">-</span> • <span id="active-status">aktif</span></p>
                    </div>
                </div>
                <input type="hidden" name="peminjaman_tanggal_pinjam" value="{{ $model->peminjaman_tanggal_pinjam ?? $nowValue ?? now()->format('Y-m-d\TH:i') }}">
                <input type="hidden" name="peminjaman_id_peminjam" value="{{ $model->peminjaman_id_peminjam ?? auth()->id() }}">
                <input type="hidden" name="peminjaman_id_approver" value="{{ $model->peminjaman_id_approver ?? $approverId ?? auth()->id() }}">

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @if(isset($model) && $model->exists)
    @php $canApprove = in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true); $st = $model->peminjaman_status ?? ''; @endphp
    @if($canApprove && $st === 'diajukan')
    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ route('peminjaman.getApprove', ['id' => $model->peminjaman_id]) }}" onclick="return confirm('Setujui peminjaman ini?')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-success text-white text-sm font-semibold hover:bg-success/90">
            <span class="material-symbols-outlined">check_circle</span> Approve
        </a>
        <button type="button" onclick="document.getElementById('reject-box').classList.toggle('hidden')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-error/90">
            <span class="material-symbols-outlined">cancel</span> Tolak
        </button>
    </div>
    <div id="reject-box" class="hidden mt-3 bg-white border border-outline-variant rounded-2xl p-4">
        <p class="text-sm font-semibold mb-2">Alasan penolakan <span class="text-error">*</span></p>
        <form method="GET" action="{{ route('peminjaman.getReject', ['id' => $model->peminjaman_id]) }}" onsubmit="if(!this.catatan.value.trim()){alert('Catatan wajib diisi');return false;} return confirm('Tolak peminjaman ini?')">
            <textarea name="catatan" rows="3" placeholder="Tulis alasan penolakan..." class="w-full px-3 py-2 border border-outline-variant rounded-xl text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none" required>{{ $model->peminjaman_catatan ?? '' }}</textarea>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="h-9 px-4 bg-error text-white rounded-xl text-sm font-semibold">Kirim Penolakan</button>
                <button type="button" onclick="document.getElementById('reject-box').classList.add('hidden')" class="h-9 px-4 bg-white border border-outline-variant rounded-xl text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
    @if($model->peminjaman_catatan)
    <div class="mt-3 p-3 rounded-xl bg-surface-container text-sm">
        <span class="text-xs font-bold text-on-surface-variant uppercase">Catatan:</span>
        <p class="mt-1">{{ $model->peminjaman_catatan }}</p>
    </div>
    @endif
    @elseif($canApprove && $st === 'aktif')
    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ route('peminjaman.getReturn', ['id' => $model->peminjaman_id]) }}" onclick="return confirm('Tandai aset sudah dikembalikan?')" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-primary text-on-primary text-sm font-semibold">
            <span class="material-symbols-outlined">assignment_return</span> Tandai Dikembalikan
        </a>
    </div>
    @elseif($st === 'ditolak' && $model->peminjaman_catatan)
    <div class="mt-4 p-3 rounded-xl bg-error/10 border border-error/20 text-sm">
        <p class="text-xs font-bold text-error uppercase">Ditolak — Alasan:</p>
        <p class="mt-1 text-error">{{ $model->peminjaman_catatan }}</p>
    </div>
    @endif
    @endif

    @php $asetLokasiMapJson = json_encode($asetLokasiMap ?? [], JSON_UNESCAPED_UNICODE); @endphp
    <script>
        (function(){
            var lokasiMap = {!! $asetLokasiMapJson !!};
            var availMap = {{ Js::from($availabilityMap ?? []) }};
            var waitingMap = {{ Js::from($waitingCountMap ?? []) }};
            var activeMap = {{ Js::from(collect($activePeminjamanMap ?? [])->map(fn($p) => ['peminjam' => $p->hasPeminjam?->name ?? '-', 'jatuh_tempo' => $p->peminjaman_jatuh_tempo ? formatDate($p->peminjaman_jatuh_tempo) : '-', 'status' => $p->peminjaman_status])->toArray()) }};
            var selAset = document.getElementById('select-peminjaman_id_aset');
            if (!selAset) return;
            var info = document.getElementById('aset-lokasi-info');
            var infoNama = document.getElementById('aset-lokasi-nama');
            var info2 = document.getElementById('aset-availability-info');
            var box = document.getElementById('aset-availability-box');
            var dot = document.getElementById('aset-availability-dot');
            var txt = document.getElementById('aset-availability-text');
            var wInfo = document.getElementById('peminjaman-waiting-info');
            var wCount = document.getElementById('waiting-count');
            var wLink = document.getElementById('waiting-link');
            var aInfo = document.getElementById('peminjaman-active-info');
            var aPeminjam = document.getElementById('active-peminjam');
            var aJatuh = document.getElementById('active-jatuh-tempo');
            var aStatus = document.getElementById('active-status');
            function apply(){
                var id = selAset.value;
                var lokasi = id && lokasiMap[id] ? lokasiMap[id] : null;
                if (info) {
                    if (lokasi) { if(infoNama) infoNama.textContent = lokasi.nama; info.classList.remove('hidden'); }
                    else info.classList.add('hidden');
                }
                if (info2) {
                    info2.classList.toggle('hidden', !id);
                    if (!id) {
                        if (wInfo) wInfo.classList.add('hidden');
                        if (aInfo) aInfo.classList.add('hidden');
                        return;
                    }
                    var available = availMap[id] !== false;
                    if (info2 && box) {
                        if (available) {
                            box.className = 'flex items-center gap-2 p-3 rounded-xl text-sm bg-emerald-50 border border-emerald-200';
                            dot.className = 'w-2.5 h-2.5 rounded-full shrink-0 bg-emerald-500';
                            txt.className = 'font-medium text-emerald-700';
                            txt.textContent = 'Aset tersedia — bisa dipinjam';
                        } else {
                            box.className = 'flex items-center gap-2 p-3 rounded-xl text-sm bg-amber-50 border border-amber-200';
                            dot.className = 'w-2.5 h-2.5 rounded-full shrink-0 bg-amber-500';
                            txt.className = 'font-medium text-amber-700';
                            txt.textContent = 'Aset sedang dipinjam — masuk Daftar Tunggu';
                        }
                    }
                    var cnt = waitingMap[id] || 0;
                    if (wInfo) {
                        if (cnt > 0) { wCount.textContent = cnt; wLink.href = '{{ url("/daftar-tunggu/table") }}?filters[daftar_tunggu_id_aset][$eq]=' + id; wInfo.classList.remove('hidden'); }
                        else wInfo.classList.add('hidden');
                    }
                    var active = activeMap[id];
                    if (aInfo) {
                        if (active) { aPeminjam.textContent = active.peminjam; aJatuh.textContent = active.jatuh_tempo; aStatus.textContent = active.status; aInfo.classList.remove('hidden'); }
                        else aInfo.classList.add('hidden');
                    }
                }
            }
            selAset.addEventListener('change', apply);
            apply();
        })();
    </script>
</x-layouts::app>
