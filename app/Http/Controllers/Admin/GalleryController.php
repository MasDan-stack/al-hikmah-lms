<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Tampilkan daftar galeri kegiatan untuk admin (Aktif & Tong Sampah)
     */
    public function index(Request $request): View
    {
        $isTrashedView = $request->get('status') === 'trashed';
        $query = $isTrashedView ? Gallery::onlyTrashed() : Gallery::query();
        $query->with(['program', 'uploader', 'categoryItem'])->orderBy('sort_order')->latest('event_date');

        // Filter Kategori
        if ($request->filled('category') && $request->category !== 'all') {
            $query->category($request->category);
        }

        // Filter Program
        if ($request->filled('program_id') && $request->program_id !== 'all') {
            $query->where('program_id', $request->program_id);
        }

        // Filter Status Publish (Hanya pada data aktif)
        if (! $isTrashedView && $request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Filter Featured
        if ($request->filled('featured') && $request->featured === '1') {
            $query->where('is_featured', true);
        }

        // Pencarian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $galleries = $query->paginate(12)->withQueryString();
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $categories = GalleryCategory::orderBy('sort_order')->get();

        $stats = [
            'total' => Gallery::count(),
            'published' => Gallery::where('is_published', true)->count(),
            'featured' => Gallery::where('is_featured', true)->count(),
            'trashed' => Gallery::onlyTrashed()->count(),
            'categories_count' => GalleryCategory::count(),
        ];

        return view('admin.galleries.index', compact('galleries', 'programs', 'categories', 'stats', 'isTrashedView'));
    }

    /**
     * Tampilkan formulir tambah dokumentasi kegiatan baru
     */
    public function create(): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $categories = GalleryCategory::active()->orderBy('sort_order')->get();
        $defaultTags = Gallery::DEFAULT_TAGS;

        return view('admin.galleries.create', compact('programs', 'categories', 'defaultTags'));
    }

    /**
     * Simpan dokumentasi kegiatan baru ke storage & database
     */
    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Simpan File Gambar ke storage/app/public/galleries
        $imagePath = $request->file('image')->store('galleries', 'public');

        $categoryId = $validated['category_id'] ?? null;
        if (! $categoryId && ! empty($validated['category'])) {
            $categoryId = GalleryCategory::where('slug', $validated['category'])->value('id');
        }

        Gallery::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? (GalleryCategory::find($categoryId)?->slug ?? 'kegiatan_belajar_mengajar'),
            'category_id' => $categoryId,
            'program_id' => $validated['program_id'] ?? null,
            'image_url' => $imagePath,
            'caption' => $validated['caption'] ?? null,
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'] ?? now()->toDateString(),
            'location' => $validated['location'] ?? null,
            'tags' => $validated['tags'] ?? [],
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => $validated['sort_order'] ?? 0,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Dokumentasi kegiatan galeri berhasil ditambahkan dan siap ditampilkan!');
    }

    /**
     * Tampilkan formulir edit dokumentasi kegiatan
     */
    public function edit(int $id): View
    {
        $gallery = Gallery::findOrFail($id);
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $categories = GalleryCategory::orderBy('sort_order')->get();
        $defaultTags = Gallery::DEFAULT_TAGS;

        return view('admin.galleries.edit', compact('gallery', 'programs', 'categories', 'defaultTags'));
    }

    /**
     * Perbarui data dokumentasi kegiatan
     */
    public function update(UpdateGalleryRequest $request, int $id): RedirectResponse
    {
        $gallery = Gallery::findOrFail($id);
        $validated = $request->validated();

        $categoryId = $validated['category_id'] ?? null;
        if (! $categoryId && ! empty($validated['category'])) {
            $categoryId = GalleryCategory::where('slug', $validated['category'])->value('id');
        }

        $updateData = [
            'title' => $validated['title'],
            'category' => $validated['category'] ?? (GalleryCategory::find($categoryId)?->slug ?? $gallery->category),
            'category_id' => $categoryId ?? $gallery->category_id,
            'program_id' => $validated['program_id'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'] ?? $gallery->event_date,
            'location' => $validated['location'] ?? null,
            'tags' => $validated['tags'] ?? [],
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        // Jika upload gambar baru, simpan path baru (file lama dihapus otomatis oleh GalleryObserver)
        if ($request->hasFile('image')) {
            $updateData['image_url'] = $request->file('image')->store('galleries', 'public');
        }

        $gallery->update($updateData);

        return redirect()->route('admin.galleries.index')
            ->with('success', "Dokumentasi '{$gallery->title}' berhasil diperbarui!");
    }

    /**
     * Hapus dokumentasi kegiatan (Soft Delete -> Pindah ke Tong Sampah)
     */
    public function destroy(int $id): RedirectResponse
    {
        $gallery = Gallery::findOrFail($id);
        $title = $gallery->title;
        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', "Dokumentasi kegiatan '{$title}' berhasil dipindahkan ke Tong Sampah.");
    }

    /**
     * Pulihkan foto dari tong sampah (Restore)
     */
    public function restore(int $id): RedirectResponse
    {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        $gallery->restore();

        return redirect()->route('admin.galleries.index', ['status' => 'trashed'])
            ->with('success', "Dokumentasi '{$gallery->title}' berhasil dipulihkan kembali ke daftar aktif!");
    }

    /**
     * Hapus permanen data dari database dan storage (Force Delete)
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        $title = $gallery->title;

        // forceDelete() akan otomatis memicu GalleryObserver::forceDeleted untuk menghapus file fisik di storage
        $gallery->forceDelete();

        return redirect()->route('admin.galleries.index', ['status' => 'trashed'])
            ->with('success', "Dokumentasi '{$title}' dan file gambarnya telah dihapus permanen dari server.");
    }

    /**
     * Toggle cepat status publish atau featured
     */
    public function toggle(Request $request, int $id): RedirectResponse
    {
        $gallery = Gallery::findOrFail($id);
        $type = $request->get('type', 'publish');

        if ($type === 'featured') {
            $gallery->update(['is_featured' => ! $gallery->is_featured]);
            $statusText = $gallery->is_featured ? 'ditampilkan di Hero Slider' : 'dihapus dari Hero Slider';
        } else {
            $gallery->update(['is_published' => ! $gallery->is_published]);
            $statusText = $gallery->is_published ? 'berhasil dipublikasikan' : 'disimpan sebagai draft';
        }

        return back()->with('success', "Status foto '{$gallery->title}' {$statusText}.");
    }

    /**
     * Drag-and-drop AJAX reorder (Atomik via DB::transaction)
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:galleries,id'],
        ]);

        $order = $request->input('order', []);

        DB::transaction(function () use ($order) {
            foreach ($order as $index => $id) {
                Gallery::where('id', $id)->update(['sort_order' => $index + 1]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Urutan galeri berhasil diperbarui secara atomik!']);
    }
}
