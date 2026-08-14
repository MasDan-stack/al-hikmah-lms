# 📋 PRODUCT REQUIREMENTS DOCUMENT (PRD) & ARSITEKTUR IMPLEMENTASI
## MODUL MANAJEMEN KETERSEDIAAN GURU & ALOKASI SANTRI (ENTERPRISE PRODUCTION-GRADE)

> **Proyek:** AL-HIKMAH Learning Management System (LMS)  
> **Framework:** Laravel 12 (PHP 8.2+) | Bootstrap 5 | Blade | MySQL  
> **Level Panduan:** Blueprint & Step-by-Step Guide for Junior Programmer  
> **Dokumen Terkait:** `modul.md`, `modul-issue.md`, `final.md`  
> **Target Pengguna:** Administrator Lembaga, Ustadz / Mentor Al-Qur'an, Orang Tua / Wali Santri  
> **Status:** ✅ 100% Implemented, Production-Grade Architecture & Testing Passed  

---

## 1. EXECUTIVE SUMMARY

### 1.1 Problem Statement (Akar Masalah Arsitektur)
Pada sistem operasional pendampingan Al-Qur'an di AL-HIKMAH LMS, ditemukan beberapa kendala teknis krusial:
1. **Peluang Race Condition & Overbooking:** Pengecekan kuota mentor dan mutasi alokasi santri sebelumnya terpisah tanpa `DB::transaction()` dan *pessimistic locking* (`lockForUpdate()`), berisiko *overbooking* jika ada admin bekerja bersamaan.
2. **N+1 Query Bottleneck pada Matriks & API Filter:** Halaman matriks dan API filter mentor sebelumnya menjalankan puluhan hingga ratusan query berulang di dalam *nested loop*.
3. **Fat Controller & Violation of Single Responsibility:** Controller admin mengurus validasi manual, mutasi database, pembuatan notifikasi in-app, dan log aktivitas secara synchronous.
4. **Ketiadaan Fitur Cuti Spesifik (Date Exception) & Guru Pengganti (Inval):** Sistem awal hanya mendukung siklus mingguan (Senin–Minggu) tanpa penanganan tanggal libur insidental.

### 1.2 Proposed Solution (Solusi Sistem)
Membangun **Modul Manajemen Ketersediaan Guru & Alokasi Santri Terpadu berstandar Enterprise**:
- **Action Service Pattern & Concurrency Safety:** Memindahkan logika alokasi ke `AssignStudentAction` terproteksi `DB::transaction()` dan `lockForUpdate()`.
- **Form Request Isolation:** Memisahkan validasi ke `AssignStudentRequest`.
- **Single Aggregated Query Matrix & API Filter (0 N+1 Queries):** Menggunakan query `GROUP BY mentor_id, day_assigned` dan agregasi *in-memory*.
- **Event-Driven Architecture (EDA):** Mengisolasi efek samping notifikasi dan audit log via Domain Event `StudentAssignedToMentor` dan Queued Listeners (`ShouldQueue`).
- **Composite Indexing & Date Exceptions:** Menambahkan indeks komposit `idx_mentor_day_active` dan skema tabel cuti `mentor_leaves`.

### 1.3 Success Criteria (Measurable KPIs)
| Indikator Keberhasilan | Target | Metode Pengukuran |
| :--- | :---: | :--- |
| **Kecepatan Alokasi Santri** | < 2 menit | Waktu Admin memilih santri hingga alokasi tersimpan |
| **Concurrency Overbooking Risk** | 0% (Atomic Safe) | Pessimistic locking assertion & transaction isolation |
| **Jumlah Query DB Matriks & API** | ≤ 3 Queries Total | DB Query Log Assertion (Bebas N+1) |
| **Visibilitas Kontak Wali Santri** | 100% | Seluruh guru dapat mengakses nomor WhatsApp wali santri binaan secara langsung |
| **Automated Test Coverage** | 100% Pass | All Feature & Action Tests pass green |

---

## 2. USER EXPERIENCE & FUNCTIONALITY

### 2.1 User Personas
1. **Admin Operasional**: Mengelola penempatan santri baru ke mentor yang sesuai, memantau beban mengajar mentor agar seimbang.
2. **Ustadz / Mentor**: Mengatur jadwal ketersediaan pribadi, mengajukan cuti pada tanggal spesifik, dan berkomunikasi dengan orang tua.
3. **Orang Tua / Wali**: Menerima kepastian jadwal belajar ananda dan notifikasi pengampu belajar.

### 2.2 User Stories & Acceptance Criteria

#### A. MODUL ADMIN: MATRIKS KETERSEDIAAN & ALOKASI AMAN CONCURRENCY
- **User Story:** *Sebagai Admin, saya ingin melihat matriks jadwal 7 hari seluruh mentor beserta kuotanya secara bebas dari race condition dan N+1 query.*
- **Acceptance Criteria (AC):**
  - [ ] **AC-A1:** Validasi input ditangani oleh `AssignStudentRequest` secara terpisah dari Controller.
  - [ ] **AC-A2:** Alokasi diproses melalui `AssignStudentAction` terbungkus `DB::transaction()` dan `lockForUpdate()`.
  - [ ] **AC-A3:** Matriks 7 hari ter-render dalam 1 query agregasi `GROUP BY` (0 N+1 query).
  - [ ] **AC-A4:** Pelepasan santri (*unassign*) mengupdate status pivot `is_active = false`.

#### B. MODUL EVENT & NOTIFIKASI LATAR BELAKANG
- **User Story:** *Sebagai Mentor & Wali Santri, kami ingin mendapat notifikasi pengampuan secara otomatis tanpa memperlambat aplikasi.*
- **Acceptance Criteria (AC):**
  - [ ] **AC-B1:** Alokasi berhasil memicu Domain Event `StudentAssignedToMentor`.
  - [ ] **AC-B2:** Listeners `SendAssignmentNotificationListener` dan `LogMentorActivityListener` mengimplementasikan `ShouldQueue`.

---

## 3. TECHNICAL SPECIFICATIONS & ARCHITECTURE

### 3.1 Data Flow Architecture

```
[Admin Lembaga] ──► [AssignStudentRequest]
                          │
                          ▼
           [MentorAvailabilityController]
                          │
                          ▼
            [AssignStudentAction::execute()]
                          │
       ┌──────────────────┴───────────────────────┐
       │   DB::transaction() & lockForUpdate()    │
       │   ├─ 1. Lock Row Mentor & Availability   │
       │   ├─ 2. Validasi Kuota Terkunci          │
       │   ├─ 3. Validasi Bentrok Hari Santri     │
       │   └─ 4. $mentor->students()->attach()    │
       └──────────────────┬───────────────────────┘
                          │
                          ▼
             event(StudentAssignedToMentor)
                          │
          ┌───────────────┴───────────────┐
          ▼                               ▼
[SendAssignmentNotification]     [LogMentorActivity]
```

---

## 4. STEP-BY-STEP IMPLEMENTATION GUIDE (FOR JUNIOR PROGRAMMERS)

1. **Langkah 1 (Database Migrations & Indexing):**
   - Migrasi indeks komposit `mentor_student` (`idx_mentor_day_active`) dan tabel cuti `mentor_leaves`.
   - Jalankan `php artisan migrate --no-interaction`.
2. **Langkah 2 (Form Request & Action Service):**
   - Buat Form Request `AssignStudentRequest.php` untuk isolasi validasi input.
   - Buat Action Class `AssignStudentAction.php` terproteksi `DB::transaction()` dan `lockForUpdate()`.
3. **Langkah 3 (Domain Event & Queued Listeners):**
   - Buat Event `StudentAssignedToMentor.php` dan Listeners `SendAssignmentNotificationListener.php` & `LogMentorActivityListener.php`.
   - Daftarkan listener di `AppServiceProvider::boot()`.
4. **Langkah 4 (Refactor Controller Admin & Views):**
   - Perbarui `MentorAvailabilityController::index()` dan `getAvailableMentors()` dengan single query agregasi.
   - Sambungkan method `assignStudent()` ke `AssignStudentAction`.
5. **Langkah 5 (Formatting & Automated Testing):**
   - Format dengan `vendor/bin/pint --dirty --format agent`.
   - Eksekusi pengujian `php artisan test --filter=MentorAvailabilityTest --compact`.
