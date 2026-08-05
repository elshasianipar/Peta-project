<x-slot:header>
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Artikel</h2>
        <a href="{{ route('admin.artikel.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">+ Tulis artikel</a>
    </div>
</x-slot:header>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        {{-- Cari --}}
        <div class="mb-4 max-w-md">
            <label for="cari-artikel" class="sr-only">Cari judul</label>
            <input
                id="cari-artikel"
                type="search"
                wire:model.live.debounce.300ms="cari"
                placeholder="Cari judul…"
                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Tanggal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($articles as $a)
                            @php
                                $badge = $a->status === 'published'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <tr wire:key="artikel-{{ $a->id }}">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('admin.artikel.edit', $a->id) }}" class="font-medium hover:underline">{{ $a->title }}</a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $a->category_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ $a->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ date('d M Y', strtotime((string) $a->created_at)) }}</td>
                                <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                    <a href="{{ route('admin.artikel.edit', $a->id) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Edit</a>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $a->id }})"
                                        wire:confirm="Hapus artikel &quot;{{ $a->title }}&quot;?"
                                        class="text-red-600 hover:underline dark:text-red-400 ms-3"
                                    >Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @if ($cari !== '')
                                        Tidak ada artikel untuk "{{ $cari }}".
                                    @else
                                        Belum ada artikel. <a href="{{ route('admin.artikel.create') }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Tulis sekarang</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $articles->links() }}</div>
    </div>
</div>