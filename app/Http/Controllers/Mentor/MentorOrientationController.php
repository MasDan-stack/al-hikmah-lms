<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorActivityLog;
use App\Models\MentorProbationTracking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorOrientationController extends Controller
{
    /**
     * Menampilkan Halaman Pusat Orientasi & Pembekalan Guru Baru (Probation Hub).
     */
    public function index(): View
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        $probationTracking = $mentor
            ? MentorProbationTracking::where('mentor_id', $mentor->id)
                ->whereIn('status', ['active', 'extended'])
                ->latest()
                ->first()
                ?? MentorProbationTracking::where('mentor_id', $mentor->id)->latest()->first()
            : null;

        $modules = $this->getModulesDefinition($probationTracking);

        $completedCount = $probationTracking ? $probationTracking->getCompletedModulesCount() : 4;
        $progressPercent = min(100, (int) round(($completedCount / 4) * 100));

        // Metrik sisa hari jika masa percobaan
        $daysLeft = 0;
        $totalDays = 90;
        $daysElapsed = 0;
        if ($probationTracking) {
            $daysLeft = max(0, (int) today()->diffInDays($probationTracking->end_date, false));
            $totalDays = max(1, (int) ($probationTracking->start_date?->diffInDays($probationTracking->end_date) ?? 90));
            $daysElapsed = max(0, $totalDays - $daysLeft);
        }

        return view('mentor.orientation.index', compact(
            'mentor',
            'probationTracking',
            'modules',
            'completedCount',
            'progressPercent',
            'daysLeft',
            'totalDays',
            'daysElapsed'
        ));
    }

    /**
     * Menandai modul orientasi tertentu telah selesai dipelajari oleh mentor.
     */
    public function completeModule(Request $request, string $moduleKey): RedirectResponse
    {
        $validModules = ['mod1', 'mod2', 'mod3', 'mod4'];
        if (! in_array($moduleKey, $validModules)) {
            return back()->with('error', 'Modul orientasi yang dipilih tidak valid.');
        }

        $user = auth()->user();
        $mentor = $user->mentor;

        if (! $mentor) {
            return back()->with('error', 'Profil data mentor tidak ditemukan.');
        }

        $probation = MentorProbationTracking::where('mentor_id', $mentor->id)
            ->whereIn('status', ['active', 'extended'])
            ->latest()
            ->first()
            ?? MentorProbationTracking::where('mentor_id', $mentor->id)->latest()->first();

        $moduleTitles = [
            'mod1' => 'Modul 1: Orientasi Lembaga & SOP Pengajaran Al-Hikmah',
            'mod2' => 'Modul 2: Standar Mutaba\'ah & Penilaian Tajwid Terpadu',
            'mod3' => 'Modul 3: Simulasi Praktik Sesi Perdana di LMS',
            'mod4' => 'Modul 4: Komunikasi Efektif & Pelaporan ke Wali Santri',
        ];

        if (! $probation) {
            return back()->with('success', "Alhamdulillah! Anda telah menuntaskan penelaahan {$moduleTitles[$moduleKey]}.");
        }

        $status = $probation->orientation_modules_status ?? [];
        $status[$moduleKey] = true;

        $updateData = [
            'orientation_modules_status' => $status,
        ];

        if ($moduleKey === 'mod1') {
            $updateData['orientation_completed'] = true;
        } elseif ($moduleKey === 'mod2') {
            $updateData['system_training_completed'] = true;
        } elseif ($moduleKey === 'mod3') {
            $updateData['first_session_conducted'] = true;
        }

        $probation->update($updateData);

        // Update total training modules count
        $completedCount = $probation->fresh()->getCompletedModulesCount();
        $probation->update([
            'training_modules_completed' => $completedCount,
        ]);

        // Catat Audit Activity Log Mentor
        MentorActivityLog::log(
            $mentor->id,
            'orientation_module_completed',
            "Menuntaskan {$moduleTitles[$moduleKey]} pada Hub Orientasi"
        );

        return back()->with('success', "Maa Syaa Allah! {$moduleTitles[$moduleKey]} berhasil diselesaikan.");
    }

    /**
     * Definisi kurikulum & silabus 4 modul orientasi wajib.
     */
    protected function getModulesDefinition(?MentorProbationTracking $probation): array
    {
        $isMod1Done = $probation ? $probation->isModuleCompleted('mod1') : true;
        $isMod2Done = $probation ? $probation->isModuleCompleted('mod2') : true;
        $isMod3Done = $probation ? $probation->isModuleCompleted('mod3') : true;
        $isMod4Done = $probation ? $probation->isModuleCompleted('mod4') : true;

        return [
            'mod1' => [
                'key' => 'mod1',
                'number' => 1,
                'code' => 'MOD-01',
                'title' => 'Orientasi Lembaga & SOP Pengajaran Al-Hikmah',
                'badge_color' => 'primary',
                'icon' => 'bi-building-check',
                'estimated_time' => '15 Menit',
                'is_completed' => $isMod1Done,
                'summary' => 'Mengenal visi-misi yayasan, adab muallim Qur\'an, standar busana syar\'i, SOP kehadiran, dan tata cara izin/cuti resmi.',
                'topics' => [
                    [
                        'heading' => '1. Visi, Misi & Ruh Pendidikan Al-Hikmah',
                        'content' => 'Al-Hikmah berkomitmen mencetak generasi Qur\'ani yang beradab mulia, bertajwid fasih, dan memiliki kecintaan mendalam terhadap Al-Qur\'an. Setiap guru bukan hanya pengajar (*mu\'allim*), tetapi juga teladan (*murabbi*) yang mengutamakan keikhlasan dan kesabaran dalam membimbing ananda santri.',
                    ],
                    [
                        'heading' => '2. SOP Kehadiran & Ketepatan Waktu KBM',
                        'content' => 'Guru wajib hadir dan membuka ruang bimbingan (online melalui tautan room atau offline di lokasi belajar) minimal 5 menit sebelum jadwal sesi dimulai. Keterlambatan tanpa konfirmasi akan menurunkan rasio kehadiran dan mempengaruhi evaluasi masa percobaan.',
                    ],
                    [
                        'heading' => '3. Standar Busana & Penampilan Pendidik',
                        'content' => 'Pendidik wajib mengenakan busana yang syar\'i, bersih, dan rapi saat mengajar. Pengajar ikhwan mengenakan baju koko/gamis berkerah, dan pengajar akhwat mengenakan gamis serta jilbab syar\'i yang menutup aurat secara sempurna.',
                    ],
                    [
                        'heading' => '4. Prosedur Izin, Sakit & Pengganti (Substitute)',
                        'content' => 'Apabila berhalangan hadir karena uzur syar\'i atau sakit, guru wajib mengajukan permohonan melalui menu "Pengajuan Cuti & Pengganti" di LMS minimal 1x24 jam sebelum sesi berlangsung agar santri bimbingan dapat dialokasikan ke guru pengganti tanpa memutus kontinuitas belajar.',
                    ],
                ],
                'key_takeaways' => [
                    'Menjaga niat ikhlas lillahi ta\'ala dalam setiap hembusan nafas membimbing santri.',
                    'Hadir di ruang belajar minimal 5 menit sebelum jadwal sesi dimulai.',
                    'Berbusana syar\'i, rapi, dan mencerminkan wibawa pendidik Al-Qur\'an.',
                    'Pengajuan izin berhalangan dilakukan resmi via sistem, dilarang meliburkan santri secara sepihak.',
                ],
            ],

            'mod2' => [
                'key' => 'mod2',
                'number' => 2,
                'code' => 'MOD-02',
                'title' => 'Standar Mutaba\'ah & Penilaian Tajwid Terpadu',
                'badge_color' => 'success',
                'icon' => 'bi-book-half',
                'estimated_time' => '20 Menit',
                'is_completed' => $isMod2Done,
                'summary' => 'Memahami rubrik penilaian tajwid 4 dimensi, pencatatan mutaba\'ah harian santri, dan metode talaqqi terpadu.',
                'topics' => [
                    [
                        'heading' => '1. Rubrik Penilaian Tajwid 4 Dimensi (Skala 0 - 100)',
                        'content' => 'Penilaian tilawah ananda santri menggunakan 4 pilar objektif: (a) Makharijul Huruf (30%) - ketepatan letak artikulasi huruf; (b) Sifatul Huruf (25%) - hams/jahr, isti\'la, qalqalah, dan sifat lazimah lainnya; (c) Ahkamul Madd wal Qashr (25%) - konsistensi 2, 4, 5, hingga 6 harakat; (d) Ahkamun Nun was Sakinah & Mim Sukun (20%) - izhar, idgham, iqlab, ikhfa.',
                    ],
                    [
                        'heading' => '2. Klasifikasi Sesi: Ziyadah vs Muraja\'ah',
                        'content' => 'Sesi bimbingan terbagi menjadi dua fokus: Ziyadah (penambahan setoran ayat hafalan baru) dan Muraja\'ah (penguatan hafalan surat-surat sebelumnya). Guru wajib memastikan hafalan lama telah mutqin sebelum mengizinkan penambahan juz baru.',
                    ],
                    [
                        'heading' => '3. Ketertiban Pengisian Jurnal Mutaba\'ah di LMS',
                        'content' => 'Setiap akhir sesi bimbingan, guru wajib menginput mutaba\'ah melalui menu "Catat Progres Harian" (`mentor.progress.create`), mencatat surat dan ayat yang dibaca, nilai tajwid, kelancaran, serta memberikan catatan motivatif bagi wali santri.',
                    ],
                ],
                'key_takeaways' => [
                    'Menilai tajwid santri secara adil dan objektif sesuai rubrik 4 dimensi.',
                    'Membedakan porsi KBM antara Ziyadah (setoran baru) dan Muraja\'ah (penguatan).',
                    'Wajib mencatat progres mutaba\'ah segera setelah sesi bimbingan berakhir.',
                ],
            ],

            'mod3' => [
                'key' => 'mod3',
                'number' => 3,
                'code' => 'MOD-03',
                'title' => 'Simulasi Praktik Sesi Perdana di LMS',
                'badge_color' => 'info',
                'icon' => 'bi-laptop',
                'estimated_time' => '25 Menit',
                'is_completed' => $isMod3Done,
                'summary' => 'Panduan alur operasional LMS: membuka sesi, konfirmasi kehadiran santri, tautan video ruang temu, dan penyelesaian sesi.',
                'topics' => [
                    [
                        'heading' => '1. Memeriksa Jadwal Sesi Mengajar',
                        'content' => 'Buka menu "Jadwal Sesi Mengajar" (`mentor.sessions.index`). Pastikan nama santri, program (Tahsin / Tahfidz), tanggal, dan jam bimbingan telah sesuai. Baca catatan khusus dari sesi sebelumnya.',
                    ],
                    [
                        'heading' => '2. Membuka Ruang Pertemuan (Video Call / Meet)',
                        'content' => 'Pada kartu sesi, klik tombol "Mulai Sesi / Buka Room". Tautan Google Meet atau Zoom yang tertera dapat langsung diakses oleh guru dan santri.',
                    ],
                    [
                        'heading' => '3. Melakukan Presensi Kehadiran Santri',
                        'content' => 'Konfirmasi status santri secara real-time pada tombol aksi: pilih "Hadir" jika santri bergabung, "Izin" atau "Sakit" jika terdapat pemberitahuan resmi dari orang tua.',
                    ],
                    [
                        'heading' => '4. Menyelesaikan Sesi & Input Mutaba\'ah',
                        'content' => 'Setelah bimbingan selesai, klik tombol "Selesaikan Sesi" dan lengkapi form progres harian santri. Sesi perdana yang sukses akan otomatis mengaktifkan checklist sesi perdana di akun Anda.',
                    ],
                ],
                'key_takeaways' => [
                    'Selalu memeriksa detail sesi dan nama santri sebelum memulai KBM.',
                    'Mengonfirmasi kehadiran santri tepat waktu pada sistem.',
                    'Menuntaskan sesi dengan menekan tombol "Selesai" dan menginput data mutaba\'ah.',
                ],
            ],

            'mod4' => [
                'key' => 'mod4',
                'number' => 4,
                'code' => 'MOD-04',
                'title' => 'Komunikasi Efektif & Pelaporan ke Wali Santri',
                'badge_color' => 'warning',
                'icon' => 'bi-chat-heart',
                'estimated_time' => '15 Menit',
                'is_completed' => $isMod4Done,
                'summary' => 'Etika komunikasi santun dengan orang tua santri, pemanfaatan fitur chat sistem, dan metode penyampaian umpan balik konstruktif.',
                'topics' => [
                    [
                        'heading' => '1. Adab Komunikasi Islami dengan Orang Tua',
                        'content' => 'Selalu awali dan akhiri komunikasi dengan salam serta ungkapan doa keberkahan. Panggil wali santri dengan santun (Ayah/Bunda/Bapak/Ibu). Hindari penggunaan kata-kata yang dapat menurunkan semangat belajar santri.',
                    ],
                    [
                        'heading' => '2. Pemanfaatan Fitur Pesan Resmi LMS',
                        'content' => 'Gunakan menu "Pesan & Diskusi" (`mentor.messages.index`) untuk berkoordinasi dengan wali santri mengenai perkembangan hafalan, persiapan ujian juz, atau penyesuaian jadwal yang disepakati.',
                    ],
                    [
                        'heading' => '3. Metode Sandwich Feedback dalam Evaluasi',
                        'content' => 'Saat memberikan catatan evaluasi: awali dengan mengapresiasi pencapaian dan kelebihan santri, kemudian sampaikan poin makhraj/tajwid yang perlu dilatih di rumah, dan tutup dengan kalimat optimis serta doa penyemangat.',
                    ],
                    [
                        'heading' => '4. Penanganan Masukan & Eskalasi Masalah',
                        'content' => 'Jika wali santri menyampaikan komplain atau kendala teknis, dengarkan dengan tenang dan penuh empati. Apabila membutuhkan kebijakan yayasan, sampaikan kepada Koordinator Pengajar Al-Hikmah agar ditindaklanjuti secara terpadu.',
                    ],
                ],
                'key_takeaways' => [
                    'Menjunjung tinggi kehangatan ukhuwah dan adab mulia dalam setiap interaksi.',
                    'Menggunakan metode sandwich feedback agar santri dan orang tua tetap termotivasi.',
                    'Merespon pesan wali santri secara responsif dan profesional.',
                ],
            ],
        ];
    }
}
