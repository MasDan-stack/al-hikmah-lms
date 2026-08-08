# AL-HIKMAH — Platform Manajemen Belajar Al-Qur'an

> **Dokumen Spesifikasi Proyek & System Requirement Specification (SRS)**  
> **Versi:** 1.1 (Enhanced & Finalized)  
> **Tanggal Update:** 8 Agustus 2026  
> **Status:** Approved Draft & GitHub Issues Mapped  

---

## 1. EXECUTIVE SUMMARY

- **Nama Proyek:** AL-HIKMAH Learning Management System (LMS)
- **Visi:** Menjadi platform pendampingan belajar Al-Qur'an yang personal, berbasis nilai, dan mudah diakses keluarga Indonesia.
- **Masalah:** Keluarga kesulitan menemukan pendampingan Al-Qur'an yang konsisten, personal, dan terstruktur sesuai adab.
- **Solusi:** Platform manajemen murid, pendamping, jadwal, dan progres belajar berbasis web dengan pendekatan humanis & edukatif.
- **Target User:** Orang Tua/Wali, Murid (usia 10-15 tahun), Pendamping/Guru, dan Admin.
- **Tech Stack Utama:** Laravel 12, Livewire 4.3, Flux UI, Laravel Boost, MySQL, Tailwind CSS / Bootstrap 5.

---

## 2. USER PERSONA & ACCESS CONTROL MATRIX

| User Role | Goal Utama | Main Pain Point | Frekuensi Penggunaan | Hak Akses Utama |
|---|---|---|---|---|
| 👨‍👩‍👦 **Orang Tua / Wali** | Mendaftarkan anak, memantau progres hafalan/bacaan, menerima laporan bulanan. | Sulit cari pendamping terpercaya, tidak tahu detail perkembangan anak. | 1-2x / minggu | View Progres Anak, Pengajuan Sesi, Rating Pendamping, Download Laporan PDF |
| 👦 **Murid (10-15 Thn)** | Belajar dengan nyaman, melihat capaian target hafalan, mendapat motivasi. | Merasa bosan, malu bertanya, kesulitan melacak target hafalan. | Setiap Sesi Belajar | View Personal Dashboard, Target Hafalan, Jadwal Sesi |
| 👨‍🏫 **Pendamping / Guru** | Mengelola jadwal mengajar, menginput progres bacaan/hafalan, memberi catatan adab. | Admin manual merepotkan, catatan progres berantakan. | Harian | Input Sesi & Progres, Manajamen Jadwal, Catatan Perkembangan |
| 🛠️ **Admin** | Mengelola semua master data (User, Sesi, Program, Pembayaran, Reports). | Data tercecer di berbagai media (WA/Excel), tidak terintegrasi. | Harian | Full Access (CRUD Master Data, System Settings, Global Reports) |

---

## 3. FUNCTIONAL REQUIREMENTS (PRIORITIZED)

### 🔴 P1 — WAJIB ADA (MVP - Issue #1 s/d #5)
| ID | Fitur | Deskripsi | Komponen UI / Tech |
|---|---|---|---|
| **F1** | **Manajemen Murid** | CRUD data murid (nama, usia, lokasi, kontak, relasi ke akun Orang Tua) | `flux:table` + `flux:modal` |
| **F2** | **Manajemen Pendamping** | CRUD pendamping (spesialisasi tajwid/tahfidz, bio, jadwal aktif, rating) | `flux:table` + `flux:select` |
| **F3** | **Pendaftaran Online** | Form pendaftaran murid/program baru dengan validasi data | `flux:input` + `flux:button` |
| **F4** | **Penjadwalan Sesi** | Kalender sesi belajar (Online / Offline / Hybrid) dengan status sesi | `SessionCalendar` (Livewire) |
| **F5** | **Progres Belajar Qur'an** | Catatan per sesi: Surah, Ayat, Juz, Nilai Tajwid, Catatan Adab & Evaluasi | `ProgressTracker` (Livewire) |
| **F6** | **Dashboard Analytics** | Ringkasan statistik real-time: Total Murid, Pendamping, Sesi Aktif, Overview Progres | `DashboardStats` (Livewire) |
| **F7** | **Autentikasi & RBAC** | Login/Register & Middleware Role-based access control (4 Roles) | Laravel Breeze + Middleware |

### 🟡 P2 — PENTING (Issue #6 & #7)
| ID | Fitur | Deskripsi | Tech Stack |
|---|---|---|---|
| **F8** | **Export Laporan PDF** | Generate PDF laporan perkembangan harian/bulanan per murid | Dompdf / Laravel Snappy |
| **F9** | **Integrasi WA Gateway** | Pengingat otomatis sesi H-1 / 1 jam sebelum & pengiriman ringkasan progres | WhatsApp API (WAPi / Fonnte) + Queue Jobs |
| **F10** | **Manajemen Program** | CRUD kategori program (Tahsin, Tahfidz, Iqra, Ghorib, Tajwid) | `flux:table` + `flux:modal` |
| **F11** | **Rating & Ulasan** | Orang tua dapat memberikan rating & feedback untuk pendamping setelah sesi selesai | `flux:rating` / Custom Component |

### 🟢 P3 — NICE TO HAVE (Issue #8)
| ID | Fitur | Deskripsi | Tech Stack |
|---|---|---|---|
| **F12** | **Payment Gateway** | Integrasi pembayaran SPP & pendaftaran program | Midtrans / Xendit API |
| **F13** | **Galeri Aktivitas** | Upload & dokumentasi foto kegiatan belajar santri | `galleries` Module |
| **F14** | **Mobile Responsive PWA** | Web app yang dapat di-install di perangkat mobile | PWA Manifest & Service Worker |

---

## 4. NON-FUNCTIONAL REQUIREMENTS

| Kategori | Spesifikasi & Target Metric |
|---|---|
| **Performance** | Page Load Time < 2 detik, mendukung 100+ concurrent live sessions. |
| **Security** | Role-based access control (RBAC), CSRF protection, XSS filtering, Enkripsi data sensitif anak. |
| **Responsiveness** | Mobile-First design, kompatibel penuh di Smartphone, Tablet, dan Desktop. |
| **SEO & Meta** | Semantic HTML5, OpenGraph meta tags, URL friendly slug. |
| **Accessibility** | Standar WCAG 2.1 AA (Contrast ratio memadai, keyboard navigable, ARIA labels). |
| **Scalability** | Arsitektur database siap untuk scaling hingga 1,000+ murid dan puluhan ribu record progres. |

---

## 5. TECH STACK MAPPING

```
┌─────────────────────────────────────────────────────────────┐
│                    AL-HIKMAH TECH STACK                     │
├─────────────────────────────────────────────────────────────┤
│  FRONTEND (UI & Reactivity)                                 │
│  - Livewire 4.3 (Reactive Server-Driven Components)         │
│  - Flux UI (Interactive UI Component Library)               │
│  - Tailwind CSS / Bootstrap 5 (Layout & Styling)            │
│                                                             │
│  BACKEND & CORE                                             │
│  - Laravel 12 Framework                                     │
│  - Eloquent ORM + Database Migrations                       │
│  - Laravel Boost 2.2 (Scaffolding & Guidelines)             │
│  - Laravel Breeze (Auth Controllers & Scaffolding)          │
│                                                             │
│  DATABASE & SERVICES                                        │
│  - MySQL 8.0 / MariaDB                                      │
│  - WhatsApp API Gateway (Queue Job Notification)            │
│  - Dompdf / Snappy (PDF Generation)                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 6. ENHANCED DATABASE SCHEMA

```sql
-- Core User & Role Tables
roles (id, name, created_at, updated_at); 
-- Values: 'admin', 'mentor', 'parent', 'student'

users (
    id, name, email, password, role_id, phone, avatar, 
    email_verified_at, created_at, updated_at
);

-- Profiles & Relations
parents (id, user_id, address, emergency_phone, created_at, updated_at);

students (
    id, user_id, parent_id, full_name, age, gender, location, 
    notes, created_at, updated_at
);

mentors (
    id, user_id, full_name, specialization, bio, rating, 
    is_active, created_at, updated_at
);

-- Programs & Enrollments
programs (
    id, name, description, duration_weeks, price, level, 
    created_at, updated_at
);

student_program (
    id, student_id, program_id, status, enrolled_at, 
    created_at, updated_at
);

-- Sessions & Calendar
sessions (
    id, student_id, mentor_id, program_id, session_date, 
    start_time, end_time, method, status, meeting_link, 
    created_at, updated_at
);
-- status enum: 'scheduled', 'completed', 'rescheduled', 'cancelled', 'absent'
-- method enum: 'online', 'offline', 'hybrid'

-- Qur'an Progress Tracking (Domain Specific)
progress (
    id, session_id, student_id, mentor_id,
    kategori, surah_start, surah_end, ayat_start, ayat_end, juz,
    nilai_fluent, nilai_tajwid, nilai_adab,
    catatan_evaluasi, homework, created_at, updated_at
);

-- Payments & Invoicing
payments (
    id, student_id, program_id, amount, invoice_number, 
    status, payment_date, payment_method, gateway_response, 
    created_at, updated_at
);

-- Media & Notifications
galleries (id, title, image_url, description, uploaded_by, created_at);
notifications (id, user_id, title, message, type, is_read, created_at);
```

---

## 7. USER FLOW (MVP)

```
┌───────────────────────────────────────────────────────────────────┐
│                       AL-HIKMAH USER FLOW                         │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│                     ┌──────────────────────┐                      │
│                     │     LANDING PAGE     │                      │
│                     └──────────┬───────────┘                      │
│                                │                                  │
│         ┌──────────────────────┼──────────────────────┐           │
│         ▼                      ▼                      ▼           │
│  ┌─────────────┐        ┌──────────────┐       ┌──────────────┐   │
│  │ Form Daftar │        │ Login Admin  │       │ Login Mentor │   │
│  └──────┬──────┘        └──────┬───────┘       └──────┬───────┘   │
│         │                      │                      │           │
│         ▼                      ▼                      ▼           │
│  [Confirm WA &]         [Dashboard Admin]      [Dashboard Mentor] │
│  [Parent Portal]        - Total Murid          - Sesi Hari Ini    │
│  - Monitoring       - Mentor Aktif         - Input Progres    │
│  - Laporan PDF      - Sesi & Schedule      - Catatan Surah    │
│  - Rating Mentor    - CRUD Master Data     - Rating Summary   │
│                                                                   │
└───────────────────────────────────────────────────────────────────┘
```

---

## 8. MILESTONES & GITHUB ISSUES MAPPING (12 MINGGU)

| Minggu | Fokus Utama | Target Output | Mapped GitHub Issue |
|---|---|---|---|
| **1-2** | Setup & Database Architecture | Schema DB, Migrations, Seeders, Auth & Middleware | [Issue #1](https://github.com/MasDan-stack/al-hikmah-lms/issues/1) |
| **3-4** | Core Master Data Management | CRUD Program, Murid & Pendamping via Flux UI | [Issue #2](https://github.com/MasDan-stack/al-hikmah-lms/issues/2) |
| **5-6** | Penjadwalan Sesi Belajar | Komponen `SessionCalendar`, Booking & Status Sesi | [Issue #3](https://github.com/MasDan-stack/al-hikmah-lms/issues/3) |
| **7-8** | Tracking Progres Al-Qur'an | Module `ProgressTracker` (Surah, Ayat, Tajwid, Evaluasi) | [Issue #4](https://github.com/MasDan-stack/al-hikmah-lms/issues/4) |
| **9-10** | Dashboard & Reporting | Dashboard Real-time Stats, Export Laporan PDF, WA Gateway | [Issue #5](https://github.com/MasDan-stack/al-hikmah-lms/issues/5), [Issue #6](https://github.com/MasDan-stack/al-hikmah-lms/issues/6), [Issue #7](https://github.com/MasDan-stack/al-hikmah-lms/issues/7) |
| **11-12** | Payment & Final Deployment | Integration Payment Gateway, Galeri, QA & Deployment | [Issue #8](https://github.com/MasDan-stack/al-hikmah-lms/issues/8) |

---

## 9. IMPLEMENTASI TOOLKIT (BOOST + LIVEWIRE + FLUX UI)

### 🔹 Command Scaffolding (Laravel Boost)
```bash
php artisan boost:crud Program
php artisan boost:crud Student
php artisan boost:crud Mentor
php artisan boost:crud Session
php artisan boost:crud Progress
```

### 🔹 Livewire Reactive Components
- `DashboardStats`: Menampilkan summary angka real-time di dashboard Admin/Mentor.
- `SessionCalendar`: Tampilan kalender interaktif untuk manajemen sesi belajar.
- `ProgressTracker`: Component pencatatan capaian hafalan/bacaan murid.
- `RegistrationForm`: Multi-step form pendaftaran santri baru.

### 🔹 Flux UI Components
- `<flux:table>`: Tabel data murid & mentor lengkap dengan fitur search, filter, dan sorting.
- `<flux:modal>`: Dialog popup untuk form tambah/edit data.
- `<flux:input>`, `<flux:select>`, `<flux:textarea>`: Form kontrol terstandarisasi.
- `<flux:navbar>`, `<flux:sidebar>`: Layout navigasi utama dashboard.

---

## 10. RISK MANAGEMENT & MITIGASI

| Identifikasi Risiko | Tingkat Dampak | Strategi Mitigasi |
|---|---|---|
| **Kurva Pembelajaran Livewire 4 & Flux UI** | Sedang | Mulai dari komponen sederhana, gunakan standar komponen Flux UI. |
| **Performa Query pada Data Progres Besar** | Tinggi | Terapkan Eager Loading, Indexing kolom `student_id` & `session_id`, serta Pagination. |
| **Keamanan Data Pribadi Anak/Santri** | Tinggi | Terapkan Enkripsi pada field sensitif, Strict RBAC Middleware, dan validasi data ketat. |
| **Kegagalan Pengiriman Pesan WA Gateway** | Sedang | Gunakan Laravel Queue Jobs & Database Fallback Log untuk retry otomatis. |

---

✅ **Status Dokumentasi:** *Updated & Synced dengan GitHub Repository Issues.*
