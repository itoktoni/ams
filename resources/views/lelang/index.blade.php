@extends('cms::frontend.layouts.public')
@section('title', 'Lelang Aset — AMS')
@section('content')
<section class="bg-surface-container-low pt-20 sm:pt-24 pb-12 sm:pb-16 mt-2">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
      <div>
        <p class="text-xs font-bold tracking-[0.14em] text-primary uppercase">Lelang Publik</p>
        <h1 class="text-2xl sm:text-3xl font-bold mt-1">Barang Lelang Perusahaan</h1>
        <p class="text-sm text-on-surface-variant mt-2 max-w-2xl">Aset yang masa pakainya habis atau akan dilelang. Daftar → login → tawar dengan harga minimum. Pemenang adalah penawar tertinggi.</p>
      </div>
      <form method="GET" class="flex gap-2 w-full sm:w-auto">
        <input name="q" value="{{ request('q') }}" placeholder="Cari aset / nomor..." class="flex-1 sm:w-64 h-11 px-4 bg-white border border-outline-variant rounded-xl outline-none focus:border-primary text-sm">
        <button class="h-11 px-5 bg-primary text-white rounded-xl text-sm font-semibold">Cari</button>
      </form>
    </div>

    @if($items->isEmpty())
      <div class="bg-white border border-outline-variant rounded-2xl p-10 text-center">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30">gavel</span>
        <p class="text-sm text-on-surface-variant mt-3">Belum ada barang lelang saat ini.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        @foreach($items as $row)
          @php
            $aset = $row->hasAset; $kat = $aset?->hasKategori;
            $lelangImg = $row->penjualan_aset_foto_serah_terima ?: $aset?->aset_foto;
            $lelangImgUrl = $lelangImg ? fileUrl($lelangImg) : '';
          @endphp
          <a href="{{ route('lelang.show', ['id' => $row->penjualan_aset_id]) }}" class="bg-white border border-outline-variant rounded-2xl overflow-hidden hover:shadow-lg transition group flex flex-col">
            <div class="aspect-square bg-surface-container relative overflow-hidden">
              @if($lelangImgUrl)
                <img src="{{ $lelangImgUrl }}" class="w-full h-full object-cover object-center group-hover:scale-[1.02] transition duration-500" alt="{{ $aset?->aset_nama ?? $row->penjualan_aset_nomor }}">
                @if(!$aset?->aset_foto && $row->penjualan_aset_foto_serah_terima)
                  <span class="absolute bottom-2 right-2 text-[10px] bg-black/60 text-white px-2 py-0.5 rounded-full">Foto lelang</span>
                @endif
              @else
                <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20"><span class="material-symbols-outlined text-5xl">inventory_2</span></div>
              @endif
              <span class="absolute top-3 left-3 text-[11px] font-bold tracking-widest px-2.5 py-1 rounded-full bg-warning text-white">{{ strtoupper($row->penjualan_aset_status) }}</span>
              <span class="absolute top-3 right-3 text-xs bg-white/90 backdrop-blur px-2.5 py-1 rounded-full font-semibold">{{ $kat?->aset_kategori_nama ?? '-' }}</span>
            </div>
            <div class="p-4 flex-1 flex flex-col">
              <p class="text-[11px] font-bold tracking-widest text-primary uppercase">{{ $row->penjualan_aset_nomor }}</p>
              <h3 class="font-bold text-on-surface leading-tight mt-1 line-clamp-2">{{ $aset?->aset_nama ?? 'Aset #' . $row->penjualan_aset_id_aset }}</h3>
              <p class="text-xs text-on-surface-variant mt-1 line-clamp-2">{{ $row->penjualan_aset_alasan ?? $aset?->aset_kondisi ?? '' }}</p>
              <div class="grid grid-cols-2 gap-2 mt-4">
                <div class="bg-surface-container rounded-xl p-2.5 text-center">
                  <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Appraisal</p>
                  <p class="text-sm font-bold mt-0.5">Rp {{ number_format($row->penjualan_aset_harga_appraisal ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-warning/10 rounded-xl p-2.5 text-center">
                  <p class="text-[10px] font-bold tracking-widest text-warning uppercase">Tertinggi</p>
                  <p class="text-sm font-bold text-warning mt-0.5">Rp {{ number_format($row->highest_bid ?? 0, 0, ',', '.') }}</p>
                </div>
              </div>
              <div class="flex items-center justify-between mt-3 text-xs text-on-surface-variant">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">group</span> {{ $row->bid_count }} bid</span>
                <span>{{ $row->penjualan_aset_tanggal_request ? \Carbon\Carbon::parse($row->penjualan_aset_tanggal_request)->format('d M Y') : '' }}</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
      <div class="mt-10 flex justify-center">{{ $items->links() }}</div>
    @endif
  </div>
</section>
@endsection
