<?php /** @var App\Models\Penghapusan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                @php $isCreate = !isset($model) || !$model->exists; @endphp
                @if($isCreate)
                    <input type="hidden" name="penghapusan_nomor" value="">
                @else
                    <x-input col="6" name="penghapusan_nomor" helper="Otomatis jika kosong" />
                @endif
                <x-select col="12" name="penghapusan_id_aset" :options="App\Models\Aset::getOptions()" label="Aset" helper="Pilih aset yang akan dihapus" class="search" />
                <x-textarea col="12" name="penghapusan_alasan" label="Alasan Penghapusan" placeholder="Mis. Rusak berat tidak layak pakai, hilang, afkir..." />

                {{-- disembunyikan: tanggal/status/triase/nilai default --}}
                <input type="hidden" name="penghapusan_tanggal_request" value="{{ old('penghapusan_tanggal_request', $model->penghapusan_tanggal_request ? \Carbon\Carbon::parse($model->penghapusan_tanggal_request)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                <input type="hidden" name="penghapusan_status" value="{{ old('penghapusan_status', $model->penghapusan_status ?? 'diajukan') }}">
                <input type="hidden" name="penghapusan_triase" value="{{ old('penghapusan_triase', $model->penghapusan_triase ?? '') }}">
                <input type="hidden" name="penghapusan_tanggal_akhir_karantina" value="{{ old('penghapusan_tanggal_akhir_karantina', $model->penghapusan_tanggal_akhir_karantina ? \Carbon\Carbon::parse($model->penghapusan_tanggal_akhir_karantina)->format('Y-m-d') : '') }}">
                <input type="hidden" name="penghapusan_nilai_buku" value="{{ old('penghapusan_nilai_buku', $model->penghapusan_nilai_buku ?? 0) }}">
                <input type="hidden" name="penghapusan_nilai_sisa" value="{{ old('penghapusan_nilai_sisa', $model->penghapusan_nilai_sisa ?? 0) }}">
                <input type="hidden" name="penghapusan_gain_loss" value="{{ old('penghapusan_gain_loss', $model->penghapusan_gain_loss ?? 0) }}">
                <input type="hidden" name="penghapusan_catatan" value="{{ old('penghapusan_catatan', $model->penghapusan_catatan ?? '') }}">

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']">
            @if(isset($model) && $model->exists && ($model->penghapusan_status ?? '') === 'disetujui')
                <a href="{{ route('penghapusan.getBeritaAcara', ['id' => $model->penghapusan_id]) }}" class="inline-flex items-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-white border border-outline-variant text-on-surface hover:bg-surface-container shadow-sm transition-all shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">description</span>
                    <span class="hidden sm:inline">Berita Acara</span>
                </a>
            @endif
            @if(isset($model) && $model->exists && ($model->penghapusan_status ?? '') === 'diajukan' && in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true))
                <a href="{{ route('penghapusan.getApprove', ['id' => $model->penghapusan_id]) }}" onclick="return confirm('Setujui penghapusan ini?')" class="inline-flex items-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-success text-white hover:bg-success/90 shadow-sm transition-all shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">check_circle</span>
                    <span class="hidden sm:inline">Approve</span>
                </a>
                <button type="button" onclick="document.getElementById('reject-box-penghapusan').classList.toggle('hidden')" class="inline-flex items-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-error text-white hover:bg-error/90 shadow-sm transition-all shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">cancel</span>
                    <span class="hidden sm:inline">Tolak</span>
                </button>
            @endif
        </x-action>
    </x-form>
    @if(isset($model) && $model->exists && ($model->penghapusan_status ?? '') === 'diajukan' && in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true))
    <div id="reject-box-penghapusan" class="hidden mt-3 bg-white border border-outline-variant rounded-2xl p-4 max-w-2xl mx-auto">
        <p class="text-sm font-semibold mb-2">Alasan penolakan <span class="text-error">*</span></p>
        <form method="GET" action="{{ route('penghapusan.getReject', ['id' => $model->penghapusan_id]) }}" onsubmit="if(!this.catatan.value.trim()){alert('Catatan wajib diisi');return false;} return confirm('Tolak penghapusan ini?')">
            <textarea name="catatan" rows="3" placeholder="Tulis alasan penolakan..." class="w-full px-3 py-2 border border-outline-variant rounded-xl text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none" required></textarea>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="h-9 px-4 bg-error text-white rounded-xl text-sm font-semibold">Kirim Penolakan</button>
                <button type="button" onclick="document.getElementById('reject-box-penghapusan').classList.add('hidden')" class="h-9 px-4 bg-white border border-outline-variant rounded-xl text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
    @endif
    @if(isset($model) && $model->exists && ($model->penghapusan_status ?? '') === 'diajukan')
    <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-sm max-w-2xl mx-auto">Status: <b>diajukan</b> — menunggu approval di <a href="{{ route('persetujuan.getTable') }}" class="text-primary underline">Persetujuan</a>.</div>
    @endif
</x-layouts::app>
