@extends('layouts.editorial')

@section('title', $article->title . ' — Lembar')
@section('meta_description', $article->excerpt)

@section('content')
    @php
        $code = fn (?string $slug) => $slug ? Illuminate\Support\Str::upper(Illuminate\Support\Str::substr($slug, 0, 3)) : '—';
    @endphp

    <article>
        <header class="border-b border-press/12">
            <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 sm:py-20">
                <p class="font-mono text-xs uppercase tracking-wider text-stamp">
                    @if ($article->category)
                        {{ $code($article->category->slug) }} · <a href="{{ route('kategori.show', $article->category->slug) }}" class="hover:underline">{{ $article->category->name }}</a>
                    @else
                        Tanpa rubrik
                    @endif
                </p>
                <h1 class="mt-4 font-mono text-3xl font-semibold leading-[1.1] tracking-tight text-press sm:text-5xl">{{ $article->title }}</h1>
                <p class="mt-4 text-lg leading-relaxed text-mute">{{ $article->excerpt }}</p>

                <div class="mt-6 border-t border-press/12 pt-4 font-mono text-sm">
                    <p class="text-press">{{ $article->user->name }}</p>
                    <p class="text-mute">{{ $article->created_at->locale('id')->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            @if ($article->thumbnail)
                <img src="{{ $article->thumbnail_url }}" alt="" class="mb-10 aspect-[16/9] w-full rounded-lg object-cover" loading="lazy" />
            @endif

            <div class="prose-article max-w-none">{!! $article->content !!}</div>

            <div class="mt-12 border-t border-press/12 pt-6">
                <a href="{{ route('home') }}" class="font-mono text-sm uppercase tracking-wider text-press transition-colors hover:text-stamp">← Kembali ke edisi</a>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="border-t border-press/12">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Tulisan terkait</p>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $r)
                        <a href="{{ route('artikel.show', $r->slug) }}" class="group block">
                            <img src="{{ $r->thumbnail_url }}" alt="" class="aspect-[16/10] w-full rounded-lg object-cover" loading="lazy" />
                            <p class="mt-3 font-mono text-xs uppercase tracking-wider text-stamp">{{ $code($r->category?->slug) }}</p>
                            <h3 class="mt-1 font-mono text-base font-medium text-press transition-colors group-hover:text-stamp">{{ $r->title }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection