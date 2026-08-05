export const StarterKit = {};

export const Image = {
    configure() {
        return this;
    },
};

class CommandChain {
    constructor(editor) {
        this.editor = editor;
    }

    focus() {
        this.editor.focus();
        return this;
    }

    toggleBold() {
        return this;
    }

    toggleItalic() {
        return this;
    }

    toggleStrike() {
        return this;
    }

    toggleHeading({ level }) {
        this.editor._setHeading(level);
        return this;
    }

    toggleBulletList() {
        this.editor._toggleList('ul');
        return this;
    }

    toggleOrderedList() {
        this.editor._toggleList('ol');
        return this;
    }

    toggleBlockquote() {
        this.editor._toggleBlockquote();
        return this;
    }

    toggleCodeBlock() {
        this.editor._toggleCodeBlock();
        return this;
    }

    undo() {
        this.editor._undo();
        return this;
    }

    redo() {
        this.editor._redo();
        return this;
    }

    run() {
        return this;
    }
}

export class Editor {
    constructor({ element, content = '', onUpdate, onSelectionUpdate }) {
        this.element = element;
        this.onUpdate = onUpdate;
        this.onSelectionUpdate = onSelectionUpdate;
        this.isDestroyed = false;

        if (this.element) {
            this.element.contentEditable = 'true';
            this.element.innerHTML = content;
            this.element.addEventListener('input', () => this._emitUpdate());
            this.element.addEventListener('keyup', () => this._emitSelectionUpdate());
            this.element.addEventListener('mouseup', () => this._emitSelectionUpdate());
        }

        this.view = {
            state: {
                schema: {
                    nodes: {
                        image: {
                            create: ({ src, alt }) => ({ type: 'image', attrs: { src, alt } }),
                        },
                    },
                },
                tr: {
                    replaceSelectionWith() {
                        return this;
                    },
                },
                apply(tr) {
                    return { tr };
                },
            },
            updateState() {},
            focus: () => this.focus(),
        };
    }

    chain() {
        return new CommandChain(this);
    }

    focus() {
        if (this.element) {
            this.element.focus();
        }
    }

    getHTML() {
        return this.element ? this.element.innerHTML : '';
    }

    isActive() {
        return false;
    }

    destroy() {
        this.isDestroyed = true;
    }

    _emitUpdate() {
        this.onUpdate?.({ editor: this });
    }

    _emitSelectionUpdate() {
        this.onSelectionUpdate?.();
    }

    _setHeading(level) {
        if (!this.element) {
            return;
        }

        const doc = this.element.ownerDocument;
        doc.execCommand('formatBlock', false, `h${level}`);
        this._emitUpdate();
    }

    _toggleList(tag) {
        if (!this.element) {
            return;
        }

        const doc = this.element.ownerDocument;
        doc.execCommand(tag === 'ul' ? 'insertUnorderedList' : 'insertOrderedList');
        this._emitUpdate();
    }

    _toggleBlockquote() {
        if (!this.element) {
            return;
        }

        const doc = this.element.ownerDocument;
        doc.execCommand('formatBlock', false, 'blockquote');
        this._emitUpdate();
    }

    _toggleCodeBlock() {
        if (!this.element) {
            return;
        }

        const doc = this.element.ownerDocument;
        doc.execCommand('formatBlock', false, 'pre');
        this._emitUpdate();
    }

    _undo() {
        this.element?.ownerDocument.execCommand('undo');
        this._emitUpdate();
    }

    _redo() {
        this.element?.ownerDocument.execCommand('redo');
        this._emitUpdate();
    }
}
