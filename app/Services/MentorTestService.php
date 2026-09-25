<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\MentorApplication;
use App\Models\MentorTestSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MentorTestService
{
    public function __construct(
        protected GeminiQuestionService $geminiQuestionService
    ) {}

    public function generateTest(MentorApplication $application, array $options = []): MentorTestSession
    {
        return DB::transaction(function () use ($application, $options) {
            $program = $options['program'] ?? $application->specialization ?? 'Tahfidz';
            $topic = $options['topic'] ?? 'Kaidah Tajwid, Makharijul Huruf, & Tahsin Al-Qur\'an';
            $count = (int) ($options['count'] ?? 15);
            $difficulty = $options['difficulty'] ?? 'Sedang';

            $session = MentorTestSession::create([
                'application_id' => $application->id,
                'session_type' => 'tajwid_test',
                'scheduled_at' => now(),
                'duration_minutes' => 60,
                'mode' => 'online',
                'status' => 'in_progress',
            ]);

            // Coba generate pertanyaan via Gemini Service dengan fast fallback
            $questions = [];
            try {
                if ($count <= 5) {
                    $questions = $this->geminiQuestionService->generateQuestions($program, $topic, $count, $difficulty);
                }
            } catch (\Exception $e) {
                Log::info('Gemini question generation fallback used: '.$e->getMessage());
            }

            if (empty($questions) || count($questions) < $count) {
                $questions = $this->getCuratedQuestionBank($program, $count);
            }

            $session->update([
                'ai_question_payload' => [
                    'topic' => $topic,
                    'program' => $program,
                    'total_questions' => count($questions),
                    'categories' => [
                        'tajwid_test' => 'Tajwid Test',
                        'makharijul_huruf' => 'Makharijul Huruf',
                        'tahsin' => 'Tahsin',
                    ],
                    'questions' => $questions,
                ],
            ]);

            $application->update([
                'status' => 'test_scheduled',
                'current_stage' => 3,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id() ?? $application->user_id,
                action: 'mentor_test_generated',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: null,
                newValues: ['session_id' => $session->id, 'status' => 'test_scheduled', 'total_questions' => count($questions)]
            );

            return $session;
        });
    }

    /**
     * Menyimpan dan menilai jawaban tes yang dikerjakan oleh Calon Guru
     */
    public function submitApplicantAnswers(MentorTestSession $session, array $answers): array
    {
        return DB::transaction(function () use ($session, $answers) {
            $payload = $session->ai_question_payload ?? [];
            $questions = $payload['questions'] ?? [];
            $totalQuestions = count($questions);

            $correctCount = 0;
            $gradedAnswers = [];
            $categoryStats = [
                'tajwid_test' => ['name' => 'Tajwid Test', 'correct' => 0, 'total' => 0],
                'makharijul_huruf' => ['name' => 'Makharijul Huruf', 'correct' => 0, 'total' => 0],
                'tahsin' => ['name' => 'Tahsin', 'correct' => 0, 'total' => 0],
            ];

            foreach ($questions as $index => $q) {
                $catCode = $q['category_code'] ?? 'tajwid_test';
                if (! isset($categoryStats[$catCode])) {
                    $categoryStats[$catCode] = ['name' => $q['category'] ?? 'Umum', 'correct' => 0, 'total' => 0];
                }
                $categoryStats[$catCode]['total']++;

                $submittedAnswer = isset($answers[$index]) ? (int) $answers[$index] : null;
                $correctAnswer = (int) ($q['correct_answer'] ?? 0);
                $isCorrect = ($submittedAnswer !== null && $submittedAnswer === $correctAnswer);

                if ($isCorrect) {
                    $correctCount++;
                    $categoryStats[$catCode]['correct']++;
                }

                $gradedAnswers[] = [
                    'question_index' => $index,
                    'category' => $q['category'] ?? 'Tajwid Test',
                    'category_code' => $catCode,
                    'submitted_answer' => $submittedAnswer,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $isCorrect,
                ];
            }

            // Hitung persentase per kategori
            foreach ($categoryStats as $code => &$stat) {
                $stat['percentage'] = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100, 1) : 0;
            }

            $calculatedScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 80.00;
            $grade = $calculatedScore >= 90 ? 'mumtaz' : ($calculatedScore >= 80 ? 'jayyid_jiddan' : ($calculatedScore >= 70 ? 'jayyid' : ($calculatedScore >= 60 ? 'maqbul' : 'rasib')));

            // Update session
            $payload['applicant_answers'] = $gradedAnswers;
            $payload['category_scores'] = $categoryStats;
            $payload['submitted_at'] = now()->toDateTimeString();

            $session->update([
                'score' => $calculatedScore,
                'grade' => $grade,
                'status' => 'completed',
                'completed_at' => now(),
                'evaluator_notes' => "Tes kompetensi 15 butir diselesaikan oleh calon guru. Skor: {$calculatedScore}/100 ({$correctCount}/{$totalQuestions} benar).",
                'ai_question_payload' => $payload,
            ]);

            // Update application status
            $session->application->update([
                'status' => 'test_completed',
                'final_score' => $calculatedScore,
                'current_stage' => 3,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id() ?? $session->application->user_id,
                action: 'mentor_test_submitted',
                entityType: 'mentor_application',
                entityId: $session->application_id,
                oldValues: ['status' => 'test_scheduled'],
                newValues: ['status' => 'test_completed', 'score' => $calculatedScore, 'grade' => $grade]
            );

            return [
                'score' => $calculatedScore,
                'grade' => $grade,
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'category_scores' => $categoryStats,
            ];
        });
    }

    public function evaluateTest(MentorTestSession $session, array $manualScores = []): bool
    {
        return DB::transaction(function () use ($session, $manualScores) {
            $score = $manualScores['score'] ?? 85.00;
            $grade = $manualScores['grade'] ?? 'jayyid_jiddan';
            $notes = $manualScores['notes'] ?? 'Evaluasi otomatis sistem kompetensi Al-Hikmah.';

            $session->update([
                'score' => $score,
                'grade' => $grade,
                'evaluator_notes' => $notes,
                'evaluator_id' => auth()->id(),
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $application = $session->application;
            $application->update([
                'status' => 'test_completed',
                'final_score' => $score,
                'current_stage' => 3,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id() ?? $application->user_id,
                action: 'mentor_test_evaluated',
                entityType: 'mentor_application',
                entityId: $session->application_id,
                oldValues: ['status' => 'test_scheduled'],
                newValues: ['status' => 'test_completed', 'score' => $score, 'grade' => $grade]
            );

            return true;
        });
    }

    public function scheduleTest(MentorApplication $application, array $data): MentorTestSession
    {
        return DB::transaction(function () use ($application, $data) {
            $session = MentorTestSession::create(array_merge($data, [
                'application_id' => $application->id,
                'status' => 'scheduled',
            ]));

            $application->update([
                'status' => 'test_scheduled',
                'current_stage' => 3,
            ]);

            return $session;
        });
    }

    public function scoreTest(MentorTestSession $session, array $data): bool
    {
        return $this->evaluateTest($session, $data);
    }

    /**
     * Bank Soal Standar 15 Butir Kompetensi Guru Al-Hikmah LMS (3 Kategori: Tajwid, Makharijul Huruf, Tahsin)
     */
    public function getCuratedQuestionBank(string $program = 'Tahfidz', int $count = 15): array
    {
        $bank = [
            // ==========================================
            // KATEGORI 1: TAJWID TEST (5 SOAL)
            // ==========================================
            [
                'id' => 1,
                'category' => 'Tajwid Test',
                'category_code' => 'tajwid_test',
                'question' => 'Berapakah jumlah huruf Ikhfa Haqiqi dalam kaidah hukum Nun Sukun dan Tanwin?',
                'options' => [
                    '15 Huruf (ت ث ج د ذ ز س ش ص ض ط ظ ف ق ك)',
                    '14 Huruf',
                    '6 Huruf (ء هـ ع ح غ خ)',
                    '4 Huruf (ي ن م و)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Huruf Ikhfa Haqiqi berjumlah tepat 15 huruf sebagaimana dirumuskan dalam Matan Al-Jazariyyah & Tuhfatul Athfal.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 2,
                'category' => 'Tajwid Test',
                'category_code' => 'tajwid_test',
                'question' => 'Apakah hukum bacaan apabila huruf Mad Thabi\'i bertemu dengan huruf Hamzah dalam satu kata/kalimat bersambung?',
                'options' => [
                    'Mad Wajib Muttashil (panjang 4-5 harakat saat washal)',
                    'Mad Jaiz Munfashil (panjang 2, 4, atau 5 harakat)',
                    'Mad Lazim Mukhaffaf Kilmi (panjang 6 harakat)',
                    'Mad \'Aridh Lissukun (panjang 2, 4, atau 6 harakat)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Jika huruf mad bertemu hamzah dalam satu kata yang sama, hukumnya adalah Mad Wajib Muttashil dengan kadar panjang 4 atau 5 harakat.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 3,
                'category' => 'Tajwid Test',
                'category_code' => 'tajwid_test',
                'question' => 'Manakah di bawah ini yang merupakan contoh dan hukum bacaan Mim Sukun bertemu huruf Ba (ب)?',
                'options' => [
                    'Ikhfa Syafawi disertai ghunnah (Contoh: تَرْمِيهِمْ بِحِجَارَةٍ)',
                    'Izhar Syafawi tanpa ghunnah (Contoh: لَهُمْ فِيهَا)',
                    'Idgham Mimi / Mitslain (Contoh: عَلَيْهِمْ مُّؤْصَدَةٌ)',
                    'Iqlab disertai penukaran huruf mim',
                ],
                'correct_answer' => 0,
                'explanation' => 'Mim sukun bertemu ba dinamakan Ikhfa Syafawi yang dibaca samar pada dua bibir disertai ghunnah 2 harakat.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 4,
                'category' => 'Tajwid Test',
                'category_code' => 'tajwid_test',
                'question' => 'Kondisi manakah yang menyebabkan huruf Ra\' (ر) wajib dibaca Tarqiq (tipis)?',
                'options' => [
                    'Ra\' berharakat kasrah, atau Ra\' sukun didahului harakat kasrah asli tanpa huruf isti\'la setelahnya',
                    'Ra\' berharakat dhammah atau fathah',
                    'Ra\' sukun didahului huruf berharakat fathah',
                    'Ra\' sukun karena waqaf didahului huruf alif',
                ],
                'correct_answer' => 0,
                'explanation' => 'Huruf Ra\' dibaca Tarqiq (tipis) apabila berharakat kasrah atau sukun setelah kasrah asli dan tidak bertemu huruf isti\'la.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 5,
                'category' => 'Tajwid Test',
                'category_code' => 'tajwid_test',
                'question' => 'Berapa harakat panjang bacaan untuk Mad Lazim Mutsaqqal Kilmi (seperti pada lafazh دَابَّةٍ atau الضَّالِّينَ)?',
                'options' => [
                    '6 Harakat (Wajib Isyba\')',
                    '2 Harakat (Qashr)',
                    '4 Harakat (Tawassuth)',
                    'Boleh memilih antara 2, 4, atau 6 harakat',
                ],
                'correct_answer' => 0,
                'explanation' => 'Mad Lazim Mutsaqqal Kilmi wajib dibaca panjang 6 harakat secara sempurna (isyba\') karena mad bertemu huruf bertasydid dalam satu kata.',
                'difficulty' => 'Sedang',
            ],

            // ==========================================
            // KATEGORI 2: MAKHARIJUL HURUF (5 SOAL)
            // ==========================================
            [
                'id' => 6,
                'category' => 'Makharijul Huruf',
                'category_code' => 'makharijul_huruf',
                'question' => 'Manakah klasifikasi pembagian makhraj huruf untuk wilayah Al-Halq (Tenggorokan)?',
                'options' => [
                    'Aqshal Halqi (ء, هـ), Wasathul Halqi (ع, ح), dan Adnal Halqi (غ, خ)',
                    'Al-Jauf (rongga mulut), Al-Lisan (lidah), dan Asy-Syafatain (dua bibir)',
                    'Al-Khaisyum (rongga hidung) dan Asy-Syafatain saja',
                    'Aqshal Lisan (ق, ك) dan Wasathul Lisan (ج, ش, ي)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Makhraj Halqiyah terbagi tiga: Aqsha (pangkal: Hamzah, Ha), Wasath (tengah: \'Ain, Ha), dan Adna (ujung: Ghain, Kha).',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 7,
                'category' => 'Makharijul Huruf',
                'category_code' => 'makharijul_huruf',
                'question' => 'Dari bagian manakah makhraj keluarnya huruf Dhad (ض) menurut kaidah tajwid Imam Ibnul Jazari?',
                'options' => [
                    'Salah satu tepi lidah (Hafatul Lisan) atau kedua tepinya bertemu dengan gigi geraham atas',
                    'Ujung lidah menempel pada pangkal dua gigi seri atas',
                    'Pangkal lidah paling belakang menempel pada langit-langit lunak',
                    'Dua bibir dirapatkan secara sempurna',
                ],
                'correct_answer' => 0,
                'explanation' => 'Huruf Dhad (ض) keluar dari salah satu tepi lidah atau kedua-duanya disentuhkan ke dinding gigi geraham atas disertai sifat Istithalah.',
                'difficulty' => 'Tinggi',
            ],
            [
                'id' => 8,
                'category' => 'Makharijul Huruf',
                'category_code' => 'makharijul_huruf',
                'question' => 'Huruf-huruf apakah yang keluar dari Wasathul Lisan (Tengah Lidah) ketika bertemu langit-langit keras atas?',
                'options' => [
                    'Jim (ج), Syin (ش), dan Ya (ي) bukan mad',
                    'Qaf (ق) dan Kaf (ك)',
                    'Tha (ط), Dal (د), dan Ta (ت)',
                    'Shad (ص), Zay (ز), dan Sin (س)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Tengah lidah (Wasathul Lisan) adalah tempat keluarnya tiga huruf: Jim (ج), Syin (ش), dan Ya (ي) mutaharrikah/layyinah.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 9,
                'category' => 'Makharijul Huruf',
                'category_code' => 'makharijul_huruf',
                'question' => 'Sifat huruf Hams (terhembusnya nafas saat melafalkan huruf) dimiliki oleh sepuluh huruf yang terangkum dalam bait:',
                'options' => [
                    'فَحَثَّهُ شَخْصٌ سَكَتَ (Fa, Ha, Tsa, Ha\', Sya, Kha, Sha, Sa, Ka, Ta)',
                    'أَجِدُ قَطٍ بَكَتْ (Huruf Syiddah)',
                    'خُصَّ ضَغْطٍ قِظْ (Huruf Isti\'la)',
                    'قُطْبُ جَدٍّ (Huruf Qalqalah)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Huruf Hams terangkum dalam kalimat "Fa hatstsyahu syakhshun sakat" yang ditandai dengan keluarnya hembusan nafas.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 10,
                'category' => 'Makharijul Huruf',
                'category_code' => 'makharijul_huruf',
                'question' => 'Wilayah makhraj Al-Jauf (Rongga Mulut dan Tenggorokan) merupakan tempat keluarnya huruf:',
                'options' => [
                    'Tiga huruf Mad: Alif sukun setelah fathah, Wawu sukun setelah dhammah, dan Ya sukun setelah kasrah',
                    'Huruf-huruf Halqiyah (ء, هـ, ع, ح, غ, خ)',
                    'Huruf Ghunnah pada hidung (Al-Khaisyum)',
                    'Huruf Asy-Syafatain (ب, م, و, ف)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Al-Jauf adalah rongga terbuka tenggorokan hingga mulut yang menjadi makhraj keluarnya huruf-huruf mad thabi\'i.',
                'difficulty' => 'Sedang',
            ],

            // ==========================================
            // KATEGORI 3: TAHSIN (5 SOAL)
            // ==========================================
            [
                'id' => 11,
                'category' => 'Tahsin',
                'category_code' => 'tahsin',
                'question' => 'Dalam metodologi Tahsin Nabawiyyah, prinsip utama "Talaqqi dan Musyafahah" didefinisikan sebagai:',
                'options' => [
                    'Menyimak bacaan guru secara langsung serta mencontohkan gerakan bibir dan makhraj secara tatap muka (vis-à-vis)',
                    'Belajar membaca Al-Qur\'an secara mandiri melalui buku teori tanpa bimbingan guru',
                    'Mendengarkan rekaman audio tanpa verifikasi kefasihan lisan guru',
                    'Menghafal teks terjemahan dan tajwid tanpa mempraktikkan bacaan ayat',
                ],
                'correct_answer' => 0,
                'explanation' => 'Talaqqi (menerima bacaan) dan Musyafahah (melihat gerakan bibir guru) adalah sanad periwayatan Al-Qur\'an sejak masa Rasulullah SAW.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 12,
                'category' => 'Tahsin',
                'category_code' => 'tahsin',
                'question' => 'Perbedaan mendasar antara Lahn Jali (kesalahan nyata) dan Lahn Khafi (kesalahan samar) dalam membaca Al-Qur\'an adalah:',
                'options' => [
                    'Lahn Jali merusak struktur harakat/huruf dan hukumnya haram, sedangkan Lahn Khafi mengurangi kesempurnaan sifat/kadar panjang tajwid',
                    'Lahn Jali hanya terjadi pada mad, sedangkan Lahn Khafi terjadi pada makhraj huruf',
                    'Lahn Jali dimaafkan dalam shalat, sedangkan Lahn Khafi membatalkan shalat',
                    'Tidak ada perbedaan, keduanya berstatus makruh',
                ],
                'correct_answer' => 0,
                'explanation' => 'Lahn Jali adalah kekeliruan fatal (mengubah harakat/huruf) yang berdosa jika disengaja, sedangkan Lahn Khafi adalah cacat pada keindahan tajwid.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 13,
                'category' => 'Tahsin',
                'category_code' => 'tahsin',
                'question' => 'Bagaimana cara membaca lafazh gharib "مَجْرٰ۪ىهَا" (Surah Hud: 41) menurut Riwayat Hafsh \'an \'Ashim Thariq Asy-Syathibiyyah?',
                'options' => [
                    'Dibaca Imalah Sughra/Kubra (memiringkan bunyi fathah ke arah kasrah dan alif ke arah ya)',
                    'Dibaca Tashil (meringankan bunyi hamzah)',
                    'Dibaca Isymam (memonyongkan bibir tanpa suara)',
                    'Dibaca Saktah (berhenti sejenak tanpa bernafas)',
                ],
                'correct_answer' => 0,
                'explanation' => 'Lafazh "Majreha" dalam Surah Hud: 41 wajib dibaca Imalah (antara bunyi "a" dan "e") dalam riwayat Hafsh.',
                'difficulty' => 'Tinggi',
            ],
            [
                'id' => 14,
                'category' => 'Tahsin',
                'category_code' => 'tahsin',
                'question' => 'Kaidah manakah yang tepat saat melakukan Waqaf Kafi (Berhenti yang Cukup)?',
                'options' => [
                    'Boleh waqaf dan memulai (Ibtida\') pada kalimat berikutnya karena makna telah sempurna meskipun masih ada kaitan makna tematik',
                    'Wajib mengulang kalimat sebelumnya karena makna belum bersambung',
                    'Dilarang berhenti karena dapat merusak arti ayat',
                    'Hanya boleh berhenti di akhir juz',
                ],
                'correct_answer' => 0,
                'explanation' => 'Waqaf Kafi adalah berhenti pada kata yang telah sempurna susunan lafazhnya namun maknanya masih berkaitan dengan ayat setelahnya.',
                'difficulty' => 'Sedang',
            ],
            [
                'id' => 15,
                'category' => 'Tahsin',
                'category_code' => 'tahsin',
                'question' => 'Bagaimana langkah pedagogis terbaik seorang guru Al-Qur\'an saat mengoreksi santri yang kesulitan melafalkan huruf \'Ain (ع) dan Hamzah (ء)?',
                'options' => [
                    'Membimbing dari pembedaan makhraj (Wasathul Halqi vs Aqshal Halqi), melatih suara mengalir pada \'Ain, dan memberi contoh berulang dengan sabar',
                    'Meminta santri berhenti membaca dan melewatkan huruf tersebut',
                    'Memarahi santri di depan teman-temannya agar termotivasi',
                    'Mengganti huruf \'Ain dengan huruf Alif agar santri tidak kesulitan',
                ],
                'correct_answer' => 0,
                'explanation' => 'Guru yang bijak membimbing santri dengan pendekatan makhraj anatomis, talaqqi bertahap, serta adab mengajar yang santun.',
                'difficulty' => 'Sedang',
            ],
        ];

        return array_slice($bank, 0, $count);
    }
}
