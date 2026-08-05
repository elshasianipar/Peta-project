<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));

        $query = Artikel::with(['category', 'user'])
            ->published()
            ->latest();

        if ($cari !== '') {
            $query->where('title', 'like', "%{$cari}%");
        }

        $latest = $query->paginate(8)->withQueryString();

        $categories = kategori::withCount(['articles' => fn ($q) => $q->published()])
            ->orderByDesc('articles_count')
            ->get();

        $totals = [
            'articles' => Artikel::published()->count(),
            'penulis' => User::where('role', 'author')->count(),
            'rubrik' => $categories->count(),
        ];

        return view('editorial.index', compact('latest', 'categories', 'totals', 'cari'));
    }

    public function show(string $slug): View
    {
        $article = Artikel::with(['category', 'user'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Artikel::with(['category', 'user'])
            ->published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(4)
            ->get();

        return view('editorial.show', compact('article', 'related'));
    }

    public function category(string $slug): View
    {
        $category = kategori::where('slug', $slug)->firstOrFail();

        $articles = Artikel::with(['category', 'user'])
            ->published()
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('editorial.category', compact('category', 'articles'));
    }

    public function archive(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));

        $query = Artikel::with(['category', 'user'])
            ->published()
            ->latest();

        if ($cari !== '') {
            $query->where('title', 'like', "%{$cari}%");
        }

        $articles = $query->paginate(12)->withQueryString();

        return view('editorial.archive', compact('articles', 'cari'));
    }

    public function about(): View
    {
        $penulis = User::orderBy('name')->get();
        // $aboutContent = Setting::get('about_content', Setting::defaultAbout());

        return view('editorial.about', compact('penulis', 'aboutContent'));
    }
}
