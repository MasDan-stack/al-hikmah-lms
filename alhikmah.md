AL-HIKMAH — Platform Manajemen Belajar Al-Qur'an

1. EXECUTIVE SUMMARY

Nama Proyek : AL-HIKMAH Learning Management System
Visi : Menjadi platform pendampingan belajar Al-Qur'an yang personal, berbasis nilai, dan mudah diakses keluarga Indonesia
Masalah : Keluarga kesulitan menemukan pendampingan Al-Qur'an yang konsisten, personal, dan sesuai adab
Solusi : Platform manajemen murid, pendamping, jadwal, dan progres belajar dengan pendekatan humanis
Target User : Orang tua, murid (usia 10-15 tahun), pendamping/guru, dan admin
Tech Stack : Laravel 12, Livewire 4.3, Flux UI, Laravel Boost, MySQL, Bootstrap 5

2. USER PERSONA
👨‍👩‍👦 Orang Tua/Wali
Goal: Mendaftarkan anak, memantau progres, berkomunikasi dengan pendamping

Pain Point: Sulit cari pendamping terpercaya, tidak tahu perkembangan anak

Frekuensi: 1-2x/minggu

👦 Murid (10-15 tahun)
Goal: Belajar dengan nyaman, melihat pencapaian, mendapat motivasi

Pain Point: Bosan, sulit memahami, malu bertanya

Frekuensi: Setiap sesi belajar

👨‍🏫 Pendamping/Guru
Goal: Mengelola jadwal, mencatat progres, memberi laporan ke orang tua

Pain Point: Admin manual, laporan berantakan

Frekuensi: Harian

🛠️ Admin
Goal: Mengelola semua data (user, program, pembayaran, laporan)

Pain Point: Data tersebar, tidak terintegrasi

Frekuensi: Harian

3. FUNCTIONAL REQUIREMENTS (Prioritized)
🔴 P1 — WAJIB ADA (MVP)
ID	Fitur	Deskripsi	UI Komponen
F1	Manajemen Murid	CRUD data murid (nama, usia, lokasi, kontak)	flux:table + flux:modal
F2	Manajemen Pendamping	CRUD pendamping (spesialisasi, jadwal, rating)	flux:table + flux:select
F3	Pendaftaran Online	Form pendaftaran dengan validasi & notifikasi WA	flux:input, flux:button
F4	Penjadwalan Sesi	Kalender sesi belajar (Online/Offline/Hybrid)	flux:calendar (custom)
F5	Progres Belajar	Catatan pertemuan, capaian, dan target	flux:card, flux:progress
F6	Dashboard Admin	Statistik: total murid, pendamping, sesi aktif	flux:chart (custom)
F7	Autentikasi	Login/Register (Admin, Pendamping, Orang Tua)	Laravel Breeze + Livewire
🟡 P2 — PENTING
ID	Fitur	Deskripsi
F8	Laporan Otomatis	Generate PDF laporan progres per murid per bulan
F9	Notifikasi WA	Pengingat jadwal, konfirmasi sesi via WhatsApp API
F10	Manajemen Program	CRUD program (Tahsin, Tahfidz, Iqra, dll)
F11	Rating Pendamping	Orang tua bisa memberi rating setelah sesi
F12	Galeri Aktivitas	Upload foto kegiatan belajar
🟢 P3 — NICE TO HAVE
ID	Fitur	Deskripsi
F13	Payment Gateway	Integrasi pembayaran (Midtrans/Xendit)
F14	Forum Diskusi	Tanya jawab antara orang tua & pendamping
F15	Modul Ujian	Evaluasi bulanan dengan soal interaktif
F16	Mobile Responsive PWA	Installable web app
4. NON-FUNCTIONAL REQUIREMENTS
Kategori	Spesifikasi
Performance	Load time < 2 detik, support 100 user concurrent
Security	Role-based access (Admin, Pendamping, Orang Tua), CSRF, XSS protection
Responsive	Mobile-first (Bootstrap 5)
SEO	Meta tags, sitemap, struktur URL friendly
Accessibility	WCAG 2.1 minimal (ARIA labels, contrast)
Scalability	Bisa scale ke 1000+ murid

5. TECH STACK MAPPING

FRONTEND (UI)
Bootstrap 5 (layout) + Flux UI (komponen interaktif)
Livewire  (reactivity)

BACKEND (Laravel)
Laravel 12 + Eloquent ORM
Laravel Boost (CRUD scaffolding)
Laravel Breeze (authentication)

DATABASE (MySQL)
users, roles, students, mentors, sessions,
programs, progress, payments, galleries

6. DATABASE SCHEMA (DRAFT)
-- Core Tables
users (id, name, email, password, role_id, phone, avatar)
roles (id, name) -- admin, mentor, parent, student

students (id, user_id, full_name, age, location, parent_phone, program_id)
mentors (id, user_id, full_name, specialization, bio, rating)

programs (id, name, description, duration, price, level)
sessions (id, student_id, mentor_id, program_id, date, time, method, status)

progress (id, session_id, material, notes, score, attendance)
payments (id, student_id, amount, status, payment_date, invoice)

galleries (id, title, image_url, description, uploaded_by)
notifications (id, user_id, message, is_read, type)

-- Many-to-Many
student_program (student_id, program_id, enrolled_at)
mentor_program (mentor_id, program_id)

7. USER FLOW (MVP)
┌─────────────────────────────────────────────────────────────┐
│                    AL-HIKMAH USER FLOW                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [LANDING PAGE]                                            │
│       │                                                    │
│       ▼                                                    │
│  ┌─────────────┐    ┌──────────────┐    ┌──────────────┐  │
│  │  Daftar/Mulai│    │  Login Admin │    │ Login Mentor │  │
│  └─────────────┘    └──────────────┘    └──────────────┘  │
│       │                    │                    │          │
│       ▼                    ▼                    ▼          │
│  [Form Pendaftaran]  [Dashboard Admin]  [Dashboard Mentor]│
│   - Nama              - Total Murid      - Jadwal Hari Ini│
│   - WA                - Mentor Aktif     - Catatan Progres│
│   - Usia              - Sesi Hari Ini   - Kirim Laporan  │
│   - Program           - CRUD Semua      - Rating         │
│   - Metode            - Laporan PDF                       │
│                                                             │
└─────────────────────────────────────────────────────────────┘

8. MILESTONES & TIMELINE (12 Minggu)
Minggu	Fokus	Output
1-2	Setup & Database	Laravel + Boost + Livewire + Flux terinstall, schema siap
3-4	Auth & Role	Login/Register 3 role, middleware
5-6	CRUD Core (Boost)	Manajemen Murid, Pendamping, Program
7-8	Penjadwalan & Progres	Livewire komponen jadwal, catatan progres
9-10	Dashboard & Laporan	Admin dashboard, PDF report
11-12	UI Polish & Deployment	Flux UI final, deploy ke production
9. FITUR LIVEwire + FLUX + BOOST SPESIFIK
🔹 Laravel Boost akan digunakan untuk:
php artisan boost:crud Student → Generate CRUD murid

php artisan boost:crud Mentor → Generate CRUD pendamping

php artisan boost:crud Program → Generate CRUD program

php artisan boost:crud Session → Generate CRUD sesi

🔹 Livewire Komponen:
DashboardStats → Statistik real-time

SessionCalendar → Kalender interaktif

ProgressTracker → Tracking belajar per murid

RegistrationForm → Form multi-step pendaftaran

🔹 Flux UI Components:
flux:table → Tabel murid/mentor dengan search & sort

flux:modal → Form CRUD popup

flux:input, flux:select → Form elemen

flux:navbar, flux:sidebar → Layout admin

10. RISK & MITIGASI
Risk	Mitigasi
Livewire learning curve	Mulai dengan komponen sederhana dulu
Performance dengan data besar	Gunakan pagination & lazy loading
Keamanan data anak	Enkripsi data sensitif, strict role
WhatsApp integration	Gunakan WA Gateway API (WAPi)
✅ NEXT STEP: Eksekusi
