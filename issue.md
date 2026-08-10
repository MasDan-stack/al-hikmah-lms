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
- ⚠️ **Relasi RBAC (`roles` & `users`):** Model `User` dan `Role` telah dikonfigurasi, perlu pemisahan rute dashboard per role (`admin`, `mentor`, `parent`, `student`).

### B. Controller, Middleware & Layout Integrasi (BERJALAN / SELESAI ✅)
- ✅ **Sinkronisasi File Assets Publik:** File `style.css` dan `scripts.js` terbaru yang telah direfaktorisasi telah disalin dan disinkronkan ke `public/assets/css/` dan `public/assets/js/`.
- ✅ **Improvement Landing Layout ([landing.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layouts/landing.blade.php)):** Menambahkan link aksesibilitas native `.skip-to-main` dan menghapus duplikasi skrip inline `themeToggle`.
- ✅ **Layout Auth Terpadu ([auth-layout.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layout/auth-layout.blade.php)):** Dibuat khusus untuk mendukung halaman error `401.blade.php`, `403.blade.php`, serta form Login dan Register dengan desain AL-HIKMAH (animasi Islami, kartu glassmorphism, dan toggle dark mode).
- ✅ **Desain Halaman Auth ([login.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/login.blade.php) & [register.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/register.blade.php)):** Mengadopsi sistem desain utama AL-HIKMAH lengkap dengan validasi visual dan tombol gradasi hijau.
- ✅ **Controller Auth Blade Rendering Fix ([AuthenticatedSessionController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Auth/AuthenticatedSessionController.php) & [RegisteredUserController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Auth/RegisteredUserController.php)):** Diperbarui agar merender tampilan Blade view (`auth.login`, `auth.register`, `auth.forgot-password`) alih-alih merender halaman Inertia default, sehingga rute `http://127.0.0.1:8000/login` dan `http://127.0.0.1:8000/register` langsung menggunakan desain `style.css` dan `scripts.js`.
- ✅ **Desain Admin Layout & Dashboard ([admin.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layouts/admin.blade.php) & [dashboard.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/admin/dashboard.blade.php)):** Mengadopsi `public/assets/css/style.css` dan `public/assets/js/scripts.js`, dilengkapi sidebar branding, toggle dark mode, kartu statistik berikon, dan tabel aktivitas terbaru.
- ⚠️ **Feature Test Mismatch (RBAC & Auth):** Ditemukan 16 tes di `tests/Feature` yang memerlukan penyesuaian redirect role-based dashboard agar seluruh test suite Pest berjalan hijau (100% passing).

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
5. **[NEXT] Pengembangan Livewire & Flux UI Components:**
   - Integrasikan komponen `DashboardStats`, `SessionCalendar`, `ProgressTracker`, dan `RegistrationForm`.
6. **[NEXT] Pengujian Otomatis (Pest Tests):**
   - Sesuaikan rute auth & role dashboard agar seluruh Feature Test Pest berjalan hijau (100% passing).

---
*Dokumen ini diperbarui secara otomatis oleh Antigravity IDE sebagai referensi pengembangan dan perbaikan proyek AL-HIKMAH LMS.*
