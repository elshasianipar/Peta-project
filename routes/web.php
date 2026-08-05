<?php

use App\Http\Controllers\AdminAboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubriceberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticleController::class, 'index'])->name('home');

Route::get('/artikel', [ArticleController::class, 'archive'])->name('artikel.index');

Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('artikel.show');

Route::get('/about', [ArticleController::class, 'about'])->name('about');

Route::get('/kategori/{slug}', [ArticleController::class, 'category'])->name('kategori.show');

Route::post('/langganan', [SubscriberController::class, 'store'])->name('langganan.store');


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('artikel', 'admin.articles.index')->name('artikel.index');
    Route::livewire('artikel/create', 'admin.articles.create')->name('artikel.create');
    Route::livewire('artikel/{article}/edit', 'admin.articles.edit')->name('artikel.edit');

    // Kategori: CRUD penuh via Livewire (full-page component), tanpa controller.
    Route::livewire('kategori', 'kategori.index')->name('kategori.index');
    Route::livewire('kategori/create', 'kategori.create')->name('kategori.create');
    Route::livewire('kategori/{category}/edit', 'kategori.edit')->name('kategori.edit');

    Route::get('about', [AdminAboutController::class, 'edit'])->name('about.edit');
    Route::put('about', [AdminAboutController::class, 'update'])->name('about.update');

    Route::get('about', [AdminAboutController::class, 'edit'])->name('about.edit');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

