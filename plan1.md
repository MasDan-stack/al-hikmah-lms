# 📋 DOKUMEN PLANNING & ROADMAP IMPROVEMENT LANDING PAGE & DASHBOARD INTEGRATION (`plan1.md`)

> **Target Pembaca:** Junior Developer / Development Team  
> **Versi:** 1.0 (Finalized Plan)  
> **Tanggal Update:** 11 Agustus 2026  
> **Fokus Utama:** Sinkronisasi Halaman Depan (`program`, `biaya`, `metode`, `tahfidz`, `tentang-kami`) dengan Dashboard Master Data AL-HIKMAH LMS ([Issue #2](https://github.com/MasDan-stack/al-hikmah-lms/issues/2))

---

## 📌 DAFTAR ISI
1. [Ringkasan Eksekutif & Tujuan Proyek](#1-ringkasan-eksekutif--tujuan-proyek)
2. [Hasil Audit & Celah Teknis (Gap Analysis) 5 Halaman Depan](#2-hasil-audit--celah-teknis-gap-analysis-5-halaman-depan)
3. [Arsitektur & Matriks Integrasi Data (Database ↔ Landing Page)](#3-arsitektur--matriks-integrasi-data-database--landing-page)
4. [Rincian Tugas & Panduan Eksekusi Langkah demi Langkah](#4-rincian-tugas--panduan-eksekusi-langkah-demi-langkah)
   - [Tugas 1: Dinamisasi Program & Paket Biaya (`program.blade.php` & `biaya.blade.php`)](#tugas-1-dinamisasi-program--paket-biaya-programbladephp--biayabladephp)
   - [Tugas 2: Sinkronisasi Statistik Real-time Dashboard (`tentang-kami.blade.php`)](#tugas-2-sinkronisasi-statistik-real-time-dashboard-tentang-kamibladephp)
   - [Tugas 3: Integrasi Metode Belajar & Showcase Pendamping (`metode.blade.php`)](#tugas-3-integrasi-metode-belajar--showcase-pendamping-metodebladephp)
   - [Tugas 4: Form Pendaftaran Online & Modal Sync (`tahfidz.blade.php` & `#daftarModal`)](#tugas-4-form-pendaftaran-online--modal-sync-tahfidzbladephp--daftarmodal)
   - [Tugas 5: Standarisasi UI/UX, Aksesibilitas WCAG AA & Dark Mode](#tugas-5-standarisasi-uiux-aksesibilitas-wcag-aa--dark-mode)
5. [Standard Operating Procedure (SOP) Pengujian & Verifikasi Kode](#5-standard-operating-procedure-sop-pengujian--verifikasi-kode)

---

## 1. RINGKASAN EKSEKUTIF & TUJUAN PROYEK

Setelah menyelesaikan **Issue #2** ([Epic] Core Master Data Management: CRUD Program, Pendamping, dan Santri), sistem LMS telah memiliki database yang aktif dan terstruktur.

Namun, 5 file halaman depan (Landing Page) saat ini masih menggunakan **data statis (hardcoded)**:
1. `resources/views/program.blade.php` (Daftar program masih teks manual).
2. `resources/views/biaya.blade.php` (Harga paket masih teks manual, tidak membaca harga dari DB).
3. `resources/views/metode.blade.php` (Pilihan metode belum terhubung ke opsi sesi).
4. `resources/views/tahfidz.blade.php` (Tombol pendaftaran belum memicu pendaftaran program spesifik).
5. `resources/views/tentang-kami.blade.php` (Statistik santri dan pengajar belum real-time).

### 🎯 Tujuan `plan1.md`:
Memberikan panduan teknis yang presisi dan bertahap bagi **Junior Developer** untuk mengubah halaman depan menjadi **dinamis**, terintegrasi penuh dengan **Dashboard Master Data**, serta memenuhi standar performa & desain AL-HIKMAH LMS.

---

## 2. HASIL AUDIT & CELAH TEKNIS (GAP ANALYSIS) 5 HALAMAN DEPAN

| File View | Kondisi Saat Ini (Technical Debt) | Target Improvement / Sinkronisasi Dashboard | Prioritas |
|---|---|---|---|
| 📄 `program.blade.php` | Program Anak, Dewasa, dan Bahasa Arab di-hardcode di Blade HTML. | Menampilkan data dari tabel `programs` (Managed via `ProgramManager` Livewire). Memiliki fallback jika DB kosong. | **P1 (High)** |
| 📄 `biaya.blade.php` | Harga paket Rp 400rb, Rp 800rb, Rp 1,2jt di-hardcode. | Mengambil harga `price` dan durasi `duration_weeks` langsung dari tabel `programs`. | **P1 (High)** |
| 📄 `metode.blade.php` | Pilihan kelas (Online, Offline Home Visit, Hybrid) statis. | Menyambungkan CTA metode ke form pendaftaran dengan preset metode belajar (`online`/`offline`/`hybrid`). | **P2 (Medium)** |
| 📄 `tahfidz.blade.php` | Gambar menggunakan fallback placehold.co dan tombol CTA belum membawa parameter ID program. | Menggunakan asset gambar lokal, membawa `program_id` (Tahfidz) ke modal pendaftaran. | **P2 (Medium)** |
| 📄 `tentang-kami.blade.php` | Filosofi dan nilai sudah bagus, namun belum ada ringkasan counter real-time (Total Santri, Guru, Program). | Menambahkan widget counter real-time dari data `Student::count()`, `Mentor::where('is_active', true)->count()`, dll. | **P2 (Medium)** |

---

## 3. ARSITEKTUR & MATRIKS INTEGRASI DATA (DATABASE ↔ LANDING PAGE)

```
┌────────────────────────────────────────────────────────────────────────┐
│                        DATA FLOW INTEGRATION                           │
├────────────────────────────────────────────────────────────────────────┤
│  [DATABASE MASTER DATA]                                                │
│  - Program (id, name, level, duration_weeks, price)                    │
│  - Mentor (id, full_name, specialization, rating, is_active)          │
│  - Student (id, full_name, age, gender, location)                      │
│                                                                        │
│                                 ▼                                      │
│  [CONTROLLER / ROUTES SERVICES]                                        │
│  - Web Routes (web.php) ──► Query Eloquent Models                      │
│  - Livewire Components (RegistrationForm / ProgramManager)             │
│                                                                        │
│                                 ▼                                      │
│  [PUBLIC LANDING PAGES]                                                │
│  - program.blade.php    ◄── Render Data Program Real-time              │
│  - biaya.blade.php      ◄── Render Harga & Paket dari DB               │
│  - tentang-kami.blade.php ◄── Render Real Stats (Santri & Guru)        │
│  - tahfidz.blade.php    ◄── Pendaftaran Direct ke Program ID Tahfidz    │
│  - metode.blade.php     ◄── Select Filter Metode Belajar (Online/Offline)│
└────────────────────────────────────────────────────────────────────────┘
```

---

## 4. RINCIAN TUGAS & PANDUAN EKSEKUSI LANGKAH DEMI LANGKAH

### Tugas 1: Dinamisasi Program & Paket Biaya (`program.blade.php` & `biaya.blade.php`)

#### 🔴 Penyebab Masalah:
Saat Admin menambah atau mengedit harga program melalui menu `/admin/programs` di Dashboard, perubahan tersebut tidak tercermin di halaman depan (`/program` dan `/biaya`).

#### 🟢 Langkah Penyelesaian untuk Junior Dev:

1. **Perbarui Closure Route di `routes/web.php`:**
   ```php
   use App\Models\Program;

   Route::get('/program', function () {
       $programs = Program::all();
       return view('program', compact('programs'));
   })->name('program');

   Route::get('/biaya', function () {
       $programs = Program::orderBy('price', 'asc')->get();
       return view('biaya', compact('programs'));
   })->name('biaya');
   ```

2. **Perbarui `resources/views/program.blade.php`:**
   - Gunakan perulangan `@forelse($programs as $program)` untuk merender kartu program secara dinamis.
   - Sediakan blok `@empty` sebagai fallback tampilan jika database belum diisi seeder.

3. **Perbarui `resources/views/biaya.blade.php`:**
   - Tampilkan harga format Rupiah `Rp {{ number_format($program->price, 0, ',', '.') }}` langsung dari model `Program`.
   - Hubungkan tombol "Konsultasikan" ke WhatsApp Gateway dengan pesan otomatis berisi nama program terkait:
     `https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20tertarik%20dengan%20program%20{{ urlencode($program->name) }}`

---

### Tugas 2: Sinkronisasi Statistik Real-time Dashboard (`tentang-kami.blade.php`)

#### 🔴 Penyebab Masalah:
Halaman "Tentang Kami" memuat nilai-nilai Al-Hikmah tetapi belum memiliki elemen bukti sosial (*social proof*) berupa angka real-time jumlah santri yang telah didampingi dan pengajar yang aktif.

#### 🟢 Langkah Penyelesaian untuk Junior Dev:

1. **Perbarui Closure Route `tentang-kami` di `routes/web.php`:**
   ```php
   use App\Models\Student;
   use App\Models\Mentor;
   use App\Models\Program;

   Route::get('/tentang-kami', function () {
       $totalStudents = Student::count();
       $totalMentors = Mentor::where('is_active', true)->count();
       $totalPrograms = Program::count();

       return view('tentang-kami', compact('totalStudents', 'totalMentors', 'totalPrograms'));
   })->name('tentang-kami');
   ```

2. **Tambahkan Section Counter di `resources/views/tentang-kami.blade.php`:**
   ```blade
   <section class="section-padding bg-success text-white text-center">
       <div class="container">
           <div class="row g-4">
               <div class="col-md-4">
                   <h2 class="display-4 fw-bold mb-1">{{ $totalStudents > 0 ? $totalStudents : '100+' }}</h2>
                   <p class="mb-0 text-white-50">Santri Terdaftar</p>
               </div>
               <div class="col-md-4">
                   <h2 class="display-4 fw-bold mb-1">{{ $totalMentors > 0 ? $totalMentors : '15+' }}</h2>
                   <p class="mb-0 text-white-50">Pendamping Berpengalaman</p>
               </div>
               <div class="col-md-4">
                   <h2 class="display-4 fw-bold mb-1">{{ $totalPrograms > 0 ? $totalPrograms : '6' }}</h2>
                   <p class="mb-0 text-white-50">Program Pilihan</p>
               </div>
           </div>
       </div>
   </section>
   ```

---

### Tugas 3: Integrasi Metode Belajar & Showcase Pendamping (`metode.blade.php`)

#### 🔴 Penyebab Masalah:
Tampilan metode belajar (Online, Offline/Home Visit, Hybrid) belum terhubung ke tombol pendaftaran yang dapat memilih metode belajar secara otomatis.

#### 🟢 Langkah Penyelesaian untuk Junior Dev:

1. **Perbarui Tombol Pendaftaran pada Kartu Metode di `resources/views/metode.blade.php`:**
   Ubah link tombol pada kartu Online, Offline, dan Hybrid agar membawa query parameter `?method=online`, `?method=offline`, atau `?method=hybrid`.
   ```blade
   <a href="{{ route('home') }}?method=online#daftarModal" class="btn btn-outline-custom w-100">
       Pilih Metode Online
   </a>
   ```
2. **Tambahkan daftar area layanan Jabodetabek yang aktif** agar calon wali santri mendapatkan informasi lokasi yang tepat.

---

### Tugas 4: Form Pendaftaran Online & Modal Sync (`tahfidz.blade.php` & `#daftarModal`)

#### 🔴 Penyebab Masalah:
Pada file `tahfidz.blade.php` baris 24:
`onerror="this.src='https://placehold.co/600x500/0d7a3e/white?text=Tahfidz'"`
Menggunakan URL gambar eksternal yang melanggar standar kemandirian aset lokal. Selain itu, tombol "Daftar Program Tahfidz" belum memilih secara otomatis pilihan program Tahfidz di modal.

#### 🟢 Langkah Penyelesaian untuk Junior Dev:

1. **Ganti Sumber Gambar Eksternal di `resources/views/tahfidz.blade.php`:**
   Ganti `https://placehold.co/600x500...` dengan SVG lokal atau gambar aset internal `asset('assets/img/62.jpg')`.
2. **Sinkronkan Modal Pendaftaran (`#daftarModal`):**
   Pastikan dropdown program pada modal pendaftaran membaca daftar `$programs` dari database dan terpilih otomatis (*selected*) ketika diakses dari halaman Tahfidz.

---

### Tugas 5: Standarisasi UI/UX, Aksesibilitas WCAG AA & Dark Mode

#### 🔴 Penyebab Masalah:
Beberapa elemen halaman landing memerlukan jaminan rasio kontras warna di Mode Gelap (Dark Mode) agar ramah mata dan memenuhi standar **WCAG 2.1 AA**.

#### 🟢 Langkah Penyelesaian untuk Junior Dev:
1. Pastikan setiap gambar memuat atribut `alt` yang deskriptif (bukan `alt="image"` atau kosong).
2. Pastikan tombol dengan ikon saja memuat atribut `aria-label`.
3. Jalankan pengujian visual dark mode pada layar mobile (HP) dan desktop.

---

## 5. STANDARD OPERATING PROCEDURE (SOP) PENGUJIAN & VERIFIKASI KODE

Sebelum mengajukan Pull Request / Commit untuk tugas di atas, Junior Developer WAJIB mengikui langkah pengujian berikut:

### 1. Jalankan Formatter Pint
```bash
vendor/bin/pint --format agent
```

### 2. Jalankan Automated Test Suite
```bash
php artisan test --compact
```

### 3. Cek List Rute Aplikasi
```bash
php artisan route:list --except-vendor
```

---
*Dokumen plan1.md ini siap digunakan oleh Junior Developer sebagai panduan eksekusi pengembangan selanjutnya.*
