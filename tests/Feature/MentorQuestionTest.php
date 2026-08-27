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
        'category' => 'Tajwid',
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
        ->with('Kelas Tajwid Al-Hikmah', 'Hukum Nun Mati', 5, 'Sedang')
        ->andReturn([
            [
                'question' => 'Apabila nun sukun bertemu huruf ba (ب), maka hukum bacaannya adalah...',
                'options' => ['Idgham Bighunnah', 'Iqlab', 'Izhar Halqi', 'Ikhfa Haqiqi'],
                'correct_answer' => 1,
                'explanation' => 'Iqlab terjadi jika nun mati/tanwin bertemu huruf ba, dibaca mendengung dengan suara mim.',
                'difficulty' => 'Sedang',
            ],
        ]);

    $this->app->instance(GeminiQuestionService::class, $mockService);

    $response = $this->actingAs($this->mentor)
        ->postJson(route('mentor.questions.preview'), [
            'program_id' => $this->program->id,
            'topic' => 'Hukum Nun Mati',
            'count' => 5,
            'difficulty' => 'Sedang',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonPath('data.0.question', 'Apabila nun sukun bertemu huruf ba (ب), maka hukum bacaannya adalah...');
});

test('mentor can store batch questions to database', function () {
    $payload = [
        'program_id' => $this->program->id,
        'topic' => 'Hukum Nun Mati',
        'difficulty' => 'Sedang',
        'questions' => [
            [
                'question' => 'Apabila nun sukun bertemu huruf ba (ب), maka hukum bacaannya adalah...',
                'options' => ['Idgham Bighunnah', 'Iqlab', 'Izhar Halqi', 'Ikhfa Haqiqi'],
                'correct_answer' => 1,
                'explanation' => 'Iqlab terjadi jika nun mati/tanwin bertemu huruf ba.',
            ],
        ],
    ];

    $response = $this->actingAs($this->mentor)
        ->post(route('mentor.questions.store-batch'), $payload);

    $response->assertRedirect(route('mentor.questions.index'));
    $this->assertDatabaseHas('questions', [
        'user_id' => $this->mentor->id,
        'program_id' => $this->program->id,
        'topic' => 'Hukum Nun Mati',
        'correct_answer' => 1,
    ]);
});

test('mentor can soft delete question to trash and restore it', function () {
    $question = Question::create([
        'program_id' => $this->program->id,
        'user_id' => $this->mentor->id,
        'topic' => 'Hukum Qalqalah',
        'difficulty' => 'Mudah',
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
            'topic' => 'ab', // min 3
            'count' => 30, // max 20
            'difficulty' => 'InvalidDifficulty',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['program_id', 'topic', 'count', 'difficulty']);
});

test('preview returns error json when ai service throws exception', function () {
    $mockService = Mockery::mock(GeminiQuestionService::class);
    $mockService->shouldReceive('generateQuestions')
        ->once()
        ->andThrow(new Exception('Gemini API Connection failed'));

    $this->app->instance(GeminiQuestionService::class, $mockService);

    $response = $this->actingAs($this->mentor)
        ->postJson(route('mentor.questions.preview'), [
            'program_id' => $this->program->id,
            'topic' => 'Hukum Idgham',
            'count' => 5,
            'difficulty' => 'Sedang',
        ]);

    $response->assertStatus(500)
        ->assertJson([
            'status' => 'error',
            'code' => 'GEMINI_API_ERROR',
            'message' => 'Gemini API Connection failed',
        ]);
});

test('mentor can force delete question from trash', function () {
    $question = Question::create([
        'program_id' => $this->program->id,
        'user_id' => $this->mentor->id,
        'topic' => 'Hukum Qalqalah',
        'difficulty' => 'Mudah',
        'question' => 'Contoh qalqalah sugro adalah...',
        'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
        'correct_answer' => 0,
    ]);

    $question->delete();

    $response = $this->actingAs($this->mentor)
        ->delete(route('mentor.questions.force-delete', $question->id));

    $response->assertRedirect(route('mentor.questions.trash'));
    $this->assertDatabaseMissing('questions', ['id' => $question->id]);
});

test('mentor cannot delete another mentors question', function () {
    $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value]);
    $otherMentor = User::factory()->create(['role_id' => $mentorRole->id]);

    $question = Question::create([
        'program_id' => $this->program->id,
        'user_id' => $otherMentor->id,
        'topic' => 'Hukum Tajwid',
        'difficulty' => 'Sedang',
        'question' => 'Soal dari mentor lain',
        'options' => ['A', 'B', 'C', 'D'],
        'correct_answer' => 0,
    ]);

    $response = $this->actingAs($this->mentor)
        ->delete(route('mentor.questions.destroy', $question->id));

    $response->assertStatus(403);
});
