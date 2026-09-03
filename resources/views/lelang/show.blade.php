@extends('cms::frontend.layouts.public')
@section('title', ($item->hasAset->aset_nama ?? 'Lelang') . ' — AMS')
@section('content')
<section class="bg-surface-container-low py-6 sm:py-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <a href="{{ route('lelang.index') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary mb-4">
      <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar lelang
    </a>

    @if(session('success'))
      <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="bg-error/10 border border-error/20 text-error px-4 py-3 rounded-xl mb-4">
        @foreach($errors->all() as $e)<p class="text-sm">{{ $e }}</p>@endforeach
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Left --}}
      <div class="lg:col-span-2">
        @php $detailImg = $item->penjualan_aset_foto_serah_terima ?: $item->hasAset?->aset_foto; $detailImgUrl = $detailImg ? fileUrl($detailImg) : ''; @endphp
        <div class="bg-white border border-outline-variant rounded-2xl overflow-hidden">
          <div class="aspect-[4/5] sm:aspect-square bg-surface-container relative overflow-hidden">
            @if($detailImgUrl)
              <img src="{{ $detailImgUrl }}" class="w-full h-full object-cover object-center" alt="{{ $item->hasAset->aset_nama ?? $item->penjualan_aset_nomor }}">
            @else
              <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20"><span class="material-symbols-outlined text-6xl">inventory_2</span></div>
            @endif
            <span class="absolute top-4 left-4 text-xs font-bold tracking-widest px-3 py-1.5 rounded-full bg-warning text-white">{{ strtoupper($item->penjualan_aset_status) }}</span>
          </div>
          <div class="p-5 sm:p-6">
            <p class="text-xs font-bold tracking-[0.14em] text-primary uppercase">{{ $item->penjualan_aset_nomor }} • {{ $item->hasAset->hasKategori->aset_kategori_nama ?? '-' }}</p>
            <h1 class="text-2xl sm:text-3xl font-bold mt-2 leading-tight">{{ $item->hasAset->aset_nama ?? 'Aset' }}</h1>
            <p class="text-sm text-on-surface-variant mt-2">{{ $item->penjualan_aset_alasan }}</p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
              <div class="bg-surface-container rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Nilai Buku</p>
                <p class="text-sm font-bold mt-1">Rp {{ number_format($item->penjualan_aset_nilai_buku ?? 0,0,',','.') }}</p>
              </div>
              <div class="bg-surface-container rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Appraisal (Min)</p>
                <p class="text-sm font-bold mt-1">Rp {{ number_format($item->penjualan_aset_harga_appraisal ?? 0,0,',','.') }}</p>
              </div>
              <div class="bg-warning/10 rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold tracking-widest text-warning uppercase">Tertinggi</p>
                <p class="text-sm font-bold text-warning mt-1">Rp {{ number_format($highest ?? 0,0,',','.') }}</p>
              </div>
              <div class="bg-success/10 rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold tracking-widest text-success uppercase">Total Bid</p>
                <p class="text-sm font-bold text-success mt-1">{{ $offers->count() }}</p>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div><p class="text-xs text-on-surface-variant uppercase tracking-wide">Kode Aset</p><p class="font-medium">{{ $item->hasAset->aset_kode ?? '-' }}</p></div>
              <div><p class="text-xs text-on-surface-variant uppercase tracking-wide">Kondisi</p><p class="font-medium">{{ $item->penjualan_aset_kondisi ?? $item->hasAset->aset_kondisi ?? '-' }}</p></div>
              <div><p class="text-xs text-on-surface-variant uppercase tracking-wide">Lokasi</p><p class="font-medium">{{ $item->hasAset->hasLokasi->aset_lokasi_nama ?? '-' }}</p></div>
              <div><p class="text-xs text-on-surface-variant uppercase tracking-wide">Tgl Request</p><p class="font-medium">{{ $item->penjualan_aset_tanggal_request ? \Carbon\Carbon::parse($item->penjualan_aset_tanggal_request)->format('d M Y H:i') : '-' }}</p></div>
            </div>

            @if($item->penjualan_aset_catatan)
              <div class="mt-6 bg-surface-container rounded-xl p-4">
                <p class="text-xs font-bold tracking-widest uppercase text-on-surface-variant mb-1">Catatan</p>
                <p class="text-sm leading-relaxed">{{ $item->penjualan_aset_catatan }}</p>
              </div>
            @endif
          </div>
        </div>

        {{-- Offers table --}}
        <div class="bg-white border border-outline-variant rounded-2xl p-5 sm:p-6 mt-6">
          <h3 class="font-bold flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-warning/10 flex items-center justify-center"><span class="material-symbols-outlined text-warning text-[18px]">leaderboard</span></span>
            Daftar Penawaran <span class="text-xs font-normal bg-surface-container px-2 py-1 rounded-full">{{ $offers->count() }}</span>
          </h3>
          <p class="text-xs text-on-surface-variant mt-1">Diurutkan tertinggi → terendah. Pemenang adalah harga paling besar.</p>

          @if($offers->isEmpty())
            <p class="text-sm text-on-surface-variant text-center py-8 border border-dashed rounded-xl mt-4">Belum ada penawaran — jadilah penawar pertama!</p>
          @else
            <div class="mt-4 overflow-x-auto -mx-5 sm:mx-0">
              <table class="w-full text-sm min-w-[560px]">
                <thead><tr class="text-xs text-on-surface-variant uppercase border-b">
                  <th class="text-left pb-2 pl-4">Rank</th><th class="text-left pb-2">Pembeli</th><th class="text-right pb-2">Harga</th><th class="text-left pb-2 pl-4">Waktu</th>
                </tr></thead>
                <tbody>
                @foreach($offers as $i => $o)
                  <tr class="border-b border-outline-variant/30 {{ $i===0?'bg-warning/[0.06]':'' }}">
                    <td class="py-3 pl-4">@if($i===0)<span class="bg-warning text-white text-xs font-bold px-2 py-1 rounded-full inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">emoji_events</span> #1</span>@else <span class="text-xs text-on-surface-variant">#{{ $i+1 }}</span>@endif</td>
                    <td class="py-3">
                      <p class="font-medium">{{ $o->penawaran_penjualan_nama_pembeli }}</p>
                      @if($o->hasUser)<p class="text-xs text-on-surface-variant">{{ $o->hasUser->email }}</p>@endif
                      <p class="text-xs text-on-surface-variant">{{ $o->penawaran_penjualan_kontak }}</p>
                    </td>
                    <td class="py-3 text-right font-bold {{ $i===0?'text-warning':'' }}">Rp {{ number_format($o->penawaran_penjualan_harga,0,',','.') }}</td>
                    <td class="py-3 pl-4 text-xs text-on-surface-variant">{{ $o->penawaran_penjualan_waktu ? \Carbon\Carbon::parse($o->penawaran_penjualan_waktu)->format('d M Y H:i') : $o->penawaran_penjualan_tanggal }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: bid card --}}
      <div class="lg:col-span-1">
        <div class="bg-white border border-outline-variant rounded-2xl p-5 sm:p-6 sticky top-6">
          <h3 class="font-bold flex items-center gap-2"><span class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-[18px]">gavel</span></span> Ajukan Penawaran</h3>
          <p class="text-xs text-on-surface-variant mt-1">Harga minimum <b>Rp {{ number_format($minBid,0,',','.') }}</b>. Lebih tinggi dari bid tertinggi menang.</p>

          @guest
            <div class="mt-4 bg-warning/10 border border-warning/20 rounded-xl p-4 text-center">
              <p class="text-sm font-medium">Harap login untuk menawar</p>
              <div class="flex gap-2 mt-3 justify-center">
                <a href="{{ route('login') }}" class="px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold">Login</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white border border-outline-variant rounded-xl text-sm font-semibold">Register</a>
              </div>
            </div>
          @else
            <form method="POST" action="{{ route('lelang.bid', ['id' => $item->penjualan_aset_id]) }}" class="mt-4 space-y-3">
              @csrf
              <div>
                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Harga Penawaran (Rp)</label>
                <input type="number" name="harga" min="{{ $minBid }}" step="1000" value="{{ old('harga', $minBid) }}" class="w-full h-11 px-4 bg-white border border-outline-variant rounded-xl focus:border-primary outline-none text-sm font-medium" required>
                <p class="text-[11px] text-on-surface-variant mt-1">Minimum Rp {{ number_format($minBid,0,',','.') }}</p>
              </div>
              <div>
                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Kontak (opsional)</label>
                <input type="text" name="kontak" value="{{ old('kontak', auth()->user()->email) }}" placeholder="No HP / email" class="w-full h-11 px-4 bg-white border border-outline-variant rounded-xl focus:border-primary outline-none text-sm">
              </div>
              @php $captchaKey = 'lelang_'.uniqid(); @endphp
              <div>
                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Captcha <span class="text-error">*</span></label>
                <div class="flex gap-2">
                  <img src="{{ route('captcha.contact') }}?key={{ $captchaKey }}" alt="captcha" class="h-11 rounded-xl border border-outline-variant bg-white cursor-pointer" onclick="this.src='{{ route('captcha.contact') }}?key={{ $captchaKey }}&t='+Date.now()" title="Klik untuk refresh">
                  <input type="hidden" name="captcha_key" value="{{ $captchaKey }}">
                  <input type="number" name="captcha" placeholder="Jawab ?" class="flex-1 h-11 px-4 bg-white border border-outline-variant rounded-xl focus:border-primary outline-none text-sm" required>
                </div>
                <p class="text-[11px] text-on-surface-variant mt-1">Isi hasil penjumlahan / perkalian di gambar (anti-bot).</p>
              </div>
              <button type="submit" class="w-full h-11 bg-primary text-white rounded-xl font-semibold hover:bg-[#001d6b] transition flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">send</span> Kirim Penawaran
              </button>
              <p class="text-[11px] text-on-surface-variant text-center">Dengan menawar Anda menyetujui syarat lelang.</p>
            </form>
          @endguest

          @if($winner)
            <div class="mt-6 bg-success/10 border border-success/20 rounded-xl p-4">
              <p class="text-xs font-bold tracking-widest text-success uppercase">Pemenang Sementara</p>
              <p class="font-bold mt-1">{{ $winner->penawaran_penjualan_nama_pembeli }}</p>
              <p class="text-sm font-bold text-success">Rp {{ number_format($winner->penawaran_penjualan_harga,0,',','.') }}</p>
              <p class="text-xs text-on-surface-variant">{{ $winner->penawaran_penjualan_waktu ? \Carbon\Carbon::parse($winner->penawaran_penjualan_waktu)->format('d M Y H:i') : '' }}</p>
            </div>
          @endif

          <a href="{{ route('lelang.index') }}" class="block text-center text-sm text-primary font-semibold mt-4 hover:underline">Lihat semua lelang →</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
