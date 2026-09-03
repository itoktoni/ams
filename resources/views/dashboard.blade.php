<x-layouts::app title="Dashboard">
@php
  $user = auth()->user();
  $greet = now()->hour < 12 ? 'Selamat pagi' : (now()->hour < 15 ? 'Selamat siang' : (now()->hour < 18 ? 'Selamat sore' : 'Selamat malam'));
  $pctAktif = $amsStats['total_aset'] ? round($amsStats['aset_aktif']/$amsStats['total_aset']*100) : 0;
  $totalNilaiFmt = number_format($amsStats['total_nilai'] ?? 0, 0, ',', '.');
@endphp

<style>
  .kpi-card{transition:transform .15s, box-shadow .15s}
  @media(min-width:768px){.kpi-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,40,142,.08)}}
  /* ApexCharts responsive fix — prevents 600px fixed width from breaking mobile */
  .chart-wrap{overflow:hidden; min-width:0; width:100%}
  .chart-wrap > div{width:100% !important; min-width:0 !important; max-width:100% !important}
  .chart-wrap .apexcharts-canvas{width:100% !important; max-width:100% !important}
  .chart-wrap .apexcharts-canvas svg{width:100% !important}
</style>

<div class="w-full min-w-0 overflow-hidden">

 {{-- HEADER --}}
@php $roleLabel = ['developer'=>'Developer','admin'=>'Admin','supervisor'=>'Supervisor','teknisi'=>'Teknisi','pengguna_aset'=>'Pengguna Aset','user'=>'User','customer'=>'Customer'][$role] ?? ucfirst($role); @endphp
<div class="flex flex-col gap-3 sm:gap-4 mb-4 sm:mb-6 min-w-0">
  <div class="min-w-0">
    <p class="text-[10px] sm:text-xs font-bold tracking-[0.14em] text-primary uppercase mb-1 truncate">AMS • Asset Management System • {{ $roleLabel }}</p>
    <h1 class="text-xl sm:text-2xl lg:text-[28px] font-bold tracking-tight text-on-surface leading-tight break-words">{{$greet}}, {{ strtok($user->name ?? 'Admin', ' ') }} 👋</h1>
    <p class="text-xs sm:text-sm text-on-surface-variant mt-1.5 leading-relaxed break-words">{{ now()->translatedFormat('l, d F Y') }}
      @if($role==='customer') • Lelang publik
      @elseif(in_array($role,['pengguna_aset','user'])) • {{ $amsStats['total_aset'] }} aset milik Anda • {{ $amsStats['tiket_buka'] }} tiket Anda
      @else • {{ $amsStats['total_aset'] }} aset • {{ $amsStats['tiket_buka'] }} tiket perlu tindakan
      @endif
    </p>
    @if(!empty($budgetInfo))
    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $budgetInfo['sisa']<0?'bg-error/10 border-error text-error':'bg-primary/5 border-primary/20 text-primary' }}">
        <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span>
        Budget {{ $budgetInfo['department']->department_nama }}: {{ formatRupiah($budgetInfo['sisa']) }} sisa / {{ formatRupiah($budgetInfo['department']->department_budget) }}
      </span>
      <a href="{{ route('permintaan-suku-cadang.getTable') }}" class="text-primary underline">Permintaan ({{ $amsStats['permintaan_menunggu'] }} menunggu)</a>
    </div>
    @endif
  </div>
  <div class="flex flex-col sm:flex-row gap-2 sm:items-center min-w-0">
    @if($role==='customer')
      <a href="{{ route('lelang.index') }}" class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#001d6b] transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">gavel</span> Lihat Lelang
      </a>
    @elseif(in_array($role,['pengguna_aset','user']))
      <a href="{{ route('tiket.getCreate') }}" class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#001d6b] transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">confirmation_number</span> Buat Tiket
      </a>
      <a href="{{ route('permintaan-suku-cadang.getCreate') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-outline-variant text-on-surface px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-surface-container transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">inventory_2</span> Minta Suku Cadang
      </a>
    @elseif($role==='teknisi')
      <a href="{{ route('tiket.getTable') }}" class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#001d6b] transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">engineering</span> Tiket Saya
      </a>
      <a href="{{ route('batch-tiket.getTable') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-outline-variant text-on-surface px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-surface-container transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">view_agenda</span> Batch
      </a>
    @else
      <a href="{{ route('aset.getCreate') }}" class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#001d6b] transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span> Tambah Aset
      </a>
      <a href="{{ route('tiket.getCreate') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-outline-variant text-on-surface px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-surface-container transition w-full sm:w-auto shrink-0">
        <span class="material-symbols-outlined text-[18px]">confirmation_number</span> Buat Tiket
      </a>
    @endif
  </div>
</div>

 {{-- HERO KPIs — per role --}}
@if($role==='customer')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4 min-w-0">
  <a href="{{ route('lelang.index') }}" class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0 hover:shadow-sm transition">
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Lelang Publik</p>
    <p class="text-2xl font-bold text-primary mt-1">Penjualan Aset</p>
    <p class="text-xs text-on-surface-variant mt-2">Lihat aset lelang & ajukan penawaran</p>
  </a>
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Akun</p>
    <p class="text-lg font-bold text-on-surface mt-1">{{ $user->name }}</p>
    <p class="text-xs text-on-surface-variant">Customer • lelang only</p>
  </div>
  <div class="kpi-card bg-primary text-on-primary rounded-2xl p-4 sm:p-5 min-w-0">
    <p class="text-[10px] font-bold tracking-widest uppercase opacity-80">Akses</p>
    <p class="text-sm font-semibold mt-1">Menu admin disembunyikan — hubungi admin untuk akses lain</p>
  </div>
</div>
@elseif(in_array($role,['pengguna_aset','user']))
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4 min-w-0">
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <div class="flex items-center gap-3 mb-2"><span class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-white">precision_manufacturing</span></span><span class="text-xs font-bold tracking-widest text-on-surface-variant">ASET SAYA</span></div>
    <p class="text-2xl font-bold text-on-surface">{{ $amsStats['total_aset'] }}</p>
    <p class="text-xs text-on-surface-variant"><span class="text-success font-semibold">{{ $amsStats['aset_aktif'] }} aktif</span> • {{ $amsStats['aset_rusak'] }} rusak</p>
    <a href="{{ route('aset.getTable') }}" class="text-xs text-primary underline mt-2 inline-block">Lihat aset →</a>
  </div>
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <div class="flex items-center gap-3 mb-2"><span class="w-10 h-10 rounded-xl bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning">confirmation_number</span></span><span class="text-xs font-bold tracking-widest text-on-surface-variant">TIKET SAYA</span></div>
    <p class="text-2xl font-bold text-on-surface">{{ $amsStats['total_tiket'] }}</p>
    <p class="text-xs text-on-surface-variant">{{ $amsStats['tiket_buka'] }} buka • {{ $amsStats['tiket_progres'] }} progres</p>
    <a href="{{ route('tiket.getTable') }}" class="text-xs text-primary underline mt-2 inline-block">Buat / lihat tiket →</a>
  </div>
  <div class="kpi-card bg-white border border-primary/20 rounded-2xl p-4 sm:p-5 min-w-0 {{ isset($budgetInfo) && $budgetInfo['sisa']<0 ? 'border-error' : '' }}">
    <p class="text-[10px] font-bold tracking-widest uppercase {{ isset($budgetInfo) && $budgetInfo['sisa']<0 ? 'text-error' : 'text-primary' }}">BUDGET SUKU CADANG</p>
    @if(!empty($budgetInfo))
      <p class="text-sm font-bold mt-1">{{ formatRupiah($budgetInfo['terpakai']) }} / {{ formatRupiah($budgetInfo['department']->department_budget) }}</p>
      <p class="text-xs {{ $budgetInfo['sisa']<0?'text-error font-bold':'text-success' }}">Sisa {{ formatRupiah($budgetInfo['sisa']) }}</p>
      <a href="{{ route('permintaan-suku-cadang.getTable') }}" class="text-xs text-primary underline mt-1 inline-block">{{ $amsStats['permintaan_menunggu'] }} menunggu →</a>
    @else
      <p class="text-xs text-on-surface-variant mt-2">Belum ada department</p>
    @endif
  </div>
</div>
@elseif($role==='teknisi')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4 min-w-0">
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Tiket Ditugaskan</p>
    <p class="text-2xl font-bold text-warning mt-1">{{ $amsStats['tiket_progres'] }}</p>
    <p class="text-xs text-on-surface-variant">{{ $amsStats['tiket_buka'] }} buka • {{ $amsStats['total_tiket'] }} total</p>
    <a href="{{ route('tiket.getTable') }}" class="text-xs text-primary underline mt-2 inline-block">Kerjakan tiket →</a>
  </div>
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Batch & Alert</p>
    <p class="text-lg font-bold text-on-surface mt-1">{{ $amsStats['alert_terbuka'] }} alert</p>
    <p class="text-xs text-on-surface-variant">Perlu perhatian</p>
    <a href="{{ route('alert.getTable') }}" class="text-xs text-primary underline mt-1 inline-block">Lihat alert →</a>
  </div>
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase">Aset (view)</p>
    <p class="text-2xl font-bold text-on-surface mt-1">{{ $amsStats['total_aset'] }}</p>
    <p class="text-xs text-on-surface-variant">hanya lihat</p>
    <a href="{{ route('aset.getTable') }}" class="text-xs text-primary underline mt-1 inline-block">Lihat aset →</a>
  </div>
</div>
@else
{{-- developer / admin / supervisor — full KPIs --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-3 sm:mb-4 min-w-0">
  {{-- Total Aset --}}
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 relative overflow-hidden min-w-0">
    <div class="absolute -right-6 -top-6 w-20 h-20 sm:w-24 sm:h-24 bg-primary/5 rounded-full pointer-events-none"></div>
    <div class="flex items-start justify-between gap-2 mb-3 min-w-0">
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-white text-[20px]">precision_manufacturing</span></div>
      <span class="text-[10px] sm:text-[11px] font-bold tracking-widest px-2 py-1 rounded-full bg-success/10 text-success whitespace-nowrap shrink-0">● {{$pctAktif}}% AKTIF</span>
    </div>
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Total Aset</p>
    <p class="text-2xl sm:text-3xl font-bold text-on-surface mt-1 truncate">{{ number_format($amsStats['total_aset']) }}</p>
    <p class="text-[11px] sm:text-xs text-on-surface-variant mt-2 leading-relaxed break-words"><span class="text-success font-semibold">{{$amsStats['aset_aktif']}} aktif</span> • {{$amsStats['aset_maintenance']}} maint • {{$amsStats['aset_rusak']}} rusak</p>
    <div class="mt-3 sm:mt-4 h-1.5 bg-surface-container rounded-full overflow-hidden flex">
      <div class="bg-primary h-full" style="width:{{$pctAktif}}%"></div>
      <div class="bg-warning h-full" style="width:{{$amsStats['total_aset']? round($amsStats['aset_maintenance']/$amsStats['total_aset']*100):0}}%"></div>
      <div class="bg-error h-full" style="width:{{$amsStats['total_aset']? round($amsStats['aset_rusak']/$amsStats['total_aset']*100):0}}%"></div>
    </div>
  </div>

  {{-- Nilai Buku --}}
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0 overflow-hidden">
    <div class="flex items-start justify-between gap-2 mb-3 min-w-0">
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-[#0ea5e9]/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[#0ea5e9]">account_balance_wallet</span></div>
      <span class="text-[11px] font-medium text-on-surface-variant whitespace-nowrap shrink-0">{{ $amsStats['dokumen_expired_soon'] }} exp soon</span>
    </div>
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Nilai Perolehan</p>
    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-on-surface mt-1 break-words leading-tight">Rp {{ $totalNilaiFmt }}</p>
    <p class="text-[11px] sm:text-xs text-on-surface-variant mt-2 flex items-start gap-1 leading-relaxed"><span class="material-symbols-outlined text-[16px] text-success shrink-0 mt-0.5">verified</span><span class="min-w-0">Penyusutan otomatis • Buku besar siap</span></p>
    <a href="{{ route('buku-penyusutan.getTable') }}" class="inline-flex mt-3 text-xs font-semibold text-primary hover:underline items-center gap-1">Lihat buku <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
  </div>

  {{-- Tiket --}}
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <div class="flex items-start justify-between gap-2 mb-3 min-w-0">
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning">confirmation_number</span></div>
      <span class="text-[10px] sm:text-[11px] font-bold tracking-widest px-2 py-1 rounded-full whitespace-nowrap shrink-0 {{ $amsStats['tiket_buka']>0?'bg-error/10 text-error':'bg-success/10 text-success' }}">{{ $amsStats['tiket_buka'] }} BUKA</span>
    </div>
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Tiket Pemeliharaan</p>
    <p class="text-2xl sm:text-3xl font-bold text-on-surface mt-1">{{ $amsStats['total_tiket'] }}</p>
    <div class="grid grid-cols-3 gap-1.5 sm:gap-2 mt-3">
      <span class="bg-surface-container rounded-lg px-1 sm:px-2 py-2 text-center min-w-0"><span class="block text-base sm:text-lg font-bold text-warning">{{ $amsStats['tiket_progres'] }}</span><span class="text-[9px] sm:text-[10px] tracking-widest font-bold text-on-surface-variant block truncate">PROGRES</span></span>
      <span class="bg-surface-container rounded-lg px-1 sm:px-2 py-2 text-center min-w-0"><span class="block text-base sm:text-lg font-bold text-info">{{ $amsStats['tiket_buka'] }}</span><span class="text-[9px] sm:text-[10px] tracking-widest font-bold text-on-surface-variant block truncate">BUKA</span></span>
      <span class="bg-primary/5 rounded-lg px-1 sm:px-2 py-2 text-center min-w-0"><span class="block text-base sm:text-lg font-bold text-primary">{{ $amsStats['total_teknisi'] }}</span><span class="text-[9px] sm:text-[10px] tracking-widest font-bold text-on-surface-variant block truncate">TEKNISI</span></span>
    </div>
  </div>

  {{-- Pinjam & Alert --}}
  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 min-w-0">
    <div class="flex items-start justify-between gap-2 mb-3 min-w-0">
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-success/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-success">swap_horiz</span></div>
      <span class="text-[10px] sm:text-[11px] font-bold tracking-widest px-2 py-1 rounded-full whitespace-nowrap shrink-0 {{ $amsStats['peminjaman_terlambat']>0?'bg-error text-white':'bg-success/10 text-success' }}">{{ $amsStats['peminjaman_terlambat'] }} TERLAMBAT</span>
    </div>
    <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Peminjaman & Alert</p>
    <p class="text-2xl sm:text-3xl font-bold text-on-surface mt-1 truncate">{{ $amsStats['peminjaman_aktif'] }} <span class="text-xs sm:text-sm font-medium text-on-surface-variant">aktif</span></p>
    <div class="grid grid-cols-3 gap-1.5 sm:gap-2 mt-3 text-center">
      <div class="bg-surface-container rounded-xl py-2 min-w-0"><p class="text-sm font-bold text-error truncate">{{ $amsStats['alert_terbuka'] }}</p><p class="text-[9px] sm:text-[10px] font-bold text-on-surface-variant uppercase truncate">Alert</p></div>
      <div class="bg-surface-container rounded-xl py-2 min-w-0"><p class="text-sm font-bold text-warning truncate">{{ $amsStats['service_due'] }}</p><p class="text-[9px] sm:text-[10px] font-bold text-on-surface-variant uppercase truncate">Service</p></div>
      <div class="bg-surface-container rounded-xl py-2 min-w-0"><p class="text-sm font-bold text-primary truncate">{{ $opnameProgress }}</p><p class="text-[9px] sm:text-[10px] font-bold text-on-surface-variant uppercase truncate">Opname</p></div>
    </div>
  </div>
</div>
@endif

 {{-- SECONDARY STRIP — role-aware --}}
@if($role!=='customer')
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2 sm:gap-3 mb-4 sm:mb-5 min-w-0">
  @if(in_array($role,['developer','admin','supervisor']) || $role==='teknisi')
  <a href="{{ route('aset.getTable') }}" class="bg-white border border-outline-variant rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 hover:shadow-sm transition min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[18px] sm:text-[20px]">category</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-on-surface-variant block truncate">Kategori</span><span class="text-xs sm:text-sm font-bold truncate block">{{ $kategoriDist->count() }} top</span></span>
  </a>
  @endif
  @if(in_array($role,['developer','admin']))
  <a href="{{ route('dokumen-aset.getTable') }}" class="bg-white border border-outline-variant rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 hover:shadow-sm transition min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-error/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-error text-[18px] sm:text-[20px]">description</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-on-surface-variant block truncate">Dok exp 30h</span><span class="text-xs sm:text-sm font-bold text-error truncate block">{{ $amsStats['dokumen_expired_soon'] }}</span></span>
  </a>
  @endif
  @if(!in_array($role,['pengguna_aset','user']))
  <a href="{{ route('jadwal-service.getTable') }}" class="bg-white border border-outline-variant rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 hover:shadow-sm transition min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning text-[18px] sm:text-[20px]">event_repeat</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-on-surface-variant block truncate">Service</span><span class="text-xs sm:text-sm font-bold truncate block">{{ $amsStats['service_due'] }} due</span></span>
  </a>
  <a href="{{ route('suku-cadang.getTable') }}" class="bg-white border border-outline-variant rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 hover:shadow-sm transition min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-info/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-info text-[18px] sm:text-[20px]">inventory</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-on-surface-variant block truncate">Stok tipis</span><span class="text-xs sm:text-sm font-bold truncate block">{{ $amsStats['stok_menipis'] ?? 0 }}</span></span>
  </a>
  @endif
  @if(in_array($role,['developer','admin']))
  <div class="bg-white border border-outline-variant rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-success/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-success text-[18px] sm:text-[20px]">people</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-on-surface-variant block truncate">Users</span><span class="text-xs sm:text-sm font-bold truncate block">{{ $stats['total_users'] }} • {{ $stats['unread_notifications'] }}</span></span>
  </div>
  @endif
  <a href="{{ route('permintaan-suku-cadang.getTable') }}" class="bg-white border border-outline-variant rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 hover:shadow-sm transition min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[18px] sm:text-[20px]">inventory_2</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-on-surface-variant block truncate">Permintaan</span><span class="text-xs sm:text-sm font-bold truncate block">{{ $amsStats['permintaan_menunggu'] }} menunggu</span></span>
  </a>
  @if($role!=='teknisi')
  <a href="{{ route('alert.getTable') }}" class="bg-error text-white rounded-xl px-3 sm:px-4 py-3 flex items-center gap-2 sm:gap-3 hover:bg-[#a31515] transition min-w-0 overflow-hidden">
    <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-white/20 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-white text-[18px] sm:text-[20px]">warning</span></span>
    <span class="min-w-0"><span class="text-[11px] sm:text-xs text-white/80 block truncate">Alert</span><span class="text-xs sm:text-sm font-bold truncate block">{{ $amsStats['alert_terbuka'] }} terbuka</span></span>
  </a>
  @endif
</div>
@endif

 {{-- MAIN: charts + side — role-aware, hide for pengguna/customer --}}
@if(in_array($role,['developer','admin','supervisor','teknisi']))
<div class="grid grid-cols-1 xl:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4 min-w-0">
  {{-- Left 2/3 --}}
  <div class="xl:col-span-2 space-y-3 sm:space-y-4 min-w-0">
    @if(in_array($role,['developer','admin']))
    <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
      <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4 min-w-0">
        <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 min-w-0"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[16px] sm:text-[18px]">trending_up</span></span><span class="truncate">Registrasi User • 7 hari</span></h3>
        <span class="text-[11px] text-on-surface-variant hidden sm:block shrink-0">area</span>
      </div>
      <div class="chart-wrap">{!! $userChart->container() !!}</div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 min-w-0">
      @if($role!=='teknisi')
      <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
        <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 mb-3 truncate"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-success/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-success text-[16px] sm:text-[18px]">donut_large</span></span> Status Aset</h3>
        <div class="bg-surface-container rounded-xl p-2 sm:p-3 chart-wrap">
          @if($amsChartAset){!! $amsChartAset->container() !!}@else<p class="text-sm text-center py-8 text-on-surface-variant">Belum ada data</p>@endif
        </div>
        <div class="flex flex-wrap gap-1.5 mt-3">
          @foreach(($kategoriDist ?? collect()) as $k)
            <span class="text-[10px] sm:text-[11px] font-semibold bg-surface-container px-2 py-1 rounded-full truncate max-w-full">{{ $k->aset_kategori_nama }} • {{ $k->total }}</span>
          @endforeach
        </div>
      </div>
      @endif
      <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
        <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 mb-3 truncate"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning text-[16px] sm:text-[18px]">donut_large</span></span> Status Tiket</h3>
        <div class="bg-surface-container rounded-xl p-2 sm:p-3 chart-wrap">
          @if($amsChartTiket){!! $amsChartTiket->container() !!}@else<p class="text-sm text-center py-8 text-on-surface-variant">Belum ada data</p>@endif
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-3 text-xs text-on-surface-variant">
          <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span> Buka</span>
          <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-warning"></span> Progres</span>
          <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-success"></span> Selesai</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Right rail --}}
  <div class="space-y-3 sm:space-y-4 min-w-0">
    @if($role!=='teknisi')
    <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
      <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 mb-3 sm:mb-4 truncate"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-error/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-error text-[16px] sm:text-[18px]">priority_high</span></span> Perlu Perhatian</h3>

      @if($expiringCustom->isNotEmpty())
        <p class="text-[10px] font-bold tracking-widest text-error uppercase mb-2 truncate">STNK / KIR / Pajak ≤ 30 hari</p>
        <div class="space-y-2 mb-4">
          @foreach($expiringCustom as $a)
            @php $cf=$a->aset_custom_fields ?? []; $tgl = $cf['tanggal_expired_stnk'] ?? $cf['tanggal_expired_kir'] ?? $cf['tanggal_pajak'] ?? '-'; @endphp
            <a href="{{ route('aset.getUpdate', ['id'=>$a->aset_id]) }}" class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-xl border border-error/20 bg-error/[0.04] hover:bg-error/[0.08] transition min-w-0 overflow-hidden">
              <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-error/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-error text-[16px] sm:text-[18px]">directions_car</span></span>
              <span class="min-w-0 flex-1">
                <span class="text-xs sm:text-sm font-semibold truncate block">{{ $a->aset_nama }}</span>
                <span class="text-[11px] sm:text-xs text-on-surface-variant truncate block">{{ $a->aset_kode }} • {{ $cf['no_polisi'] ?? $cf['no_stnk'] ?? '-' }}</span>
              </span>
              <span class="text-[11px] sm:text-xs font-bold text-error whitespace-nowrap shrink-0">{{ $tgl }}</span>
            </a>
          @endforeach
        </div>
      @endif

      @if($recentAlerts->isNotEmpty())
        <p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase mb-2 truncate">Alert terbaru</p>
        <div class="space-y-2">
          @foreach($recentAlerts as $al)
            <div class="flex gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-surface-container min-w-0 overflow-hidden">
              <span class="w-2 h-2 rounded-full mt-2 shrink-0 {{ $al->alert_level==='kritis'?'bg-error':($al->alert_level==='peringatan'?'bg-warning':'bg-info') }}"></span>
              <span class="min-w-0 flex-1">
                <span class="text-xs sm:text-sm font-medium leading-tight truncate block">{{ $al->alert_judul }}</span>
                <span class="text-[11px] sm:text-xs text-on-surface-variant truncate block">{{ $al->alert_pesan }}</span>
              </span>
            </div>
          @endforeach
        </div>
        <a href="{{ route('alert.getTable') }}" class="block text-center text-xs font-semibold text-primary mt-3 hover:underline">Lihat semua alert →</a>
      @else
        <p class="text-sm text-on-surface-variant text-center py-6">Tidak ada alert kritis 🎉</p>
      @endif
    </div>
    @endif

    @if(in_array($role,['developer','admin']))
    <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
      <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 mb-3 truncate"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[16px] sm:text-[18px]">notifications</span></span> Notifikasi</h3>
      <div class="bg-surface-container rounded-xl p-2 sm:p-3 chart-wrap">{!! $notifChart->container() !!}</div>
      <div class="grid grid-cols-2 gap-2 mt-3 text-center">
        <div class="bg-success/10 rounded-xl py-2 min-w-0"><p class="text-base sm:text-lg font-bold text-success truncate">{{ $stats['total_notifications'] - $stats['unread_notifications'] }}</p><p class="text-[9px] sm:text-[10px] font-bold tracking-widest text-on-surface-variant truncate">READ</p></div>
        <div class="bg-warning/10 rounded-xl py-2 min-w-0"><p class="text-base sm:text-lg font-bold text-warning truncate">{{ $stats['unread_notifications'] }}</p><p class="text-[9px] sm:text-[10px] font-bold tracking-widest text-on-surface-variant truncate">UNREAD</p></div>
      </div>
    </div>
    @endif
  </div>
</div>
@endif
{{-- Customer / pengguna simple bottom --}}
@if(in_array($role,['pengguna_aset','user','customer']))
<div class="grid grid-cols-1 gap-3 sm:gap-4 mb-4 sm:mb-6 min-w-0">
  <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
    <div class="flex items-center justify-between gap-2 mb-3 min-w-0">
      <h3 class="font-semibold text-sm sm:text-base flex items-center gap-2 min-w-0"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning text-[16px] sm:text-[18px]">confirmation_number</span></span><span class="truncate">Tiket Saya</span></h3>
      <a href="{{ route('tiket.getTable') }}" class="text-xs font-semibold text-primary hover:underline shrink-0">Lihat semua</a>
    </div>
    @if($recentTiket->isNotEmpty())
    <div class="space-y-2">
      @foreach($recentTiket as $t)
        <a href="{{ route('tiket.getUpdate', ['id'=>$t->tiket_id]) }}" class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-surface-container transition border border-transparent hover:border-outline-variant min-w-0 overflow-hidden">
          <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0 {{ $t->tiket_status==='buka'?'bg-error/10 text-error':($t->tiket_status==='progres'?'bg-warning/10 text-warning':'bg-success/10 text-success') }}">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">support</span>
          </span>
          <span class="min-w-0 flex-1">
            <span class="text-xs sm:text-sm font-semibold truncate block">{{ $t->tiket_judul ?? $t->tiket_nomor }}</span>
            <span class="text-[11px] sm:text-xs text-on-surface-variant truncate block">{{ $t->has_aset->aset_nama ?? '—' }} • {{ $t->tiket_tingkat_urgensi ?? '-' }}</span>
          </span>
          <span class="text-[10px] sm:text-[11px] font-bold px-2 py-1 rounded-full whitespace-nowrap shrink-0 {{ $t->tiket_status==='buka'?'bg-error text-white':($t->tiket_status==='progres'?'bg-warning text-white':'bg-success text-white') }}">{{ strtoupper($t->tiket_status) }}</span>
        </a>
      @endforeach
    </div>
    @else
      <p class="text-sm text-on-surface-variant text-center py-8">Belum ada tiket — <a href="{{ route('tiket.getCreate') }}" class="text-primary underline">buat tiket</a></p>
    @endif
    @if(!empty($permintaanRecent) && $permintaanRecent->isNotEmpty())
    <div class="mt-4 pt-4 border-t border-outline-variant/20">
      <h4 class="text-xs font-bold tracking-widest text-on-surface-variant uppercase mb-2">Permintaan Suku Cadang</h4>
      <div class="space-y-2">
        @foreach($permintaanRecent as $p)
          <div class="flex items-center gap-2 p-2 rounded-xl bg-surface-container text-xs">
            <span class="font-mono font-bold">{{ $p->permintaan_suku_cadang_nomor }}</span>
            <span class="truncate flex-1">{{ $p->has_suku_cadang?->suku_cadang_nama ?? '—' }}</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $p->permintaan_suku_cadang_status==='menunggu'?'bg-warning/20 text-warning':($p->permintaan_suku_cadang_status==='disetujui'?'bg-success/20 text-success':'bg-surface-container-high') }}">{{ $p->permintaan_suku_cadang_status }}</span>
          </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>
@endif

 @if(in_array($role,['developer','admin','supervisor']))
 {{-- BOTTOM: tables — admin only --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6 min-w-0">
  <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
    <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4 min-w-0">
      <h3 class="font-semibold text-sm sm:text-base flex items-center gap-2 min-w-0"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning text-[16px] sm:text-[18px]">confirmation_number</span></span><span class="truncate">Tiket Terbaru</span></h3>
      <a href="{{ route('tiket.getTable') }}" class="text-xs font-semibold text-primary hover:underline shrink-0">Lihat semua</a>
    </div>
    @if($recentTiket->isNotEmpty())
    <div class="space-y-2">
      @foreach($recentTiket as $t)
        <a href="{{ route('tiket.getUpdate', ['id'=>$t->tiket_id]) }}" class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-surface-container transition border border-transparent hover:border-outline-variant min-w-0 overflow-hidden">
          <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0 {{ $t->tiket_status==='buka'?'bg-error/10 text-error':($t->tiket_status==='progres'?'bg-warning/10 text-warning':'bg-success/10 text-success') }}">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">support</span>
          </span>
          <span class="min-w-0 flex-1">
            <span class="text-xs sm:text-sm font-semibold truncate block">{{ $t->tiket_judul ?? $t->tiket_nomor }}</span>
            <span class="text-[11px] sm:text-xs text-on-surface-variant truncate block">{{ $t->has_aset->aset_nama ?? '—' }} • {{ $t->tiket_tingkat_urgensi ?? '-' }}</span>
          </span>
          <span class="text-[10px] sm:text-[11px] font-bold px-2 py-1 rounded-full whitespace-nowrap shrink-0 {{ $t->tiket_status==='buka'?'bg-error text-white':($t->tiket_status==='progres'?'bg-warning text-white':'bg-success text-white') }}">{{ strtoupper($t->tiket_status) }}</span>
        </a>
      @endforeach
    </div>
    @else
      <p class="text-sm text-on-surface-variant text-center py-8">Belum ada tiket</p>
    @endif
  </div>

  <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
    <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4 min-w-0">
      <h3 class="font-semibold text-sm sm:text-base flex items-center gap-2 min-w-0"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[16px] sm:text-[18px]">inventory_2</span></span><span class="truncate">Aset Terbaru</span></h3>
      <a href="{{ route('aset.getTable') }}" class="text-xs font-semibold text-primary hover:underline shrink-0">Lihat semua</a>
    </div>
    @if($recentAset->isNotEmpty())
    <div class="space-y-2">
      @foreach($recentAset as $a)
        <a href="{{ route('aset.getUpdate', ['id'=>$a->aset_id]) }}" class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-surface-container transition border border-transparent hover:border-outline-variant min-w-0 overflow-hidden">
          <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-surface-container flex items-center justify-center shrink-0 overflow-hidden">
            @if($a->aset_foto) <img src="{{ fileUrl($a->aset_foto) }}" class="w-full h-full object-cover" alt=""> @else <span class="material-symbols-outlined text-on-surface-variant text-[18px]">image</span> @endif
          </span>
          <span class="min-w-0 flex-1">
            <span class="text-xs sm:text-sm font-semibold truncate block">{{ $a->aset_nama }}</span>
            <span class="text-[11px] sm:text-xs text-on-surface-variant truncate block">{{ $a->aset_kode }} • {{ $a->has_kategori->aset_kategori_nama ?? '-' }}</span>
          </span>
          <span class="text-[10px] sm:text-[11px] font-bold px-2 py-1 rounded-full whitespace-nowrap shrink-0 {{ $a->aset_status==='aktif'?'bg-success/10 text-success':($a->aset_status==='maintenance'?'bg-warning/10 text-warning':'bg-error/10 text-error') }}">{{ strtoupper($a->aset_status) }}</span>
        </a>
      @endforeach
    </div>
    @else
      <p class="text-sm text-on-surface-variant text-center py-8">Belum ada aset</p>
    @endif
  </div>
</div>
@endif

 {{-- Quick nav — role-aware --}}
@if($role==='customer')
<div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
  <h3 class="font-semibold text-sm sm:text-base mb-3 truncate">Akses Cepat</h3>
  <div class="grid grid-cols-1 gap-2 min-w-0">
    <a href="{{ route('lelang.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-surface-container hover:bg-surface-container-high transition">
      <span class="w-10 h-10 rounded-xl bg-white border border-outline-variant flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary">gavel</span></span>
      <span class="text-sm font-semibold">Lelang Publik</span>
    </a>
  </div>
</div>
@elseif(in_array($role,['pengguna_aset','user']))
<div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
  <h3 class="font-semibold text-sm sm:text-base mb-3 truncate">Akses Cepat</h3>
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 min-w-0">
    @php $q2=[['tiket','Tiket Saya','confirmation_number'],['permintaan-suku-cadang','Minta Part','inventory_2'],['aset','Aset Saya','precision_manufacturing']]; @endphp
    @foreach($q2 as [$r,$l,$ic])
      <a href="{{ route($r.'.getTable') }}" class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-surface-container hover:bg-surface-container-high transition border border-transparent hover:border-outline-variant">
        <span class="w-10 h-10 rounded-xl bg-white border border-outline-variant flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[20px]">{{ $ic }}</span></span>
        <span class="text-xs font-semibold text-center">{{ $l }}</span>
      </a>
    @endforeach
  </div>
</div>
@elseif($role==='teknisi')
<div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
  <h3 class="font-semibold text-sm sm:text-base mb-3 truncate">Akses Cepat</h3>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 min-w-0">
    @php $q3=[['tiket','Tiket','confirmation_number'],['batch-tiket','Batch','view_agenda'],['aset','Aset','precision_manufacturing'],['alert','Alert','warning']]; @endphp
    @foreach($q3 as [$r,$l,$ic])
      <a href="{{ route($r.'.getTable') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-surface-container hover:bg-surface-container-high transition border border-transparent hover:border-outline-variant">
        <span class="w-10 h-10 rounded-xl bg-white border border-outline-variant flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[20px]">{{ $ic }}</span></span>
        <span class="text-xs font-semibold text-center">{{ $l }}</span>
      </a>
    @endforeach
  </div>
</div>
@else
<div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
  <h3 class="font-semibold text-sm sm:text-base mb-3 truncate">Akses Cepat</h3>
  <div class="grid grid-cols-3 sm:grid-cols-4 xl:grid-cols-8 gap-2 min-w-0">
    @php $q=[['aset','Aset','precision_manufacturing'],['tiket','Tiket','confirmation_number'],['peminjaman','Pinjam','swap_horiz'],['vendor','Vendor','store'],['gudang','Gudang','warehouse'],['jadwal-service','Service','event_repeat'],['opname','Opname','qr_code_scanner'],['penjualan-aset','Jual','sell']]; @endphp
    @foreach($q as [$r,$l,$ic])
      <a href="{{ route($r.'.getTable') }}" class="flex flex-col items-center gap-1.5 sm:gap-2 p-2.5 sm:p-4 rounded-xl bg-surface-container hover:bg-surface-container-high transition border border-transparent hover:border-outline-variant min-w-0 overflow-hidden">
        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-outline-variant flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-primary text-[18px] sm:text-[20px]">{{ $ic }}</span></span>
        <span class="text-[11px] sm:text-xs font-semibold truncate w-full text-center">{{ $l }}</span>
      </a>
    @endforeach
  </div>
</div>
@endif

</div>

@push('scripts')
  {!! $userChart->script() !!}
  {!! $notifChart->script() !!}
  @if($amsChartAset) {!! $amsChartAset->script() !!} @endif
  @if($amsChartTiket) {!! $amsChartTiket->script() !!} @endif
  <script>
    // Force ApexCharts to fit mobile — re-render on resize, prevent fixed 600px overflow
    window.addEventListener('load', () => {
      setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
    });
  </script>
@endpush
</x-layouts::app>
