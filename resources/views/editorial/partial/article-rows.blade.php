@props(['articles'])

@php
    $code = fn (?string $slug) => $slug ? Illuminate\Support\Str::upper(Illuminate\Support\Str::substr($slug, 0, 3)) : '—';
@endphp

<ul class="divide-y divide-press/12 border-y border-press/12">
    @foreach ($articles as $a)
        @php
            $date = $a->created_at->locale('id')->translatedFormat('d M y');
        @endphp
        <li class="group grid gap-1 py-5 sm:grid-cols-[9ch_4ch_1fr] sm:items-baseline sm:gap-x-6">
            <div class="flex items-baseline gap-3 sm:contents">
                <time datetime="{{ $a->created_at->toDateString() }}" class="font-mono text-xs tabular-nums text-mute sm:col-start-1">{{ $date }}</time>
                <span class="font-mono text-xs uppercase tracking-wider text-stamp sm:col-start-2">{{ $code($a->category?->slug) }}</span>
            </div>

            <div class="sm:col-start-3">
                <a href="{{ route('artikel.show', $a->slug) }}" class="font-mono text-base font-medium text-press transition-colors group-hover:text-stamp">{{ $a->title }}</a>
                <p class="mt-1 max-w-2xl text-sm leading-relaxed text-mute">{{ $a->excerpt }}</p>
            </div>
        </li>
    @endforeach
</ul>

@if (method_exists($articles, 'links'))
    <div class="mt-6">{{ $articles->links() }}</div>
@endif