import { Editor, StarterKit, Image } from './tiptap-fallback.js';

// TipTap + Livewire: pakai instance Alpine milik Livewire (yang menyediakan
// magic `$wire`). JANGAN import/start Alpine sendiri — Livewire sudah membungkus
// Alpine-nya sendiri, jadi membuat instance kedua hanya memunculkan error
// "Detected multiple instances of Alpine" dan `$wire is not defined`.
// Lihat: https://livewire.laravel.com/docs/4.x/troubleshooting#multiple-instances-of-alpine
document.addEventListener('alpine:init', () => {
    window.Alpine.data('tiptapEditor', () => ({
        editor: null,

        // URL gambar yang menunggu untuk disisipkan setelah editor siap.
        // Menyimpan ke state reaktif memungkinkan kita menjeda sampai transaksi
        // Livewire/Alpine selesai, menghindari "Applying a mismatched transaction".
        pendingImageUrl: null,

        // Status tombol toolbar (reaktif terhadap seleksi editor).
        // Catatan: nama STATE sengaja dipisah dari nama METHOD. Jika sama,
        // syncActive() yang menulis `this.bold = ...` akan menimpa method-nya
        // sehingga `@click="bold()"` malah memanggil boolean → error.
        bold: false,
        italic: false,
        strike: false,
        h1: false,
        h2: false,
        bulletList: false,
        orderedList: false,
        blockquote: false,
        codeBlock: false,

        init() {
            this.editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit,
                    Image.configure({ allowBase64: true }),
                ],
                content: this.$wire.content || '',
                onUpdate: ({ editor }) => {
                    this.$wire.content = editor.getHTML();
                    this.syncActive();
                },
                onSelectionUpdate: () => {
                    this.syncActive();
                },
            });

            this.syncActive();
        },

        syncActive() {
            if (!this.editor) {
                return;
            }

            this.bold = this.editor.isActive('bold');
            this.italic = this.editor.isActive('italic');
            this.strike = this.editor.isActive('strike');
            this.h1 = this.editor.isActive('heading', { level: 1 });
            this.h2 = this.editor.isActive('heading', { level: 2 });
            this.bulletList = this.editor.isActive('bulletList');
            this.orderedList = this.editor.isActive('orderedList');
            this.blockquote = this.editor.isActive('blockquote');
            this.codeBlock = this.editor.isActive('codeBlock');
        },

        // ── Aksi toolbar ──────────────────────────────────────────────
        toggleBold() { this.editor?.chain().focus().toggleBold().run(); },
        toggleItalic() { this.editor?.chain().focus().toggleItalic().run(); },
        toggleStrike() { this.editor?.chain().focus().toggleStrike().run(); },
        toggleH1() { this.editor?.chain().focus().toggleHeading({ level: 1 }).run(); },
        toggleH2() { this.editor?.chain().focus().toggleHeading({ level: 2 }).run(); },
        toggleBulletList() { this.editor?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { this.editor?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote() { this.editor?.chain().focus().toggleBlockquote().run(); },
        toggleCodeBlock() { this.editor?.chain().focus().toggleCodeBlock().run(); },
        undo() { this.editor?.chain().focus().undo().run(); },
        redo() { this.editor?.chain().focus().redo().run(); },

        // ── Sisipkan gambar ────────────────────────────────────────────
        // Tombol "Gambar" hanya membuka input file tersembunyi. Unggahan
        // ditangani Livewire (wire:model="contentImage"); setelah disimpan,
        // server mengirim event `content-image-inserted` berisi URL gambar,
        // yang ditangkap insertImageFromEvent() untuk menyisipkan ke editor.
        // Input file sengaja ditaruh DI LUAR div x-data (lihat form.blade.php) agar
        // wire:model di-track Livewire normal; jadi akses via id, bukan x-ref.
        pickImage() {
            const input = document.getElementById('contentImageInput');
            if (input) {
                input.value = '';
                input.click();
            }
        },

        insertImageFromEvent(event) {
            // Livewire dispatch('…', url: $u) → event.detail = { url: '…' }.
            // Cadangan: kalau detail berupa string mentah, pakai langsung.
            const detail = event?.detail;
            const url = (detail && typeof detail === 'object') ? detail.url : detail;

            const input = document.getElementById('contentImageInput');
            if (input) {
                input.value = ''; // bolehkan memilih file yang sama lagi
            }

            if (!url || !this.editor || this.editor.isDestroyed) {
                console.warn('[tiptap] Cannot insert image: missing URL or editor destroyed', { url, editor: this.editor });
                return;
            }

            // Tunda ke tick berikutnya agar siklus respons Livewire/Alpine
            // benar-benar selesai dan state ProseMirror stabil sebelum kita
            // menyisipkan. Memanggil setImage di tengah siklus Livewire
            // memunculkan "Applying a mismatched transaction" karena state
            // editor berganti antara transaksi dibuat dan didispatch.
            console.log('[tiptap] Queuing image for insert:', url);
            this.pendingImageUrl = url;

            setTimeout(() => this.insertPendingImage(url), 0);
        },

        insertPendingImage(url) {
            // Cegah double-dispatch bila beberapa event ter-stack.
            if (this.pendingImageUrl !== url) {
                return;
            }

            if (!this.editor || this.editor.isDestroyed) {
                this.pendingImageUrl = null;
                return;
            }

            // PENTING: jangan pakai editor.chain()/commands/view.dispatch() di
            // sini. Alur ini berjalan SETELAH siklus upload Livewire. TipTap v3
            // `dispatchTransaction` memvalidasi transaksi dengan
            // `tr.before.eq(this.doc)` di mana `this.state` dibaca ulang saat
            // dispatch — dan state itu sudah berganti/bergeser pasca-Livewire,
            // sehingga doc berbeda → "Applying a mismatched transaction".
            //
            // Solusi: bypass dispatchTransaction TipTap sepenuhnya. Terapkan
            // transaksi ke state yang TANGKAP sendiri (`state.apply(tr)` —
            // memvalidasi `tr.before` terhadap state yang sama, dijamin cocok),
            // lalu tulis hasilnya kembali via `view.updateState(newState)`.
            // Karena tidak ada re-read state current, mismatch tidak mungkin.
            // Efek samping onUpdate (sinkron `$wire.content` + syncActive) kita
            // panggil manual karena `updateState` langsung tak memicu emit TipTap.
            try {
                const view = this.editor.view;
                const state = view.state;
                const imageNode = state.schema.nodes.image.create({ src: url, alt: '' });
                const tr = state.tr.replaceSelectionWith(imageNode, false);
                const newState = state.apply(tr);

                view.updateState(newState);
                this.$wire.content = this.editor.getHTML();
                this.syncActive();
                view.focus();

                console.log('[tiptap] Image inserted:', url);
                console.log('[tiptap] post-insert HTML:', this.editor.getHTML());
            } catch (error) {
                console.error('[tiptap] Failed to insert image:', error);
            } finally {
                this.pendingImageUrl = null;
            }
        },
    }));
});