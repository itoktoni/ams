<?php /** @var App\Models\Tiket $tiket */ ?>
<x-layouts::app>
    <x-breadcrumb :items="[['url' => route('tiket.getTable'), 'label' => 'Tiket'], ['url' => '', 'label' => $tiket->tiket_nomor]]" />

    @php
        $isTeknisi = auth()->user()?->role === 'teknisi';
        $isOwner = $isTeknisi && (int)$tiket->tiket_id_teknisi === (int)auth()->id();
        $isPengguna = auth()->user() && in_array(auth()->user()->role, ['pengguna_aset','user'], true);
        $canClaim = $tiket->tiket_status === 'buka' && empty($tiket->tiket_id_teknisi);
        $canAnalisa = $isOwner && in_array($tiket->tiket_status, ['ditugaskan','progres'], true);
        $canSelesai = $isOwner && in_array($tiket->tiket_status, ['progres','menunggu_part'], true);
        $sMap = ['buka'=>'sky','ditugaskan'=>'indigo','progres'=>'blue','menunggu_part'=>'amber','selesai'=>'emerald','terverifikasi'=>'green'];
        $statusLabel = \App\Enums\Tiket\StatusTiketEnum::getDescription($tiket->tiket_status);
    @endphp

    <div class="grid grid-cols-12 gap-4">
        {{-- Header card --}}
        <div class="col-span-12">
            <x-card :label="$tiket->tiket_nomor.' — '.$statusLabel">
                <div class="col-span-12 grid grid-cols-12 gap-3 text-sm">
                    <div class="col-span-12 md:col-span-6">
                        <p class="text-xs text-on-surface-variant">Aset</p>
                        <p class="font-semibold">{{ $tiket->hasAset?->aset_nama }} — {{ $tiket->hasAset?->aset_kode }}</p>
                        <p class="text-xs text-on-surface-variant mt-2">Lokasi</p>
                        <p>{{ $tiket->hasLokasi?->aset_lokasi_nama ?? '-' }}</p>
                        <p class="text-xs text-on-surface-variant mt-2">Pelapor</p>
                        <p>{{ $tiket->hasPelapor?->name ?? '-' }}</p>
                        <p class="text-xs text-on-surface-variant mt-2">Teknisi</p>
                        <p>{{ $tiket->hasTeknisi?->name ?? ($tiket->tiket_id_teknisi ? 'ID '.$tiket->tiket_id_teknisi : 'Belum ditugaskan') }}</p>
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <p class="text-xs text-on-surface-variant">Judul</p>
                        <p class="font-medium">{{ $tiket->tiket_judul }}</p>
                        <p class="text-xs text-on-surface-variant mt-2">Deskripsi</p>
                        <p class="whitespace-pre-wrap">{{ $tiket->tiket_deskripsi ?? '-' }}</p>
                        <div class="grid grid-cols-1 gap-2 mt-3 text-xs">
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-on-surface-variant">event</span><span class="text-on-surface-variant">Waktu pembuatan:</span> <span class="font-medium text-on-surface">{{ $tiket->tiket_tanggal_lapor ? formatDate($tiket->tiket_tanggal_lapor, true) : '-' }}</span></div>
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-on-surface-variant">hand_gesture</span><span class="text-on-surface-variant">Waktu diambil:</span> <span class="font-medium text-on-surface">{{ $tiket->tiket_tanggal_tugas ? formatDate($tiket->tiket_tanggal_tugas, true) : ($tiket->tiket_tanggal_mulai ? formatDate($tiket->tiket_tanggal_mulai, true) : 'Belum diambil') }}</span></div>
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-on-surface-variant">task_alt</span><span class="text-on-surface-variant">Waktu selesai:</span> <span class="font-medium text-on-surface">{{ $tiket->tiket_tanggal_selesai ? formatDate($tiket->tiket_tanggal_selesai, true) : 'Belum selesai' }}</span></div>
                        </div>
                        @if($tiket->hasAset?->aset_qr ?? false)
                            <div class="mt-2 text-xs">QR: {{ $tiket->hasAset->aset_kode_qr }}</div>
                        @endif
                    </div>
                    <div class="col-span-12 flex flex-wrap gap-2 mt-2">
                        @if($canClaim && $isTeknisi)
                            <form method="POST" action="{{ route('tiket.postClaim', $tiket->tiket_id) }}">
                                @csrf
                                <button type="submit" class="h-10 px-5 rounded-xl bg-primary text-on-primary font-semibold">Ambil Tiket Ini</button>
                            </form>
                        @endif
                        <a href="{{ route('tiket.getTable') }}" class="h-10 px-4 rounded-xl bg-white border border-outline-variant inline-flex items-center text-sm">Kembali ke List</a>
                        @if(!$isPengguna)
                        <a href="{{ route('tiket.getReportBulanan', ['teknisi_id' => $tiket->tiket_id_teknisi, 'periode' => $tiket->tiket_tanggal_selesai ? $tiket->tiket_tanggal_selesai->format('Y-m') : now()->format('Y-m')]) }}" class="h-10 px-4 rounded-xl bg-surface-container inline-flex items-center text-sm">Lihat Report Bulanan</a>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Analisa & Kesimpulan — disembunyikan untuk pengguna --}}
        @if(!$isPengguna)
        <div class="col-span-12 md:col-span-6">
            <x-card label="Analisa Teknisi (Bukti Kerja — Tahap Diagnosa)" icon="search">
                @if($tiket->tiket_analisa)
                    <div class="col-span-12 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm whitespace-pre-wrap">{{ $tiket->tiket_analisa }}</div>
                    <div class="col-span-12 text-xs text-on-surface-variant mt-1">Ditulis: {{ $tiket->tiket_tanggal_analisa ? formatDate($tiket->tiket_tanggal_analisa, true) : '-' }} @if($tiket->tiket_foto_sebelum) · <a href="{{ fileUrl($tiket->tiket_foto_sebelum) }}" target="_blank" class="underline">Foto Sebelum</a> @endif</div>
                    @if($tiket->tiket_foto_sebelum)
                        <div class="col-span-12 mt-2">{!! renderImage($tiket->tiket_foto_sebelum, 'Foto Sebelum', 'max-h-48 rounded-xl border') !!}</div>
                    @endif
                @else
                    <div class="col-span-12 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant">Belum ada analisa.</div>
                @endif

                @if($canAnalisa)
                <form method="POST" action="{{ route('tiket.postAnalisa', $tiket->tiket_id) }}" enctype="multipart/form-data" class="col-span-12 mt-3 space-y-3">
                    @csrf
                    <div class="col-span-12">
                        <label class="text-sm font-medium">Tulis / Update Analisa *</label>
                        <textarea name="tiket_analisa" rows="4" required minlength="10" class="w-full mt-1 p-3 border border-outline-variant rounded-xl text-sm" placeholder="Contoh: Kabel power putus di socket, cek multimeter 0V, perlu ganti kabel...">{{ old('tiket_analisa', $tiket->tiket_analisa) }}</textarea>
                        <p class="text-xs text-on-surface-variant mt-1">Min 10 karakter. Ini akan mengubah status menjadi Progres.</p>
                    </div>
                    <div class="col-span-12">
                        <label class="text-sm font-medium">Foto Sebelum (opsional)</label>
                        <input type="file" name="tiket_foto_sebelum" accept="image/*" capture="environment" class="w-full mt-1 text-sm">
                        @if($tiket->tiket_foto_sebelum)
                            <label class="text-xs flex items-center gap-1 mt-1"><input type="checkbox" name="remove_tiket_foto_sebelum" value="1"> Hapus foto lama</label>
                        @endif
                    </div>
                    <button type="submit" class="h-10 px-5 rounded-xl bg-blue-600 text-white font-semibold w-full">Simpan Analisa & Mulai Kerjakan</button>
                </form>
                @endif
            </x-card>
        </div>

        <div class="col-span-12 md:col-span-6">
            <x-card label="Kesimpulan & Tindakan (Bukti Kerja — Tahap Selesai)" icon="task_alt">
                @if($tiket->tiket_kesimpulan)
                    <div class="col-span-12 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm whitespace-pre-wrap">{{ $tiket->tiket_kesimpulan }}</div>
                    <div class="col-span-12 text-xs text-on-surface-variant mt-1">Ditulis: {{ $tiket->tiket_tanggal_kesimpulan ? formatDate($tiket->tiket_tanggal_kesimpulan, true) : '-' }} @if($tiket->tiket_foto_sesudah) · <a href="{{ fileUrl($tiket->tiket_foto_sesudah) }}" target="_blank" class="underline">Foto Sesudah</a> @endif</div>
                    @if($tiket->tiket_foto_sesudah)
                        <div class="col-span-12 mt-2">{!! renderImage($tiket->tiket_foto_sesudah, 'Foto Sesudah', 'max-h-48 rounded-xl border') !!}</div>
                    @endif
                @else
                    <div class="col-span-12 p-3 bg-surface-container-low rounded-xl text-sm text-on-surface-variant">Belum ada kesimpulan.</div>
                @endif

                @if($canSelesai)
                <form method="POST" action="{{ route('tiket.postSelesai', $tiket->tiket_id) }}" enctype="multipart/form-data" class="col-span-12 mt-3 space-y-3">
                    @csrf
                    <div class="col-span-12">
                        <label class="text-sm font-medium">Tulis Kesimpulan *</label>
                        <textarea name="tiket_kesimpulan" rows="4" required minlength="10" class="w-full mt-1 p-3 border border-outline-variant rounded-xl text-sm" placeholder="Contoh: Ganti kabel power, test nyala normal, serah terima ke pelapor...">{{ old('tiket_kesimpulan', $tiket->tiket_kesimpulan) }}</textarea>
                        <p class="text-xs text-on-surface-variant mt-1">Min 10 karakter. Ini akan mengubah status menjadi Selesai.</p>
                    </div>
                    <div class="col-span-12">
                        <label class="text-sm font-medium">Foto Sesudah (bukti)</label>
                        <input type="file" name="tiket_foto_sesudah" accept="image/*" capture="environment" class="w-full mt-1 text-sm">
                        @if($tiket->tiket_foto_sesudah)
                            <label class="text-xs flex items-center gap-1 mt-1"><input type="checkbox" name="remove_tiket_foto_sesudah" value="1"> Hapus foto lama</label>
                        @endif
                    </div>
                    <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white font-semibold w-full">Simpan Kesimpulan & Selesaikan</button>
                </form>
                @elseif($isOwner && $tiket->tiket_status === 'selesai')
                    <div class="col-span-12 mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm">Tiket selesai — bukti kerja sudah tercatat untuk report bulanan.</div>
                @endif
            </x-card>
        </div>
        @else
        {{-- Pengguna: hanya status progress, tanpa detail internal teknisi --}}
        <div class="col-span-12">
            <x-card label="Status Pengerjaan" icon="hourglass_top">
                <div class="col-span-12 p-4 rounded-xl border bg-surface-container-low">
                    @if(in_array($tiket->tiket_status, ['buka'], true))
                        <p class="text-sm font-medium">Menunggu teknisi mengambil tiket</p>
                        <p class="text-xs text-on-surface-variant mt-1">Tiket Anda sudah dilaporkan, teknisi akan segera mengambil.</p>
                    @elseif(in_array($tiket->tiket_status, ['ditugaskan'], true))
                        <p class="text-sm font-medium">Teknisi ditugaskan — segera dianalisa</p>
                        <p class="text-xs text-on-surface-variant mt-1">Teknisi: {{ $tiket->hasTeknisi?->name ?? '-' }}</p>
                    @elseif(in_array($tiket->tiket_status, ['progres','menunggu_part'], true))
                        <p class="text-sm font-medium">Sedang dikerjakan teknisi</p>
                        <p class="text-xs text-on-surface-variant mt-1">Teknisi sedang melakukan perbaikan. Anda akan mendapat notifikasi saat selesai.</p>
                    @elseif(in_array($tiket->tiket_status, ['selesai','terverifikasi'], true))
                        <p class="text-sm font-medium text-emerald-700">Selesai dikerjakan</p>
                        <p class="text-xs text-on-surface-variant mt-1">Selesai pada {{ $tiket->tiket_tanggal_selesai ? formatDate($tiket->tiket_tanggal_selesai, true) : '-' }} @if($tiket->tiket_durasi_menit) · Durasi {{ $tiket->tiket_durasi_menit }} menit @endif</p>
                    @endif
                    <div class="mt-3 inline-flex px-3 py-1 rounded-full text-xs font-semibold
                        @if($tiket->tiket_status==='selesai') bg-emerald-100 text-emerald-700 border border-emerald-200
                        @elseif($tiket->tiket_status==='progres') bg-blue-100 text-blue-700 border border-blue-200
                        @else bg-surface-container border @endif
                    ">{{ $statusLabel }}</div>
                </div>
            </x-card>
        </div>
        @endif

        {{-- Timeline log — untuk pengguna hanya status tanpa catatan internal --}}
        <div class="col-span-12">
            <x-card label="Timeline Pengerjaan (Tiket Log)" icon="history">
                @php $logs = $tiket->hasLogTiket()->orderBy('created_at','asc')->orderBy('tiket_log_id','asc')->get(); @endphp
                @if($logs->isEmpty())
                    <div class="col-span-12 text-sm text-on-surface-variant">Belum ada log.</div>
                @else
                    <div class="col-span-12 space-y-0">
                        @foreach($logs as $log)
                            @if($isPengguna && $log->tiket_log_status_ke === 'progres' && str_contains($log->tiket_log_catatan ?? '', 'Update analisa:'))
                                @continue
                            @endif
                            <div class="flex gap-3 py-3 border-b border-outline-variant/50 last:border-0">
                                <div class="w-2 h-2 rounded-full bg-primary mt-2 shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">
                                        {{ $log->tiket_log_status_dari ? \App\Enums\Tiket\StatusTiketEnum::getDescription($log->tiket_log_status_dari) : '-' }}
                                        →
                                        {{ \App\Enums\Tiket\StatusTiketEnum::getDescription($log->tiket_log_status_ke) }}
                                        <span class="text-xs text-on-surface-variant">· {{ $log->created_at ? formatDate($log->created_at, true) : '-' }}</span>
                                    </p>
                                    @if(!$isPengguna && $log->tiket_log_catatan)
                                        <p class="text-sm text-on-surface-variant whitespace-pre-wrap mt-1">{{ Str::limit($log->tiket_log_catatan, 300) }}</p>
                                    @endif
                                    @if(!$isPengguna && $log->tiket_log_actor)
                                        <p class="text-xs text-on-surface-variant mt-1">Actor: {{ \App\Models\User::find($log->tiket_log_actor)?->name ?? 'ID '.$log->tiket_log_actor }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
