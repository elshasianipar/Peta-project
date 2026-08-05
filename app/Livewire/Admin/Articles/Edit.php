<?php

namespace App\Livewire\Admin\Articles;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Edit extends Component
{
    public int $articleId;

    public function mount($article): void
    {
        // Tanpa type-hint model → `{article}` dari rute adalah ID mentah, bukan binding model.
        $this->articleId = (int) $article;
    }

    public function render()
    {
        $article = DB::table('articles')->where('id', $this->articleId)->first();

        abort_if(! $article, 404);

        return view('livewire.admin.articles.edit', ['article' => $article]);
    }
}
