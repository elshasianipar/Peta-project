@extends('layouts.editorial')

@section('title', $category->name . ' — Lembar')
@section('meta_description', 'Tulisan Lembar di rubrik ' . $category->name . ': uang, ditulis pelan-pelan.')

@section('content')
    @php
        use Illuminate\Support\Str;
        use App\Models\Category;

        $code   = Str::upper(Str::substr($category->slug, 0, 3));
        $onFirst = $articles->onFirstPage();
        $lead    = $onFirst ? $articles->first() : null;
        $rest    = $articles->getCollection()->slice($lead ? 1 : 0)->values();
        $rowCode = fn (?string $slug) => $slug ? Str::upper(Str::substr($slug, 0, 3)) : '—';
        $tanggal = fn ($d) => $d->locale('id')->translatedFormat('d M Y');

        $siblings = Category::withCount(['articles' => fn ($q) => $q->published()])
            ->where('id', '!=', $category->id)
            ->orderByDesc('articles_count')
            ->limit(6)
            ->get();
    @endphp

    {{-- 1. Masthead section front --}}
    <section class="relative overflow-hidden border-b border-press/12">
        {{-- folio mark: the section's 3-letter code, amplified --}}
        <span aria-hidden="true" class="pointer-events-none absolute -right-2 bottom-[-0.18em] select-none font-mono text-[8.5rem] font-bold leading-none tracking-tighter text-stamp/15 sm:text-[13rem]">
            {{ $code }}
        </span>

        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">
                Rubrik · <span class="text-stamp">{{ $code }}</span>
            </p>
            <h1 class="mt-4 max-w-2xl font-mono text-4xl font-semibold leading-[1.04] tracking-tight text-press sm:text-6xl">
                {{ $category->name }}
            </h1>
            <p class="mt-4 max-w-lg text-base leading-relaxed text-mute sm:text-lg">
                {{ number_format($articles->total(), 0, ',', '.') }} tulisan di rubrik ini, ditulis pelan-pelan.
            </p>

            @if ($lead)
                <p class="mt-6 border-t border-press/12 pt-4 font-mono text-[0.62rem] uppercase tracking-wider text-mute">
                    Diperbarui {{ $tanggal($lead->created_at) }}
                </p>
            @endif
        </div>
    </section>

    @if ($articles->isEmpty())
        {{-- Empty: directed, not moody --}}
        <section class="border-b border-press/12">
            <div class="mx-auto max-w-6xl px-4 py-20 text-center sm:px-6">
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Rubrik kosong</p>
                <p class="mx-auto mt-4 max-w-md font-mono text-2xl font-semibold tracking-tight text-press">
                    Belum ada tulisan di rubrik {{ $category->name }}.
                </p>
                <p class="mt-3 text-base text-mute">Edisi berikutnya sedang ditulis. Sementara itu, baca yang terbaru.</p>
                <a href="{{ route('home') }}" class="mt-8 inline-flex items-center justify-center rounded-md bg-stamp px-6 py-3 font-mono text-sm font-medium uppercase tracking-wider text-ledger transition-colors hover:bg-stamp/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stamp">
                    Ke edisi terbaru
                </a>
            </div>
        </section>
    @else
        {{-- 2. Lead story (page 1 only) --}}
        @if ($lead)
            <section class="border-b border-press/12">
                <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Tulisan terbaru</p>
                    <a href="{{ route('artikel.show', $lead->slug) }}" class="group mt-6 grid gap-8 sm:grid-cols-2 sm:gap-12">
                        <div class="overflow-hidden rounded-lg border border-press/12 bg-ledger">
                            @if ($lead->thumbnail)
                                <img src="{{ $lead->thumbnail_url }}" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy" />
                            @else
                                <div class="flex aspect-[16/10] w-full items-center justify-center bg-press/5 font-mono text-6xl font-bold tracking-tighter text-stamp/25">{{ $code }}</div>
                            @endif
                        </div>
                        <div class="flex flex-col justify-center">
                            <p class="font-mono text-xs uppercase tracking-wider text-stamp">{{ $code }} · {{ $category->name }}</p>
                            <h2 class="mt-3 font-mono text-2xl font-semibold leading-tight tracking-tight text-press transition-colors group-hover:text-stamp sm:text-4xl">
                                {{ $lead->title }}
                            </h2>
                            <p class="mt-4 max-w-xl text-base leading-relaxed text-mute">{{ $lead->excerpt }}</p>
                            <p class="mt-6 font-mono text-[0.62rem] uppercase tracking-wider text-mute">
                                {{ $lead->user->name }} · {{ $tanggal($lead->created_at) }}
                            </p>
                        </div>
                    </a>
                </div>
            </section>
        @endif

        {{-- 3. The rest: dense dated rows --}}
        <section class="border-b border-press/12">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                @if ($lead)
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Tulisan lain di rubrik ini</p>
                @endif

                <ul class="mt-6 divide-y divide-press/12 border-y border-press/12">
                    @foreach ($rest as $a)
                        <li class="group grid gap-1 py-5 sm:grid-cols-[9ch_4ch_1fr] sm:items-baseline sm:gap-x-6">
                            <div class="flex items-baseline gap-3 sm:contents">
                                <time datetime="{{ $a->created_at->toDateString() }}" class="font-mono text-xs tabular-nums text-mute sm:col-start-1">
                                    {{ $a->created_at->locale('id')->translatedFormat('d M y') }}
                                </time>
                                <span class="font-mono text-xs uppercase tracking-wider text-stamp sm:col-start-2">{{ $rowCode($a->category?->slug) }}</span>
                            </div>
                            <div class="sm:col-start-3">
                                <a href="{{ route('artikel.show', $a->slug) }}" class="font-mono text-base font-medium text-press transition-colors group-hover:text-stamp">{{ $a->title }}</a>
                                <p class="mt-1 max-w-2xl text-sm leading-relaxed text-mute">{{ $a->excerpt }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">{{ $articles->links() }}</div>
            </div>
        </section>

        {{-- 4. Rubrik lain: cross-navigation to sibling sections --}}
        @if ($siblings->isNotEmpty())
            <section class="border-b border-press/12">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-mute">Rubrik lain</p>
                    <h2 class="mt-2 font-mono text-2xl font-semibold tracking-tight text-press sm:text-3xl">Bagian lain di Lembar</h2>
                    <ul class="mt-8 grid gap-px overflow-hidden border border-press/12 bg-press/12 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($siblings as $s)
                            <li>
                                <a href="{{ route('kategori.show', $s->slug) }}" class="group flex h-full items-baseline justify-between gap-4 bg-ledger px-5 py-5 transition-colors hover:bg-press/5">
                                    <span>
                                        <span class="font-mono text-[0.62rem] uppercase tracking-wider text-stamp">{{ $rowCode($s->slug) }}</span>
                                        <span class="mt-1 block font-mono text-base font-medium text-press transition-colors group-hover:text-stamp">{{ $s->name }}</span>
                                    </span>
                                    <span class="shrink-0 font-mono text-sm tabular-nums text-mute">{{ number_format($s->articles_count, 0, ',', '.') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif
    @endif
@endsection