# 🕌 RINGKASAN & EKSPLANASI PROYEK: AL-HIKMAH LMS

> **Dokumen Informasi Proyek untuk Pimpinan / Atasan**  
> **Nama Sistem:** AL-HIKMAH Learning Management System (LMS)  
> **Versi Aplikasi:** 1.0 (Production Ready Base)  
> **Framework & Teknologi:** Laravel 12 | PHP 8.2 | Livewire 4.3 | Flux UI | MySQL | Bootstrap 5  
> **Tanggal Laporan:** 11 Agustus 2026  

---

## 📌 1. EXECUTIVE SUMMARY & VISI PROYEK

**AL-HIKMAH LMS** adalah platform manajemen pendampingan belajar Al-Qur'an terpadu yang dirancang khusus untuk memfasilitasi anak-anak (usia 10–15 tahun) dan dewasa dalam belajar membaca (Iqra/Tahsin), menghafal (Tahfidz), memahami Tajwid, serta pembiasaan Adab & Doa Harian.

### 💡 Masalah yang Diselesaikan:
1. **Pencatatan Manual & Tercecer**: Sebelumnya informasi pendaftaran, progres hafalan, dan nomor kontak CS masih tersebar di spreadsheet/WhatsApp secara manual.
2. **Keterbatasan Pengelolaan Kontak**: Mengubah nomor CS/WhatsApp atau informasi lembaga dulunya memerlukan bantuan developer untuk mengedit file konfigurasi server (`.env`).
3. **Kurangnya Transparansi Progres**: Orang tua kesulitan melacak perkembangan hafalan dan evaluasi tajwid anaknya secara real-time.

### 🎯 Solusi yang Dihadirkan:
1. **Web Landing Page Interaktif & Dinamis**: Halaman publik modern berbasis desain Islami yang menyajikan program belajar, rincian biaya, metode bimbingan, dan galeri.
2. **Modul Pengaturan Website (CMS Settings)**: Antarmuka khusus di Web Admin Panel yang memungkinkan Pengelola/Admin mengubah nomor WhatsApp CS, username Instagram, email kontak, alamat, serta biaya pendaftaran secara instan dari halaman Web.
3. **Multi-Role User Management (RBAC)**: Sistem hak akses 4 tingkat (Admin, Mentor/Guru, Orang Tua, dan Santri).
4. **Livewire Dynamic Component**: Kalender sesi belajar (`SessionCalendar`), pencatat progres hafalan (`ProgressTracker`), dan pembuatan laporan perkembangan otomatis format PDF.

---

## 🏗️ 2. HAK AKSES & MODUL PENGGUNA (4 ROLES)

Sistem membagi pengguna ke dalam 4 peran utama dengan hak akses terintegrasi:

| Peran (Role) | Hak Akses Utama | Fungsi & Fitur Utama |
|---|---|---|
| 🛠️ **Admin** | Full Access Management | Mengelola Master Data Santri, Pendamping, Program, Transaksi Pembayaran, dan Pengaturan Website (CMS Settings). |
| 👨‍🏫 **Mentor / Guru** | Pengelolaan Sesi & Progres | Melihat jadwal mengajar, mencatat capaian hafalan (Surah/Ayat/Juz), memberikan penilaian Tajwid dan Catatan Adab. |
| 👨‍👩‍👦 **Orang Tua / Wali** | Pemantauan & Pendaftaran | Meninjau laporan progres anak secara real-time, mengunduh Laporan PDF bulanan, dan melakukan konsultasi/pendaftaran. |
| 👦 **Santri / Murid** | Personal Dashboard | Melihat target hafalan pribadi, jadwal sesi belajar mendatang, dan riwayat prestasi belajar. |

---

## 🛠️ 3. RINCIAN MODUL FITUR UTAMA SISTEM

### 💼 A. Modul Pengaturan Website (CMS Settings)
- **Halaman Admin:** `/admin/settings`
- **Fungsi:** Mengizinkan Admin mengedit secara dinamis:
  - Nomor WhatsApp CS (otomatis meng-update tombol *Floating WA*, link pendaftaran, dan kontak footer di seluruh website).
  - Username Instagram, Email Resmi, dan Alamat / Area Layanan.
  - Nama Lembaga, Tagline Website, dan Biaya Pendaftaran Standar.
- **Helper Terintegrasi:** `site_setting($key)` dan `wa_url($pesan)` yang memiliki *fallback system* otomatis.

### 📚 B. Modul Program & Investasi Belajar
- **Halaman Publik:** `/program` dan `/biaya`
- **Fungsi:** Menyajikan daftar program secara terstruktur dalam 3 kategori:
  1. **Program Anak (10–15 thn):** Iqra & Dasar Al-Qur'an, Tahsin Dasar, Adab & Doa Harian, Tahfidz Al-Qur'an.
  2. **Program Dewasa & Muslimah:** Belajar dari Nol, Tahsin Dewasa, Kelas Muslimah, Tahfidz Dewasa.
  3. **Program Bahasa Arab:** Bahasa Arab Dasar, Nahwu & Sharaf (dengan desain aksen kartu khusus `.arabic-featured`).

### 📅 C. Modul Penjadwalan & Sesi Belajar (`learning_sessions`)
- **Fungsi:** Mengatur sesi bimbingan antara Pendamping dan Santri.
- **Metode Belajar:** Online, Offline (Home Visit), dan Hybrid.
- **Tampilan:** Komponen Livewire `SessionCalendar` yang mendukung penyaringan berdasarkan status (Terjadwal, Selesai, Dibatalkan).

### 📈 D. Modul Progres Belajar & Laporan PDF
- **Fungsi:** Catatan detail tiap sesi yang mencakup nama Surah, Ayat, Juz, Nilai Tajwid, serta Catatan Adab.
- **Laporan PDF:** Fitur pencetakan otomatis laporan ringkasan perkembangan santri via route `/report/download/{student}`.

---

## 🗄️ 4. ARSITEKTUR DATABASE & MASTER DATA

Aplikasi dibangun menggunakan skema relational database MySQL yang solid:

1. **`users` & `roles`**: Penyimpanan akun pengguna & hak akses RBAC (Admin, Mentor, Parent, Student).
2. **`programs`**: Data program belajar, durasi (minggu), level, dan biaya.
3. **`student_program`**: Tabel pivot pendaftaran murid pada program tertentu.
4. **`learning_sessions`**: Transaksi sesi belajar (tanggal, jam, metode, status, dan catatan).
5. **`progress`**: Records capaian hafalan/bacaan santri per sesi.
6. **`payments`**: Pencatatan invoice pendaftaran & pembayaran SPP bulanan.
7. **`settings`**: Penyimpanan key-value dinamis untuk seluruh konfigurasi website.
8. **`galleries` & `notifications`**: Dokumentasi kegiatan dan notifikasi sistem.

---

## 🧪 5. JAMINAN KUALITAS & TESTING (QA)

Aplikasi **AL-HIKMAH LMS** dibangun mengikuti standar kualitas Laravel terkini (*Laravel 12 & Laravel Boost*):

1. **Database Seeder & Inisialisasi**: Perintah `php artisan migrate:fresh --seed` berjalan **100% sukses** tanpa kendala constraint.
2. **Automated Testing (Pest PHP)**: Pengujian fitur otomatis (`php artisan test`) dinyatakan **LULUS (33 passed, 156 assertions)**.
3. **Standardisasi Kode**: Seluruh kode PHP dan Blade telah dirapikan menggunakan Laravel Pint (`vendor/bin/pint`).

---

## 🚀 6. KESIMPULAN & REKOMENDASI PENGEMBANGAN

Aplikasi **AL-HIKMAH LMS** saat ini berada dalam kondisi **siap pakai (Production Ready)** untuk operasi dasar manajemen bimbingan Al-Qur'an. Pengelola sudah dapat mengoperasikan panel admin, mengubah pengaturan kontak website secara mandiri, dan mendaftarkan santri serta sesi belajar.

**Rencana Pengembangan Lanjutan (Opsional):**
1. **Integrasi Payment Gateway**: Integrasi Otomatis Midtrans/Xendit untuk pembayaran pendaftaran & SPP bulanan via Transfer/QRIS.
2. **Integrasi WhatsApp Gateway**: Pengiriman notifikasi pengingat sesi H-1 secara otomatis ke WhatsApp Orang Tua.

---
*Laporan ini disusun secara otomatis oleh Sistem Manajemen Proyek AL-HIKMAH LMS.*
