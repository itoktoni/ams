<?php /** @var App\Models\Opname $opname */ ?>
<x-layouts::app>
    <x-breadcrumb :items="[['url' => route('opname.getTable'), 'label' => 'Opname'], ['url' => route('opname.getUpdate', ['id' => $opname->opname_id]), 'label' => $opname->opname_nomor], ['url' => '', 'label' => 'Scan']]" />

    <div class="bg-white border border-outline-variant rounded-2xl p-4 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-base">{{ $opname->opname_nomor }} — {{ $opname->hasLokasi?->aset_lokasi_nama ?? '-' }}</h2>
                <p class="text-xs text-on-surface-variant">{{ formatDate($opname->opname_tanggal_mulai) }} s/d {{ formatDate($opname->opname_tanggal_selesai) }} • {{ $opname->opname_status }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('opname.getUpdate', ['id' => $opname->opname_id]) }}" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-xl bg-white border border-outline-variant text-sm font-semibold">
                    <span class="material-symbols-outlined text-lg">edit</span> Edit
                </a>
                <a href="{{ route('opname.getReport', ['id' => $opname->opname_id]) }}" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-xl bg-primary text-on-primary text-sm font-semibold">
                    <span class="material-symbols-outlined text-lg">assessment</span> Report
                </a>
            </div>
        </div>
    </div>

    <livewire:opname-scanner :opnameId="$opname->opname_id" />

    <div class="bg-white border border-outline-variant rounded-2xl p-4 mt-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" id="opname-scan-toggle" class="inline-flex items-center gap-2 h-10 px-4 bg-primary text-on-primary rounded-xl text-sm font-semibold">
                <span class="material-symbols-outlined">qr_code_scanner</span> <span id="opname-scan-toggle-label">Buka QR Scanner</span>
            </button>
        </div>
        <div id="opname-qr-wrap" class="hidden mt-3">
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
</x-layouts::app>
