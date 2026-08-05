@php
    $categories = App\Models\kategori::orderBy('name')->get();
@endphp

<header
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    class="sticky top-0 z-50 border-b border-press/12 bg-ledger/95 backdrop-blur"
>
    <nav class="mx-auto grid h-16 max-w-6xl grid-cols-2 items-center px-4 sm:px-6 md:grid-cols-3" aria-label="Navigasi utama">
        {{-- Wordmark --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('img/logo.svg') }}" alt="Lembar" class="h-8 w-8" />
            <span class="font-mono text-[1.05rem] font-semibold tracking-tight text-press">Lembar</span>
        </a>

        {{-- Menu (desktop, tengah) --}}
        <div class="hidden items-center justify-center gap-6 md:flex">
            <a href="{{ route('artikel.index') }}" class="font-mono text-xs uppercase tracking-wider text-mute transition-colors hover:text-press focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-press">Artikel</a>
            <a href="{{ route('about') }}" class="font-mono text-xs uppercase tracking-wider text-mute transition-colors hover:text-press focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-press">About</a>

            {{-- Dropdown Kategori --}}
            <div class="relative" x-data="{ cat: false }" @click.outside="cat = false" @keydown.escape.window="cat = false">
                <button
                    type="button"
                    @click="cat = !cat"
                    :aria-expanded="cat"
                    aria-haspopup="true"
                    class="flex items-center gap-1.5 font-mono text-xs uppercase tracking-wider text-mute transition-colors hover:text-press focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-press"
                >
                    Kategori
                    <svg class="h-3 w-3 transition-transform duration-200" :class="cat ? 'rotate-180' : ''" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="M3 4.5 6 7.5 9 4.5" />
                    </svg>
                </button>
                <div
                    x-show="cat"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="-translate-y-1 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="-translate-y-1 opacity-0"
                    class="absolute left-1/2 top-full z-50 mt-2 w-48 -translate-x-1/2 rounded-md border border-press/12 bg-ledger py-1 shadow-sm"
                    style="display: none;"
                >
                    @foreach ($categories as $c)
                        <a href="{{ route('kategori.show', $c->slug) }}" @click="cat = false" class="block px-4 py-2 font-mono text-sm text-mute transition-colors hover:bg-press/5 hover:text-press">{{ $c->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Cari + Langganan (desktop, kanan) --}}
        <div class="hidden items-center justify-end gap-5 md:flex">
            <form action="{{ route('home') }}" method="GET" role="search" class="relative">
                <label for="cari-nav" class="sr-only">Cari artikel</label>
                <input id="cari-nav" type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari artikel…" class="w-44 border-0 border-b border-press/20 bg-transparent py-1 font-mono text-sm text-press placeholder:text-mute/70 focus:border-press focus:outline-none" />
            </form>
            <a href="#langganan" class="font-mono text-xs uppercase tracking-wider text-press transition-colors hover:text-stamp">Langganan</a>
        </div>

        {{-- Burger (mobile) --}}
        <button
            type="button"
            @click="open = !open"
            :aria-expanded="open"
            aria-label="Buka menu"
            class="flex h-10 w-10 flex-col items-center justify-center gap-1.5 justify-self-end md:hidden"
        >
            <span class="sr-only">Buka menu</span>
            <span class="block h-0.5 w-5 bg-press transition-transform duration-200" :class="open ? 'translate-y-2 rotate-45' : ''"></span>
            <span class="block h-0.5 w-5 bg-press transition-opacity duration-200" :class="open ? 'opacity-0' : ''"></span>
            <span class="block h-0.5 w-5 bg-press transition-transform duration-200" :class="open ? '-translate-y-2 -rotate-45' : ''"></span>
        </button>
    </nav>

    {{-- Panel mobile --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-2 opacity-0"
        @click.outside="open = false"
        class="border-t border-press/12 bg-ledger md:hidden"
        style="display: none;"
    >
        <div class="mx-auto max-w-6xl px-4 py-2 sm:px-6">
            <form action="{{ route('home') }}" method="GET" role="search" class="py-2">
                <label for="cari-mob" class="sr-only">Cari artikel</label>
                <input id="cari-mob" type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari artikel…" class="w-full border-0 border-b border-press/20 bg-transparent py-1 font-mono text-sm focus:outline-none" />
            </form>
            <a href="{{ route('artikel.index') }}" @click="open = false" class="block border-b border-press/5 py-3 font-mono text-sm uppercase tracking-wider text-mute transition-colors hover:text-press">Artikel</a>
            <a href="{{ route('about') }}" @click="open = false" class="block border-b border-press/5 py-3 font-mono text-sm uppercase tracking-wider text-mute transition-colors hover:text-press">About</a>
            <p class="py-3 font-mono text-[0.62rem] uppercase tracking-wider text-mute/70">Kategori</p>
            @foreach ($categories as $c)
                <a href="{{ route('kategori.show', $c->slug) }}" @click="open = false" class="block border-b border-press/5 py-3 pl-4 font-mono text-sm text-mute transition-colors hover:text-press">{{ $c->name }}</a>
            @endforeach
            <a href="#langganan" @click="open = false" class="block py-3 font-mono text-sm uppercase tracking-wider text-stamp">Langganan</a>
        </div>
    </div>
</header>