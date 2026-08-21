<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Tampilkan katalog program belajar (Deskripsi Saja)
     */
    public function program(): View
    {
        $anakPrograms = Program::where('is_active', true)->anak()->orderBy('sort_order')->get();
        $dewasaPrograms = Program::where('is_active', true)->dewasa()->orderBy('sort_order')->get();
        $arabPrograms = Program::where('is_active', true)->bahasaArab()->orderBy('sort_order')->get();

        return view('program', compact('anakPrograms', 'dewasaPrograms', 'arabPrograms'));
    }

    /**
     * Tampilkan etalase galeri dokumentasi kegiatan AL-HIKMAH (Publik)
     */
    public function galeri(Request $request): View
    {
        $query = Gallery::published()->with(['program', 'categoryItem'])->orderBy('sort_order')->latest('event_date');

        // Filter Berdasarkan Kategori
        if ($request->filled('category') && $request->category !== 'all') {
            $query->category($request->category);
        }

        // Filter Berdasarkan Program
        if ($request->filled('program_id') && $request->program_id !== 'all') {
            $query->where('program_id', $request->program_id);
        }

        // Filter Berdasarkan Tag
        if ($request->filled('tag') && $request->tag !== 'all') {
            $query->tagFilter($request->tag);
        }

        // Pencarian Keyword
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('caption', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Data Foto Galeri (Pagination 9 per halaman)
        $galleries = $query->paginate(9)->withQueryString();

        // 3-5 Foto Unggulan untuk Hero Slider
        $featuredGalleries = Gallery::published()->featured()->with(['categoryItem'])->orderBy('sort_order')->latest('event_date')->take(5)->get();
        if ($featuredGalleries->isEmpty()) {
            $featuredGalleries = Gallery::published()->with(['categoryItem'])->orderBy('sort_order')->latest('event_date')->take(3)->get();
        }

        // Dynamic Meta Tags untuk SEO
        $meta = [
            'title' => 'Galeri Dokumentasi Belajar Al-Qur\'an | AL-HIKMAH',
            'description' => 'Menyimpan momen indah, menyaksikan perjalanan anak dan keluarga belajar membaca, menghafal, dan menghidupkan nilai-nilai Al-Qur\'an bersama AL-HIKMAH.',
            'image' => $featuredGalleries->first()?->asset_url ?? asset('assets/img/og-image.jpg'),
        ];

        // Daftar Master Program & Kategori untuk Navigasi Filter
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $categories = GalleryCategory::active()->ordered()->get();
        $groupedCategories = $categories->groupBy('group');
        $popularTags = Gallery::DEFAULT_TAGS;

        return view('galeri', compact(
            'galleries',
            'featuredGalleries',
            'programs',
            'categories',
            'groupedCategories',
            'popularTags',
            'meta'
        ));
    }

    /**
     * AJAX Tracker Increment Views Count (Anti-Spam Session Protection)
     */
    public function incrementView(int $id): JsonResponse
    {
        $sessionKey = 'viewed_gallery_'.$id;

        if (! session()->has($sessionKey)) {
            Gallery::where('id', $id)->increment('views_count');
            session()->put($sessionKey, true);

            return response()->json(['success' => true, 'incremented' => true]);
        }

        return response()->json(['success' => true, 'incremented' => false]);
    }

    /**
     * Tampilkan peta alur & roadmap belajar
     */
    public function roadmap(): View
    {
        $parentEnrollments = collect();

        if (auth()->check() && auth()->user()->isParent()) {
            $parentProfile = auth()->user()->parentProfile;
            if ($parentProfile) {
                $parentEnrollments = Enrollment::whereHas('student', function ($query) use ($parentProfile) {
                    $query->where('parent_id', $parentProfile->id);
                })->with(['program', 'student', 'mentor'])->latest('id')->get();
            }
        }

        return view('roadmap', compact('parentEnrollments'));
    }

    /**
     * Tampilkan informasi paket & biaya belajar (Terhubung Database, Khusus Orang Tua & Admin)
     */
    public function biaya(): View
    {
        if (! auth()->check() || (! auth()->user()->isParent() && ! auth()->user()->isAdmin())) {
            abort(403, 'Informasi rincian investasi dan biaya belajar hanya dapat diakses oleh Orang Tua / Wali dan Administrator yang telah terdaftar.');
        }

        $programs = Program::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        $registrationFee = 150000;

        $parentEnrollments = collect();
        if (auth()->user()->isParent()) {
            $parentProfile = auth()->user()->parentProfile;
            if ($parentProfile) {
                $parentEnrollments = Enrollment::whereHas('student', function ($query) use ($parentProfile) {
                    $query->where('parent_id', $parentProfile->id);
                })->with(['program', 'student', 'mentor'])->latest('id')->get();
            }
        }

        return view('biaya', compact('programs', 'registrationFee', 'parentEnrollments'));
    }
}
