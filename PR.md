# 🚀 PULL REQUEST (PR): Refactoring UI Template, Dinamisasi WhatsApp, CMS Settings & Update Seeders

> **Target Branch:** `main` / `master`  
> **Source Branch:** `feature/ui-revisi-and-cms-settings`  
> **Related Issues:** Fixes #1, Fixes #2 (https://github.com/MasDan-stack/al-hikmah-lms/issues/2)  
> **Status Test Suite:** ✅ **30 Passed (0 Failed, 147 Assertions)**  

---

## 📌 Ringkasan Perubahan (Summary of Changes)

Pull Request ini mencakup perbaikan antarmuka (UI/UX) sesuai revisi master template HTML, implementasi dinamisasi sistem kontak/WhatsApp terpusat, perencanaan & modul CMS Settings Admin Panel, serta penyelarasan seluruh database seeders untuk aplikasi **AL-HIKMAH LMS**.

---

## 🔑 Fitur & Perbaikan Utama

### 1. 🎨 Refactoring Tampilan Halaman Program & Biaya (Presisi Master Template)
- **Halaman Program (`resources/views/program.blade.php`)**:
  - Merefakor tampilan agar 100% presisi mengikuti master `template/program.html` dengan 3 Section Utama:
    1. **Program Anak (10–15 th) — Utama** (*Iqra & Dasar Al-Qur'an*, *Tahsin Dasar*, *Adab & Doa Harian*, *Tahfidz Al-Qur'an*).
    2. **Program Tambahan (Dewasa & Muslimah)** (*Belajar dari Nol*, *Tahsin Dewasa*, *Kelas Muslimah*, *Tahfidz Dewasa*).
    3. **Program Bahasa Arab** (*Bahasa Arab Dasar*, *Nahwu & Sharaf* dengan aksen emas `.arabic-featured`).
  - Menambahkan dukungan *Dual-Rendering*: tetap menampilkan section template HTML publik sekaligus merender data dinamis dari database (`$programs`) jika tersedia di LMS.
- **Halaman Biaya (`resources/views/biaya.blade.php`)**:
  - Merestrukturisasi tampilan agar 100% presisi mengikuti `template/biaya.html`:
    1. **Biaya Pendaftaran**: Banner Rp 150.000 (*Sekali Bayar*).
    2. **Paket Belajar Bulanan**: Kartu *Basic* (4x/bulan), *Standard* (8x/bulan) dengan ribbon **`⭐ Banyak Dipilih`** (`.paket-popular-ribbon`), dan *Premium* (12x/bulan).

---

### 2. 📱 Dinamisasi WhatsApp & Kontak Website (`wa_url()` & JS Config)
- **Konfigurasi Kontak Terpusat**:
  - Membuat file konfigurasi `config/settings.php` dan variabel `.env` (`WHATSAPP_NUMBER=6285786689008`).
- **Global Helper `wa_url($message)`**:
  - Membuat `app/Helpers/settings.php` dan mendaftarkannya pada `composer.json` serta `AppServiceProvider` agar termuat secara otomatis di seluruh environment (Web, CLI, PHPUnit, Pest).
- **Penggantian Hardcoded Link WA**:
  - Mengubah link WhatsApp di `resources/views/tahfidz.blade.php` (baris 46–48 & 64), `layouts/landing.blade.php` (floating WA), `partials/footer.blade.php`, `home.blade.php`, `program.blade.php`, `biaya.blade.php`, dan `metode.blade.php`.
- **Integrasi Modal Registration JavaScript (`public/assets/js/scripts.js`)**:
  - Menambahkan `window.ALHIKMAH_CONFIG.whatsappNumber` di `landing.blade.php` sehingga pengiriman formulir pendaftaran via modal dialog di JS secara dinamis menggunakan nomor WA dari `.env`/database.

---

### 3. 🎛️ Modul Pengaturan Website (CMS Setting) & GitHub Issue Guide (`issue-modul.md`)
- **Panduan GitHub Issue (`issue-modul.md`)**:
  - Dibuatkan dokumen perencanaan lengkap berformat GitHub Issue untuk rujukan pengerjaan developer.
- **Arsitektur CMS Setting**:
  - Migration & Model `Setting` (`key`, `value`, `label`, `group`).
  - Controller `Admin\SettingController` dengan route `/admin/settings` (GET & POST) yang dilindungi role Admin.
  - View `resources/views/admin/settings/index.blade.php` untuk mengedit nomor WhatsApp, email, instagram, alamat, dan biaya pendaftaran langsung dari Admin Panel.

---

### 4. 🔄 Pembaharuan & Sinkronisasi Seluruh Database Seeders (`database/seeders/*`)
- **`SettingSeeder.php`**: Menyediakan data awal pengaturan kontak dan informasi lembaga.
- **`ProgramSeeder.php`**: Memperbarui 10 modul program agar presisi dengan template UI AL-HIKMAH (*Iqra*, *Tahsin*, *Adab*, *Tahfidz*, *Muslimah*, *Bahasa Arab*, dll.).
- **`DatabaseSeeder.php`**: Memastikan urutan penyemaian data berjalan lancar tanpa error foreign key constraint.

---

## 🧪 Hasil Verifikasi & Quality Assurance

1. **Automated Unit & Feature Testing (Pest PHP)**:
   ```bash
   php artisan test --compact
   ```
   **Output:**
   ```text
   Tests:    30 passed (147 assertions)
   Duration: 4.57s
   ```
   *Seluruh 30 skenario pengujian lulus 100%.*

2. **Laravel Pint Code Formatter**:
   ```bash
   vendor/bin/pint --dirty --format agent
   ```
   **Output:**
   ```json
   {"tool":"pint","result":"passed"}
   ```

3. **Cache Clearing**:
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```
   *Konfigurasi dan view cache berhasil dibersihkan.*

---

## 📁 Daftar File yang Diubah / Ditambahkan (File Changes)

| Tipe Modifikasi | Path File | Keterangan Ringkas |
|---|---|---|
| **[NEW]** | [config/settings.php](file:///c:/xampp/htdocs/al-hikmah-lms/config/settings.php) | Configuration file untuk kontak terpusat |
| **[NEW]** | [app/Helpers/settings.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Helpers/settings.php) | Helper `wa_url()` dan `site_setting()` |
| **[NEW]** | [issue-revisi.md](file:///c:/xampp/htdocs/al-hikmah-lms/issue-revisi.md) | Panduan perencanaan revisi UI & WA |
| **[NEW]** | [issue-modul.md](file:///c:/xampp/htdocs/al-hikmah-lms/issue-modul.md) | GitHub Issue guide CMS Setting & Seeders |
| **[NEW]** | [PR.md](file:///c:/xampp/htdocs/al-hikmah-lms/PR.md) | Dokumen Pull Request resmi |
| **[MODIFY]** | [.env](file:///c:/xampp/htdocs/al-hikmah-lms/.env) | Menambahkan `WHATSAPP_NUMBER=6285786689008` |
| **[MODIFY]** | [composer.json](file:///c:/xampp/htdocs/al-hikmah-lms/composer.json) | Autoload file `app/Helpers/settings.php` |
| **[MODIFY]** | [app/Providers/AppServiceProvider.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Providers/AppServiceProvider.php) | Auto-require helper settings untuk PHPUnit/Pest |
| **[MODIFY]** | [resources/views/program.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/program.blade.php) | Refactor 3 section program sesuai template |
| **[MODIFY]** | [resources/views/biaya.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/biaya.blade.php) | Refactor pendaftaran & paket bulanan sesuai template |
| **[MODIFY]** | [resources/views/tahfidz.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/tahfidz.blade.php) | Integrasi `wa_url()` pada button pendaftaran & CTA |
| **[MODIFY]** | [resources/views/layouts/landing.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/layouts/landing.blade.php) | Dinamisasi floating WA & window.ALHIKMAH_CONFIG |
| **[MODIFY]** | [resources/views/partials/footer.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/partials/footer.blade.php) | Dinamisasi link kontak & WA footer |
| **[MODIFY]** | [resources/views/metode.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/metode.blade.php) | Dinamisasi link WA pada kartu metode belajar |
| **[MODIFY]** | [resources/views/home.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/home.blade.php) | Dinamisasi link WA pada CTA Berbincang dengan Kami |
| **[MODIFY]** | [public/assets/js/scripts.js](file:///c:/xampp/htdocs/al-hikmah-lms/public/assets/js/scripts.js) | Dynamic WA number dari ALHIKMAH_CONFIG |

---

## 🎯 Instruksi Reviewer / Review Checklist

- [x] Pastikan seluruh tampilan di `/program`, `/biaya`, `/tahfidz`, `/metode`, dan `/` tampil responsif dan presisi.
- [x] Klik tombol WhatsApp di halaman manapun untuk memastikan nomor tujuan mengikuti pengaturan di `.env`.
- [x] Jalankan `php artisan test` untuk memastikan regression-free code.

---
