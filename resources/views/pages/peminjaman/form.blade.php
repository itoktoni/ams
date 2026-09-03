<?php /** @var App\Models\Peminjaman $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isUserMode = $isUserMode ?? false; @endphp

                @if($isUserMode)
                    {{-- ===== FORM SEDERHANA UNTUK USER ===== --}}
                    <x-select col="12" name="peminjaman_id_aset" :options="$asetOptions ?? []" helper="Hanya aset yang di-assign kepada Anda" />

                    {{-- info availability aset terpilih --}}
                    <div class="col-span-12 hidden" id="aset-availability-info">
                        <div class="flex items-center gap-2 p-3 rounded-xl text-sm" id="aset-availability-box">
                            <span id="aset-availability-dot" class="w-2.5 h-2.5 rounded-full shrink-0"></span>
                            <span id="aset-availability-text" class="font-medium"></span>
                        </div>
                    </div>

                    {{-- approver otomatis --}}
                    <input type="hidden" name="peminjaman_id_approver" value="{{ $approverId }}">
                    <input type="hidden" name="peminjaman_id_peminjam" value="{{ auth()->id() }}">
                    <div class="col-span-12 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant">
                        Approval: <span class="font-semibold text-on-surface">{{ $approverName }}</span> (otomatis)
                    </div>

                    {{-- tanggal pinjam otomatis now --}}
                    <input type="hidden" name="peminjaman_tanggal_pinjam" value="{{ $nowValue }}">
                    <div class="col-span-12 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant">
                        Tanggal pinjam: <span class="font-semibold text-on-surface">sekarang</span> (otomatis)
                    </div>

                    {{-- user hanya isi rencana tanggal kembali --}}
                    <x-input col="12" type="datetime-local" name="peminjaman_jatuh_tempo" label="Rencana Tanggal Kembali" />

                    {{-- tujuan sederhana (opsional) --}}
                    <x-input col="12" name="peminjaman_tujuan" label="Keperluan (opsional)" />

                    {{-- field otomatis / disembunyikan --}}
                    <input type="hidden" name="peminjaman_nomor" value="">
                    <input type="hidden" name="peminjaman_status" value="diajukan">
                    <input type="hidden" name="peminjaman_grace_jam" value="">
                    <input type="hidden" name="peminjaman_denda" value="">
                    <input type="hidden" name="peminjaman_perpanjang_ke" value="">
                    <input type="hidden" name="peminjaman_catatan" value="">
                    <input type="hidden" name="peminjaman_tanggal_kembali" value="">
                    <input type="hidden" name="peminjaman_kondisi_kembali" value="">
                    <input type="hidden" name="is_daftar_tunggu" id="is-daftar-tunggu" value="0">

                @else
                    {{-- ===== FORM LENGKAP UNTUK ADMIN ===== --}}
                    <x-input col="6" name="peminjaman_nomor" />
                    <x-select col="6" name="peminjaman_status" :options="App\Enums\Peminjaman\StatusPeminjamanEnum::getOptions()" />

                    <x-select col="6" name="peminjaman_id_aset" :options="App\Models\Aset::getOptions()" />
                    <x-select col="6" name="peminjaman_id_peminjam" :options="App\Models\User::getOptions()" />
                    <x-select col="6" name="peminjaman_id_approver" :options="App\Models\User::getOptions()" />

                    <x-input col="6" type="datetime-local" name="peminjaman_tanggal_pinjam" />
                    <x-input col="6" type="datetime-local" name="peminjaman_jatuh_tempo" />
                    <x-input col="6" type="datetime-local" name="peminjaman_tanggal_kembali" />

                    <x-input col="4" type="number" name="peminjaman_grace_jam" />
                    <x-input col="4" type="number" step="0.01" name="peminjaman_denda" />
                    <x-input col="4" name="peminjaman_kondisi_kembali" />

                    <x-input col="6" type="number" name="peminjaman_perpanjang_ke" />

                    <x-textarea col="12" name="peminjaman_tujuan" />
                    <x-textarea col="12" name="peminjaman_catatan" />

                    <x-file
                        name="peminjaman_foto_kembali"
                        label="Foto Kembali"
                        col="12"
                        accept="image/*"
                        capture="environment"
                        :preview="true"
                        :value="$model?->peminjaman_foto_kembali_url"
                        helper="Ambil foto via kamera di HP atau pilih dari galeri" />
                @endif

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @if($isUserMode ?? false)
    <div class="mt-4 flex justify-center">
        <a href="#" class="text-sm text-primary underline hidden" id="btn-daftar-tunggu">Aset sedang dipinjam — Masuk Daftar Tunggu</a>
    </div>
    <script>
        (function () {
            var availMap = {{ Js::from($availabilityMap ?? []) }};
            var sel = document.getElementById('select-peminjaman_id_aset');
            var info = document.getElementById('aset-availability-info');
            var box = document.getElementById('aset-availability-box');
            var dot = document.getElementById('aset-availability-dot');
            var txt = document.getElementById('aset-availability-text');
            var btnTunggu = document.getElementById('btn-daftar-tunggu');
            function apply() {
                var id = sel ? sel.value : '';
                info.classList.toggle('hidden', !id);
                if (!id) return;
                var available = availMap[id] !== false;
                if (available) {
                    box.className = 'flex items-center gap-2 p-3 rounded-xl text-sm bg-emerald-50 border border-emerald-200';
                    dot.className = 'w-2.5 h-2.5 rounded-full shrink-0 bg-emerald-500';
                    txt.className = 'font-medium text-emerald-700';
                    txt.textContent = 'Aset tersedia — bisa dipinjam sekarang';
                    btnTunggu.classList.add('hidden');
                } else {
                    box.className = 'flex items-center gap-2 p-3 rounded-xl text-sm bg-amber-50 border border-amber-200';
                    dot.className = 'w-2.5 h-2.5 rounded-full shrink-0 bg-amber-500';
                    txt.className = 'font-medium text-amber-700';
                    txt.textContent = 'Aset sedang dipinjam — Anda bisa masuk Daftar Tunggu';
                    btnTunggu.classList.remove('hidden');
                    btnTunggu.href = '{{ url('/daftar-tunggu/create') }}?aset=' + id;
                }
            }
            if (sel) { sel.addEventListener('change', apply); apply(); }
        })();
    </script>
    @endif
</x-layouts::app>
