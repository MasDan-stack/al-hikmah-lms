<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminBlogCategoryController extends Controller
{
    public function index(): View
    {
        $categories = BlogCategory::withCount('articles')->orderBy('sort_order')->get();

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        BlogCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'bi-tag',
            'color' => $validated['color'] ?? '#0d7a3e',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Kategori blog baru berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = BlogCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'bi-tag',
            'color' => $validated['color'] ?? '#0d7a3e',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Kategori blog berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = BlogCategory::withCount('articles')->findOrFail($id);

        if ($category->articles_count > 0) {
            return redirect()->route('admin.blog.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki artikel tertaut.');
        }

        $category->delete();

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Kategori blog berhasil dihapus.');
    }
}
