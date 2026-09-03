<?php /** @var App\Models\Tiket $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isPengguna = auth()->user()?->role === 'pengguna_aset'; $hasNomor = !empty($model?->tiket_nomor); @endphp
                @if($isPengguna && ! $hasNomor && ! ($model?->exists))
                    {{-- Nomor hide jika kosong (pengguna) — auto generate di controller --}}
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
                <div class="col-span-12 flex gap-2">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('qr-scan-wrap').classList.toggle('hidden')" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-outline-variant rounded-xl text-sm font-semibold">
                        <span class="material-symbols-outlined text-lg">qr_code_scanner</span> Scan QR Aset
                    </a>
                    <span class="text-xs text-on-surface-variant self-center">atau pilih manual di atas</span>
                </div>
                <div id="qr-scan-wrap" class="col-span-12 hidden">
                    <div id="qr-reader" class="rounded-xl overflow-hidden border border-outline-variant"></div>
                    <p class="text-xs text-on-surface-variant mt-2">Arahkan kamera ke QR aset — akan otomatis isi & reload ke form dengan <code>?aset_qr=...</code></p>
                </div>
                <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
                <script>
                (function(){
                    var wrap = document.getElementById('qr-scan-wrap');
                    var qr = null;
                    function onScan(decoded){
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
                    document.addEventListener('click', function(e){
                        if (e.target.closest('a') && e.target.closest('a').textContent.includes('Scan QR')) {
                            setTimeout(function(){
                                if (wrap.classList.contains('hidden')) {
                                    if (qr) { try{ qr.stop(); }catch(e){} qr=null; }
                                    return;
                                }
                                if (!window.Html5Qrcode) return;
                                qr = new Html5Qrcode("qr-reader");
                                qr.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScan).catch(function(){});
                            }, 100);
                        }
                    });
                })();
                </script>
                @endif
                <x-input col="12" name="tiket_judul" />
                @if($isPengguna)
                    <input type="hidden" name="tiket_id_pelapor" value="{{ auth()->id() }}">
                    <div class="col-span-12 p-3 bg-primary/5 border border-primary/10 rounded-xl text-sm">
                        <span class="font-semibold">Pelapor:</span> {{ auth()->user()->name }} (otomatis)
                    </div>
                @else
                    <x-select col="6" name="tiket_id_pelapor" :options="App\Models\User::getOptions()" />
                    <x-select col="6" name="tiket_id_teknisi" :options="App\Models\Teknisi::getOptions()" />
                @endif
                <x-select col="6" name="tiket_tingkat_urgensi" :options="App\Enums\Tiket\TingkatUrgensiEnum::getOptions()" />
                @if($isPengguna)
                    <input type="hidden" name="tiket_status" value="buka">
                    <div class="col-span-12 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm">Status: <b>Buka</b> — akan dinotifikasi ke teknisi & di-assign otomatis.</div>
                @else
                    <x-select col="6" name="tiket_status" :options="App\Enums\Tiket\StatusTiketEnum::getOptions()" />
                @endif
                @if(! $isPengguna)
                    <x-select col="6" name="tiket_id_batch" :options="App\Models\BatchTiket::getOptions()" />
                @endif

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
                    {{-- Lapor/tempo/biaya/rating/catatan/foto disembunyikan untuk pengguna — otomatis di controller --}}
                    <input type="hidden" name="tiket_tanggal_lapor" value="{{ now()->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="tiket_jatuh_tempo" value="">
                    <input type="hidden" name="tiket_biaya" value="0">
                    <input type="hidden" name="tiket_rating" value="">
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
                    <x-input col="6" type="datetime-local" name="tiket_tanggal_lapor" />
                    <x-input col="6" type="datetime-local" name="tiket_jatuh_tempo" />
                    <x-input col="6" type="number" step="0.01" name="tiket_biaya" />
                    <x-input col="6" type="number" step="0.01" name="tiket_rating" />
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
