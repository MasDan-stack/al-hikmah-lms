<?php

use App\Services\GeminiQuestionService;
use Tests\TestCase;

uses(TestCase::class);

test('gemini service builds structured prompt correctly', function () {
    $service = new GeminiQuestionService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('buildPrompt');
    $method->setAccessible(true);

    $prompt = $method->invoke($service, 'Tahsin Al-Hikmah', 'Hukum Nun Mati', 5, 'Sedang');

    expect($prompt)->toContain('Hukum Nun Mati')
        ->toContain('Tahsin Al-Hikmah')
        ->toContain('5')
        ->toContain('Sedang');
});

test('gemini service parses clean json response properly', function () {
    $service = new GeminiQuestionService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('parseResponse');
    $method->setAccessible(true);

    $mockJson = json_encode([
        'topic' => 'Hukum Idzhar',
        'program' => 'Tahsin Dasar',
        'total_questions' => 1,
        'questions' => [
            [
                'question' => 'Apa itu Idzhar?',
                'options' => ['Jelas', 'Dengung', 'Samar', 'Membalik'],
                'correct_answer' => 0,
                'explanation' => 'Idzhar artinya membaca jelas tanpa dengung.',
                'difficulty' => 'Mudah',
            ],
        ],
    ]);

    $result = $method->invoke($service, $mockJson, 'Mudah');

    expect($result)->toBeArray()
        ->and(count($result))->toBe(1)
        ->and($result[0]['question'])->toBe('Apa itu Idzhar?')
        ->and($result[0]['correct_answer'])->toBe(0)
        ->and($result[0]['options'])->toHaveCount(4)
        ->and($result[0]['explanation'])->toBe('Idzhar artinya membaca jelas tanpa dengung.');
});

test('gemini service parses markdown wrapped json properly', function () {
    $service = new GeminiQuestionService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('parseResponse');
    $method->setAccessible(true);

    $rawText = "```json\n".json_encode([
        'questions' => [
            [
                'question' => 'Huruf Iqlab adalah...',
                'options' => ['Ba', 'Nun', 'Mim', 'Lam'],
                'correct_answer' => 0,
                'explanation' => 'Huruf iqlab hanya satu yaitu ba (ب).',
                'difficulty' => 'Sedang',
            ],
        ],
    ])."\n```";

    $result = $method->invoke($service, $rawText, 'Sedang');

    expect($result)->toBeArray()
        ->and(count($result))->toBe(1)
        ->and($result[0]['question'])->toBe('Huruf Iqlab adalah...')
        ->and($result[0]['options'][0])->toBe('Ba');
});

test('gemini service throws exception on invalid json', function () {
    $service = new GeminiQuestionService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('parseResponse');
    $method->setAccessible(true);

    expect(fn () => $method->invoke($service, 'NOT A JSON STRING', 'Sedang'))
        ->toThrow(Exception::class, 'Format respons dari AI bukan JSON yang valid.');
});

test('gemini service throws exception when apiKey is missing', function () {
    config(['services.gemini.api_key' => '']);
    putenv('GEMINI_API_KEY=');

    $service = new GeminiQuestionService;

    expect(fn () => $service->generateQuestions('Tahsin', 'Hukum Mad', 5, 'Sedang'))
        ->toThrow(Exception::class, 'Layanan AI Generator belum diaktifkan oleh Administrator sistem.');
});
