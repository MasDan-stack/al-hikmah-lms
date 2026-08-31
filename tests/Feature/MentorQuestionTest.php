<?php

use App\Enums\Role as RoleEnum;
use App\Models\Program;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use App\Services\GeminiQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
    $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);

    $this->mentor = User::factory()->create([
        'role_id' => $mentorRole->id,
    ]);

    $this->student = User::factory()->create([
        'role_id' => $studentRole->id,
    ]);

    $this->program = Program::create([
        'name' => 'Kelas Tajwid Al-Hikmah',
        'category' => 'anak',
        'description' => 'Program belajar tajwid dasar hingga mahir.',
        'duration_weeks' => 12,
        'price' => 150000,
        'level' => 'Pemula',
        'is_popular' => true,
        'is_active' => true,
    ]);
});

test('non mentor cannot access mentor bank soal page', function () {
    $response = $this->actingAs($this->student)->get(route('mentor.questions.index'));
    $response->assertStatus(403);
});

test('mentor can view bank soal page', function () {
    $response = $this->actingAs($this->mentor)->get(route('mentor.questions.index'));
    $response->assertStatus(200);
    $response->assertSee('Bank Soal Evaluasi');
});

test('mentor can view ai question generator page', function () {
    $response = $this->actingAs($this->mentor)->get(route('mentor.questions.generate'));
    $response->assertStatus(200);
    $response->assertSee('AI Auto-Generate Soal');
});

test('mentor can preview ai generated questions with mocked service', function () {
    $mockService = Mockery::mock(GeminiQuestionService::class);
    $mockService->shouldReceive('generateQuestions')
        ->once()
        ->with('Kelas Tajwid Al-Hikmah', 'Hukum Nun Mati', 5, 'Sedang', 'multiple_choice', 'auto')
        ->andReturn([
            [
                'type' => 'multiple_choice',
                'question' => 'Apabila nun sukun bertemu huruf ba (ب), maka hukum bacaannya adalah...',
                'options' => ['Idgham Bighunnah', 'Iqlab', 'Izhar Halqi', 'Ikhfa Haqiqi'],
                'correct_answer' => 1,
                'explanation' => 'Iqlab terjadi jika nun mati/tanwin bertemu huruf ba, dibaca mendengung dengan suara mim.',
                'difficulty' => 'Sedang',
            ],
        ]);
    $mockService->shouldReceive('getActiveProvider')->andReturn('qwen');
    $mockService->shouldReceive('getActiveModel')->andReturn('qwen-plus');
    $mockService->shouldReceive('isFallbackUsed')->andReturn(false);
    $mockService->shouldReceive('getLastError')->andReturn(null);

    $this->app->instance(GeminiQuestionService::class, $mockService);

    $response = $this->actingAs($this->mentor)
        ->postJson(route('mentor.questions.preview'), [
            'program_id' => $this->program->id,
            'topic' => 'Hukum Nun Mati',
            'count' => 5,
            'difficulty' => 'Sedang',
            'question_type' => 'multiple_choice',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonPath('data.0.question', 'Apabila nun sukun bertemu huruf ba (ب), maka hukum bacaannya adalah...');
});

test('mentor can store batch multiple choice and essay questions to database', function () {
    $payload = [
        'program_id' => $this->program->id,
        'topic' => 'Hukum Nun Mati & Thaharah',
        'difficulty' => 'Sulit',
        'questions' => [
            [
                'type' => 'multiple_choice',
                'question' => 'Apabila nun sukun bertemu huruf ba (ب), maka hukum bacaannya adalah...',
                'options' => ['Idgham Bighunnah', 'Iqlab', 'Izhar Halqi', 'Ikhfa Haqiqi'],
                'correct_answer' => 1,
                'explanation' => 'Iqlab terjadi jika nun mati/tanwin bertemu huruf ba.',
            ],
            [
                'type' => 'essay',
                'question' => 'Jelaskan perbedaan haid dan nifas serta konsekuensinya pada ibadah shalat!',
                'essay_answer' => 'Haid adalah darah siklus alami, nifas darah pasca persalinan.',
                'rubric' => '100 poin jika menyertakan dalil.',
                'explanation' => 'Kaidah fiqih thaharah.',
            ],
        ],
    ];

    $response = $this->actingAs($this->mentor)
        ->post(route('mentor.questions.store-batch'), $payload);

    $response->assertRedirect(route('mentor.questions.index'));

    $this->assertDatabaseHas('questions', [
        'user_id' => $this->mentor->id,
        'program_id' => $this->program->id,
        'type' => 'multiple_choice',
        'correct_answer' => 1,
    ]);

    $this->assertDatabaseHas('questions', [
        'user_id' => $this->mentor->id,
        'program_id' => $this->program->id,
        'type' => 'essay',
        'essay_answer' => 'Haid adalah darah siklus alami, nifas darah pasca persalinan.',
    ]);
});

test('mentor can view printable worksheet page', function () {
    $question = Question::create([
        'program_id' => $this->program->id,
        'user_id' => $this->mentor->id,
        'topic' => 'Tajwid Dasar',
        'difficulty' => 'Sedang',
        'type' => 'multiple_choice',
        'question' => 'Hukum bacaan mim sukun bertemu mim adalah...',
        'options' => ['Idgham Mimi', 'Ikhfa Syafawi', 'Izhar Syafawi', 'Iqlab'],
        'correct_answer' => 0,
        'explanation' => 'Idgham mimi/mutamatsilain.',
    ]);

    $response = $this->actingAs($this->mentor)->get(route('mentor.questions.print', [
        'program_id' => $this->program->id,
        'topic' => 'Tajwid Dasar',
    ]));

    $response->assertStatus(200);
    $response->assertSee('LEMBAR EVALUASI');
    $response->assertSee('Hukum bacaan mim sukun bertemu mim adalah...');
    $response->assertSee('Idgham Mimi');
});

test('mentor can soft delete question to trash and restore it', function () {
    $question = Question::create([
        'program_id' => $this->program->id,
        'user_id' => $this->mentor->id,
        'topic' => 'Hukum Qalqalah',
        'difficulty' => 'Mudah',
        'type' => 'multiple_choice',
        'question' => 'Huruf qalqalah terbagi menjadi...',
        'options' => ['5 huruf', '6 huruf', '7 huruf', '4 huruf'],
        'correct_answer' => 0,
        'created_by_ai' => true,
    ]);

    // Soft Delete
    $deleteResponse = $this->actingAs($this->mentor)
        ->delete(route('mentor.questions.destroy', $question->id));

    $deleteResponse->assertRedirect(route('mentor.questions.index'));
    $this->assertSoftDeleted('questions', ['id' => $question->id]);

    // Restore
    $restoreResponse = $this->actingAs($this->mentor)
        ->post(route('mentor.questions.restore', $question->id));

    $restoreResponse->assertRedirect(route('mentor.questions.trash'));
    $this->assertDatabaseHas('questions', ['id' => $question->id, 'deleted_at' => null]);
});

test('preview returns validation errors when input is invalid', function () {
    $response = $this->actingAs($this->mentor)
        ->postJson(route('mentor.questions.preview'), [
            'program_id' => 99999, // non-existent
            'count' => 50, // max 25
            'difficulty' => 'InvalidDifficulty',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['program_id', 'count', 'difficulty']);
});
