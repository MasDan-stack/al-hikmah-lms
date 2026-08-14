# 📋 PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Kontrol Visibilitas Akses Role-Based & Proteksi Informasi Pendampingan (Biaya)

> **Dokumen Versi:** 1.0  
> **Status:** Ready for Junior Implementation  
> **Author:** Senior Programmer & Architect  
> **Target Implementer:** Junior Programmer  
> **Lokasi File:** `c:\xampp\htdocs\al-hikmah-lms\auth-visibility.md`  

---

## 1. EXECUTIVE SUMMARY

- **Problem Statement:**  
  Elemen navigasi dan tombol call-to-action (CTA) menuju halaman biaya/informasi pendampingan (`/biaya`) sebelumnya masih terlihat oleh pengunjung publik (guest), santri, dan mentor di beberapa halaman landing page (`metode.blade.php`, `tahfidz.blade.php`, dll). Hal ini membingungkan pengguna umum karena ketika diakses mereka akan menemui halaman error 403, serta tidak memberikan diferensiasi visual saat administrator sedang login.

- **Proposed Solution:**  
  Menerapkan mekanisme **Granular Role-Based UI Rendering** di seluruh landing page dan komponen bersama (Navbar, Footer, Metode, Tahfidz, Program). Elemen informasi biaya hanya akan dirender untuk **Orang Tua (Parent)** secara penuh, diberikan label khusus `(Kamu Administrator)` untuk **Administrator**, dan secara konsisten **disembunyikan (hidden)** untuk **Guest**, **Mentor**, dan **Santri (Student)**.

- **Success Criteria:**  
  1. **Zero Unauthorized Exposure:** 100% tombol dan tautan `/biaya` tidak dirender sama sekali pada sesi Guest, Mentor, dan Santri.
  2. **Clear Administrator Context:** Administrator melihat tombol dengan label penjelas `(Kamu Administrator)` dan icon representatif.
  3. **Full Parent Accessibility:** Orang Tua yang login dapat melihat seluruh CTA informasi pendampingan/biaya dengan styling default yang elegan.
  4. **100% Test Automation Green:** Seluruh unit & feature tests terkait otorisasi dan render landing page lulus tanpa regresi.

---

## 2. USER EXPERIENCE & FUNCTIONALITY

### 👥 User Personas
1. **Pengunjung Publik / Guest:** Pengunjung umum yang mencari info kurikulum & profil AL-HIKMAH tanpa melihat rincian komersial sebelum mendaftar/berkonsultasi.
2. **Orang Tua / Wali Murid (Parent):** Pengguna terdaftar yang memiliki hak penuh melihat rincian investasi pendidikan dan memilih paket belajar santri.
3. **Administrator:** Pengelola lembaga yang perlu menginspeksi rincian biaya dengan identifikasi status admin yang jelas di antarmuka.
4. **Pendamping / Guru (Mentor):** Pengajar yang fokus pada materi belajar, jadwal, dan progres santri tanpa dibebani elemen biaya di landing page.
5. **Santri / Murid (Student):** Anak/remaja yang fokus pada materi pembelajaran Al-Qur'an.

### 📖 User Stories & Acceptance Criteria

#### Story 1: Akses Orang Tua (Parent)
- **User Story:** *Sebagai Orang Tua yang telah login, saya ingin melihat tombol informasi pendampingan & biaya di halaman Metode, Tahfidz, Program, Navbar, dan Footer agar saya dapat mengevaluasi paket belajar anak saya.*
- **Acceptance Criteria:**
  - `metode.blade.php` menampilkan tombol `<a href="{{ route('biaya') }}" class="btn btn-primary-custom"><i class="bi bi-info-circle me-1"></i> Informasi Pendampingan</a>`.
  - `tahfidz.blade.php` menampilkan tombol `<a href="{{ route('biaya') }}" class="btn btn-outline-custom ms-2"><i class="bi bi-info-circle me-1"></i> Informasi Pendampingan</a>`.
  - `program.blade.php` menampilkan banner rincian biaya dengan tombol default.
  - `navbar.blade.php` & `footer.blade.php` menampilkan menu/link Biaya secara aktif.

#### Story 2: Akses Administrator
- **User Story:** *Sebagai Admin yang login, saya ingin tombol informasi pendampingan memberikan informasi bahwa saya mengaksesnya sebagai administrator agar konteks peran saya jelas.*
- **Acceptance Criteria:**
  - Tombol pada `metode.blade.php` bertuliskan: `<i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)`.
  - Tombol pada `tahfidz.blade.php` bertuliskan: `<i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)`.
  - Tombol banner pada `program.blade.php` bertuliskan: `<i class="bi bi-tag-fill me-2"></i> Lihat Informasi Biaya & Paket (Kamu Administrator)`.
  - `navbar.blade.php` menampilkan badge: `<span class="badge bg-warning text-dark ms-1">Admin</span>`.

#### Story 3: Akses Guest, Mentor, dan Santri
- **User Story:** *Sebagai Guest, Mentor, atau Santri, saya tidak ingin melihat tombol biaya yang tidak berhak saya akses agar tampilan bersih dan bebas dari error 403.*
- **Acceptance Criteria:**
  - Tidak ada elemen HTML atau link menuju `route('biaya')` yang ter-render pada DOM.
  - Tidak memengaruhi tombol aksi lainnya (seperti tombol "Lihat Program", "Konsultasi WA", atau "Daftar Program Tahfidz").

### 🚫 Non-Goals
- Tidak mengubah logika backend pembayaran atau nominal harga di database.
- Tidak menghalangi akses publik ke katalog materi umum di `/program`, `/metode`, dan `/tahfidz`.

---

## 3. TECHNICAL SPECIFICATIONS

### 🏗️ Architecture & Directives Mapping
Gunakan Blade directive bawaan Laravel yang memanfaatkan helper method pada model `User`:
- `auth()->check()`: Memeriksa apakah user sudah login.
- `auth()->user()->isParent()`: Memeriksa apakah role saat ini adalah `parent`.
- `auth()->user()->isAdmin()`: Memeriksa apakah role saat ini adalah `admin`.

---

## 4. TAHAPAN IMPLEMENTASI UNTUK JUNIOR PROGRAMMER

### 📂 File 1: `resources/views/metode.blade.php`
**Lokasi:** Baris 65–69  
**Instruksi:** Ganti blok tombol CTA bagian bawah menjadi:
```blade
<div class="text-center mt-4" data-reveal>
    <a href="{{ route('program') }}" class="btn btn-outline-custom me-2"><i class="bi bi-journal-bookmark me-1"></i> Lihat Program</a>
    @auth
        @if (auth()->user()->isParent())
            <a href="{{ route('biaya') }}" class="btn btn-primary-custom"><i class="bi bi-info-circle me-1"></i> Informasi Pendampingan</a>
        @elseif (auth()->user()->isAdmin())
            <a href="{{ route('biaya') }}" class="btn btn-primary-custom"><i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)</a>
        @endif
    @endauth
</div>
```

---

### 📂 File 2: `resources/views/tahfidz.blade.php`
**Lokasi:** Baris 67–70  
**Instruksi:** Ganti baris tombol Informasi Pendampingan menjadi:
```blade
@auth
    @if (auth()->user()->isParent())
        <a href="{{ route('biaya') }}" class="btn btn-outline-custom ms-2"><i class="bi bi-info-circle me-1"></i> Informasi Pendampingan</a>
    @elseif (auth()->user()->isAdmin())
        <a href="{{ route('biaya') }}" class="btn btn-outline-custom ms-2"><i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)</a>
    @endif
@endauth
```

---

### 📂 File 3: `resources/views/program.blade.php`
**Lokasi:** Bagian Banner Biaya (Baris 72–85)  
**Instruksi:** Sesuaikan tombol di dalam banner agar mencantumkan keterangan administrator:
```blade
{{-- Hanya Tampil untuk Orang Tua (Parent) dan Admin yang Sudah Login --}}
@auth
    @if (auth()->user()->isParent() || auth()->user()->isAdmin())
        <!-- Banner Arahkan ke Biaya -->
        <section class="py-5 bg-white border-top">
            <div class="container text-center">
                <h4 class="fw-bold mb-2">Ingin Mengetahui Rincian Investasi & Jadwal Belajar?</h4>
                <p class="text-muted mb-4">Lihat informasi biaya transparan untuk setiap program yang Anda minati.</p>
                @if (auth()->user()->isParent())
                    <a href="{{ route('biaya') }}" class="btn btn-primary-custom px-4 py-2 rounded-pill">
                        <i class="bi bi-tag-fill me-2"></i> Lihat Informasi Biaya & Paket
                    </a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('biaya') }}" class="btn btn-primary-custom px-4 py-2 rounded-pill">
                        <i class="bi bi-tag-fill me-2"></i> Lihat Informasi Biaya & Paket (Kamu Administrator)
                    </a>
                @endif
            </div>
        </section>
    @endif
@endauth
```

---

### 📂 File 4: `resources/views/partials/navbar.blade.php`
**Lokasi:** Dropdown Menu Program & Biaya (Baris 77–85)  
**Instruksi:** Tambahkan identifikasi badge Admin pada navbar:
```blade
{{-- Hanya Tampil untuk Orang Tua (Parent) dan Admin yang Sudah Login --}}
@auth
    @if (auth()->user()->isParent())
        <li>
            <a class="dropdown-item" href="{{ route('biaya') }}">
                <i class="bi bi-info-circle"></i> Informasi Pendampingan
            </a>
        </li>
    @elseif (auth()->user()->isAdmin())
        <li>
            <a class="dropdown-item" href="{{ route('biaya') }}">
                <i class="bi bi-info-circle"></i> Informasi Pendampingan <span class="badge bg-warning text-dark ms-1">Admin</span>
            </a>
        </li>
    @endif
@endauth
```

---

### 📂 File 5: `resources/views/partials/footer.blade.php`
**Lokasi:** Bagian Tautan Navigasi (Baris 30–33)  
**Instruksi:** Pastikan kondisi role diterapkan rapi:
```blade
@auth
    @if (auth()->user()->isParent())
        <li><a href="{{ route('biaya') }}">Biaya</a></li>
    @elseif (auth()->user()->isAdmin())
        <li><a href="{{ route('biaya') }}">Biaya (Admin)</a></li>
    @endif
@endauth
```

---

## 5. PANDUAN PENGUJIAN OTOMATIS (TESTING FOR JUNIOR)

Setelah mengedit view di atas, Junior Programmer wajib menambahkan/menjalankan pengujian fitur di `tests/Feature/LandingPagesRoleVisibilityTest.php`:

```bash
php artisan test --filter=LandingPagesRoleVisibilityTest --compact
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

### 🎯 Checklist Pengujian:
1. [ ] **Guest Test:** Membuka `/metode`, `/tahfidz`, `/program` -> `assertDontSee('Informasi Pendampingan')` dan `assertDontSee('Kamu Administrator')`.
2. [ ] **Parent Test:** Login sebagai Parent -> `assertSee('Informasi Pendampingan')` dan `assertDontSee('Kamu Administrator')`.
3. [ ] **Admin Test:** Login sebagai Admin -> `assertSee('Informasi Pendampingan (Kamu Administrator)')` pada halaman `/metode` & `/tahfidz`.
4. [ ] **Mentor/Student Test:** Login sebagai Mentor atau Santri -> `assertDontSee('Informasi Pendampingan')`.
5. [ ] **Pint Style Check:** `vendor/bin/pint --dirty --format agent` berstatus *passed*.

---

## 6. RISKS & MITIGATION

| Potensi Risiko | Tingkat | Mitigasi Senior Tech |
|---|---|---|
| Blade cache lama masih menampilkan tampilan sebelum diedit | Rendah | Jalankan `php artisan view:clear` setiap kali mengubah file `.blade.php`. |
| Error `Call to a member function isParent() on null` saat belum login | Tinggi | Selalu bungkus pemeriksaan role di dalam `@auth ... @endauth` atau gunakan `auth()->check()`. |
| Link footer memicu error 403 saat diklik guest | Sedang | Lindungi link footer dengan `@auth` yang sama persis. |
