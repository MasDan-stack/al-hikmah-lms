<?php

use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorInterventionTicket;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);

    // Admin
    $this->adminUser = User::factory()->create([
        'name' => 'Admin Pengawas',
        'role_id' => $this->adminRole->id,
        'phone' => '081299990000',
    ]);

    // Parent
    $this->parentUser = User::factory()->create([
        'name' => 'Ibu Khadijah',
        'role_id' => $this->parentRole->id,
        'phone' => '081288887777',
    ]);
    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->parentUser->id,
        'mother_name' => 'Ibu Khadijah',
        'phone' => '081288887777',
    ]);

    // Mentor
    $this->mentorUser = User::factory()->create([
        'name' => 'Ust. Hasan Basri',
        'role_id' => $this->mentorRole->id,
        'phone' => '081277776666',
    ]);
    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ust. Hasan Basri',
        'status' => 'active',
        'is_active' => true,
        'rating' => 4.90,
    ]);

    // Student
    $this->studentUser = User::factory()->create([
        'name' => 'Santri Bilal',
        'role_id' => $this->studentRole->id,
    ]);
    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Santri Bilal',
        'age' => 10,
        'gender' => 'L',
    ]);

    $this->program = Program::firstOrCreate(
        ['name' => 'Tahfidz Intensif'],
        ['slug' => 'tahfidz-intensif', 'price' => 300000, 'duration_months' => 3, 'is_active' => true]
    );

    Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'mentor_id' => $this->mentor->id,
        'status' => 'active',
        'payment_status' => 'paid',
    ]);

    // Completed Session
    $this->session = Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Sesi Evaluasi Surah Al-Mulk',
        'date' => today()->subDay()->toDateString(),
        'time' => '17:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);
});

test('low rating feedback creates automatic intervention ticket with critical/high severity', function () {
    $mockWa = Mockery::mock(WhatsAppService::class);
    $mockWa->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($phone, $msg) {
            return str_contains($msg, 'TIKET INTERVENSI KOMPLAIN WALI SANTRI')
                && str_contains($msg, 'Ust. Hasan Basri');
        })
        ->andReturn(true);

    $this->app->instance(WhatsAppService::class, $mockWa);

    $response = $this->actingAs($this->parentUser)->post(route('parent.feedbacks.store'), [
        'session_id' => $this->session->id,
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'overall_rating' => 2,
        'comment' => 'Pengajaran kurang memuaskan hari ini.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $ticket = MentorInterventionTicket::where('mentor_id', $this->mentor->id)->first();
    expect($ticket)->not->toBeNull();
    expect($ticket->status)->toBe('open');
    expect($ticket->severity)->toBe('high');
    expect($ticket->ticket_number)->toStartWith('TIK-');
});

test('feedback with complaint keywords triggers ticket creation even with rating 3', function () {
    $mockWa = Mockery::mock(WhatsAppService::class);
    $mockWa->shouldReceive('sendMessage')->once()->andReturn(true);
    $this->app->instance(WhatsAppService::class, $mockWa);

    $response = $this->actingAs($this->parentUser)->post(route('parent.feedbacks.store'), [
        'session_id' => $this->session->id,
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'overall_rating' => 3,
        'comment' => 'Ustadz sering terlambat 15 menit dan tampak terburu-buru.',
    ]);

    $ticket = MentorInterventionTicket::where('mentor_id', $this->mentor->id)->latest()->first();
    expect($ticket)->not->toBeNull();
    expect($ticket->complaint_category)->toBe('attendance_late');
    expect($ticket->detected_keywords)->toContain('sering terlambat');
});

test('admin can view and update ticket status with action plan and resolution notes', function () {
    $uniq = strtoupper(substr(uniqid(), -4));
    $ticket = MentorInterventionTicket::create([
        'ticket_number' => "TIK-20260906-{$uniq}",
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'parent_id' => $this->parentUser->id,
        'session_id' => $this->session->id,
        'severity' => 'high',
        'complaint_category' => 'attendance_late',
        'status' => 'open',
        'parent_comment' => 'Ustadz sering terlambat',
    ]);

    // Admin view index
    $indexRes = $this->actingAs($this->adminUser)->get(route('admin.tickets.index'));
    $indexRes->assertStatus(200);
    $indexRes->assertSee("#TIK-20260906-{$uniq}");

    // Admin view show
    $showRes = $this->actingAs($this->adminUser)->get(route('admin.tickets.show', $ticket->id));
    $showRes->assertStatus(200);
    $showRes->assertSee('Tindak Lanjut & Rekonsiliasi', false);

    // Admin update status to resolved
    $updateRes = $this->actingAs($this->adminUser)->post(route('admin.tickets.update', $ticket->id), [
        'status' => 'resolved',
        'action_plan' => 'Mengingatkan guru hadir tepat waktu.',
        'resolution_notes' => 'Wali santri telah dikonfirmasi dan menyetujui jadwal baru.',
    ]);

    $updateRes->assertRedirect(route('admin.tickets.show', $ticket->id));
    $updateRes->assertSessionHas('success');

    $ticket->refresh();
    expect($ticket->status)->toBe('resolved');
    expect($ticket->resolved_at)->not->toBeNull();
    expect($ticket->handled_by)->toBe($this->adminUser->id);
});

test('admin can escalate ticket to student mutation which registers in student mutation logs', function () {
    $uniq = strtoupper(substr(uniqid(), -4));
    $ticket = MentorInterventionTicket::create([
        'ticket_number' => "TIK-20260906-{$uniq}",
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'parent_id' => $this->parentUser->id,
        'session_id' => $this->session->id,
        'severity' => 'critical',
        'complaint_category' => 'attitude_pedagogy',
        'status' => 'open',
        'parent_comment' => 'Minta ganti guru karena tidak cocok.',
    ]);

    $response = $this->actingAs($this->adminUser)->post(route('admin.tickets.escalate', $ticket->id), [
        'escalation_notes' => 'Mediasi tidak menemukan kesepakatan, orang tua meminta guru baru.',
    ]);

    $response->assertRedirect(route('admin.tickets.show', $ticket->id));
    $response->assertSessionHas('warning');

    $ticket->refresh();
    expect($ticket->status)->toBe('escalated_to_mutation');

    // Check student mutation logs table
    $mutationLog = DB::table('student_mutation_logs')
        ->where('previous_mentor_id', $this->mentor->id)
        ->where('student_id', $this->student->id)
        ->first();

    expect($mutationLog)->not->toBeNull();
    expect($mutationLog->reason_category)->toBe('dissatisfaction');
    expect($mutationLog->notes)->toContain($ticket->ticket_number);
});
