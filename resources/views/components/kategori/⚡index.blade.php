<?php

use App\Models\kategori;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url]
    public string $cari = '';

    /** @return \Illuminate\Support\Collection<int, Category> */
    #[Computed]
    public function categories()
    {
        return kategori::withCount(['articles' => fn ($q) => $q->published()])
            ->when($this->cari !== '', fn ($q) => $q->where('name', 'like', "%{$this->cari}%"))
            ->orderBy('name')
            ->get();
    }

    public function delete(kategori $category): void
    {
        $category->delete();

        unset($this->categories); // segarkan daftar setelah hapus

        session()->flash('status', 'Kategori dihapus.');
    }
};
?>

<div>
    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    {{-- Cari --}}
    <div class="mb-4">
        <label for="cari-kategori" class="sr-only">Cari kategori</label>
        <input
            id="cari-kategori"
            type="search"
            wire:model.live.debounce.300ms="cari"
            placeholder="Cari kategori…"
            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        />
    </div>

    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Tulisan</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($this->categories as $c)
                        <tr wire:key="kategori-{{ $c->id }}">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $c->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $c->slug }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 tabular-nums">{{ $c->articles_count }}</td>
                            <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                <a href="{{ route('admin.kategori.edit', $c) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Edit</a>
                                <button
                                    type="button"
                                    wire:click="delete({{ $c->id }})"
                                    wire:confirm="Hapus kategori \"{{ $c->name }}\"? Tulisan di dalamnya tidak ikut terhapus."
                                    class="text-red-600 hover:underline dark:text-red-400 ms-3"
                                >Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                @if ($cari !== '')
                                    Tidak ada kategori untuk "{{ $cari }}".
                                @else
                                    Belum ada kategori. <a href="{{ route('admin.kategori.create') }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Tambah sekarang</a>.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>