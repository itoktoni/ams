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
  /* ApexCharts fix: width is already 100% (Larapex default), so only constrain the
     wrapper. Forcing svg width:100% would squish the donut into an ellipse on mobile,
     so we let ApexCharts keep the circle round and just center it. */
  .chart-wrap{position:relative; width:100%; min-width:0; overflow:hidden}
  .chart-wrap .apexcharts-canvas{width:100% !important; margin:0 auto !important}
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
      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $budgetInfo['tersedia']<0?'bg-error/10 border-error text-error':'bg-primary/5 border-primary/20 text-primary' }}">
        <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span>
        Budget {{ $budgetInfo['department']->department_nama }}: {{ formatRupiah($budgetInfo['tersedia']) }} tersedia / {{ formatRupiah($budgetInfo['department']->department_budget) }}
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
@php
  $pctMaint = $amsStats['total_aset'] ? round($amsStats['aset_maintenance']/$amsStats['total_aset']*100) : 0;
  $pctRusak = $amsStats['total_aset'] ? round($amsStats['aset_rusak']/$amsStats['total_aset']*100) : 0;
  $tindakan = collect([
    ['label' => 'Aset rusak',             'sub' => 'perlu dilaporkan',     'count' => (int) $amsStats['aset_rusak'],           'icon' => 'report',              'tone' => 'error',   'route' => 'aset.getTable'],
    ['label' => 'Permintaan suku cadang', 'sub' => 'menunggu persetujuan', 'count' => (int) $amsStats['permintaan_menunggu'], 'icon' => 'inventory_2',         'tone' => 'warning', 'route' => 'permintaan-suku-cadang.getTable'],
    ['label' => 'Tiket menunggu teknisi', 'sub' => 'belum ditangani',      'count' => (int) $amsStats['tiket_buka'],          'icon' => 'confirmation_number', 'tone' => 'warning', 'route' => 'tiket.getTable'],
    ['label' => 'Tiket progres',          'sub' => 'sedang dikerjakan',    'count' => (int) $amsStats['tiket_progres'],       'icon' => 'engineering',         'tone' => 'info',    'route' => 'tiket.getTable'],
  ]);
@endphp

{{-- 3 angka utama --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-5 min-w-0">

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-primary text-[18px] shrink-0">precision_manufacturing</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Aset Saya</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['total_aset']) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate"><span class="text-success font-semibold">{{ $amsStats['aset_aktif'] }} aktif</span> • {{ $amsStats['aset_maintenance'] }} maint • {{ $amsStats['aset_rusak'] }} rusak</p>
    <div class="mt-auto pt-2.5">
      <div class="h-1.5 bg-surface-container rounded-full overflow-hidden flex">
        <div class="bg-success h-full" style="width:{{ $pctAktif }}%"></div>
        <div class="bg-warning h-full" style="width:{{ $pctMaint }}%"></div>
        <div class="bg-error h-full" style="width:{{ $pctRusak }}%"></div>
      </div>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-warning text-[18px] shrink-0">confirmation_number</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Tiket Saya</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['total_tiket']) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate"><span class="text-error font-semibold">{{ $amsStats['tiket_buka'] }} buka</span> • <span class="text-warning font-semibold">{{ $amsStats['tiket_progres'] }} progres</span></p>
    <div class="mt-auto pt-2.5">
      <a href="{{ route('tiket.getCreate') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Buat tiket <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-[#0ea5e9] text-[18px] shrink-0">account_balance_wallet</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Budget Suku Cadang</span>
    </div>
    @if(!empty($budgetInfo))
      <p class="text-base sm:text-lg font-bold text-on-surface truncate leading-tight">{{ formatRupiah($budgetInfo['tersedia']) }}</p>
      <p class="text-[11px] text-on-surface-variant mt-1 truncate">Terpakai {{ formatRupiah($budgetInfo['terpakai']) }} • Menunggu {{ formatRupiah($budgetInfo['pending']) }}</p>
      <div class="mt-auto pt-2.5">
        <a href="{{ route('permintaan-suku-cadang.getCreate') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Ajukan permintaan <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
      </div>
    @else
      <p class="text-sm font-semibold text-on-surface mt-1 truncate">Belum ada department</p>
      <p class="text-[11px] text-on-surface-variant mt-1 truncate">Hubungi admin untuk assign</p>
      <div class="mt-auto pt-2.5"><span class="text-[11px] text-on-surface-variant">Budget tidak tersedia</span></div>
    @endif
  </div>
</div>

@include('dashboard.partials.tindakan', ['items' => $tindakan])
@elseif($role==='teknisi')
@php
  $tiketSelesai = max(0, (int) $amsStats['total_tiket'] - (int) $amsStats['tiket_buka'] - (int) $amsStats['tiket_progres']);
  $pctBuka   = $amsStats['total_tiket'] ? round($amsStats['tiket_buka']/$amsStats['total_tiket']*100) : 0;
  $pctProgres= $amsStats['total_tiket'] ? round($amsStats['tiket_progres']/$amsStats['total_tiket']*100) : 0;
  $pctDone   = $amsStats['total_tiket'] ? round($tiketSelesai/$amsStats['total_tiket']*100) : 0;
  $tindakan = collect([
    ['label' => 'Tiket belum ditangani', 'sub' => 'status buka',         'count' => (int) $amsStats['tiket_buka'],           'icon' => 'confirmation_number', 'tone' => 'error',   'route' => 'tiket.getTable'],
    ['label' => 'Service jatuh tempo',   'sub' => 'kurang dari 14 hari', 'count' => (int) $amsStats['service_due'],          'icon' => 'event_repeat',        'tone' => 'warning', 'route' => 'jadwal-service.getTable'],
    ['label' => 'Stok menipis',          'sub' => 'di bawah minimum',    'count' => (int) ($amsStats['stok_menipis'] ?? 0),  'icon' => 'inventory',           'tone' => 'warning', 'route' => 'stok-suku-cadang.getTable'],
    ['label' => 'Alert terbuka',         'sub' => 'perlu ditinjau',      'count' => (int) $amsStats['alert_terbuka'],        'icon' => 'warning',             'tone' => 'warning', 'route' => 'alert.getTable'],
    ['label' => 'Tiket progres',         'sub' => 'sedang dikerjakan',   'count' => (int) $amsStats['tiket_progres'],        'icon' => 'engineering',         'tone' => 'info',    'route' => 'tiket.getTable'],
  ]);
@endphp

{{-- 3 angka utama --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-5 min-w-0">

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-primary text-[18px] shrink-0">confirmation_number</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Tiket Saya</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['total_tiket']) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate"><span class="text-error font-semibold">{{ $amsStats['tiket_buka'] }} buka</span> • <span class="text-warning font-semibold">{{ $amsStats['tiket_progres'] }} progres</span> • {{ $tiketSelesai }} selesai</p>
    <div class="mt-auto pt-2.5">
      <div class="h-1.5 bg-surface-container rounded-full overflow-hidden flex">
        <div class="bg-error h-full" style="width:{{ $pctBuka }}%"></div>
        <div class="bg-warning h-full" style="width:{{ $pctProgres }}%"></div>
        <div class="bg-success h-full" style="width:{{ $pctDone }}%"></div>
      </div>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-error text-[18px] shrink-0">priority_high</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Menunggu Dikerjakan</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold {{ $amsStats['tiket_buka'] > 0 ? 'text-error' : 'text-success' }} truncate">{{ number_format($amsStats['tiket_buka']) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate">tiket berstatus buka</p>
    <div class="mt-auto pt-2.5">
      <a href="{{ route('tiket.getTable') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Ambil tiket <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-[#0ea5e9] text-[18px] shrink-0">build</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Service &amp; Stok</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['service_due']) }} <span class="text-xs font-medium text-on-surface-variant">service</span></p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate {{ ($amsStats['stok_menipis'] ?? 0) > 0 ? 'text-warning font-semibold' : '' }}">{{ $amsStats['stok_menipis'] ?? 0 }} stok di bawah minimum</p>
    <div class="mt-auto pt-2.5">
      <a href="{{ route('jadwal-service.getTable') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Jadwal service <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
    </div>
  </div>
</div>

@include('dashboard.partials.tindakan', ['items' => $tindakan, 'heading' => 'Perlu Tindakan · Teknisi'])
@else
{{-- developer / admin / supervisor — ringkas & informatif --}}
@php
  $pctMaint = $amsStats['total_aset'] ? round($amsStats['aset_maintenance']/$amsStats['total_aset']*100) : 0;
  $pctRusak = $amsStats['total_aset'] ? round($amsStats['aset_rusak']/$amsStats['total_aset']*100) : 0;
  $tindakan = collect([
    ['label' => 'Tiket belum ditangani',   'sub' => 'status buka',          'count' => (int) $amsStats['tiket_buka'],              'icon' => 'confirmation_number', 'tone' => 'error',   'route' => 'tiket.getTable'],
    ['label' => 'Peminjaman terlambat',    'sub' => 'lewat jatuh tempo',    'count' => (int) $amsStats['peminjaman_terlambat'],   'icon' => 'swap_horiz',          'tone' => 'error',   'route' => 'peminjaman.getTable'],
    ['label' => 'Dokumen expired',         'sub' => 'kurang dari 30 hari',  'count' => (int) $amsStats['dokumen_expired_soon'],   'icon' => 'description',         'tone' => 'error',   'route' => 'dokumen-aset.getTable'],
    ['label' => 'Alert terbuka',           'sub' => 'perlu ditinjau',       'count' => (int) $amsStats['alert_terbuka'],          'icon' => 'warning',             'tone' => 'warning', 'route' => 'alert.getTable'],
    ['label' => 'Permintaan suku cadang',  'sub' => 'menunggu persetujuan', 'count' => (int) $amsStats['permintaan_menunggu'],    'icon' => 'inventory_2',         'tone' => 'warning', 'route' => 'permintaan-suku-cadang.getTable'],
    ['label' => 'Stok menipis',            'sub' => 'di bawah minimum',     'count' => (int) ($amsStats['stok_menipis'] ?? 0),    'icon' => 'inventory',           'tone' => 'warning', 'route' => 'stok-suku-cadang.getTable'],
    ['label' => 'Service jatuh tempo',     'sub' => 'kurang dari 14 hari',  'count' => (int) $amsStats['service_due'],            'icon' => 'event_repeat',        'tone' => 'info',    'route' => 'jadwal-service.getTable'],
    ['label' => 'Tiket progres',           'sub' => 'sedang dikerjakan',    'count' => (int) $amsStats['tiket_progres'],          'icon' => 'engineering',         'tone' => 'info',    'route' => 'tiket.getTable'],
    ['label' => 'Opname berjalan',         'sub' => 'sedang proses',        'count' => (int) $opnameProgress,                     'icon' => 'qr_code_scanner',     'tone' => 'info',    'route' => 'opname.getTable'],
  ])->filter(fn ($i) => $i['count'] > 0)->sortByDesc('count')->values();
@endphp

{{-- 4 angka utama --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-5 min-w-0">

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-primary text-[18px] shrink-0">precision_manufacturing</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Total Aset</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['total_aset']) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate"><span class="text-success font-semibold">{{ $amsStats['aset_aktif'] }} aktif</span> • {{ $amsStats['aset_maintenance'] }} maint • {{ $amsStats['aset_rusak'] }} rusak</p>
    <div class="mt-auto pt-2.5">
      <div class="h-1.5 bg-surface-container rounded-full overflow-hidden flex">
        <div class="bg-success h-full" style="width:{{ $pctAktif }}%"></div>
        <div class="bg-warning h-full" style="width:{{ $pctMaint }}%"></div>
        <div class="bg-error h-full" style="width:{{ $pctRusak }}%"></div>
      </div>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-[#0ea5e9] text-[18px] shrink-0">account_balance_wallet</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Nilai Perolehan</span>
    </div>
    <p class="text-base sm:text-lg lg:text-xl font-bold text-on-surface truncate leading-tight">{{ formatRupiah($amsStats['total_nilai'] ?? 0) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate">{{ number_format($amsStats['total_aset']) }} aset tercatat</p>
    <div class="mt-auto pt-2.5">
      <a href="{{ route('buku-penyusutan.getTable') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Buku penyusutan <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-warning text-[18px] shrink-0">confirmation_number</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Tiket</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['total_tiket']) }}</p>
    <p class="text-[11px] text-on-surface-variant mt-1 truncate"><span class="text-error font-semibold">{{ $amsStats['tiket_buka'] }} buka</span> • <span class="text-warning font-semibold">{{ $amsStats['tiket_progres'] }} progres</span> • {{ $amsStats['total_teknisi'] }} teknisi</p>
    <div class="mt-auto pt-2.5">
      <a href="{{ route('tiket.getTable') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Kelola tiket <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
    </div>
  </div>

  <div class="kpi-card bg-white border border-outline-variant rounded-2xl p-3 sm:p-4 min-w-0 flex flex-col">
    <div class="flex items-center gap-2 mb-2 min-w-0">
      <span class="material-symbols-outlined text-success text-[18px] shrink-0">swap_horiz</span>
      <span class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase truncate">Peminjaman</span>
    </div>
    <p class="text-xl sm:text-2xl font-bold text-on-surface truncate">{{ number_format($amsStats['peminjaman_aktif']) }} <span class="text-xs font-medium text-on-surface-variant">aktif</span></p>
    <p class="text-[11px] mt-1 truncate {{ $amsStats['peminjaman_terlambat'] > 0 ? 'text-error font-semibold' : 'text-on-surface-variant' }}">{{ $amsStats['peminjaman_terlambat'] }} terlambat</p>
    <div class="mt-auto pt-2.5">
      <a href="{{ route('peminjaman.getTable') }}" class="text-[11px] font-semibold text-primary hover:underline inline-flex items-center gap-1">Kelola pinjam <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
    </div>
  </div>
</div>

@include('dashboard.partials.tindakan', ['items' => $tindakan])
@endif


 {{-- MAIN: charts + side — role-aware, hide for pengguna/customer --}}
@if(in_array($role,['developer','admin','supervisor']))
{{-- Distribusi: aset & tiket --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-5 min-w-0">
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
@elseif($role==='teknisi')
<div class="grid grid-cols-1 gap-3 sm:gap-4 mb-4 sm:mb-5 min-w-0">
  <div class="bg-white border border-outline-variant rounded-2xl p-3 sm:p-5 min-w-0 overflow-hidden">
    <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 mb-3 truncate"><span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-warning/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-warning text-[16px] sm:text-[18px]">donut_large</span></span> Status Tiket</h3>
    <div class="bg-surface-container rounded-xl p-2 sm:p-3 chart-wrap">
      @if($amsChartTiket){!! $amsChartTiket->container() !!}@else<p class="text-sm text-center py-8 text-on-surface-variant">Belum ada data</p>@endif
    </div>
  </div>
</div>
@endif
{{-- Customer / pengguna simple bottom --}}
@if(in_array($role,['pengguna_aset','user','customer','teknisi']))
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
    @if($role!=='teknisi' && !empty($permintaanRecent) && $permintaanRecent->isNotEmpty())
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
