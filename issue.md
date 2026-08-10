# DOCUMENTASI ISSUE & IMPROVEMENT ROADMAP — AL-HIKMAH LMS

> **Tanggal Audit:** 10 Agustus 2026  
> **Status Proyek:** Approved Planning & Active Refactoring  
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

Berdasarkan dokumen spesifikasi `alhikmah.md`, berikut adalah analisis celah (*gap analysis*) antara spesifikasi dan implementasi codebase saat ini:

### A. Core Database Migrations & Models
- ❌ **Tabel `programs` & Model `Program`:** Belum dibuat migration & model untuk mengelola master data program (Tahsin, Tahfidz, Iqra, Ghorib, Tajwid).
- ❌ **Tabel `student_program` (Pivot):** Belum ada tabel relasi enrollment antara santri dan program.
- ❌ **Tabel `progress` & Model `Progress`:** Belum ada skema pencatatan capaian bacaan/hafalan (Surah, Ayat, Juz, Nilai Tajwid, Nilai Adab, Evaluasi).
- ❌ **Tabel `payments` & Model `Payment`:** Belum ada skema invoice dan riwayat pembayaran SPP/program.
- ❌ **Tabel `galleries` & `notifications`:** Belum ada migration pendukung media dan notifikasi.
- ⚠️ **Relasi RBAC (`roles` & `users`):** Model `User` dan `Role` perlu dipertegas dengan Spatie/Custom Middleware untuk 4 role utama: `admin`, `mentor`, `parent`, `student`.

### B. Controller & Middleware
- ❌ **Middleware Role Access Control:** Belum ada kustom middleware `role:admin`, `role:mentor`, `role:parent`, `role:student` yang mengamankan rute terpisah.
- ❌ **Resource Controllers:** Controller CRUD untuk Student, Mentor, Program, Session, dan Progress belum lengkap di folder `app/Http/Controllers/Admin/`.
- ⚠️ **Feature Test Mismatch (RBAC & Auth):** 16 tes di `tests/Feature` mengalami kegagalan karena registrasi user baru menetapkan role `student`, sementara rute `/dashboard` secara otomatis mengarahkan ke `admin.dashboard` yang mewajibkan middleware `role:admin`. Diperlukan pemisahan dashboard route per role (Admin Dashboard vs Student Portal) agar test suite Pest berjalan hijau (100% passing).

---

## 3. AUDIT & IMPROVEMENT TEMPLATE CODEBASE

Berikut adalah rincian masalah, temuan teknis, dan perbaikan yang telah/harus diterapkan pada file template UI:

### A. Audit & Improvement: `template/assets/css/style.css`

| No | Poin Masalah | Deskripsi Technical Debt | Tindakan Solusi / Improvement |
|---|---|---|---|
| 1 | **External Resource Dependency** | Baris 211 menggunakan `url('https://image.qwenlm.ai/...')` untuk `.bg-islamic-animation`. Sangat berisiko *broken link* atau gagal muat saat koneksi lambat/offline. | Ganti dengan CSS Radial Gradient & SVG Pattern lokal yang independen dan ringan. |
| 2 | **Overuse `!important` Flags** | Banyak deklarasi `!important` pada kelas navbar dan button (misal baris 403, 413, 471, 520, 529). Memperburuk *CSS specificity maintainability*. | Refactor menggunakan CSS Custom Properties (`var(--...)`) dan cascading rule yang lebih selektif. |
| 3 | **Dark Mode Contrast Ratio** | Rasio kontras warna `--text-muted` (#7a7a9e) dan `--border-color` di mode gelap belum memenuhi standar WCAG 2.1 AA (min 4.5:1). | Tingkatkan kecerahan warna text-muted (#9a9abf) dan border-color pada mode `[data-bs-theme="dark"]`. |
| 4 | **Responsive Navbar Clipping** | Pada layar HP dengan tinggi minim (landscape mode), `.navbar-collapse` dengan `max-height: 80vh` dapat mengalami isu *content overflow*. | Tambahkan `overscroll-behavior: contain` dan optimasi scrollbar internal menu mobile. |
| 5 | **Modern CSS Utility Additions** | Belum ada styling utility untuk form validation state (`.is-invalid`, `.invalid-feedback`) dan animasi halus untuk modal/alert. | Tambahkan kelas CSS kustom untuk masukan validasi form dan state komponen Livewire/Flux UI. |

### B. Audit & Improvement: `template/assets/js/scripts.js`

| No | Poin Masalah | Deskripsi Technical Debt / Bug | Tindakan Solusi / Improvement |
|---|---|---|---|
| 1 | 🔴 **WhatsApp Encoding Bug (CRITICAL)** | String pesan WA diisi karakter `%0A` secara manual lalu diproses ulang oleh `encodeURIComponent(message)` (baris 304 & 315). Hasilnya `%250A`, menyebabkan format enter/ganti baris pada WhatsApp rusak. | Gunakan karakter newline asli (`\n`) pada string JavaScript sebelum diproses oleh `encodeURIComponent()`. |
| 2 | 🟡 **DOM Query Performance Overhead** | Fungsi `updateActiveNav()` melakukan `document.querySelectorAll('.nav-link')` berulang kali di dalam event loop scroll. | Temukan elemen Nodelist satu kali (*cached DOM references*) di luar event handler scroll. |
| 3 | 🟡 **Skip-To-Main Accessibility** | Elemen `.skip-to-main` disuntikkan secara dinamis via JS (baris 344-351) setelah DOM ready. Pengguna screen reader kehilangan akses fokus keyboard di awal render. | Pindahkan markup `.skip-to-main` secara native ke Blade Layout utama (`app.blade.php` & `home.blade.php`). |
| 4 | 🟢 **Form Error Message UI Feedback** | Handler `submit` hanya menambah class `.is-invalid` tanpa menampilkan pesan teks bantuan error di bawah input. | Buat helper JS untuk meng-generate pesan `.invalid-feedback` secara otomatis jika field required belum diisi. |
| 5 | 🟢 **Intersection Observer Cleanup** | Callback observer scroll-reveal menggunakan `setTimeout` tanpa membatalkan timer jika elemen keluar dari viewport sebelum revealed. | Gunakan CSS Custom Property `--reveal-delay` agar timing dikendalikan langsung oleh engine browser. |

---

## 4. ACTION PLAN & ROADMAP EKSEKUSI

1. **[SELESAI] Refactoring Template Assets (`style.css` & `scripts.js`):**
   - Terapkan perbaikan bug WhatsApp encoding, optimasi DOM query, pembersihan `!important`, dan aksesibilitas kontras warna.
2. **[NEXT] Migrasi Database Schema Lengkap:**
   - Jalankan `php artisan make:migration` untuk tabel `programs`, `student_program`, `progress`, `payments`, `galleries`, `notifications`.
3. **[NEXT] Pembuatan Model & Factory:**
   - Buat Model `Program`, `Progress`, `Payment`, `Gallery`, `Notification` beserta Factory-nya sesuai standar Laravel 12 & Laravel Boost.
4. **[NEXT] Pengembangan Livewire & Flux UI Components:**
   - Integrasikan komponen `DashboardStats`, `SessionCalendar`, `ProgressTracker`, dan `RegistrationForm`.
5. **[NEXT] Pengujian Otomatis (Pest Tests):**
   - Buat Feature Test untuk alur registrasi, validasi form, dan pencatatan progres hafalan/bacaan santri.

---
*Dokumen ini dibuat otomatis oleh Antigravity IDE sebagai referensi pengembangan dan perbaikan proyek AL-HIKMAH LMS.*
