# 🎯 PLANNING & ROADMAP PENGEMBANGAN AL-HIKMAH LMS (UPDATED)

> **Dokumen Panduan Teknis, Audit Proyek, & Roadmap Perbaikan**  
> **Target Pembaca:** Developer / Junior Programmer  
> **Versi:** 1.1 (Terbaru - Update Issue Baru & Penataan Folder)  
> **Framework & Tech Stack:** Laravel 12 | Livewire 4.3 | Flux UI | Bootstrap 5 | MySQL | Pest PHP  

---

## 📌 DAFTAR ISI
1. [Ringkasan Proyek & Status Terkini](#1-ringkasan-proyek--status-terkini)
2. [Analisis Issue Baru & Rencana Perbaikan (Immediate Tasks)](#2-analisis-issue-baru--rencana-perbaikan-immediate-tasks)
   - [Issue 1: Dynamic Role Dashboard Link pada Navbar (`navbar.blade.php`)](#issue-1-dynamic-role-dashboard-link-pada-navbar-navbarbladephp)
   - [Issue 2: Optimasi Event Delegation Dark Mode (`scripts.js` & `style.css`)](#issue-2-optimasi-event-delegation-dark-mode-scriptsjs--stylecss)
   - [Issue 3: Penataan & Standardisasi Folder Layouts (`resources/views/layouts/`)](#issue-3-penataan--standardisasi-folder-layouts-resourcesviewslayouts)
3. [Audit Komprehensif Proyek & Improvement Roadmap](#3-audit-komprehensif-proyek--improvement-roadmap)
4. [Panduan Eksekusi Langkah Demi Langkah untuk Junior Programmer](#4-panduan-eksekusi-langkah-demi-langkah-untuk-junior-programmer)
5. [Standard Operating Procedure (SOP) & Pengujian Code](#5-standard-operating-procedure-sop--pengujian-code)

---

## 1. RINGKASAN PROYEK & STATUS TERKINI

AL-HIKMAH LMS adalah platform manajemen pembelajaran Al-Qur'an untuk anak usia 10–15 tahun yang menghubungkan **Orang Tua**, **Murid/Santri**, **Pendamping/Guru**, dan **Admin**.

### Status Audit Terkini:
- ✅ **Database Schema, Migrations & Models:** Tabel `programs`, `student_program`, `progress`, `payments`, `galleries`, `notifications`, `roles`, `users`, `parents`, `mentors`, dan `students` telah selesai dibuat beserta model Eloquent & Factory-nya.
- ✅ **Multi-Role Authentication Routing:** Rute `/admin/dashboard`, `/mentor/dashboard`, `/parent/dashboard`, dan `/student/dashboard` telah terdaftar dan memiliki penanganan rute masing-masing.
- ⚠️ **Issue Baru yang Perlu Ditangani (Batch 2):**
  1. Tombol `Dashboard` di navbar website depan (`navbar.blade.php`) masih di-hardcode ke `route('admin.dashboard')`, menyebabkan error 403 saat di-klik oleh user bernon-admin (`mentor`, `parent`, `student`).
  2. Tombol Dark Mode pada `navbar.blade.php` memerlukan peningkatkan keandalan event listener (*Event Delegation*) agar merespons sempurna pada tampilan desktop maupun mobile (HP).
  3. Struktur folder layout belum standar: file `auth-layout.blade.php` berada di folder `resources/views/layout/` (singular), padahal layout lain ada di `resources/views/layouts/` (plural).

---

## 2. ANALISIS ISSUE BARU & RENCANA PERBAIKAN (IMMEDIATE TASKS)

### Issue 1: Dynamic Role Dashboard Link pada Navbar (`navbar.blade.php`)

#### 🔴 Penyebab Masalah:
Pada file `resources/views/partials/navbar.blade.php` di bagian `@auth`:
```blade
@auth
    <a href="{{ route('admin.dashboard') }}" class="btn btn-daftar">
        <i class="bi bi-speedometer2 me-1"></i> Dashboard
    </a>
...
```
Tombol mengarah langsung ke `route('admin.dashboard')`. Jika pengajar (mentor) atau orang tua login lalu menekan tombol Dashboard di navbar utama, mereka akan diarahkan ke `/admin/dashboard` yang menghasilkan halaman `401 / 403 Forbidden` karena middleware RBAC memblokir akses selain admin.

#### 🟢 Solusi & Rencana Perbaikan:
Ubah tautan tombol menjadi `route('dashboard')`:
```blade
@auth
    <a href="{{ route('dashboard') }}" class="btn btn-daftar">
        <i class="bi bi-speedometer2 me-1"></i> Dashboard
    </a>
...
```
*Penjelasan untuk Junior Dev:* Rute `route('dashboard')` di `routes/web.php` telah dirancang untuk mengecek role user secara otomatis (`isAdmin()`, `isMentor()`, `isParent()`, `isStudent()`) lalu melakukan redirect ke dashboard masing-masing.

---

### Issue 2: Optimasi Event Delegation Dark Mode (`scripts.js` & `style.css`)

#### 🔴 Penyebab Masalah:
1. **Target Event Clicking:** Saat user mengklik ikon elemen `<i class="bi bi-moon-fill">` di dalam tombol `<button class="theme-toggle-btn">`, beberapa event listener dasar gagal menangkap target tombol secara tepat.
2. **Dynamic / Mobile Layout Overhead:** Pada layar HP/responsive (`max-width: 991.98px`), tombol theme toggle di navbar memiliki penataan posisi CSS yang membutuhkan jaminan `z-index` agar tidak tertutup elemen mobile collapse.

#### 🟢 Solusi & Rencana Perbaikan:
1. **Gunakan Event Delegation di `public/assets/js/scripts.js`:**
   ```javascript
   document.addEventListener('click', function (e) {
       const toggleBtn = e.target.closest('.theme-toggle-btn, #themeToggle');
       if (toggleBtn) {
           e.preventDefault();
           const html = document.documentElement;
           const isDark = html.getAttribute('data-bs-theme') === 'dark';
           setTheme(!isDark);
       }
   });
   ```
   *Keuntungan Event Delegation:* Event listener terpasang pada level `document`, sehingga tombol dark mode baru atau tombol di menu mobile yang dimuat secara dinamis akan langsung terdeteksi tanpa perlu memasang listener berulang kali.

---

### Issue 3: Penataan & Standardisasi Folder Layouts (`resources/views/layouts/`)

#### 🔴 Penyebab Masalah:
Struktur direktori layout saat ini terpisah di dua folder yang membingungkan:
- `resources/views/layout/auth-layout.blade.php` (Folder singular: `layout`)
- `resources/views/layouts/landing.blade.php` (Folder plural: `layouts`)
- `resources/views/layouts/admin.blade.php` (Folder plural: `layouts`)

#### 🟢 Solusi & Rencana Perbaikan:
1. Pindahkan file `resources/views/layout/auth-layout.blade.php` ke `resources/views/layouts/auth-layout.blade.php`.
2. Perbarui seluruh direktif `@extends` di file views yang mereferensikan layout tersebut dari:
   `@extends('layout.auth-layout')` ➔ `@extends('layouts.auth-layout')`
   
   *Daftar File yang Diperbarui:*
   - [resources/views/dashboard.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/dashboard.blade.php)
   - [resources/views/auth/login.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/login.blade.php)
   - [resources/views/auth/register.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/register.blade.php)
   - [resources/views/auth/forgot-password.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/forgot-password.blade.php)
   - [resources/views/errors/401.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/errors/401.blade.php)
   - [resources/views/errors/403.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/errors/403.blade.php)
3. Hapus folder kosong `resources/views/layout/`.

---

## 3. AUDIT KOMPREHENSIF PROYEK & IMPROVEMENT ROADMAP

Berdasarkan analisis arsitektur proyek dan spesifikasi SRS ([alhikmah.md](file:///c:/xampp/htdocs/al-hikmah-lms/alhikmah.md)), berikut adalah daftar improvement yang direkomendasikan untuk dikembangkan pada sprint berikutnya:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      AL-HIKMAH IMPROVEMENT ROADMAP                      │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  [PHASE 1: UI & CORE AUTH FIXES] ──► [PHASE 2: LIVEWIRE 4.3 COMPONENTS] │
│  - Dynamic Navbar Dashboard Link      - DashboardStats Component        │
│  - Dark Mode Event Delegation         - SessionCalendar Component       │
│  - Layout Folder Standardization      - ProgressTracker Component       │
│                                                                         │
│  [PHASE 3: ADMIN MASTER DATA CRUD] ──► [PHASE 4: REPORTS & SERVICES]    │
│  - Student & Parent Management        - Dompdf Monthly Progress Report  │
│  - Mentor Management & Ratings        - WA Gateway Job Reminder         │
│  - Program & Payment Transactions     - Pest Automated Test Suite       │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

| No | Modul / Fitur | Rincian Technical Debt & Improvement | Komponen / Tech | Prioritas |
|---|---|---|---|---|
| 1 | **Livewire 4.3 Stats** | Membuat `DashboardStats.php` untuk menampilkan statistik reaktif real-time (Total Murid, Pendamping, Sesi Aktif) pada Admin & Mentor Dashboard. | Livewire 4.3 + Flux UI | P1 |
| 2 | **Livewire Calendar** | Membuat `SessionCalendar.php` untuk mengelola penjadwalan sesi belajar (Online/Offline) per santri dan pendamping. | Livewire 4.3 | P1 |
| 3 | **Progress Tracker** | Membuat `ProgressTracker.php` untuk mencatat hafalan/bacaan Al-Qur'an (Surah, Ayat, Juz, Nilai Tajwid & Adab). | Livewire 4.3 | P1 |
| 4 | **CRUD Master Admin** | Melengkapi Controller & Views Admin untuk kelola data Santri, Mentor, Program, dan Invoice Pembayaran. | Laravel Controller / Livewire | P1 |
| 5 | **Cetak Laporan PDF** | Mengintegrasikan paket Dompdf untuk meng-generate PDF laporan berkala perkembangan anak bagi Orang Tua. | Dompdf / Snappy | P2 |
| 6 | **Integrasi WA Gateway** | Pengiriman otomatis pengingat jadwal sesi H-1 dan ringkasan progres ke nomor WhatsApp Orang Tua. | Queue Jobs + WA API | P2 |

---

## 4. PANDUAN EKSEKUSI LANGKAH DEMI LANGKAH UNTUK JUNIOR PROGRAMMER

Jika perbaikan ini disetujui, ikuti panduan urutan kerja berikut secara presisi:

### Langkah 1: Perbarui Navbar Dashboard Link
1. Buka file `resources/views/partials/navbar.blade.php`.
2. Cari kode `<a href="{{ route('admin.dashboard') }}" class="btn btn-daftar">`.
3. Ganti menjadi `<a href="{{ route('dashboard') }}" class="btn btn-daftar">`.

### Langkah 2: Refactor Dark Mode Event Listener
1. Buka file `public/assets/js/scripts.js`.
2. Ganti logika pembacaan click event tombol theme toggle menjadi Event Delegation berbasis `document.addEventListener('click', ...)` menggunakan `e.target.closest('.theme-toggle-btn, #themeToggle')`.

### Langkah 3: Pindahkan Layout Auth & Perbarui References
1. Pindahkan file `resources/views/layout/auth-layout.blade.php` ke `resources/views/layouts/auth-layout.blade.php`.
2. Buka 6 file berikut dan sesuaikan `@extends('layout.auth-layout')` menjadi `@extends('layouts.auth-layout')`:
   - `resources/views/dashboard.blade.php`
   - `resources/views/auth/login.blade.php`
   - `resources/views/auth/register.blade.php`
   - `resources/views/auth/forgot-password.blade.php`
   - `resources/views/errors/401.blade.php`
   - `resources/views/errors/403.blade.php`
3. Hapus folder `resources/views/layout/`.

---

## 5. STANDARD OPERATING PROCEDURE (SOP) & PENGUJIAN CODE

Sebelum mengajukan Pull Request / Commit, WAJIB menjalankan perintah verifikasi berikut:

### 1. Formatter Kode PHP (Laravel Pint)
```bash
vendor/bin/pint --format agent
```

### 2. Jalankan Automated Test Suite (Pest)
```bash
php artisan test --compact
```

---
*Dokumen planning ini siap direview. Implementasi kode akan dilakukan setelah persetujuan.*
