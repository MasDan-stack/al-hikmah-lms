# DOCUMENTASI ISSUE & IMPROVEMENT ROADMAP — AL-HIKMAH LMS

> **Tanggal Update:** 10 Agustus 2026  
> **Status Proyek:** Active Implementation & UI Integration Complete  
> **Framework:** Laravel 12 | Livewire 4.3 | Flux UI | Laravel Boost 2.2  

---

## 1. STATUS LARAVEL BOOST & CONFIGURATION

- **Status Integrasi:** **AKTIF & SESUAI (COMPLIANT)** ✅
- **Temuan & Verifikasi:**
  1. File `boost.json` telah terkonfigurasi di root direktori dengan `{"agents": ["antigravity"], "guidelines": true}`.
  2. Dokumen `AGENTS.md` telah memuat panduan lengkap `<laravel-boost-guidelines>` (Foundation, Boost, PHP, Inertia/Livewire, Pint, Pest).
  3. Antigravity IDE telah secara otomatis membaca dan menerapkan aturan Laravel Boost pada workspace ini. Tidak memerlukan perubahan konfigurasi tambahan.

---

## 2. AUDIT KEBUTUHAN SISTEM & DATABASE (SRS GAP ANALYSIS)

Berdasarkan dokumen spesifikasi `alhikmah.md`, berikut adalah status analisis celah (*gap analysis*) terbaru:

### A. Core Database Migrations & Models (SELESAI ✅)
- ✅ **Tabel `programs` & Model `Program`:** Migration, Model, dan Factory selesai dibuat.
- ✅ **Tabel `student_program` (Pivot):** Migration pivot enrollment santri & program selesai dibuat.
- ✅ **Tabel `progress` & Model `Progress`:** Migration, Model, dan Factory pencatatan capaian hafalan/bacaan, Tajwid, dan Adab selesai dibuat.
- ✅ **Tabel `payments` & Model `Payment`:** Migration, Model, dan Factory transaksi invoice & pembayaran selesai dibuat.
- ✅ **Tabel `galleries` & `notifications`:** Migration, Model, dan Factory galeri media dan notifikasi user selesai dibuat.
- ✅ **Relasi RBAC (`roles` & `users`):** Model `User` dan `Role` telah dikonfigurasi beserta penanganan rute dashboard per role (`admin`, `mentor`, `parent`, `student`).

### B. Controller, Middleware & Layout Integrasi (SELESAI ✅)
- ✅ **Sinkronisasi File Assets Publik:** File `style.css` dan `scripts.js` terbaru yang telah direfaktorisasi telah disalin dan disinkronkan ke `public/assets/css/` dan `public/assets/js/`.
- ✅ **Improvement Landing Layout ([landing.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layouts/landing.blade.php)):** Menambahkan link aksesibilitas native `.skip-to-main` dan menghapus duplikasi skrip inline `themeToggle`.
- ✅ **Standardisasi Folder Layouts ([auth-layout.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layouts/auth-layout.blade.php)):** Dipindahkan ke `resources/views/layouts/` (plural) agar konsisten dengan `landing.blade.php` & `admin.blade.php`.
- ✅ **Desain Halaman Auth ([login.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/login.blade.php) & [register.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/register.blade.php)):** Mengadopsi sistem desain utama AL-HIKMAH lengkap dengan validasi visual dan tombol gradasi hijau.
- ✅ **Controller Auth Blade Rendering Fix ([AuthenticatedSessionController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Auth/AuthenticatedSessionController.php) & [RegisteredUserController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Auth/RegisteredUserController.php)):** Diperbarui agar merender tampilan Blade view (`auth.login`, `auth.register`, `auth.forgot-password`) alih-alih merender halaman Inertia default.
- ✅ **Desain Admin Layout & Dashboard ([admin.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layouts/admin.blade.php) & [dashboard.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/admin/dashboard.blade.php)):** Mengadopsi `public/assets/css/style.css` dan `public/assets/js/scripts.js`, dilengkapi sidebar branding, toggle dark mode, kartu statistik berikon, dan tabel aktivitas terbaru.

---

## 3. AUDIT & IMPROVEMENT TEMPLATE CODEBASE

Berikut adalah rincian masalah, temuan teknis, dan perbaikan yang telah diterapkan pada file template UI:

### A. Audit & Improvement: `style.css` (SELESAI ✅)

| No | Poin Masalah | Deskripsi Technical Debt | Tindakan Solusi / Improvement |
|---|---|---|---|
| 1 | **External Resource Dependency** | Baris 211 menggunakan `url('https://image.qwenlm.ai/...')` untuk `.bg-islamic-animation`. | Mengganti dengan CSS Radial Gradient & SVG Pattern lokal yang independen dan ringan. |
| 2 | **Overuse `!important` Flags** | Banyak deklarasi `!important` pada kelas navbar dan button. | Refactor menggunakan CSS Custom Properties (`var(--...)`) dan cascading rule yang lebih selektif. |
| 3 | **Dark Mode Contrast Ratio** | Rasio kontras warna `--text-muted` di mode gelap belum memenuhi standar WCAG 2.1 AA (min 4.5:1). | Meningkatkan kecerahan warna text-muted (`#9a9abf`) dan border-color pada mode `[data-bs-theme="dark"]`. |
| 4 | **Responsive Navbar Clipping** | Pada layar HP landscape, `.navbar-collapse` dengan `max-height: 80vh` mengalami *content overflow*. | Menambahkan `overscroll-behavior: contain` dan optimasi scrollbar internal menu mobile. |
| 5 | **Modern CSS Utility Additions** | Belum ada styling utility untuk form validation state (`.is-invalid`, `.invalid-feedback`). | Menambahkan kelas CSS kustom untuk masukan validasi form dan state komponen Livewire/Flux UI. |

### B. Audit & Improvement: `scripts.js` (SELESAI ✅)

| No | Poin Masalah | Deskripsi Technical Debt / Bug | Tindakan Solusi / Improvement |
|---|---|---|---|
| 1 | 🔴 **WhatsApp Encoding Bug (CRITICAL)** | String pesan WA diisi karakter `%0A` secara manual lalu diproses ulang oleh `encodeURIComponent(message)`. Hasilnya `%250A`, menyebabkan format enter rusak. | Menggunakan karakter newline asli (`\n`) pada string JavaScript sebelum diproses oleh `encodeURIComponent()`. |
| 2 | 🟡 **DOM Query Performance Overhead** | Fungsi `updateActiveNav()` melakukan `document.querySelectorAll('.nav-link')` berulang kali di dalam event loop scroll. | Menyimpan elemen Nodelist satu kali (*cached DOM references*) di luar event handler scroll. |
| 3 | 🟡 **Skip-To-Main Accessibility** | Elemen `.skip-to-main` disuntikkan secara dinamis via JS setelah DOM ready. | Memindahkan markup `.skip-to-main` secara native ke awal `<body>` Blade Layout utama (`landing.blade.php`). |
| 4 | 🟢 **Form Error Message UI Feedback** | Handler `submit` hanya menambah class `.is-invalid` tanpa pesan error visual. | Meng-generate elemen `.invalid-feedback` secara otomatis di bawah field required yang kosong. |
| 5 | 🟢 **Intersection Observer Cleanup** | Observer scroll-reveal menggunakan `setTimeout` tanpa membatalkan timer jika elemen keluar dari viewport. | Menggunakan CSS Custom Property `--reveal-delay` agar timing dikendalikan langsung oleh browser. |

---

## 4. ACTION PLAN & ROADMAP EKSEKUSI

1. **[SELESAI] Refactoring Template Assets (`style.css` & `scripts.js`):**
   - Perbaikan bug WhatsApp encoding, optimasi DOM query, dan kontras warna dark mode selesai diterapkan.
2. **[SELESAI] Migrasi Database Schema Lengkap:**
   - Tabel `programs`, `student_program`, `progress`, `payments`, `galleries`, dan `notifications` telah berhasil di-migrate.
3. **[SELESAI] Pembuatan Model & Factory:**
   - Model `Program`, `Progress`, `Payment`, `Gallery`, `Notification` beserta Factory-nya selesai dibuat & di-format via Pint.
4. **[SELESAI] Integrasi Desain UI Laravel (Landing, Auth & Admin):**
   - Assets disalin ke `public/assets/`, layout `landing.blade.php` diperbaiki, layout Auth `auth-layout.blade.php` & views `login.blade.php` / `register.blade.php` dibuat, serta `admin.blade.php` & `dashboard.blade.php` diselaraskan.
5. **[SELESAI] Pengembangan Livewire & Flux UI Components:**
   - Berhasil membuat komponen `DashboardStats`, `SessionCalendar`, `ProgressTracker`, dan `ReportController` (Cetak PDF).
6. **[SELESAI] Pembuatan Seeder Seluruh Modul:**
   - DatabaseSeeder, RoleSeeder, ProgramSeeder, UserSeeder, LearningSessionSeeder, ProgressSeeder, PaymentSeeder, GallerySeeder, dan NotificationSeeder telah siap dan diuji.

---

## 5. DOKUMENTASI ISSUE TERBARU & PANDUAN REVISI (BATCH 3 - SELESAI ✅)

1. ✅ **Fix Error SQL `scheduled_at` pada `DashboardStats.php` & `SessionCalendar.php`:** Diperbarui menjadi `date`.
2. ✅ **Fix Dark Mode Toggle pada Navbar:** Script path `footer.blade.php` dan CSS `.theme-toggle-btn` selesai disempurnakan.

---

## 6. DOKUMENTASI ISSUE TERBARU & PANDUAN REVISI (BATCH 4 - PERLU PERBAIKAN ⚠️)

Berikut adalah analisis teknis mendalam dan panduan perbaikan yang jelas dan aman untuk junior developer:

### 📌 Issue 1: Error SQL `Column not found: 1054 Unknown column 'date' in 'order clause'` pada `ProgressTracker.php`

- **Lokasi Error:**  
  [app/Livewire/ProgressTracker.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Livewire/ProgressTracker.php) (Line 13) dan [app/Http/Controllers/ReportController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/ReportController.php) (Line 15).
- **Deskripsi Error:**
  ```sql
  SQLSTATE[42S22]: Column not found: 1054 Unknown column 'date' in 'order clause'
  (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: alhikmah_lms, SQL: select * from `progress` order by `date` desc limit 10)
  ```
- **Penyebab Masalah (Root Cause Analysis):**
  1. **Tidak Ada Kolom `date` di Tabel `progress`:** File migration `2026_08_10_005002_create_progress_table.php` mendefinisikan kolom: `session_id`, `student_id`, `mentor_id`, `kategori`, `surah_start`, `surah_end`, `ayat_start`, `ayat_end`, `juz`, `nilai_fluent`, `nilai_tajwid`, `nilai_adab`, `catatan_evaluasi`, `homework`, serta `created_at` dan `updated_at`. Tabel ini **TIDAK memiliki kolom `date`**, sehingga pemanggilan `latest('date')` menyebabkan error SQL.
  2. **Relasi `program` Tidak Ada di Model `Progress`:** Pada `ProgressTracker.php` dipanggil `with(['student.user', 'mentor.user', 'program'])`. Model `Progress` (`app/Models/Progress.php`) hanya memiliki relasi `student`, `mentor`, dan `session`, tetapi tidak memiliki relasi `program`.
  3. **Mismatched Variable Names pada Blade View:** Pada [progress-tracker.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/livewire/progress-tracker.blade.php) dan [progress-pdf.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/reports/progress-pdf.blade.php), properti dipanggil dengan nama variabel dummy (`surah_name`, `verse_start`, `verse_end`, `tajwid_score`, `program->title`) yang berbeda dari nama kolom database yang asli (`surah_start`, `ayat_start`, `ayat_end`, `nilai_tajwid`, `kategori`).

- **Panduan Langkah Perbaikan untuk Junior Developer:**
  1. **Perbarui Query di `app/Livewire/ProgressTracker.php`:**
     ```php
     // Ubah dari:
     $progressList = Progress::with(['student.user', 'mentor.user', 'program'])->latest('date')->take(10)->get();

     // Menjadi:
     $progressList = Progress::with(['student.user', 'mentor.user'])->latest()->take(10)->get();
     ```
  2. **Perbarui Query di `app/Http/Controllers/ReportController.php`:**
     ```php
     // Ubah dari:
     $progressList = Progress::with(['program', 'mentor.user'])->latest('date')->take(15)->get();

     // Menjadi:
     $progressList = Progress::with(['student.user', 'mentor.user'])->latest()->take(15)->get();
     ```
  3. **Perbarui Variabel pada View `resources/views/livewire/progress-tracker.blade.php` & `resources/views/reports/progress-pdf.blade.php`:**
     - `$item->program?->title` ➔ Ganti ke `$item->kategori`
     - `$item->surah_name` ➔ Ganti ke `$item->surah_start`
     - `$item->verse_start` ➔ Ganti ke `$item->ayat_start`
     - `$item->verse_end` ➔ Ganti ke `$item->ayat_end`
     - `$item->tajwid_score` ➔ Ganti ke `$item->nilai_tajwid`
     - `$item->date` ➔ Ganti ke `$item->created_at ? $item->created_at->format('d M Y') : date('d M Y')`

---

## 7. DOKUMENTASI ISSUE TERBARU & STATUS REVISI (BATCH 5 - SELESAI ✅)

> **Status Issue #2 (https://github.com/MasDan-stack/al-hikmah-lms/issues/2):** **SELESAI (CLOSED) ✅**  
> **Status Automated Tests:** **30 PASSED (0 FAILED)**

### 📌 Ringkasan Implementasi Batch 5:

1. ✅ **Issue 1: Refactoring UI Halaman Program (`program.blade.php`) & Biaya (`biaya.blade.php`)**
   - Halaman **Program** telah diselaraskan 100% dengan `template/program.html` (3 Section: *Program Anak*, *Program Tambahan*, *Program Bahasa Arab* dengan `.arabic-featured`).
   - Halaman **Biaya** telah diselaraskan 100% dengan `template/biaya.html` (*Biaya Pendaftaran Rp 150.000* & 3 Kartu Paket Bulanan dengan ribbon **`⭐ Banyak Dipilih`**).
   - Mendukung *Dual-Rendering*: tetap menampilkan layout template HTML sekaligus merender data dinamis dari database bila tersedia di LMS.

2. ✅ **Issue 2: Dinamisasi Fitur Link WhatsApp & Kontak Website**
   - Membuat helper `wa_url($message)` di `app/Helpers/settings.php` dan mendaftarkannya pada `config/settings.php`, `.env`, serta `AppServiceProvider`.
   - Seluruh link WhatsApp di `tahfidz.blade.php` (L46-48 & L64), `landing.blade.php` (floating WA), `footer.blade.php`, `home.blade.php`, `program.blade.php`, `biaya.blade.php`, dan `metode.blade.php` telah diubah secara dinamis.
   - Mengintegrasikan `window.ALHIKMAH_CONFIG` pada `public/assets/js/scripts.js` agar pengiriman formulir pendaftaran via modal JS juga menggunakan nomor WA dari `.env`.

3. ✅ **Issue 3: Perencanaan CMS Setting Admin & Update Database Seeders (`issue-modul.md`)**
   - Menyiapkan dokumen perencanaan lengkap berformat GitHub Issue di `issue-modul.md` untuk Modul Pengaturan Website (`/admin/settings`) dan pembaharuan 10 modul seeder (`database/seeders/*`).
   - Menyiapkan dokumen Pull Request resmi di `PR.md` ([PR.md](file:///c:/xampp/htdocs/al-hikmah-lms/PR.md)).

---
*Dokumen issue.md ini diperbarui secara otomatis oleh Antigravity IDE untuk membantu proses tracking dan perbaikan proyek AL-HIKMAH LMS.*
