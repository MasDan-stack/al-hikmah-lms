# Plan 4: Audit Kode, Perbaikan Navigasi Dashboard, & Penyempurnaan Fitur LMS

Dokumen perencanaan ini disusun secara mendetail untuk **Junior Developer** agar dapat memahami seluruh bagian kode yang perlu diperbaiki, dirapikan, dan ditingkatkan kualitasnya pada proyek **AL-HIKMAH LMS**.

---

## 🎯 Tujuan Utama
1. **Memperbaiki Tombol & Link Terputus (`href="#"`)** pada Panel Admin (`admin/dashboard.blade.php` dan `layouts/admin.blade.php`).
2. **Menyempurnakan Fitur Cetak Laporan Capaian** (`ReportController`) agar dapat diakses dari Dashboard Admin, Orang Tua, dan Santri.
3. **Meningkatkan Pengalaman Pengguna (UX) Dashboard Role** (`Mentor`, `Parent`, `Student`) pada `resources/views/dashboard.blade.php` agar tampil dinamis sesuai role pengguna.
4. **Menjamin 100% Kestabilan Sistem** melalui pengujian otomatis Pest PHP dan format standar Laravel Pint.

---

## 📋 Daftar Pekerjaan & Modifikasi File

### 1. Dashboard Admin & Sidebar Layout
#### `resources/views/admin/dashboard.blade.php`
- **Masalah**:
  - Tombol *"Tambah Santri Baru"* menggunakan `data-bs-target="#daftarModal"`, namun `#daftarModal` tidak ada di layout admin (`layouts/admin.blade.php`).
  - Tombol *"Cetak Laporan Bulanan"* dan *"Buat Jadwal Sesi Belajar"* masih memiliki `href="#"`.
- **Perbaikan**:
  - Ubah tautan *"Tambah Santri Baru"* menjadi `href="{{ route('admin.students.index') }}"`.
  - Ubah tautan *"Cetak Laporan Bulanan"* menjadi `href="{{ route('report.download') }}" target="_blank"`.
  - Ubah tautan *"Buat Jadwal Sesi Belajar"* menjadi `href="{{ route('admin.students.index') }}"`.

#### `resources/views/layouts/admin.blade.php`
- **Masalah**: Menu *"Jadwal Sesi"* pada sidebar memiliki `href="#"`.
- **Perbaikan**: Arahkan menu *"Jadwal Sesi"* ke `route('admin.students.index')`.

---

### 2. Dashboard Role Dinamis (Mentor, Orang Tua, & Santri)
#### `resources/views/dashboard.blade.php`
- **Masalah**: Tampilan dashboard role non-admin saat ini masih berupa statistik generik dan belum memiliki akses langsung ke laporan cetak progres.
- **Perbaikan**:
  - Tambahkan tombol **"Cetak Laporan Perkembangan (PDF)"** yang mengarah ke `route('report.download')` dengan atribut `target="_blank"`.
  - Sesuaikan informasi sambutan dan kartu status berdasarkan role (`Mentor`, `Orang Tua / Wali`, atau `Santri`).

---

### 3. Pengujian Otomatis & Pemformatan Kode
#### `tests/Feature/AdminDashboardActionsTest.php`
- Buat file tes baru untuk memastikan:
  - Route `admin.dashboard`, `admin.students.index`, `admin.mentors.index`, `admin.programs.index` dapat diakses oleh Admin (HTTP 200).
  - Route `report.download` dapat diakses oleh pengguna terotentikasi dan merender view PDF.
  - Redirect role dashboard (`/dashboard`) bekerja secara tepat untuk `admin`, `mentor`, `parent`, dan `student`.

---

## 🧪 Langkah Verifikasi oleh Junior Developer
1. Jalankan pemformat kode PHP:
   ```bash
   vendor/bin/pint --format agent
   ```
2. Jalankan seluruh unit & feature test:
   ```bash
   php artisan test --compact
   ```
3. Uji coba manual di browser:
   - Login sebagai Admin -> Klik *"Cetak Laporan Bulanan"* -> Pastikan halaman laporan PDF terbuka di tab baru.
   - Login sebagai Orang Tua/Santri -> Klik *"Cetak Laporan Perkembangan"* -> Pastikan file/halaman cetak terbuka.
