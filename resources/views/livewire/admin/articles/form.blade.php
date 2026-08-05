@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    $thumbSrc = $thumbnail !== '' ? (str_starts_with($thumbnail, 'http') ? $thumbnail : asset('storage/'.$thumbnail)) : null;
@endphp

<div>
    <form wire:submit="save" class="space-y-4">
        {{-- Judul --}}
        <div>
            <x-input-label for="title" value="Judul" />
            <x-text-input id="title" type="text" wire:model.live.debounce.400ms="title" class="mt-1 block w-full" required autofocus />
            <x-input-error for="title" :messages="$errors->get('title')" />
        </div>

        {{-- Slug (ikut judul, bisa diedit manual) --}}
        <div>
            <x-input-label for="slug" value="Slug" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Otomatis dari judul. Bisa diedit manual.</p>
            <x-text-input id="slug" type="text" wire:model.live.debounce.400ms="slug" class="mt-1 block w-full font-mono" />
            @if (trim($slug) !== '')
                <p class="mt-1 truncate text-xs text-gray-400 dark:text-gray-500">
                    {{ route('artikel.show', $slug) }}
                </p>
            @endif
            <x-input-error for="slug" :messages="$errors->get('slug')" />
        </div>

        {{-- Kategori + Status --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="category_id" value="Kategori" />
                <select id="category_id" wire:model.change="categoryId" class="{{ $input }}">
                    <option value="">— Tanpa kategori —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="categoryId" :messages="$errors->get('categoryId')" />
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <select id="status" wire:model.change="status" class="{{ $input }}">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
                <x-input-error for="status" :messages="$errors->get('status')" />
            </div>
        </div>

        {{-- Thumbnail (file gambar) --}}
        <div>
            <x-input-label for="thumbnailFile" value="Thumbnail" />
            <input
                id="thumbnailFile"
                type="file"
                wire:model="thumbnailFile"
                accept="image/*"
                class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-indigo-500"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gambar, maksimal 2 MB.</p>
            <x-input-error for="thumbnailFile" :messages="$errors->get('thumbnailFile')" />

            {{-- Pratinjau: unggahan baru atau gambar yang sudah ada --}}
            <div class="mt-3">
                @if ($thumbnailFile)
                    <img src="{{ $thumbnailFile->temporaryUrl() }}" alt="Pratinjau thumbnail" class="h-32 w-auto rounded-md border border-gray-300 dark:border-gray-700 object-cover" />
                @elseif ($thumbSrc)
                    <img src="{{ $thumbSrc }}" alt="Thumbnail saat ini" class="h-32 w-auto rounded-md border border-gray-300 dark:border-gray-700 object-cover" />
                    <button type="button" wire:click="$set('thumbnail', '')" class="mt-1 text-xs text-red-600 hover:underline">Hapus thumbnail saat ini</button>
                @endif
            </div>
        </div>

        {{-- Konten (TipTap) --}}
        <div>
            <x-input-label value="Konten" />

            <div x-data="tiptapEditor" wire:ignore x-on:content-image-inserted.window="insertImageFromEvent($event)" class="mt-1">
                {{-- Toolbar --}}
                <div class="flex flex-wrap gap-1 rounded-t-md border border-gray-300 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-900">
                    <button type="button" @click="toggleBold()" class="tiptap-btn font-bold" :class="{ 'is-active': bold }">B</button>
                    <button type="button" @click="toggleItalic()" class="tiptap-btn italic" :class="{ 'is-active': italic }">I</button>
                    <button type="button" @click="toggleStrike()" class="tiptap-btn line-through" :class="{ 'is-active': strike }">S</button>
                    <span class="mx-1 w-px self-stretch bg-gray-300 dark:bg-gray-700"></span>
                    <button type="button" @click="toggleH1()" class="tiptap-btn" :class="{ 'is-active': h1 }">H1</button>
                    <button type="button" @click="toggleH2()" class="tiptap-btn" :class="{ 'is-active': h2 }">H2</button>
                    <span class="mx-1 w-px self-stretch bg-gray-300 dark:bg-gray-700"></span>
                    <button type="button" @click="toggleBulletList()" class="tiptap-btn" :class="{ 'is-active': bulletList }">• List</button>
                    <button type="button" @click="toggleOrderedList()" class="tiptap-btn" :class="{ 'is-active': orderedList }">1. List</button>
                    <button type="button" @click="toggleBlockquote()" class="tiptap-btn" :class="{ 'is-active': blockquote }">"</button>
                    <button type="button" @click="toggleCodeBlock()" class="tiptap-btn font-mono" :class="{ 'is-active': codeBlock }">&lt;/&gt;</button>
                    <span class="mx-1 w-px self-stretch bg-gray-300 dark:bg-gray-700"></span>
                    <button type="button" @click="pickImage()" class="tiptap-btn" title="Sisipkan gambar">Gambar</button>
                    <span class="mx-1 w-px self-stretch bg-gray-300 dark:bg-gray-700"></span>
                    <button type="button" @click="undo()" class="tiptap-btn">↶</button>
                    <button type="button" @click="redo()" class="tiptap-btn">↷</button>
                </div>

                {{-- Area editor (wire:ignore di induk x-data melindungi seluruh
                     komponen Alpine + DOM ProseMirror dari morph Livewire saat re-render) --}}
                <div x-ref="editor" data-placeholder="Tulis artikel di sini…" class="tiptap min-h-[14rem] rounded-b-md border border-t-0 border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"></div>
            </div>

            {{-- Input file tersembunyi: ditaruh DI LUAR wire:ignore agar wire:model
                 di-track Livewire normal. Alpine membukanya via id, bukan x-ref. --}}
            <input type="file" id="contentImageInput" wire:model="contentImage" accept="image/*" class="hidden" />

            <x-input-error for="content" :messages="$errors->get('content')" />
        </div>

        {{-- Aksi --}}
        <div class="flex items-center gap-3 pt-2">
            <x-primary-button>
                <span wire:loading.remove wire:target="save">{{ $articleId ? 'Simpan' : 'Terbitkan' }}</span>
                <span wire:loading wire:target="save">Menyimpan…</span>
            </x-primary-button>
            <a href="{{ route('admin.artikel.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Batal</a>
        </div>
    </form>
</div>