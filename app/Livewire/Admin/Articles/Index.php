<?php

namespace App\Livewire\Admin\Articles;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $cari = '';

    public function updatingCari(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        DB::table('articles')->where('id', $id)->delete();

        session()->flash('status', 'Artikel dihapus.');
    }

    public function render()
    {
        $articles = DB::table('articles')
            ->leftJoin('categories', 'articles.category_id', '=', 'categories.id')
            ->select('articles.*', 'categories.name as category_name')
            ->when($this->cari !== '', fn ($q) => $q->where('articles.title', 'like', "%{$this->cari}%"))
            ->orderByDesc('articles.created_at')
            ->paginate(15);

        return view('livewire.admin.articles.index', compact('articles'));
    }
}