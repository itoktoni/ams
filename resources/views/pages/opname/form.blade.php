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
    @php $progress = $model->progress; @endphp
    <div class="mt-6 space-y-4">
        <div class="bg-white border border-outline-variant rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-sm flex items-center gap-2"><span class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-lg">qr_code_scanner</span></span> Scan Opname — {{ $model->opname_nomor }}</h3>
                <a href="{{ route('opname.getReport', ['id' => $model->opname_id]) }}" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg bg-warning/10 text-warning text-sm font-semibold">Report</a>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                <div class="bg-surface-container rounded-xl py-3"><p class="text-lg font-bold">{{ $progress['total'] }}</p><p class="text-[11px] text-on-surface-variant">Total</p></div>
                <div class="bg-success/10 rounded-xl py-3"><p class="text-lg font-bold text-success">{{ $progress['found'] }}</p><p class="text-[11px] text-success">Ditemukan</p></div>
                <div class="bg-error/10 rounded-xl py-3"><p class="text-lg font-bold text-error">{{ $progress['missing'] }}</p><p class="text-[11px] text-error">Belum</p></div>
            </div>
            <div class="h-2 bg-surface-container rounded-full overflow-hidden mb-4"><div class="h-full bg-success" style="width: {{ $progress['pct'] }}%"></div></div>

            <div class="mb-3">
                <livewire:opname-scanner :opnameId="$model->opname_id" />
            </div>
            <div class="flex flex-wrap gap-2 mb-3">
                <button type="button" id="opname-scan-toggle" class="inline-flex items-center gap-2 h-10 px-4 bg-primary text-on-primary rounded-xl text-sm font-semibold">
                    <span class="material-symbols-outlined">qr_code_scanner</span> <span id="opname-scan-toggle-label">Buka QR Scanner</span>
                </button>
                <span class="text-xs text-on-surface-variant self-center">Kamera — hasil otomatis masuk ke Livewire di atas</span>
            </div>
            <div id="opname-qr-wrap" class="hidden mb-3">
                <div id="opname-qr-reader" class="rounded-xl overflow-hidden border border-outline-variant min-h-[240px] bg-black/5 flex items-center justify-center text-xs text-on-surface-variant">Kamera belum aktif — izinkan akses kamera</div>
                <p id="opname-scan-msg" class="text-sm font-medium mt-2"></p>
            </div>
            <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
            <script>
            (function(){
                var wrap = document.getElementById('opname-qr-wrap');
                var msg = document.getElementById('opname-scan-msg');
                var btnToggle = document.getElementById('opname-scan-toggle');
                var btnLabel = document.getElementById('opname-scan-toggle-label');
                var qr = null;
                function extractKode(decoded){
                    decoded = (decoded || '').trim();
                    try {
                        var u = new URL(decoded);
                        return u.searchParams.get('aset_qr') || u.searchParams.get('kode') || u.pathname.split('/').filter(Boolean).pop() || decoded;
                    } catch(e) {
                        return decoded.split('/').pop().split('?')[0].trim();
                    }
                }
                function getCamerasAndStart(){
                    Html5Qrcode.getCameras().then(function(cams){
                        if (!cams || !cams.length) throw new Error('Tidak ada kamera ditemukan');
                        var camId = cams.length > 1 ? cams[cams.length-1].id : cams[0].id;
                        qr = new Html5Qrcode("opname-qr-reader");
                        return qr.start(camId, { fps: 10, qrbox: { width: 250, height: 250 } }, function(decoded){
                            var inp = document.querySelector('input[wire\\:model="kode"]');
                            if(inp){ inp.value = extractKode(decoded); inp.dispatchEvent(new Event('input', {bubbles:true})); inp.dispatchEvent(new KeyboardEvent('keydown', {key:'Enter', bubbles:true})); inp.focus(); }
                        }, function(){});
                    }).then(function(){ msg.textContent = 'Kamera aktif — arahkan ke QR aset'; msg.className = 'text-sm text-success mt-2'; })
                    .catch(function(err){
                        var name = err && err.name ? err.name : '';
                        if (name === 'NotFoundError' || /not found/i.test(String(err))) {
                            msg.innerHTML = 'Tidak ada kamera. Gunakan <b>input Livewire</b> di atas (ketik kode lalu Enter).';
                        } else if (name === 'NotAllowedError' || /permission/i.test(String(err))) {
                            msg.textContent = 'Izin kamera ditolak — izinkan di browser lalu coba lagi.';
                        } else {
                            msg.textContent = 'Gagal akses kamera: ' + (err && err.message ? err.message : String(err));
                        }
                        msg.className = 'text-sm text-error mt-2';
                    });
                }
                function openScanner(){
                    if (!window.Html5Qrcode) {
                        msg.textContent = 'Scanner kamera tidak tersedia (html5-qrcode gagal load)';
                        msg.className = 'text-sm text-error mt-2';
                        return;
                    }
                    wrap.classList.remove('hidden');
                    if (btnLabel) btnLabel.textContent = 'Tutup QR Scanner';
                    msg.textContent = 'Meminta izin kamera...';
                    msg.className = 'text-sm text-on-surface-variant mt-2';
                    getCamerasAndStart();
                }
                function closeScanner(){
                    wrap.classList.add('hidden');
                    if (btnLabel) btnLabel.textContent = 'Buka QR Scanner';
                    if (qr) { try{ qr.stop(); qr.clear(); }catch(e){} qr=null; }
                    msg.textContent = '';
                }
                btnToggle.addEventListener('click', function(){
                    if (wrap.classList.contains('hidden')) { openScanner(); } else { closeScanner(); }
                });
                window.addEventListener('beforeunload', function(){ if (qr) { try{ qr.stop(); qr.clear(); }catch(e){} } });
            })();
            </script>
        </div>

    </div>
    @endif
</x-layouts::app>
