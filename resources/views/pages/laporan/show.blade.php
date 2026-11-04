<?php /** @var string $title */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => route('laporan.getIndex'), 'label' => 'Laporan'], ['url' => '', 'label' => $title]]" />

    <x-card :label="$title" icon="assessment">
        <form method="GET" action="{{ route($routeName) }}" class="col-span-12 grid grid-cols-12 gap-3">
            @if($hasPeriode)
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">Periode (YYYY-MM)</label>
                <input type="month" name="periode" value="{{ $periode }}" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
            </div>
            @endif
            @foreach($selects as $sel)
            <div class="col-span-6 md:col-span-3">
                <label class="text-xs font-medium text-on-surface-variant">{{ $sel['label'] }}</label>
                <select name="{{ $sel['param'] }}" class="w-full h-10 px-3 border border-outline-variant rounded-xl text-sm">
                    <option value="">Semua</option>
                    @foreach($sel['options'] as $val => $label)
                    <option value="{{ $val }}" @selected(($activeFilters[$sel['param']] ?? '') === (string)$val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
            <div class="col-span-6 md:col-span-2 flex items-end">
                <button type="submit" class="h-10 px-5 rounded-xl bg-primary text-on-primary font-semibold w-full">Tampilkan</button>
            </div>
            <div class="col-span-12 md:col-span-4 flex items-end gap-2">
                <a href="{{ route($routeName, array_merge($queryParams, ['export' => 'csv'])) }}" class="h-10 px-3 rounded-xl bg-white border border-outline-variant text-sm inline-flex items-center gap-1">CSV</a>
                <a href="{{ route($routeName, array_merge($queryParams, ['export' => 'pdf'])) }}" class="h-10 px-3 rounded-xl bg-white border border-outline-variant text-sm inline-flex items-center gap-1">PDF</a>
            </div>
        </form>

        <div class="col-span-12 grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
            @foreach($aggs as $a)
            <div class="p-3 rounded-xl border {{ $a['color'] }}">
                <p class="text-xs text-on-surface-variant">{{ $a['label'] }}</p>
                <p class="text-xl font-bold">{{ $a['value'] }}</p>
            </div>
            @endforeach
        </div>
    </x-card>

    <x-card :label="'Detail — ' . ($periode ?? 'Semua Periode')" icon="list_alt">
        @if(empty($rows))
        <div class="col-span-12 p-6 text-center text-sm text-on-surface-variant">Tidak ada data untuk filter ini.</div>
        @else
        <div class="col-span-12 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-on-surface-variant border-b">
                        @foreach($headers as $h)<th class="text-left py-2 px-2 whitespace-nowrap">{{ $h }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50">
                        @foreach($row as $cell)<td class="py-2 px-2 whitespace-nowrap">{{ $cell }}</td>@endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="col-span-12 text-xs text-on-surface-variant mt-2">{{ count($rows) }} baris{{ $periode ? " • periode $periode" : '' }}</p>
        @endif
    </x-card>
</x-layouts::app>
