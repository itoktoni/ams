<nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-outline-variant/20" id="main-nav">
    <div class="flex justify-between items-center px-6 md:px-8 py-4 max-w-7xl mx-auto">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-primary text-on-primary flex items-center justify-center font-headline-md text-sm font-bold">{{ Str::upper(Str::substr(config('app.name', 'CP'), 0, 2)) }}</span>
            <span class="font-headline-md text-headline-md font-bold text-on-surface hidden sm:inline">{{ config('app.name', 'Company') }}</span>
        </a>
        <div class="hidden md:flex items-center gap-8">
            @if($menu && $menu->items)
                @php $navItems = collect($menu->items)->sortBy('sort_order')->values(); @endphp
                @foreach($navItems as $item)
                    @if(!empty($item['children']) && count($item['children']) > 0)
                        <div class="relative group">
                            <button class="font-label-md text-sm {{ request()->is(trim($item['url'],'/').'*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} flex items-center gap-1 transition-colors">
                                {{ $item['label'] }}
                                <span class="material-symbols-outlined text-sm group-hover:rotate-180 transition-transform">expand_more</span>
                            </button>
                            <div class="absolute left-0 top-full pt-2 hidden group-hover:block z-50">
                                <div class="bg-white rounded-xl shadow-lg border border-outline-variant/20 py-2 min-w-[220px]">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ $child['url'] ?? '#' }}" target="{{ $child['target'] ?? '_self' }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ $item['url'] }}" class="font-label-md text-sm {{ request()->is(trim($item['url'],'/')) || (trim($item['url'],'/')=='' && request()->is('/')) ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            @else
                <a href="{{ url('/') }}" class="font-label-md text-sm {{ request()->is('/') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }}">Beranda</a>
                <a href="{{ url('/#produk') }}" class="font-label-md text-sm text-on-surface-variant hover:text-primary">Fitur</a>
                <a href="{{ url('/#tentang') }}" class="font-label-md text-sm text-on-surface-variant hover:text-primary">Tentang</a>
                <a href="{{ route('contact') }}" class="font-label-md text-sm text-on-surface-variant hover:text-primary">Kontak</a>
            @endif
        </div>
        <div class="flex items-center gap-2 md:gap-3">
            <a href="{{ route('search') }}" class="w-9 h-9 rounded-full border border-outline-variant/30 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-colors" aria-label="Search"><span class="material-symbols-outlined text-xl">search</span></a>
            <a href="{{ route('contact') }}" class="hidden sm:inline-flex bg-primary text-on-primary px-5 py-2.5 rounded-full font-label-md text-sm hover:opacity-90 active:scale-95 transition-all">Pesan Sekarang</a>
            <button id="mobile-menu-btn" class="md:hidden w-9 h-9 rounded-full border border-outline-variant/30 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-colors" aria-label="Menu" aria-expanded="false">
                <span class="material-symbols-outlined text-xl" id="mobile-menu-icon">menu</span>
            </button>
        </div>
    </div>
    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-outline-variant/20 bg-white/95 backdrop-blur-md">
        <div class="px-6 py-4 space-y-1 max-h-[80vh] overflow-y-auto">
            @if($menu && $menu->items)
                @php $mNavItems = collect($menu->items)->sortBy('sort_order')->values(); @endphp
                @foreach($mNavItems as $mItem)
                    @if(!empty($mItem['children']) && count($mItem['children']) > 0)
                        <div class="border-b border-outline-variant/10 last:border-0">
                            <button type="button" class="w-full flex items-center justify-between py-3 text-sm font-semibold text-on-surface hover:text-primary transition-colors mobile-dropdown-btn" aria-expanded="false">
                                <span>{{ $mItem['label'] }}</span>
                                <span class="material-symbols-outlined text-lg transition-transform">expand_more</span>
                            </button>
                            <div class="hidden pb-2 pl-3 space-y-1 mobile-dropdown-panel">
                                @foreach($mItem['children'] as $mChild)
                                    <a href="{{ $mChild['url'] ?? '#' }}" target="{{ $mChild['target'] ?? '_self' }}" class="flex items-center gap-2 py-2.5 px-3 text-sm text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                        {{ $mChild['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $mItem['url'] }}" class="flex items-center py-3 text-sm font-medium {{ request()->is(trim($mItem['url'],'/')) || (trim($mItem['url'],'/')=='' && request()->is('/')) ? 'text-primary' : 'text-on-surface hover:text-primary' }} border-b border-outline-variant/10 last:border-0 transition-colors">{{ $mItem['label'] }}</a>
                    @endif
                @endforeach
            @else
                <a href="{{ url('/') }}" class="flex items-center py-3 text-sm font-medium {{ request()->is('/') ? 'text-primary' : 'text-on-surface hover:text-primary' }} border-b border-outline-variant/10 transition-colors">Beranda</a>
                <a href="{{ url('/#produk') }}" class="flex items-center py-3 text-sm font-medium text-on-surface hover:text-primary border-b border-outline-variant/10 transition-colors">Fitur</a>
                <a href="{{ url('/#tentang') }}" class="flex items-center py-3 text-sm font-medium text-on-surface hover:text-primary border-b border-outline-variant/10 transition-colors">Tentang</a>
                <a href="{{ route('contact') }}" class="flex items-center py-3 text-sm font-medium text-on-surface hover:text-primary transition-colors">Kontak</a>
            @endif
            <div class="pt-4">
                <a href="{{ route('contact') }}" class="flex items-center justify-center gap-2 w-full bg-primary text-on-primary px-5 py-3 rounded-full font-label-md text-sm hover:opacity-90 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">chat</span> Pesan Sekarang
                </a>
            </div>
        </div>
    </div>
</nav>
<script>
(function(){
    var btn = document.getElementById('mobile-menu-btn');
    var menu = document.getElementById('mobile-menu');
    var icon = document.getElementById('mobile-menu-icon');
    if(!btn || !menu) return;
    btn.addEventListener('click', function(){
        var isHidden = menu.classList.contains('hidden');
        menu.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        if(icon) icon.textContent = isHidden ? 'close' : 'menu';
    });
    // dropdown accordion for Profil etc
    document.querySelectorAll('.mobile-dropdown-btn').forEach(function(b){
        b.addEventListener('click', function(){
            var panel = b.nextElementSibling;
            var expanded = b.getAttribute('aria-expanded') === 'true';
            b.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            if(panel) panel.classList.toggle('hidden');
            var arrow = b.querySelector('.material-symbols-outlined');
            if(arrow) arrow.style.transform = expanded ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    });
    // close when click outside or navigate
    document.addEventListener('click', function(e){
        if(!menu.contains(e.target) && !btn.contains(e.target) && !menu.classList.contains('hidden')){
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded','false');
            if(icon) icon.textContent='menu';
        }
    });
})();
</script>
