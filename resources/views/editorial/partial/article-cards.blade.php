@props(['articles'])

@php
    $code = fn (?string $slug) => $slug ? Illuminate\Support\Str::upper(Illuminate\Support\Str::substr($slug, 0, 3)) : '—';
@endphp

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($articles as $a)
        <a href="{{ route('artikel.show', $a->slug) }}" class="group flex flex-col overflow-hidden rounded-lg border border-press/12 bg-ledger transition-colors hover:border-press/30">
            @if (! empty($a->thumbnail_url))
                <img src="{{ $a->thumbnail_url }}" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy" />
            @endif
            <div class="flex flex-1 flex-col p-5">
                <p class="font-mono text-xs uppercase tracking-wider text-stamp">{{ $code($a->category?->slug) }}@if ($a->category) · {{ $a->category->name }}@endif</p>
                <h3 class="mt-2 font-mono text-lg font-medium leading-snug text-press transition-colors group-hover:text-stamp">{{ $a->title }}</h3>
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-mute">{{ $a->excerpt }}</p>
                <p class="mt-4 font-mono text-[0.62rem] uppercase tracking-wider text-mute">{{ $a->user->name }} · {{ $a->created_at->locale('id')->translatedFormat('d M Y') }}</p>
            </div>
        </a>
    @endforeach
</div>

{{-- @if (method_exists($articles, 'links'))
    <div class="mt-8">{{ $articles->links() }}</div>
@endif --}}