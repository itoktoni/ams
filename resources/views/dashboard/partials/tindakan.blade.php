{{-- Panel "Perlu Tindakan" — dipakai bersama semua role.
     $items : collection of ['label','sub','count','icon','tone','route']
     $heading : opsional, default "Perlu Tindakan"
     Item bernilai 0 disembunyikan, sisanya diurutkan dari yang terbanyak. --}}
@php
  $items = collect($items ?? [])
      ->filter(fn ($i) => (int) $i['count'] > 0)
      ->sortByDesc('count')
      ->values();
  $tones = [
    'error'   => ['icon' => 'bg-error/10 text-error',     'num' => 'text-error'],
    'warning' => ['icon' => 'bg-warning/10 text-warning', 'num' => 'text-warning'],
    'info'    => ['icon' => 'bg-info/10 text-info',       'num' => 'text-info'],
  ];
@endphp
<div class="bg-white border border-outline-variant rounded-2xl p-4 sm:p-5 mb-4 sm:mb-5 min-w-0">
  <div class="flex items-center justify-between gap-3 mb-4 min-w-0">
    <h3 class="font-semibold text-sm sm:text-base text-on-surface flex items-center gap-2 min-w-0">
      <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-error/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-error text-[16px] sm:text-[18px]">priority_high</span></span>
      <span class="truncate">{{ $heading ?? 'Perlu Tindakan' }}</span>
    </h3>
    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0 {{ $items->isEmpty() ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">{{ $items->count() }} item</span>
  </div>

  @if($items->isNotEmpty())
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 min-w-0">
    @foreach($items as $it)
    @php $tone = $tones[$it['tone']] ?? $tones['info']; @endphp
    <a href="{{ route($it['route']) }}" class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant hover:border-primary/30 hover:shadow-sm transition min-w-0 overflow-hidden">
      <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl {{ $tone['icon'] }} flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px] sm:text-[20px]">{{ $it['icon'] }}</span></span>
      <span class="min-w-0 flex-1">
        <span class="text-xs sm:text-sm font-semibold text-on-surface truncate block">{{ $it['label'] }}</span>
        <span class="text-[11px] text-on-surface-variant truncate block">{{ $it['sub'] }}</span>
      </span>
      <span class="text-lg sm:text-xl font-bold {{ $tone['num'] }} shrink-0">{{ number_format($it['count']) }}</span>
    </a>
    @endforeach
  </div>
  @else
  <p class="text-sm text-on-surface-variant text-center py-6">Tidak ada yang perlu ditindaklanjuti.</p>
  @endif

  @if(!empty($expiringCustom) && $expiringCustom->isNotEmpty())
  <div class="mt-4 pt-4 border-t border-outline-variant/20">
    <p class="text-[10px] font-bold tracking-widest text-error uppercase mb-2 truncate">STNK / KIR / Pajak kurang dari 30 hari</p>
    <div class="space-y-2">
      @foreach($expiringCustom as $a)
        @php $cf = $a->aset_custom_fields ?? []; $tgl = $cf['tanggal_expired_stnk'] ?? $cf['tanggal_expired_kir'] ?? $cf['tanggal_pajak'] ?? '-'; @endphp
        <a href="{{ route('aset.getUpdate', ['id' => $a->aset_id]) }}" class="flex items-center gap-2 sm:gap-3 p-2.5 rounded-xl border border-error/20 bg-error/[0.04] hover:bg-error/[0.08] transition min-w-0 overflow-hidden">
          <span class="w-8 h-8 rounded-lg bg-error/10 text-error flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[16px]">directions_car</span></span>
          <span class="min-w-0 flex-1">
            <span class="text-xs sm:text-sm font-semibold truncate block">{{ $a->aset_nama }}</span>
            <span class="text-[11px] text-on-surface-variant truncate block">{{ $a->aset_kode }} • {{ $cf['no_polisi'] ?? $cf['no_stnk'] ?? '-' }}</span>
          </span>
          <span class="text-[11px] font-bold text-error whitespace-nowrap shrink-0">{{ $tgl }}</span>
        </a>
      @endforeach
    </div>
  </div>
  @endif
</div>
