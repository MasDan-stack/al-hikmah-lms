<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use App\Services\MentorRecruitmentService;
use Illuminate\Http\Request;

class MentorApplicationController extends Controller
{
    public function __construct(
        protected MentorRecruitmentService $recruitmentService
    ) {}

    public function create()
    {
        return view('public.mentor-recruitment.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mentor_applications,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:25',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'education' => 'required|string|max:100',
            'institution' => 'required|string|max:150',
            'experience_years' => 'required|integer|min:0',
            'experience_description' => 'required|string',
            'specialization' => 'required|string|max:50',
            'sanad_chain' => 'nullable|string',
            'hifz_total_juz' => 'required|integer|min:0|max:30',
            'cv' => 'required|file|mimes:pdf|max:2048',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $appData = collect($validated)->except(['cv', 'certificate', 'password_confirmation'])->toArray();
        $application = $this->recruitmentService->submitApplication($appData);

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store("private/mentor_applications/{$application->id}");
            $application->documents()->create([
                'document_type' => 'cv',
                'file_path' => $path,
                'file_name' => $request->file('cv')->getClientOriginalName(),
                'file_size' => $request->file('cv')->getSize() / 1024,
                'mime_type' => $request->file('cv')->getMimeType(),
            ]);
        }

        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store("private/mentor_applications/{$application->id}");
            $application->documents()->create([
                'document_type' => 'certificate',
                'file_path' => $path,
                'file_name' => $request->file('certificate')->getClientOriginalName(),
                'file_size' => $request->file('certificate')->getSize() / 1024,
                'mime_type' => $request->file('certificate')->getMimeType(),
            ]);
        }

        return redirect()->route('mentor.dashboard')->with('success', 'Alhamdulillah! Pendaftaran berhasil dikirim. Akun portal calon guru Anda telah aktif. Nomor Registrasi: '.$application->application_code);
    }

    public function status()
    {
        return view('public.mentor-recruitment.status-tracker');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $application = MentorApplication::where('phone', $request->phone)->first();

        if (! $application) {
            return back()->with('error', 'Data pelamar tidak ditemukan dengan nomor WhatsApp tersebut.');
        }

        return view('public.mentor-recruitment.status-tracker', compact('application'));
    }
}
