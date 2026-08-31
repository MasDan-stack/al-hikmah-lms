<?php

use App\Enums\Role as RoleEnum;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use App\Services\GeminiQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);

    $this->mentor = User::factory()->create([
        'role_id' => $mentorRole->id,
    ]);

    $this->program = Program::create([
        'name' => 'Tahsin & Tajwid Al-Qur\'an',
        'category' => 'dewasa',
        'description' => 'Program penguasaan tajwid dan makharijul huruf komprehensif.',
        'duration_weeks' => 8,
        'price' => 200000,
        'level' => 'Menengah',
        'is_popular' => true,
        'is_active' => true,
    ]);
});

test('mentor question generator page renders configured multi-ai providers', function () {
    $response = $this->actingAs($this->mentor)->get(route('mentor.questions.generate'));

    $response->assertStatus(200)
        ->assertSee('Pilih Engine AI Generator')
        ->assertSee('DeepSeek AI')
        ->assertSee('Alibaba Qwen AI')
        ->assertSee('Google Gemini AI')
        ->assertSee('OpenAI ChatGPT')
        ->assertSee('Auto (Smart Cascade Multi-AI)');
});

test('mentor can preview questions with specific ai provider selected', function () {
    $mockService = Mockery::mock(GeminiQuestionService::class);
    $mockService->shouldReceive('generateQuestions')
        ->once()
        ->with('Tahsin & Tajwid Al-Qur\'an', 'Makharijul Huruf', 5, 'Sedang', 'multiple_choice', 'deepseek')
        ->andReturn([
            [
                'type' => 'multiple_choice',
                'question' => 'Manakah yang termasuk huruf Halqiyah (tenggorokan)?',
                'options' => ['ء هـ ع ح غ خ', 'ب م و ف', 'ت ث د ذ', 'ق ك'],
                'correct_answer' => 0,
                'explanation' => 'Huruf Halqiyah terbagi menjadi 3 tingkatan tenggorokan.',
                'difficulty' => 'Sedang',
            ],
        ]);
    $mockService->shouldReceive('getActiveProvider')->andReturn('deepseek');
    $mockService->shouldReceive('getActiveModel')->andReturn('deepseek-chat');
    $mockService->shouldReceive('isFallbackUsed')->andReturn(false);
    $mockService->shouldReceive('getLastError')->andReturn(null);

    $this->app->instance(GeminiQuestionService::class, $mockService);

    $response = $this->actingAs($this->mentor)
        ->postJson(route('mentor.questions.preview'), [
            'program_id' => $this->program->id,
            'topic' => 'Makharijul Huruf',
            'count' => 5,
            'difficulty' => 'Sedang',
            'question_type' => 'multiple_choice',
            'ai_provider' => 'deepseek',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'active_provider' => 'deepseek',
            'active_model' => 'deepseek-chat',
            'is_fallback' => false,
        ])
        ->assertJsonPath('data.0.question', 'Manakah yang termasuk huruf Halqiyah (tenggorokan)?');
});

test('smart cascade fallback seamlessly generates curated questions if all ai fail', function () {
    $service = new GeminiQuestionService;

    $questions = $service->generateQuestions(
        program: $this->program->name,
        topic: 'Hukum Nun Mati & Tanwin',
        count: 5,
        difficulty: 'Sedang',
        questionType: 'multiple_choice',
        requestedProvider: 'auto'
    );

    expect($questions)->toBeArray()
        ->and(count($questions))->toBe(5)
        ->and($questions[0])->toHaveKeys(['type', 'question', 'options', 'correct_answer', 'explanation']);
});
