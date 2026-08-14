MODUL JADWAL & MANAJEMEN KETERSEDIAAN GURU (MENTOR AVAILABILITY & SCHEDULING SYSTEM)
AL-HIKMAH LEARNING MANAGEMENT SYSTEM (LMS)
📋 Document Control
Property	Details
Project Title	AL-HIKMAH LMS - Mentor Availability & Scheduling Module
Document Version	1.0 (Production Blueprint)
Status	Draft for Review
Date	12 Agustus 2026
Target Framework	Laravel 12	Livewire 4.3	Bootstrap 5	MySQL
1. EXECUTIVE SUMMARY
1.1 Problem Statement
Saat ini, AL-HIKMAH LMS memiliki 2 masalah kritis dalam manajemen jadwal dan alokasi guru:

🔴 Issue #1: Admin Tidak Tahu Ketersediaan Guru
"Misal ada santri baru, otomatis kan gue mencari guru pendamping yang kosong dong, sedangkan gue gak tau hari apa aja si guru/pendamping itu kosong, senin-minggu itu, misal 1 hari libur kita anggap minggu libur, berarti kan senin-sabtu saya sebagai admin harus tau si Guru/pendamping itu."

Dampak:

Admin tidak bisa mengalokasikan santri baru ke guru yang tepat

Admin tidak tahu jadwal kosong guru (hari apa saja yang tersedia)

Risiko overbooking (guru kelebihan murid di hari yang sama)

Proses alokasi santri baru menjadi lambat dan tidak efisien

🔴 Issue #2: Guru Tidak Tahu Daftar Santri & Orang Tua Binaannya
"Misal gue sebagai Guru/Pendamping kan gak tau santri mana aja yang gue ajarkan, dan orang tua mana juga gak tau."

Dampak:

Guru tidak memiliki visibilitas daftar santri binaan

Guru tidak tahu orang tua/wali dari santri yang diajar

Komunikasi antara guru dan orang tua menjadi sulit

Guru tidak bisa mempersiapkan materi untuk santri yang akan diajar

1.2 Proposed Solution
Membangun Modul Manajemen Ketersediaan & Jadwal Guru yang terintegrasi dengan sistem yang sudah ada:

Untuk Admin:
Melihat Ketersediaan Guru - Admin bisa melihat hari apa saja guru tersedia mengajar

Melihat Beban Mengajar Guru - Admin bisa melihat berapa banyak santri yang sudah dialokasikan ke guru tertentu

Mengalokasikan Santri ke Guru - Admin bisa memilih guru berdasarkan ketersediaan dan beban mengajar

Untuk Guru/Mentor:
Melihat Daftar Santri Binaan - Guru bisa melihat semua santri yang diajar

Melihat Informasi Orang Tua - Guru bisa melihat kontak orang tua dari santri binaan

Mengatur Ketersediaan - Guru bisa mengatur hari dan jam mengajar yang tersedia

1.3 Success Criteria (KPIs)
KPI	Target	Metrik Pengukuran
Alokasi Santri Baru	< 5 menit	Waktu admin mengalokasikan santri ke guru
Visibilitas Guru	100%	Guru tahu semua santri & orang tua binaan
Overbooking	0%	Tidak ada guru yang kelebihan murid di hari yang sama
Ketersediaan Terupdate	100%	Guru update jadwal ketersediaan minimal 1x/bulan
Efisiensi Admin	+80%	Waktu admin mencari guru kosong berkurang drastis
2. USER EXPERIENCE & FUNCTIONALITY
2.1 User Personas
Persona	Deskripsi	Kebutuhan Utama
Admin Lembaga	Pengelola operasional harian	Melihat ketersediaan guru & mengalokasikan santri
Ustadz Ahmad (Mentor)	Guru/pengajar Al-Qur'an	Melihat daftar santri binaan & orang tua, mengatur jadwal
2.2 User Stories & Acceptance Criteria
✅ A. MODUL KETERSEDIAAN GURU (MENTOR AVAILABILITY)
User Story:

Sebagai Admin, saya ingin melihat ketersediaan guru (hari apa saja yang kosong) dan beban mengajar mereka, agar saya bisa mengalokasikan santri baru ke guru yang tepat.

Acceptance Criteria:

ID	Kriteria	Deskripsi
AC-A1	Tampilan Kalender Ketersediaan	Admin bisa melihat kalender 7 hari (Senin-Minggu) dengan status ketersediaan setiap guru
AC-A2	Status Ketersediaan	Setiap hari memiliki status: Tersedia, Penuh, Libur, Tidak Tersedia
AC-A3	Beban Mengajar	Tampilkan jumlah santri yang dialokasikan ke guru per hari
AC-A4	Filter & Sorting	Admin bisa filter berdasarkan status, hari, atau spesialisasi guru
AC-A5	Alokasi Santri	Admin bisa langsung alokasikan santri ke guru dari tampilan kalender
✅ B. MODUL DAFTAR SANTRI & ORANG TUA (MENTOR STUDENT LIST)
User Story:

Sebagai Guru/Mentor, saya ingin melihat daftar semua santri yang saya ajar beserta informasi orang tua/wali mereka, agar saya bisa mempersiapkan materi dan berkomunikasi dengan orang tua.

Acceptance Criteria:

ID	Kriteria	Deskripsi
AC-B1	Daftar Santri Binaan	Guru melihat daftar lengkap santri yang diajar
AC-B2	Informasi Santri	Nama, usia, program, level, progres terakhir
AC-B3	Informasi Orang Tua	Nama orang tua, nomor WhatsApp, alamat
AC-B4	Status Bimbingan	Aktif/Tidak Aktif, jumlah sesi, rata-rata nilai
AC-B5	Aksi Cepat	Tombol: Hubungi Orang Tua, Catat Progres, Lihat Detail
✅ C. MODUL PENGATURAN JADWAL GURU (MENTOR SCHEDULE SETTING)
User Story:

Sebagai Guru/Mentor, saya ingin mengatur hari dan jam mengajar saya, agar Admin tahu kapan saya tersedia dan tidak terjadi overbooking.

Acceptance Criteria:

ID	Kriteria	Deskripsi
AC-C1	Halaman Pengaturan Jadwal	Guru bisa mengatur ketersediaan per hari (Senin-Minggu)
AC-C2	Jam Mengajar	Guru bisa menentukan jam mulai dan jam selesai mengajar
AC-C3	Kuota Murid	Guru bisa menentukan maksimal murid per hari
AC-C4	Hari Libur	Guru bisa menandai hari libur/tidak tersedia
AC-C5	Validasi	Sistem memastikan tidak ada konflik jadwal
✅ D. MODUL DASHBOARD MENTOR (ENHANCEMENT)
User Story:

Sebagai Guru/Mentor, saya ingin di dashboard melihat ringkasan santri binaan dan orang tua, serta jadwal mengajar minggu ini.

Acceptance Criteria:

ID	Kriteria	Deskripsi
AC-D1	Widget Santri Binaan	Tampilkan jumlah santri + daftar singkat di dashboard
AC-D2	Widget Orang Tua	Tampilkan daftar orang tua dari santri binaan
AC-D3	Jadwal Minggu Ini	Tampilkan jadwal mengajar minggu ini
AC-D4	Link Cepat	Tombol ke daftar santri, daftar orang tua, pengaturan jadwal
3. TECHNICAL SPECIFICATIONS
3.1 Database Schema
🆕 NEW TABLE: mentor_availabilities
php
Schema::create('mentor_availabilities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
    $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
    $table->time('start_time')->nullable();
    $table->time('end_time')->nullable();
    $table->integer('max_students')->default(5);
    $table->boolean('is_available')->default(true);
    $table->boolean('is_holiday')->default(false);
    $table->string('notes')->nullable();
    $table->timestamps();
    
    // Unique: 1 mentor hanya punya 1 record per hari
    $table->unique(['mentor_id', 'day']);
    
    // Index untuk query cepat
    $table->index(['mentor_id', 'day', 'is_available']);
});
🔧 ALTER TABLE: mentor_student (Pivot)
php
Schema::table('mentor_student', function (Blueprint $table) {
    // Tambah kolom untuk tracking
    $table->enum('day_assigned', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
          ->nullable()->after('is_active');
    $table->time('time_assigned')->nullable()->after('day_assigned');
});
🔧 ALTER TABLE: mentors
php
Schema::table('mentors', function (Blueprint $table) {
    // Total kapasitas murid per hari (default)
    $table->integer('default_max_students_per_day')->default(5)->after('status');
});
3.2 Models
📄 Model: MentorAvailability.php
php
<?php
// app/Models/MentorAvailability.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'day',
        'start_time',
        'end_time',
        'max_students',
        'is_available',
        'is_holiday',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
        'is_holiday' => 'boolean',
    ];

    public const DAYS = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
    ];

    public const DAYS_ORDER = [
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    // Helper: cek apakah mentor tersedia di hari tertentu
    public function isAvailable(): bool
    {
        return $this->is_available && !$this->is_holiday;
    }

    // Helper: dapatkan label hari
    public function getDayLabelAttribute(): string
    {
        return self::DAYS[$this->day] ?? $this->day;
    }

    // Scope: hanya yang tersedia
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->where('is_holiday', false);
    }

    // Scope: berdasarkan hari
    public function scopeOnDay($query, $day)
    {
        return $query->where('day', $day);
    }
}
📄 Model: Mentor (Update)
php
<?php
// app/Models/Mentor.php - TAMBAHKAN RELASI & HELPER

namespace App\Models;

// ... existing code

class Mentor extends Model
{
    // ... existing fillable, casts

    // Relasi ke Availability
    public function availabilities(): HasMany
    {
        return $this->hasMany(MentorAvailability::class, 'mentor_id');
    }

    // Relasi ke Students melalui pivot
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'mentor_student', 'mentor_id', 'student_id')
                    ->withPivot('day_assigned', 'time_assigned', 'is_active')
                    ->wherePivot('is_active', true);
    }

    // Helper: cek ketersediaan di hari tertentu
    public function isAvailableOn($day): bool
    {
        $availability = $this->availabilities()->onDay($day)->first();
        return $availability ? $availability->isAvailable() : false;
    }

    // Helper: dapatkan jumlah murid di hari tertentu
    public function getStudentCountOnDay($day): int
    {
        return $this->students()
            ->wherePivot('day_assigned', $day)
            ->wherePivot('is_active', true)
            ->count();
    }

    // Helper: cek apakah masih ada kuota di hari tertentu
    public function hasQuotaOnDay($day): bool
    {
        $availability = $this->availabilities()->onDay($day)->first();
        if (!$availability || !$availability->isAvailable()) {
            return false;
        }

        $currentCount = $this->getStudentCountOnDay($day);
        return $currentCount < $availability->max_students;
    }

    // Helper: dapatkan daftar hari yang tersedia
    public function getAvailableDays(): array
    {
        return $this->availabilities()
            ->available()
            ->pluck('day')
            ->toArray();
    }

    // Helper: dapatkan total murid aktif
    public function getActiveStudentsCount(): int
    {
        return $this->students()->wherePivot('is_active', true)->count();
    }
}
📄 Model: Student (Update)
php
<?php
// app/Models/Student.php - TAMBAHKAN RELASI & HELPER

namespace App\Models;

// ... existing code

class Student extends Model
{
    // ... existing fillable, casts

    // Relasi ke Mentor melalui pivot
    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mentor_student', 'student_id', 'mentor_id')
                    ->withPivot('day_assigned', 'time_assigned', 'is_active')
                    ->wherePivot('is_active', true);
    }

    // Helper: dapatkan mentor utama (pertama)
    public function getMainMentorAttribute()
    {
        return $this->mentors()->first();
    }

    // Helper: dapatkan hari mengajar
    public function getTeachingDayAttribute(): ?string
    {
        $pivot = $this->mentors()->first()?->pivot;
        return $pivot ? $pivot->day_assigned : null;
    }
}
3.3 Controllers
📄 Controller: Admin/MentorAvailabilityController.php
php
<?php
// app/Http/Controllers/Admin/MentorAvailabilityController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorAvailability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentorAvailabilityController extends Controller
{
    // Tampilan Kalender Ketersediaan
    public function index(Request $request)
    {
        $mentors = User::where('role', 'mentor')
            ->with(['mentorProfile', 'availabilities', 'students'])
            ->get();

        $days = MentorAvailability::DAYS_ORDER;
        $dayLabels = MentorAvailability::DAYS;

        // Data untuk kalender
        $availabilityData = [];
        foreach ($mentors as $mentor) {
            $row = [];
            foreach ($days as $day) {
                $availability = $mentor->availabilities->firstWhere('day', $day);
                $studentCount = $mentor->students()
                    ->wherePivot('day_assigned', $day)
                    ->wherePivot('is_active', true)
                    ->count();

                $row[$day] = [
                    'availability' => $availability,
                    'student_count' => $studentCount,
                    'is_available' => $availability ? $availability->isAvailable() : false,
                    'has_quota' => $availability ? $mentor->hasQuotaOnDay($day) : false,
                    'max_students' => $availability?->max_students ?? 0,
                ];
            }
            $availabilityData[$mentor->id] = [
                'mentor' => $mentor,
                'schedule' => $row,
            ];
        }

        return view('admin.mentors.availability', compact(
            'availabilityData',
            'days',
            'dayLabels'
        ));
    }

    // Alokasikan Santri ke Mentor
    public function assignStudent(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'mentor_id' => 'required|exists:users,id',
            'day' => 'required|string|in:' . implode(',', MentorAvailability::DAYS_ORDER),
        ]);

        $mentor = User::findOrFail($validated['mentor_id']);
        $student = Student::findOrFail($validated['student_id']);

        // Cek ketersediaan mentor di hari tersebut
        if (!$mentor->hasQuotaOnDay($validated['day'])) {
            return back()->with('error', 'Mentor tidak tersedia atau kuota penuh di hari tersebut.');
        }

        // Cek apakah student sudah punya mentor di hari itu
        $existing = DB::table('mentor_student')
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return back()->with('error', 'Santri ini sudah memiliki mentor aktif.');
        }

        // Assign student ke mentor
        DB::table('mentor_student')->insert([
            'mentor_id' => $validated['mentor_id'],
            'student_id' => $validated['student_id'],
            'day_assigned' => $validated['day'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.mentors.availability')
            ->with('success', "Santri berhasil dialokasikan ke {$mentor->name} pada hari " . MentorAvailability::DAYS[$validated['day']]);
    }

    // Unassign Student dari Mentor
    public function unassignStudent(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'mentor_id' => 'required|exists:users,id',
        ]);

        DB::table('mentor_student')
            ->where('mentor_id', $validated['mentor_id'])
            ->where('student_id', $validated['student_id'])
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return back()->with('success', 'Santri berhasil dilepaskan dari mentor.');
    }

    // API: Get Available Mentors for a Day (AJAX)
    public function getAvailableMentors(Request $request)
    {
        $day = $request->query('day');

        $mentors = User::where('role', 'mentor')
            ->with(['mentorProfile', 'availabilities', 'students'])
            ->get()
            ->filter(function ($mentor) use ($day) {
                return $mentor->hasQuotaOnDay($day);
            });

        return response()->json($mentors->map(function ($mentor) {
            return [
                'id' => $mentor->id,
                'name' => $mentor->name,
                'specialization' => $mentor->mentorProfile?->specialization,
                'available_days' => $mentor->getAvailableDays(),
            ];
        }));
    }
}
📄 Controller: Mentor/MentorStudentController.php (Update)
php
<?php
// app/Http/Controllers/Mentor/MentorStudentController.php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorStudentController extends Controller
{
    public function index()
    {
        $mentor = Auth::user();
        
        // Ambil semua santri binaan dengan informasi orang tua
        $students = $mentor->students()
            ->with(['parent', 'parent.user', 'progress'])
            ->wherePivot('is_active', true)
            ->get();

        // Statistik
        $stats = [
            'total_students' => $students->count(),
            'active_students' => $students->filter(fn($s) => $s->progress->isNotEmpty())->count(),
            'total_sessions' => $students->sum(fn($s) => $s->sessions->count()),
            'avg_tajwid' => $students->avg(fn($s) => $s->progress->avg('tajwid_score') ?? 0),
        ];

        return view('mentor.students.index', compact('students', 'stats'));
    }

    public function show($id)
    {
        $mentor = Auth::user();
        
        $student = $mentor->students()
            ->with(['parent', 'parent.user', 'progress' => function($q) {
                $q->latest()->limit(10);
            }, 'sessions' => function($q) {
                $q->latest()->limit(10);
            }])
            ->wherePivot('is_active', true)
            ->findOrFail($id);

        // Informasi orang tua
        $parent = $student->parent;
        $parentUser = $parent?->user;

        return view('mentor.students.show', compact('student', 'parent', 'parentUser'));
    }

    // Daftar Orang Tua dari Santri Binaan
    public function parents()
    {
        $mentor = Auth::user();
        
        $students = $mentor->students()
            ->with(['parent', 'parent.user'])
            ->wherePivot('is_active', true)
            ->get();

        $parents = $students->map(function ($student) {
            return [
                'student' => $student,
                'parent' => $student->parent,
                'parent_user' => $student->parent?->user,
            ];
        })->unique('parent.id');

        return view('mentor.students.parents', compact('parents'));
    }
}
📄 Controller: Mentor/AvailabilityController.php
php
<?php
// app/Http/Controllers/Mentor/AvailabilityController.php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    // Halaman Pengaturan Ketersediaan
    public function index()
    {
        $mentor = Auth::user();
        
        // Ambil data availability yang sudah ada
        $availabilities = $mentor->availabilities()
            ->get()
            ->keyBy('day');

        $days = MentorAvailability::DAYS;
        $daysOrder = MentorAvailability::DAYS_ORDER;

        return view('mentor.availability.index', compact('mentor', 'availabilities', 'days', 'daysOrder'));
    }

    // Update Ketersediaan
    public function update(Request $request)
    {
        $mentor = Auth::user();

        $validated = $request->validate([
            'day' => 'required|string|in:' . implode(',', MentorAvailability::DAYS_ORDER),
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'max_students' => 'nullable|integer|min:0|max:20',
            'is_available' => 'nullable|boolean',
            'is_holiday' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        // Cek apakah sudah ada record untuk hari tersebut
        $availability = MentorAvailability::firstOrNew([
            'mentor_id' => $mentor->id,
            'day' => $validated['day'],
        ]);

        $availability->fill([
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'max_students' => $validated['max_students'] ?? $mentor->mentorProfile?->default_max_students_per_day ?? 5,
            'is_available' => $validated['is_available'] ?? true,
            'is_holiday' => $validated['is_holiday'] ?? false,
            'notes' => $validated['notes'] ?? null,
        ]);

        $availability->save();

        return redirect()->route('mentor.availability.index')
            ->with('success', 'Jadwal ketersediaan berhasil diperbarui.');
    }

    // Update Bulk (Semua Hari)
    public function updateBulk(Request $request)
    {
        $mentor = Auth::user();

        $validated = $request->validate([
            'days' => 'required|array',
            'days.*.day' => 'required|string|in:' . implode(',', MentorAvailability::DAYS_ORDER),
            'days.*.start_time' => 'nullable|date_format:H:i',
            'days.*.end_time' => 'nullable|date_format:H:i|after:start_time',
            'days.*.max_students' => 'nullable|integer|min:0|max:20',
            'days.*.is_available' => 'nullable|boolean',
            'days.*.is_holiday' => 'nullable|boolean',
        ]);

        foreach ($validated['days'] as $dayData) {
            $availability = MentorAvailability::firstOrNew([
                'mentor_id' => $mentor->id,
                'day' => $dayData['day'],
            ]);

            $availability->fill([
                'start_time' => $dayData['start_time'] ?? null,
                'end_time' => $dayData['end_time'] ?? null,
                'max_students' => $dayData['max_students'] ?? $mentor->mentorProfile?->default_max_students_per_day ?? 5,
                'is_available' => $dayData['is_available'] ?? true,
                'is_holiday' => $dayData['is_holiday'] ?? false,
            ]);

            $availability->save();
        }

        return redirect()->route('mentor.availability.index')
            ->with('success', 'Semua jadwal ketersediaan berhasil diperbarui.');
    }

    // API: Get Current Availability (AJAX)
    public function getAvailability()
    {
        $mentor = Auth::user();
        $availabilities = $mentor->availabilities()->get();
        $days = MentorAvailability::DAYS_ORDER;

        $data = [];
        foreach ($days as $day) {
            $avail = $availabilities->firstWhere('day', $day);
            $data[$day] = $avail ? [
                'start_time' => $avail->start_time?->format('H:i'),
                'end_time' => $avail->end_time?->format('H:i'),
                'max_students' => $avail->max_students,
                'is_available' => $avail->is_available,
                'is_holiday' => $avail->is_holiday,
            ] : null;
        }

        return response()->json($data);
    }
}
3.4 Routes
php
<?php
// routes/web.php - TAMBAHKAN

use App\Http\Controllers\Admin\MentorAvailabilityController;
use App\Http\Controllers\Mentor\AvailabilityController;
use App\Http\Controllers\Mentor\MentorStudentController;

// ============ ADMIN ROUTES ============
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // ... existing routes

        // Mentor Availability Management
        Route::get('/mentors/availability', [MentorAvailabilityController::class, 'index'])
            ->name('mentors.availability');
        
        Route::post('/mentors/assign-student', [MentorAvailabilityController::class, 'assignStudent'])
            ->name('mentors.assign-student');
        
        Route::post('/mentors/unassign-student', [MentorAvailabilityController::class, 'unassignStudent'])
            ->name('mentors.unassign-student');
        
        Route::get('/mentors/available', [MentorAvailabilityController::class, 'getAvailableMentors'])
            ->name('mentors.available');
    });

// ============ MENTOR ROUTES ============
Route::middleware(['auth', 'role:mentor'])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        // ... existing routes

        // Student List with Parents
        Route::get('/students', [MentorStudentController::class, 'index'])
            ->name('students.index');
        
        Route::get('/students/{id}', [MentorStudentController::class, 'show'])
            ->name('students.show');
        
        Route::get('/students/parents', [MentorStudentController::class, 'parents'])
            ->name('students.parents');

        // Availability Settings
        Route::get('/availability', [AvailabilityController::class, 'index'])
            ->name('availability.index');
        
        Route::post('/availability/update', [AvailabilityController::class, 'update'])
            ->name('availability.update');
        
        Route::post('/availability/update-bulk', [AvailabilityController::class, 'updateBulk'])
            ->name('availability.update-bulk');
        
        Route::get('/availability/data', [AvailabilityController::class, 'getAvailability'])
            ->name('availability.data');
    });
4. ROADMAP & TIMELINE
Fase	Durasi	Deliverable
Sprint 1	2 hari	- Database Migration (mentor_availabilities)
- Models & Relationships
- Admin: Mentor Availability Index
Sprint 2	2 hari	- Admin: Assign/Unassign Student
- API: Get Available Mentors
- Mentor: Student List with Parents
Sprint 3	2 hari	- Mentor: Availability Settings UI
- Dashboard Enhancements
- Testing & QA
Total Estimasi: ± 6 hari kerja

5. TESTING PLAN
Automated Tests
php
<?php
// tests/Feature/MentorAvailabilityTest.php

namespace Tests\Feature;

use App\Models\MentorAvailability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_mentor_availability_calendar()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'max_students' => 5,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.mentors.availability'));

        $response->assertStatus(200);
        $response->assertSee($mentor->name);
    }

    /** @test */
    public function admin_can_assign_student_to_mentor()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'max_students' => 5,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.mentors.assign-student'), [
                'student_id' => $student->id,
                'mentor_id' => $mentor->id,
                'day' => 'monday',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'day_assigned' => 'monday',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function mentor_can_view_his_students_with_parents()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        $parent = User::factory()->create(['role' => 'parent']);

        // Connect mentor - student
        \DB::table('mentor_student')->insert([
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Connect student - parent
        \DB::table('parent_child')->insert([
            'parent_id' => $parent->id,
            'child_id' => $student->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($mentor)
            ->get(route('mentor.students.index'));

        $response->assertStatus(200);
        $response->assertSee($student->name);
        $response->assertSee($parent->name);
    }
}
6. VISUAL UI WIREFRAMES
Admin: Kalender Ketersediaan Mentor
text
┌─────────────────────────────────────────────────────────────────────────┐
│  🛠️ ADMIN DASHBOARD - KETERSEDIAAN MENTOR                             │
├─────────────────────────────────────────────────────────────────────────┤
│  [🗓️ Hari Ini] [📊 Laporan] [⚙️ Pengaturan]                           │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  Mentor   │ Senin │ Selasa │ Rabu │ Kamis │ Jumat │ Sabtu │   │   │
│  ├───────────┼───────┼────────┼──────┼───────┼───────┼───────┤   │   │
│  │ Ust. Ahmad│ ✅ 3/5│ ✅ 5/5 │ ✅ 2/5│ ❌    │ ✅ 4/5│ ✅ 1/5│   │   │
│  │           │ 08-12 │ 08-12  │ 13-17│ Libur │ 08-12 │ 08-12 │   │   │
│  ├───────────┼───────┼────────┼──────┼───────┼───────┼───────┤   │   │
│  │ Ust. Budi │ ❌    │ ✅ 3/5 │ ✅ 4/5│ ✅ 2/5│ ❌    │ ✅ 3/5│   │   │
│  │           │ Libur │ 13-17  │ 08-12│ 13-17 │ Libur │ 08-12 │   │   │
│  ├───────────┼───────┼────────┼──────┼───────┼───────┼───────┤   │   │
│  │ Ust. Citra│ ✅ 2/5│ ✅ 4/5 │ ✅ 3/5│ ✅ 5/5│ ✅ 2/5│ ❌    │   │   │
│  │           │ 08-12 │ 08-12  │ 13-17│ 08-12 │ 13-17 │ Libur │   │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ✅ = Tersedia & Ada Kuota                                            │
│  ⚠️ = Penuh / Kuota Habis                                            │
│  ❌ = Tidak Tersedia / Libur                                          │
│                                                                         │
│  [📌 Alokasikan Santri Baru]                                           │
└─────────────────────────────────────────────────────────────────────────┘
Mentor: Daftar Santri Binaan & Orang Tua
text
┌─────────────────────────────────────────────────────────────────────────┐
│  👨‍🏫 PORTAL MENTOR - SANTRI BINAAN                                    │
├─────────────────────────────────────────────────────────────────────────┤
│  📊 Statistik: 12 Santri Aktif | 8 Orang Tua | 45 Sesi Total          │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  🔍 [Cari Santri....]  [Filter: ▾ Semua Program]                       │
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │ Nama Santri  │ Usia │ Program │ Orang Tua │ Kontak │ Nilai │   │   │
│  ├──────────────┼──────┼─────────┼───────────┼────────┼───────┤   │   │
│  │ Ahmad F.     │ 13   │ Tahfidz │ Bpk. Rudi │ 📞 WA  │ 88    │   │   │
│  │              │      │         │           │ 📧     │       │   │   │
│  ├──────────────┼──────┼─────────┼───────────┼────────┼───────┤   │   │
│  │ Siti A.      │ 10   │ Tahsin  │ Ibu. Dewi │ 📞 WA  │ 85    │   │   │
│  │              │      │         │           │ 📧     │       │   │   │
│  ├──────────────┼──────┼─────────┼───────────┼────────┼───────┤   │   │
│  │ Budi S.      │ 12   │ Iqra    │ Bpk. Andi │ 📞 WA  │ 75    │   │   │
│  │              │      │         │           │ 📧     │       │   │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  [📋 Lihat Semua Orang Tua] [📅 Jadwal Minggu Ini]                    │
└─────────────────────────────────────────────────────────────────────────┘
7. RISK & MITIGASI
Risk	Impact	Mitigation
Guru tidak update ketersediaan	Data tidak akurat	Admin bisa override, reminder email
Overbooking	Guru kelelahan	Sistem otomatis cek kuota sebelum assign
Konflik jadwal	Sesi bentrok	Validasi waktu mengajar di backend
Guru keluar mendadak	Santri kehilangan mentor	Admin bisa unassign & reassign cepat
8. KESIMPULAN
✅ PRD ini menjawab 2 masalah utama:
Issue	Solusi	Status
Admin tidak tau ketersediaan guru	Kalender ketersediaan + kuota per hari	✅
Guru tidak tau santri & orang tua	Daftar santri binaan + info orang tua	✅
📊 Nilai Tambah:
Transparansi - Admin dan guru punya visibilitas penuh

Efisiensi - Alokasi santri baru jadi cepat dan tepat

Komunikasi - Guru tahu orang tua santri binaan

Akuntabilitas - Beban mengajar guru terukur

