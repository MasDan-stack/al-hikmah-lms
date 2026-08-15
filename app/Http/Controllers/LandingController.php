<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Program;
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
