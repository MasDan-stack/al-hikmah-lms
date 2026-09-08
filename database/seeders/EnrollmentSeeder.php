<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

        $fatimah = Mentor::where('full_name', 'like', '%Fatimah%')->first();
        $ahmad = Mentor::where('full_name', 'like', '%Ahmad%')->first();
        $hasan = Mentor::where('full_name', 'like', '%Hasan%')->first();

        // 1. Ensure students exist
        $fathan = Student::where('full_name', 'like', '%Fathan%')->first();
        $aisyah = Student::where('full_name', 'like', '%Aisyah%')->first();
        $rayhan = Student::where('full_name', 'like', '%Rayhan%')->first();

        // 2. Create Hikmatul Hasanah if not exists
        $hikmahParentUser = User::firstOrCreate(
            ['email' => 'wali.hikmah@gmail.com'],
            [
                'name' => 'Bunda Siti Hikmah',
                'password' => Hash::make('password'),
                'role_id' => $parentRole->id,
                'phone' => '081234567899',
            ]
        );

        $hikmahParent = ParentProfile::firstOrCreate(
            ['user_id' => $hikmahParentUser->id],
            [
                'address' => 'Jl. Margonda Raya No. 45, Depok',
                'emergency_phone' => '081234567899',
            ]
        );

        $hikmahStudentUser = User::firstOrCreate(
            ['email' => 'hikmatulhasanah@alhikmah.com'],
            [
                'name' => 'Hikmatul Hasanah',
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
                'phone' => '081234567899',
            ]
        );

        $hikmatul = Student::firstOrCreate(
            ['user_id' => $hikmahStudentUser->id],
            [
                'parent_id' => $hikmahParent->id,
                'full_name' => 'Hikmatul Hasanah',
                'age' => 7,
                'gender' => 'P',
                'location' => 'Depok',
                'notes' => 'Belajar membaca Iqra dari dasar.',
            ]
        );

        // 3. Ensure Fatimah has Muhammad Fathan on Monday (Slot 4) and Aisyah Humaira on Tuesday (Slot 4)
        if ($fatimah && $fathan) {
            DB::table('mentor_student')->updateOrInsert(
                ['mentor_id' => $fatimah->id, 'student_id' => $fathan->id],
                [
                    'day_assigned' => 'monday',
                    'slot_number' => 4,
                    'time_label' => '16:00',
                    'time_assigned' => '16:30:00',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        if ($fatimah && $aisyah) {
            DB::table('mentor_student')->updateOrInsert(
                ['mentor_id' => $fatimah->id, 'student_id' => $aisyah->id],
                [
                    'day_assigned' => 'tuesday',
                    'slot_number' => 4,
                    'time_label' => '16:00',
                    'time_assigned' => '16:30:00',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Ahmad has Rayhan on Monday (Slot 4)
        if ($ahmad && $rayhan) {
            DB::table('mentor_student')->updateOrInsert(
                ['mentor_id' => $ahmad->id, 'student_id' => $rayhan->id],
                [
                    'day_assigned' => 'monday',
                    'slot_number' => 4,
                    'time_label' => '16:00',
                    'time_assigned' => '16:30:00',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // 4. Ensure mentor availabilities slot_numbers
        if ($fatimah) {
            foreach (['monday', 'tuesday'] as $day) {
                $av = MentorAvailability::where('mentor_id', $fatimah->id)->where('day', $day)->first();
                if ($av) {
                    $av->update([
                        'slot_numbers' => [4],
                        'is_available' => true,
                        'is_holiday' => false,
                    ]);
                }
            }
        }

        $program = Program::first() ?? Program::create([
            'name' => 'Iqra & Dasar Al-Qur\'an',
            'category' => 'tahsin',
            'price' => 350000,
            'is_active' => true,
        ]);

        // Seed some prior enrollments so Hikmatul gets ID 8
        $dummyStudents = [$fathan, $aisyah, $rayhan];
        while (Enrollment::count() < 7) {
            $currCount = Enrollment::count() + 1;
            $s = $dummyStudents[($currCount - 1) % count($dummyStudents)];
            Enrollment::create([
                'student_id' => $s->id,
                'program_id' => $program->id,
                'learning_method' => 'online',
                'requested_days' => ['monday'],
                'requested_time' => '10:00:00',
                'status' => EnrollmentStatus::ACTIVE,
                'paid_at' => now()->subDays(10),
            ]);
        }

        // Enrollment #8
        Enrollment::firstOrCreate(
            ['id' => 8],
            [
                'student_id' => $hikmatul->id,
                'program_id' => $program->id,
                'learning_method' => 'online',
                'requested_days' => ['monday', 'tuesday'],
                'requested_time' => '16:00:00',
                'parent_notes' => 'Mohon guru yang sabar untuk anak usia 7 tahun.',
                'status' => EnrollmentStatus::WAITING_ADMIN,
            ]
        );
    }
}
