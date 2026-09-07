<?php

namespace Tests\Unit;

use App\Models\Mentor;
use App\Models\Student;
use App\Models\User;
use App\Services\MentorAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MentorAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_and_retrieve_mentor_availability(): void
    {
        $u = User::factory()->create(['name' => 'Ust. Budi']);
        $mentor = Mentor::create(['user_id' => $u->id, 'full_name' => $u->name, 'is_active' => true]);

        $service = app(MentorAvailabilityService::class);

        $saved = $service->saveAvailability($mentor->id, [
            'availability' => [
                'monday' => [1, 2, 3, 4],
                'tuesday' => [5],
                'wednesday' => [1, 2, 3, 4],
                'thursday' => [1, 2, 3, 4, 5, 6],
                'friday' => [2, 3, 5, 6],
                'saturday' => [3, 4, 5],
                'sunday' => [1, 2, 3, 4, 5, 6],
            ],
            'max_students' => 5,
        ]);

        $this->assertCount(7, $saved);
        $this->assertEquals([1, 2, 3, 4], $saved['monday']->slot_numbers);
        $this->assertEquals([5], $saved['tuesday']->slot_numbers);

        $retrieved = $service->getMentorAvailability($mentor->id);
        $this->assertCount(7, $retrieved);
        $this->assertEquals([1, 2, 3, 4], $retrieved['monday']->slot_numbers);
    }

    public function test_parse_whatsapp_format_with_various_separators(): void
    {
        $service = app(MentorAvailabilityService::class);

        $waText = "
Nama : Ust. Abdullah
senin : 1 2 3 4
selasa - 5
Rabu = 1 2 3 4
Kamis: 1 2 3 4 5 6
Jum'at : 2 3 5 6
Sabtu: 3 4 5
Ahad : 1 2 3 4 5 6
";

        $parsed = $service->parseWhatsAppFormat($waText);

        $this->assertEquals([1, 2, 3, 4], $parsed['monday']);
        $this->assertEquals([5], $parsed['tuesday']);
        $this->assertEquals([1, 2, 3, 4], $parsed['wednesday']);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $parsed['thursday']);
        $this->assertEquals([2, 3, 5, 6], $parsed['friday']);
        $this->assertEquals([3, 4, 5], $parsed['saturday']);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $parsed['sunday']);
    }

    public function test_export_to_whatsapp_format(): void
    {
        $u = User::factory()->create(['name' => 'Ust. Ridwan']);
        $mentor = Mentor::create(['user_id' => $u->id, 'full_name' => $u->name, 'is_active' => true]);

        $service = app(MentorAvailabilityService::class);
        $service->saveAvailability($mentor->id, [
            'availability' => [
                'monday' => [1, 2],
                'tuesday' => [5],
            ],
            'max_students' => 5,
        ]);

        $exported = $service->exportToWhatsAppFormat($mentor->id);

        $this->assertStringContainsString('Nama : Ust. Ridwan', $exported);
        $this->assertStringContainsString('Senin : 1 2', $exported);
        $this->assertStringContainsString('Selasa : 5', $exported);
        $this->assertStringContainsString('0. 05:00', $exported);
    }

    public function test_anti_conflict_validation_on_same_student_same_slot(): void
    {
        $u1 = User::factory()->create(['name' => 'Ust. A']);
        $m1 = Mentor::create(['user_id' => $u1->id, 'full_name' => $u1->name, 'is_active' => true]);

        $u2 = User::factory()->create(['name' => 'Ust. B']);
        $m2 = Mentor::create(['user_id' => $u2->id, 'full_name' => $u2->name, 'is_active' => true]);

        $stUser = User::factory()->create();
        $student = Student::create(['user_id' => $stUser->id, 'full_name' => 'Santri Alokasi', 'age' => 10]);

        $service = app(MentorAvailabilityService::class);
        $service->saveAvailability($m1->id, ['availability' => ['monday' => [1]], 'max_students' => 5]);
        $service->saveAvailability($m2->id, ['availability' => ['monday' => [1]], 'max_students' => 5]);

        // Alokasi santri ke Guru 1 di Senin Slot 1 -> Berhasil
        $service->assignStudent([
            'mentor_id' => $m1->id,
            'student_id' => $student->id,
            'day' => 'monday',
            'slot_number' => 1,
        ]);

        // Coba alokasi santri yang sama ke Guru 2 di Senin Slot 1 -> Gagal karena bentrok
        $this->expectException(ValidationException::class);
        $service->assignStudent([
            'mentor_id' => $m2->id,
            'student_id' => $student->id,
            'day' => 'monday',
            'slot_number' => 1,
        ]);
    }
}
