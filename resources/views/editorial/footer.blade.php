@php
    $menu = [
        ['label' => 'Anggaran', 'slug' => 'anggaran'],
        ['label' => 'Pasar', 'slug' => 'pasar'],
        ['label' => 'Rupiah', 'slug' => 'rupiah'],
        ['label' => 'Investasi', 'slug' => 'investasi'],
        ['label' => 'Keseharian', 'slug' => 'keseharian'],
    ];
@endphp

<footer class="border-t border-press/12 bg-ledger">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid gap-8 sm:grid-cols-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-stamp"></span>
                    <span class="font-mono text-[1.05rem] font-semibold tracking-tight text-press">Lembar</span>
                </div>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-mute">Uang, ditulis pelan-pelan. Edaran keuangan untuk anak muda Indonesia.</p>
            </div>

            <div>
                <p class="font-mono text-[0.62rem] uppercase tracking-wider text-mute">Edisi</p>
                <ul class="mt-3 space-y-2 font-mono text-sm">
                    @foreach ($menu as $m)
                        <li><a href="{{ route('kategori.show', $m['slug']) }}" class="text-press transition-colors hover:text-stamp">{{ $m['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="font-mono text-[0.62rem] uppercase tracking-wider text-mute">Ikuti</p>
                <ul class="mt-3 space-y-2 font-mono text-sm">
                    <li><a href="#langganan" class="text-press transition-colors hover:text-stamp">Edaran mingguan</a></li>
                    <li><a href="#" class="text-mute transition-colors hover:text-press">Twitter / X</a></li>
                    <li><a href="#" class="text-mute transition-colors hover:text-press">Instagram</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-2 border-t border-press/12 pt-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="font-mono text-[0.62rem] uppercase tracking-wider text-mute">© {{ date('Y') }} Lembar</p>
            <p class="font-mono text-[0.62rem] uppercase tracking-wider text-mute/70">Seluruh artikel &amp; data adalah contoh/dummy untuk keperluan skripsi.</p>
        </div>
    </div>
</footer>