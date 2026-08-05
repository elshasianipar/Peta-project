@extends('layouts.editorial')

@section('title', 'Arsip tulisan — Lembar')
@section('meta_description', 'Semua tulisan Lembar: anggaran, pasar, rupiah, dan investasi untuk anak muda Indonesia.')

@section('content')
    @php
        $code = fn (?string $slug) => $slug ? Illuminate\Support\Str::upper(Illuminate\Support\Str::substr($slug, 0, 3)) : '—';
    @endphp

    <section class="border-b border-press/12">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Arsip</p>
            <h1 class="mt-2 font-mono text-3xl font-semibold tracking-tight text-press sm:text-4xl">
                @if ($cari) Hasil cari: "{{ $cari }}" @else Semua tulisan @endif
            </h1>
            <p class="mt-3 max-w-xl text-base leading-relaxed text-mute">{{ $articles->total() }} tulisan, diurutkan dari yang terbaru.</p>

            <form action="{{ route('artikel.index') }}" method="GET" role="search" class="mt-6 max-w-md">
                <label for="cari-arsip" class="sr-only">Cari artikel</label>
                <input id="cari-arsip" type="search" name="cari" value="{{ $cari }}" placeholder="Cari judul…" class="w-full border-0 border-b border-press/20 bg-transparent py-2 font-mono text-base text-press placeholder:text-mute/70 focus:border-press focus:outline-none" />
            </form>
        </div>
    </section>

    <section class="border-b border-press/12">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            @include('editorial.partials.article-cards', ['articles' => $articles])
        </div>
    </section>
@endsection