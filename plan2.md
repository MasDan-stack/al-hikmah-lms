# 📋 DOKUMEN PLANNING IMPROVEMENT AUTH REGISTRATION, ROLE SELECTION, & NAVBAR ACCESS CONTROL (`plan2.md`)

> **Target Pembaca:** Junior Developer / Development Team  
> **Versi:** 2.0 (Updated Rules for Navbar Access Control)  
> **Tanggal Update:** 11 Agustus 2026  
> **Fokus Utama:** Restrukturisasi Alur Pendaftaran Multi-Role (Orang Tua, Pendamping/Guru, Santri) & Aturan Ketat Akses Navigasi Navbar Berdasarkan Role

---

## 📌 DAFTAR ISI
1. [Ringkasan Masalah & Tujuan (`plan2.md`)](#1-ringkasan-masalah--tujuan-plan2md)
2. [Analisis Alur Pendaftaran (Registration Flow Analysis)](#2-analisis-alur-pendaftaran-registration-flow-analysis)
3. [Perancangan Alur Pendaftaran per Role](#3-perancangan-alur-pendaftaran-per-role)
   - [A. Alur Pendaftaran Pendamping / Guru (`/bergabung`)](#a-alur-pendaftaran-pendamping--guru-bergabung)
   - [B. Alur Pendaftaran Orang Tua / Wali (`/register`)](#b-alur-pendaftaran-orang-tua--wali-register)
   - [C. Alur Pendaftaran Murid / Santri (`/register?role=student`)](#c-alur-pendaftaran-murid--santri-registerrolestudent)
4. [Perancangan Hak Akses Navigasi Navbar (`navbar.blade.php`)](#4-perancangan-hak-akses-navigasi-navbar-navbarbladephp)
5. [Rincian Tugas & Panduan Eksekusi Langkah demi Langkah](#5-rincian-tugas--panduan-eksekusi-langkah-demi-langkah)
   - [Tugas 1: Integrasi Halaman `/bergabung` (`bergabung.blade.php`)](#tugas-1-integrasi-halaman-bergabung-bergabungbladephp)
   - [Tugas 2: Pilihan Role Dinamis pada Form Registrasi (`register.blade.php` & `RegisteredUserController.php`)](#tugas-2-pilihan-role-dinamis-pada-form-registrasi-registerbladephp--registeredusercontrollerphp)
   - [Tugas 3: Proteksi Menu Navigasi Biaya & Menjadi Pendamping pada Navbar (`navbar.blade.php`)](#tugas-3-proteksi-menu-navigasi-biaya--menjadi-pendamping-pada-navbar-navbarbladephp)
   - [Tugas 4: Pembuatan Automated Test Suite (Pest PHP)](#tugas-4-pembuatan-automated-test-suite-pest-php)
6. [Standard Operating Procedure (SOP) Pengujian & Verifikasi Kode](#6-standard-operating-procedure-sop-pengujian--verifikasi-kode)

---

## 1. RINGKASAN MASALAH & TUJUAN (`plan2.md`)

### 🔴 Masalah Saat Ini & Revisi Aturan Navbar:
1. **Pendaftaran Statis Single Role:**
   - Registrasi di-hardcode ke role `student`. Diperlukan registrasi dinamis memilih role **Orang Tua / Wali** vs **Murid / Santri** pada form pendaftaran.
2. **Pendaftaran Khusus Pengajar (`/bergabung`):**
   - Rute `/bergabung` khusus bagi calon Guru / Pendamping Al-Qur'an untuk mengajukan pendaftaran mengajar.
3. **Revisi Matriks Visibilitas Navbar (`navbar.blade.php`):**
   - **Menu "Informasi Pendampingan / Biaya" (`route('biaya')`):** HANYA boleh dilihat oleh **Admin** dan **Orang Tua** yang sudah login. Guest/Publik, Guru, dan Santri TIDAK BISA melihat menu ini.
   - **Menu "Menjadi Pendamping" (`route('bergabung')`):** HANYA boleh dilihat oleh **Guest (Belum Login / Publik)**. Setelah user login (Admin, Guru, Orang Tua, maupun Santri), menu ini TIDAK MUNCUL LAGI.

---

## 2. ANALISIS ALUR PENDAFTARAN (REGISTRATION FLOW ANALYSIS)

```
┌────────────────────────────────────────────────────────────────────────┐
│                   AL-HIKMAH MULTI-ROLE AUTH FLOW                       │
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │
│  1. REGISTRASI GURU / PENDAMPING                                       │
│     Rute: GET /bergabung ──► Render bergabung.blade.php               │
│     POST /bergabung ──► User (role: mentor) + Mentor Profile ──► /mentor/dashboard
│                                                                        │
│  2. REGISTRASI ORANG TUA (DEFAULT REGISTRATION)                        │
│     Rute: GET /register ──► Form Register (Role: Parent)               │
│     POST /register ──► User (role: parent) + ParentProfile ──► /parent/dashboard
│                                                                        │
│  3. REGISTRASI SANTRI / MURID                                          │
│     GET /register?role=student                                         │
│     POST /register ──► User (role: student) + Student ──► /student/dashboard
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. PERANCANGAN ALUR PENDAFTARAN PER ROLE

### A. Alur Pendaftaran Pendamping / Guru (`/bergabung`)
- **Akses Rute:** `GET /bergabung` (Nama Route: `bergabung`).
- **Template UI:** View `resources/views/bergabung.blade.php`.
- **Formulir Pendaftaran:**
  - Input: Nama Lengkap, Email, Password, Konfirmasi Password, No. WhatsApp, Spesialisasi Mengajar, Bio.
- **Logika Backend (`RegisterMentorController.php`):**
  - Membuat `User` (role `mentor`) + profil `Mentor`.
  - Redirect ke `route('mentor.dashboard')`.

### B. Alur Pendaftaran Orang Tua / Wali (`/register`)
- **Akses Rute:** `GET /register` dengan Segmented Radio Role Selection.
- **Logika Backend (`RegisteredUserController.php`):**
  - Jika `role` = `parent`: Membuat `User` (role `parent`) + `ParentProfile`, redirect ke `route('parent.dashboard')`.

### C. Alur Pendaftaran Murid / Santri (`/register?role=student`)
- **Logika Backend:**
  - Jika `role` = `student`: Membuat `User` (role `student`) + `Student` profile, redirect ke `route('student.dashboard')`.

---

## 4. PERANCANGAN HAK AKSES NAVIGASI NAVBAR (`navbar.blade.php`)

### 📊 Matriks Hak Akses Navbar Terbaru:

| Menu Item | 🌐 Guest (Belum Login) | 🛠️ Admin | 👨‍👩‍👦 Orang Tua (`parent`) | 👨‍🏫 Guru (`mentor`) | 👦 Santri (`student`) |
|---|---|---|---|---|---|
| 💳 **Informasi Pendampingan** (`route('biaya')`) | ❌ Sembunyi | ✅ **TAMPIL** | ✅ **TAMPIL** | ❌ Sembunyi | ❌ Sembunyi |
| 🤝 **Menjadi Pendamping** (`route('bergabung')`) | ✅ **TAMPIL** | ❌ Sembunyi | ❌ Sembunyi | ❌ Sembunyi | ❌ Sembunyi |

---

## 5. RINCIAN TUGAS & PANDUAN EKSEKUSI LANGKAH DEMI LANGKAH

### Tugas 3: Proteksi Menu Navigasi Biaya & Menjadi Pendamping pada Navbar (`navbar.blade.php`)

Junior Developer wajib memperbarui Blade View `resources/views/partials/navbar.blade.php` dengan kondisi sintaksis berikut:

#### 1. Menu "Informasi Pendampingan" (`route('biaya')`)
Hanya tampil jika pengguna **sudah login (`auth()->check()`)** DAN memiliki role **Orang Tua (`isParent()`)** ATAU **Admin (`isAdmin()`)**:

```blade
{{-- Hanya Tampil untuk Orang Tua (Parent) dan Admin yang Sudah Login --}}
@if (auth()->check() && (auth()->user()->isParent() || auth()->user()->isAdmin()))
    <li>
        <a class="dropdown-item" href="{{ route('biaya') }}">
            <i class="bi bi-info-circle"></i> Informasi Pendampingan
        </a>
    </li>
@endif
```

#### 2. Menu "Menjadi Pendamping" (`route('bergabung')`)
Hanya tampil untuk **Guest (Pengunjung Publik / Belum Login)**. Setelah pengguna login dengan akun apapun (Admin, Guru, Orang Tua, Santri), menu ini otomatis disembunyikan:

```blade
{{-- Hanya Tampil untuk Guest (Belum Login / Public) --}}
@guest
    <li>
        <a class="dropdown-item" href="{{ route('bergabung') }}">
            <i class="bi bi-person-plus"></i> Menjadi Pendamping
        </a>
    </li>
@endguest
```

---

### Tugas 4: Pembuatan Automated Test Suite (Pest PHP)

Perbarui file `tests/Feature/NavbarAccessTest.php` untuk menguji ke-10 skenario sesuai matriks visibilitas:

1. **Guest (Belum Login):**
   - `route('biaya')`: **DontSee** (Sembunyi)
   - `route('bergabung')`: **See** (Tampil)

2. **Admin:**
   - `route('biaya')`: **See** (Tampil)
   - `route('bergabung')`: **DontSee** (Sembunyi)

3. **Orang Tua (Parent):**
   - `route('biaya')`: **See** (Tampil)
   - `route('bergabung')`: **DontSee** (Sembunyi)

4. **Guru (Mentor):**
   - `route('biaya')`: **DontSee** (Sembunyi)
   - `route('bergabung')`: **DontSee** (Sembunyi)

5. **Santri (Student):**
   - `route('biaya')`: **DontSee** (Sembunyi)
   - `route('bergabung')`: **DontSee** (Sembunyi)

---

## 6. STANDARD OPERATING PROCEDURE (SOP) PENGUJIAN & VERIFIKASI KODE

Sebelum menandai tugas selesai, jalankan perintah verifikasi berikut:

```bash
vendor/bin/pint --format agent
php artisan test --compact
```

---
*Dokumen plan2.md v2.0 ini siap diimplementasikan.*
