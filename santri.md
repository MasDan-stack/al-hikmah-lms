# 📋 PRD: Student Dashboard Enhancement v8.1 (REVISI FINAL)
## AL-HIKMAH LMS — Modul Gamifikasi Islami & Manajemen Akun Santri

**Status Dokumen**: ✅ Matang & Siap Implementasi  
**Versi**: 2.0 (Revisi dari v1.0)  
**Tanggal Revisi**: 28 Agustus 2026  
**Bahasa Dokumentasi**: Bahasa Indonesia (sesuai standar proyek)  
**Estimasi Durasi**: 14 Hari Kerja (3 Sprint)  

---

## 🎯 1. RINGKASAN EKSEKUTIF

### 📌 Pernyataan Masalah:
1. **Dashboard santri v8.0 bersifat pasif** — tidak ada motivasi visual atau interaksi yang mendorong santri menghafal secara konsisten.
2. **Proses pembuatan akun santri manual** — merepotkan admin & wali saat santri baru didaftarkan.
3. **Manajemen password tidak transparan** — wali tidak bisa membantu anak login jika anak lupa kredensial.

### 💡 Solusi yang Diusulkan:
- **Ekosistem Gamifikasi Islami**: Badge, leaderboard, target harian, dan countdown hitung mundur.
- **Otomasi Pembuatan Akun**: Email & password auto-generate saat pendaftaran santri baru.
- **Manajemen Password Aman & Transparan**: Reset via WhatsApp/Email dengan pencatatan audit log tanpa menyimpan plain-text password.

### 🎯 Kriteria Keberhasilan:
| KPI | Baseline v8.0 | Target v8.1 |
|---|---|---|
| Daily Active Santri | 45% | 75% |
| Target Completion Rate | N/A | ≥ 70% |
| Santri dengan ≥ 1 Badge | N/A | 80% |
| Ticket Password-related | 100% | Turun 60% |

---

## 👤 2. USER PERSONA & USER STORY

### 👥 Persona Pengguna:

1. **Ahmad (Santri, 10 tahun)**
   - *Tujuan*: Ingin mendapat badge "Hafidz Cilik" & masuk top 5 leaderboard.
   - *Kebutuhan*: Akses akun mudah tanpa harus terus bertanya ke orang tua.

2. **Umi Fatimah (Wali Santri)**
   - *Tujuan*: Memantau progres hafalan anak & bisa membantu anak saat login.
   - *Kebutuhan*: Fitur reset password anak yang langsung terkirim ke nomor WhatsApp wali.

3. **Ustadz Rizki (Mentor)**
   - *Tujuan*: Menetapkan target setoran harian dengan cepat ke banyak santri binaan.
   - *Kebutuhan*: Antarmuka praktis tanpa harus mengelola kredensial santri manual.

### 📖 User Stories:

| ID | Sebagai... | Saya Ingin... | Sehingga... | Prioritas |
|---|---|---|---|---|
| **US-01** | Santri | Melihat target hafalan hari ini & progress bar | Saya tahu apa yang harus dicapai hari ini | 🔴 High |
| **US-02** | Santri | Melihat peringkat & mendapat badge penghargaan | Saya termotivasi untuk terus istiqomah | 🟡 Medium |
| **US-03** | Mentor | Menetapkan target harian ke santri binaan | Saya punya acuan evaluasi setoran yang jelas | 🔴 High |
| **US-04** | Sistem | Otomatis membuat akun saat wali mendaftarkan anak | Proses onboarding berlangsung seamless | 🔴 High |
| **US-05** | Santri | Mereset password sendiri dengan aman | Saya bisa memulihkan akses akun secara mandiri | 🟡 Medium |
| **US-06** | Wali | Mereset password anak & menerima password baru via WA | Saya bisa mendampingi anak login | 🔴 High |
| **US-07** | Wali/Santri | Menerima notifikasi fallback jika WA & Email gagal | Tidak ada kendala komunikasi kredensial | 🟡 Medium |

### ✅ Acceptance Criteria (Detail):

#### US-01: Target Harian & Progress Bar
- Dashboard menampilkan target hari ini di widget terpisah.
- Progress bar ter-update otomatis saat mentor menginput progres di rapor mutaba'ah.
- Target yang terlewat ditandai warna merah & masuk ke daftar "Hutang Hafalan".
- Widget responsif di mobile, tablet, dan desktop.
- Dark mode compatible (mengikuti standar tema Etrain).

#### US-02: Leaderboard & Badge
- Leaderboard di-cache dan di-refresh setiap 1 jam (menggunakan Redis/File cache).
- Badge unlock memicu modal perayaan dengan animasi confetti.
- Santri dapat mengaktifkan opsi privasi (opt-out) dari leaderboard publik.
- Integrasi DataTables untuk filter kategori dan pencarian nama.

#### US-03: Mentor Assign Target
- Form bulk assign target ke banyak santri sekaligus.
- Template target siap pakai (contoh: "Hafal 1 Ayat", "Muroja'ah 1 Juz").
- Notifikasi otomatis terkirim ke santri & wali setelah target ditetapkan.

#### US-04: Auto Account Generation
- Format Email: `{3hurufdepan}.{namabelakang}@{domain_lembaga}` (contoh: `dan.hermawan@alhikmah.com`).
- Domain lembaga diambil dinamis dari `settings.domain` (bukan hardcoded).
- Password default 8 karakter kombinasi: uppercase + lowercase + number + symbol (`!@#$%^&*`).
- Password di-hash menggunakan `Hash::make()` (bcrypt bawaan Laravel).
- Mengirimkan notifikasi selamat datang + target hari pertama secara otomatis.

#### US-05: Self Password Reset
- Santri dapat mereset password dari halaman profil.
- Memerlukan validasi password lama sebelum diganti.
- Pencatatan log aktivitas di tabel `password_reset_logs`.
- Notifikasi terkirim via WhatsApp (jika nomor terdaftar) atau Email.

#### US-06: Parent Reset Child Password
- Terdapat tombol "Reset & Kirim Password" pada setiap kartu anak di dashboard wali.
- Password baru dikirimkan via WhatsApp wali (Prioritas 1).
- Fallback ke Email wali (Prioritas 2).
- Jika keduanya gagal, sistem memunculkan notifikasi in-app dengan instruksi menghubungi admin.
- **Kebijakan Keamanan**: TIDAK ADA tampilan plain-text password pada UI.

#### US-07: Fallback Notification
- Urutan prioritas notifikasi: WhatsApp ➔ Email ➔ In-App Notification.
- Pesan in-app berisi informasi: *"Password telah direset. Silakan hubungi admin untuk mendapatkan password baru jika pesan tidak diterima."*

### 🚀 Default State for New Students (Onboarding):
1. **JuzProgress**: 30 baris data diinisialisasi otomatis dengan status `not_started`.
2. **Target Pertama**: Auto-generated oleh sistem: *"Hafal 1 Ayat"* pada hari pertama.
3. **Badge B01 (🌱 Penyemai Qur'an)**: Diberikan secara otomatis saat santri melakukan setoran pertama.
4. **Notifikasi Sambutan**: Kirim pesan selamat datang + arahan target awal.

---

## 🗄️ 3. SPESIFIKASI DATABASE

### 📊 3.1 Tabel Baru (8 Tabel):

```sql
-- 1. Target Hafalan Harian
CREATE TABLE hifz_targets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    mentor_id BIGINT UNSIGNED NOT NULL,
    learning_session_id BIGINT UNSIGNED NULL,  -- INTEGRASI dengan learning_sessions
    target_date DATE NOT NULL,
    surah_name VARCHAR(100) NULL,
    start_ayat INT UNSIGNED NOT NULL,
    end_ayat INT UNSIGNED NOT NULL,
    total_ayat INT UNSIGNED GENERATED ALWAYS AS (end_ayat - start_ayat + 1) STORED,
    notes TEXT NULL,
    scheduled_time TIME NULL,
    status ENUM('pending','in_progress','completed','missed') DEFAULT 'pending',
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_student_date (student_id, target_date),
    INDEX idx_status (status),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (learning_session_id) REFERENCES learning_sessions(id) ON DELETE SET NULL
);

-- 2. Progress Hafalan Per Juz
CREATE TABLE juz_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    juz_number TINYINT UNSIGNED NOT NULL,
    total_ayat INT UNSIGNED NOT NULL,
    ayat_hafal INT UNSIGNED DEFAULT 0,
    percentage DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE WHEN total_ayat > 0 THEN (ayat_hafal / total_ayat) * 100 ELSE 0 END
    ) STORED,
    status ENUM('not_started','in_progress','completed','mutqin') DEFAULT 'not_started',
    started_at DATE NULL,
    completed_at DATE NULL,
    mutqin_at DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY uq_student_juz (student_id, juz_number),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 3. Master Katalog Badge
CREATE TABLE badges (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    category ENUM('milestone','streak','achievement','leaderboard','adab') NOT NULL,
    points_reward INT UNSIGNED DEFAULT 0,
    criteria_json JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 4. Badge yang Diraih Santri
CREATE TABLE student_badges (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    badge_id BIGINT UNSIGNED NOT NULL,
    earned_at TIMESTAMP NOT NULL,
    trigger_data JSON NULL,
    announced_to_parent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    
    UNIQUE KEY uq_student_badge (student_id, badge_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);

-- 5. Target Jangka Panjang (Milestone)
CREATE TABLE hifz_milestones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    mentor_id BIGINT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    target_type ENUM('juz_completion','ayat_milestone','exam','custom') NOT NULL,
    target_date DATETIME NOT NULL,
    progress_current INT UNSIGNED DEFAULT 0,
    progress_goal INT UNSIGNED NOT NULL,
    status ENUM('active','achieved','expired','cancelled') DEFAULT 'active',
    achieved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_student_status (student_id, status),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 6. Snapshot Leaderboard Periodik
CREATE TABLE leaderboard_snapshots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_type ENUM('daily','weekly','monthly') NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    category ENUM('overall','anak','dewasa','per_juz','streak') NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    rank_position INT UNSIGNED NOT NULL,
    total_points INT UNSIGNED NOT NULL,
    total_ayat INT UNSIGNED NOT NULL,
    total_juz_mutqin TINYINT UNSIGNED NOT NULL,
    current_streak INT UNSIGNED DEFAULT 0,
    trend ENUM('up','down','stable') DEFAULT 'stable',
    created_at TIMESTAMP NULL,
    
    INDEX idx_period_category (period_type, period_start, category),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 7. Log Transaksi Poin Gamifikasi
CREATE TABLE gamification_points (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    points INT NOT NULL,
    source_type ENUM('ayat_hafal','juz_mutqin','badge','streak','leaderboard','target_completed') NOT NULL,
    source_id BIGINT UNSIGNED NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    
    INDEX idx_student (student_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 8. Audit Log Reset Password (REVISI: Metadata Only, Tanpa Hash)
CREATE TABLE password_reset_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    changed_by BIGINT UNSIGNED NULL,  -- NULL jika self-reset
    reset_method ENUM('self','parent','admin') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    notification_channel ENUM('whatsapp','email','inapp') NOT NULL,
    notification_status ENUM('sent','failed','fallback') NOT NULL,
    created_at TIMESTAMP NULL,
    
    INDEX idx_user (user_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 📊 3.2 Modifikasi Tabel Eksisting:

```sql
-- Tambah kolom gamifikasi di tabel students
ALTER TABLE students
    ADD COLUMN total_points INT UNSIGNED DEFAULT 0 AFTER notes,
    ADD COLUMN current_streak INT UNSIGNED DEFAULT 0 AFTER total_points,
    ADD COLUMN longest_streak INT UNSIGNED DEFAULT 0 AFTER current_streak,
    ADD COLUMN last_setoran_date DATE NULL AFTER longest_streak,
    ADD COLUMN privacy_leaderboard BOOLEAN DEFAULT FALSE AFTER last_setoran_date;

-- Tambah kolom mutqin di tabel progresses
ALTER TABLE progresses
    ADD COLUMN is_mutqin_test BOOLEAN DEFAULT FALSE AFTER adab_score,
    ADD COLUMN juz_number TINYINT UNSIGNED NULL AFTER is_mutqin_test;
```

---

## 🎮 4. MODEL, CONTROLLER, & SERVICE

### 📦 4.1 Model Baru (8 Model Eloquent):

| Model | Tabel | Relasi Utama |
|---|---|---|
| `HifzTarget` | `hifz_targets` | `belongsTo(Student::class)`, `belongsTo(User::class, 'mentor_id')`, `belongsTo(LearningSession::class)` |
| `JuzProgress` | `juz_progress` | `belongsTo(Student::class)` |
| `Badge` | `badges` | `belongsToMany(Student::class, 'student_badges')` |
| `StudentBadge` | `student_badges` | `belongsTo(Student::class)`, `belongsTo(Badge::class)` |
| `HifzMilestone` | `hifz_milestones` | `belongsTo(Student::class)`, `belongsTo(User::class, 'mentor_id')` |
| `LeaderboardSnapshot` | `leaderboard_snapshots` | `belongsTo(Student::class)` |
| `GamificationPoint` | `gamification_points` | `belongsTo(Student::class)` |
| `PasswordResetLog` | `password_reset_logs` | `belongsTo(User::class, 'user_id')`, `belongsTo(User::class, 'changed_by')` |

### 📂 4.2 Controller (Konsisten Sesuai Konvensi):

1. **Student Controllers (`app/Http/Controllers/Student/`)**:
   - `StudentDashboardController.php`: Menampilkan dashboard utama beserta widget gamifikasi.
   - `StudentTargetController.php`: Pengelolaan target pribadi santri.
   - `StudentGamificationController.php`: Menampilkan koleksi badge & statistik santri.
   - `StudentPasswordController.php`: Pengelolaan reset password mandiri oleh santri.

2. **Mentor Controllers (`app/Http/Controllers/Mentor/`)**:
   - `MentorTargetController.php`: Menetapkan target hafalan harian santri binaan.

3. **Parent Controllers (`app/Http/Controllers/Parent/`)**:
   - `ParentChildController.php` *(Eksisting)*: Tambahkan method `requestPasswordReset(Student $child)`.

4. **Admin Controllers (`app/Http/Controllers/Admin/`)**:
   - `AdminBadgeController.php`: Operasi CRUD master katalog badge.
   - `AdminGamificationController.php`: Konfigurasi parameter & manajemen leaderboard.

### 🛠️ 4.3 Service Layer:

| Service | File Path | Tanggung Jawab Utama |
|---|---|---|
| `StudentAccountService` | `app/Services/StudentAccountService.php` | Auto-generate email unik, password acak aman, dan provisi akun User |
| `GamificationService` | `app/Services/GamificationService.php` | Kalkulasi poin reward & evaluasi pemenuhan kriteria badge |
| `LeaderboardService` | `app/Services/LeaderboardService.php` | Kalkulasi ranking, snapshot periodik, dan caching (`Cache::remember`) |
| `HifzProgressService` | `app/Services/HifzProgressService.php` | Agregasi dan kalkulasi persentase per Juz |
| `BadgeEvaluatorService` | `app/Services/BadgeEvaluatorService.php` | Observer evaluasi reward otomatis setelah setoran |
| `StreakTrackerService` | `app/Services/StreakTrackerService.php` | Pelacakan dan pembaruan streak harian santri |
| `EmailService` *(Baru)* | `app/Services/EmailService.php` | Layanan pengiriman notifikasi kredensial via Laravel Mail/SMTP |

### 👂 4.4 Observer & Event-Listener:

- **Observer**: `ProgressObserver` (Mengamati event `created` pada model `Progress`).
- **Events & Listeners**:
  - `ProgressRecorded` ➔ Listeners: `UpdateJuzProgress`, `EvaluateBadgeCriteria`, `UpdateGamificationPoints`, `UpdateStreakCounter`.
  - `TargetCompleted` ➔ Listener: `AwardTargetBadge`.
  - `JuzMutqinAchieved` ➔ Listener: `AwardJuzBadge`.
  - `LeaderboardRefreshed` ➔ Listener: `NotifyTopAchievers`.

### ⏰ 4.5 Scheduled Commands:

| Command | Jadwal Cron | Fungsi |
|---|---|---|
| `app:refresh-leaderboard` | Setiap jam (`0 * * * *`) | Memperbarui cache leaderboard |
| `app:mark-missed-targets` | Setiap hari 23:55 (`55 23 * * *`) | Menandai target harian yang terlewat |
| `app:reset-daily-streak` | Setiap hari 00:05 (`5 0 * * *`) | Mengevaluasi status keaktifan streak harian |
| `app:snapshot-leaderboard`| Setiap Ahad 00:00 (`0 0 * * 0`) | Mengambil snapshot peringkat mingguan |
| `app:cleanup-milestones` | Setiap hari 00:10 (`10 0 * * *`) | Mengarsipkan milestone target yang kadaluarsa |

---

## 🎨 5. DESAIN ANTARMUKA & STANDAR TEMA ETRAIN

### 🌙 5.1 Kompatibilitas Dark Mode:
Semua widget wajib menggunakan CSS variables untuk mendukung *Zero-Flicker LocalStorage Dark Mode*:
```css
:root {
    --gamification-bg: #ffffff;
    --gamification-card-bg: #f8fafc;
    --gamification-text: #1a1a1a;
    --gamification-border: #e5e7eb;
    --gamification-gold: #ffc107;
    --gamification-green: #0d7a3e;
}

[data-theme="dark"] {
    --gamification-bg: #111827;
    --gamification-card-bg: #1f2937;
    --gamification-text: #f9fafb;
    --gamification-border: #374151;
    --gamification-gold: #fbbf24;
    --gamification-green: #10b981;
}
```

### 📊 5.2 Standarisasi DataTables (v3.0.2):
Diterapkan pada semua tabel daftar untuk konsistensi:
- **Leaderboard**: Filter kategori (Overall, Anak, Dewasa, Per Juz), fitur pencarian nama.
- **Koleksi Badge**: Sorting berdasarkan tanggal perolehan (`earned_at`).
- **Riwayat Target**: Filter status (`completed`, `missed`, `pending`).

### ⏳ 5.3 Spesifikasi Countdown Widget:
- **Animasi**: Flip-Clock menggunakan CSS 3D Transforms.
- **Penanganan Timezone**: Kalkulasi target waktu di sisi server (UTC), display client-side (WIB).
- **Fallback Non-JS**: Jika JS dinonaktifkan di browser, tampilkan teks tanggal target secara statis.
- **Responsivitas**: Mode ringkas pada layar ponsel (`< 576px`).

### 🎬 5.4 Animasi & Efek Mikro:
- **Progress Bar**: Animasi pengisian halus (`cubic-bezier(0.4, 0, 0.2, 1)`).
- **Badge Unlock Modal**: Animasi confetti canvas + efek glow emas.
- **Streak Fire Effect**: Efek api berkobar saat streak santri `> 7 hari`.
- **Target Completion**: Modal ucapan *"Masya Allah!"* dengan efek reward.

---

## 🧪 6. STRATEGI PENGUJIAN (MINIMAL 50 TEST CASES)

### 📊 6.1 Cakupan Unit & Feature Test (Pest PHP):

| Domain Pengujian | Target Cases | Target Assertions | Tipe Test |
|---|---|---|---|
| Account Generation Service | 8 | 32 | Pest Unit |
| Password Management & Audit | 8 | 32 | Pest Feature |
| Gamification & Badge Evaluator | 10 | 40 | Pest Unit |
| Leaderboard & Caching | 6 | 24 | Pest Feature |
| Student Dashboard Widgets | 8 | 32 | Pest Feature |
| Mentor Target Assignment | 6 | 24 | Pest Feature |
| Parent Monitoring & Reset Flow | 4 | 16 | Pest Feature |
| **TOTAL** | **50** | **200** | — |

### 🧪 6.2 Contoh Implementasi Test Kritis:

```php
// tests/Unit/StudentAccountServiceTest.php
it('menghasilkan email unik dari nama santri', function () {
    $service = app(StudentAccountService::class);
    $email = $service->generateEmail('Ahmad bin Rizki');
    expect($email)->toBe('ahm.rizki@alhikmah.com');
});

it('menambahkan angka jika terdapat email duplikat', function () {
    User::factory()->create(['email' => 'ahm.rizki@alhikmah.com']);
    $service = app(StudentAccountService::class);
    $email = $service->generateEmail('Ahmad bin Rizki');
    expect($email)->toBe('ahm.rizki1@alhikmah.com');
});

it('password mengandung kombinasi huruf besar, kecil, angka, dan simbol', function () {
    $service = app(StudentAccountService::class);
    $password = $service->generatePassword(8);
    expect($password)
        ->toMatch('/[A-Z]/')
        ->toMatch('/[a-z]/')
        ->toMatch('/[0-9]/')
        ->toMatch('/[!@#$%^&*]/');
});

// tests/Feature/PasswordManagementTest.php
it('santri bisa mereset password mandiri dan tercatat di audit log', function () {
    $student = Student::factory()->create();
    $oldPassword = 'Lama123!';
    $student->user->update(['password' => Hash::make($oldPassword)]);
    
    $response = $this->actingAs($student->user)
        ->post('/student/password/reset', [
            'current_password' => $oldPassword,
            'new_password' => 'Baru456!',
            'new_password_confirmation' => 'Baru456!',
        ]);
    
    $response->assertRedirect();
    expect(PasswordResetLog::where('user_id', $student->user->id)->exists())->toBeTrue();
});

it('wali bisa mereset password anak dan tercatat di audit log tanpa menyimpan hash', function () {
    $parent = ParentProfile::factory()->create();
    $child = Student::factory()->create(['parent_profile_id' => $parent->id]);
    
    $response = $this->actingAs($parent->user)
        ->post("/parent/children/{$child->id}/reset-password");
    
    $response->assertRedirect();
    $log = PasswordResetLog::latest()->first();
    expect($log->reset_method)->toBe('parent')
        ->and($log->changed_by)->toBe($parent->user->id)
        ->and($log)->not->toHaveKeys(['old_password_hash', 'new_password_hash']);
});
```

---

## 📅 7. TIMELINE IMPLEMENTASI (3 SPRINT)

### 🏃 Sprint 1 (Hari 1-5): Foundation & Authentication
- **Hari 1-2: Database & Model**
  - [ ] Eksekusi 8 file migration baru.
  - [ ] Implementasi 8 Model Eloquent beserta relasi dan *casts*.
  - [ ] Jalankan `BadgeSeeder` (15 badge katalog awal).
  - [ ] Jalankan tes relasi model & database schema.
- **Hari 3-4: Account Generation**
  - [ ] Buat `StudentAccountService.php`.
  - [ ] Integrasikan service ke alur registrasi santri baru.
  - [ ] Buat `EmailService.php` sebagai fallback notifikasi.
  - [ ] Buat 8 unit test untuk account generation.
- **Hari 5: Password Management**
  - [ ] Buat `StudentPasswordController.php`.
  - [ ] Tambahkan method `requestPasswordReset` di `ParentChildController.php`.
  - [ ] Pastikan logging audit tercatat di `password_reset_logs`.
  - [ ] Buat 8 feature test password management.

### 🏃 Sprint 2 (Hari 6-10): Core Gamification & Dashboard
- **Hari 6-7: Target Harian & Progress Juz**
  - [ ] Buat `StudentTargetController.php` & `MentorTargetController.php`.
  - [ ] Buat `HifzProgressService.php`.
  - [ ] Susun Blade views: `targets/today.blade.php`, `progress/juz.blade.php`.
  - [ ] Integrasikan target dengan `learning_sessions`.
- **Hari 8-9: Gamification Service & Badge System**
  - [ ] Buat `GamificationService.php`, `BadgeEvaluatorService.php`, dan `StreakTrackerService.php`.
  - [ ] Buat `ProgressObserver.php` dan daftarkan di `bootstrap/app.php` / Providers.
  - [ ] Susun Blade view: `badges/index.blade.php` dengan DataTables.
  - [ ] Buat 10 test case gamification.
- **Hari 10: Leaderboard & Countdown Widget**
  - [ ] Buat `LeaderboardService.php` (dengan cache Redis/File).
  - [ ] Implementasikan 5 scheduled Artisan commands.
  - [ ] Susun Blade view: `leaderboard.blade.php` dengan DataTables.
  - [ ] Buat widget countdown dengan animasi flip-clock.

### 🏃 Sprint 3 (Hari 11-14): Integration, QA & Deployment
- **Hari 11-12: Integrasi & Polishing**
  - [ ] Uji kompatibilitas dark mode di semua widget.
  - [ ] Uji responsivitas UI pada mobile, tablet, dan desktop.
  - [ ] Integrasikan pengiriman notifikasi WhatsApp dan Email.
  - [ ] Tambahkan fitur target jangka panjang (Milestone).
- **Hari 13: QA & Testing Komprehensif**
  - [ ] Jalankan seluruh 50 test cases (`php artisan test`).
  - [ ] Selesaikan bug fixing hasil QA.
  - [ ] Lakukan code review dan formatting menggunakan `vendor/bin/pint`.
- **Hari 14: Deployment & Rilis**
  - [ ] Deploy ke staging environment.
  - [ ] User Acceptance Testing (UAT) bersama perwakilan Santri, Wali, dan Mentor.
  - [ ] Deploy ke production.
  - [ ] Monitoring performa pada 24 jam pertama.

---

## 📂 8. STRUKTUR FILE BARU

```
al-hikmah-lms/
├── app/
│   ├── Console/Commands/
│   │   ├── RefreshLeaderboard.php
│   │   ├── MarkMissedTargets.php
│   │   ├── ResetDailyStreakCheck.php
│   │   ├── SnapshotLeaderboard.php
│   │   └── CleanupExpiredMilestones.php
│   ├── Events/
│   │   ├── ProgressRecorded.php
│   │   ├── TargetCompleted.php
│   │   ├── JuzMutqinAchieved.php
│   │   └── LeaderboardRefreshed.php
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── AdminBadgeController.php
│   │   │   └── AdminGamificationController.php
│   │   ├── Mentor/
│   │   │   └── MentorTargetController.php
│   │   ├── Parent/
│   │   │   └── ParentChildController.php (UPDATED)
│   │   └── Student/
│   │       ├── StudentDashboardController.php
│   │       ├── StudentTargetController.php
│   │       ├── StudentGamificationController.php
│   │       └── StudentPasswordController.php
│   ├── Listeners/
│   │   ├── UpdateJuzProgress.php
│   │   ├── EvaluateBadgeCriteria.php
│   │   ├── UpdateGamificationPoints.php
│   │   ├── UpdateStreakCounter.php
│   │   ├── AwardTargetBadge.php
│   │   ├── AwardJuzBadge.php
│   │   └── NotifyTopAchievers.php
│   ├── Models/
│   │   ├── HifzTarget.php
│   │   ├── JuzProgress.php
│   │   ├── Badge.php
│   │   ├── StudentBadge.php
│   │   ├── HifzMilestone.php
│   │   ├── LeaderboardSnapshot.php
│   │   ├── GamificationPoint.php
│   │   └── PasswordResetLog.php
│   ├── Observers/
│   │   └── ProgressObserver.php
│   └── Services/
│       ├── StudentAccountService.php
│       ├── GamificationService.php
│       ├── LeaderboardService.php
│       ├── HifzProgressService.php
│       ├── BadgeEvaluatorService.php
│       ├── StreakTrackerService.php
│       └── EmailService.php (BARU)
├── database/
│   ├── migrations/
│   │   ├── 20260828000001_create_hifz_targets_table.php
│   │   ├── 20260828000002_create_juz_progress_table.php
│   │   ├── 20260828000003_create_badges_table.php
│   │   ├── 20260828000004_create_student_badges_table.php
│   │   ├── 20260828000005_create_hifz_milestones_table.php
│   │   ├── 20260828000006_create_leaderboard_snapshots_table.php
│   │   ├── 20260828000007_create_gamification_points_table.php
│   │   ├── 20260828000008_create_password_reset_logs_table.php
│   │   ├── 20260828000009_add_gamification_columns_to_students_table.php
│   │   └── 20260828000010_add_mutqin_fields_to_progresses_table.php
│   └── seeders/
│       ├── BadgeSeeder.php
│       └── DefaultHifzMilestoneSeeder.php
├── resources/views/
│   └── student/
│       ├── dashboard.blade.php
│       ├── targets/
│       │   ├── today.blade.php
│       │   └── milestones.blade.php
│       ├── progress/
│       │   └── juz.blade.php
│       ├── leaderboard.blade.php
│       ├── badges/
│       │   ├── index.blade.php
│       │   └── hall-of-fame.blade.php
│       └── components/
│           ├── countdown-widget.blade.php
│           ├── target-card.blade.php
│           ├── progress-bar-juz.blade.php
│           ├── leaderboard-entry.blade.php
│           ├── badge-card.blade.php
│           └── celebration-modal.blade.php
├── routes/
│   └── web.php (UPDATED)
└── tests/
    ├── Feature/
    │   ├── Student/
    │   │   ├── StudentDashboardTest.php
    │   │   ├── StudentTargetTest.php
    │   │   ├── StudentGamificationTest.php
    │   │   └── StudentPasswordTest.php
    │   ├── Mentor/
    │   │   └── MentorTargetTest.php
    │   └── Parent/
    │       └── ParentChildPasswordTest.php
    └── Unit/
        ├── StudentAccountServiceTest.php
        ├── GamificationServiceTest.php
        ├── LeaderboardServiceTest.php
        └── BadgeEvaluatorServiceTest.php
```

---

## ⚠️ 9. ANALISIS RISIKO & MITIGASI

| # | Risiko Potensial | Tingkat Dampak | Strategi Mitigasi |
|---|---|---|---|
| **R1** | Query Leaderboard lambat saat ribuan santri | 🔴 High | Gunakan Redis cache, snapshot periodik, serta index komposit pada database |
| **R2** | Email Service gagal mengirim kredensial | 🟡 Medium | Implementasikan fallback otomatis ke in-app notification & log pengiriman |
| **R3** | Kesulitan koordinasi implementasi bagi Junior Dev | 🟡 Medium | Ikuti urutan langkah di PRD ini + daily standup code review bersama Senior Dev |
| **R4** | Warna widget rusak pada Dark Mode | 🟡 Medium | Terapkan CSS Variables global standar Etrain & verifikasi visual via checklist QA |
| **R5** | Potensi penyalahgunaan reset password | 🔴 High | Terapkan rate limiter (maks 3x/jam), batasi otorisasi role, dan catat audit log lengkap |

---

## ✅ 10. DEFINITION OF DONE (DoD)

Fitur dinyatakan selesai dan siap dirilis ke production jika memenuhi kriteria:
- [ ] ✅ 50 test cases berjalan sukses (200+ assertions passing).
- [ ] ✅ Dark mode terintegrasi penuh tanpa glitch pada seluruh widget.
- [ ] ✅ DataTables 3.0.2 aktif pada seluruh list view.
- [ ] ✅ Desain antarmuka responsif sempurna di perangkat mobile, tablet, dan desktop.
- [ ] ✅ UI menggunakan 100% Bahasa Indonesia baku.
- [ ] ✅ Relasi `learning_sessions` dengan `hifz_targets` teruji dan berfungsi baik.
- [ ] ✅ Seluruh proses reset password tercatat di `password_reset_logs` tanpa menyimpan hash password.
- [ ] ✅ Alur notifikasi WhatsApp / Email / In-App fallback berjalan lancar.
- [ ] ✅ Code review di-approve oleh Senior Developer.
- [ ] ✅ Kode terformat rapi sesuai standar Laravel Pint (`vendor/bin/pint`).
- [ ] ✅ Waktu load dashboard santri < 2 detik pada kondisi normal.
- [ ] ✅ Nol bug kategori P0/P1 selama 7 hari masa uji coba staging.

---

## 📝 11. CATATAN UNTUK JUNIOR PROGRAMMER

### 🎯 Prioritas Urutan Pengerjaan:
1. **Hari 1–5**: Fokus selesaikan Database Migration, Model, dan Account Generation Service (Fondasi).
2. **Hari 6–10**: Fokus pada Core Gamification (Target Harian, Progress Juz, Badge System, dan Leaderboard Caching).
3. **Hari 11–14**: Fokus pada Integrasi Notifikasi, Dark Mode Polishing, dan QA Automation Testing.

### 📚 Referensi File yang Wajib Dibaca:
- `tentang.md`: Dokumentasi lengkap arsitektur AL-HIKMAH LMS v8.0.
- `app/Services/WhatsAppService.php`: Pola integrasi notifikasi pihak ketiga.
- `app/Models/Progress.php`: Contoh model dengan relasi data mutaba'ah.
- `public/assets/etrain/`: Direktori aset CSS & JS untuk tema UI antarmuka.

### 🆘 Panduan Saat Menghadapi Kendala:
- Lakukan konsultasi dengan Senior Developer pada sesi daily standup.
- Periksa konsistensi penamaan tabel, model, dan controller sesuai daftar di atas sebelum membuat file baru.
- Jalankan automated test secara lokal (`php artisan test`) sebelum mengajukan Pull Request (PR).

---

*Disusun oleh: Tim Product & Development AL-HIKMAH LMS*  
*Tanggal: 28 Agustus 2026*  
*Versi: 2.0 (Revisi Final)*  
*Status: ✅ SIAP DIBAGIKAN KE JUNIOR PROGRAMMER*