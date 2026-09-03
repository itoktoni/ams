<!DOCTYPE html>
<html lang="id">
@include('layouts.head')
<body class="bg-surface text-on-surface antialiased">
    <div class="max-w-3xl mx-auto px-4 py-6">
        <div class="text-center mb-6">
            <p class="text-[11px] font-bold tracking-[0.14em] text-primary uppercase">{{ config('website.name', 'KIRO AMS') }} • Scan Aset</p>
            @if($aset->aset_foto)
                <img src="{{ fileUrl($aset->aset_foto) }}" alt="{{ $aset->aset_nama }}" onclick="document.getElementById('img-modal').classList.remove('hidden')" class="w-40 h-40 object-cover rounded-2xl mx-auto mt-3 border border-outline-variant shadow-sm cursor-zoom-in hover:opacity-90 transition">
                <div id="img-modal" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
                    <img src="{{ fileUrl($aset->aset_foto) }}" alt="{{ $aset->aset_nama }}" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl" onclick="event.stopPropagation()">
                    <button onclick="document.getElementById('img-modal').classList.add('hidden')" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white hover:bg-white/30">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            @else
                <div class="w-40 h-40 rounded-2xl mx-auto mt-3 bg-surface-container border border-outline-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/40">inventory_2</span>
                </div>
            @endif
            <h1 class="text-xl font-bold mt-3">{{ $aset->aset_nama }}</h1>
            <p class="text-xs font-mono text-on-surface-variant">{{ $aset->aset_kode }} • {{ $aset->aset_kode_qr }}</p>
            <p class="text-xs text-on-surface-variant mt-1">{{ $aset->hasKategori?->aset_kategori_nama ?? '-' }} • {{ $aset->hasLokasi?->aset_lokasi_nama ?? '-' }}</p>
        </div>

        <div class="bg-white border border-outline-variant rounded-2xl p-4 mb-4">
            <h3 class="font-semibold text-sm flex items-center gap-2 mb-3"><span class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-lg">precision_manufacturing</span></span> Detail Aset</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-on-surface-variant text-xs">Merek / Model / SN</span><p class="font-medium">{{ $aset->aset_merek ?? '-' }} / {{ $aset->aset_model ?? '-' }} / {{ $aset->aset_nomor_seri ?? '-' }}</p></div>
                <div><span class="text-on-surface-variant text-xs">Status / Kondisi</span><p><span class="inline-flex px-2 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold">{{ $aset->aset_status ?? '-' }} / {{ $aset->aset_kondisi ?? '-' }}</span></p></div>
                <div><span class="text-on-surface-variant text-xs">Masa Manfaat</span><p>{{ $aset->aset_masa_manfaat }} bln • {{ $aset->aset_metode_penyusutan ?? '-' }}</p></div>
                <div class="col-span-2"><span class="text-on-surface-variant text-xs">Penanggung Jawab</span><p class="font-medium">{{ $aset->hasPenanggungJawab?->name ?? '-' }} {{ $aset->hasPenanggungJawab?->email ? '• '.$aset->hasPenanggungJawab->email : '' }}</p></div>
            </div>
            @auth
            <div class="flex gap-2 mt-4">
                <a href="{{ $aset->tiket_qr_url }}" class="inline-flex items-center gap-2 h-10 px-4 bg-primary text-on-primary rounded-xl text-sm font-semibold"><span class="material-symbols-outlined">confirmation_number</span> Buat Tiket</a>
                <a href="{{ route('aset.getUpdate', ['id' => $aset->aset_id]) }}" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-outline-variant rounded-xl text-sm font-semibold">Detail Lengkap</a>
            </div>
            @else
            <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-sm">
                <a href="{{ route('login') }}" class="text-primary underline font-semibold">Login</a> untuk buat tiket / lihat detail lengkap.
            </div>
            @endauth
        </div>

        <div class="bg-white border border-outline-variant rounded-2xl p-4 mb-4">
            <h3 class="font-semibold text-sm flex items-center gap-2 mb-3"><span class="w-7 h-7 rounded-lg bg-success/10 flex items-center justify-center"><span class="material-symbols-outlined text-success text-lg">history</span></span> Riwayat Service</h3>
            <div class="space-y-2">
                @forelse($riwayat as $r)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container text-sm">
                        <span class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary">build</span></span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">{{ $r->riwayat_service_jenis }} — {{ formatDate($r->riwayat_service_tanggal) }}</p>
                            <p class="text-xs text-on-surface-variant truncate">{{ $r->hasTeknisi?->teknisi_nama ?? '-' }} • {{ $r->riwayat_service_catatan ?? '' }}</p>
                        </div>
                        <span class="text-xs font-bold shrink-0">{{ $r->riwayat_service_biaya ? formatRupiah($r->riwayat_service_biaya) : '' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-on-surface-variant text-center py-4">Belum ada riwayat service</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white border border-outline-variant rounded-2xl p-4">
                <h3 class="font-semibold text-sm flex items-center gap-2 mb-3"><span class="w-7 h-7 rounded-lg bg-warning/10 flex items-center justify-center"><span class="material-symbols-outlined text-warning text-lg">event_repeat</span></span> Jadwal Service</h3>
                <div class="space-y-2">
                    @forelse($jadwal as $j)
                        <div class="p-2 rounded-lg bg-surface-container text-xs">
                            <p class="font-semibold">{{ $j->hasTemplate?->template_service_nama ?? '-' }} — {{ formatDate($j->jadwal_service_tanggal_jatuh_tempo) }}</p>
                            <p class="text-on-surface-variant">{{ $j->jadwal_service_status }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant text-center py-4">Tidak ada jadwal</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-white border border-outline-variant rounded-2xl p-4">
                <h3 class="font-semibold text-sm flex items-center gap-2 mb-3"><span class="w-7 h-7 rounded-lg bg-info/10 flex items-center justify-center"><span class="material-symbols-outlined text-info text-lg">confirmation_number</span></span> Tiket Terbaru</h3>
                <div class="space-y-2">
                    @forelse($tiket as $t)
                        <div class="p-2 rounded-lg bg-surface-container text-xs">
                            <p class="font-semibold truncate">{{ $t->tiket_judul ?? $t->tiket_nomor }}</p>
                            <p class="text-on-surface-variant">{{ $t->tiket_status }} • {{ $t->tiket_tingkat_urgensi ?? '-' }} • {{ $t->tiket_tanggal_lapor ? formatDate($t->tiket_tanggal_lapor) : '' }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant text-center py-4">Belum ada tiket</p>
                    @endforelse
                </div>
            </div>
        </div>

        <p class="text-center text-[11px] text-on-surface-variant mt-6">{{ config('website.name', 'KIRO AMS') }} — Scan {{ $aset->aset_kode_qr }} • {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    @livewireScripts
</body>
</html>
