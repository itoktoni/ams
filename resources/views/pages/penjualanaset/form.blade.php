<?php /** @var App\Models\PenjualanAset $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="penjualan_aset_nomor" />
                <x-select col="6" name="penjualan_aset_id_aset" :options="App\Models\Aset::getOptions()" />
                <x-textarea col="12" name="penjualan_aset_alasan" />
                <x-input col="6" type="number" step="0.01" name="penjualan_aset_nilai_buku" />
                <x-input col="6" type="number" step="0.01" name="penjualan_aset_harga_appraisal" />
                <x-input col="6" type="number" step="0.01" name="penjualan_aset_harga_jual" />
                <x-select col="6" name="penjualan_aset_status" :options="App\Enums\Penjualan\StatusPenjualanEnum::getOptions()" />
                <x-input col="6" type="date" name="penjualan_aset_tanggal_request" />
                <x-input col="6" type="date" name="penjualan_aset_tanggal_jual" />
                <x-input col="6" type="date" name="penjualan_aset_tanggal_serah_terima" />
                <x-input col="6" name="penjualan_aset_penerima" />
                <x-select col="6" name="penjualan_aset_kondisi" :options="App\Enums\Aset\KondisiAsetEnum::getOptions()" />
                <x-input col="6" type="number" step="0.01" name="penjualan_aset_gain_loss" />
                <x-textarea col="12" name="penjualan_aset_catatan" />
                <x-file
                    name="penjualan_aset_foto_serah_terima"
                    label="Foto Serah Terima"
                    col="12"
                    accept="image/*"
                    :preview="true"
                    :value="$model?->penjualan_aset_foto_serah_terima_url"
                    helper="Unggah foto dokumen serah terima aset" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    @if(isset($model) && $model->exists)
        @php
            $offers = $penawaran ?? collect();
            $isLelangOpen = in_array($model->penjualan_aset_status, ['ditawarkan','negosiasi','disetujui','terverifikasi','diajukan']);
        @endphp
        <div class="bg-white border border-outline-variant rounded-2xl p-5 sm:p-6 mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h3 class="font-semibold text-on-surface flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-warning/10 flex items-center justify-center"><span class="material-symbols-outlined text-warning text-[18px]">gavel</span></span>
                    Daftar Penawaran Lelang
                    <span class="text-xs font-normal bg-surface-container px-2.5 py-1 rounded-full">{{ $offers->count() }} penawaran</span>
                </h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('lelang.show', ['id' => $model->penjualan_aset_id]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline">
                        <span class="material-symbols-outlined text-[16px]">open_in_new</span> Lihat Halaman Publik
                    </a>
                    <span class="text-xs px-2.5 py-1 rounded-full font-bold {{ $isLelangOpen ? 'bg-success/10 text-success' : 'bg-surface-container text-on-surface-variant' }}">{{ $isLelangOpen ? 'LELANG BUKA' : 'TUTUP' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="bg-surface-container rounded-xl p-3 text-center">
                    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Harga Appraisal</p>
                    <p class="text-sm font-bold mt-1">Rp {{ number_format($model->penjualan_aset_harga_appraisal ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-warning/10 rounded-xl p-3 text-center">
                    <p class="text-[10px] font-bold tracking-widest text-warning uppercase">Bid Tertinggi</p>
                    <p class="text-sm font-bold text-warning mt-1">Rp {{ number_format($highest ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-primary/5 rounded-xl p-3 text-center">
                    <p class="text-[10px] font-bold tracking-widest text-primary uppercase">Min Bid Berikutnya</p>
                    <p class="text-sm font-bold text-primary mt-1">Rp {{ number_format($minBid ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-success/10 rounded-xl p-3 text-center">
                    <p class="text-[10px] font-bold tracking-widest text-success uppercase">Pemenang Sementara</p>
                    <p class="text-xs font-bold mt-1 truncate">{{ $winner->penawaran_penjualan_nama_pembeli ?? '—' }}</p>
                </div>
            </div>

            @if($offers->isEmpty())
                <p class="text-sm text-on-surface-variant text-center py-8 border border-dashed border-outline-variant rounded-xl">Belum ada penawaran. Bagikan link publik lelang untuk mulai menerima bid.</p>
            @else
                <div class="overflow-x-auto -mx-5 sm:mx-0">
                    <table class="w-full text-sm min-w-[640px]">
                        <thead>
                            <tr class="text-left text-xs text-on-surface-variant uppercase border-b border-outline-variant">
                                <th class="pb-3 pl-5 sm:pl-3 pr-3">#</th>
                                <th class="pb-3 pr-3">Pembeli</th>
                                <th class="pb-3 pr-3">Kontak</th>
                                <th class="pb-3 pr-3 text-right">Harga</th>
                                <th class="pb-3 pr-3">Waktu</th>
                                <th class="pb-3 pr-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offers as $idx => $o)
                            <tr class="border-b border-outline-variant/40 {{ $idx===0 ? 'bg-warning/[0.04]' : '' }}">
                                <td class="py-3 pl-5 sm:pl-3 pr-3">
                                    @if($idx===0) <span class="inline-flex items-center gap-1 text-xs font-bold bg-warning text-white px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-[14px]">emoji_events</span> #1</span>
                                    @else <span class="text-xs text-on-surface-variant">#{{ $idx+1 }}</span> @endif
                                </td>
                                <td class="py-3 pr-3 font-medium">
                                    {{ $o->penawaran_penjualan_nama_pembeli }}
                                    @if($o->hasUser) <span class="text-[11px] text-on-surface-variant block">{{ $o->hasUser->email }}</span> @endif
                                </td>
                                <td class="py-3 pr-3 text-on-surface-variant text-xs">{{ $o->penawaran_penjualan_kontak ?? '—' }}</td>
                                <td class="py-3 pr-3 text-right font-bold {{ $idx===0?'text-warning':'' }}">Rp {{ number_format($o->penawaran_penjualan_harga, 0, ',', '.') }}</td>
                                <td class="py-3 pr-3 text-xs text-on-surface-variant">{{ $o->penawaran_penjualan_waktu ? \Carbon\Carbon::parse($o->penawaran_penjualan_waktu)->format('d M Y H:i') : ($o->penawaran_penjualan_tanggal ? \Carbon\Carbon::parse($o->penawaran_penjualan_tanggal)->format('d M Y') : '—') }}</td>
                                <td class="py-3 pr-3"><span class="text-[11px] px-2 py-1 rounded-full font-semibold {{ $idx===0 ? 'bg-success text-white' : 'bg-surface-container text-on-surface-variant' }}">{{ $o->penawaran_penjualan_status ?? 'diajukan' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</x-layouts::app>
