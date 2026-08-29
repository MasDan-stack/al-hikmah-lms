<?php

use App\Services\GeminiQuestionService;
use Tests\TestCase;

uses(TestCase::class);

test('gemini service builds structured prompt correctly for multiple choice and essay', function () {
    $service = new GeminiQuestionService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('buildPrompt');
    $method->setAccessible(true);

    $promptMc = $method->invoke($service, 'Tahsin Dasar', 'Hukum Nun Mati', 5, 'Sedang', 'multiple_choice');

    expect($promptMc)->toContain('Hukum Nun Mati')
        ->toContain('Tahsin Dasar')
        ->toContain('5')
        ->toContain('Sedang')
        ->toContain('PILIHAN GANDA');

    $promptEssay = $method->invoke($service, 'Kelas Muslimah', 'Fiqih Thaharah', 3, 'Sulit', 'essay');

    expect($promptEssay)->toContain('Fiqih Thaharah')
        ->toContain('Kelas Muslimah')
        ->toContain('SULIT')
        ->toContain('ESSAY / URAIAN');
});

test('gemini service parses clean multiple choice and essay json response properly', function () {
    $service = new GeminiQuestionService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('parseResponse');
    $method->setAccessible(true);

    $mockJson = json_encode([
        'questions' => [
            [
                'type' => 'multiple_choice',
                'question' => 'Apa itu Idzhar?',
                'options' => ['Jelas', 'Dengung', 'Samar', 'Membalik'],
                'correct_answer' => 0,
                'explanation' => 'Idzhar artinya membaca jelas tanpa dengung.',
                'difficulty' => 'Mudah',
            ],
            [
                'type' => 'essay',
                'question' => 'Jelaskan perbedaan darah haid dan istihadhah!',
                'essay_answer' => 'Haid adalah darah bulanan alami, istihadhah darah penyakit.',
                'rubric' => '100 poin jika lengkap',
                'explanation' => 'Berdasarkan fiqih nisa.',
                'difficulty' => 'Sedang',
            ],
        ],
    ]);

    $result = $method->invoke($service, $mockJson, 'Mudah', 'multiple_choice');

    expect($result)->toBeArray()
        ->and(count($result))->toBe(2)
        ->and($result[0]['type'])->toBe('multiple_choice')
        ->and($result[0]['question'])->toBe('Apa itu Idzhar?')
        ->and($result[0]['correct_answer'])->toBe(0)
        ->and($result[1]['type'])->toBe('essay')
        ->and($result[1]['question'])->toBe('Jelaskan perbedaan darah haid dan istihadhah!')
        ->and($result[1]['essay_answer'])->toContain('Haid adalah darah bulanan');
});

test('gemini service fallback generates curated questions without failing when apikey is missing', function () {
    config(['services.gemini.api_key' => '']);
    putenv('GEMINI_API_KEY=');

    $service = new GeminiQuestionService;

    // Test Kelas Muslimah
    $questions = $service->generateQuestions('Kelas Muslimah', 'Fiqih Nisa Thaharah', 3, 'Sulit', 'essay');

    expect($questions)->toBeArray()
        ->and(count($questions))->toBe(3)
        ->and($questions[0]['type'])->toBe('essay');

    // Test Nahwu & Sharaf
    $nahwuQuestions = $service->generateQuestions('Nahwu & Sharaf', 'Pembagian Kata', 4, 'Sedang', 'multiple_choice');

    expect($nahwuQuestions)->toBeArray()
        ->and(count($nahwuQuestions))->toBe(4)
        ->and($nahwuQuestions[0]['type'])->toBe('multiple_choice');

    // Test 15 questions count guarantee and uniqueness
    $fifteenQuestions = $service->generateQuestions('Tahsin Dasar', 'Makharijul Huruf', 15, 'Sulit', 'multiple_choice');

    expect($fifteenQuestions)->toBeArray()
        ->and(count($fifteenQuestions))->toBe(15);

    $uniqueQuestions = array_unique(array_column($fifteenQuestions, 'question'));
    expect(count($uniqueQuestions))->toBe(15);
});

test('multi-provider ai detects providers correctly based on configuration and model names', function () {
    // 1. Explicit DeepSeek Provider
    config(['services.ai.provider' => 'deepseek', 'services.ai.api_key' => 'sk-test-deepseek', 'services.ai.model' => 'deepseek-chat']);
    $deepseekService = new GeminiQuestionService;
    expect($deepseekService->getActiveProvider())->toBe('deepseek')
        ->and($deepseekService->getActiveModel())->toBe('deepseek-chat');

    // 2. Explicit Qwen Provider
    config(['services.ai.provider' => 'qwen', 'services.ai.api_key' => 'sk-test-qwen', 'services.ai.model' => 'qwen-plus']);
    $qwenService = new GeminiQuestionService;
    expect($qwenService->getActiveProvider())->toBe('qwen')
        ->and($qwenService->getActiveModel())->toBe('qwen-plus');

    // 3. Explicit Claude Provider
    config(['services.ai.provider' => 'claude', 'services.ai.api_key' => 'sk-ant-test', 'services.ai.model' => 'claude-3-5-sonnet-20241022']);
    $claudeService = new GeminiQuestionService;
    expect($claudeService->getActiveProvider())->toBe('claude')
        ->and($claudeService->getActiveModel())->toBe('claude-3-5-sonnet-20241022');

    // 4. Explicit OpenAI Provider
    config(['services.ai.provider' => 'openai', 'services.ai.api_key' => 'sk-test-openai', 'services.ai.model' => 'gpt-4o-mini']);
    $openaiService = new GeminiQuestionService;
    expect($openaiService->getActiveProvider())->toBe('openai')
        ->and($openaiService->getActiveModel())->toBe('gpt-4o-mini');

    // 5. Auto detection by Claude API key prefix
    config(['services.ai.provider' => 'auto', 'services.ai.api_key' => 'sk-ant-api03-12345', 'services.ai.model' => 'claude-3-5-haiku-20241022']);
    $autoClaude = new GeminiQuestionService;
    expect($autoClaude->getActiveProvider())->toBe('claude');

    // 6. Auto detection by DeepSeek model name
    config(['services.ai.provider' => 'auto', 'services.ai.api_key' => 'sk-ds-key-123', 'services.ai.model' => 'deepseek-reasoner']);
    $autoDeepseek = new GeminiQuestionService;
    expect($autoDeepseek->getActiveProvider())->toBe('deepseek');

    // 7. Auto detection when only QWEN_API_KEY is populated
    config([
        'services.ai.provider' => 'auto',
        'services.ai.api_key' => '',
        'services.ai.gemini.api_key' => '',
        'services.ai.deepseek.api_key' => '',
        'services.ai.claude.api_key' => '',
        'services.ai.openai.api_key' => '',
        'services.ai.qwen.api_key' => 'sk-ws-test-qwen-123',
        'services.ai.qwen.model' => 'qwen-plus',
    ]);
    $autoQwen = new GeminiQuestionService;
    expect($autoQwen->getActiveProvider())->toBe('qwen')
        ->and($autoQwen->getActiveModel())->toBe('qwen-plus');
});

test('curated questions bank returns 100% accurate program-specific topic questions for all programs', function () {
    config(['services.gemini.api_key' => '']);
    putenv('GEMINI_API_KEY=');
    $service = new GeminiQuestionService;

    // 1. Bahasa Arab Dasar -> MUST be Arabic vocabulary / grammar / conversation, NOT Tajwid
    $arabicQuestions = $service->generateQuestions('Bahasa Arab Dasar', 'Mufrodat & Dhomir', 15, 'Sedang', 'multiple_choice');
    expect($arabicQuestions)->toBeArray()->and(count($arabicQuestions))->toBe(15);
    $arabicTexts = implode(' ', array_column($arabicQuestions, 'question'));
    expect($arabicTexts)->toContain('bahasa Arab')
        ->and($arabicTexts)->not->toContain('Nun Sukun')
        ->and($arabicTexts)->not->toContain('Makharijul Huruf');

    // 2. Adab & Doa Harian
    $adabQuestions = $service->generateQuestions('Adab & Doa Harian', 'Doa & Adab Harian', 5, 'Sedang', 'multiple_choice');
    expect($adabQuestions)->toBeArray()->and(count($adabQuestions))->toBe(5);
    $adabTexts = implode(' ', array_column($adabQuestions, 'question'));
    expect($adabTexts)->toContain('Doa');

    // 3. Tahfidz Al-Qur\'an
    $tahfidzQuestions = $service->generateQuestions('Tahfidz Al-Qur\'an', 'Juz 30', 5, 'Sedang', 'multiple_choice');
    expect($tahfidzQuestions)->toBeArray()->and(count($tahfidzQuestions))->toBe(5);
    $tahfidzTexts = implode(' ', array_column($tahfidzQuestions, 'question'));
    expect($tahfidzTexts)->toContain('Surah');

    // 4. Iqra & Dasar Al-Qur\'an
    $iqraQuestions = $service->generateQuestions('Iqra & Dasar Al-Qur\'an', 'Pengenalan Huruf', 5, 'Sedang', 'multiple_choice');
    expect($iqraQuestions)->toBeArray()->and(count($iqraQuestions))->toBe(5);
    $iqraTexts = implode(' ', array_column($iqraQuestions, 'question'));
    expect($iqraTexts)->toContain('Huruf');
});
