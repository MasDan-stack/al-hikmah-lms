<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiQuestionService
{
    protected string $apiKey;

    protected string $model;

    protected int $maxRetries;

    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key', config('gemini.api_key', env('GEMINI_API_KEY', '')));
        $this->model = (string) config('services.gemini.model', env('GEMINI_TEXT_MODEL', 'gemini-3.6-flash'));
        $this->maxRetries = (int) config('services.gemini.max_retries', env('GEMINI_MAX_RETRIES', 1));
        $this->timeout = (int) config('services.gemini.timeout', env('GEMINI_TIMEOUT', 30));
    }

    /**
     * Generate soal pilihan ganda menggunakan Google Gemini AI
     *
     * @throws Exception
     */
    public function generateQuestions(string $program, string $topic, int $count = 5, string $difficulty = 'Sedang'): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Layanan AI Generator belum diaktifkan oleh Administrator sistem.');
        }

        $prompt = $this->buildPrompt($program, $topic, $count, $difficulty);
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        // Skema JSON Strict yang dipaksa langsung di level API Gemini
        $responseSchema = [
            'type' => 'OBJECT',
            'properties' => [
                'topic' => ['type' => 'STRING'],
                'program' => ['type' => 'STRING'],
                'total_questions' => ['type' => 'INTEGER'],
                'questions' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'question' => ['type' => 'STRING'],
                            'options' => [
                                'type' => 'ARRAY',
                                'items' => ['type' => 'STRING'],
                            ],
                            'correct_answer' => ['type' => 'INTEGER'],
                            'explanation' => ['type' => 'STRING'],
                            'difficulty' => ['type' => 'STRING'],
                        ],
                        'required' => ['question', 'options', 'correct_answer', 'explanation', 'difficulty'],
                    ],
                ],
            ],
            'required' => ['topic', 'program', 'total_questions', 'questions'],
        ];

        try {
            $response = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => defined('CURL_IPRESOLVE_V4') ? CURL_IPRESOLVE_V4 : 1,
                ],
            ])
                ->timeout($this->timeout)
                ->retry($this->maxRetries, 1000, function ($exception, $request) {
                    Log::warning('AI Service Connection issue, retrying...', ['error' => $exception->getMessage()]);

                    return true;
                }, throw: false)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'AlHikmah-LMS/1.0',
                ])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'maxOutputTokens' => 4096,
                    ],
                ]);

            if (! $response->successful()) {
                $status = $response->status();
                $errorMsg = $response->json('error.message') ?? $response->body();

                Log::error('AI Service Error Response', [
                    'status' => $status,
                    'error' => $errorMsg,
                ]);

                if ($status === 429) {
                    throw new Exception('Batas kuota harian pembuatan soal AI telah tercapai (Maksimal kuota rate limit sistem). Silakan coba sesaat lagi atau esok hari.');
                }

                if ($status === 401 || $status === 403) {
                    throw new Exception('Otentikasi layanan AI gagal atau izin akses ditolak. Silakan hubungi Administrator.');
                }

                throw new Exception("Layanan AI sedang sibuk atau mengalami kendala (Kode {$status}). Silakan coba sesaat lagi.");
            }

            $body = $response->json();
            $candidates = $body['candidates'] ?? [];

            if (empty($candidates)) {
                $blockReason = $body['promptFeedback']['blockReason'] ?? 'UNKNOWN';
                throw new Exception("Topik/materi tidak dapat diproses oleh AI (Alasan: {$blockReason}).");
            }

            $rawText = $candidates[0]['content']['parts'][0]['text'] ?? '';

            if (empty($rawText)) {
                $finishReason = $candidates[0]['finishReason'] ?? 'EMPTY';
                throw new Exception("Respon dari sistem AI kosong (Finish Reason: {$finishReason}).");
            }

            return $this->parseResponse($rawText, $difficulty);

        } catch (Exception $e) {
            Log::error('AI Service Exception: '.$e->getMessage());
            throw new Exception('Gagal menghasilkan soal dari AI: '.$e->getMessage());
        }
    }

    /**
     * Membangun prompt komprehensif untuk seluruh rumpun studi Islam & topik bebas
     */
    protected function buildPrompt(string $program, string $topic, int $count, string $difficulty): string
    {
        return <<<EOT
Anda adalah Asisten AI Pakar Pendidikan Islam, Al-Qur'an & Hadits, Tafsir, Fiqih, Aqidah-Akhlak, Sirah Nabawiyah, Bahasa Arab, serta Pengembangan Diri Islami (Tazkiyatun Nafs) untuk AL-HIKMAH LMS.

TUGAS ANDA:
Buatlah tepat {$count} butir soal pilihan ganda berkualitas tinggi berdasarkan topik/tema/pertanyaan:
"{$topic}"
(Target Program Belajar: "{$program}", Tingkat Kesulitan: "{$difficulty}")

PEDOMAN PENYUSUNAN SOAL:
1. Fleksibilitas Tema: Soal dapat mencakup tema bebas apapun yang diminta mentor (misalnya: refleksi mengenal diri/tazkiyatun nafs beserta dalil surat & ayat Al-Qur'an, tafsir tematik, tajwid, hadits nabawi, fiqih ibadah/muamalah, sirah nabi, maupun bahasa Arab).
2. Rujukan Dalil & Fakta: Jika melibatkan ayat Al-Qur'an atau Hadits, sebutkan nama Surat & nomor Ayat (misal: Q.S. Az-Zariyat: 56, Q.S. Al-Hasyr: 18) atau nama perawi hadits dengan tepat.
3. Penulisan Arab: Sertakan teks potongan ayat/istilah Arab berharakat jika relevan, disertai arti/transliterasi ringkas dalam kurung.
4. Struktur Pilihan Ganda: Wajib menyediakan tepat 4 opsi jawaban (indeks 0 sampai 3) yang logis, tidak ambigu, dan mendidik.
5. Kunci Jawaban: 'correct_answer' wajib berupa integer index 0 sampai 3 (0 = Opsi pertama, 1 = Opsi kedua, 2 = Opsi ketiga, 3 = Opsi keempat).
6. Penjelasan (Explanation): Tuliskan penjelasan ringkas (1-2 kalimat) yang menerangkan alasan kebenaran jawaban beserta rujukan dalil surat/ayat Al-Qur'an atau kaidah hukum terkait.
7. Tingkat Kesulitan:
   - Mudah: Pemahaman dasar definisi, arti kata, atau pencocokan dalil surat yang populer.
   - Sedang: Penerapan pemahaman makna ayat, sebab turunnya (asbabun nuzul), atau kaidah hukum.
   - Sulit (HOTS): Analisis perbandingan tafsir, korelasi ayat dengan pembentukan karakter/diri, atau studi kasus praktis.

FORMAT OUTPUT WAJIB JSON MURNI (Wajib diawali dengan { dan diakhiri dengan }):
{
  "questions": [
    {
      "question": "Teks pertanyaan soal...",
      "options": ["Opsi A", "Opsi B", "Opsi C", "Opsi D"],
      "correct_answer": 0,
      "explanation": "Penjelasan ringkas beserta rujukan dalil Surat & Ayat...",
      "difficulty": "{$difficulty}"
    }
  ]
}
EOT;
    }

    /**
     * Membersihkan dan mem-parse respons string ke JSON Array secara tangguh
     */
    protected function parseResponse(string $response, string $fallbackDifficulty = 'Sedang'): array
    {
        $cleaned = trim($response);

        // 1. Ekstrak konten dalam blok ```json ... ``` jika ada
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        } else {
            // 2. Jika ada teks pengantar/penutup di luar JSON, potong dari { pertama ke } terakhir atau [ ke ]
            $firstBrace = strpos($cleaned, '{');
            $firstBracket = strpos($cleaned, '[');

            if ($firstBrace !== false && ($firstBracket === false || $firstBrace < $firstBracket)) {
                $lastBrace = strrpos($cleaned, '}');
                if ($lastBrace !== false && $lastBrace > $firstBrace) {
                    $cleaned = substr($cleaned, $firstBrace, $lastBrace - $firstBrace + 1);
                }
            } elseif ($firstBracket !== false) {
                $lastBracket = strrpos($cleaned, ']');
                if ($lastBracket !== false && $lastBracket > $firstBracket) {
                    $cleaned = substr($cleaned, $firstBracket, $lastBracket - $firstBracket + 1);
                }
            }
        }

        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Decode Error: '.json_last_error_msg().' | Raw: '.$response);
            throw new Exception('Format respons dari AI bukan JSON yang valid.');
        }

        $items = [];
        if (isset($decoded['questions']) && is_array($decoded['questions'])) {
            $items = $decoded['questions'];
        } elseif (is_array($decoded) && array_is_list($decoded)) {
            $items = $decoded;
        }

        if (empty($items)) {
            throw new Exception('Format data butir soal dari AI kosong atau tidak sesuai skema.');
        }

        // Normalisasi format array soal
        return array_map(function ($item) use ($fallbackDifficulty) {
            $questionText = (string) ($item['question'] ?? $item['pertanyaan'] ?? 'Pertanyaan');
            $rawOptions = $item['options'] ?? $item['pilihan'] ?? $item['opsi'] ?? [];
            $options = is_array($rawOptions) ? array_values($rawOptions) : [];

            // Bersihkan prefix opsi jika ada (seperti "A. ", "B. ")
            $options = array_map(function ($opt) {
                return preg_replace('/^[A-Da-d]\.\s*/', '', (string) $opt);
            }, $options);

            // Pastikan ada 4 opsi
            while (count($options) < 4) {
                $options[] = '-';
            }
            $options = array_slice($options, 0, 4);

            $rawAnswer = $item['correct_answer'] ?? $item['jawaban_benar'] ?? $item['kunci'] ?? 0;
            if (is_string($rawAnswer)) {
                $rawAnswerUpper = strtoupper(trim($rawAnswer));
                $letterMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
                $correctAnswer = $letterMap[$rawAnswerUpper] ?? (int) $rawAnswer;
            } else {
                $correctAnswer = (int) $rawAnswer;
            }

            if ($correctAnswer < 0 || $correctAnswer > 3) {
                $correctAnswer = 0;
            }

            $explanation = (string) ($item['explanation'] ?? $item['penjelasan'] ?? $item['pembahasan'] ?? '');
            $difficulty = (string) ($item['difficulty'] ?? $item['tingkat_kesulitan'] ?? $fallbackDifficulty);

            return [
                'question' => $questionText,
                'options' => $options,
                'correct_answer' => $correctAnswer,
                'explanation' => $explanation,
                'difficulty' => $difficulty,
            ];
        }, $items);
    }
}
