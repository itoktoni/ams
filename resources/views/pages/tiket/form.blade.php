<?php /** @var App\Models\Tiket $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php
                    $isPengguna = auth()->user()?->role === 'pengguna_aset';
                    $isDeveloper = auth()->user()?->role === 'developer';
                    $isTeknisiUser = auth()->user()?->role === 'teknisi';
                    $isCreate = !isset($model) || !$model->exists;
                    $hasNomor = !empty($model?->tiket_nomor);
                @endphp
                {{-- Nomor: selalu hide di create, auto generate di controller --}}
                @if($isCreate)
                    <input type="hidden" name="tiket_nomor" value="">
                @else
                    <x-input col="6" name="tiket_nomor" :helper="$isPengguna ? 'Otomatis terisi jika kosong' : null" />
                @endif
                @if(!empty($qrAsetId))
                <div class="col-span-12 p-3 bg-primary/5 border border-primary/20 rounded-xl flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-primary">qr_code_2</span>
                    <span>QR terdeteksi: <b>{{ $qrAsetLabel }}</b> — aset otomatis terisi</span>
                    <a href="{{ route('tiket.getCreate') }}" class="ml-auto text-xs text-on-surface-variant underline">Hapus</a>
                </div>
                @endif
                <x-select col="12" name="tiket_id_aset" :options="$asetOptions ?? App\Models\Aset::getOptions()" :helper="$isPengguna ? 'Hanya aset yang di-assign kepada Anda' : null" />
                @if(empty($qrAsetId))
                <div class="col-span-12 flex flex-wrap gap-2 items-center">
                    <button type="button" id="btn-scan-qr" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-outline-variant rounded-xl text-sm font-semibold hover:bg-surface-container">
                        <span class="material-symbols-outlined text-lg">qr_code_scanner</span> Scan QR Aset
                    </button>
                    <span class="text-xs text-on-surface-variant">atau pilih manual di atas</span>
                    <span id="qr-scan-status" class="text-xs text-on-surface-variant hidden"></span>
                </div>
                <div id="qr-scan-wrap" class="col-span-12 hidden">
                    <div id="qr-reader" class="rounded-xl overflow-hidden border border-outline-variant min-h-[260px] bg-black/5"></div>
                    <p class="text-xs text-on-surface-variant mt-2">Arahkan kamera ke QR aset — akan otomatis isi & reload ke form dengan <code>?aset_qr=...</code></p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" id="btn-stop-qr" class="hidden inline-flex items-center gap-1 h-9 px-3 rounded-lg bg-error/10 text-error border border-error/20 text-sm font-semibold">Tutup Kamera</button>
                        <label class="inline-flex items-center gap-1 h-9 px-3 rounded-lg bg-surface-container border border-outline-variant text-sm font-semibold cursor-pointer">
                            <span class="material-symbols-outlined text-lg">upload</span> Upload Gambar QR
                            <input type="file" id="qr-file-input" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <p id="qr-scan-error" class="text-xs text-error mt-2 hidden"></p>
                </div>
                <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
                <script>
                (function(){
                    var wrap = document.getElementById('qr-scan-wrap');
                    var readerEl = document.getElementById('qr-reader');
                    var btnScan = document.getElementById('btn-scan-qr');
                    var btnStop = document.getElementById('btn-stop-qr');
                    var fileInput = document.getElementById('qr-file-input');
                    var errEl = document.getElementById('qr-scan-error');
                    var statusEl = document.getElementById('qr-scan-status');
                    var qr = null;
                    var isStarting = false;

                    function showError(msg){
                        if(!errEl) return;
                        errEl.textContent = msg;
                        errEl.classList.remove('hidden');
                    }
                    function clearError(){
                        if(!errEl) return;
                        errEl.textContent = '';
                        errEl.classList.add('hidden');
                    }
                    function setStatus(msg){
                        if(!statusEl) return;
                        statusEl.textContent = msg;
                        statusEl.classList.remove('hidden');
                    }
                    function clearStatus(){
                        if(!statusEl) return;
                        statusEl.textContent = '';
                        statusEl.classList.add('hidden');
                    }
                    function onScan(decoded){
                        clearError();
                        try {
                            var url = new URL(decoded);
                            var kode = url.searchParams.get('aset_qr') || url.pathname.split('/').pop();
                            if (kode) window.location.href = '{{ route('tiket.getCreate') }}?aset_qr=' + encodeURIComponent(kode);
                            else window.location.href = decoded;
                        } catch(e) {
                            var kode = decoded.trim().split('/').pop().split('?')[0];
                            window.location.href = '{{ route('tiket.getCreate') }}?aset_qr=' + encodeURIComponent(kode);
                        }
                    }
                    function stopQr(){
                        if(qr){
                            try{ qr.stop(); }catch(e){}
                            try{ qr.clear(); }catch(e){}
                            qr = null;
                        }
                        if(btnStop) btnStop.classList.add('hidden');
                        clearStatus();
                    }
                    function startQr(){
                        if(isStarting) return;
                        if(!window.Html5Qrcode){
                            showError('Library QR belum termuat. Periksa koneksi internet.');
                            return;
                        }
                        // Kamera butuh HTTPS atau localhost — beri pesan jelas jika http://ams.test tanpa SSL
                        if(!window.isSecureContext){
                            showError('Kamera diblokir browser karena halaman http (tidak aman). Buka via https://ams.test atau gunakan Upload Gambar QR.');
                            // tetap coba start — di beberapa browser tetap bisa di http://ams.test jika di-host file, tapi beri warning
                        }
                        clearError();
                        setStatus('Meminta izin kamera…');
                        isStarting = true;
                        qr = new Html5Qrcode("qr-reader");
                        qr.start({ facingMode: "environment" }, { fps: 10, qrbox: {width:250, height:250} }, onScan, function(){})
                          .then(function(){
                              isStarting = false;
                              setStatus('Kamera aktif — arahkan ke QR aset');
                              if(btnStop) btnStop.classList.remove('hidden');
                          })
                          .catch(function(err){
                              isStarting = false;
                              var msg = (err && err.message) ? err.message : String(err);
                              if(msg.indexOf('NotAllowedError')!==-1 || msg.indexOf('Permission')!==-1){
                                  showError('Izin kamera ditolak. Aktifkan izin kamera di browser.');
                              } else if(msg.indexOf('NotFoundError')!==-1){
                                  showError('Kamera tidak ditemukan di perangkat ini.');
                              } else if(!window.isSecureContext){
                                  showError('Gagal akses kamera (http tidak aman). Gunakan https://ams.test atau Upload Gambar QR. Detail: '+msg);
                              } else {
                                  showError('Gagal memulai kamera: '+msg);
                              }
                              clearStatus();
                              stopQr();
                          });
                    }
                    if(btnScan){
                        btnScan.addEventListener('click', function(e){
                            e.preventDefault();
                            if(!wrap) return;
                            var willShow = wrap.classList.contains('hidden');
                            if(willShow){
                                wrap.classList.remove('hidden');
                                startQr();
                            } else {
                                wrap.classList.add('hidden');
                                stopQr();
                                clearError();
                            }
                        });
                    }
                    if(btnStop){
                        btnStop.addEventListener('click', function(){
                            if(wrap) wrap.classList.add('hidden');
                            stopQr();
                            clearError();
                        });
                    }
                    // Fallback: scan dari file gambar
                    if(fileInput){
                        fileInput.addEventListener('change', function(e){
                            var file = e.target.files[0];
                            if(!file) return;
                            if(!window.Html5Qrcode){
                                showError('Library QR belum termuat.');
                                return;
                            }
                            clearError();
                            setStatus('Memproses gambar…');
                            var tmpQr = new Html5Qrcode("qr-reader");
                            tmpQr.scanFile(file, true).then(onScan).catch(function(err){
                                showError('Gagal baca QR dari gambar: '+(err && err.message ? err.message : err));
                                clearStatus();
                            });
                            // reset agar bisa pilih file yang sama lagi
                            e.target.value = '';
                        });
                    }
                    // Bersihkan kamera saat pindah halaman (Livewire navigate)
                    document.addEventListener('livewire:navigating', stopQr);
                    window.addEventListener('beforeunload', stopQr);
                })();
                </script>
                @endif
                {{-- Judul: hide, otomatis terbentuk saat simpan: ASSET : [nama asset] DI LOKASI : [nama lokasi] TELAH TERJADI : [deskripsi] DI LAPORKAN OLEH : [pelapor] --}}
                <input type="hidden" name="tiket_judul" value="{{ old('tiket_judul', $model->tiket_judul ?? '') }}">
                {{-- Pelapor: ikut user login, hanya developer bisa pilih manual --}}
                @if($isCreate)
                    @if($isDeveloper)
                        <x-select col="6" name="tiket_id_pelapor" :options="App\Models\User::getOptions()" />
                    @else
                        <input type="hidden" name="tiket_id_pelapor" value="{{ auth()->id() }}">
                        <div class="col-span-12 p-3 bg-primary/5 border border-primary/10 rounded-xl text-sm">
                            <span class="font-semibold">Pelapor:</span> {{ auth()->user()->name }} (otomatis)
                        </div>
                    @endif
                    {{-- Teknisi: diambil dari group aset, hide di create --}}
                    <input type="hidden" name="tiket_id_teknisi" value="">
                @else
                    @if($isPengguna)
                        <input type="hidden" name="tiket_id_pelapor" value="{{ auth()->id() }}">
                        <div class="col-span-12 p-3 bg-primary/5 border border-primary/10 rounded-xl text-sm">
                            <span class="font-semibold">Pelapor:</span> {{ auth()->user()->name }} (otomatis)
                        </div>
                    @else
                        @if($isDeveloper)
                            <x-select col="6" name="tiket_id_pelapor" :options="App\Models\User::getOptions()" />
                        @else
                            <input type="hidden" name="tiket_id_pelapor" value="{{ $model->tiket_id_pelapor ?? auth()->id() }}">
                            <div class="col-span-12 p-3 bg-primary/5 border border-primary/10 rounded-xl text-sm">
                                <span class="font-semibold">Pelapor:</span> {{ $model->hasPelapor?->name ?? auth()->user()->name }} (otomatis)
                            </div>
                        @endif
                        <x-select col="6" name="tiket_id_teknisi" :options="App\Models\Teknisi::getOptions()" />
                    @endif
                @endif
                {{-- Urgensi: hide, tapi show jika developer atau teknisi --}}
                @if($isCreate)
                    @if($isDeveloper || $isTeknisiUser)
                        <x-select col="6" name="tiket_tingkat_urgensi" :options="App\Enums\Tiket\TingkatUrgensiEnum::getOptions()" />
                    @else
                        <input type="hidden" name="tiket_tingkat_urgensi" value="sedang">
                    @endif
                @else
                    <x-select col="6" name="tiket_tingkat_urgensi" :options="App\Enums\Tiket\TingkatUrgensiEnum::getOptions()" />
                @endif
                {{-- Status, Batch, Lapor, Tempo, Biaya, Rating: hide di create & update — tempo akan terisi saat teknisi mengambil, biaya/rating dihapus --}}
                <input type="hidden" name="tiket_status" value="{{ $isCreate ? 'buka' : ($model->tiket_status ?? 'buka') }}">
                @if($isCreate)
                    <div class="col-span-12 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm">Status: <b>Buka</b> — akan dinotifikasi ke teknisi group & di-assign otomatis.</div>
                @endif
                <input type="hidden" name="tiket_id_batch" value="{{ $isCreate ? '' : ($model->tiket_id_batch ?? '') }}">
                <input type="hidden" name="tiket_tanggal_lapor" value="{{ $isCreate ? now()->format('Y-m-d\TH:i') : old('tiket_tanggal_lapor', $model->tiket_tanggal_lapor ? \Carbon\Carbon::parse($model->tiket_tanggal_lapor)->format('Y-m-d\TH:i') : '') }}">
                <input type="hidden" name="tiket_jatuh_tempo" value="{{ $isCreate ? '' : old('tiket_jatuh_tempo', $model->tiket_jatuh_tempo ? \Carbon\Carbon::parse($model->tiket_jatuh_tempo)->format('Y-m-d\TH:i') : '') }}">
                <input type="hidden" name="tiket_biaya" value="{{ $isCreate ? '0' : old('tiket_biaya', $model->tiket_biaya ?? '0') }}">
                <input type="hidden" name="tiket_rating" value="{{ $isCreate ? '' : old('tiket_rating', $model->tiket_rating ?? '') }}">

                <x-textarea col="12" name="tiket_deskripsi" label="Deskripsi Kerusakan" helper="Jelaskan kerusakan aset Anda — mis. Mobil tidak bisa start, AC tidak dingin" />

                @if($isPengguna)
                    {{-- Lokasi mengikuti aset — auto dari aset_id_lokasi; jika aset tanpa lokasi, tampilkan pilihan manual --}}
                    <input type="hidden" name="tiket_id_lokasi" value="{{ $model?->tiket_id_lokasi ?? '' }}" id="tiket_id_lokasi_auto">
                    <div class="col-span-12 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant hidden" id="aset-lokasi-info">
                        <span class="material-symbols-outlined text-base align-middle">location_on</span>
                        Lokasi aset: <span class="font-medium text-on-surface" id="aset-lokasi-nama"></span>
                    </div>
                    <div class="col-span-12 hidden" id="tiket-lokasi-manual-wrap">
                        <x-select col="6" name="tiket_id_lokasi_manual" :options="App\Models\LokasiAset::getOptions()" label="Lokasi" helper="Aset ini belum punya lokasi — silakan pilih lokasi manual" />
                    </div>
                    <input type="hidden" name="tiket_catatan" value="">
                @else
                    <x-select col="6" name="tiket_id_lokasi" :options="App\Models\LokasiAset::getOptions()" />
                    <div class="col-span-12 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant hidden" id="aset-lokasi-info">
                        <span class="material-symbols-outlined text-base align-middle">location_on</span>
                        Lokasi aset: <span class="font-medium text-on-surface" id="aset-lokasi-nama"></span>
                    </div>
                    <div class="col-span-12 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm hidden" id="aset-lokasi-kosong-info">
                        <span class="material-symbols-outlined text-base align-middle">warning</span>
                        Aset ini belum memiliki lokasi — silakan pilih lokasi manual di atas.
                    </div>
                @endif

                @if($isPengguna)
                    {{-- catatan & foto disembunyikan untuk pengguna --}}
                @else
                    <x-textarea col="12" name="tiket_catatan" />
                    <x-file
                        name="tiket_foto_sebelum"
                        label="Foto Sebelum"
                        col="6"
                        accept="image/*"
                        capture="environment"
                        :preview="true"
                        :value="$model?->tiket_foto_sebelum_url"
                        helper="Ambil foto via kamera di HP atau pilih dari galeri" />

                    <x-file
                        name="tiket_foto_sesudah"
                        label="Foto Sesudah"
                        col="6"
                        accept="image/*"
                        capture="environment"
                        :preview="true"
                        :value="$model?->tiket_foto_sesudah_url"
                        helper="Ambil foto via kamera di HP atau pilih dari galeri" />
                @endif

            @endbind
        </x-card>

        @php $lokasiMapJson = json_encode($asetLokasiMap ?? [], JSON_UNESCAPED_UNICODE); $qrAsetIdJson = json_encode($qrAsetId ?? null); @endphp
        <script>
            (function () {
                var lokasiMap = {!! $lokasiMapJson !!};
                var qrAsetId = {!! $qrAsetIdJson !!};
                var selAset = document.getElementById('select-tiket_id_aset');
                if (!selAset) return;
                if (qrAsetId) {
                    selAset.value = String(qrAsetId);
                    selAset.dispatchEvent(new Event('change', {bubbles:true}));
                }

                var infoBox = document.getElementById('aset-lokasi-info');
                var infoNama = document.getElementById('aset-lokasi-nama');
                var kosongBox = document.getElementById('aset-lokasi-kosong-info');
                var manualWrap = document.getElementById('tiket-lokasi-manual-wrap');
                var hiddenAuto = document.getElementById('tiket_id_lokasi_auto');
                var selLokasi = document.getElementById('select-tiket_id_lokasi');

                function apply() {
                    var asetId = selAset.value;
                    var lokasi = asetId && lokasiMap[asetId] ? lokasiMap[asetId] : null;
                    var hasLokasi = !!lokasi;

                    // info lokasi aset
                    if (infoBox) {
                        if (hasLokasi) {
                            if (infoNama) infoNama.textContent = lokasi.nama;
                            infoBox.classList.remove('hidden');
                        } else {
                            infoBox.classList.add('hidden');
                        }
                    }
                    // peringatan aset tanpa lokasi (non-pengguna)
                    if (kosongBox) {
                        if (asetId && !hasLokasi) kosongBox.classList.remove('hidden');
                        else kosongBox.classList.add('hidden');
                    }
                    // auto-set lokasi
                    if (hiddenAuto) {
                        hiddenAuto.value = hasLokasi ? lokasi.id : '';
                    }
                    if (selLokasi) {
                        if (hasLokasi && !selLokasi.value) {
                            selLokasi.value = lokasi.id;
                        }
                    }
                    // aset tanpa lokasi → tampilkan pilihan lokasi manual (pengguna)
                    if (manualWrap) {
                        if (asetId && !hasLokasi) manualWrap.classList.remove('hidden');
                        else manualWrap.classList.add('hidden');
                    }
                }

                selAset.addEventListener('change', apply);
                // prefill saat load (mode update)
                apply();
            })();
        </script>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
