# 🚀 [EPIC & FEATURE] Modul Dashboard Mentor (Role-Based Dashboard)

Dokumen perencanaan ini dibuat sebagai panduan teknis (*issue task guide*) bagi **Junior Developer / Programmer** untuk mengimplementasikan **Modul Dashboard Mentor (`/mentor/dashboard`)** dan seluruh sub-fitur operasional mengajar di aplikasi **AL-HIKMAH LMS**.

---

## 📌 Context & Motivation

- **Masalah Saat Ini**: Setelah melakukan login sebagai Mentor (`role: mentor`), halaman `/mentor/dashboard` hanya merender view generik tanpa informasi jadwal mengajar, santri binaan, atau form pencatatan hafalan yang terpusat.
- **Solusi**: Dibuatkan antarmuka terpusat bagi Mentor/Guru Al-Qur'an untuk melihat ringkasan sesi mengajar hari ini, jadwal 7 hari ke depan, daftar santri binaan, form pencatatan progres hafalan/tajwid/adab yang cepat, serta ekspor laporan per santri.

---

## 📋 Summary Checklist Fitur

- [x] **Task 1**: Skema Database Pivot & Relationship (`mentor_student` & Model Enhancements)
- [x] **Task 2**: `MentorDashboardController` & Sub-Controller (Route `/mentor/*`)
- [x] **Task 3**: Blade Layout & Dashboard UI View (`resources/views/mentor/dashboard.blade.php`)
- [x] **Task 4**: Komponen Livewire `TodaySchedule` & `UpcomingSessions`
- [x] **Task 5**: Komponen Livewire `MyStudents` & Halaman Detail Santri Binaan
- [x] **Task 6**: Halaman Form Catat Progres Hafalan Cepat (`/mentor/progress/create`)
- [x] **Task 7**: Halaman Profil & Pengaturan Akun Mentor (`/mentor/profile`)
- [x] **Task 8**: Testing & Quality Assurance Checklist

---

## 🛠️ Task 1: Skema Database & Model Enhancements

### 1.1 Buat Migration Tabel Pivot `mentor_student`
Jalankan perintah di terminal:
```bash
php artisan make:migration create_mentor_student_table
```

Edit file migration `database/migrations/xxxx_xx_xx_xxxxxx_create_mentor_student_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('assigned_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_student');
    }
};
```

### 1.2 Update Relasi pada Model `App\Models\Mentor` dan `App\Models\Student`
- Di `app/Models/Mentor.php`:
  ```php
  public function students(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
      return $this->belongsToMany(Student::class, 'mentor_student')->withTimestamps();
  }
  ```
- Di `app/Models/Student.php`:
  ```php
  public function mentors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
      return $this->belongsToMany(Mentor::class, 'mentor_student')->withTimestamps();
  }
  ```

---

## 🎛️ Task 2: Controller & Routing Mentor (`routes/web.php`)

### 2.1 Buat Controller `MentorDashboardController`
Jalankan perintah:
```bash
php artisan make:controller Mentor/DashboardController
php artisan make:controller Mentor/SessionController
php artisan make:controller Mentor/StudentController
php artisan make:controller Mentor/ProgressController
```

### 2.2 Daftarkan Routes di `routes/web.php`
Perbarui grup route mentor:
```php
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\SessionController as MentorSessionController;
use App\Http\Controllers\Mentor\StudentController as MentorStudentController;
use App\Http\Controllers\Mentor\ProgressController as MentorProgressController;

Route::middleware(['auth', 'role:mentor'])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        Route::get('/dashboard', [MentorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/sessions', [MentorSessionController::class, 'index'])->name('sessions.index');
        Route::get('/students', [MentorStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{id}', [MentorStudentController::class, 'show'])->name('students.show');
        Route::get('/progress/create', [MentorProgressController::class, 'create'])->name('progress.create');
        Route::post('/progress', [MentorProgressController::class, 'store'])->name('progress.store');
        Route::get('/profile', [MentorDashboardController::class, 'profile'])->name('profile');
    });
```

---

## 🎨 Task 3: Blade View Dashboard Mentor (`resources/views/mentor/dashboard.blade.php`)

Buat tampilan dashboard mentor yang menyajikan:
1. **Statistik Ringkasan (Cards)**:
   - Sesi Mengajar Hari Ini
   - Total Santri Binaan Aktif
   - Rata-rata Nilai Tajwid/Adab Santri
   - Sesi Mendatang (7 Hari Ke Depan)
2. **Widget Jadwal Mengajar Hari Ini**
3. **Widget Santri Binaan & Progres Terakhir**
4. **Tombol Aksis Cepat**:
   - `[📝 Catat Progres]` -> Mengarah ke `/mentor/progress/create`
   - `[👨‍🎓 Semua Santri]` -> Mengarah ke `/mentor/students`
   - `[📅 Kalender Sesi]` -> Mengarah ke `/mentor/sessions`

---

## ⚡ Task 4 & 5: Livewire Components untuk Dynamic Data

Jalankan perintah pembuat komponen Livewire:
```bash
php artisan make:livewire Mentor/TodaySchedule
php artisan make:livewire Mentor/MyStudents
php artisan make:livewire Mentor/UpcomingSessions
```

- **`TodaySchedule`**: Mengambil data `Session::where('mentor_id', $mentorId)->whereDate('date', today())->get()`. Memberikan opsi aksi cepat ubah status sesi (`completed` / `cancelled`).
- **`MyStudents`**: Menampilkan daftar santri dari `$mentor->students` beserta record `Progress` terakhir (Surah, Ayat, Nilai Tajwid, Catatan Adab).

---

## 📝 Task 6: Form Catat Progres Hafalan Cepat (`/mentor/progress/create`)

Buat form Blade di `resources/views/mentor/progress/create.blade.php` dengan field:
- **Pilih Santri**: `<select name="student_id">` (hanya santri binaan mentor aktif).
- **Pilih Sesi**: `<select name="session_id">`.
- **Kategori**: `Tahfidz` / `Tahsin` / `Iqra` / `Adab`.
- **Capaian Surah & Ayat**: `surah_start`, `surah_end`, `ayat_start`, `ayat_end`, `juz`.
- **Nilai Evaluasi**: `nilai_fluent` (1-100), `nilai_tajwid` (1-100), `nilai_adab` (Baik/Sangat Baik/Cukup).
- **Catatan & Tugas**: `catatan_evaluasi` & `homework`.

---

## 🧪 Task 7: Quality Assurance & Testing Checklist

Sebelum melaporkan task selesai, jalankan checklist verifikasi:
- [x] Database Refresh & Seed:
  ```bash
  php artisan migrate:fresh --seed
  ```
- [x] Login sebagai Mentor (`mentor@alhikmah.id` / password default) dan pastikan langsung diarahkan ke `/mentor/dashboard`.
- [x] Pastikan jadwal mengajar hari ini dan daftar santri binaan tampil dengan presisi.
- [x] Coba jalankan form **Catat Progres** untuk salah satu santri dan pastikan data tersimpan di tabel `progress`.
- [x] Jalankan kode formatter dan pengujian otomatis:
  ```bash
  vendor/bin/pint --dirty --format agent
  php artisan test --compact
  ```

---
