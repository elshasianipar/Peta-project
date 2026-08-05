@extends('layouts.editorial')

@section('title', 'Lembar — uang, ditulis pelan-pelan.')
@section('meta_description', 'Artikel keuangan untuk anak muda Indonesia: anggaran, pasar, rupiah, dan investasi tanpa jargon.')

@section('content')
    @php
        $period = now()->locale('id')->translatedFormat('F Y');
        $code = fn (?string $slug) => $slug ? Illuminate\Support\Str::upper(Illuminate\Support\Str::substr($slug, 0, 3)) : '—';
    @endphp

    {{-- 1. Hero / statement header --}}
    <section class="border-b border-press/12">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Laporan — {{ $period }}</p>
            <h1 class="mt-5 max-w-3xl font-mono text-4xl font-semibold leading-[1.05] tracking-tight text-press sm:text-6xl">
                Uang, ditulis<br>pelan-pelan.
            </h1>
            <p class="mt-5 max-w-xl text-base leading-relaxed text-mute sm:text-lg">
                Lembar menulis keuangan untuk anak muda Indonesia — anggaran, pasar, dan rupiah tanpa jargon. Baca pelan-pelan, pahamnya lebih lama.
            </p>
            <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
                <a href="#langganan" class="inline-flex items-center justify-center rounded-md bg-stamp px-6 py-3 font-mono text-sm font-medium uppercase tracking-wider text-ledger transition-colors hover:bg-stamp/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stamp">Langganan edaran</a>
                <a href="#edisi" class="font-mono text-sm uppercase tracking-wider text-press transition-colors hover:text-stamp focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stamp">Lihat edisi →</a>
            </div>

            <dl class="mt-12 grid grid-cols-2 gap-y-4 border-t border-press/12 pt-6 sm:grid-cols-3">
                <div>
                    <dt class="font-mono text-[0.62rem] uppercase tracking-wider text-mute">Edisi</dt>
                    <dd class="mt-1 font-mono text-2xl tabular-nums text-press">{{ number_format($totals['articles'], 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-[0.62rem] uppercase tracking-wider text-mute">Penulis</dt>
                    <dd class="mt-1 font-mono text-2xl tabular-nums text-press">{{ number_format($totals['penulis'], 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-[0.62rem] uppercase tracking-wider text-mute">Rubrik</dt>
                    <dd class="mt-1 font-mono text-2xl tabular-nums text-press">{{ $totals['rubrik'] }}</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- 2. Edisi terbaru --}}
    <section id="edisi" class="border-b border-press/12">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Edisi Terbaru</p>
            <h2 class="mt-2 font-mono text-2xl font-semibold tracking-tight text-press sm:text-3xl">
                @if ($cari) Hasil cari: "{{ $cari }}" @else Tulisan terbaru @endif
            </h2>
            <div class="mt-8">
                @include('editorial.partial.article-cards', ['articles' => $latest])
            </div>
        </div>
    </section>

    {{-- 4. Langganan --}}
    <section id="langganan" class="border-b border-press/12">
        <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Langganan</p>
            <h2 class="mt-3 font-mono text-3xl font-semibold tracking-tight text-press sm:text-4xl">Edaran mingguan, gratis.</h2>
            <p class="mt-3 text-base leading-relaxed text-mute">Satu email tiap Senin. Ringkasan tulisan baru ditambah satu angka keuangan minggu ini. Berhenti kapan saja.</p>

            <form action="{{ route('langganan.store') }}" method="POST" class="mt-8" x-data="{ ok: {{ session('subscribed') ? 'true' : 'false' }} }">
                @csrf
                <div class="flex flex-col gap-3 sm:flex-row">
                    <label for="email" class="sr-only">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="kamu@email.com"
                        required
                        class="flex-1 border-0 border-b border-press/20 bg-transparent py-2 font-mono text-base text-press placeholder:text-mute/70 focus:border-press focus:outline-none @error('email') border-stamp @enderror"
                    />
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-stamp px-6 py-3 font-mono text-sm font-medium uppercase tracking-wider text-ledger transition-colors hover:bg-stamp/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stamp">Langganan</button>
                </div>

                @error('email')
                    <p class="mt-2 font-mono text-sm text-stamp">{{ $message }}</p>
                @enderror

                <p x-show="ok" x-cloak class="mt-3 font-mono text-sm text-stamp">Terdaftar. Cek emailmu untuk edaran pertama.</p>
            </form>
        </div>
    </section>
@endsection