<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\MentorActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MentorProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $mentor = $user->mentor?->load(['application.documents']);
        $recentActivities = $mentor
            ? MentorActivityLog::where('mentor_id', $mentor->id)->latest()->take(10)->get()
            : collect();

        return view('mentor.profile', compact('user', 'mentor', 'recentActivities'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'avatar' => $avatarPath,
        ]);

        if ($mentor) {
            $bankHolder = $request->bank_account_holder ?? $request->bank_account_name;
            $gender = $request->gender ? (in_array($request->gender, ['female', 'P']) ? 'P' : 'L') : $mentor->gender;

            $mentor->update([
                'full_name' => $request->name,
                'birth_date' => $request->birth_date,
                'gender' => $gender,
                'address' => $request->address,
                'city' => $request->city,
                'education' => $request->education,
                'institution' => $request->institution,
                'experience_years' => $request->experience_years ?? $mentor->experience_years,
                'hifz_total_juz' => $request->hifz_total_juz ?? $mentor->hifz_total_juz,
                'specialization' => $request->specialization,
                'bio' => $request->bio,
                'sanad_chain' => $request->sanad_chain,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_name' => $bankHolder,
            ]);

            // Handle file uploads (CV & Certificate)
            if ($mentor->application) {
                $app = $mentor->application;

                if ($request->hasFile('cv')) {
                    $path = $request->file('cv')->store("private/mentor_applications/{$app->id}");
                    $app->documents()->updateOrCreate(
                        ['document_type' => 'cv'],
                        [
                            'file_path' => $path,
                            'file_name' => $request->file('cv')->getClientOriginalName(),
                            'file_size' => $request->file('cv')->getSize() / 1024,
                            'mime_type' => $request->file('cv')->getMimeType(),
                        ]
                    );
                }

                if ($request->hasFile('certificate')) {
                    $path = $request->file('certificate')->store("private/mentor_applications/{$app->id}");
                    $app->documents()->updateOrCreate(
                        ['document_type' => 'certificate'],
                        [
                            'file_path' => $path,
                            'file_name' => $request->file('certificate')->getClientOriginalName(),
                            'file_size' => $request->file('certificate')->getSize() / 1024,
                            'mime_type' => $request->file('certificate')->getMimeType(),
                        ]
                    );
                }

                // Sync data ke mentor_application
                $app->update([
                    'full_name' => $request->name,
                    'phone' => $request->phone ?? $app->phone,
                    'birth_date' => $request->birth_date ?? $app->birth_date,
                    'gender' => in_array($gender, ['P', 'female']) ? 'female' : 'male',
                    'address' => $request->address ?? $app->address,
                    'city' => $request->city ?? $app->city,
                    'education' => $request->education ?? $app->education,
                    'institution' => $request->institution ?? $app->institution,
                    'experience_years' => $request->experience_years ?? $app->experience_years,
                    'hifz_total_juz' => $request->hifz_total_juz ?? $app->hifz_total_juz,
                    'specialization' => $request->specialization ?? $app->specialization,
                    'experience_description' => $request->bio ?? $app->experience_description,
                    'sanad_chain' => $request->sanad_chain ?? $app->sanad_chain,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Profil guru, data portofolio, dan rekening pencairan honor berhasil diperbarui!');
    }

    public function downloadDocument(int $documentId): BinaryFileResponse|RedirectResponse
    {
        $mentor = auth()->user()->mentor;
        if (! $mentor || ! $mentor->application) {
            abort(404, 'Data lamaran tidak ditemukan.');
        }

        $document = $mentor->application->documents()->findOrFail($documentId);

        if (! Storage::exists($document->file_path)) {
            if (Storage::disk('public')->exists($document->file_path)) {
                return Storage::disk('public')->response($document->file_path, $document->file_name);
            }

            return back()->with('error', 'File berkas tidak ditemukan di server.');
        }

        return Storage::response($document->file_path, $document->file_name);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password akun guru berhasil diperbarui!');
    }
}
