<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryCategoryRequest;
use App\Http\Requests\UpdateGalleryCategoryRequest;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GalleryCategoryController extends Controller
{
    /**
     * Tampilkan daftar seluruh kategori galeri beserta statistiknya
     */
    public function index(Request $request): View
    {
        $isTrashedView = $request->get('status') === 'trashed';
        $query = $isTrashedView ? GalleryCategory::onlyTrashed()->withCount('galleries') : GalleryCategory::withCount('galleries');

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter Grup Kategori
        if ($request->filled('group') && $request->group !== 'all') {
            $query->where('group', $request->group);
        }

        // Filter Status Aktif (Hanya pada data aktif)
        if (! $isTrashedView && $request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        $stats = [
            'total' => GalleryCategory::count(),
            'active' => GalleryCategory::where('is_active', true)->count(),
            'inactive' => GalleryCategory::where('is_active', false)->count(),
            'trashed' => GalleryCategory::onlyTrashed()->count(),
            'total_galleries' => Gallery::count(),
        ];

        $groups = GalleryCategory::GROUPS;
        $badgeOptions = GalleryCategory::BADGE_OPTIONS;
        $iconOptions = GalleryCategory::ICON_OPTIONS;

        return view('admin.gallery_categories.index', compact('categories', 'stats', 'groups', 'badgeOptions', 'iconOptions', 'isTrashedView'));
    }

    /**
     * Simpan kategori galeri baru ke database
     */
    public function store(StoreGalleryCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        GalleryCategory::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'group' => $validated['group'],
            'icon' => $validated['icon'] ?? 'bi-images',
            'badge_class' => $validated['badge_class'] ?? 'bg-success',
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.gallery-categories.index')
            ->with('success', 'Kategori galeri "'.$validated['name'].'" berhasil ditambahkan!');
    }

    /**
     * Perbarui data kategori galeri
     */
    public function update(UpdateGalleryCategoryRequest $request, int $id): RedirectResponse
    {
        $category = GalleryCategory::findOrFail($id);
        $validated = $request->validated();

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'group' => $validated['group'],
            'icon' => $validated['icon'] ?? 'bi-images',
            'badge_class' => $validated['badge_class'] ?? 'bg-success',
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.gallery-categories.index')
            ->with('success', 'Kategori galeri "'.$category->name.'" berhasil diperbarui!');
    }

    /**
     * Hapus sementara kategori galeri (Pindah ke Tong Sampah)
     */
    public function destroy(int $id): RedirectResponse
    {
        $category = GalleryCategory::withCount('galleries')->findOrFail($id);

        $name = $category->name;
        $galleriesCount = $category->galleries_count;

        // Lepaskan referensi category_id pada galeri yang masih terhubung agar tidak error
        $category->galleries()->update(['category_id' => null]);

        $category->delete();

        $message = "Kategori \"{$name}\" berhasil dipindahkan ke Tong Sampah.";
        if ($galleriesCount > 0) {
            $message .= " Sebanyak {$galleriesCount} foto terkait dialihkan ke status Tanpa Kategori.";
        }

        return redirect()->route('admin.gallery-categories.index')
            ->with('success', $message);
    }

    /**
     * Pulihkan kategori galeri dari Tong Sampah
     */
    public function restore(int $id): RedirectResponse
    {
        $category = GalleryCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('admin.gallery-categories.index', ['status' => 'trashed'])
            ->with('success', "Kategori \"{$category->name}\" berhasil dipulihkan kembali ke daftar aktif!");
    }

    /**
     * Hapus permanen kategori galeri dari database
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $category = GalleryCategory::onlyTrashed()->findOrFail($id);
        $name = $category->name;

        // Lepaskan referensi jika ada galeri tersisa
        $category->galleries()->update(['category_id' => null]);

        $category->forceDelete();

        return redirect()->route('admin.gallery-categories.index', ['status' => 'trashed'])
            ->with('success', "Kategori \"{$name}\" telah dihapus secara permanen dari sistem.");
    }

    /**
     * Toggle status aktif kategori via AJAX atau Form
     */
    public function toggle(int $id): JsonResponse|RedirectResponse
    {
        $category = GalleryCategory::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $category->is_active,
                'message' => 'Status kategori berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Status kategori "'.$category->name.'" berhasil diubah.');
    }

    /**
     * Update urutan tampil kategori secara atomik
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:gallery_categories,id'],
            'items.*.sort_order' => ['required', 'integer'],
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                GalleryCategory::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Urutan kategori berhasil diperbarui.',
        ]);
    }
}
