<?php

use App\Enums\Role as RoleEnum;
use App\Models\Mentor;
use App\Models\MentorLeave;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => 'Pendamping / Guru']);
    $this->adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => 'Admin']);
    $this->parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => 'Orang Tua / Wali']);

    $this->mentorUser = User::factory()->create(['role_id' => $this->mentorRole->id]);
    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ustadz Zaid',
        'specialization' => 'Tahsin & Tahfidz',
        'status' => 'active',
        'is_active' => true,
    ]);

    $this->substituteUser = User::factory()->create(['role_id' => $this->mentorRole->id]);
    $this->substituteMentor = Mentor::create([
        'user_id' => $this->substituteUser->id,
        'full_name' => 'Ustadz Umar',
        'specialization' => 'Bahasa Arab & Tahsin',
        'status' => 'active',
        'is_active' => true,
    ]);

    $this->adminUser = User::factory()->create(['role_id' => $this->adminRole->id]);
});

test('mentor can view their leave management page', function () {
    $this->actingAs($this->mentorUser)
        ->get(route('mentor.leaves.index'))
        ->assertStatus(200)
        ->assertSee('Pengajuan Cuti &')
        ->assertSee('Guru Pengganti');
});

test('mentor can submit a single day leave request', function () {
    $leaveDate = Carbon::tomorrow()->format('Y-m-d');

    $response = $this->actingAs($this->mentorUser)
        ->post(route('mentor.leaves.store'), [
            'start_date' => $leaveDate,
            'reason' => 'Menghadiri wisuda keluarga',
        ]);

    $response->assertRedirect(route('mentor.leaves.index'));
    $response->assertSessionHas('success');

    $leave = MentorLeave::where('mentor_id', $this->mentor->id)
        ->where('leave_date', $leaveDate)
        ->first();

    expect($leave)->not->toBeNull()
        ->and($leave->reason)->toBe('Menghadiri wisuda keluarga')
        ->and($leave->status)->toBe('pending');
});

test('mentor can submit multi-day date range leave request', function () {
    $startDate = Carbon::tomorrow()->format('Y-m-d');
    $endDate = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

    $response = $this->actingAs($this->mentorUser)
        ->post(route('mentor.leaves.store'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Safar keluar kota 3 hari',
        ]);

    $response->assertRedirect(route('mentor.leaves.index'));
    $response->assertSessionHas('success');

    $count = MentorLeave::where('mentor_id', $this->mentor->id)
        ->where('status', 'pending')
        ->count();

    expect($count)->toBe(3);
});

test('mentor can cancel their pending leave request', function () {
    $leave = MentorLeave::create([
        'mentor_id' => $this->mentor->id,
        'leave_date' => Carbon::tomorrow()->format('Y-m-d'),
        'reason' => 'Keperluan mendadak',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->mentorUser)
        ->delete(route('mentor.leaves.destroy', $leave->id));

    $response->assertRedirect(route('mentor.leaves.index'));
    $response->assertSessionHas('success');

    expect(MentorLeave::find($leave->id))->toBeNull();
});

test('admin can view mentor leaves management page', function () {
    $this->actingAs($this->adminUser)
        ->get(route('admin.mentors.leaves.index'))
        ->assertStatus(200)
        ->assertSee('Manajemen Cuti &')
        ->assertSee('Guru Pengganti');
});

test('admin can approve mentor leave and assign substitute mentor', function () {
    $leave = MentorLeave::create([
        'mentor_id' => $this->mentor->id,
        'leave_date' => Carbon::tomorrow()->format('Y-m-d'),
        'reason' => 'Sakit demam',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.mentors.leaves.approve', $leave->id), [
            'substitute_mentor_id' => $this->substituteMentor->id,
            'notify_parents' => 0,
        ]);

    $response->assertRedirect(route('admin.mentors.leaves.index'));
    $response->assertSessionHas('success');

    $leave->refresh();
    expect($leave->status)->toBe('approved')
        ->and($leave->substitute_mentor_id)->toBe($this->substituteMentor->id);
});

test('admin can reject mentor leave with reason', function () {
    $leave = MentorLeave::create([
        'mentor_id' => $this->mentor->id,
        'leave_date' => Carbon::tomorrow()->format('Y-m-d'),
        'reason' => 'Cuti liburan',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.mentors.leaves.reject', $leave->id), [
            'rejection_note' => 'Jadwal tasmi santri tidak dapat digeser',
        ]);

    $response->assertRedirect(route('admin.mentors.leaves.index'));
    $response->assertSessionHas('success');

    $leave->refresh();
    expect($leave->status)->toBe('rejected')
        ->and($leave->reason)->toContain('Catatan Admin: Jadwal tasmi santri tidak dapat digeser');
});

test('non mentor cannot access mentor leave page', function () {
    $parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);

    $this->actingAs($parentUser)
        ->get(route('mentor.leaves.index'))
        ->assertStatus(403);
});
