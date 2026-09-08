<?php

use App\Models\MentorApplication;
use App\Models\MentorTestSession;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->application = MentorApplication::factory()->create([
        'status' => 'document_review',
        'specialization' => 'Tahsin',
    ]);
});

test('admin can generate test and ai creates question session', function () {
    // Mock the Gemini API call
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode([
                                'questions' => [
                                    [
                                        'question' => 'Jelaskan hukum nun mati?',
                                        'options' => ['Izhar', 'Idgham', 'Iqlab', 'Ikhfa'],
                                        'correct_answer' => 0,
                                        'explanation' => 'Nun mati memiliki 4 hukum bacaan.',
                                        'difficulty' => 'Sedang',
                                    ],
                                ],
                            ])],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.tests.generate', $this->application->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_test_sessions', [
        'application_id' => $this->application->id,
        'status' => 'in_progress',
    ]);

    $this->assertDatabaseHas('mentor_applications', [
        'id' => $this->application->id,
        'status' => 'test_scheduled',
    ]);
});

test('admin can evaluate test session and advance stage', function () {
    $testSession = MentorTestSession::create([
        'application_id' => $this->application->id,
        'scheduled_at' => now(),
        'status' => 'in_progress',
        'duration_minutes' => 45,
    ]);

    $this->application->update(['status' => 'test_scheduled']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.tests.evaluate', $testSession->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_test_sessions', [
        'id' => $testSession->id,
        'status' => 'completed',
        'score' => 85.00,
    ]);

    $this->assertDatabaseHas('mentor_applications', [
        'id' => $this->application->id,
        'status' => 'test_completed',
    ]);
});
