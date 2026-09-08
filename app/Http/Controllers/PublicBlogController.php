<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicBlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::published()->with(['category', 'user', 'tags'])->latest('published_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(3)->withQueryString();
        $categories = BlogCategory::where('is_active', true)->withCount('publishedArticles')->orderBy('sort_order')->get();
        $tags = BlogTag::withCount('articles')->orderBy('articles_count', 'desc')->take(20)->get();
        $recentArticles = Article::published()->latest('published_at')->take(4)->get();

        return view('blog.index', compact('articles', 'categories', 'tags', 'recentArticles'));
    }

    public function category(string $slug): View
    {
        $category = BlogCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $articles = Article::published()
            ->where('category_id', $category->id)
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->paginate(3)
            ->withQueryString();

        $categories = BlogCategory::where('is_active', true)->withCount('publishedArticles')->orderBy('sort_order')->get();
        $tags = BlogTag::withCount('articles')->orderBy('articles_count', 'desc')->take(20)->get();
        $recentArticles = Article::published()->latest('published_at')->take(4)->get();

        return view('blog.index', compact('articles', 'category', 'categories', 'tags', 'recentArticles'));
    }

    public function tag(string $slug): View
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $articles = Article::published()
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('blog_tags.id', $tag->id);
            })
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->paginate(3)
            ->withQueryString();

        $categories = BlogCategory::where('is_active', true)->withCount('publishedArticles')->orderBy('sort_order')->get();
        $tags = BlogTag::withCount('articles')->orderBy('articles_count', 'desc')->take(20)->get();
        $recentArticles = Article::published()->latest('published_at')->take(4)->get();

        return view('blog.index', compact('articles', 'tag', 'categories', 'tags', 'recentArticles'));
    }

    public function show(string $slug): View
    {
        $article = Article::published()->where('slug', $slug)->with(['category', 'user', 'tags'])->firstOrFail();

        $sessionKey = 'viewed_article_'.$article->id;
        if (! session()->has($sessionKey)) {
            $article->increment('views_count');
            session()->put($sessionKey, now()->timestamp);
        }

        $tagIds = $article->tags->pluck('id')->toArray();
        $relatedQuery = Article::published()->where('id', '!=', $article->id);

        if (! empty($tagIds) || $article->category_id) {
            $relatedQuery->where(function ($q) use ($tagIds, $article) {
                if (! empty($tagIds)) {
                    $q->whereHas('tags', function ($sub) use ($tagIds) {
                        $sub->whereIn('blog_tags.id', $tagIds);
                    });
                }
                if ($article->category_id) {
                    $q->orWhere('category_id', $article->category_id);
                }
            });
        }

        $relatedArticles = $relatedQuery->with(['category', 'user'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $recentArticles = Article::published()->where('id', '!=', $article->id)->latest('published_at')->take(4)->get();
        $categories = BlogCategory::where('is_active', true)->withCount('publishedArticles')->orderBy('sort_order')->get();
        $tags = BlogTag::withCount('articles')->orderBy('articles_count', 'desc')->take(20)->get();

        $prevArticle = Article::published()->where('published_at', '<', $article->published_at ?? $article->created_at)->orderBy('published_at', 'desc')->first();
        $nextArticle = Article::published()->where('published_at', '>', $article->published_at ?? $article->created_at)->orderBy('published_at', 'asc')->first();

        return view('blog.show', compact('article', 'relatedArticles', 'recentArticles', 'categories', 'tags', 'prevArticle', 'nextArticle'));
    }

    public function trackShare(string $slug): JsonResponse
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();
        $article->increment('shares_count');

        return response()->json([
            'status' => 'success',
            'shares_count' => $article->shares_count,
        ]);
    }

    public function sitemap(): Response
    {
        $articles = Article::published()->latest('updated_at')->get();
        $categories = BlogCategory::where('is_active', true)->get();
        $tags = BlogTag::all();

        $content = view('blog.sitemap', compact('articles', 'categories', 'tags'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
