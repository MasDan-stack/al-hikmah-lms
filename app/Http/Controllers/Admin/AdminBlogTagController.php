<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminBlogTagController extends Controller
{
    public function index(): View
    {
        $tags = BlogTag::withCount('articles')->orderBy('name')->get();

        return view('admin.blog.tags.index', compact('tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:blog_tags,name'],
        ]);

        BlogTag::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag blog baru berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $tag = BlogTag::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:blog_tags,name,'.$id],
        ]);

        $tag->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag blog berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tag = BlogTag::findOrFail($id);
        $tag->articles()->detach();
        $tag->delete();

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag blog berhasil dihapus.');
    }
}
