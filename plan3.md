# 📋 DOKUMEN PLANNING REFAKTORISASI NAVBAR, DYNAMIC AUTH ACTIONS, & CLEANUP RUTE BLADE (`plan3.md`)

> **Target Pembaca:** Junior Developer / Development Team  
> **Versi:** 3.0 (Comprehensive Navbar & Route Refactoring)  
> **Tanggal Update:** 11 Agustus 2026  
> **Fokus Utama:** Penyempurnaan Tampilan Dinamis Auth Actions Navbar, Pembersihan Tautan Statis `.html`, dan Restrukturisasi Partial Layout

---

## 📌 DAFTAR ISI
1. [Ringkasan Masalah & Tujuan (`plan3.md`)](#1-ringkasan-masalah--tujuan-plan3md)
2. [Analisis Area Perbaikan Kode (Codebase Audit)](#2-analisis-area-perbaikan-kode-codebase-audit)
3. [Rincian Rencana Refaktorisasi per Komponen](#3-rincian-rencana-refaktorisasi-per-komponen)
   - [A. Refaktorisasi Dynamic Auth Actions Navbar (`navbar.blade.php`)](#a-refaktorisasi-dynamic-auth-actions-navbar-navbarbladephp)
   - [B. Migrasi Tautan Statis `.html` ke Named Routes Laravel](#b-migrasi-tautan-statis-html-ke-named-routes-laravel)
   - [C. Penyelarasan Icon Bootstrap & Kerapihan Layout Navbar](#c-penyelarasan-icon-bootstrap--kerapihan-layout-navbar)
4. [Langkah-Langkah Eksekusi untuk Junior Developer](#4-langkah-langkah-eksekusi-untuk-junior-developer)
   - [Tugas 1: Perbarui Tampilan Navbar Action (`navbar.blade.php`)](#tugas-1-perbarui-tampilan-navbar-action-navbarbladephp)
   - [Tugas 2: Bersihkan Tautan `.html` pada Navbar, Footer, & Home](#tugas-2-bersihkan-tautan-html-pada-navbar-footer--home)
   - [Tugas 3: Buat Automated Test Suite (`DynamicNavbarActionsTest.php`)](#tugas-3-buat-automated-test-suite-dynamicnavbaractionstestphp)
5. [Standard Operating Procedure (SOP) Verifikasi Kode](#5-standard-operating-procedure-sop-verifikasi-kode)

---

## 1. RINGKASAN MASALAH & TUJUAN (`plan3.md`)

### 🔴 Temuan & Masalah Kode Saat Ini:
1. **Tombol Actions Navbar (`navbar.blade.php:L104-L117`) Tidak Dinamis:**
   - Saat pengguna sudah **login (`@auth`)**, tombol `Mulai Perjalanan` (Modal Form Pendaftaran) tetap muncul di sebelah tombol `Dashboard`. Hal ini berlebihan dan membingungkan pengguna yang sudah menjadi anggota/santri/guru/admin.
   - Pengguna yang sudah login membutuhkan akses cepat ke **Profil User**, **Role Badge**, dan tombol **Keluar (Logout)** secara langsung dari navbar.
2. **Sisa Tautan Statis `.html` di Beberapa Template:**
   - Masih ditemukan tautan ke file `.html` (seperti `galeri.html`, `faq.html`, `index.html`, `tentang-kami.html`, `program.html`, `metode.html`, `tahfidz.html`, `biaya.html`, `bergabung.html`) pada file `navbar.blade.php`, `footer.blade.php`, dan `home.blade.php`.
   - Tautan ini harus diganti dengan fungsi pembantu `route(...)` resmi Laravel agar tidak terjadi *404 Not Found* saat diakses dari sub-halaman.
3. **Penyelarasan Icon & Visual Aesthetics:**
   - Ikon Bootstrap Icons perlu diseragamkan agar tampilan navbar terkesan lebih modern, rapi, dan konsisten.

---

## 2. ANALISIS AREA PERBAIKAN KODE (CODEBASE AUDIT)

```
┌────────────────────────────────────────────────────────────────────────┐
│               NAVBAR DYNAMIC AUTH ACTIONS & ROUTE REFACTOR             │
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │
│  [GUEST / PUBLIC]                                                      │
│  ├─► Tombol "Masuk" (route('login'))                                  │
│  └─► Tombol "Mulai Perjalanan" (Modal Pendaftaran / Konsultasi)         │
│                                                                        │
│  [AUTHENTICATED USER / @auth]                                         │
│  ├─► Tombol "Dashboard" (route('dashboard')) + Role Badge              │
│  └─► Dropdown User (Nama User, Dashboard Link, Form Logout)            │
│                                                                        │
│  [CLEANUP TAUTAN STATIS]                                              │
│  ├─► navbar.blade.php  : galeri.html -> route('home')#galeri          │
│  ├─► footer.blade.php  : ganti semua .html dengan route()             │
│  └─► home.blade.php    : ganti tentang-kami.html dll dengan route()     │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. RINCIAN RENCANA REFAKTORISASI PER KOMPONEN

### A. Refaktorisasi Dynamic Auth Actions Navbar (`navbar.blade.php`)

Junior Developer wajib mengganti blok `@auth ... @else ... @endauth` pada `resources/views/partials/navbar.blade.php` dengan struktur dinamis berikut:

```blade
<div class="navbar-actions d-flex align-items-center gap-2">
    @auth
        <!-- User Dropdown & Dashboard Button -->
        <div class="dropdown">
            <button class="btn btn-outline-custom dropdown-toggle d-flex align-items-center gap-2 py-2 px-3 rounded-pill" 
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-5 text-success"></i>
                <span class="fw-semibold text-dark small">{{ auth()->user()->name }}</span>
                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small ms-1">
                    {{ auth()->user()->role?->label ?? 'User' }}
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 mt-2">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                    <div class="small text-muted">{{ auth()->user()->email }}</div>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2 text-success"></i> Dashboard
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    @else
        <!-- Guest Actions -->
        <a href="{{ route('login') }}" class="btn btn-outline-custom rounded-pill px-3 py-2">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
        </a>
        <button type="button" class="btn btn-daftar rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#daftarModal">
            <i class="bi bi-pencil-square me-1"></i> Mulai Perjalanan
        </button>
    @endauth
</div>
```

---

### B. Migrasi Tautan Statis `.html` ke Named Routes Laravel

Junior Developer wajib mengganti seluruh rute statis pada file-file berikut:

#### 1. File `resources/views/partials/navbar.blade.php`:
- `href="galeri.html"` ──► `href="{{ route('home') }}#galeri"`
- `href="faq.html"` ──► `href="{{ route('home') }}#faq"`

#### 2. File `resources/views/partials/footer.blade.php`:
- `href="index.html"` ──► `href="{{ route('home') }}"`
- `href="tentang-kami.html"` ──► `href="{{ route('tentang-kami') }}"`
- `href="program.html"` ──► `href="{{ route('program') }}"`
- `href="metode.html"` ──► `href="{{ route('metode') }}"`
- `href="tahfidz.html"` ──► `href="{{ route('tahfidz') }}"`
- `href="biaya.html"` ──► `href="{{ route('biaya') }}"`
- `href="bergabung.html"` ──► `href="{{ route('bergabung') }}"`

#### 3. File `resources/views/home.blade.php`:
- `href="tentang-kami.html"` ──► `href="{{ route('tentang-kami') }}"`
- `href="metode.html"` ──► `href="{{ route('metode') }}"`
- `href="tahfidz.html"` ──► `href="{{ route('tahfidz') }}"`

---

## 4. LANGKAH-LANGKAH EKSEKUSI UNTUK JUNIOR DEVELOPER

1. **Edit file `resources/views/partials/navbar.blade.php`:**
   - Perbarui area `navbar-actions` dengan blok `@auth` (User Profile & Logout Form) dan `@guest` (Masuk & Mulai Perjalanan).
   - Ganti tautan `galeri.html` dan `faq.html` dengan jangkar rute `{{ route('home') }}#galeri` dan `{{ route('home') }}#faq`.

2. **Edit file `resources/views/partials/footer.blade.php`:**
   - Perbarui semua tautan footer agar menggunakan pembantu `route(...)`.

3. **Edit file `resources/views/home.blade.php`:**
   - Perbarui tautan CTA internal agar menggunakan pembantu `route(...)`.

4. **Buat file tes `tests/Feature/DynamicNavbarActionsTest.php`:**
   - Uji tampilan navbar untuk `@guest`: Pastikan tombol Masuk dan Mulai Perjalanan muncul.
   - Uji tampilan navbar untuk `@auth`: Pastikan tombol Dashboard, nama user, dan form Logout muncul, serta modal `Mulai Perjalanan` tidak dirender bagi pengguna yang sudah login.

---

## 5. STANDARD OPERATING PROCEDURE (SOP) VERIFIKASI KODE

Sebelum menyelesaikan pekerjaan, pastikan untuk selalu menjalankan perintah berikut:

```bash
vendor/bin/pint --format agent
php artisan test --compact
```

---
*Dokumen plan3.md ini telah rampung dan siap dieksekusi.*
