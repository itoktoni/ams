@php
    $bottomNav = config('menu.bottom_nav');
    $user = auth()->user();
    $restrict = config('permision', []);
    $role = $user?->role ?? 'guest';
    $isDenied = function($routeName) use ($restrict, $role) {
        if (! isset($restrict[$role])) return false;
        $mod = explode('.', $routeName)[0];
        foreach ($restrict[$role] as $key => $rule) {
            $match = $mod === $key || str_starts_with($mod, $key) || str_starts_with($routeName, $key.'.') || $routeName === $key;
            if (! $match) continue;
            if ($rule === false) return true;
            if (is_array($rule) && (in_array('table', $rule, true) || isset($rule['table']))) return true;
        }
        return false;
    };
    // customer khusus: ganti bottom nav jadi Home + Lelang
    if ($role === 'customer') {
        $bottomNav = [
            ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'lelang.index', 'icon' => 'gavel', 'label' => 'Lelang'],
        ];
        // filter tetap, tapi lelang tidak di-restrict
    } else {
        $bottomNav = collect($bottomNav)->filter(fn($it) => ! $isDenied($it['route']))->values()->all();
        if (count($bottomNav) > 5) $bottomNav = array_slice($bottomNav, 0, 5);
        if (empty($bottomNav)) $bottomNav = config('menu.bottom_nav');
    }
@endphp

<nav class="md:hidden fixed inset-x-0 bottom-0 z-50 h-16 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
    <div class="flex items-center justify-around h-16 px-2">
        @foreach($bottomNav as $index => $item)
            @php
                $routeName = $item['route'];
                $url = route($routeName);
                $isActive = request()->routeIs($routeName) || request()->routeIs($routeName . '.*');
                $isCenter = $index === 2;
            @endphp

            @if($isCenter)
                <div class="flex items-center justify-center flex-1 -mt-4">
                    <a
                        href="{{ $url }}"
                        wire:navigate
                        class="flex items-center justify-center bg-primary text-on-primary w-14 h-14 rounded-2xl shadow-lg ring-4 ring-surface-container-lowest active:scale-90 transition-all"
                    >
                        <span class="material-symbols-outlined text-[28px]">{{ $item['icon'] }}</span>
                    </a>
                </div>
            @else
                <a href="{{ $url }}" wire:navigate class="flex flex-col items-center justify-center transition-all flex-1 {{ $isActive ? 'text-primary opacity-100' : 'text-on-surface-variant opacity-60 hover:opacity-100' }}">
                    <span class="material-symbols-outlined text-[24px]">{{ $item['icon'] }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-tighter">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
