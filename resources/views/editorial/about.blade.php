@extends('layouts.editorial')

@section('title', 'Tentang — Lembar')
@section('meta_description', 'Tentang Lembar: publikasi keuangan untuk anak muda Indonesia, ditulis pelan-pelan.')

@section('content')
    <section class="border-b border-press/12">
        <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Tentang</p>
            <h1 class="mt-3 font-mono text-4xl font-semibold leading-[1.05] tracking-tight text-press sm:text-5xl">Uang, ditulis pelan-pelan.</h1>
            <div class="prose-article mt-8 max-w-none">
                {!! $aboutContent !!}
            </div>
        </div>
    </section>

    <section class="border-b border-press/12">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Tim redaksi</p>
            <h2 class="mt-2 font-mono text-2xl font-semibold tracking-tight text-press sm:text-3xl">Penulis</h2>
            <ul class="mt-8 divide-y divide-press/12 border-y border-press/12">
                @foreach ($penulis as $p)
                    <li class="group flex items-baseline justify-between gap-4 py-4">
                        <span class="font-mono text-lg text-press transition-colors group-hover:text-stamp">{{ $p->name }}</span>
                        <span class="font-mono text-xs uppercase tracking-wider text-stamp">{{ $p->isAdmin() ? 'Redaksi' : 'Penulis' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section id="langganan" class="border-b border-press/12">
        <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Langganan</p>
            <h2 class="mt-3 font-mono text-3xl font-semibold tracking-tight text-press sm:text-4xl">Edaran mingguan, gratis.</h2>
            <p class="mt-3 text-base leading-relaxed text-mute">Satu email tiap Senin. Ringkasan tulisan baru ditambah satu angka keuangan minggu ini.</p>
            <div class="mt-6">
                <a href="{{ route('home') }}#langganan" class="inline-flex items-center justify-center rounded-md bg-stamp px-6 py-3 font-mono text-sm font-medium uppercase tracking-wider text-ledger transition-colors hover:bg-stamp/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stamp">Langganan edaran</a>
            </div>
        </div>
    </section>
@endsection