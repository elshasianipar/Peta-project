<?php

use App\Models\kategori as Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public ?Category $category = null;
    public string $name = '';
    // public string $slug = '';

    /** Whether the user has typed in the slug field (stop auto-filling from name). */
    // public bool $slugTouched = false;

    public function mount(?Category $category = null): void
    {
        if ($category && $category->exists) {
            $this->category = $category;
            $this->name = $category->name;
            // $this->slug = $category->slug;
            // $this->slugTouched = true;
        }
    }

    // public function updatedName(string $value): void
    // {
    //     if (! $this->slugTouched) {
    //         $this->slug = Str::slug($value);
    //     }
    // }

    // public function updatedSlug(string $value): void
    // {
    //     $this->slugTouched = trim($value) !== '';
    // }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->category?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            // 'slug' => ['nullable', 'string', 'max:100', Rule::unique('categories', 'slug')->ignore($id)],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        // $data['slug'] = trim($data['slug']) !== '' ? $data['slug'] : Str::slug($data['name']);

        if ($this->category && $this->category->exists) {
            $this->category->update($data);
            $message = 'Kategori diperbarui.';
        } else {
            Category::create($data);
            $message = 'Kategori disimpan.';
        }

        session()->flash('status', $message);
        $this->redirectRoute('admin.kategori.index');
    }
};
?>

<div>
    <form wire:submit="save" class="space-y-4">
        {{-- Nama --}}
        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input
                id="name"
                type="text"
                wire:model.live.debounce.300ms="name"
                class="mt-1 block w-full"
                required
                autofocus
            />
            <x-input-error for="name" :messages="$errors->get('name')" />
        </div>

        {{-- Aksi --}}
        <div class="flex items-center gap-3 pt-2">
            <x-primary-button>
                <span wire:loading.remove wire:target="save">
                    {{ $category && $category->exists ? 'Simpan' : 'Tambah' }}
                </span>
                <span wire:loading wire:target="save">Menyimpan…</span>
            </x-primary-button>
            <a href="{{ route('admin.kategori.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
                Batal
            </a>
        </div>
    </form>
</div>