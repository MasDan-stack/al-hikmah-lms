<?php

use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('roles are seeded correctly', function () {
    expect(Role::count())->toBe(4);
    expect(Role::pluck('name')->toArray())->toContain('admin', 'mentor', 'parent', 'student');
});

test('user role helper methods work correctly', function () {
    $admin = User::where('email', 'admin@alhikmah.com')->first();
    $mentor = User::where('email', 'ustadz.ahmad@alhikmah.com')->first();
    $parent = User::where('email', 'orangtua@alhikmah.com')->first();
    $student = User::where('email', 'santri.fathan@alhikmah.com')->first();

    expect($admin->isAdmin())->toBeTrue();
    expect($mentor->isMentor())->toBeTrue();
    expect($parent->isParent())->toBeTrue();
    expect($student->isStudent())->toBeTrue();

    expect($admin->isMentor())->toBeFalse();
});

test('user relationships with profiles exist', function () {
    $mentorUser = User::where('email', 'ustadz.ahmad@alhikmah.com')->first();
    $parentUser = User::where('email', 'orangtua@alhikmah.com')->first();
    $studentUser = User::where('email', 'santri.fathan@alhikmah.com')->first();

    expect($mentorUser->mentor)->toBeInstanceOf(Mentor::class);
    expect($parentUser->parentProfile)->toBeInstanceOf(ParentProfile::class);
    expect($studentUser->student)->toBeInstanceOf(Student::class);
    expect($studentUser->student->parent_id)->toBe($parentUser->parentProfile->id);
});

test('role middleware allows authorized user and denies unauthorized user', function () {
    Route::get('/test-admin-route', fn () => response('Admin Dashboard'))
        ->middleware(['web', 'auth', 'role:admin']);

    $admin = User::where('email', 'admin@alhikmah.com')->first();
    $student = User::where('email', 'santri.fathan@alhikmah.com')->first();

    $this->actingAs($admin)
        ->get('/test-admin-route')
        ->assertStatus(200)
        ->assertSee('Admin Dashboard');

    $this->actingAs($student)
        ->get('/test-admin-route')
        ->assertStatus(403);
});
