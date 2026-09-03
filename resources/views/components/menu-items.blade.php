@props(['items', 'mobile' => false])

@php
    $menu = config('menu.sidebar');
    $user = auth()->user();
    $restrict = config('permision', []);
    $role = $user?->role ?? 'guest';
    // helper: cek apakah role di-restrict untuk route ini (table/view)
    $isDenied = function($routeName) use ($restrict, $role) {
        if (! isset($restrict[$role])) return false;
        $mod = explode('.', $routeName)[0]; // aset.getTable -> aset
        foreach ($restrict[$role] as $key => $rule) {
            $match = $mod === $key || str_starts_with($mod, $key) || str_starts_with($routeName, $key.'.') || $routeName === $key;
            if (! $match) continue;
            if ($rule === false) return true;
            if (is_array($rule) && (in_array('table', $rule, true) || isset($rule['table']) || isset($rule['view']))) return true;
        }
        return false;
    };
@endphp

@foreach($menu as $section)
    @php
        // filter items yang deny untuk role ini
        $visibleItems = collect($section['items'])->filter(fn($it) => ! $isDenied($it['route']));
        if ($visibleItems->isEmpty() && $section['label']) continue;
    @endphp
    @if($section['label'] && $visibleItems->isNotEmpty())
        <div class="px-4 pt-4 pb-1 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ $section['label'] }}</div>
    @endif
    @foreach($visibleItems as $item)
        @php
            $routeName = $item['route'];
            $url = route($routeName);
            $matchRoutes = $item['match'] ?? [];
            $isActive = request()->routeIs($routeName)
                || request()->routeIs($routeName . '.*')
                || collect($matchRoutes)->contains(fn ($m) => request()->routeIs($m));
        @endphp
        <a
            href="{{ $url }}"
            wire:navigate
            @if($mobile) @click="drawerOpen = false" @endif
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ $mobile ? '' : 'group' }} {{ $isActive ? 'bg-primary text-on-primary font-semibold' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}"
        >
            <span class="material-symbols-outlined {{ $isActive ? 'text-on-primary' : 'text-on-surface-variant' . ($mobile ? '' : ' group-hover:text-on-surface') }}">{{ $item['icon'] }}</span>
            <span class="font-body-sm">{{ $item['label'] }}</span>
        </a>
    @endforeach
@endforeach
