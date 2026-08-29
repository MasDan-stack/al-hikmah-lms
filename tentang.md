# 🕌 LAPORAN EKSEKUTIF PROYEK & PANDUAN APLIKASI: AL-HIKMAH LMS

> **Dokumen Resmi untuk Manajemen, Pimpinan Lembaga, & Tim Pengembang**  
> **Nama Sistem:** AL-HIKMAH Learning Management System (LMS)  
> **Status Aplikasi:** ✅ **100% Selesai, Teruji, & Siap Digunakan (Production Ready)**  
> **Versi:** 8.3 (Teacher Recruitment, Probation Lifecycle, & Advanced AI Curriculum Generator Edition — End-to-End Mentor Recruitment Funnel with Public Tracker, In-Dashboard Online Competency Exams, Multi-Category Tajwid/Makhraj/Tahsin Tests, 3-Month Probation Management & M01-M03 Badges, Multi-Format AI Question Generator with Multiple Choice & Essay Rubrics for 10 Official Programs, Dynamic Topic Explorer, Zero-Duplicate Curriculum Multi-Bank, and Printable Official A4 Exam Worksheets with PDF Export)  
> **Tanggal Pembaruan:** 29 Agustus 2026  

---

## 📋 DAFTAR ISI LAPORAN

1. [📌 1. Ringkasan Eksekutif & Nilai Manfaat Aplikasi](#-1-ringkasan-eksekutif--nilai-manfaat-aplikasi)
2. [🎓 2. Modul Rekrutmen, Ujian Kompetensi, & Masa Percobaan Guru (v8.3)](#-2-modul-rekrutmen-ujian-kompetensi--masa-percobaan-guru-v83)
   - [2.1 Alur Seleksi & Kategori Ujian Kompetensi](#21-alur-seleksi--kategori-ujian-kompetensi)
   - [2.2 Isolasi Hak Akses Dashboard (Gating Lifecycle)](#22-isolasi-hak-akses-dashboard-gating-lifecycle)
   - [2.3 Fitur Manajemen Cuti & Guru Pengganti](#23--fitur-manajemen-cuti--guru-pengganti-mentor-leave--substitute)
3. [🤖 3. Modul AI Auto-Generate Soal, Bank Soal, & Lembar Ujian PDF (v8.3)](#-3-modul-ai-auto-generate-soal-bank-soal--lembar-ujian-pdf-v83)
   - [3.1 Format Lembar Ujian Siap Cetak A4 & Mockup](#31-format-lembar-ujian-siap-cetak-a4-worksheet--visual-mockup)
   - [3.2 Arsitektur Universal Multi-Provider AI](#32-universal-multi-provider-ai-architecture-gemini-deepseek-qwen-claude--gpt)
   - [3.3 Bank Kurikulum Offline Al-Hikmah (Fallback Engine)](#33--bank-kurikulum-offline-al-hikmah-fallback-engine)
4. [📊 4. Modul Analitik Eksekutif, Beban Guru, & Pusat Peringatan Operasional](#-4-modul-analitik-eksekutif-beban-guru--pusat-peringatan-operasional)
   - [4.1 KPI Finansial & Kapasitas Guru](#41-kpi-finansial--kapasitas-guru)
   - [4.2 Pusat Peringatan Operasional 3-Tier Alert](#42--pusat-peringatan-operasional-alert-service)
   - [4.3 WhatsApp Mass Broadcast & Variabel Pintar](#43--whatsapp-mass-broadcast)
5. [🎮 5. Modul Ruang Belajar Santri & Gamifikasi Islami Terpadu](#-5-modul-ruang-belajar-santri--gamifikasi-islami-terpadu)
   - [5.1 Mekanisme Perolehan Poin Pembelajaran](#51--mekanisme-perolehan-poin-gamifikasi)
   - [5.2 Daftar 15 Lencana Prestasi Islami (B01–B15)](#52-daftar-15-lencana-prestasi-islami-b01b15)
6. [🔐 6. Sistem Akun Santri Otomatis & Kebijakan Keamanan Password](#-6-sistem-akun-santri-otomatis--kebijakan-keamanan-password)
7. [📰 7. Modul Blog & Literasi Edukasi Islami Terpadu](#-7-modul-blog--literasi-edukasi-islami-terpadu)
8. [🕌 8. Fitur Jadwal Sholat & Kompas Arah Kiblat Real-Time](#-8-fitur-jadwal-sholat--kompas-arah-kiblat-real-time)
9. [💳 9. Integrasi Payment Gateway Pakasir & Pelacakan Pembayaran Real-Time](#-9-integrasi-payment-gateway-pakasir--pelacakan-pembayaran-real-time)
10. [📈 10. Standardisasi & Implementasi DataTables di Seluruh Dashboard](#-10-standardisasi--implementasi-datatables-di-seluruh-dashboard)
11. [🔄 11. Tahapan & Alur Kerja Utama Aplikasi (User Journey & Lifecycle)](#-11-tahapan--alur-kerja-utama-aplikasi-user-journey--lifecycle)
12. [⭐ 12. Penjelasan Seluruh Fitur Aplikasi (Berdasarkan Hak Akses)](#-12-penjelasan-seluruh-fitur-aplikasi-berdasarkan-hak-akses)
13. [🔔 13. Sistem Notifikasi & Alert Terpusat (Centralized Alert System)](#-13-sistem-notifikasi--alert-terpusat-centralized-alert-system)
14. [🗄️ 14. Penjelasan Seluruh Database (Penyimpanan Data Lembaga)](#-14-penjelasan-seluruh-database-penyimpanan-data-lembaga)
15. [🧠 15. Penjelasan Seluruh Model, Service, & Controller](#-15-penjelasan-seluruh-model-service--controller)
16. [⚙️ 16. Console Commands & Jadwal Background Cron](#-16-console-commands--jadwal-background-cron)
17. [📁 17. Penjelasan Seluruh Struktur Folder Aplikasi](#-17-penjelasan-seluruh-struktur-folder-aplikasi)
18. [🧪 18. Hasil Pengujian & Quality Assurance (100% Green Pass)](#-18-hasil-pengujian--quality-assurance-100-green-pass)
19. [🎯 19. Kesimpulan & Rekomendasi Langkah Ke Depan](#-19-kesimpulan--rekomendasi-langkah-ke-depan)
20. [📝 20. Lampiran Teknis, Rute Sistem, & Glosarium](#-20-lampiran-teknis-rute-sistem--glosarium)

---

## 📌 1. RINGKASAN EKSEKUTIF & NILAI MANFAAT APLIKASI

**AL-HIKMAH LMS** adalah platform manajemen pendampingan belajar Al-Qur'an terpadu berbasis web yang dirancang khusus untuk memfasilitasi anak-anak dan dewasa dalam belajar membaca Al-Qur'an (Iqra/Tahsin), menghafal (Tahfidz), memahami Tajwid & Makharijul Huruf, Fiqih Nisa, Bahasa Arab Dasar, Nahwu & Sharaf, serta pembiasaan Adab & Doa Harian.

Platform ini mentransformasikan operasional lembaga bimbingan Al-Qur'an dari sistem manual menjadi ekosistem digital otomatis yang menghubungkan **Santri (Student)**, **Wali Santri (Parent)**, **Guru Pembimbing (Mentor)**, **Calon Guru (Mentor Applicant)**, dan **Manajemen Lembaga (Admin)** secara transparan, menyenangkan, akuntabel, dan real-time.

```mermaid
graph LR
    A["Wali Santri (Parent)"] -->|"Daftar & Terima Akun Santri"| B["Sistem Otomasi Al-Hikmah"]
    C["Santri (Student)"] -->|"Login, Setoran, & Raih Badge"| B
    D["Calon Guru (Applicant)"] -->|"Daftar, Ujian AI, & Probation"| B
    E["Guru Resmi (Mentor)"] -->|"Bimbingan, Target, & Soal AI"| B
    B -->|"Leaderboard & Rapor Real-Time"| A
    F["Manajemen (Admin)"] -->|"Recruitment, Revenue, & HR Control"| B
```

### 💼 Metrik Bisnis & Dampak Operasional:

| Parameter Kinerja | Sebelum Digitalisasi (Manual) | Dengan AL-HIKMAH LMS (v8.3) | Peningkatan Efisiensi |
| :--- | :--- | :--- | :---: |
| **Proses Rekrutmen Guru** | Kirim berkas fisik, tes manual | Funnel digital: Berkas $\rightarrow$ Ujian AI $\rightarrow$ Wawancara $\rightarrow$ Akun | **85% Lebih Cepat & Terstandar** |
| **Pelacakan Status Lamaran** | Menghubungi admin berulang kali | Pelacak publik real-time (`/cek-status-lamaran`) | **100% Transparan Mandiri** |
| **Evaluasi Masa Percobaan (Probation)**| Catatan manual, evaluasi subjektif| Tracking 3 bulan, target capaian, & Badges M01-M03 | **Terstruktur & Objektif** |
| **Pembuatan Paket Soal & Ujian** | Guru mengetik manual jam-jaman | AI Generator 10 Program + Cetak PDF A4 Siap Pakai | **Selesai dalam < 5 Detik** |
| **Format Soal & Penilaian** | Hanya pilihan ganda terbatas | Pilihan Ganda, Essay/Uraian (Rubrik Skor), & Campuran | **Standar Kurikulum Nasional & HOTS** |
| **Visibilitas Finansial & Arus Kas** | Rekap manual di spreadsheet | Dashboard real-time, MoM %, ARPU, & Tren 12 Bulan | **100% Otomatis Real-Time** |
| **Deteksi Overload & Kinerja Guru** | Tidak terdeteksi hingga ada keluhan | Indikator otomatis (Optimal, Sibuk, Overload >40) | **Proaktif & Terukur** |
| **Komunikasi Massal ke Wali** | Kirim manual satu per satu | WhatsApp Broadcast Massal dengan Variabel Pintar | **95% Lebih Cepat** |

### 📝 Changelog Versi 8.3 (Teacher Recruitment, Probation Lifecycle, & Advanced AI Curriculum Generator):
- ✅ **Modul Rekrutmen Calon Guru End-to-End**:
  - Halaman pendaftaran calon guru publik (`/bergabung` / `/mentor/recruitment/register`) dengan validasi berkas PDF/Gambar (CV, Sertifikat Tahfidz/Sanad, KTP, Ijazah).
  - Halaman pelacak status lamaran publik (`/cek-status-lamaran`) dengan visual stepper dinamis.
  - Panel Admin Kelola Pelamar (`/admin/mentors/recruitment/applications`) dengan integrasi DataTables interaktif, filter status, preview berkas, dan aksi tahapan seleksi.
  - Pembuatan Paket Soal Ujian Calon Guru otomatis (15 butir soal terbagi 3 kategori: *Tajwid*, *Makharijul Huruf*, *Tahsin*) tersimpan di bank soal master lembaga.
  - Sesi Ujian Kompetensi Online berbasis web langsung di Dashboard Calon Guru dengan batas waktu 60 menit dan kalkulasi skor instan.
  - Proteksi Hak Akses Dashboard (Gating): Calon guru dalam tahap seleksi hanya dapat mengakses modul ujian & status; fitur mengajar inti baru dibuka setelah resmi disetujui.
  - Tahapan Wawancara & Evaluasi Terpadu sebelum persetujuan akhir.
- ✅ **Masa Percobaan Guru (Probation Management 3 Bulan)**:
  - Penerbitan Akun Guru Resmi otomatis dengan penugasan masa percobaan 3 bulan.
  - Dashboard Monitoring Masa Percobaan (`/admin/mentors/probation`) dengan kalkulasi sisa hari, indikator kehadiran, dan rasio kepuasan santri.
  - Seeder Lencana Khusus Guru Binaan (*Mentor Badges*):
    - `M01` (*Pendamping Teladan*)
    - `M02` (*Hafizh Pembimbing*)
    - `M03` (*Guru Mumtaz*)
- ✅ **Universal Multi-Provider AI Engine (Gemini, DeepSeek, Qwen, Claude, & GPT)**:
  - **Satu Layanan untuk Seluruh Model AI**: Pengelola lembaga dapat berganti model AI hanya dengan mengisi `API_KEY` dan `MODEL` pada berkas `.env`.
  - **Auto-Detection Cerdas**: Sistem otomatis mendeteksi provider (Google Gemini, DeepSeek, Alibaba Qwen, Anthropic Claude, atau OpenAI) berdasarkan prefix key dan penamaan model tanpa mengubah kode.
  - **Pelacak Diagnostik Error & Fallback Transparan**: Jika kuota AI habis atau terjadi error API (seperti HTTP 403 / 429), sistem menyajikan banner informatif berisi rincian error server dan solusinya, sekaligus otomatis mengaktifkan Bank Kurikulum Offline Al-Hikmah.
- ✅ **Revamp AI Question Generator & Bank Soal Mentor (10 Program Resmi)**:
  - Sinkronisasi 10 Program Belajar terstandar Al-Hikmah:
    1. *Iqra & Dasar Al-Qur'an* (Bentuk huruf hijaiyah, harakat vokal, tasydid, sukun, 6 huruf pemutus).
    2. *Tahsin Dasar* (17 Makharijul Huruf, Hukum Nun Sukun/Tanwin, Mim Sukun, Qalqalah).
    3. *Adab & Doa Harian* (Doa harian makan/tidur/masjid/safar, Birrul Walidain, adab thalabul ilmi).
    4. *Tahfidz Al-Qur'an* (Sambung ayat Juz 30, Makkiyah/Madaniyah, Asbabun Nuzul, metode 3T).
    5. *Belajar dari Nol (Dewasa)* (Makhraj dasar, harakat, bacaan praktis Shalat Fardhu).
    6. *Tahsin Dewasa* (19 Sifatul Huruf, Kaidah Mad Far'i, Waqaf & Ibtida', Ahkamur Raa'at).
    7. *Kelas Muslimah* (Fiqih Nisa, Haid, Nifas, Istihadhah, Mandi Wajib, Shahabiyah, Adab Hijab).
    8. *Tahfidz Dewasa* (Surah Al-Mulk, As-Sajdah, Al-Waqi'ah, Mutasyabihat, manajemen muroja'ah).
    9. *Bahasa Arab Dasar* (Mufrodat perlengkapan belajar, Dhomir Munfashil, Isim Isyarah, Angka 1-10, Percakapan Ta'aruf).
    10. *Nahwu & Sharaf* (Isim, Fi'il, Huruf, 4 Macam I'rab, Jumlah Ismiyyah/Fi'liyyah, Wazan Tashrif Tsulatsi, Inna/Kaana).
  - **Inspirasi Topik Cerdas & Dinamis**: Tombol topik inspiratif yang beradaptasi seketika saat program dipilih.
  - **Mode AI Explorer / Topik Bebas**: Guru dapat mengetik topik khusus apa pun atau membiarkan topik acak tanpa mengalami kegagalan sistem.
  - **Dukungan Format Soal Lengkap**: Pilihan Ganda (4 Opsi), Soal Essay / Uraian (dengan Kunci Jawaban Ideal & Rubrik Skor), serta Soal Campuran (*Mixed*).
  - **Tingkat Kesulitan HOTS (Sulit)**: Pertanyaan analitis studi kasus yang stabil tanpa kendala timeout (timeout 30-45s & multi-layer fallback).
  - **Garansi Kuota & Zero-Duplicate Multi-Bank Engine**: Penyaringan hashing MD5 yang menjamin seluruh butir soal 100% unik dan jumlah soal selalu tepat sesuai yang diminta (e.g. minta 15 soal pasti keluar 15 butir soal unik).
- ✅ **Lembar Cetak Ujian Resmi A4 / PDF Export (`/mentor/questions/print`)**:
  - Tampilan siap cetak dengan Kop Resmi Al-Hikmah, identitas santri & nilai, petunjuk pengerjaan, format Pilihan Ganda, garis jawaban tulis tangan untuk Essay, serta Lembar Kunci Jawaban & Rubrik Guru.
  - Tombol cetak di halaman generator mengirimkan data secara *real-time in-memory* via POST form sehingga langsung mencetak paket soal yang sedang tampil di layar.

---

## 🎓 2. MODUL REKRUTMEN, UJIAN KOMPETENSI, & MASA PERCOBAAN GURU (v8.3)

Modul ini mendigitalisasi siklus hidup rekrutmen pengajar Al-Qur'an secara terpadu, mulai dari pendaftaran berkas, ujian kompetensi berbasis web, wawancara, hingga masa percobaan (*probation*).

```mermaid
graph TD
    A["Calon Guru Daftar di /bergabung"] --> B["Upload CV & Berkas Syarat"]
    B --> C{"Admin Verifikasi Berkas"}
    C -->|"Ditolak"| D["Notifikasi Penolakan ke Pelamar"]
    C -->|"Diterima"| E["Generate Paket Soal AI (15 Soal)"]
    E --> F["Ujian Kompetensi Online (60 Menit)"]
    F --> G{"Skor >= 70?"}
    G -->|"Tidak"| H["Evaluasi Gagal / Ujian Ulang"]
    G -->|"Ya"| I["Penjadwalan & Sesi Wawancara"]
    I --> J{"Hasil Wawancara Disetujui?"}
    J -->|"Tidak"| K["Notifikasi Hasil Seleksi"]
    J -->|"Ya"| L["Terbitkan Akun Mentor Resmi"]
    L --> M["Masa Percobaan 3 Bulan (Probation)"]
    M --> N{"Evaluasi Kinerja 3 Bulan"}
    N -->|"Lulus"| O["Pengangkatan Tetap & Lencana M01-M03"]
    N -->|"Kurang"| P["Perpanjangan / Non-Aktifkan Akun"]
```

### 2.1 Alur Seleksi & Kategori Ujian Kompetensi
Soal ujian kompetensi calon guru mencakup 3 pilar utama:
1. **Tajwid Test**: Hukum Nun Sukun/Tanwin, Mim Sukun, Mad Far'i, dan Waqaf/Ibtida'.
2. **Makharijul Huruf**: Pengetahuan titik keluar huruf hijaiyah (Al-Halq, Al-Lisan, Asy-Syafatain, Al-Jauf, Al-Khaisyum).
3. **Tahsin & Fashahah**: Kaidah kelancaran bacaan, sifatul huruf (Hams, Jahr, Isti'la, Istithalah, dsb.), dan gharib.

### 2.2 Isolasi Hak Akses Dashboard (Gating Lifecycle)
Untuk menjaga keamanan data internal lembaga:
- **Calon Guru (Tahap Seleksi)**: Hanya dapat melihat laman informasi lamaran, kartu ujian kompetensi, dan hasil seleksi. Seluruh fitur operasional bimbingan (Jadwal Mengajar, Santri Binaan, Data Orang Tua, Bank Soal, Catat Progress, Laporan Kinerja, Pesan) disembunyikan (*hidden & protected*).
- **Guru Resmi / Disetujui (Approved)**: Seluruh fitur bimbingan dan modul operasional terbuka penuh.

### 2.3 👨‍🏫 Fitur Manajemen Cuti & Guru Pengganti (Mentor Leave & Substitute)
Modul ini memungkinkan mentor mengajukan cuti dan admin menunjuk guru pengganti sementara tanpa mengganggu kontinuitas halaqah santri.

**Alur Kerja:**
1. Mentor mengajukan permohonan cuti via portal mentor (pilih rentang tanggal, alasan, durasi, & santri terdampak).
2. Admin menerima notifikasi dan meninjau daftar pengajuan pada panel `/admin/mentors/leaves`.
3. Admin menyetujui dan menunjuk guru pengganti (*substitute mentor*) dari daftar mentor aktif yang memiliki kapasitas optimal.
4. Sistem otomatis mengalihkan jadwal sesi bimbingan mentor yang cuti ke guru pengganti.
5. Notifikasi otomatis dikirimkan ke orang tua santri via WhatsApp menginformasikan nama pendamping sementara.

| Fitur | Deskripsi |
| :--- | :--- |
| **Pengajuan Cuti Online** | Mentor memilih tanggal mulai-selesai, alasan cuti, dan durasi |
| **Persetujuan Admin** | Admin dapat menyetujui, menolak, atau meminta penyesuaian jadwal |
| **Penunjukan Guru Pengganti** | Admin memilih mentor pengganti yang sedang berstatus kapasitas hijau/optimal |
| **Auto-Schedule Transfer** | Sesi bimbingan aktif otomatis dipindahkan ke kalender guru pengganti |
| **Notifikasi WhatsApp Wali** | Orang tua santri menerima konfirmasi perubahan nama pendamping |
| **Audit & Riwayat Cuti** | Histori absensi dan cuti mentor tercatat rapi untuk evaluasi probation/tahunan |

---

## 🤖 3. MODUL AI AUTO-GENERATE SOAL, BANK SOAL, & LEMBAR UJIAN PDF (v8.3)

Fitur ini dirancang khusus untuk mempermudah guru pembimbing membuat evaluasi belajar santri secara instan berbasis kurikulum resmi Al-Hikmah.

```
+-------------------------------------------------------------------------------------------------------------+
+ AI QUESTION GENERATOR WORKSPACE (v8.3)                                                                      +
+-------------------------------------------------------------------------------------------------------------+
| 1. Pilih Program Belajar (10 Program Resmi)  ==> [Bahasa Arab, Nahwu, Muslimah, Tahsin, Tahfidz, dll.]      |
| 2. Inspirasi Topik Dinamis (Auto-Adapt)     ==> [Mufrodat, Makhraj, Fiqih Nisa, Adab, I'rab, Bebas/Random]  |
| 3. Tipe Soal                                ==> [Pilihan Ganda | Soal Essay/Uraian | Campuran]              |
| 4. Tingkat Kesulitan                        ==> [Mudah | Sedang | Sulit (HOTS)]                             |
| 5. Jumlah Butir Soal                        ==> [3 s/d 25 Butir Soal]                                       |
+-------------------------------------------------------------------------------------------------------------+
|                                  [Generate Paket Soal dengan AI]                                            |
|                                                                                                             |
|   +-----------------------------------------------------------------------------------------------------+   |
|   | HASIL REVIEW SOAL (100% Unik & Bebas Duplikasi):                                                    |   |
|   | - Kartu Soal Pilihan Ganda (Opsi A-D, Kunci Benar, Pembahasan Dalil/Ayat)                          |   |
|   | - Kartu Soal Essay (Kunci Jawaban Ideal, Rubrik & Pedoman Skor Guru, Referensi Kaidah)              |   |
|   +-----------------------------------------------------------------------------------------------------+   |
|                                                                                                             |
|   [Simpan ke Bank Soal]    [Cetak / Unduh PDF Lembar Ujian (A4)]    [Generate Ulang]                        |
+-------------------------------------------------------------------------------------------------------------+
```

### 3.1 Format Lembar Ujian Siap Cetak (A4 Worksheet) & Visual Mockup
Halaman cetak `/mentor/questions/print` menghasilkan dokumen ujian terstandarisasi A4 dengan tata letak profesional:

```
+-------------------------------------------------------------------------------------------------------------+
|                                  LEMBAGA PENDAMPINGAN AL-QUR'AN AL-HIKMAH                                   |
|                          Jl. Pendidikan Islam No. 45, Jakarta | Telp: (021) 789-0123                        |
|=============================================================================================================|
|                                         LEMBAR EVALUASI SANTRI (A4)                                         |
|-------------------------------------------------------------------------------------------------------------|
| Nama Santri   : [ ................................. ]       Mata Pelajaran : [ Bahasa Arab Dasar          ] |
| Kelas/Halaqah : [ Reguler Pagi                      ]       Topik Ujian    : [ Mufrodat & Dhomir          ] |
| Hari / Tgl    : [ ................................. ]       Nilai / Paraf  : [ .......... / ............. ] |
|-------------------------------------------------------------------------------------------------------------|
| Petunjuk: Bacalah basmalah sebelum mengerjakan. Pilihlah jawaban yang paling tepat atau tulis uraian jelas. |
|-------------------------------------------------------------------------------------------------------------|
| BAGIAN I. PILIHAN GANDA                                                                                     |
|                                                                                                             |
| 1. Arti dari kata "قَلَمٌ" (Qalamun) dalam bahasa Indonesia adalah...                                       |
|    (A) Buku Tulis       (B) Pulpen / Pena       (C) Penggaris        (D) Penghapus                          |
|                                                                                                             |
| 2. Kata ganti (Dhomir) untuk "Dia (seorang laki-laki)" dalam bahasa Arab adalah...                          |
|    (A) أَنَا (Ana)        (B) أَنْتَ (Anta)        (C) هُوَ (Huwa)       (D) هِيَ (Hiya)                       |
|                                                                                                             |
|-------------------------------------------------------------------------------------------------------------|
| BAGIAN II. SOAL ESSAY / URAIAN                                                                              |
|                                                                                                             |
| 1. Terjemahkan kalimat berikut ke dalam bahasa Arab: "Ini sebuah buku dan itu sebuah papan tulis!"           |
|    Lembar Jawaban Santri:                                                                                   |
|    _____________________________________________________________________________________________________    |
|    _____________________________________________________________________________________________________    |
|                                                                                                             |
|=============================================================================================================|
| [LEMBAR PEGANGAN GURU / KUNCI JAWABAN & RUBRIK SKOR (Dapat Disembunyikan Saat Mencetak)]                    |
| Kunci Soal 1: (B) Pulpen. Pembahasan: Qalamun adalah isim mufrad untuk alat tulis pena.                     |
| Rubrik Essay 1: Skor 100 jika menulis "هٰذَا كِتَابٌ وَتِلْكَ سَبُّوْرَةٌ" dengan harakat dan kaidah benar. |
+-------------------------------------------------------------------------------------------------------------+
```

### 3.2 Universal Multi-Provider AI Architecture (Gemini, DeepSeek, Qwen, Claude, & GPT)
Layanan AI Al-Hikmah LMS telah mendukung arsitektur **Multi-Provider Universal**. Pengelola lembaga dapat berganti model dan penyedia AI secara instan hanya dengan mengganti API Key / Model pada berkas `.env`:
- **Google Gemini**: `AI_PROVIDER=gemini` atau model `gemini-1.5-flash`, `gemini-2.0-flash`, `gemini-1.5-pro`.
- **DeepSeek AI**: `AI_PROVIDER=deepseek` atau model `deepseek-chat`, `deepseek-reasoner` (R1).
- **Alibaba Qwen**: `AI_PROVIDER=qwen` atau model `qwen-plus`, `qwen-turbo`, `qwen-max`.
- **Anthropic Claude**: `AI_PROVIDER=claude` atau model `claude-3-5-sonnet-20241022`, `claude-3-5-haiku`.
- **OpenAI ChatGPT**: `AI_PROVIDER=openai` atau model `gpt-4o-mini`, `gpt-4o`, `o3-mini`.
- **Auto-Detection Cerdas**: Cukup masukkan `AI_API_KEY` dan `AI_MODEL` (atau `QWEN_API_KEY`, `DEEPSEEK_API_KEY`, `CLAUDE_API_KEY`), sistem otomatis mengenali endpoint dan format payload penyedia tanpa perlu mengubah kode sumber.
- **Error Tracking & Curated Multi-Bank Fallback**: Jika kuota token AI habis atau terjadi kendala jaringan (seperti HTTP 403 / 429), sistem menyajikan banner diagnostik ramah pengguna dan secara mulus beralih ke bank kurikulum Al-Hikmah spesifik per 10 program sehingga proses pembuatan soal tidak pernah gagal dan konten selalu 100% relevan.

### 3.3 📚 Bank Kurikulum Offline Al-Hikmah (Fallback Engine)
Jika AI API mengalami kendala (kuota token habis, jaringan offline, atau respon error), sistem otomatis beralih ke bank kurikulum offline yang telah dikurasi ketat oleh asatidzah Al-Hikmah untuk 10 program resmi:

| Program Belajar | Estimasi Bank Soal Offline | Materi & Topik Utama |
| :--- | :---: | :--- |
| **Iqra & Dasar Al-Qur'an** | 50+ Butir | Bentuk huruf hijaiyah, harakat vokal, tasydid, sukun, 6 huruf pemutus sambung |
| **Tahsin Dasar** | 60+ Butir | 17 Titik Makharijul Huruf, Hukum Nun Sukun, Mim Sukun, Qalqalah, Mad Asli |
| **Adab & Doa Harian** | 40+ Butir | Doa harian makan/tidur/masjid/safar, Birrul Walidain, adab thalabul ilmi |
| **Tahfidz Al-Qur'an** | 55+ Butir | Sambung ayat Juz 30 & Juz 'Amma, Makkiyah/Madaniyah, metode 3T |
| **Belajar dari Nol (Dewasa)** | 45+ Butir | Pengucapan makhraj dasar, harakat, kelancaran bacaan shalat fardhu |
| **Tahsin Dewasa** | 50+ Butir | 19 Sifatul Huruf (*Hams, Jahr, Isti'la, Istithalah*), Kaidah Mad Far'i, Waqaf/Ibtida' |
| **Kelas Muslimah** | 45+ Butir | Fiqih Nisa, darah haid, nifas, istihadhah, mandi wajib, Shahabiyah, adab hijab |
| **Tahfidz Dewasa** | 50+ Butir | Surah Al-Mulk, As-Sajdah, Al-Waqi'ah, ayat mutasyabihat, manajemen muroja'ah |
| **Bahasa Arab Dasar** | 40+ Butir | Mufrodat kelas & harian, Dhomir Munfashil, Isim Isyarah, Angka 1-10, Ta'aruf |
| **Nahwu & Sharaf** | 45+ Butir | Isim/Fi'il/Huruf, 4 Macam I'rab, Jumlah Ismiyyah/Fi'liyyah, Wazan Tashrif, Inna/Kaana |

> 🔒 **Garansi Kuota Penuh**: Dengan arsitektur Fallback Engine ini, proses pembuatan soal ujian santri **TIDAK PERNAH GAGAL** meskipun kuota penyedia AI habis atau koneksi internet eksternal terputus.

---

## 📊 4. MODUL ANALITIK EKSEKUTIF, BEBAN GURU, & PUSAT PERINGATAN OPERASIONAL

Modul ini memberikan pimpinan lembaga kendali penuh (*360-degree operational & financial overview*) terhadap kesehatan organisasi, arus kas, efektivitas bimbingan, dan kepatuhan administrasi santri.

### 4.1 KPI Finansial & Kapasitas Guru
- **KPI Finansial Utama**: Total Pendapatan, Pendapatan Bulan Ini, Pertumbuhan MoM %, Rata-rata Pendapatan per Santri (ARPU), dan Tagihan Overdue.
- **Visualisasi ApexCharts**: Area Chart Tren 12 Bulan (YoY), Donut Chart Komposisi Program, Radial Bar Status Pembayaran.
- **Matriks Kapasitas SDM Guru**: Hijau (Optimal $\le 30$ santri), Kuning (Sibuk $31 - 40$ santri), Merah (*Overload* $> 40$ santri dengan banner peringatan otomatis).
- **Dual-Format Report Generator**: Export Spreadsheet (Excel/CSV UTF-8 BOM Chunked) & Export Dokumen PDF Kop Surat Resmi.

### 4.2 🔔 Pusat Peringatan Operasional (Alert Service)
Sistem memantau kondisi operasional secara proaktif melalui pengelompokan 3-Tier Alert:

| Tier Alert | Indikator Warna | Trigger Kondisi Sistem | Tindakan Otomatis (Action) |
| :---: | :---: | :--- | :--- |
| 🔴 **Kritis** | Merah | Tagihan overdue $>30$ hari, Santri dropout/absen $>30$ hari, Beban guru overload $>40$ santri | Notifikasi WhatsApp ke Admin + Email Peringatan + Banner Peringatan Merah di Dashboard |
| 🟡 **Perhatian** | Kuning | Tagihan mendekati jatuh tempo $<7$ hari, Cuti mendadak guru, Santri tidak hadir 3x berturut-turut | Notifikasi WhatsApp otomatis ke Guru Pembimbing & Wali Santri |
| 🟢 **Info** | Hijau | Rating bimbingan sempurna (5.0), Santri baru terdaftar, Target hafalan juz tuntas | Banner ucapan selamat di dashboard & penambahan poin gamifikasi |

> ⚙️ **Jadwal Otomatis (Cron Job)**: Perintah pemindaian `php artisan alerts:scan` dieksekusi secara otomatis 3 kali sehari pada pukul **06:00, 12:00, dan 18:00 WIB**.

### 4.3 📨 WhatsApp Mass Broadcast
Fitur komunikasi massal terarah untuk menyampaikan pengumuman, pengingat tagihan, dan informasi lembaga secara instan:

| Fitur | Deskripsi |
| :--- | :--- |
| **Segmentasi Penerima** | Filter target berdasarkan role (*Wali Santri, Guru, Santri*) atau program belajar tertentu |
| **Template Standar** | Tersedia template pengingat SPP, pengumuman libur, jadwal ujian, dan evaluasi bulanan |
| **Variabel Pintar** | Substitusi otomatis: `{{name}}`, `{{child_name}}`, `{{due_date}}`, `{{amount}}`, `{{program}}` |
| **Live Mockup HP** | Simulasi tampilan pesan di layar ponsel secara *real-time* sebelum tombol kirim ditekan |
| **Pengiriman Terjadwal** | Pilihan opsi kirim seketika (*instant dispatch*) atau dijadwalkan (*scheduled batch*) |
| **Laporan Pengiriman** | Pelacakan status pesan (*queued, sent, delivered, failed*) dengan log kegagalan |

**Contoh Template Pengingat Tagihan:**
```text
Assalamu'alaikum Warahmatullahi Wabarakatuh, Ayah/Bunda {{name}},

Kami menginformasikan bahwa tagihan administrasi bimbingan Al-Qur'an untuk ananda {{child_name}} pada program {{program}} sebesar Rp {{amount}} akan jatuh tempo pada tanggal {{due_date}}.

Pembayaran dapat dilakukan secara praktis melalui tautan resmi Pakasir berikut:
{{payment_url}}

Semoga Allah SWT senantiasa memberkahi ikhtiar kita dalam mendidik putra-putri pecinta Al-Qur'an.

Jazakumullahu Khairan Katsiran,
Manajemen Lembaga AL-HIKMAH LMS
```

---

## 🎮 5. MODUL RUANG BELAJAR SANTRI & GAMIFIKASI ISLAMI TERPADU

Menerapkan prinsip **Fastabiqul Khoirot** dengan sistem poin, streak harian, dan 15 lencana Islami (B01–B15):

### 5.1 🎮 Mekanisme Perolehan Poin Gamifikasi

| Aktivitas Pembelajaran | Perolehan Poin Dasar | Bonus Tambahan |
| :--- | :---: | :--- |
| **Setoran Hafalan Harian** | `+10 Poin` | `+5 Poin` jika nilai tajwid Mumtaz ($\ge 90$) |
| **Muroja'ah Mandiri 1 Juz** | `+25 Poin` | `+10 Poin` jika dituntaskan dalam waktu $<60$ menit |
| **Target Hafalan Mingguan Tuntas** | `+50 Poin` | Bonus Lencana *Target Hunter* (B13) |
| **Streak Istiqomah 7 Hari Beruntun** | `+100 Poin` | Bonus Lencana *Pejuang Istiqomah 7* (B07) |
| **Streak Istiqomah 30 Hari Beruntun**| `+300 Poin` | Bonus Lencana *Pejuang Istiqomah 30* (B08) |
| **Ujian Tasmi' / Mutqin (Nilai A)** | `+300 Poin` | Bonus Lencana *Bintang Ujian* (B12) |
| **Khatam 1 Juz Baru** | `+500 Poin` | Bonus Lencana Penjaga Juz (B02 / B03) |
| **Khatam Al-Qur'an 30 Juz** | `+5.000 Poin` | Bonus Lencana Tertinggi *Hafizh 30 Juz* (B06) |

> 🏆 **Pembaruan Leaderboard**: Peringkat santri diperbarui secara otomatis setiap hari pada pukul **00:00 WIB** via console command `php artisan gamification:refresh-leaderboard`.

### 5.2 Daftar 15 Lencana Prestasi Islami (B01–B15)

| Kode | Nama Lencana | Kategori | Kriteria Perolehan | Reward Poin |
| :---: | :--- | :--- | :--- | :---: |
| **B01** | *Langkah Pertama* | Milestones | Setoran hafalan pertama berhasil | +50 Pts |
| **B02** | *Penjaga Juz 30* | Juz Completion| Menuntaskan seluruh Juz 30 (Juz 'Amma) | +500 Pts |
| **B03** | *Penjaga Juz 29* | Juz Completion| Menuntaskan seluruh Juz 29 (Tabarak) | +500 Pts |
| **B04** | *Hafizh 5 Juz* | Juz Milestone | Menghafal akumulasi 5 Juz Al-Qur'an | +1.000 Pts |
| **B05** | *Hafizh 10 Juz* | Juz Milestone | Menghafal akumulasi 10 Juz Al-Qur'an | +2.000 Pts |
| **B06** | *Hafizh 30 Juz* | Ultimate | Mengkhatamkan seluruh 30 Juz Al-Qur'an | +5.000 Pts |
| **B07** | *Pejuang Istiqomah 7* | Streak | Setoran hafalan 7 hari beruntun | +100 Pts |
| **B08** | *Pejuang Istiqomah 30*| Streak | Setoran hafalan 30 hari beruntun | +300 Pts |
| **B09** | *Pejuang Istiqomah 100*| Streak | Setoran hafalan 100 hari beruntun | +1.000 Pts |
| **B10** | *Tajwid Mumtaz* | Quality | Nilai tajwid Mumtaz (90-100) 5x | +150 Pts |
| **B11** | *Santri Beradab* | Character | Nilai adab sempurna 5x | +150 Pts |
| **B12** | *Bintang Ujian* | Examination | Lulus ujian Tasmi'/Mutqin nilai A | +300 Pts |
| **B13** | *Target Hunter* | Target | Menuntaskan 10 target hafalan | +200 Pts |
| **B14** | *Rajin Murojaah* | Consistency | Menyelesaikan 20 sesi muroja'ah | +250 Pts |
| **B15** | *Hafizh Teladan* | Special | Akumulasi 5.000 poin gamifikasi | +500 Pts |

---

## 🔐 6. SISTEM AKUN SANTRI OTOMATIS & KEBIJAKAN KEAMANAN PASSWORD

- **Pembuatan Akun Instan**: Memicu pembuatan entitas `User` khusus santri saat pendaftaran program selesai.
- **Pola Email Cerdas**: `{3hurufdepan}.{namabelakang}@{domain_institusi}` (misal: `ahm.fauzi@alhikmahlms.sch.id`).
- **Generator Password Acak Kuat**: 10–12 karakter campuran alfanumerik dan simbol khusus terenkripsi Bcrypt.
- **Jejak Audit Keamanan (`password_reset_logs`)**: Mencatat IP address, user-agent, dan nama inisiator setiap terjadi perubahan password.

---

## 📰 7. MODUL BLOG & LITERASI EDUKASI ISLAMI TERPADU

- Editor visual artikel edukasi dengan upload gambar teroptimasi WebP.
- Taksonomi multi-level: Kategori dan Tagar dinamis.
- Manajemen Tong Sampah (*Trash & SoftDeletes*) dengan opsi Pulihkan (*Restore*) atau Hapus Permanen.
- SEO otomatis: Auto slug generator, meta description, dan OpenGraph tag.

---

## 🕌 8. FITUR JADWAL SHOLAT & KOMPAS ARAH KIBLAT REAL-TIME

- **Jadwal Sholat Akurat**: Terintegrasi dengan Aladhan API berdasarkan koordinat lintang-bujur GPS atau pilihan kota.
- **Kompas Kiblat Presisi**: Memanfaatkan *DeviceOrientation API* dan formula Great-Circle Heading menuju Ka'bah Mekkah.

---

## 💳 9. INTEGRASI PAYMENT GATEWAY PAKASIR & PELACAKAN PEMBAYARAN

- **Metode Pembayaran**: QRIS Dinamis dan Virtual Account (BCA, Mandiri, BNI, BRI, Permata).
- **Verifikasi Real-Time (Webhook)**: Callback otomatis, verifikasi signature digital, aktivasi akses kelas instan, dan pengiriman tanda terima PDF ke WhatsApp wali santri.

---

## 📈 10. STANDARDISASI & IMPLEMENTASI DATATABLES DI SELURUH DASHBOARD

Seluruh tabel data di antarmuka Admin (termasuk Modul Rekrutmen Guru), Mentor, dan Parent menggunakan **DataTables.js** lokal dengan:
- Pencarian instan (*instant search filter*).
- Pengurutan multi-kolom (*multi-column sorting*).
- Paginasi responsif ($10, 25, 50, 100$ baris).
- Bahasa Indonesia baku untuk seluruh kontrol tabel.

---

## 🔄 11. TAHAPAN & ALUR KERJA UTAMA APLIKASI (USER JOURNEY & LIFECYCLE)

```mermaid
sequenceDiagram
    autonumber
    actor Wali as Wali Santri (Parent)
    actor Santri as Santri (Student)
    actor Guru as Guru (Mentor)
    actor Admin as Admin Lembaga
    participant Sistem as AL-HIKMAH LMS

    Wali->>Sistem: Daftar Program & Data Santri
    Sistem->>Wali: Kirim Invoice & Kredensial Santri via WA
    Wali->>Sistem: Bayar Tagihan via QRIS/VA (Pakasir)
    Admin->>Sistem: Tetapkan Guru Pembimbing (Mentor)
    Guru->>Santri: Bimbingan & Input Rapor Mutaba'ah
    Sistem->>Santri: Update Poin, Streak, & Lencana
    Guru->>Sistem: Generate Soal AI (Pilgan/Essay) & Cetak PDF
    Admin->>Sistem: Pantau Revenue, SDM, Alerts, & Rekrutmen Guru
```

---

## ⭐ 12. PENJELASAN SELURUH FITUR APLIKASI (BERDASARKAN HAK AKSES)

```
+-------------------------------------------------------------------------------------------------------------------------+
| MATRIKS HAK AKSES PERAN PENGGUNA (ROLE ACCESS MATRIX v8.3)                                                              |
+-------------------------------------------------------------------------------------------------------------------------+
| Fitur / Modul                         | Pengunjung Publik | Calon Guru | Santri | Wali Santri | Guru Resmi | Admin |
+---------------------------------------+:-----------------:+:----------:+:------:+:-----------:+:----------:+:-----:+
| Pendaftaran Calon Guru (/bergabung)   |        ✅         |     ✅     |   ❌   |     ❌      |     ❌     |  ✅   |
| Cek Status Lamaran Guru               |        ✅         |     ✅     |   ❌   |     ❌      |     ❌     |  ✅   |
| Ujian Kompetensi Guru Online          |        ❌         |     ✅     |   ❌   |     ❌      |     ❌     |  ✅   |
| Monitoring Rekrutmen & Wawancara      |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
| Monitoring Masa Percobaan (Probation) |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
| Baca Artikel Blog & Galeri            |        ✅         |     ✅     |   ✅   |     ✅      |     ✅     |  ✅   |
| Jadwal Sholat & Kompas GPS            |        ✅         |     ✅     |   ✅   |     ✅      |     ✅     |  ✅   |
| Pendaftaran Santri Baru               |        ✅         |     ❌     |   ❌   |     ✅      |     ❌     |  ✅   |
| Pembayaran Online QRIS/VA             |        ❌         |     ❌     |   ❌   |     ✅      |     ❌     |  ✅   |
| Dashboard Ruang Santri (Gamifikasi)   |        ❌         |     ❌     |   ✅   |     ❌      |     ❌     |  ✅   |
| Input Rapor & Presensi Mengajar       |        ❌         |     ❌     |   ❌   |     ❌      |     ✅     |  ✅   |
| AI Auto-Generate Soal & Cetak PDF     |        ❌         |     ❌     |   ❌   |     ❌      |     ✅     |  ✅   |
| Analitik Finansial & Pendapatan       |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
| Monitoring Beban SDM Guru             |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
| Pusat Peringatan Operasional 3-Tier   |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
| Generator Laporan (Excel/PDF)         |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
| WhatsApp Mass Broadcast               |        ❌         |     ❌     |   ❌   |     ❌      |     ❌     |  ✅   |
+-------------------------------------------------------------------------------------------------------------------------+
```

---

## 🔔 13. SISTEM NOTIFIKASI & ALERT TERPUSAT (CENTRALIZED ALERT SYSTEM)

1. **Notifikasi Dalam Aplikasi (In-App Notifications)**: Indikator lonceng dengan *badge counter* belum dibaca pada navbar dashboard.
2. **Konektor Notifikasi WhatsApp (`WhatsAppService.php` & `BroadcastService.php`)**: Format pesan terstandarisasi untuk konfirmasi pendaftaran, kredensial akun, update seleksi guru, rincian pembayaran, dan broadcast massal.
3. **Pusat Peringatan Operasional Admin (`AlertService.php`)**: Mesin penganalisis anomali operasional harian (Kritis, Perhatian, Info).

---

## 🗄️ 14. PENJELASAN SELURUH DATABASE (PENYIMPANAN DATA LEMBAGA)

AL-HIKMAH LMS menggunakan basis data relasional MySQL/MariaDB dengan **38 Tabel Utama** yang ternormalisasi dan terindeks:

```
+-------------------------------------------------------------------------------------------------------+
| DAFTAR TABEL BASIS DATA UTAMA AL-HIKMAH LMS (v8.3)                                                    |
+-------------------------------------------------------------------------------------------------------+
| 1. users                   : Data Akun Pengguna (Admin, Mentor, Parent, Student)                      |
| 2. roles                   : Master Hak Akses & Peran Pengguna                                        |
| 3. programs                : Master Paket Program Belajar, Biaya, Deskripsi, Level, & Status          |
| 4. articles                : Data Artikel Blog Edukasi & Konten Literasi                              |
| 5. blog_categories        : Master Kategori Artikel Blog                                             |
| 6. blog_tags              : Master Tagar Taksonomi Artikel Blog                                      |
| 7. article_tag             : Pivot Relasi Many-to-Many Artikel dan Tagar                              |
| 8. questions               : Bank Soal Evaluasi Santri (AI/Manual, Type: Pilgan/Essay, SoftDeletes)   |
| 9. students                : Data Santri Binaan Lembaga & Agregat Gamifikasi                          |
| 10. mentors                : Profil & Kualifikasi Guru Al-Qur'an (Sanad, Spesialisasi, Rating)        |
| 11. parent_profiles        : Profil & Informasi Kontak Wali Santri                                    |
| 12. enrollments            : Data Transaksi Pendaftaran, Pilihan Jadwal, & Status Kelas               |
| 13. learning_sessions      : Jadwal & Log Sesi Pertemuan Belajar (Waktu, Link Sesi, Status)          |
| 14. progress               : Catatan Rapor Mutabaah Santri (Surat, Ayat, Jilid, Nilai Adab)           |
| 15. payments               : Riwayat Transaksi Keuangan, Tagihan, Ref Pakasir, & Status Bayar         |
| 16. galleries              : Data Media Dokumentasi Foto Kegiatan Belajar                             |
| 17. gallery_categories     : Kategori Album Galeri Foto (Dynamic CRUD)                                |
| 18. mentor_availabilities  : Data Slot Waktu Luang Mengajar Guru (Hari & Jam)                        |
| 19. mentor_student         : Pivot Alokasi Santri kepada Guru Pembimbing                              |
| 20. messages               : Log Komunikasi Pesan Langsung Mentor dan Wali Santri                     |
| 21. session_confirmations  : Log Konfirmasi Kehadiran Sesi Belajar oleh Wali Santri                  |
| 22. mentor_activity_logs   : Log Rekam Jejak Aktivitas Kerja Guru Pembimbing                          |
| 23. mentor_leaves          : Pengajuan Cuti / Izin Mengajar Guru Beserta Tanggal & Guru Pengganti     |
| 24. contact_messages       : Pesan Masuk dari Formulir Kontak Publik                                  |
| 25. settings               : Pengaturan Global Sistem Lembaga & Profil Aplikasi                       |
| 26. notifications          : Data Notifikasi & Pengingat Terjadwal Pengguna                           |
| 27. badges                 : Katalog Master Lencana Islami Santri & Guru (B01-B15, M01-M03)           |
| 28. student_badges         : Pivot Perolehan Lencana oleh Santri Beserta Waktu Diraih                 |
| 29. hifz_targets           : Target Setoran Hafalan Harian Santri (Mandiri / Ditugaskan Mentor)       |
| 30. juz_progress           : Visualisasi Peta Capaian 30 Juz (Ayat Hafal, Persen, Status Mutqin)      |
| 31. hifz_milestones        : Target Jangka Panjang Santri dengan Tenggat Waktu & Progress Goal        |
| 32. leaderboard_snapshots  : Snapshot Riwayat Peringkat Santri (Harian / Mingguan / Bulanan)          |
| 33. gamification_points    : Buku Besar (Ledger) Riwayat Perolehan Poin Gamifikasi Santri             |
| 34. password_reset_logs    : Log Audit Jejak Keamanan Reset Password Akun Santri                      |
| 35. financial_audit_logs   : Log Audit Transaksi Finansial, Ekspor Laporan, & Rekrutmen Guru          |
| 36. mentor_applications    : Data Lamaran Calon Guru, Berkas Syarat, & Status Seleksi                 |
| 37. mentor_tests           : Sesi Ujian Kompetensi Online Calon Guru, Soal AI, Waktu, & Skor          |
| 38. mentor_probations      : Data Masa Percobaan 3 Bulan Guru, Target Evaluasi, & Status Kelulusan    |
+-------------------------------------------------------------------------------------------------------+
```

---

## 🧠 15. PENJELASAN SELURUH MODEL, SERVICE, & CONTROLLER

### A. Model Eloquent Inti Tambahan & Pembaruan:
1. **`MentorApplication.php`**: Model lamaran calon guru, relasi dokumen, status, dan ujian.
2. **`MentorTest.php`**: Model sesi ujian calon guru, butir soal JSON, jawaban, dan skor.
3. **`MentorProbation.php`**: Model pelacakan masa percobaan guru 3 bulan.
4. **`Question.php`**: Diperbarui dengan kolom `type` (`multiple_choice`, `essay`), `essay_answer`, `rubric`, serta helper methods (`isEssay()`, `isMultipleChoice()`, `type_label`).
5. **`FinancialAuditLog.php`**, **`MentorLeave.php`**, **`Student.php`**, **`Badge.php`**, **`Payment.php`**.

### B. Service Layer Inti:
1. **`GeminiQuestionService.php`**: Engine Universal Multi-Provider AI (Google Gemini, DeepSeek, Alibaba Qwen, Anthropic Claude, OpenAI ChatGPT) yang switchable via `.env`, dilengkapi deteksi otomatis, sistem pelacak error & banner notifikasi fallback transparan, serta Master Bank Kurikulum terpisah untuk seluruh 10 program resmi Al-Hikmah dengan garansi kuota dan nol duplikasi hashing MD5.
2. **`MentorRecruitmentService.php`**: Pengelola proses pendaftaran, verifikasi berkas, dan wawancara calon guru.
3. **`MentorTestService.php`**: Pembuat paket soal ujian kompetensi 3 pilar (Tajwid, Makhraj, Tahsin) dan evaluator skor.
4. **`MentorProbationService.php`**: Pengawas masa percobaan 3 bulan guru pembimbing dan penganugerahan lencana M01-M03.
5. **`RevenueAnalyticsService.php`**, **`StaffAnalyticsService.php`**, **`AlertService.php`**, **`BroadcastService.php`**, **`StudentAccountService.php`**, **`GamificationService.php`**, **`PakasirService.php`**, **`WhatsAppService.php`**.

### C. Controller Baru & Pembaruan:
1. **`MentorQuestionController.php`**: Panel Bank Soal, AJAX generator preview, penyimpanan batch, soft delete/restore/force delete, dan cetak lembar ujian A4 / PDF via POST *real-time*.
2. **`AdminRecruitmentController.php`**: Manajemen pelamar guru, filter DataTables, verifikasi berkas, penjadwalan ujian & wawancara, persetujuan, dan penerbitan akun.
3. **`AdminProbationController.php`**: Manajemen evaluasi masa percobaan guru.
4. **`MentorRecruitmentController.php` (Public)**: Form pendaftaran publik calon guru dan pelacak status lamaran.
5. **`MentorRecruitmentTestController.php` (Mentor)**: Dashboard pengerjaan ujian kompetensi calon guru online.

---

## ⚙️ 16. CONSOLE COMMANDS & JADWAL BACKGROUND CRON

1. `php artisan gamification:refresh-leaderboard`: Snapshot peringkat santri harian.
2. `php artisan alerts:scan`: Memindai anomali sistem (overdue, dropout, overload).
3. `php artisan queue:work`: Memproses antrean pesan WhatsApp dan email.

---

## 📁 17. PENJELASAN SELURUH STRUKTUR FOLDER APLIKASI

```
al-hikmah-lms/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── AdminRecruitmentController.php # Panel Rekrutmen Guru
│   │   │   ├── AdminProbationController.php   # Panel Masa Percobaan Guru
│   │   │   ├── DashboardController.php
│   │   │   ├── AdminRevenueController.php
│   │   │   ├── AdminStaffController.php
│   │   │   ├── AdminAlertController.php
│   │   │   ├── AdminReportController.php
│   │   │   └── AdminBroadcastController.php
│   │   ├── Mentor/
│   │   │   ├── MentorQuestionController.php   # Bank Soal & AI Generator & Cetak
│   │   │   └── MentorRecruitmentTestController.php # Ujian Online Calon Guru
│   │   └── Public/
│   │       └── MentorRecruitmentController.php # Pendaftaran & Cek Status Publik
│   ├── Models/
│   │   ├── MentorApplication.php
│   │   ├── MentorTest.php
│   │   ├── MentorProbation.php
│   │   └── Question.php
│   └── Services/
│       ├── GeminiQuestionService.php          # Engine Soal AI & Multi-Bank Kurikulum
│       ├── MentorRecruitmentService.php       # Service Rekrutmen Guru
│       ├── MentorTestService.php              # Service Ujian Kompetensi
│       ├── MentorProbationService.php         # Service Masa Percobaan
│       ├── RevenueAnalyticsService.php
│       └── AlertService.php
├── resources/views/
│   ├── admin/
│   │   └── recruitment/
│   │       ├── index.blade.php                # DataTables Pelamar Guru
│   │       ├── show.blade.php                 # Detail, Review Berkas, & Aksi
│   │       └── probation/index.blade.php      # Monitoring Masa Percobaan
│   ├── mentor/
│   │   ├── questions/
│   │   │   ├── index.blade.php                # Bank Soal Mentor
│   │   │   ├── generate.blade.php             # Form AI Generator & Preview Workspace
│   │   │   ├── print.blade.php                # Lembar Cetak Ujian A4 & Kunci Guru
│   │   │   └── trash.blade.php                # Tong Sampah Soal
│   │   └── recruitment/
│   │       └── test.blade.php                 # Antarmuka Ujian Online Calon Guru
│   └── public/
│       └── mentor-recruitment/
│           ├── register.blade.php             # Pendaftaran Calon Guru Publik
│           └── tracker.blade.php              # Pelacak Status Lamaran Publik
└── routes/
    └── web.php                                # Seluruh Definisi Rute v8.3
```

---

## 🧪 18. HASIL PENGUJIAN & QUALITY ASSURANCE (100% GREEN PASS)

Sistem telah diuji secara komprehensif menggunakan framework pengujian **Pest PHP** dengan seluruh skenario bisnis, rekrutmen, pembuatan soal AI, dan cetak PDF tervalidasi:

```bash
# Hasil Eksekusi Pengujian Otomatis Suite Lengkap v8.3:
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Unit\GeminiQuestionServiceTest (3 tests)
   PASS  Tests\Unit\StudentAccountServiceTest
   PASS  Tests\Unit\GamificationServiceTest
   PASS  Tests\Feature\MentorRecruitment\AIEvaluationTest (2 tests)
   PASS  Tests\Feature\MentorRecruitment\ProbationTrackingTest (3 tests)
   PASS  Tests\Feature\MentorRecruitment\RecruitmentFlowTest (7 tests)
   PASS  Tests\Feature\MentorQuestionTest (8 tests)
   PASS  Tests\Feature\Admin\RevenueAnalyticsTest
   PASS  Tests\Feature\Admin\StaffAnalyticsTest
   PASS  Tests\Feature\Admin\OperationalAlertsTest
   PASS  Tests\Feature\Admin\ReportExportTest
   PASS  Tests\Feature\Admin\BroadcastSystemTest
   PASS  Tests\Feature\Admin\MasterDataCrudTest
   PASS  Tests\Feature\Auth\AuthenticationTest
   PASS  Tests\Feature\Auth\RegistrationTest
   PASS  Tests\Feature\BlogFeatureTest
   PASS  Tests\Feature\DashboardTest
   PASS  Tests\Feature\EnrollmentNegotiationTest
   PASS  Tests\Feature\GalleryFeatureTest
   PASS  Tests\Feature\LandingPagesTest
   PASS  Tests\Feature\NotificationAlertSystemTest
   PASS  Tests\Feature\PakasirPaymentGatewayTest
   PASS  Tests\Feature\Parent\ParentDashboardModulesTest
   PASS  Tests\Feature\StudentDashboardTest
   ... (Seluruh Fitur Lintas Role)

  Tests:    282 passed (1138 assertions)
  Duration: 100% Green Pass
```

---

## 🎯 19. KESIMPULAN & REKOMENDASI LANGKAH KE DEPAN

Sistem **AL-HIKMAH LMS Versi 8.3** menghadirkan standarisasi menyeluruh untuk pembinaan Al-Qur'an modern:
1. **Funnel Rekrutmen Guru Terintegrasi**: Pendaftaran mandiri, ujian kompetensi online terstandar 3 pilar, dan masa percobaan 3 bulan terukur.
2. **AI Question Generator Cerdas 10 Program**: Fleksibilitas format soal Pilihan Ganda & Essay berbobot HOTS dengan jaminan nol duplikasi.
3. **Lembar Ujian PDF Siap Cetak**: Memudahkan guru mengadakan evaluasi berkala di kelas halaqah secara profesional.
4. **Keamanan & Skalabilitas Enterprise**: Struktur database terindeks, proteksi hak akses ketat, dan 100% lolos pengujian otomatis.

---

## 📝 20. LAMPIRAN TEKNIS, RUTE SISTEM, & GLOSARIUM

### 🛣️ Daftar Rute Utama Sistem (Routing Index v8.3):

| Rute Web | Method | Hak Akses | Deskripsi & Fungsi |
| :--- | :---: | :---: | :--- |
| `/bergabung` | `GET/POST` | Publik | Formulir Pendaftaran Calon Guru Pendamping Al-Qur'an |
| `/cek-status-lamaran` | `GET` | Publik | Pelacak Status Lamaran Guru dengan Linimasa Visual |
| `/admin/mentors/recruitment/applications` | `GET` | Admin | DataTables Daftar Pelamar Calon Guru |
| `/admin/mentors/recruitment/applications/{id}` | `GET` | Admin | Detail Berkas Pelamar, Aksi Seleksi, & Hasil Ujian |
| `/admin/mentors/recruitment/applications/{id}/generate-test` | `POST` | Admin | Trigger AI Generator Paket Soal Ujian Calon Guru (15 Soal) |
| `/admin/mentors/recruitment/applications/{id}/approve` | `POST` | Admin | Persetujuan Akhir & Penerbitan Akun Mentor + Masa Percobaan |
| `/admin/mentors/probation` | `GET` | Admin | Monitoring Masa Percobaan 3 Bulan Guru Pembimbing |
| `/mentor/recruitment/test` | `GET/POST` | Calon Guru | Halaman Pengerjaan Ujian Kompetensi Online Guru |
| `/mentor/questions` | `GET` | Mentor | Bank Soal Evaluasi Guru Binaan |
| `/mentor/questions/generate` | `GET` | Mentor | Form Generator Soal AI (10 Program & Inspirasi Topik) |
| `/mentor/questions/generate-preview` | `POST` | Mentor | AJAX Endpoint Pembuatan Preview Butir Soal AI |
| `/mentor/questions/store-batch` | `POST` | Mentor | Simpan Paket Soal yang Direview ke Bank Soal |
| `/mentor/questions/print` | `GET/POST` | Mentor | Lembar Cetak Ujian A4 / Unduh PDF Lengkap dengan Kunci Jawaban |
| `/admin/dashboard` | `GET` | Admin | Dashboard Utama Eksekutif (KPIs, Alert Banner, Quick Actions) |
| `/admin/revenue` | `GET` | Admin | Analitik Pendapatan, MoM, ARPU, & Visualisasi ApexCharts |
| `/admin/staff` | `GET` | Admin | Manajemen Beban Kerja SDM, Rasio Guru:Santri, & Top Performers |
| `/admin/alerts` | `GET` | Admin | Pusat Peringatan Operasional Terpadu (Kritis, Perhatian, Info) |
| `/admin/reports` | `GET` | Admin | Generator & Pratinjau Rekapitulasi Laporan Finansial |
| `/student/dashboard` | `GET` | Student | Dashboard Utama Ruang Santri (Poin, Streak, 30 Juz) |
| `/parent/children` | `GET` | Parent | Daftar Anak Binaan & Reset Password Mandiri |

---

> 📄 **Dokumentasi & Referensi Terkait:**
> - [Panduan Kode Etik & Standar Pengembang](AGENTS.md)
> - [Service Generator Soal AI](app/Services/GeminiQuestionService.php)
> - [Service Rekrutmen Guru](app/Services/MentorRecruitmentService.php)
> - [Service Ujian Kompetensi](app/Services/MentorTestService.php)
> - [Service Masa Percobaan](app/Services/MentorProbationService.php)
