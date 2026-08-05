<?php

namespace App\Livewire\Admin\Articles;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    /** ID artikel yang sedang disunting (null saat membuat baru). */
    public ?int $articleId = null;

    public string $title = '';

    public string $slug = '';

    public string $content = '';

    /** Path/URL gambar yang sudah tersimpan (untuk tampil saat edit). */
    public string $thumbnail = '';

    /** Unggahan baru dari input file. */
    public $thumbnailFile = null;

    public ?int $categoryId = null;

    public string $status = 'draft';

    /** Stop auto-filling slug from title once the user edits it. */
    public bool $slugTouched = false;

    /** Unggahan gambar sisipan di dalam konten (sementara; langsung disimpan oleh saveContentImage). */
    public $contentImage = null;

    /**
     * @param  object|null  $article  baris dari `DB::table('articles')` saat edit, null saat buat.
     */
    public function mount($article = null): void
    {
        if ($article) {
            $this->articleId = (int) $article->id;
            $this->title = $article->title;
            $this->slug = $article->slug;
            $this->content = $article->content ?? '';
            $this->thumbnail = $article->thumbnail ?? '';
            $this->categoryId = $article->category_id !== null ? (int) $article->category_id : null;
            $this->status = $article->status;
            $this->slugTouched = true;
        }
    }

    /** Pesan validasi kustom untuk unggahan gambar. */
    public function messages(): array
    {
        return [
            'thumbnailFile.image' => 'File harus berupa gambar.',
            'thumbnailFile.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->slugTouched) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug(string $value): void
    {
        $this->slugTouched = trim($value) !== '';
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($this->articleId)],
            'content' => ['required', 'string'],
            'thumbnailFile' => ['nullable', 'image', 'max:2048'],
            'categoryId' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:draft,published'],
        ];
    }

    /**
     * Hook `wire:model="contentImage"`: simpan gambar sisipan editor, lalu kirim
     * event browser `content-image-inserted` (berisi URL) yang ditangkap Alpine
     * untuk menyisipkan `<img>` ke TipTap. URL root-relatif biar portabel di front-end.
     */
    public function updatedContentImage(): void
    {
        $this->validateOnly('contentImage', ['contentImage' => ['required', 'image', 'max:5120']]);

        $path = $this->contentImage->store('content-images', 'public');
        $this->reset('contentImage');

        $this->dispatch('content-image-inserted', url: Storage::disk('public')->url($path));
    }

    public function save(): void
    {
        $data = $this->validate();

        $payload = [
            'title' => $data['title'],
            'slug' => trim($data['slug']) !== '' ? $data['slug'] : Str::slug($data['title']),
            'content' => $data['content'],
            'category_id' => $data['categoryId'],
            'status' => $data['status'],
        ];

        // Thumbnail: unggahan baru menimpa yang lama; tanpa unggahan, pertahankan yang ada.
        if ($this->thumbnailFile) {
            $payload['thumbnail'] = $this->thumbnailFile->store('thumbnails', 'public');
        } else {
            $payload['thumbnail'] = $this->thumbnail !== '' ? $this->thumbnail : null;
        }

        if ($this->articleId) {
            // Query builder tidak memperbarui timestamp otomatis.
            $payload['updated_at'] = now();
            DB::table('articles')->where('id', $this->articleId)->update($payload);
            $message = 'Artikel diperbarui.';
        } else {
            $payload['user_id'] = auth()->id();
            $payload['created_at'] = now();
            $payload['updated_at'] = now();
            DB::table('articles')->insert($payload);
            $message = 'Artikel disimpan.';
        }

        session()->flash('status', $message);
        $this->redirectRoute('admin.artikel.index');
    }

    public function render()
    {
        $categories = DB::table('categories')->orderBy('name')->get();

        return view('livewire.admin.articles.form', compact('categories'));
    }
}
