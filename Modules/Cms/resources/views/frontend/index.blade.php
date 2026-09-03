@extends('cms::frontend.layouts.public')

@section('title', $homeEntry?->title ?? config('app.name', 'LARAVEL'))

@section('content')

@if ($homeHtml)
    {!! $homeHtml !!}
@else
    <section class="py-20 bg-surface-container-low">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="material-symbols-outlined text-6xl text-primary/40 mb-4 block">home</span>
            <h1 class="font-headline-xl text-headline-xl text-on-surface mb-4">{{ config('app.name', 'LARAVEL') }}</h1>
            <p class="text-on-surface-variant text-body-lg mb-8">Selamat datang di situs resmi. Belum ada konten homepage yang dipublikasikan — buat konten dengan tipe <strong>Homepage</strong> lalu set status <strong>Published</strong>.</p>
            <a href="{{ route('login') }}" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-label-md hover:opacity-90 transition-all">Login Admin</a>
        </div>
    </section>
@endif

{{-- Lelang Publik Highlight --}}
<section class="py-10 bg-white border-t border-outline-variant/40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
      <div>
        <p class="text-xs font-bold tracking-[0.14em] text-primary uppercase">Lelang Aset</p>
        <h2 class="text-2xl font-bold mt-1">Barang Lelang Tersedia</h2>
        <p class="text-sm text-on-surface-variant mt-1">Aset habis masa pakai yang dilelang — lihat detail, daftar & tawar langsung.</p>
      </div>
      <a href="{{ route('lelang.index') }}" class="inline-flex items-center gap-1.5 h-11 px-5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-[#003d2a] transition">
        Lihat Semua Lelang <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
      </a>
    </div>

    @if(($lelangItems ?? collect())->isEmpty())
      <div class="bg-surface-container-low border border-dashed border-outline-variant rounded-2xl p-8 text-center">
        <span class="material-symbols-outlined text-4xl text-on-surface-variant/30">gavel</span>
        <p class="text-sm text-on-surface-variant mt-2">Belum ada lelang aktif saat ini.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @foreach($lelangItems as $row)
          @php $aset = $row->hasAset; $homeImg = $row->penjualan_aset_foto_serah_terima ?: $aset?->aset_foto; $homeImgUrl = $homeImg ? fileUrl($homeImg) : ''; @endphp
          <a href="{{ route('lelang.show', ['id' => $row->penjualan_aset_id]) }}" class="bg-white border border-outline-variant rounded-2xl overflow-hidden hover:shadow-lg transition group flex flex-col">
            <div class="aspect-square bg-surface-container relative overflow-hidden">
              @if($homeImgUrl)
                <img src="{{ $homeImgUrl }}" class="w-full h-full object-cover object-center group-hover:scale-[1.02] transition" alt="{{ $aset?->aset_nama ?? $row->penjualan_aset_nomor }}">
              @else
                <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20"><span class="material-symbols-outlined text-5xl">inventory_2</span></div>
              @endif
              <span class="absolute top-3 left-3 text-[11px] font-bold tracking-widest px-2.5 py-1 rounded-full bg-warning text-white">{{ strtoupper($row->penjualan_aset_status) }}</span>
            </div>
            <div class="p-4 flex-1 flex flex-col">
              <p class="text-[11px] font-bold tracking-widest text-primary uppercase">{{ $row->penjualan_aset_nomor }}</p>
              <h3 class="font-bold leading-tight mt-1 line-clamp-2">{{ $aset?->aset_nama ?? 'Aset' }}</h3>
              <div class="grid grid-cols-2 gap-2 mt-4">
                <div class="bg-surface-container rounded-xl p-2.5 text-center">
                  <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Appraisal</p>
                  <p class="text-sm font-bold mt-0.5">Rp {{ number_format($row->penjualan_aset_harga_appraisal ?? 0,0,',','.') }}</p>
                </div>
                <div class="bg-warning/10 rounded-xl p-2.5 text-center">
                  <p class="text-[10px] font-bold tracking-widest text-warning uppercase">Tertinggi</p>
                  <p class="text-sm font-bold text-warning mt-0.5">Rp {{ number_format($row->highest_bid ?? 0,0,',','.') }}</p>
                </div>
              </div>
              <p class="text-xs text-on-surface-variant mt-3 inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">group</span> {{ $row->bid_count }} penawaran</p>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</section>

@endsection