<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminBlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::with(['category', 'user', 'tags'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $articles = $query->paginate(15)->withQueryString();
        $categories = BlogCategory::all();

        $analytics = [
            'total_articles' => Article::count(),
            'published_count' => Article::published()->count(),
            'total_views' => Article::sum('views_count'),
            'total_shares' => Article::sum('shares_count'),
            'top_articles' => Article::published()->orderBy('views_count', 'desc')->take(5)->get(),
        ];

        return view('admin.blog.index', compact('articles', 'categories', 'analytics'));
    }

    public function create(): View
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('sort_order')->get();
        $tags = BlogTag::orderBy('name')->get();

        return view('admin.blog.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'cover_caption' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:blog_tags,id'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('blog/covers', 'public');
        }

        $article = Article::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::random(5),
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160),
            'content' => $validated['content'],
            'cover_image' => $coverPath,
            'cover_caption' => $validated['cover_caption'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if (! empty($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artikel blog berhasil dibuat dan disimpan!');
    }

    public function edit(int $id): View
    {
        $article = Article::with('tags')->findOrFail($id);
        $categories = BlogCategory::where('is_active', true)->orderBy('sort_order')->get();
        $tags = BlogTag::orderBy('name')->get();

        return view('admin.blog.edit', compact('article', 'categories', 'tags'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'cover_caption' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:blog_tags,id'],
        ]);

        $coverPath = $article->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $coverPath = $request->file('cover_image')->store('blog/covers', 'public');
        }

        $article->update([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160),
            'content' => $validated['content'],
            'cover_image' => $coverPath,
            'cover_caption' => $validated['cover_caption'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? ($article->published_at ?? now()) : null),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $article->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artikel blog berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artikel berhasil dipindahkan ke Tong Sampah (Trash).');
    }

    public function trash(): View
    {
        $articles = Article::onlyTrashed()
            ->with(['category', 'user'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.blog.trash', compact('articles'));
    }

    public function restore(int $id): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();

        return redirect()->route('admin.blog.trash')
            ->with('success', 'Artikel berhasil dipulihkan dari tong sampah.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);

        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->tags()->detach();
        $article->forceDelete();

        return redirect()->route('admin.blog.trash')
            ->with('success', 'Artikel telah dihapus secara permanen beserta berkas gambarnya.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);
        $newStatus = $article->status === 'published' ? 'draft' : 'published';
        $article->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? ($article->published_at ?? now()) : $article->published_at,
        ]);

        return back()->with('success', "Status artikel diubah menjadi {$newStatus}.");
    }

    public function toggleFeatured(int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);
        $article->update(['is_featured' => ! $article->is_featured]);

        return back()->with('success', 'Status artikel unggulan berhasil diperbarui.');
    }

    /**
     * CKEditor 5 Inline Image Upload Handler
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3072'],
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $path = $file->store('blog/content', 'public');
            $url = Storage::disk('public')->url($path);

            return response()->json([
                'url' => $url,
                'default' => $url,
            ]);
        }

        return response()->json([
            'error' => [
                'message' => 'Gagal mengunggah berkas gambar.',
            ],
        ], 400);
    }
}
