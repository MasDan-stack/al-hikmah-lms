<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiQuestionService
{
    protected string $provider = 'auto';

    protected string $apiKey = '';

    protected string $model = 'standard';

    protected ?string $baseUrl = null;

    protected int $maxRetries;

    protected int $timeout;

    protected ?string $lastError = null;

    protected bool $isFallbackUsed = false;

    public function __construct()
    {
        $this->maxRetries = (int) config('services.ai.max_retries', env('AI_MAX_RETRIES', env('GEMINI_MAX_RETRIES', 2)));
        $this->timeout = (int) config('services.ai.timeout', env('AI_TIMEOUT', env('GEMINI_TIMEOUT', 45)));
        $this->resolveProviderAndCredentials();
    }

    /**
     * Resolusi otomatis provider AI & kredensial berdasarkan konfigurasi .env
     */
    protected function resolveProviderAndCredentials(?string $preferredProvider = null): void
    {
        $rawProvider = strtolower(trim((string) ($preferredProvider ?: config('services.ai.provider', env('AI_PROVIDER', 'auto')))));

        $universalApiKey = trim((string) config('services.ai.api_key', env('AI_API_KEY', '')));
        $universalModel = trim((string) config('services.ai.model', env('AI_MODEL', '')));
        $universalBaseUrl = config('services.ai.base_url', env('AI_BASE_URL', null));

        $geminiKey = trim((string) config('services.ai.gemini.api_key', config('services.gemini.api_key', env('GEMINI_API_KEY', ''))));
        $geminiModel = trim((string) config('services.ai.gemini.model', config('services.gemini.model', env('GEMINI_TEXT_MODEL', 'gemini-1.5-flash'))));

        $openAiKey = trim((string) config('services.ai.openai.api_key', env('OPENAI_API_KEY', '')));
        $openAiModel = trim((string) config('services.ai.openai.model', env('OPENAI_MODEL', 'gpt-4o-mini')));
        $openAiBaseUrl = config('services.ai.openai.base_url', env('OPENAI_BASE_URL', 'https://api.openai.com/v1'));

        $deepseekKey = trim((string) config('services.ai.deepseek.api_key', env('DEEPSEEK_API_KEY', '')));
        $deepseekModel = trim((string) config('services.ai.deepseek.model', env('DEEPSEEK_MODEL', 'deepseek-chat')));
        $deepseekBaseUrl = config('services.ai.deepseek.base_url', env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'));

        $qwenKey = trim((string) config('services.ai.qwen.api_key', env('QWEN_API_KEY', '')));
        $qwenModel = trim((string) config('services.ai.qwen.model', env('QWEN_MODEL', 'qwen-plus')));
        $qwenBaseUrl = config('services.ai.qwen.base_url', env('QWEN_BASE_URL', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1'));

        $claudeKey = trim((string) config('services.ai.claude.api_key', env('CLAUDE_API_KEY', env('ANTHROPIC_API_KEY', ''))));
        $claudeModel = trim((string) config('services.ai.claude.model', env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022')));
        $claudeBaseUrl = config('services.ai.claude.base_url', env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1'));

        // 1. JIKA PROVIDER DI-SET SECARA EKSPLISIT
        if (in_array($rawProvider, ['gemini', 'google'])) {
            $this->provider = 'gemini';
            $this->apiKey = $geminiKey ?: $universalApiKey;
            $this->model = $geminiModel ?: ($universalModel ?: 'gemini-1.5-flash');
            $this->baseUrl = null;

            return;
        } elseif (in_array($rawProvider, ['qwen', 'dashscope', 'alibaba'])) {
            $this->provider = 'qwen';
            $this->apiKey = $qwenKey ?: $universalApiKey;
            $this->model = $qwenModel ?: ($universalModel ?: 'qwen-plus');
            $this->baseUrl = $qwenBaseUrl ?: ($universalBaseUrl ?: 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1');

            return;
        } elseif (in_array($rawProvider, ['deepseek'])) {
            $this->provider = 'deepseek';
            $this->apiKey = $deepseekKey ?: $universalApiKey;
            $this->model = $deepseekModel ?: ($universalModel ?: 'deepseek-chat');
            $this->baseUrl = $deepseekBaseUrl ?: ($universalBaseUrl ?: 'https://api.deepseek.com/v1');

            return;
        } elseif (in_array($rawProvider, ['claude', 'anthropic'])) {
            $this->provider = 'claude';
            $this->apiKey = $claudeKey ?: $universalApiKey;
            $this->model = $claudeModel ?: ($universalModel ?: 'claude-3-5-sonnet-20241022');
            $this->baseUrl = $claudeBaseUrl ?: $universalBaseUrl;

            return;
        } elseif (in_array($rawProvider, ['openai', 'gpt', 'chatgpt'])) {
            $this->provider = 'openai';
            $this->apiKey = $openAiKey ?: $universalApiKey;
            $this->model = $openAiModel ?: ($universalModel ?: 'gpt-4o-mini');
            $this->baseUrl = $openAiBaseUrl ?: $universalBaseUrl;

            return;
        }

        // 2. JIKA UNIVERSAL API KEY ATAU MODEL KHUSUS DIKONFIGURASI
        $activeModel = strtolower($universalModel);
        if (! empty($universalApiKey) || ! empty($universalModel)) {
            if (str_contains($activeModel, 'claude') || str_starts_with($universalApiKey, 'sk-ant-')) {
                if (! empty($claudeKey) || ! empty($universalApiKey)) {
                    $this->provider = 'claude';
                    $this->apiKey = $universalApiKey ?: $claudeKey;
                    $this->model = $universalModel ?: ($claudeModel ?: 'claude-3-5-sonnet-20241022');
                    $this->baseUrl = $claudeBaseUrl ?: 'https://api.anthropic.com/v1';

                    return;
                }
            } elseif (str_contains($activeModel, 'deepseek')) {
                if (! empty($deepseekKey) || ! empty($universalApiKey)) {
                    $this->provider = 'deepseek';
                    $this->apiKey = $universalApiKey ?: $deepseekKey;
                    $this->model = $universalModel ?: ($deepseekModel ?: 'deepseek-chat');
                    $this->baseUrl = $deepseekBaseUrl ?: 'https://api.deepseek.com/v1';

                    return;
                }
            } elseif (str_contains($activeModel, 'qwen') || str_starts_with($universalApiKey, 'sk-ws-')) {
                if (! empty($qwenKey) || ! empty($universalApiKey)) {
                    $this->provider = 'qwen';
                    $this->apiKey = $universalApiKey ?: $qwenKey;
                    $this->model = $universalModel ?: ($qwenModel ?: 'qwen-plus');
                    $this->baseUrl = $qwenBaseUrl ?: 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1';

                    return;
                }
            } elseif (str_contains($activeModel, 'gpt') || str_contains($activeModel, 'o1') || str_contains($activeModel, 'o3')) {
                if (! empty($openAiKey) || ! empty($universalApiKey)) {
                    $this->provider = 'openai';
                    $this->apiKey = $universalApiKey ?: $openAiKey;
                    $this->model = $universalModel ?: ($openAiModel ?: 'gpt-4o-mini');
                    $this->baseUrl = $openAiBaseUrl ?: 'https://api.openai.com/v1';

                    return;
                }
            } elseif (str_starts_with($universalApiKey, 'AIzaSy') || (str_contains($activeModel, 'gemini') && ! empty($activeModel))) {
                if (! empty($geminiKey) || ! empty($universalApiKey)) {
                    $this->provider = 'gemini';
                    $this->apiKey = $universalApiKey ?: $geminiKey;
                    $this->model = $universalModel ?: ($geminiModel ?: 'gemini-1.5-flash');
                    $this->baseUrl = null;

                    return;
                }
            }
        }

        // 3. JIKA MODE 'auto': Prioritaskan provider yang memiliki API Key valid
        if (! empty($deepseekKey)) {
            $this->provider = 'deepseek';
            $this->apiKey = $deepseekKey;
            $this->model = $deepseekModel ?: 'deepseek-chat';
            $this->baseUrl = $deepseekBaseUrl ?: 'https://api.deepseek.com/v1';

            return;
        }

        if (! empty($qwenKey)) {
            $this->provider = 'qwen';
            $this->apiKey = $qwenKey;
            $this->model = $qwenModel ?: 'qwen-plus';
            $this->baseUrl = $qwenBaseUrl ?: 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1';

            return;
        }

        if (! empty($openAiKey)) {
            $this->provider = 'openai';
            $this->apiKey = $openAiKey;
            $this->model = $openAiModel ?: 'gpt-4o-mini';
            $this->baseUrl = $openAiBaseUrl ?: 'https://api.openai.com/v1';

            return;
        }

        if (! empty($geminiKey)) {
            $this->provider = 'gemini';
            $this->apiKey = $geminiKey;
            $this->model = $geminiModel ?: 'gemini-1.5-flash';
            $this->baseUrl = null;

            return;
        }

        if (! empty($claudeKey)) {
            $this->provider = 'claude';
            $this->apiKey = $claudeKey;
            $this->model = $claudeModel ?: 'claude-3-5-sonnet-20241022';
            $this->baseUrl = $claudeBaseUrl ?: 'https://api.anthropic.com/v1';

            return;
        }

        $this->provider = 'gemini';
        $this->apiKey = $universalApiKey;
        $this->model = $universalModel ?: 'gemini-1.5-flash';
        $this->baseUrl = $universalBaseUrl;
    }

    /**
     * Dapatkan daftar seluruh provider AI yang didukung beserta status ketersediaan API key-nya
     */
    public function getConfiguredProviders(): array
    {
        $deepseekKey = trim((string) config('services.ai.deepseek.api_key', env('DEEPSEEK_API_KEY', '')));
        $qwenKey = trim((string) config('services.ai.qwen.api_key', env('QWEN_API_KEY', '')));
        $geminiKey = trim((string) config('services.ai.gemini.api_key', config('services.gemini.api_key', env('GEMINI_API_KEY', ''))));
        $openAiKey = trim((string) config('services.ai.openai.api_key', env('OPENAI_API_KEY', '')));
        $claudeKey = trim((string) config('services.ai.claude.api_key', env('CLAUDE_API_KEY', env('ANTHROPIC_API_KEY', ''))));

        $hasAnyKey = ! empty($deepseekKey) || ! empty($qwenKey) || ! empty($geminiKey) || ! empty($openAiKey) || ! empty($claudeKey);

        return [
            'auto' => [
                'id' => 'auto',
                'name' => 'Auto (Smart Cascade Multi-AI)',
                'model' => 'DeepSeek ➔ Qwen ➔ OpenAI ➔ Gemini',
                'is_configured' => $hasAnyKey,
                'badge' => '⚡ Rekomendasi',
                'badge_class' => 'bg-warning text-dark',
                'icon' => 'bi-lightning-charge-fill text-warning',
                'description' => 'Otomatis memilih AI terbaik dan beralih ke cadangan jika terjadi limit kuota.',
            ],
            'deepseek' => [
                'id' => 'deepseek',
                'name' => 'DeepSeek AI',
                'model' => config('services.ai.deepseek.model', env('DEEPSEEK_MODEL', 'deepseek-chat')),
                'is_configured' => ! empty($deepseekKey),
                'badge' => 'DeepSeek V3',
                'badge_class' => 'bg-primary text-white',
                'icon' => 'bi-cpu-fill text-primary',
                'description' => 'Penalaran tajwid mendalam, fiqih analitis, & perbandingan kaidah Al-Qur\'an.',
            ],
            'qwen' => [
                'id' => 'qwen',
                'name' => 'Alibaba Qwen AI',
                'model' => config('services.ai.qwen.model', env('QWEN_MODEL', 'qwen-plus')),
                'is_configured' => ! empty($qwenKey),
                'badge' => 'Qwen Plus',
                'badge_class' => 'bg-info text-dark',
                'icon' => 'bi-stars text-info',
                'description' => 'Sangat unggul dalam tata bahasa Arab (Nahwu-Sharaf) & silabus pendidikan Islam.',
            ],
            'gemini' => [
                'id' => 'gemini',
                'name' => 'Google Gemini AI',
                'model' => config('services.ai.gemini.model', config('services.gemini.model', env('GEMINI_TEXT_MODEL', 'gemini-1.5-flash'))),
                'is_configured' => ! empty($geminiKey),
                'badge' => 'Gemini Flash',
                'badge_class' => 'bg-success text-white',
                'icon' => 'bi-google text-success',
                'description' => 'Kecepatan inferensi tinggi untuk evaluasi hafalan mufrodat & hukum bacaan.',
            ],
            'openai' => [
                'id' => 'openai',
                'name' => 'OpenAI ChatGPT',
                'model' => config('services.ai.openai.model', env('OPENAI_MODEL', 'gpt-4o-mini')),
                'is_configured' => ! empty($openAiKey),
                'badge' => 'GPT-4o Mini',
                'badge_class' => 'bg-dark text-white',
                'icon' => 'bi-robot text-dark',
                'description' => 'Struktur pertanyaan variatif dengan pemahaman kontekstual seimbang.',
            ],
            'claude' => [
                'id' => 'claude',
                'name' => 'Anthropic Claude',
                'model' => config('services.ai.claude.model', env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022')),
                'is_configured' => ! empty($claudeKey),
                'badge' => 'Claude 3.5',
                'badge_class' => 'bg-danger text-white',
                'icon' => 'bi-gem text-danger',
                'description' => 'Analisis pedagogik adab, karakter santri, & soal HOTS kompleks.',
            ],
        ];
    }

    /**
     * Dapatkan nama provider aktif untuk kebutuhan audit / status
     */
    public function getActiveProvider(): string
    {
        return $this->provider;
    }

    /**
     * Dapatkan model aktif
     */
    public function getActiveModel(): string
    {
        return $this->model;
    }

    /**
     * Dapatkan pesan error terakhir jika terjadi fallback
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Cek apakah pemanggilan terakhir menggunakan bank kurikulum fallback
     */
    public function isFallbackUsed(): bool
    {
        return $this->isFallbackUsed;
    }

    /**
     * Generate soal pilihan ganda, essay, atau campuran menggunakan Smart Cascade Multi-Provider AI
     *
     * @throws Exception
     */
    public function generateQuestions(
        string $program,
        ?string $topic = null,
        int $count = 5,
        string $difficulty = 'Sedang',
        string $questionType = 'multiple_choice',
        string $requestedProvider = 'auto'
    ): array {
        $this->lastError = null;
        $this->isFallbackUsed = false;

        $cleanTopic = trim($topic ?? '');
        if (empty($cleanTopic) || strtolower($cleanTopic) === 'random' || strtolower($cleanTopic) === 'otomatis') {
            $cleanTopic = $this->getDefaultTopicForProgram($program);
        }

        // Susun urutan cascade kandidat AI
        $cascadeChain = $this->buildCascadeChain($requestedProvider);
        $prompt = $this->buildPrompt($program, $cleanTopic, $count, $difficulty, $questionType);

        foreach ($cascadeChain as $candidate) {
            $candidateProvider = $candidate['provider'];
            $candidateKey = $candidate['key'];
            $candidateModel = $candidate['model'];
            $candidateBaseUrl = $candidate['base_url'];

            if (empty($candidateKey)) {
                continue;
            }

            try {
                $rawText = match ($candidateProvider) {
                    'openai', 'deepseek', 'qwen' => $this->callOpenAiCompatibleApi($prompt, $candidateProvider, $candidateKey, $candidateModel, $candidateBaseUrl),
                    'claude' => $this->callClaudeApi($prompt, $candidateKey, $candidateModel, $candidateBaseUrl),
                    default => $this->callGeminiApi($prompt, $candidateKey, $candidateModel),
                };

                if (empty($rawText)) {
                    Log::warning("AI Provider [{$candidateProvider}] returned empty text, continuing cascade...");

                    continue;
                }

                $parsed = $this->parseResponse($rawText, $difficulty, $questionType, $program, $cleanTopic, $count, false);

                if (! empty($parsed) && is_array($parsed)) {
                    $this->provider = $candidateProvider;
                    $this->model = $candidateModel;
                    $this->apiKey = $candidateKey;
                    $this->baseUrl = $candidateBaseUrl;
                    $this->isFallbackUsed = false;
                    $this->lastError = null;

                    return $parsed;
                }
            } catch (Exception $e) {
                $this->lastError = "[{$candidateProvider}] ".$e->getMessage();
                Log::warning("AI Cascade [{$candidateProvider}] Failed: ".$e->getMessage().', falling over to next provider.');
            }
        }

        // JIKA SELURUH KANDIDAT AI GAGAL ATAU BELUM DIKONFIGURASI, GUNAKAN BANK KURIKULUM TERKURASI
        $this->isFallbackUsed = true;
        $this->provider = 'curated_fallback';
        $this->model = 'Al-Hikmah Standard Curriculum';

        return $this->getCuratedFallbackQuestions($program, $cleanTopic, $count, $difficulty, $questionType);
    }

    /**
     * Membangun urutan prioritas Cascade Failover
     */
    protected function buildCascadeChain(string $requestedProvider = 'auto'): array
    {
        $deepseekKey = trim((string) config('services.ai.deepseek.api_key', env('DEEPSEEK_API_KEY', '')));
        $deepseekModel = trim((string) config('services.ai.deepseek.model', env('DEEPSEEK_MODEL', 'deepseek-chat')));
        $deepseekBaseUrl = config('services.ai.deepseek.base_url', env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'));

        $qwenKey = trim((string) config('services.ai.qwen.api_key', env('QWEN_API_KEY', '')));
        $qwenModel = trim((string) config('services.ai.qwen.model', env('QWEN_MODEL', 'qwen-plus')));
        $qwenBaseUrl = config('services.ai.qwen.base_url', env('QWEN_BASE_URL', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1'));

        $openAiKey = trim((string) config('services.ai.openai.api_key', env('OPENAI_API_KEY', '')));
        $openAiModel = trim((string) config('services.ai.openai.model', env('OPENAI_MODEL', 'gpt-4o-mini')));
        $openAiBaseUrl = config('services.ai.openai.base_url', env('OPENAI_BASE_URL', 'https://api.openai.com/v1'));

        $geminiKey = trim((string) config('services.ai.gemini.api_key', config('services.gemini.api_key', env('GEMINI_API_KEY', ''))));
        $geminiModel = trim((string) config('services.ai.gemini.model', config('services.gemini.model', env('GEMINI_TEXT_MODEL', 'gemini-1.5-flash'))));

        $claudeKey = trim((string) config('services.ai.claude.api_key', env('CLAUDE_API_KEY', env('ANTHROPIC_API_KEY', ''))));
        $claudeModel = trim((string) config('services.ai.claude.model', env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022')));
        $claudeBaseUrl = config('services.ai.claude.base_url', env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1'));

        $allProviders = [
            'deepseek' => ['provider' => 'deepseek', 'key' => $deepseekKey, 'model' => $deepseekModel, 'base_url' => $deepseekBaseUrl],
            'qwen' => ['provider' => 'qwen', 'key' => $qwenKey, 'model' => $qwenModel, 'base_url' => $qwenBaseUrl],
            'openai' => ['provider' => 'openai', 'key' => $openAiKey, 'model' => $openAiModel, 'base_url' => $openAiBaseUrl],
            'gemini' => ['provider' => 'gemini', 'key' => $geminiKey, 'model' => $geminiModel, 'base_url' => null],
            'claude' => ['provider' => 'claude', 'key' => $claudeKey, 'model' => $claudeModel, 'base_url' => $claudeBaseUrl],
        ];

        $req = strtolower(trim($requestedProvider));
        if ($req !== 'auto' && isset($allProviders[$req])) {
            $chain = [$allProviders[$req]];
            // Sisipkan provider lain sebagai backup jika pilihan utama gagal
            foreach ($allProviders as $pKey => $pVal) {
                if ($pKey !== $req && ! empty($pVal['key'])) {
                    $chain[] = $pVal;
                }
            }

            return $chain;
        }

        // Mode 'auto': Coba seluruh provider yang memiliki API key
        $chain = [];
        foreach ($allProviders as $pVal) {
            if (! empty($pVal['key'])) {
                $chain[] = $pVal;
            }
        }

        return $chain;
    }

    /**
     * Panggilan REST API Google Gemini
     */
    protected function callGeminiApi(string $prompt, ?string $apiKey = null, ?string $model = null): string
    {
        $key = $apiKey ?: $this->apiKey;
        $modelName = $model ?: ($this->model ?: 'gemini-1.5-flash');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$key}";

        $response = Http::withOptions([
            'curl' => [
                CURLOPT_IPRESOLVE => defined('CURL_IPRESOLVE_V4') ? CURL_IPRESOLVE_V4 : 1,
            ],
        ])
            ->timeout($this->timeout)
            ->retry($this->maxRetries, 1500, throw: false)
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
                    'temperature' => 0.7,
                    'maxOutputTokens' => 8192,
                ],
            ]);

        if (! $response->successful()) {
            throw new Exception("Gemini API Error (HTTP {$response->status()}): ".($response->json('error.message') ?? $response->body()));
        }

        $body = $response->json();

        return $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    /**
     * Panggilan REST API format OpenAI Chat Completions (Dipakai oleh OpenAI GPT, DeepSeek, & Qwen DashScope)
     */
    protected function callOpenAiCompatibleApi(string $prompt, string $provider = 'openai', ?string $apiKey = null, ?string $model = null, ?string $baseUrl = null): string
    {
        $key = $apiKey ?: $this->apiKey;
        $modelName = $model ?: $this->model;
        $base = rtrim($baseUrl ?: ($this->baseUrl ?: 'https://api.openai.com/v1'), '/');
        $endpoint = "{$base}/chat/completions";

        $payload = [
            'model' => $modelName,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Anda adalah Dewan Pakar Pendidikan Islam & Kurikulum Al-Qur\'an Al-Hikmah. Anda HANYA menjawab dalam format JSON MURNI yang valid tanpa teks pembuka atau penutup di luar JSON.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
        ];

        // Format JSON mode jika didukung provider
        if ($provider === 'openai' || $provider === 'deepseek') {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withOptions([
            'curl' => [
                CURLOPT_IPRESOLVE => defined('CURL_IPRESOLVE_V4') ? CURL_IPRESOLVE_V4 : 1,
            ],
        ])
            ->timeout($this->timeout)
            ->retry($this->maxRetries, 1500, throw: false)
            ->withHeaders([
                'Authorization' => 'Bearer '.$key,
                'Content-Type' => 'application/json',
                'User-Agent' => 'AlHikmah-LMS/1.0',
            ])
            ->post($endpoint, $payload);

        // Jika Qwen international 401/404, coba fallback otomatis ke Qwen domestic endpoint
        if (! $response->successful() && $provider === 'qwen' && str_contains($base, 'dashscope-intl')) {
            $fallbackEndpoint = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
            $retryResponse = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => defined('CURL_IPRESOLVE_V4') ? CURL_IPRESOLVE_V4 : 1,
                ],
            ])
                ->timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$key,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'AlHikmah-LMS/1.0',
                ])
                ->post($fallbackEndpoint, $payload);

            if ($retryResponse->successful()) {
                $body = $retryResponse->json();

                return $body['choices'][0]['message']['content'] ?? '';
            }
        }

        if (! $response->successful()) {
            throw new Exception("{$provider} API Error (HTTP {$response->status()}): ".($response->json('error.message') ?? $response->body()));
        }

        $body = $response->json();

        return $body['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Panggilan REST API Anthropic Claude
     */
    protected function callClaudeApi(string $prompt, ?string $apiKey = null, ?string $model = null, ?string $baseUrl = null): string
    {
        $key = $apiKey ?: $this->apiKey;
        $modelName = $model ?: $this->model;
        $base = rtrim($baseUrl ?: ($this->baseUrl ?: 'https://api.anthropic.com/v1'), '/');
        $endpoint = "{$base}/messages";

        $response = Http::withOptions([
            'curl' => [
                CURLOPT_IPRESOLVE => defined('CURL_IPRESOLVE_V4') ? CURL_IPRESOLVE_V4 : 1,
            ],
        ])
            ->timeout($this->timeout)
            ->retry($this->maxRetries, 1500, throw: false)
            ->withHeaders([
                'x-api-key' => $key,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
                'User-Agent' => 'AlHikmah-LMS/1.0',
            ])
            ->post($endpoint, [
                'model' => $modelName,
                'max_tokens' => 8192,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'system' => 'Anda adalah Dewan Pakar Pendidikan Islam Al-Hikmah. Jawablah HANYA dalam format JSON MURNI yang valid.',
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            throw new Exception("Claude API Error (HTTP {$response->status()}): ".($response->json('error.message') ?? $response->body()));
        }

        $body = $response->json();

        return $body['content'][0]['text'] ?? '';
    }

    /**
     * Membangun prompt komprehensif untuk seluruh 10 Program Belajar & Tipe Soal (Pilihan Ganda / Essay / Campuran)
     */
    protected function buildPrompt(string $program, string $topic, int $count, string $difficulty, string $questionType): string
    {
        $typeInstruction = match ($questionType) {
            'essay' => "Seluruh {$count} butir soal WAJIB berupa SOAL ESSAY / URAIAN yang menuntut penalaran, penjelasan komprehensif, dan analisis santri.",
            'mixed' => "Buatlah kombinasi butir soal: sebagian berupa Pilihan Ganda (multiple_choice) dan sebagian berupa Soal Essay / Uraian (essay), dengan total TEPAT {$count} butir soal.",
            default => "Seluruh {$count} butir soal WAJIB berupa SOAL PILIHAN GANDA (multiple_choice) dengan tepat 4 opsi jawaban (A, B, C, D).",
        };

        $difficultyInstruction = match ($difficulty) {
            'Sulit' => 'Tingkat Kesulitan: SULIT (HOTS - Higher Order Thinking Skills). Buatlah soal analitis, studi kasus penerapan fiqih/tajwid/adab nyata, perbandingan ayat/hadits/kaidah nahwu, atau identifikasi kesalahan bacaan/kaidah yang memerlukan ketelitian tinggi.',
            'Mudah' => 'Tingkat Kesulitan: MUDAH. Fokus pada pemahaman dasar, hafalan mufrodat/doa/ayat populer, pengenalan bentuk huruf/tanda baca, dan definisi ringkas.',
            default => 'Tingkat Kesulitan: SEDANG. Pemahaman kaidah, penerapan hukum tajwid pada potongan ayat, sebab turunnya ayat (asbabun nuzul), atau susunan kalimat bahasa Arab.',
        };

        return <<<EOT
Anda adalah Dewan Pakar Pendidikan Islam, Al-Qur'an, Tajwid & Makharijul Huruf, Hadits, Fiqih (Ibadah, Muamalah, Fiqih Nisa), Bahasa Arab (Nahwu & Sharaf), serta Karakter Islami di Lembaga AL-HIKMAH LMS.

TUGAS ANDA:
Buatlah TEPAT {$count} butir soal ujian/evaluasi berkualitas tinggi.
- Target Program Belajar: "{$program}"
- Topik / Fokus Materi: "{$topic}"
- {$difficultyInstruction}
- {$typeInstruction}

ATURAN ANTI-DUPLIKASI (SANGAT PENTING):
1. Seluruh {$count} butir soal WAJIB 100% UNIK, BERBEDA SATU SAMA LAIN, dan memiliki variasi kasus/pertanyaan yang berbeda.
2. DILARANG KERAS membuat pertanyaan yang berulang, sama, atau hanya mengganti 1 kata dari pertanyaan sebelumnya.
3. Pastikan jumlah array "questions" yang dihasilkan TEPAT {$count} butir soal.

FORMAT OUTPUT WAJIB JSON MURNI (Wajib diawali dengan { dan diakhiri dengan }):
{
  "questions": [
    {
      "type": "multiple_choice",
      "question": "Teks pertanyaan pilihan ganda...",
      "options": ["Opsi A", "Opsi B", "Opsi C", "Opsi D"],
      "correct_answer": 0,
      "explanation": "Penjelasan ringkas beserta rujukan dalil Surat/Ayat atau kaidah...",
      "difficulty": "{$difficulty}"
    },
    {
      "type": "essay",
      "question": "Teks pertanyaan essay/uraian...",
      "essay_answer": "Kunci jawaban / jawaban ideal yang diharapkan secara lengkap...",
      "rubric": "Rubrik penilaian: 100 poin jika menyebutkan dalil dan penjelasan lengkap...",
      "explanation": "Penjelasan kaidah atau sumber rujukan ilmiah...",
      "difficulty": "{$difficulty}"
    }
  ]
}
EOT;
    }

    /**
     * Membersihkan dan mem-parse respons string ke JSON Array secara tangguh & anti-duplikasi
     */
    protected function parseResponse(
        string $response,
        string $fallbackDifficulty = 'Sedang',
        string $requestedType = 'multiple_choice',
        string $program = '',
        string $topic = '',
        ?int $targetCount = null,
        bool $allowFallback = true
    ): ?array {
        $cleaned = trim($response);

        // 1. Ekstrak konten dalam blok ```json ... ``` jika ada
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        } else {
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

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            Log::warning('JSON Decode Error on AI response: '.json_last_error_msg());

            return $allowFallback ? $this->getCuratedFallbackQuestions($program, $topic, $targetCount ?? 5, $fallbackDifficulty, $requestedType) : null;
        }

        $items = [];
        if (isset($decoded['questions']) && is_array($decoded['questions'])) {
            $items = $decoded['questions'];
        } elseif (array_is_list($decoded)) {
            $items = $decoded;
        }

        if (empty($items)) {
            return $allowFallback ? $this->getCuratedFallbackQuestions($program, $topic, $targetCount ?? 5, $fallbackDifficulty, $requestedType) : null;
        }

        // Anti-Duplikasi: Saring butir soal unik berdasarkan hash teks pertanyaan
        $seenKeys = [];
        $uniqueQuestions = [];

        foreach ($items as $item) {
            $questionText = trim((string) ($item['question'] ?? $item['pertanyaan'] ?? ''));
            if (empty($questionText)) {
                continue;
            }

            $key = md5(mb_strtolower($questionText));
            if (isset($seenKeys[$key])) {
                continue; // Lewati pertanyaan duplikat
            }
            $seenKeys[$key] = true;

            $type = $item['type'] ?? ($requestedType === 'essay' ? 'essay' : 'multiple_choice');
            $explanation = (string) ($item['explanation'] ?? $item['penjelasan'] ?? $item['pembahasan'] ?? '');
            $difficulty = (string) ($item['difficulty'] ?? $item['tingkat_kesulitan'] ?? $fallbackDifficulty);

            if ($type === 'essay') {
                $essayAnswer = (string) ($item['essay_answer'] ?? $item['kunci_jawaban'] ?? $item['jawaban_ideal'] ?? 'Kunci jawaban terlampir pada panduan materi.');
                $rubric = (string) ($item['rubric'] ?? $item['rubrik'] ?? 'Penilaian didasarkan pada ketepatan dalil, kelengkapan argumentasi, dan kesesuaian kaidah.');

                $uniqueQuestions[] = [
                    'type' => 'essay',
                    'question' => $questionText,
                    'options' => null,
                    'correct_answer' => null,
                    'essay_answer' => $essayAnswer,
                    'rubric' => $rubric,
                    'explanation' => $explanation,
                    'difficulty' => $difficulty,
                ];
            } else {
                $rawOptions = $item['options'] ?? $item['pilihan'] ?? $item['opsi'] ?? [];
                $options = is_array($rawOptions) ? array_values($rawOptions) : [];

                $options = array_map(function ($opt) {
                    return preg_replace('/^[A-Da-d]\.\s*/', '', (string) $opt);
                }, $options);

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

                $uniqueQuestions[] = [
                    'type' => 'multiple_choice',
                    'question' => $questionText,
                    'options' => $options,
                    'correct_answer' => $correctAnswer,
                    'essay_answer' => null,
                    'rubric' => null,
                    'explanation' => $explanation,
                    'difficulty' => $difficulty,
                ];
            }
        }

        $expectedCount = $targetCount ?? count($uniqueQuestions);

        if (empty($uniqueQuestions)) {
            return $allowFallback ? $this->getCuratedFallbackQuestions($program, $topic, $expectedCount ?: 5, $fallbackDifficulty, $requestedType) : null;
        }

        // Jika jumlah soal unik kurang dari target dan program terdefinisi, lengkapi dari bank kurikulum cadangan tanpa duplikasi jika allowFallback aktif
        if (count($uniqueQuestions) < $expectedCount && $allowFallback) {
            $fallbackSet = $this->getCuratedFallbackQuestions($program, $topic, $expectedCount * 2, $fallbackDifficulty, $requestedType);
            foreach ($fallbackSet as $fItem) {
                $fKey = md5(mb_strtolower(trim($fItem['question'])));
                if (! isset($seenKeys[$fKey])) {
                    $seenKeys[$fKey] = true;
                    $uniqueQuestions[] = $fItem;
                }
                if (count($uniqueQuestions) >= $expectedCount) {
                    break;
                }
            }
        }

        return array_slice($uniqueQuestions, 0, $expectedCount);
    }

    /**
     * Dapatkan topik standar default per program jika mentor mengosongkan topik
     */
    protected function getDefaultTopicForProgram(string $program): string
    {
        return match ($program) {
            'Iqra & Dasar Al-Qur\'an' => 'Pengenalan Huruf Hijaiyah, Tanda Baca Harakat, dan Huruf Sambung Dasar',
            'Tahsin Dasar' => 'Kaidah Tajwid Dasar, Hukum Nun Sukun & Tanwin, serta Makharijul Huruf',
            'Adab & Doa Harian' => 'Adab Thalabul Ilmi, Doa Harian Sehari-hari, dan Berbakti Kepada Orang Tua',
            'Tahfidz Al-Qur\'an' => 'Sambung Ayat dan Pemahaman Karakteristik Surah Pendek Juz 30',
            'Belajar dari Nol (Dewasa)' => 'Makhraj Huruf Arab dan Bacaan Praktis Shalat Fardhu',
            'Tahsin Dewasa' => 'Sifatul Huruf Lanjutan, Kaidah Mad Far\'i, dan Waqaf & Ibtida\'',
            'Kelas Muslimah' => 'Fiqih Nisa (Thaharah & Ibadah), Keteladanan Shahabiyah, dan Adab Muslimah',
            'Tahfidz Dewasa' => 'Metode Hifz & Murajaah, Mutasyabihat, dan Surah Pilihan (Al-Mulk, As-Sajdah)',
            'Bahasa Arab Dasar' => 'Mufrodat Harian, Dhomir (Kata Ganti), dan Percakapan Sapaan Dasar',
            'Nahwu & Sharaf' => 'Pembagian Isim, Fi\'il, Huruf, Kaidah I\'rab, dan Wazan Tashrif Fi\'il',
            default => 'Kaidah Tajwid, Pemahaman Al-Qur\'an, dan Adab Islami',
        };
    }

    /**
     * Bank Soal Cadangan Kurikulum Al-Hikmah (Luas, Masif, Beragam & Garansi Jumlah Sesuai Permintaan)
     */
    public function getCuratedFallbackQuestions(
        string $program,
        string $topic,
        int $count = 5,
        string $difficulty = 'Sedang',
        string $questionType = 'multiple_choice'
    ): array {
        // Ambil bank utama program
        $primaryBank = $this->getCuratedMasterBank($program, $difficulty);

        // Ambil bank pendukung lintas kurikulum Al-Hikmah
        $auxiliaryBank = $this->getAuxiliaryUniversalBank($difficulty);

        // Gabungkan seluruh bank soal unik
        $allQuestions = array_merge($primaryBank, $auxiliaryBank);

        if ($questionType === 'essay') {
            $filtered = array_values(array_filter($allQuestions, fn ($q) => ($q['type'] ?? '') === 'essay'));
        } elseif ($questionType === 'multiple_choice') {
            $filtered = array_values(array_filter($allQuestions, fn ($q) => ($q['type'] ?? '') === 'multiple_choice'));
        } else {
            // Mixed (Campuran): ambil proporsional MC & Essay
            $mcList = array_values(array_filter($allQuestions, fn ($q) => ($q['type'] ?? '') === 'multiple_choice'));
            $essayList = array_values(array_filter($allQuestions, fn ($q) => ($q['type'] ?? '') === 'essay'));
            shuffle($mcList);
            shuffle($essayList);

            $targetEssayCount = max(1, (int) round($count * 0.4));
            $targetMcCount = $count - $targetEssayCount;

            $selectedMc = array_slice($mcList, 0, $targetMcCount);
            $selectedEssay = array_slice($essayList, 0, $targetEssayCount);

            $mixed = array_merge($selectedMc, $selectedEssay);

            return array_slice($mixed, 0, $count);
        }

        // Deduplikasi ketat
        $seen = [];
        $uniqueList = [];
        foreach ($filtered as $item) {
            $hash = md5(mb_strtolower(trim($item['question'])));
            if (! isset($seen[$hash])) {
                $seen[$hash] = true;
                $uniqueList[] = $item;
            }
        }

        // Jika kuota yang diminta lebih besar dari daftar saat ini, lengkapi dengan variasi pertanyaan dinamis
        if (count($uniqueList) < $count) {
            $extraQuestions = $this->generateSupplementalQuestions($program, $topic, $difficulty, $questionType, $count - count($uniqueList));
            foreach ($extraQuestions as $extra) {
                $hash = md5(mb_strtolower(trim($extra['question'])));
                if (! isset($seen[$hash])) {
                    $seen[$hash] = true;
                    $uniqueList[] = $extra;
                }
            }
        }

        return array_slice($uniqueList, 0, $count);
    }

    /**
     * Master Bank Soal Khusus per 10 Program Resmi Al-Hikmah
     */
    protected function getCuratedMasterBank(string $program, string $difficulty): array
    {
        return match ($program) {
            'Bahasa Arab Dasar' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Arti dari kosa kata bahasa Arab "مَدْرَسَةٌ" (Madrasatun) dalam bahasa Indonesia adalah...',
                    'options' => ['Sekolah', 'Rumah', 'Masjid', 'Perpustakaan'],
                    'correct_answer' => 0,
                    'explanation' => 'Kata "مَدْرَسَةٌ" berasal dari kata dasar "دَرَسَ" (belajar) yang berarti tempat belajar atau sekolah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kata ganti orang pertama tunggal ("Saya") dalam bahasa Arab adalah...',
                    'options' => ['أَنْتَ (Anta)', 'هُوَ (Huwa)', 'أَنَا (Ana)', 'نَحْنُ (Nahnu)'],
                    'correct_answer' => 2,
                    'explanation' => 'Dhomir "أَنَا" (Ana) digunakan untuk kata ganti orang pertama tunggal (Saya/Aku).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Isim Isyarah yang tepat untuk menunjuk benda dekat berjenis mudzakkar pada kalimat "... كِتَابٌ جَدِيدٌ" (Ini sebuah buku baru) adalah...',
                    'options' => ['هٰذَا (Haadza)', 'هٰذِهِ (Haadihi)', 'تِلْكَ (Tilka)', 'ذٰلِكَ (Dzaalika)'],
                    'correct_answer' => 0,
                    'explanation' => 'Kata "كِتَابٌ" adalah isim mudzakkar, maka menggunakan kata tunjuk dekat mudzakkar yaitu "هٰذَا".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Ungkapan sapaan "صَبَاحَ الْخَيْرِ" (Shabaahal khair) dalam bahasa Indonesia memiliki arti...',
                    'options' => ['Selamat Pagi', 'Selamat Siang', 'Selamat Malam', 'Selamat Tinggal'],
                    'correct_answer' => 0,
                    'explanation' => '"Shabaahal khair" adalah sapaan selamat pagi, dan dijawab dengan "Shabaahan nuur".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Pertanyaan untuk menanyakan kabar "Bagaimana kabarmu?" kepada seorang teman laki-laki dalam bahasa Arab adalah...',
                    'options' => ['مَا اسْمُكَ؟', 'كَيْفَ حَالُكَ؟', 'مِنْ أَيْنَ أَنْتَ؟', 'كَمْ عُمْرُكَ؟'],
                    'correct_answer' => 1,
                    'explanation' => '"كَيْفَ حَالُكَ؟" (Kaifa haaluka?) digunakan untuk menanyakan keadaan atau kabar seseorang.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Lafadz bilangan angka "خَمْسَةٌ" (Khamsatun) dalam bahasa Arab menunjukkan angka...',
                    'options' => ['3 (Tiga)', '5 (Lima)', '7 (Tujuh)', '9 (Sembilan)'],
                    'correct_answer' => 1,
                    'explanation' => 'Khamsatun (خَمْسَةٌ) adalah bilangan 5 dalam bahasa Arab.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Lawan kata (antonim) dari kata sifat "كَبِيْرٌ" (Kabirun - Besar) dalam bahasa Arab adalah...',
                    'options' => ['صَغِيْرٌ (Shaghirun)', 'طَوِيْلٌ (Thawiilun)', 'قَصِيْرٌ (Qashiirun)', 'جَمِيْلٌ (Jamiilun)'],
                    'correct_answer' => 0,
                    'explanation' => 'Antonim dari "كَبِيْرٌ" (besar) adalah "صَغِيْرٌ" (kecil).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Bentuk kata benda yang menunjukkan dua orang siswa laki-laki (Isim Mutsanna) adalah...',
                    'options' => ['طَالِبٌ (Thalibun)', 'طَالِبَانِ (Thalibani)', 'طُلاَّبٌ (Thullabun)', 'طَالِبَاتٌ (Thalibatun)'],
                    'correct_answer' => 1,
                    'explanation' => 'Isim Mutsanna (dua) dibentuk dengan menambahkan alif dan nun (ـَانِ) di akhir kata mufrad.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Warna "أَبْيَضُ" (Abyadhu) dalam bahasa Arab memiliki arti...',
                    'options' => ['Hitam', 'Putih', 'Merah', 'Hijau'],
                    'correct_answer' => 1,
                    'explanation' => '"أَبْيَضُ" (Abyadhu) berarti putih, sedangkan hitam adalah "أَسْوَدُ" (Aswadu).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Arti dari kosa kata "قَلَمٌ" (Qalamun) adalah...',
                    'options' => ['Buku Tulis', 'Pena / Pulpen', 'Penggaris', 'Penghapus'],
                    'correct_answer' => 1,
                    'explanation' => '"قَلَمٌ" berarti pulpen atau pena alat tulis.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kata tanya yang digunakan untuk menanyakan tempat "Di mana...?" dalam bahasa Arab adalah...',
                    'options' => ['مَنْ (Man)', 'مَا (Maa)', 'أَيْنَ (Ayna)', 'هَلْ (Hal)'],
                    'correct_answer' => 2,
                    'explanation' => '"أَيْنَ" (Ayna) adalah kata tanya tempat (Di mana).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Ungkapan perpisahan "إِلَى اللِّقَاءِ" (Ilal liqa\') memiliki arti...',
                    'options' => ['Selamat Datang', 'Sampai Jumpa Lagi', 'Terima Kasih', 'Sama-sama'],
                    'correct_answer' => 1,
                    'explanation' => '"إِلَى اللِّقَاءِ" adalah ungkapan sampai jumpa lagi yang dijawab dengan "مَعَ السَّلاَمَةِ".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Arti dari kosa kata "غُرْفَةُ النَّوْمِ" (Ghurfatun Naumi) adalah...',
                    'options' => ['Ruang Tamu', 'Kamar Tidur', 'Dapur', 'Kamar Mandi'],
                    'correct_answer' => 1,
                    'explanation' => '"غُرْفَةُ النَّوْمِ" tersusun dari kata ghurfah (ruangan) dan an-naum (tidur), berarti kamar tidur.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kata ganti "نَحْنُ" (Nahnu) dalam bahasa Indonesia berarti...',
                    'options' => ['Kamu sekalian', 'Mereka berdua', 'Kami / Kita', 'Dia (perempuan)'],
                    'correct_answer' => 2,
                    'explanation' => '"نَحْنُ" (Nahnu) adalah dhomir mutakallim ma\'al ghair (kata ganti jamak orang pertama: kami/kita).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Isim Isyarah untuk menunjuk benda jauh muannats pada kalimat "... سَبُّوْرَةٌ جَمِيْلَةٌ" (Itu papan tulis yang bagus) adalah...',
                    'options' => ['ذٰلِكَ (Dzaalika)', 'تِلْكَ (Tilka)', 'هٰذَا (Haadza)', 'هٰؤُلاَءِ (Haa\'ulaa\'i)'],
                    'correct_answer' => 1,
                    'explanation' => 'Kata "سَبُّوْرَةٌ" memiliki ta\' marbuthah (muannats), maka menggunakan kata tunjuk jauh muannats yaitu "تِلْكَ".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan 5 kosa kata bahasa Arab seputar peralatan sekolah/belajar (Al-Adawatul Madrasiyyah) berharakat lengkap beserta artinya dalam bahasa Indonesia!',
                    'essay_answer' => '1. كِتَابٌ (Buku), 2. قَلَمٌ (Pena/Pulpen), 3. مِسْطَرَةٌ (Penggaris), 4. حَقِيْبَةٌ (Tas), 5. مِمْسَحَةٌ (Penghapus).',
                    'rubric' => 'Skor 100: Menuliskan 5 kosakata dengan harakat yang tepat beserta terjemahan yang akurat.',
                    'explanation' => 'Kosakata dasar materi Al-Adawatul Madrasiyyah tingkat pemula.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Sebutkan 6 kata ganti orang (Dhomir Munfashil) dalam bahasa Arab: Huwa, Hiya, Anta, Anti, Ana, Nahnu beserta artinya masing-masing!',
                    'essay_answer' => '1. هُوَ (Dia laki-laki), 2. هِيَ (Dia perempuan), 3. أَنْتَ (Kamu laki-laki), 4. أَنْتِ (Kamu perempuan), 5. أَنَا (Saya/Aku), 6. نَحْنُ (Kami/Kita).',
                    'rubric' => 'Skor 100: Menyebutkan seluruh 6 dhomir beserta klasifikasi subjek dan artinya dengan benar.',
                    'explanation' => 'Materi Dhomir Munfashil dasar bahasa Arab.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Buatlah 2 contoh kalimat sederhana menggunakan Isim Isyarah "هٰذَا" (Haadza) dan "هٰذِهِ" (Haadihi) yang berharakat lengkap!',
                    'essay_answer' => '1. Contoh Haadza (Mudzakkar): هٰذَا مَسْجِدٌ كَبِيْرٌ (Ini masjid yang besar). 2. Contoh Haadihi (Muannats): هٰذِهِ سَيَّارَةٌ جَدِيْدَةٌ (Ini mobil yang baru).',
                    'rubric' => 'Skor 100: Menyusun 2 kalimat berharakat dengan penyesuaian mudzakkar dan muannats yang tepat.',
                    'explanation' => 'Kaidah Isim Isyarah lil Qorib.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan teks percakapan singkat ta\'aruf (perkenalan) antara 2 orang santri (Ahmad & Hasan) meliputi sapaan, nama, dan asal kota!',
                    'essay_answer' => 'أَحْمَدُ: السَّلاَمُ عَلَيْكُمْ. حَسَنٌ: وَعَلَيْكُمُ السَّلاَمُ. أَحْمَدُ: مَا اسْمُكَ؟ حَسَنٌ: اِسْمِي حَسَنٌ، وَمَا اسْمُكَ؟ أَحْمَدُ: اِسْمِي أَحْمَدُ. حَسَنٌ: مِنْ أَيْنَ أَنْتَ؟ أَحْمَدُ: أَنَا مِنْ جَاكَرْتَا.',
                    'rubric' => 'Skor 100: Menuliskan dialog ta\'aruf lengkap dengan struktur tanya jawab sapaan, nama, dan asal kota.',
                    'explanation' => 'Materi Hiwar Ta\'aruf dasar bahasa Arab.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan bilangan angka 1 sampai 10 dalam bahasa Arab (Mudzakkar) berharakat lengkap!',
                    'essay_answer' => '1. وَاحِدٌ (1), 2. اِثْنَانِ (2), 3. ثَلاَثَةٌ (3), 4. أَرْبَعَةٌ (4), 5. خَمْسَةٌ (5), 6. سِتَّةٌ (6), 7. سَبْعَةٌ (7), 8. ثَمَانِيَةٌ (8), 9. تِسْعَةٌ (9), 10. عَشَرَةٌ (10).',
                    'rubric' => 'Skor 100: Menuliskan angka 1 hingga 10 secara urut dan berharakat benar.',
                    'explanation' => 'Materi Al-A\'dad (Bilangan Angka) 1-10.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Adab & Doa Harian' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Doa yang disunnahkan untuk dibaca ketika hendak masuk ke dalam kamar mandi / toilet adalah...',
                    'options' => [
                        'اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنَ الْخُبُثِ وَالْخَبَائِثِ',
                        'الْحَمْدُ لِلَّهِ الَّذِي أَذْهَبَ عَنِّي الْأَذَى وَعَافَانِي',
                        'بِسْمِ اللَّهِ تَوَكَّلْتُ عَلَى اللَّهِ',
                        'اللَّهُمَّ بَارِكْ لَنَا فِيمَا رَزَقْتَنَا',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Doa masuk toilet memohon perlindungan kepada Allah dari gangguan setan laki-laki (khubuts) dan setan perempuan (khabaits).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Adab makan dan minum yang diajarkan oleh Rasulullah SAW kepada Umar bin Abi Salamah RA adalah...',
                    'options' => [
                        'Membaca basmalah, makan dengan tangan kanan, dan mengambil makanan yang terdekat',
                        'Makan sambil berdiri dan berbicara cepat',
                        'Meniup makanan yang masih panas agar cepat dingin',
                        'Mengambil makanan yang paling jauh terlebih dahulu',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Rasulullah SAW bersabda: "Sammi-llah, wa kul biyaminik, wa kul mimma yalik" (Sebutlah nama Allah, makanlah dengan tangan kananmu, dan makanlah dari yang dekat denganmu).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Doa yang dibaca ketika bangun dari tidur di pagi hari adalah...',
                    'options' => [
                        'الْحَمْدُ لِلَّهِ الَّذِي أَحْيَانَا بَعْدَ مَا أَمَاتَنَا وَإِلَيْهِ النُّشُورُ',
                        'بِاسْمِكَ اللَّهُمَّ أَحْيَا وَأَمُوتُ',
                        'رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً',
                        'اللَّهُمَّ أَنْتَ رَبِّي لَا إِلَٰهَ إِلَّا أَنْتَ',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Doa bangun tidur mensyukuri nikmat kehidupan setelah tidur dan mengingatkan pada hari kebangkitan (an-nusyur).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Ketika seorang muslim bersin dan mengucapkan "Alhamdulillah", maka orang yang mendengarnya disunnahkan mendoakan dengan ucapan...',
                    'options' => ['يَرْحَمُكَ اللَّهُ (Yarhamukallah)', 'يَهْدِيكُمُ اللَّهُ', 'غَفَرَ اللَّهُ لَكَ', 'بَارَكَ اللَّهُ فِيكَ'],
                    'correct_answer' => 0,
                    'explanation' => 'Menjawab orang yang bersin (Tasyimtul \'athis) hukumnya fardhu kifayah/sunnah muakkadah dengan mengucapkan "Yarhamukallah".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Doa keluar rumah yang memberikan perlindungan dan jaminan petunjuk dari Allah SWT adalah...',
                    'options' => [
                        'بِسْمِ اللَّهِ تَوَكَّلْتُ عَلَى اللَّهِ لَا حَوْلَ وَلَا قُوَّةَ إِلَّا بِاللَّهِ',
                        'اللَّهُمَّ افْتَحْ لِي أَبْوَابَ رَحْمَتِكَ',
                        'سُبْحَانَ اللَّهِ وَبِحَمْدِهِ',
                        'اللَّهُمَّ اغْفِرْ لِي ذُنُوبِي',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Barangsiapa membaca doa ini saat keluar rumah, malaikat akan berkata: "Engkau telah diberi petunjuk, dicukupi, dan dilindungi".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kewajiban berbakti kepada kedua orang tua (Birrul Walidain) dan larangan berkata "Ah/Cis" (Uffin) ditegaskan dalam surah...',
                    'options' => ['Q.S. Al-Isra\': 23', 'Q.S. Al-Baqarah: 183', 'Q.S. An-Nur: 31', 'Q.S. Al-Kautsar: 2'],
                    'correct_answer' => 0,
                    'explanation' => 'Q.S. Al-Isra ayat 23 menegaskan larangan membentak dan mengucapkan kata yang menyakiti hati orang tua.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Adab memasuki masjid yang sesuai dengan sunnah Rasulullah SAW adalah mendahulukan...',
                    'options' => [
                        'Kaki kanan disertai doa masuk masjid',
                        'Kaki kiri disertai doa keluar masjid',
                        'Kedua kaki bersamaan',
                        'Kaki kiri terlebih dahulu',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Sunnah mendahulukan kaki kanan saat masuk masjid dan membaca doa "Allahummaf-tah li abwaba rahmatik".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Doa yang diajarkan Nabi SAW memohon ilmu yang bermanfaat, rezeki yang thayyib, dan amalan yang diterima adalah...',
                    'options' => [
                        'اللَّهُمَّ إِنِّي أَسْأَلُكَ عِلْمًا نَافِعًا وَرِزْقًا طَيِّبًا وَعَمَلًا مُتَقَبَّلًا',
                        'رَبِّ زِدْنِي عِلْمًا وَارْزُقْنِي فَهْمًا',
                        'اللَّهُمَّ لَا سَهْلَ إِلَّا مَا جَعَلْتَهُ سَهْلًا',
                        'رَبِّ اشْرَحْ لِي صَدْرِي',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Doa ini shahih dibaca Rasulullah SAW setiap selesai shalat Shubuh (HR. Ibnu Majah).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Doa yang dibaca ketika menaiki kendaraan agar senantiasa dalam perlindungan Allah adalah...',
                    'options' => [
                        'سُبْحَانَ الَّذِي سَخَّرَ لَنَا هَٰذَا وَمَا كُنَّا لَهُ مُقْرِنِينَ وَإِنَّا إِلَىٰ رَبِّنَا لَمُنْقَلِبُونَ',
                        'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ',
                        'حَسْبُنَا اللَّهُ وَنِعْمَ الْوَكِيلُ',
                        'اللَّهُمَّ أَنْتَ الصَّاحِبُ فِي السَّفَرِ',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Doa menaiki kendaraan bersumber dari Q.S. Az-Zukhruf ayat 13-14.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Adab bermajelis yang baik menurut tuntunan Islam antara lain...',
                    'options' => [
                        'Memberikan kelapangan tempat duduk bagi yang baru datang dan membaca doa kafaratul majelis saat selesai',
                        'Menyela pembicaraan orang lain secara kasar',
                        'Duduk di tengah-tengah halaqah tanpa izin',
                        'Membicarakan aib orang lain (ghibah)',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Islam memerintahkan berlapang-lapang dalam majelis (Q.S. Al-Mujadilah: 11) dan menutup majelis dengan doa kafaratul majelis.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan adab-adab Islami sebelum tidur dan bangun tidur sesuai sunnah Rasulullah SAW!',
                    'essay_answer' => 'Sebelum tidur: Berwudhu, mengibaskan tempat tidur, mematikan api/lampu, membaca ayat kursi dan 3 Qul (Al-Ikhlas, Al-Falaq, An-Nas), berbaring miring ke sisi kanan, dan membaca doa tidur (Bismika Allahumma ahya wa amut). Bangun tidur: Mengusap wajah, membaca doa bangun tidur, mencuci kedua tangan, bersiwak, dan berwudhu.',
                    'rubric' => 'Skor 100: Menjelaskan minimal 4 adab sebelum tidur dan 3 adab bangun tidur secara runtut.',
                    'explanation' => 'Adab An-Naum wal Istiqadz dalam Kitab Riyadhus Shalihin.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan lafadz doa untuk kedua orang tua (Ibu & Ayah) berharakat lengkap beserta terjemahan dan hikmah berbakti kepada mereka!',
                    'essay_answer' => 'Doa: رَبِّ اغْفِرْ لِي وَلِوَالِدَيَّ وَارْحَمْهُمَا كَمَا رَبَّيَانِي صَغِيرًا. Terjemah: "Wahai Tuhanku, ampunilah aku dan kedua orang tuaku, dan sayangilah mereka berdua sebagaimana mereka telah mendidikku di waktu kecil." Hikmah: Merupakan amalan yang paling dicintai Allah, kunci pembuka pintu surga, dan memperpanjang umur serta melapangkan rezeki.',
                    'rubric' => 'Skor 100: Menuliskan lafadz doa berharakat, terjemah, dan menguraikan minimal 2 hikmah berbakti.',
                    'explanation' => 'Kewajiban Birrul Walidain dalam Islam.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Uraikan 4 adab utama seorang penuntut ilmu (Thalabul Ilmi) terhadap guru/ustadz menurut adab para ulama salaf!',
                    'essay_answer' => '1. Tawadhu\' dan menghormati guru baik di hadapan maupun di belakangnya. 2. Mendengarkan penjelasan guru dengan seksama tanpa memotong pembicaraan. 3. Meminta izin dengan sopan saat hendak bertanya atau meninggalkan majelis. 4. Menjaga dan menutup aib guru serta mendoakan kebaikan bagi guru.',
                    'rubric' => 'Skor 100: Menguraikan 4 adab secara komprehensif dan berbobot.',
                    'explanation' => 'Kitab Ta\'limul Muta\'allim & Adabut Thalib.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Tahfidz Al-Qur\'an', 'Tahfidz Dewasa' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Lanjutan dari ayat "عَمَّ يَتَسَآءَلُونَ" dalam pembukaan Surah An-Naba adalah...',
                    'options' => ['عَنِ النَّبَإِ الْعَظِيمِ', 'الَّذِي هُمْ فِيهِ مُخْتَلِفُونَ', 'كَلَّا سَيَعْلَمُونَ', 'أَلَمْ نَجْعَلِ الْأَرْضَ مِهَادًا'],
                    'correct_answer' => 0,
                    'explanation' => 'Ayat kedua Surah An-Naba berbunyi "عَنِ النَّبَإِ الْعَظِيمِ".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Surah dalam Juz 30 yang menceritakan tentang penciptaan manusia dalam bentuk yang sebaik-baiknya (Fi Ahsani Taqwim) adalah...',
                    'options' => ['Surah At-Tin', 'Surah Al-\'Alaq', 'Surah Al-Bayyinah', 'Surah Al-Qadr'],
                    'correct_answer' => 0,
                    'explanation' => 'Lafadz "لَقَدْ خَلَقْنَا الْإِنْسَانَ فِي أَحْسَنِ تَقْوِيمٍ" terdapat pada ayat ke-4 Surah At-Tin.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Ayat pertama dari Surah Al-Mulk yang dianjurkan dibaca setiap malam sebagai pelindung adzab kubur adalah...',
                    'options' => [
                        'تَبَارَكَ الَّذِي بِيَدِهِ الْمُلْكُ وَهُوَ عَلَىٰ كُلِّ شَيْءٍ قَدِيرٌ',
                        'الَّذِي خَلَقَ الْمَوْتَ وَالْحَيَاةَ لِيَبْلُوَكُمْ',
                        'الَّذِي خَلَقَ سَبْعَ سَمَاوَاتٍ طِبَاقًا',
                        'فَارْجِعِ الْبَصَرَ هَلْ تَرَىٰ مِنْ فُطُورٍ',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Surah Al-Mulk ayat 1 diawali dengan "Tabarakalladzi biyadihil mulk...".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Metode menghafal Al-Qur\'an dengan cara mengulang-ulang hafalan lama dan baru secara teratur agar melekat kuat disebut...',
                    'options' => ['Muroja\'ah (Tikrar)', 'Talqin', 'Tafsir', 'Tarjamah'],
                    'correct_answer' => 0,
                    'explanation' => 'Muroja\'ah atau tikrar adalah pilar utama menjaga kelestarian hafalan Al-Qur\'an agar tidak hilang.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Lafadz "فَإِنَّ مَعَ الْعُسْرِ يُسْرًا" diulang sebanyak dua kali dalam surah...',
                    'options' => ['Surah Al-Insyirah (Asy-Syarh)', 'Surah Adh-Dhuha', 'Surah At-Takatsur', 'Surah Al-Humazah'],
                    'correct_answer' => 0,
                    'explanation' => 'Penegasan bahwa bersama kesulitan pasti ada kemudahan terdapat pada Surah Al-Insyirah ayat 5 dan 6.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Jumlah ayat dalam Surah An-Nazi\'at adalah sebanyak...',
                    'options' => ['40 Ayat', '46 Ayat', '50 Ayat', '36 Ayat'],
                    'correct_answer' => 1,
                    'explanation' => 'Surah An-Nazi\'at terdiri dari 46 ayat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Ayat terakhir dari Surah Al-Fajr (ayat ke-30) berbunyi...',
                    'options' => ['وَادْخُلِي جَنَّتِي', 'فَادْخُلِي فِي عِبَادِي', 'ارْجِعِي إِلَىٰ رَبِّكِ رَاضِيَةً مَرْضِيَّةً', 'يَا أَيَّتُهَا النَّفْسُ الْمُطْمَئِنَّةُ'],
                    'correct_answer' => 0,
                    'explanation' => 'Penutup Surah Al-Fajr ayat 30 adalah "Wadkhuli Jannati".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Metode "Tasmi\'" dalam program Tahfidz Al-Qur\'an Al-Hikmah bermakna...',
                    'options' => [
                        'Memperdengarkan hafalan secara langsung kepada ustadz/penyimak tanpa melihat mushaf',
                        'Membaca Al-Qur\'an dengan terjemahan bahasa Indonesia',
                        'Menulis ayat Al-Qur\'an di atas papan tulis',
                        'Mendengarkan murottal dari rekaman audio saja',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Tasmi\' adalah ujian kelayakan hafalan dengan menyetorkan bacaan hafalan secara langsung di hadapan penyimak.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan metode 3T (Talqin, Tikrar, Tasmi\') dalam menghafal Al-Qur\'an dan bagaimana menyusun jadwal muroja\'ah harian yang efektif!',
                    'essay_answer' => '1. Talqin: Guru membacakan ayat dengan tajwid benar dan santri menirukan. 2. Tikrar: Santri mengulang-ulang ayat baru sebanyak 20-40 kali hingga mutqin. 3. Tasmi\': Memperdengarkan hafalan kepada guru untuk dinilai kelancaran dan ketepatan tajwidnya. Jadwal Muroja\'ah: Membagi hafalan menjadi Sabqi (hafalan baru kemarin), Manzil (hafalan lama), dengan porsi minimal 1 juz per hari.',
                    'rubric' => 'Skor 100: Menjelaskan ketiga tahapan 3T secara terperinci dan memberikan contoh manajemen muroja\'ah yang sistematis.',
                    'explanation' => 'Manajemen Tahfidzul Qur\'an Mutqin.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan 5 ayat pertama dari Surah Al-Mulk lengkap dengan harakat dan terjemahan ringkasnya!',
                    'essay_answer' => '1. تَبَارَكَ الَّذِي بِيَدِهِ الْمُلْكُ وَهُوَ عَلَىٰ كُلِّ شَيْءٍ قَدِيرٌ. 2. الَّذِي خَلَقَ الْمَوْتَ وَالْحَيَاةَ لِيَبْلُوَكُمْ أَيُّكُمْ أَحْسَنُ عَمَلًا ۚ وَهُوَ الْعَزِيزُ الْغَفُورُ. 3. الَّذِي خَلَقَ سَبْعَ سَمَاوَاتٍ طِبَاقًا ۖ مَّا تَرَىٰ فِي خَلْقِ الرَّحْمَٰنِ مِن تَفَاوُتٍ... 4. ثُمَّ ارْجِعِ الْبَصَرَ كَرَّتَيْنِ... 5. وَلَقَدْ زَيَّنَّا السَّمَاءَ الدُّنْيَا بِمَصَابِيحَ...',
                    'rubric' => 'Skor 100: Menuliskan 5 ayat Al-Mulk dengan runtut, harakat benar, dan makna intisarinya.',
                    'explanation' => 'Hafalan Surah Al-Mulk Juz 29.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Iqra & Dasar Al-Qur\'an', 'Belajar dari Nol (Dewasa)' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf hijaiyah yang memiliki titik satu di bawah garis huruf adalah huruf...',
                    'options' => ['Ba\' (ب)', 'Ta\' (ت)', 'Tsa\' (ث)', 'Nun (ن)'],
                    'correct_answer' => 0,
                    'explanation' => 'Huruf Ba (ب) memiliki satu titik di bawah, sedangkan Ta (ت) memiliki dua titik di atas.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda baca "Dhammah" (ُ) pada huruf hijaiyah menghasilkan bunyi vokal...',
                    'options' => ['Bunyi "A"', 'Bunyi "I"', 'Bunyi "U"', 'Bunyi "AN"'],
                    'correct_answer' => 2,
                    'explanation' => 'Fathah berbunyi A, Kasrah berbunyi I, dan Dhammah berbunyi U.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda baca "Tanwin Kasrah" (Kasratain) pada huruf Lam (لٍ) dibaca dengan bunyi...',
                    'options' => ['Lan', 'Lin', 'Lun', 'Laa'],
                    'correct_answer' => 1,
                    'explanation' => 'Kasratain menghasilkan bunyi "in", sehingga huruf Lam ber-kasratain dibaca "Lin".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf Hijaiyah yang TIDAK BISA disambung dengan huruf setelahnya (hanya bisa disambung dari depan) antara lain...',
                    'options' => [
                        'Alif (ا), Dal (د), Dzal (ذ), Ra (ر), Zai (ز), Wawu (و)',
                        'Ba (ب), Ta (ت), Tsa (ث), Jim (ج)',
                        'Sin (س), Syin (ش), Shad (ص), Dhad (ض)',
                        'Fa (ف), Qaf (ق), Kaf (ك), Lam (ل)',
                    ],
                    'correct_answer' => 0,
                    'explanation' => '6 Huruf hijaiyah pemutus (tidak bisa menyambung ke huruf sesudahnya) adalah Alif, Dal, Dzal, Ra, Zai, dan Wawu.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kadar panjang harakat untuk huruf yang berharakat Fathah diikuti Alif mati (Mad Thabi\'i) adalah...',
                    'options' => ['1 Harakat (Pendek)', '2 Harakat (1 Alif)', '4 Harakat', '6 Harakat'],
                    'correct_answer' => 1,
                    'explanation' => 'Mad Thabi\'i atau mad asli wajib dibaca panjang sepanjang 2 harakat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda "Tasydid / Syaddah" (ّ) di atas huruf menandakan bahwa huruf tersebut harus dibaca...',
                    'options' => [
                        'Rangkap / dobel dengan penekanan suara',
                        'Mati / tidak berbunyi',
                        'Panjang 6 harakat',
                        'Memantul (Qalqalah)',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Tasydid menandakan penggabungan dua huruf sejenis, huruf pertama sukun dan huruf kedua berharakat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda baca bulat kecil di atas huruf yang menandakan huruf tersebut mati (tidak bervokal) disebut tanda...',
                    'options' => ['Sukun (ْ)', 'Fathatain (ً)', 'Tasydid (ّ)', 'Kasrah (ِ)'],
                    'correct_answer' => 0,
                    'explanation' => 'Tanda Sukun menandakan huruf konsonan mati.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Sebutkan 6 huruf hijaiyah yang tidak dapat menyambung dengan huruf setelahnya dan berikan 1 contoh kata untuk masing-masing huruf!',
                    'essay_answer' => 'Huruf: 1. Alif (ا) - contoh: أَنَا, 2. Dal (د) - contoh: دَرَسَ, 3. Dzal (ذ) - contoh: ذَهَبَ, 4. Ra (ر) - contoh: رَأَى, 5. Zai (ز) - contoh: زَارَ, 6. Wawu (و) - contoh: وَجَدَ.',
                    'rubric' => 'Skor 100: Menyebutkan seluruh 6 huruf dengan benar disertai contoh kata sederhana.',
                    'explanation' => 'Kaidah penulisan huruf sambung hijaiyah Iqra 2 & 3.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Tahsin Dasar', 'Tahsin Dewasa' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum tajwid yang terjadi apabila Nun Sukun (نْ) atau Tanwin bertemu dengan huruf Ba\' (ب) adalah...',
                    'options' => ['Izhar Halqi', 'Idgham Bighunnah', 'Iqlab', 'Ikhfa Haqiqi'],
                    'correct_answer' => 2,
                    'explanation' => 'Iqlab adalah menukar bunyi nun sukun atau tanwin menjadi mim sukun dengan ghunnah saat bertemu huruf Ba (ب).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Wilayah Makharijul Huruf "Al-Halq" (Tenggorokan) terbagi menjadi 3 bagian. Huruf yang keluar dari Aqshal Halq (Pangkal Tenggorokan) adalah...',
                    'options' => ['Hamzah (ء) dan Ha\' (هـ)', 'Ain (ع) dan Ha\' (ح)', 'Ghain (غ) dan Kha\' (خ)', 'Qaf (ق) dan Kaf (ك)'],
                    'correct_answer' => 0,
                    'explanation' => 'Aqshal Halq (tenggorokan terdalam dekat pita suara) adalah makhraj dari huruf Hamzah (ء) dan Ha\' besar (هـ).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Berapakah kadar panjang harakat bacaan untuk Mad Wajib Muttashil menurut riwayat Imam Hafsh ‘an ‘Ashim thariq Asy-Syathibiyyah?',
                    'options' => ['2 Harakat', '4 atau 5 Harakat', '6 Harakat penuh', '1 Harakat'],
                    'correct_answer' => 1,
                    'explanation' => 'Mad Wajib Muttashil dibaca sepanjang 4 atau 5 harakat saat washal maupun waqaf.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf-huruf yang memiliki sifat "Hams" (mengalirnya nafas saat diucapkan) terangkum dalam bait...',
                    'options' => [
                        'فَحَثَّهُ شَخْصٌ سَكَتَ (Fa hatstsyahu syakhshun sakat)',
                        'خُصَّ ضَغْطٍ قِظْ (Khushsha dhaghthin qizh)',
                        'قُطْبُ جَدٍّ (Quthbu jadin)',
                        'يَرْمَلُوْنَ (Yarmaluun)',
                    ],
                    'correct_answer' => 0,
                    'explanation' => '10 Huruf Hams terangkum dalam kalimat "فحثه شخص سكت" (Fa, Ha, Tsa, Ha besar, Syin, Kha, Shad, Sin, Kaf, Ta).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Contoh hukum bacaan Idgham Bilaghunnah terjadi pada lafadz...',
                    'options' => ['مِنْ مَّالٍ', 'مِنْ لَّدُنْهُ', 'مَنْ يَّقُوْلُ', 'مِنْ خَوْفٍ'],
                    'correct_answer' => 1,
                    'explanation' => 'Idgham Bilaghunnah terjadi saat nun sukun/tanwin bertemu huruf Lam (ل) atau Ra (ر), seperti pada "مِنْ لَّدُنْهُ".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Sifat "Istithalah" (memanjangnya suara dari pangkal tepi lidah hingga ujungnya) secara khusus hanya dimiliki oleh huruf...',
                    'options' => ['Shad (ص)', 'Dhad (ض)', 'Tha (ط)', 'Zha (ظ)'],
                    'correct_answer' => 1,
                    'explanation' => 'Huruf Dhad (ض) adalah satu-satunya huruf hijaiyah yang memiliki sifat Istithalah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Pada surah Hud ayat 41 pada lafadz "مَجْرٰ۪ىهَا", bacaan tersebut dibaca miring antara fathah dan kasrah yang disebut dengan...',
                    'options' => ['Saktah', 'Imalah', 'Isymam', 'Tashil'],
                    'correct_answer' => 1,
                    'explanation' => 'Imalah adalah memiringkan bunyi fathah ke arah kasrah dan alif ke arah ya, dibaca "Majreha".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf-huruf yang keluar dari Wasathul Lisan (Tengah Lidah bertemu Langit-langit atas) adalah...',
                    'options' => ['Jim (ج), Syin (ش), Ya (ي)', 'Qaf (ق) dan Kaf (ك)', 'Lam (ل), Nun (ن), Ra (ر)', 'Tha (ط), Dal (د), Ta (ت)'],
                    'correct_answer' => 0,
                    'explanation' => 'Makhraj Wasathul Lisan menghasilkan 3 huruf: Jim, Syin, dan Ya (terangkum dalam JASY).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum Mim Sukun (مْ) bertemu dengan huruf Mim (م) disebut...',
                    'options' => ['Idgham Mimi / Mitslain', 'Ikhfa Syafawi', 'Izhar Syafawi', 'Iqlab'],
                    'correct_answer' => 0,
                    'explanation' => 'Mim sukun bertemu mim berharakat dibaca Idgham Mimi (Mitslain) dengan dengung 2 harakat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf Isti\'la (huruf tebal yang mengangkat pangkal lidah ke langit-langit) terangkum dalam bait...',
                    'options' => ['خُصَّ ضَغْطٍ قِظْ (Khushsha Dhaghthin Qizh)', 'قُطْبُ جَدٍّ', 'يَرْمَلُوْنَ', 'حُرُوْفُ الْمَدِّ'],
                    'correct_answer' => 0,
                    'explanation' => 'Huruf Isti\'la ada 7: Kha, Shad, Dhad, Ghain, Tha, Qaf, dan Zha.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kadar panjang bacaan untuk Mad Lazim Kilmi Mutsaqqal (seperti pada lafadz "وَلاَ الضَّآلِّيْنَ") adalah...',
                    'options' => ['2 Harakat', '4 Harakat', '6 Harakat (Wajib)', '8 Harakat'],
                    'correct_answer' => 2,
                    'explanation' => 'Mad Lazim Kilmi Mutsaqqal wajib dibaca panjang 6 harakat secara konsisten.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf Safir (huruf yang memiliki suara desis menyerupai siulan burung) terdiri atas 3 huruf yaitu...',
                    'options' => ['Shad (ص), Zai (ز), Sin (س)', 'Tha, Dal, Ta', 'Fa, Wawu, Ba', 'Jim, Syin, Ya'],
                    'correct_answer' => 0,
                    'explanation' => 'Huruf Safir adalah Shad, Zai, dan Sin.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum bacaan Alif Lam Syamsiyyah terjadi apabila Alif Lam (ال) bertemu dengan huruf...',
                    'options' => ['Ta, Tsa, Dal, Dzal, Ra, Zai, Sin, Syin, Shad, Dhad, Tha, Zha, Lam, Nun', 'Alif, Ba, Ghain, Ha, Jim, Kaf, Wawu, Kha, Fa, Ain, Qaf, Ya, Mim, Ha besar', 'Nun dan Mim saja', 'Huruf Halqi saja'],
                    'correct_answer' => 0,
                    'explanation' => 'Alif Lam Syamsiyyah melebur ke dalam 14 huruf syamsiyyah yang bertasydid.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Mad yang terjadi ketika huruf Hamzah berharakat mendahului huruf Mad dalam satu kata disebut...',
                    'options' => ['Mad Badal', 'Mad Tamkin', 'Mad Farq', 'Mad Shilah Qashirah'],
                    'correct_answer' => 0,
                    'explanation' => 'Mad Badal adalah setiap hamzah yang mendahului huruf mad, contohnya "آمَنُوا" (Aamanuu).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Berhenti sejenak tanpa bernafas selama 2 harakat pada tempat-tempat khusus dalam Al-Qur\'an disebut bacaan...',
                    'options' => ['Saktah', 'Isymam', 'Tashil', 'Imalah'],
                    'correct_answer' => 0,
                    'explanation' => 'Saktah adalah menahan suara tanpa bernafas dengan niat melanjutkan bacaan (contoh: Surah Al-Kahfi ayat 1-2).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Sebutkan 5 huruf Qalqalah dan jelaskan perbedaan antara Qalqalah Sughra, Qalqalah Kubra, dan Qalqalah Akbar disertai masing-masing 1 contoh ayat Al-Qur\'an!',
                    'essay_answer' => 'Huruf Qalqalah: ق, ط, ب, ج, د (Baju Di Toko / Quthbu Jadin). 1. Sughra: Huruf qalqalah sukun di tengah kata (contoh: يَطْمَعُ). 2. Kubra: Huruf qalqalah sukun di akhir kata karena waqaf (contoh: وَالْفَلَقِ). 3. Akbar: Huruf qalqalah bertasydid di akhir kata saat waqaf (contoh: تَبَّتْ يَدَا أَبِي لَهَبٍ وَتَبَّ).',
                    'rubric' => 'Skor 100: Menyebutkan 5 huruf dan menerangkan 3 tingkatan qalqalah beserta contoh ayat yang akurat.',
                    'explanation' => 'Kaidah sifatul huruf Qalqalah (pantulan suara) dalam kitab Matan Al-Jazariyyah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan mengapa sifat "Istithalah" hanya khusus dimiliki oleh huruf Dhad (ض), dan bagaimana cara melafalkannya tanpa tertukar dengan huruf Zha\' (ظ)?',
                    'essay_answer' => 'Istithalah adalah memanjangnya suara huruf dari awal tepi lidah hingga ujung tepi lidah menyentuh gigi geraham atas. Huruf Dhad (ض) keluar dari tepi lidah (Hafatul Lisan) menempel geraham, sedangkan Zha\' (ظ) keluar dari ujung lidah menyentuh ujung gigi seri atas. Sehingga Dhad tidak boleh dikeluarkan dari ujung gigi agar tidak terdengar seperti Zha\'.',
                    'rubric' => 'Skor 100: Menjelaskan definisi istithalah, makhraj tepi lidah vs ujung lidah, dan perbedaan fonetik dengan huruf Zha\'.',
                    'explanation' => 'Berdasarkan uraian Imam Ibnul Jazari dalam Bab Makharij & Sifatul Huruf.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan hukum bacaan Ra\' Tafkhim (tebal) dan Ra\' Tarqiq (tipis) beserta kondisi masing-masing disertai contoh lafadz dalam Al-Qur\'an!',
                    'essay_answer' => '1. Ra\' Tafkhim: Ra berharakat fathah/dhammah, ra sukun didahului fathah/dhammah, atau ra sukun didahului hamzah washal. Contoh: رَبِّنَا, الْقُرْآنُ. 2. Ra\' Tarqiq: Ra berharakat kasrah, ra sukun didahului kasrah asli tanpa huruf isti\'la setelahnya, atau ra waqaf didahului ya sukun (Mad Layyin). Contoh: رِزْقًا, فِرْعَوْنَ, خَيْرٌ.',
                    'rubric' => 'Skor 100: Menyebutkan kaidah tafkhim dan tarqiq secara lengkap beserta contoh kata yang benar.',
                    'explanation' => 'Hukum Ahkamur Raa\'at dalam Matan Tuhfatul Athfal dan Jazariyyah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Uraikan 5 pembagian Makharijul Huruf secara umum (Al-Jauf, Al-Halq, Al-Lisan, Asy-Syafatain, Al-Khaisyum) beserta huruf-huruf yang keluar darinya!',
                    'essay_answer' => '1. Al-Jauf (Rongga mulut & tenggorokan): Huruf mad (Alif, Wawu mad, Ya mad). 2. Al-Halq (Tenggorokan): Hamzah, Ha besar, Ain, Ha kecil, Ghain, Kha. 3. Al-Lisan (Lidah): 18 huruf (Qaf, Kaf, Jim, Syin, Ya, Dhad, Lam, Nun, Ra, Tha, Dal, Ta, Shad, Zai, Sin, Zha, Dzal, Tsa). 4. Asy-Syafatain (Bibir): Fa, Wawu, Ba, Mim. 5. Al-Khaisyum (Rongga hidung): Suara dengung (Ghunnah).',
                    'rubric' => 'Skor 100: Menyebutkan kelima makhraj global beserta persebaran hurufnya secara lengkap.',
                    'explanation' => 'Makharijul Huruf Al-\'Ammah dalam Matan Al-Jazariyyah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan perbedaan antara Ghunnah Musyaddadah, Idgham Bighunnah, dan Ikhfa Syafawi beserta kadar harakat dengungnya!',
                    'essay_answer' => '1. Ghunnah Musyaddadah: Huruf Nun dan Mim yang bertasydid asli (contoh: إِنَّ, ثُمَّ), dengung 2 harakat. 2. Idgham Bighunnah: Nun sukun/tanwin bertemu Yanmu (ي ن م و), huruf nun melebur disertai dengung 2 harakat. 3. Ikhfa Syafawi: Mim sukun bertemu huruf Ba (ب), mim disamarkan dengan dengung 2 harakat merapatkan bibir secara ringan.',
                    'rubric' => 'Skor 100: Menerangkan definisi, contoh, dan kadar ghunnah ketiga hukum dengan tepat.',
                    'explanation' => 'Ahkamul Ghunnah dalam Tajwid Al-Qur\'an.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Kelas Muslimah' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Dalam Fiqih Nisa, darah yang keluar dari rahim wanita secara alami pada siklus tertentu tanpa sebab persalinan atau penyakit disebut...',
                    'options' => ['Darah Nifas', 'Darah Haid', 'Darah Istihadhah', 'Darah Wiladah'],
                    'correct_answer' => 1,
                    'explanation' => 'Darah Haid adalah darah alami yang keluar dari rahim wanita sehat pada masa pubertas sesuai siklus bulanan.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Shahabiyah mulia yang dikenal dengan julukan "Dzatun Nithaqain" (Wanita Pemilik Dua Ikat Pinggang) adalah...',
                    'options' => ['Fathimah Az-Zahra RA', 'Aisyah binti Abi Bakr RA', 'Asma binti Abi Bakr RA', 'Khadijah binti Khuwailid RA'],
                    'correct_answer' => 2,
                    'explanation' => 'Asma binti Abi Bakr RA membelah ikat pinggangnya menjadi dua untuk mengikat perbekalan Hijrah Nabi SAW.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Bagaimanakah ketentuan bersuci (thaharah) bagi seorang muslimah yang mengalami kondisi istihadhah sebelum menunaikan shalat fardhu?',
                    'options' => [
                        'Cukup berwudhu sekali untuk seluruh shalat fardhu dalam sehari',
                        'Membersihkan darah, mengenakan pembalut/penahan, dan berwudhu setiap kali masuk waktu shalat fardhu',
                        'Tidak diperkenankan shalat sampai darah berhenti sepenuhnya',
                        'Wajib mandi besar setiap kali hendak shalat lima waktu',
                    ],
                    'correct_answer' => 1,
                    'explanation' => 'Wanita mustahadhah wajib membersihkan darah, menyumbat/membalutnya, lalu berwudhu untuk setiap kali waktu shalat fardhu tiba.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Batas maksimal masa nifas bagi seorang wanita menurut madzhab Syafi\'iyyah adalah selama...',
                    'options' => ['30 Hari', '40 Hari', '60 Hari', '90 Hari'],
                    'correct_answer' => 2,
                    'explanation' => 'Menurut madzhab Syafi\'i, masa nifas umumnya 40 hari dan batas maksimalnya mencapai 60 hari.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum mengqadha puasa Ramadhan bagi wanita yang tidak berpuasa karena haid adalah...',
                    'options' => ['Wajib diqadha di hari lain', 'Cukup membayar fidyah saja', 'Gugur kewajibannya', 'Sunnah diqadha'],
                    'correct_answer' => 0,
                    'explanation' => 'Wanita haid diperintahkan mengqadha puasa dan tidak diperintahkan mengqadha shalat (HR. Muslim).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Berikut ini yang BUKAN merupakan amalan yang boleh dilakukan oleh wanita yang sedang haid adalah...',
                    'options' => ['Berdzikir dan berdoa', 'Membaca buku ilmu agama', 'Menunaikan Thawaf di Ka\'bah', 'Mendengarkan lantunan Al-Qur\'an'],
                    'correct_answer' => 2,
                    'explanation' => 'Thawaf di Ka\'bah mensyaratkan kesucian dari hadats besar dan kecil.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Istri pertama Rasulullah SAW yang senantiasa menenangkan dan mendukung dakwah dengan jiwa raga serta hartanya adalah...',
                    'options' => ['Aisyah binti Abi Bakr', 'Khadijah binti Khuwailid', 'Hafshah binti Umar', 'Ummu Salamah'],
                    'correct_answer' => 1,
                    'explanation' => 'Sayyidah Khadijah RA adalah wanita mulia pertama yang beriman dan menyokong dakwah Nabi SAW.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Surah dalam Al-Qur\'an yang secara khusus memuat banyak panduan hukum keluarga, waris, dan hak-hak wanita adalah...',
                    'options' => ['Surah An-Nisa\'', 'Surah Al-Ma\'idah', 'Surah At-Taubah', 'Surah Al-Anfal'],
                    'correct_answer' => 0,
                    'explanation' => 'Surah An-Nisa\' secara komprehensif menguraikan hak, perlindungan, dan hukum seputar kaum wanita.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Batas minimal masa suci antara dua siklus haid menurut mayoritas ulama fikih adalah selama...',
                    'options' => ['7 Hari', '10 Hari', '15 Hari', '21 Hari'],
                    'correct_answer' => 2,
                    'explanation' => 'Batas minimal masa suci pemisah antara dua siklus haid adalah 15 hari 15 malam.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Shahabiyah yang dikenal sebagai perawi hadits terbanyak dari kalangan wanita dan pakar ilmu fikih adalah...',
                    'options' => ['Sayyidah Aisyah binti Abi Bakr RA', 'Ummu Sulaim RA', 'Zainab binti Jahsy RA', 'Fathimah binti Qais RA'],
                    'correct_answer' => 0,
                    'explanation' => 'Sayyidah Aisyah RA meriwayatkan lebih dari 2.210 hadits dan menjadi rujukan fatwa para sahabat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda berhentinya masa haid yang menunjukkan seorang wanita telah suci (thahir) ditandai dengan...',
                    'options' => ['Keluarnya lendir putih (al-qashshah al-baidha\') atau keringnya kapas bersih', 'Warna darah berubah menjadi kuning pekat', 'Rasa nyeri pinggang mereda', 'Telah melewati 3 hari berturut-turut'],
                    'correct_answer' => 0,
                    'explanation' => 'Tanda suci haid adalah al-jufuf (keringnya tempat keluar darah) atau keluarnya cairan putih jernih.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kewajiban menutup aurat bagi wanita muslimah di hadapan laki-laki bukan mahram ditegaskan dalam surah...',
                    'options' => ['Q.S. Al-Ahzab: 59 & Q.S. An-Nur: 31', 'Q.S. Al-Baqarah: 183', 'Q.S. Ali Imran: 104', 'Q.S. Al-Hujurat: 13'],
                    'correct_answer' => 0,
                    'explanation' => 'Q.S. Al-Ahzab ayat 59 dan Q.S. An-Nur ayat 31 memuat perintah hijab dan menutup aurat bagi mukminah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum shalat seorang wanita yang baru saja menyadari darah haidnya keluar setelah selesai waktu shalat Ashar adalah...',
                    'options' => ['Shalat Asharnya sah dan tidak perlu diqadha', 'Wajib mengqadha shalat Ashar setelah suci', 'Wajib mengulang shalat Zhuhur dan Ashar', 'Membayar denda fidyah'],
                    'correct_answer' => 0,
                    'explanation' => 'Jika shalat ditunaikan saat masih suci sebelum keluar darah, shalatnya sah dan tidak gugur pahalanya.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Gelar mulia yang disematkan kepada Maryam binti Imran dalam Al-Qur\'an atas kesucian dan keteguhan ibadahnya adalah...',
                    'options' => ['Sayyidatu Nisa\'il \'Alamin (Pemimpin Wanita Semesta Alam)', 'Ummul Mukminin', 'Dzatu Hijrah', 'Syafi\'atul Ummah'],
                    'correct_answer' => 0,
                    'explanation' => 'Allah mensucikan Maryam binti Imran di atas seluruh wanita di alam semesta (Q.S. Ali Imran: 42).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum mencukur rambut kepala hingga botak licin bagi wanita menurut mayoritas ulama adalah...',
                    'options' => ['Makruh / Dilarang kecuali ada hajat medis darurat', 'Sunnah Muakkadah', 'Wajib saat tahallul haji', 'Mubah tanpa syarat'],
                    'correct_answer' => 0,
                    'explanation' => 'Rasulullah SAW melarang wanita mencukur gundul kepalanya, dan saat tahallul cukup memotong ujung rambut sepanjang satu ruas jari.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan perbedaan mendasar antara darah haid, nifas, dan istihadhah ditinjau dari karakteristik keluarnya darah serta konsekuensi hukum terhadap pelaksanaan shalat dan puasa!',
                    'essay_answer' => '1. Haid: Darah alami bulanan (warna merah kehitaman, kental), mengharamkan shalat dan puasa (wajib qadha puasa, tidak qadha shalat). 2. Nifas: Darah setelah melahirkan (maksimal 60 hari), hukumnya sama dengan haid. 3. Istihadhah: Darah penyakit di luar siklus haid/nifas (warna merah segar, encer), tetap wajib shalat dan puasa setelah bersuci dan berwudhu setiap waktu shalat.',
                    'rubric' => 'Skor 100: Menjelaskan ketiga jenis darah, karakteristik fisik, dan hukum shalat/puasa serta ketentuan qadha secara sempurna.',
                    'explanation' => 'Berdasarkan hadits riwayat Bukhari & Muslim tentang hukum thaharah dan istihadhah bagi wanita muslimah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan adab dan rambu-rambu syar\'i seorang muslimah dalam berinteraksi sosial dan menjaga kehormatan diri berdasarkan tadabbur Q.S. An-Nur ayat 31!',
                    'essay_answer' => 'Adab muslimah berdasarkan Q.S. An-Nur: 31 meliputi: menundukkan pandangan (ghadhul bashar), menjaga kemaluan, tidak menampakkan perhiasan kecuali yang biasa nampak, mengulurkan jilbab/khimar hingga menutup dada, serta menjaga suara dan cara berjalan dari fitnah.',
                    'rubric' => 'Skor 100: Menyebutkan minimal 4 poin adab beserta keterkaitannya dengan surah An-Nur ayat 31.',
                    'explanation' => 'Q.S. An-Nur: 31 memuat panduan komprehensif bagi wanita mukminah dalam menjaga kehormatan dan aurat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Bagaimanakah tata cara mandi wajib (ghusl) yang sah dan sempurna bagi seorang wanita setelah selesai masa haid sesuai tuntunan sunnah Rasulullah SAW?',
                    'essay_answer' => 'Rukun: 1. Niat mengangkat hadats besar haid, 2. Meratakan air suci ke seluruh tubuh dari ujung rambut hingga ujung kaki. Sunnah: Membasuh kedua tangan, membersihkan kemaluan dari sisa kotoran, berwudhu sempurna, menyela pangkal rambut dengan air wewangian/sabun, mengguyur kepala 3 kali, lalu mengguyur tubuh sisi kanan dan kiri.',
                    'rubric' => 'Skor 100: Menerangkan niat, rukun meratakan air, dan urutan sunnah mandi wajib secara runtut.',
                    'explanation' => 'Berdasarkan hadits riwayat Bukhari dan Muslim dari Sayyidah Aisyah RA tentang tata cara mandi junub/haid.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan hikmah disyariatkannya masa \'Iddah bagi wanita muslimah yang bercerai atau ditinggal wafat oleh suaminya!',
                    'essay_answer' => 'Hikmah masa \'Iddah: 1. Memastikan kebersihan rahim (istibra\' ar-rahim) agar tidak terjadi percampuran nasab anak. 2. Memberikan kesempatan rujuk bagi pasangan suami istri yang bercerai talak raj\'i. 3. Bentuk penghormatan atas ikatan pernikahan dan berkabung atas wafatnya suami. 4. Menjaga hak dan kehormatan wanita serta janin yang dikandung.',
                    'rubric' => 'Skor 100: Menguraikan minimal 3 hikmah utama pensyariatan \'iddah secara komprehensif.',
                    'explanation' => 'Fikih Munakahat dan Ahkamul Usrah dalam Islam.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Uraikan peran dan keteladanan Sayyidah Khadijah Al-Kubra RA dalam membangun ketahanan ekonomi dan emosional keluarga dakwah Nabi Muhammad SAW!',
                    'essay_answer' => 'Khadijah RA adalah pebisnis ulung yang dermawan, memfasilitasi kebutuhan dakwah awal Islam dengan seluruh kekayaannya, menjadi sosok pertama yang mempercayai kenabian saat peristiwa Gua Hira (Zammiluni), dan memberikan ketenteraman jiwa sehingga Nabi SAW dapat fokus menyampaikan risalah tauhid.',
                    'rubric' => 'Skor 100: Menjelaskan kontribusi emosional, spiritual, dan finansial Sayyidah Khadijah RA secara mendalam.',
                    'explanation' => 'Sirah Nabawiyyah karya Ibnu Hisyam & Ar-Rahiq Al-Makhtum.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Nahwu & Sharaf' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kalimah dalam bahasa Arab terbagi menjadi tiga macam, yaitu...',
                    'options' => ['Mubtada, Khabar, Fa\'il', 'Isim, Fi\'il, Huruf', 'Fi\'il Madhi, Mudhari, Amr', 'Marfu\', Manshub, Majrur'],
                    'correct_answer' => 1,
                    'explanation' => 'Menurut kitab Al-Ajurrumiyyah, pembagian kata (al-kalam) terdiri atas Isim (kata benda/sifat), Fi\'il (kata kerja), dan Huruf yang bermakna.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda I\'rab Rafa\' yang asli pada Isim Mufrad adalah...',
                    'options' => ['Fathah', 'Kasrah', 'Dhammah', 'Sukun'],
                    'correct_answer' => 2,
                    'explanation' => 'Tanda asli untuk I\'rab Rafa\' adalah Dhammah, seperti pada kalimat "جَاءَ زَيْدٌ" (Zaid telah datang).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Pada kalimat "قَرَأَ الطَّالِبُ الْقُرْآنَ", kedudukan I\'rab dari kata "الْقُرْآنَ" adalah...',
                    'options' => ['Fa\'il (Marfu\')', 'Maf\'ul Bih (Manshub)', 'Mubtada\' (Marfu\')', 'Mudhaf Ilaih (Majrur)'],
                    'correct_answer' => 1,
                    'explanation' => 'Kata "الْقُرْآنَ" berkedudukan sebagai Maf\'ul Bih (objek penderita) yang berhukum Manshub dengan tanda Fathah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Berikut ini yang merupakan tanda khas dari Isim (kata benda) adalah...',
                    'options' => ['Kemungkinan diawali huruf Qad (قَدْ)', 'Dapat menerima Tanwin dan Alif Lam (ال)', 'Dapat bersambung dengan Ta\' Ta\'nits Sakinah', 'Menerima Jazm'],
                    'correct_answer' => 1,
                    'explanation' => 'Tanda isim antara lain: menerima tanwin, kemasukan alif lam (ال), dan diawali huruf jar.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Fi\'il yang menunjukkan perbuatan yang telah lampau disebut dengan...',
                    'options' => ['Fi\'il Mudhari\'', 'Fi\'il Amr', 'Fi\'il Madhi', 'Fi\'il Nahyi'],
                    'correct_answer' => 2,
                    'explanation' => 'Fi\'il Madhi (فعل ماض) adalah kata kerja lampau, seperti كَتَبَ (dia telah menulis).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum I\'rab dari Isim yang berkedudukan sebagai Fa\'il (Pelaku) adalah...',
                    'options' => ['Manshub', 'Marfu\'', 'Majrur', 'Majzum'],
                    'correct_answer' => 1,
                    'explanation' => 'Fa\'il termasuk dalam kelompok Marfu\'atul Asma\' (isim-isim yang berhukum Rafa\').',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Dalam susunan Idhafah "كِتَابُ الطَّالِبِ", kata "الطَّالِبِ" berkedudukan sebagai...',
                    'options' => ['Mudhaf', 'Mudhaf Ilaih', 'Na\'at', 'Khabar'],
                    'correct_answer' => 1,
                    'explanation' => 'Kata kedua dalam susunan Idhafah disebut Mudhaf Ilaih yang selalu berhukum Majrur.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda Nashab pada Jama\' Muannats Salim (seperti مُسْلِمَاتٍ) adalah...',
                    'options' => ['Fathah', 'Kasrah', 'Ya\'', 'Hadfu Nun'],
                    'correct_answer' => 1,
                    'explanation' => 'Jama\' Muannats Salim ketika Manshub menggunakan tanda Kasrah sebagai pengganti Fathah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Huruf-huruf Jazm yang menjazmkan satu Fi\'il Mudhari\' antara lain...',
                    'options' => ['لَمْ (Lam), لَمَّا (Lamma), لَامُ الْأَمْرِ (Lam Amr), لَا النَّاهِيَةُ (La Nahiyah)', 'أَنْ, لَنْ, إِذَنْ, كَيْ', 'مِنْ, إِلَى, عَنْ, عَلَى', 'إِنْ, أَنَّ, كَأَنَّ, لٰكِنَّ'],
                    'correct_answer' => 0,
                    'explanation' => 'Al-Jawazim yang menjazmkan satu fi\'il adalah Lam, Lamma, Lam Amr, dan La Nahiyah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Amil Nawasih "كَانَ وَأَخَوَاتُهَا" (Kaana dan saudaranya) memiliki fungsi amal...',
                    'options' => ['Merofa\'kan Mubtada\' (Isim Kaana) dan Me-nashab-kan Khabar (Khabar Kaana)', 'Me-nashab-kan Mubtada\' dan Merofa\'kan Khabar', 'Men-jazm-kan dua fi\'il', 'Men-jer-kan seluruh isim'],
                    'correct_answer' => 0,
                    'explanation' => 'Kaana wa akhawatuha tarfa\'ul isma wa tanshibul khabar (ترفع الاسم وتنصب الخبر).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Perubahan bentuk kata dari kata dasar "عَلِمَ" (mengetahui) menjadi "مَعْلُوْمٌ" merupakan contoh pembentukan...',
                    'options' => ['Isim Fa\'il', 'Isim Maf\'ul', 'Isim Makan', 'Isim Alat'],
                    'correct_answer' => 1,
                    'explanation' => 'Wazan مَفْعُوْلٌ merupakan wazan Isim Maf\'ul (objek/yang diketahui) dari fi\'il tsulatsi.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Isim Ghairu Munsharif (Isim yang tidak boleh menerima tanwin) ketika Majrur bertanda...',
                    'options' => ['Kasrah', 'Fathah', 'Dhammah', 'Sukun'],
                    'correct_answer' => 1,
                    'explanation' => 'Isim Ghairu Munsharif (seperti مَسَاجِدَ, إِبْرَاهِيمَ) bertanda Fathah saat Majrur selama tidak ber-alif lam atau di-idhafahkan.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Wazan Fi\'il Madhi Tsulatsi Mazid bi Harfin (tambah 1 huruf hamzah di awal) adalah...',
                    'options' => ['أَفْعَلَ - يُفْعِلُ - إِفْعَالًا', 'فَعَّلَ - يُفَعِّلُ', 'فَاعَلَ - يُفَاعِلُ', 'اِنْفَعَلَ - يَنْفَعِلُ'],
                    'correct_answer' => 0,
                    'explanation' => 'Wazan أَفْعَلَ (seperti أَكْرَمَ - يُكْرِمُ - إِكْرَامًا) adalah bina\' tsulatsi mazid 1 huruf hamzah qatha\'.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tanda I\'rab Rafa\' pada Asma\'ul Khamsah (أَبُوْكَ, أَخُوْكَ, حَمُوْكَ, فُوْكَ, ذُوْ مَالٍ) adalah...',
                    'options' => ['Wawu (و)', 'Alif (ا)', 'Ya\' (ي)', 'Dhammah Muqaddarah'],
                    'correct_answer' => 0,
                    'explanation' => 'Asma\'ul Khamsah di-rafa\'-kan dengan Wawu, di-nashab-kan dengan Alif, dan di-jar-kan dengan Ya\'.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Fi\'il yang memerlukan objek penderita (Maf\'ul Bih) dalam susunan kalimat disebut...',
                    'options' => ['Fi\'il Muta\'addi', 'Fi\'il Lazim', 'Fi\'il Majhul', 'Fi\'il Naqish'],
                    'correct_answer' => 0,
                    'explanation' => 'Fi\'il Muta\'addi adalah kata kerja transitif yang membutuhkan objek (Maf\'ul bih).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan perbedaan mendasar antara Jumlah Ismiyyah dan Jumlah Fi\'liyyah dalam tata bahasa Arab, sertakan masing-masing 1 contoh berharakat lengkap!',
                    'essay_answer' => '1. Jumlah Ismiyyah: Kalimat yang diawali dengan Isim, tersusun dari Mubtada\' dan Khabar. Contoh: اَلْعِلْمُ نُوْرٌ (Ilmu itu cahaya). 2. Jumlah Fi\'liyyah: Kalimat yang diawali dengan Fi\'il, tersusun dari Fi\'il dan Fa\'il (serta Maf\'ul bih jika muta\'addi). Contoh: كَتَبَ التِّلْمِيْذُ الدَّرْسَ (Murid itu menulis pelajaran).',
                    'rubric' => 'Skor 100: Menjelaskan definisi kedua struktur kalimat dan memberikan contoh berharakat dengan benar.',
                    'explanation' => 'Kaidah dasar sintaksis bahasa Arab dalam pembentukan kalimat sempurna (kalam mufid).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Uraikan wazan Tashrif Istilahi untuk Fi\'il Tsulatsi Mujarrad Bab Pertama (فَعَلَ - يَفْعُلُ - فَعْلًا) beserta contoh pembentukan katanya!',
                    'essay_answer' => 'Wazan: فَعَلَ - يَفْعُلُ - فَعْلًا - وَمَفْعَلًا - فَهُوَ فَاعِلٌ - وَذَاكَ مَفْعُوْلٌ - اُفْعُلْ - لَا تَفْعُلْ - مَفْعَلٌ - مَفْعَلٌ - مِفْعَلٌ. Contoh kata نَصَرَ: نَصَرَ - يَنْصُرُ - نَصْرًا - وَمَنْصَرًا - فَهُوَ نَاصِرٌ - وَذَاكَ مَنْصُوْرٌ - اُنْصُرْ - لَا تَنْصُرْ - مَنْصَرٌ - مَنْصَرٌ - مِنْصَرٌ.',
                    'rubric' => 'Skor 100: Menuliskan wazan sharaf lengkap dari fi\'il madhi sampai isim alat beserta contoh konjugasinya.',
                    'explanation' => 'Kaidah morfologi bahasa Arab (Ilmu Sharaf) Bab Nashara-Yanshuru.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan dan jelaskan 4 macam I\'rab (Rafa\', Nashab, Khafadh/Jar, Jazm) beserta pembagian kekhususannya pada Isim dan Fi\'il!',
                    'essay_answer' => '1. Rafa\': Masuk pada Isim dan Fi\'il. 2. Nashab: Masuk pada Isim dan Fi\'il. 3. Khafadh/Jar: Khusus masuk pada Isim (Fi\'il tidak bisa di-jar). 4. Jazm: Khusus masuk pada Fi\'il (Isim tidak bisa di-jazm).',
                    'rubric' => 'Skor 100: Menerangkan 4 jenis i\'rab dan kekhususan isim vs fi\'il secara tepat sesuai Matan Al-Ajurrumiyyah.',
                    'explanation' => 'Kaidah bab Ma\'rifatu \'Alamatil I\'rab dalam kitab Jurrumiyyah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan fungsi dan amalan dari Inna wa Akhawatuha (إِنَّ وَأَخَوَاتُهَا) pada Mubtada dan Khabar serta sebutkan 5 saudaranya!',
                    'essay_answer' => 'Amal Inna: Me-nashab-kan Mubtada\' (disebut Isim Inna) dan Me-rafa\'-kan Khabar (disebut Khabar Inna). Saudaranya: 1. أَنَّ (Taukid), 2. كَأَنَّ (Tasybih/Penyerupaan), 3. لٰكِنَّ (Istidrak/Penyanggahan), 4. لَيْتَ (Tamanni/Pengandaian), 5. لَعَلَّ (Tarajji/Harapan).',
                    'rubric' => 'Skor 100: Menjelaskan amal inna dan menyebutkan 5 saudara inna beserta fungsinya.',
                    'explanation' => 'Bab Inna wa Akhawatuha dalam Jurrumiyyah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan perbedaan antara Jama\' Mudzakkar Salim, Jama\' Muannats Salim, dan Jama\' Taksir beserta contoh perubahan bentuk katanya!',
                    'essay_answer' => '1. Jama\' Mudzakkar Salim: Jamak laki-laki beraturan dengan tambahan Wawu-Nun (مُسْلِمُونَ) saat rafa\' dan Ya-Nun (مُسْلِمِينَ) saat nashab/jar. 2. Jama\' Muannats Salim: Jamak perempuan beraturan dengan tambahan Alif-Ta\' (مُسْلِمَاتٌ). 3. Jama\' Taksir: Jamak tidak beraturan yang pecah dari pola mufradnya (contoh: كِتَابٌ menjadi كُتُبٌ, رَجُلٌ menjadi رِجَالٌ).',
                    'rubric' => 'Skor 100: Menjelaskan ketiga bentuk jamak beserta rumus dan contoh perubahan katanya.',
                    'explanation' => 'Kaidah Pembagian Isim Jamak dalam Bahasa Arab.',
                    'difficulty' => $difficulty,
                ],
            ],

            default => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum tajwid yang terjadi apabila Nun Sukun (نْ) atau Tanwin bertemu dengan huruf Ba\' (ب) adalah...',
                    'options' => ['Izhar Halqi', 'Idgham Bighunnah', 'Iqlab', 'Ikhfa Haqiqi'],
                    'correct_answer' => 2,
                    'explanation' => 'Iqlab adalah menukar bunyi nun sukun atau tanwin menjadi mim sukun dengan ghunnah saat bertemu huruf Ba (ب).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Wilayah Makharijul Huruf "Al-Halq" (Tenggorokan) terbagi menjadi 3 bagian. Huruf yang keluar dari Aqshal Halq (Pangkal Tenggorokan) adalah...',
                    'options' => ['Hamzah (ء) dan Ha\' (هـ)', 'Ain (ع) dan Ha\' (ح)', 'Ghain (غ) dan Kha\' (خ)', 'Qaf (ق) dan Kaf (ك)'],
                    'correct_answer' => 0,
                    'explanation' => 'Aqshal Halq (tenggorokan terdalam dekat pita suara) adalah makhraj dari huruf Hamzah (ء) dan Ha\' besar (هـ).',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Sebutkan 5 huruf Qalqalah dan jelaskan perbedaan antara Qalqalah Sughra, Qalqalah Kubra, dan Qalqalah Akbar disertai masing-masing 1 contoh ayat Al-Qur\'an!',
                    'essay_answer' => 'Huruf Qalqalah: ق, ط, ب, ج, د (Baju Di Toko / Quthbu Jadin). 1. Sughra: Huruf qalqalah sukun di tengah kata (contoh: يَطْمَعُ). 2. Kubra: Huruf qalqalah sukun di akhir kata karena waqaf (contoh: وَالْفَلَقِ). 3. Akbar: Huruf qalqalah bertasydid di akhir kata saat waqaf (contoh: تَبَّتْ يَدَا أَبِي لَهَبٍ وَتَبَّ).',
                    'rubric' => 'Skor 100: Menyebutkan 5 huruf dan menerangkan 3 tingkatan qalqalah beserta contoh ayat yang akurat.',
                    'explanation' => 'Kaidah sifatul huruf Qalqalah (pantulan suara) dalam kitab Matan Al-Jazariyyah.',
                    'difficulty' => $difficulty,
                ],
            ],
        };
    }

    /**
     * Bank Universal Pelengkap Lintas Silabus Al-Hikmah
     */
    protected function getAuxiliaryUniversalBank(string $difficulty): array
    {
        return [
            [
                'type' => 'multiple_choice',
                'question' => 'Berapa jumlah ayat dalam Surah Al-Fatihah termasuk ayat basmalah menurut jumhur ulama pembacaan Makkah dan Kufah?',
                'options' => ['5 Ayat', '6 Ayat', '7 Ayat', '8 Ayat'],
                'correct_answer' => 2,
                'explanation' => 'Surah Al-Fatihah terdiri atas 7 ayat (As-Sab\'ul Matsani).',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Surah terpanjang dalam mushaf Al-Qur\'an yang memuat hukum muamalah dan utang-piutang terpanjang adalah...',
                'options' => ['Surah Al-Baqarah', 'Surah Ali \'Imran', 'Surah An-Nisa\'', 'Surah Al-Ma\'idah'],
                'correct_answer' => 0,
                'explanation' => 'Surah Al-Baqarah adalah surah terpanjang dengan 286 ayat, memuat Ayat Mudayanah (ayat 282).',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Doa yang dibaca ketika sujud tilawah saat mendengar atau membaca ayat sajdah adalah...',
                'options' => [
                    'سَجَدَ وَجْهِيَ لِلَّذِي خَلَقَهُ وَصَوَّرَهُ وَشَقَّ سَمْعَهُ وَبَصَرَهُ',
                    'اَللّٰهُمَّ اغْفِرْ لِي وَارْحَمْنِي',
                    'رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً',
                    'سُبْحَانَ رَبِّيَ الْأَعْلَى وَبِحَمْدِهِ',
                ],
                'correct_answer' => 0,
                'explanation' => 'Doa sujud tilawah: "Sajada wajhiya lilladzi khalaqahu wa shawwarahu wa syaqqa sam\'ahu wa basharahu..."',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Tanda waqaf lazim (diharuskan berhenti) dalam mushaf standar ditandai dengan huruf...',
                'options' => ['Huruf Mim (مـ)', 'Huruf Lam Alif (لا)', 'Huruf Jim (ج)', 'Huruf Shad Lam (صلى)'],
                'correct_answer' => 0,
                'explanation' => 'Tanda huruf Mim kecil (مـ) adalah simbol Waqaf Lazim (harus berhenti).',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Tanda waqaf "Laa" (لا) dalam mushaf Al-Qur\'an memiliki arti petunjuk...',
                'options' => ['Dilarang berhenti (harus lanjut baca)', 'Boleh berhenti boleh lanjut', 'Harus berhenti', 'Berhenti pada salah satu titik'],
                'correct_answer' => 0,
                'explanation' => 'Tanda Lam-Alif (لا) menandakan larangan waqaf jika bukan di akhir ayat.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Nabi yang mendapat gelar "Kalimullah" (yang diajak berbicara langsung oleh Allah) adalah...',
                'options' => ['Nabi Musa AS', 'Nabi Ibrahim AS', 'Nabi Isa AS', 'Nabi Daud AS'],
                'correct_answer' => 0,
                'explanation' => 'Nabi Musa AS digelari Kalimullah sebagaimana dijelaskan dalam Q.S. An-Nisa\': 164.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Surah dalam Al-Qur\'an yang jika dibaca senilai dengan sepertiga Al-Qur\'an karena memurnikan keesaan Allah adalah...',
                'options' => ['Surah Al-Ikhlas', 'Surah Al-Falaq', 'Surah An-Nas', 'Surah Al-Kafirun'],
                'correct_answer' => 0,
                'explanation' => 'Rasulullah SAW bersabda bahwa Surah Al-Ikhlas sebanding dengan sepertiga Al-Qur\'an.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Berikut ini yang merupakan rukun wudhu yang wajib ditunaikan secara tertib adalah...',
                'options' => ['Niat, Membasuh wajah, Membasuh kedua tangan hingga siku, Mengusap sebagian kepala, Membasuh kedua kaki hingga mata kaki, Tertib', 'Berkumur-kumur, Mengusap telinga, Niat', 'Membasuh leher, Niat, Mengusap kepala', 'Membaca basmalah, Berdoa setelah wudhu'],
                'correct_answer' => 0,
                'explanation' => 'Rukun wudhu ada 6 sesuai firman Allah dalam Q.S. Al-Ma\'idah ayat 6.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Kadar zakat fitrah per jiwa yang wajib dikeluarkan menjelang hari raya Idul Fitri adalah setara dengan...',
                'options' => ['1 Sha\' (sekitar 2,5 kg hingga 3 kg beras)', '2 Sha\' kurma', '5 Mud gandum', '1/2 Sha\' jagung'],
                'correct_answer' => 0,
                'explanation' => 'Kewajiban zakat fitrah adalah satu sha\' makanan pokok negeri setempat.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Surah yang dianjurkan untuk dibaca secara rutin setiap malam sebelum tidur untuk melindungi dari siksa kubur adalah...',
                'options' => ['Surah Al-Mulk (Tabarak)', 'Surah As-Sajdah', 'Surah Ya-Sin', 'Surah Al-Waqi\'ah'],
                'correct_answer' => 0,
                'explanation' => 'Hadits shahih menyebutkan bahwa Surah Al-Mulk adalah Al-Mani\'ah (penghalang dari adzab kubur).',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Shalat sunnah yang dikerjakan saat memasuki masjid sebelum duduk disebut shalat...',
                'options' => ['Tahiyyatul Masjid', 'Dhuha', 'Istikharah', 'Hajat'],
                'correct_answer' => 0,
                'explanation' => 'Rasulullah SAW menganjurkan shalat dua rakaat tahiyyatul masjid sebelum duduk.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Kitab suci Taurat diturunkan oleh Allah SWT kepada Nabi...',
                'options' => ['Nabi Musa AS', 'Nabi Daud AS', 'Nabi Isa AS', 'Nabi Ibrahim AS'],
                'correct_answer' => 0,
                'explanation' => 'Taurat diturunkan kepada Nabi Musa AS, Zabur kepada Nabi Daud AS, Injil kepada Nabi Isa AS, dan Al-Qur\'an kepada Nabi Muhammad SAW.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Malaikat yang bertugas meniup sangkakala pada hari kiamat adalah...',
                'options' => ['Malaikat Israfil', 'Malaikat Mikail', 'Malaikat Jibril', 'Malaikat Izrail'],
                'correct_answer' => 0,
                'explanation' => 'Malaikat Israfil bertugas meniup sangkakala tanda tibanya hari kiamat dan kebangkitan.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Bulan dalam kalender Hijriyah di mana umat Islam diwajibkan berpuasa sebulan penuh adalah...',
                'options' => ['Bulan Ramadhan', 'Bulan Syawal', 'Bulan Rajab', 'Bulan Sya\'ban'],
                'correct_answer' => 0,
                'explanation' => 'Kewajiban puasa Ramadhan ditegaskan dalam Q.S. Al-Baqarah ayat 183.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Kota suci tempat kelahiran Nabi Muhammad SAW dan lokasi berdirinya Ka\'bah adalah...',
                'options' => ['Makkah Al-Mukarramah', 'Madinah Al-Munawwarah', 'Baitul Maqdis (Yerusalem)', 'Thaif'],
                'correct_answer' => 0,
                'explanation' => 'Nabi Muhammad SAW lahir di kota Makkah pada Tahun Gajah.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'essay',
                'question' => 'Uraikan 3 keutamaan membaca dan menghafalkan Al-Qur\'an berdasarkan hadits-hadits shahih Rasulullah SAW!',
                'essay_answer' => '1. Al-Qur\'an akan menjadi syafaat bagi pembacanya di hari kiamat (HR. Muslim). 2. Sebaik-baik manusia adalah yang belajar Al-Qur\'an dan mengajarkannya (HR. Bukhari). 3. Orang yang mahir membaca Al-Qur\'an akan bersama para malaikat yang mulia (HR. Bukhari & Muslim).',
                'rubric' => 'Skor 100: Menyebutkan 3 keutamaan beserta substansi hadits shahih secara jelas.',
                'explanation' => 'Fadhailul Qur\'an dalam riwayat hadits Nabawi.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'essay',
                'question' => 'Jelaskan perbedaan antara Rukun Shalat, Syarat Sah Shalat, dan Sunnah Ab\'adh dalam fikih ibadah!',
                'essay_answer' => '1. Syarat Sah: Hal yang harus dipenuhi sebelum shalat (misal: suci dari hadats, menutup aurat, masuk waktu). 2. Rukun Shalat: Bagian pokok di dalam shalat yang jika ditinggalkan shalat batal (misal: Takbiratul ihram, Fatihah, Ruku\', Sujud). 3. Sunnah Ab\'adh: Amalan sunnah yang jika tertinggal dianjurkan diganti dengan Sujud Sahwi (misal: Tasyahud awal, Qunut Subuh).',
                'rubric' => 'Skor 100: Menjelaskan ketiga kategori hukum beserta konsekuensi dan contoh masing-masing.',
                'explanation' => 'Kaidah Fikih Ibadah Mazhab Syafi\'i.',
                'difficulty' => $difficulty,
            ],
            [
                'type' => 'essay',
                'question' => 'Sebutkan Rukun Iman yang 6 secara urut dan berikan penjelasan ringkas tentang Iman kepada Qada dan Qadar!',
                'essay_answer' => '1. Iman kepada Allah, 2. Malaikat-Nya, 3. Kitab-kitab-Nya, 4. Rasul-rasul-Nya, 5. Hari Akhir, 6. Qada dan Qadar. Iman kepada Qada dan Qadar adalah meyakini bahwa segala ketentuan baik dan buruk telah ditetapkan oleh ilmu dan kehendak Allah SWT, disertai kewajiban manusia untuk berusaha dan bertawakal.',
                'rubric' => 'Skor 100: Menyebutkan 6 rukun iman secara urut dan menjelaskan makna takdir secara benar.',
                'explanation' => 'Hadits Jibril \'Alaihissalam tentang Iman, Islam, dan Ihsan.',
                'difficulty' => $difficulty,
            ],
        ];
    }

    /**
     * Generator Butir Soal Tambahan Otomatis & Dinamis Sesuai Program Belajar
     */
    protected function generateSupplementalQuestions(string $program, string $topic, string $difficulty, string $type, int $neededCount): array
    {
        $pool = match ($program) {
            'Bahasa Arab Dasar', 'Nahwu & Sharaf' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Arti dari kosa kata "بَابٌ" (Baabun) dalam bahasa Indonesia adalah...',
                    'options' => ['Pintu', 'Jendela', 'Dinding', 'Atap'],
                    'correct_answer' => 0,
                    'explanation' => 'Kata "بَابٌ" berarti pintu.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Bentuk jamak dari kata "كِتَابٌ" (Kitabun - Sebuah Buku) adalah...',
                    'options' => ['كُتُبٌ (Kutubun)', 'كِتَابَانِ', 'كَاتِبُوْنَ', 'مَكْتَبَةٌ'],
                    'correct_answer' => 0,
                    'explanation' => 'Kutubun (كُتُبٌ) adalah jamak taksir dari kitabun.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Arti dari ungkapan "شُكْرًا جَزِيْلًا" (Syukran jaziilan) adalah...',
                    'options' => ['Terima Kasih Banyak', 'Sama-sama', 'Maafkan Saya', 'Permisi'],
                    'correct_answer' => 0,
                    'explanation' => 'Syukran jaziilan adalah ucapan terima kasih banyak yang dijawab dengan "Afwan".',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Kata "مُسْتَشْفَى" (Mustasyfaa) dalam bahasa Indonesia berarti...',
                    'options' => ['Rumah Sakit', 'Apotek', 'Puskesmas', 'Laboratorium'],
                    'correct_answer' => 0,
                    'explanation' => 'Mustasyfaa berarti rumah sakit.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan perbedaan fungsi antara kata tanya "مَنْ" (Man) dan "مَا" (Maa) beserta contoh kalimatnya!',
                    'essay_answer' => '"مَنْ" digunakan untuk menanyakan makhluk berakal (orang/manusia), contoh: مَنْ هٰذَا؟ هٰذَا مُدَرِّسٌ. Sedangkan "مَا" digunakan untuk benda tidak berakal, contoh: مَا هٰذَا؟ هٰذَا قَلَمٌ.',
                    'rubric' => 'Skor 100: Menjelaskan objek berakal vs tidak berakal beserta contoh kalimat yang benar.',
                    'explanation' => 'Kaidah Asma\'ul Istifham.',
                    'difficulty' => $difficulty,
                ],
            ],

            'Adab & Doa Harian' => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Doa yang dibaca ketika mengenakan pakaian baru memohon kebaikan dan perlindungan dari keburukan adalah...',
                    'options' => [
                        'الْحَمْدُ لِلَّهِ الَّذِي كَسَانِي هَٰذَا الثَّوْبَ وَرَزَقَنِيهِ مِنْ غَيْرِ حَوْلٍ مِنِّي وَلَا قُوَّةٍ',
                        'بِسْمِ اللَّهِ وَلَجْنَا',
                        'اللَّهُمَّ حَاسِبْنِي حِسَابًا يَسِيرًا',
                        'اللَّهُمَّ إِنِّي أَسْأَلُكَ الْعَفْوَ وَالْعَافِيَةَ',
                    ],
                    'correct_answer' => 0,
                    'explanation' => 'Doa mengenakan pakaian memuji Allah yang telah memberikan pakaian tanpa daya dan upaya dari hamba.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Tuliskan doa sebelum dan sesudah makan beserta adab ketika makanan terjatuh!',
                    'essay_answer' => 'Sebelum makan: بِسْمِ اللَّهِ (atau Allahumma baarik lana fiima razaqtana). Sesudah makan: الْحَمْدُ لِلَّهِ الَّذِي أَطْعَمَنَا وَسَقَانَا وَجَعَلَنَا مُسْلِمِينَ. Jika makanan jatuh: Dianjurkan mengambilnya, membersihkan kotoran yang menempel, lalu memakannya tanpa membiarkannya untuk setan.',
                    'rubric' => 'Skor 100: Menuliskan doa dan adab membersihkan makanan yang jatuh sesuai sunnah.',
                    'explanation' => 'Adab Al-Akl wasy Syurb.',
                    'difficulty' => $difficulty,
                ],
            ],

            default => [
                [
                    'type' => 'multiple_choice',
                    'question' => 'Dalam kaidah tajwid, mad yang terjadi apabila ada huruf Mad bertemu dengan huruf sukun karena waqaf di akhir ayat disebut...',
                    'options' => ['Mad \'Aridh Lissukun', 'Mad Iwad', 'Mad Badal', 'Mad Tamkin'],
                    'correct_answer' => 0,
                    'explanation' => 'Mad \'Aridh Lissukun boleh dibaca 2, 4, atau 6 harakat saat berhenti di ujung ayat.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Hukum membaca basmalah pada permulaan Surah At-Taubah (Bara\'ah) menurut para ulama qira\'at adalah...',
                    'options' => ['Tidak dianjurkan / Dilarang (Haram/Makruh)', 'Wajib dibaca', 'Sunnah Muakkadah', 'Mubah'],
                    'correct_answer' => 0,
                    'explanation' => 'Surah At-Taubah diawali dengan pemutusan perjanjian dan ancaman bagi kaum musyrikin sehingga tidak diawali basmalah.',
                    'difficulty' => $difficulty,
                ],
                [
                    'type' => 'essay',
                    'question' => 'Jelaskan tata cara melafalkan hukum bacaan Ikhfa Haqiqi (menyamarkan nun sukun/tanwin) dengan dengung (ghunnah) yang tepat!',
                    'essay_answer' => 'Ikhfa Haqiqi dilafalkan dengan menyamarkan bunyi nun sukun mendekati makhraj huruf ikhfa berikutnya, disertai dengung (ghunnah) sepanjang 2 harakat, di mana suara keluar seimbang antara rongga mulut dan hidung (khaisyum).',
                    'rubric' => 'Skor 100: Menjelaskan posisi makhraj lidah yang menggantung dan proporsi dengung 2 harakat.',
                    'explanation' => 'Kaidah Ahkamun Nunis Sakinati wat Tanwin.',
                    'difficulty' => $difficulty,
                ],
            ],
        };

        $filtered = array_values(array_filter($pool, function ($item) use ($type) {
            if ($type === 'essay') {
                return $item['type'] === 'essay';
            }
            if ($type === 'multiple_choice') {
                return $item['type'] === 'multiple_choice';
            }

            return true;
        }));

        return array_slice($filtered, 0, $neededCount);
    }
}
