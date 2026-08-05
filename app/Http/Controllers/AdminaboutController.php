<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminAboutController extends Controller
{
    /**
     * Tampilkan halaman "Tentang" ke publik.
     * Route: Route::get('/tentang', [AdminAboutController::class, 'index'])->name('about');
     */
    public function index(): View
    {
        $aboutContent = Setting::where('key', 'about_content')->value('value') ?? '';

        $penulis = User::where('is_penulis', true)
            ->orderBy('name')
            ->get();

        return view('pages.tentang', [
            'aboutContent' => $aboutContent,
            'penulis' => $penulis,
        ]);
    }

    /**
     * Tampilkan form edit di admin panel.
     * Route: Route::get('/admin/about', [AdminAboutController::class, 'edit'])->name('admin.about.edit');
     */
    public function edit(): View
    {
        $aboutContent = Setting::where('key', 'about_content')->value('value') ?? '';

        return view('admin.about.edit', [
            'aboutContent' => $aboutContent,
        ]);
    }

    /**
     * Simpan perubahan konten "Tentang" dari admin panel.
     * Route: Route::put('/admin/about', [AdminAboutController::class, 'update'])->name('admin.about.update');
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'about_content' => ['required', 'string'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'about_content'],
            ['value' => $validated['about_content']]
        );

        return redirect()
            ->route('admin.about.edit')
            ->with('status', 'Konten "Tentang" berhasil diperbarui.');
    }
}