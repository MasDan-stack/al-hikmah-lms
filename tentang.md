# 🕌 LAPORAN EKSEKUTIF PROYEK & PANDUAN APLIKASI: AL-HIKMAH LMS

> **Dokumen Resmi untuk Manajemen, Pimpinan Lembaga, & Tim Pengembang**  
> **Nama Sistem:** AL-HIKMAH Learning Management System (LMS)  
> **Status Aplikasi:** ✅ **100% Selesai, Teruji, & Siap Digunakan (Production Ready)**  
> **Versi:** 10.9 (Consolidated Product Foundations, Strategic Marketing Conversion Architecture, High-Empathy Parent Hooks, Value Comparison & Reframing Matrix, Membership Pass Elevation, Subpage Fixed-Navbar Safe Spacing)  
> **Tanggal Pembaruan:** 17 September 2026  

---

## 📋 DAFTAR ISI LAPORAN

1. [📌 1. Ringkasan Eksekutif, Fondasi Produk & Nilai Manfaat Aplikasi](#-1-ringkasan-eksekutif-fondasi-produk--nilai-manfaat-aplikasi)
   - [1.1 Platform, Tech Stack, & 4 Persona Pengguna Inti](#11-platform-tech-stack--4-persona-pengguna-inti)
   - [1.2 Positioning Produk, Brand Commitments & 4 Prinsip Desain](#12-positioning-produk-brand-commitments--4-prinsip-desain)
   - [1.3 Kapabilitas Inti & Batasan Sistem (Capabilities & Constraints)](#13-kapabilitas-inti--batasan-sistem-capabilities--constraints)
   - [1.4 Metrik Bisnis & Dampak Operasional](#14-metrik-bisnis--dampak-operasional)
   - [1.5 Konsolidasi Dokumen Rekayasa & PRD ke dalam Single Source of Truth](#15-konsolidasi-dokumen-rekayasa--prd-ke-dalam-single-source-of-truth)
2. [🎓 2. Modul Rekrutmen, Ujian Kompetensi, & Masa Percobaan Guru](#-2-modul-rekrutmen-ujian-kompetensi--masa-percobaan-guru)
   - [2.1 Alur Seleksi & Kategori Ujian Kompetensi](#21-alur-seleksi--kategori-ujian-kompetensi)
   - [2.2 Isolasi Hak Akses Dashboard (Gating Lifecycle)](#22-isolasi-hak-akses-dashboard-gating-lifecycle)
   - [2.3 Fitur Manajemen Cuti & Guru Pengganti (Mentor Leave & Substitute)](#23-fitur-manajemen-cuti--guru-pengganti-mentor-leave--substitute)
   - [2.4 ATS Pipeline 7-Tahapan & 1-Klik Verifikasi Berkas (Paket 15 Soal Seleksi AI & Notifikasi WhatsApp)](#24-ats-pipeline-7-tahapan--1-klik-verifikasi-berkas-paket-15-soal-seleksi-ai--notifikasi-whatsapp)
   - [2.5 Revitalisasi Modul Masa Percobaan (Live Probation Hub, 4 Pilar Orientasi, & Lencana M01)](#25-revitalisasi-modul-masa-percobaan-live-probation-hub-4-pilar-orientasi--lencana-m01)
   - [2.6 Cron Sinkronisasi Harian Masa Percobaan & Peringatan Otomatis WhatsApp H-14](#26-cron-sinkronisasi-harian-masa-percobaan--peringatan-otomatis-whatsapp-h-14)
   - [2.7 Detail Profil Lengkap Guru, Verifikasi Rekening Bank & Student Hand-Over Wizard](#27-detail-profil-lengkap-guru-adminstaffid-verifikasi-rekening-bank--student-hand-over-wizard)
   - [2.8 Sistem Pendukung Keputusan (SPK) Pemilihan Guru Teladan Berbasis Analytical Hierarchy Process (AHP Saaty)](#28-sistem-pendukung-keputusan-spk-pemilihan-guru-teladan-berbasis-analytical-hierarchy-process-ahp-saaty)
3. [🤖 3. Modul AI Auto-Generate Soal, Bank Soal, & Lembar Ujian PDF](#-3-modul-ai-auto-generate-soal-bank-soal--lembar-ujian-pdf)
   - [3.1 Format Lembar Ujian Siap Cetak A4 & Kunci Jawaban Guru](#31-format-lembar-ujian-siap-cetak-a4--kunci-jawaban-guru)
   - [3.2 Arsitektur Universal Multi-Provider AI & UI Selector](#32-arsitektur-universal-multi-provider-ai--ui-selector)
   - [3.3 Smart Cascade Failover Engine (Zero Downtime)](#33-smart-cascade-failover-engine-zero-downtime)
   - [3.4 Bank Kurikulum Offline Al-Hikmah (Fallback Engine)](#34-bank-kurikulum-offline-al-hikmah-fallback-engine)
4. [⏰ 4. Modul Ketersediaan Mengajar Angka Slot (0–6) & Kebijakan Anti-Bentrok 1-on-1](#-4-modul-ketersediaan-mengajar-angka-slot-06--kebijakan-anti-bentrok-1-on-1)
   - [4.1 Standarisasi Master Angka Slot Waktu (0–6)](#41-standarisasi-master-angka-slot-waktu-06)
   - [4.2 Smart WhatsApp Text Importer & Exporter Jadwal](#42-smart-whatsapp-text-importer--exporter-jadwal)
   - [4.3 Fitur Hari Bebas (Libur Rutin) Mentor & Day-Off Monitoring Center](#43-fitur-hari-bebas-libur-rutin-mentor--day-off-monitoring-center)
   - [4.4 Prinsip Bimbingan Privat 1-on-1 & Proteksi Anti-Bentrok (Zero Double-Booking)](#44-prinsip-bimbingan-privat-1-on-1--proteksi-anti-bentrok-zero-double-booking)
   - [4.5 Alur Penugasan Admin (OPSI A: Setujui vs OPSI B: Tawarkan Alternatif)](#45-alur-penugasan-admin-opsi-a-setujui-vs-opsi-b-tawarkan-alternatif)
5. [🧮 5. Modul Smart Matchmaking v3.0, External Calendar Sync, & Dynamic Load Balancing](#-5-modul-smart-matchmaking-v30-external-calendar-sync--dynamic-load-balancing)
   - [5.1 Formula Multi-Kriteria 6 Dimensi v3.0 Berbobot](#51-formula-multi-kriteria-6-dimensi-v30-berbobot)
   - [5.2 Integrasi Kalender Eksternal (Google Calendar OAuth & Catatan Kesiapan API)](#52-integrasi-kalender-eksternal-google-calendar-oauth--catatan-kesiapan-api)
   - [5.3 Smart Load Balancing & Proteksi Kelelahan Guru (Burnout Prevention)](#53-smart-load-balancing--proteksi-kelelahan-guru-burnout-prevention)
   - [5.4 Vektor Keselarasan Gaya Belajar Santri & Profil Pedagogis Guru (Cosine Similarity)](#54-vektor-keselarasan-gaya-belajar-santri--profil-pedagogis-guru-cosine-similarity)
   - [5.5 Aturan Syariat Gender Berbasis Usia 10 Tahun](#55-aturan-syariat-gender-berbasis-usia-10-tahun)
   - [5.6 Explainable AI (Why Not...? Tooltip Inspection)](#56-explainable-ai-why-not-tooltip-inspection)
   - [5.7 Family Blacklist Engine & Auto-Assign $\ge 95\%$](#57-family-blacklist-engine--auto-assign-ge-95)
6. [🔮 6. Predictive Analytics & Early Warning System (PA-EWS)](#-6-predictive-analytics--early-warning-system-pa-ews)
   - [6.1 Model 1: Dropout & Churn Risk Prediction (4-Factor Ensemble)](#61-model-1-dropout--churn-risk-prediction-4-factor-ensemble)
   - [6.2 Model 2: Learning Velocity & Proyeksi Tanggal Khatam (ETA)](#62-model-2-learning-velocity--proyeksi-tanggal-khatam-eta)
   - [6.3 Model 3: Revenue Forecasting 6 Bulan dengan Pengali Musiman](#63-model-3-revenue-forecasting-6-bulan-dengan-pengali-musiman)
   - [6.4 Model 4: Teacher Performance Trend & Early Coaching Triggers](#64-model-4-teacher-performance-trend--early-coaching-triggers)
   - [6.5 1-Click WhatsApp Quick Intervention Hub](#65-1-click-whatsapp-quick-intervention-hub)
7. [👨‍👩‍👧 7. Modul Portal Wali Santri & Penguncian Jadwal Pasca-Bayar](#-7-modul-portal-wali-santri--penguncian-jadwal-pasca-bayar)
   - [7.1 Alur Pendaftaran Tanpa Auto-Bayar (Status Waiting Admin)](#71-alur-pendaftaran-tanpa-auto-bayar-status-waiting-admin)
   - [7.2 Fitur Edit Hari & Jam Sebelum Pembayaran](#72-fitur-edit-hari--jam-sebelum-pembayaran)
   - [7.3 Kunci Permanen Jadwal Belajar Pasca-Bayar (`isActive()`)](#73-kunci-permanen-jadwal-belajar-pasca-bayar-isactive)
   - [7.4 Sistem Evaluasi Bimbingan & Rating Multi-Kategori Pasca Sesi (Pending Review & Sinkronisasi Skor Guru)](#74-sistem-evaluasi-bimbingan--rating-multi-kategori-pasca-sesi-pending-review--sinkronisasi-skor-guru)
   - [7.5 Mesin Deteksi Sentimen Komplain & Tiket Intervensi Koordinator Akademik](#75-mesin-deteksi-sentimen-komplain--tiket-intervensi-koordinator-akademik)
8. [🎮 8. Modul Ruang Belajar Santri & Gamifikasi Islami Terpadu](#-8-modul-ruang-belajar-santri--gamifikasi-islami-terpadu)
   - [8.1 Sistem Poin Fastabiqul Khoirot](#81-sistem-poin-fastabiqul-khoirot)
   - [8.2 15 Lencana Prestasi Santri (B01–B15) & Peta 30 Juz](#82-15-lencana-prestasi-santri-b01b15--peta-30-juz)
9. [🔐 9. Otomasi Akun Santri & Kebijakan Keamanan Password](#-9-otomasi-akun-santri--kebijakan-keamanan-password)
   - [9.1 Format Email Bersih Bebas Karakter Acak](#91-format-email-bersih-bebas-karakter-acak)
   - [9.2 Password Default `santri123` & Banner Peringatan Keamanan](#92-password-default-santri123--banner-peringatan-keamanan)
10. [📊 10. Standarisasi Universal DataTables Berbasis Aset Lokal](#-10-standarisasi-universal-datatables-berbasis-aset-lokal)
11. [📰 11. Modul Blog & Literasi Edukasi Islami](#-11-modul-blog--literasi-edukasi-islami)
12. [🕌 12. Fitur Jadwal Sholat & Kompas Arah Kiblat Real-Time](#-12-fitur-jadwal-sholat--kompas-arah-kiblat-real-time)
13. [💳 13. Integrasi Payment Gateway Pakasir & Invoice Real-Time](#-13-integrasi-payment-gateway-pakasir--invoice-real-time)
14. [⭐ 14. Matriks Hak Akses Pengguna (Role Permission Matrix)](#-14-matriks-hak-akses-pengguna-role-permission-matrix)
15. [🔔 15. Sistem Notifikasi & Alert Terpusat (Centralized Alert System)](#-15-sistem-notifikasi--alert-terpusat-centralized-alert-system)
16. [🗄️ 16. Penjelasan Seluruh Database (58 Tabel Utama)](#-16-penjelasan-seluruh-database-58-tabel-utama)
17. [🧠 17. Penjelasan Seluruh Model, Service, & Controller Inti](#-17-penjelasan-seluruh-model-service--controller-inti)
18. [⚙️ 18. Console Commands & Background Scheduler](#-18-console-commands--background-scheduler)
19. [📁 19. Struktur Folder Proyek](#-19-struktur-folder-proyek)
20. [👤 20. Modul Manajemen Profil Multi-Role & Sinkronisasi Lokasi Terpusat](#-20-modul-manajemen-profil-multi-role--sinkronisasi-lokasi-terpusat)
21. [🧪 21. Hasil Pengujian Otomatis & Quality Assurance (100% Green Pass)](#-21-hasil-pengujian-otomatis--quality-assurance-100-green-pass)
22. [🎨 22. Standarisasi Antarmuka Publik & Penyelarasan Frontend Antislop-UI](#-22-standarisasi-antarmuka-publik--penyelarasan-frontend-antislop-ui)
    - [22.1 Penyelarasan Halaman Tentang Kami (`/tentang-kami`)](#221-penyelarasan-halaman-tentang-kami-tentang-kami)
    - [22.2 Redesain Pelacak Status Lamaran Publik (`/cek-status-lamaran`)](#222-redesain-pelacak-status-lamaran-publik-cek-status-lamaran)
    - [22.3 Aksesibilitas Keyboard & Semantik HTML5](#223-aksesibilitas-keyboard--semantik-html5)
    - [22.4 Eliminasi Warna Biru Navbar & Harmonisasi Palet Hijau-Emas](#224-eliminasi-warna-biru-navbar--harmonisasi-palet-hijau-emas)
    - [22.5 Modern Split-Screen Onboarding Guru Mitra (`/bergabung`)](#225-modern-split-screen-onboarding-guru-mitra-bergabung)
    - [22.6 Elevasi Desain Halaman Metode Belajar (`/metode`)](#226-elevasi-desain-halaman-metode-belajar-metode)
    - [22.7 Overhaul Antislop Halaman Biaya & Paket (`/biaya`)](#227-overhaul-antislop-halaman-biaya--paket-biaya)
    - [22.8 Animasi Mikro Elegan & Kepatuhan WCAG AA / Aksesibilitas Gerak](#228-animasi-mikro-elegan--kepatuhan-wcag-aa--aksesibilitas-gerak)
23. [🌟 23. Desain Minimalis Modern & Fitur Booking Sesi Uji Coba Gratis 15 Menit (Placement Test)](#-23-desain-minimalis-modern--fitur-booking-sesi-uji-coba-gratis-15-menit-placement-test)
    - [23.1 Latar Belakang & Nilai Manfaat Penempatan Awal](#231-latar-belakang--nilai-manfaat-penempatan-awal)
    - [23.2 Arsitektur Basis Data `trial_bookings` & Status Lifecycle](#232-arsitektur-basis-data-trial_bookings--status-lifecycle)
    - [23.3 Integrasi Otomasi Notifikasi WhatsApp Koordinasi Jadwal](#233-integrasi-otomasi-notifikasi-whatsapp-koordinasi-jadwal)
    - [23.4 Penyelarasan Frontend Antislop Suite (UI, Human, LayoutMobile, Copywriting)](#234-penyelarasan-frontend-antislop-suite-ui-human-layoutmobile-copywriting)
24. [💎 24. Standardisasi 4 Paket Bimbingan Privat (Opsi A) & Durasi 90 Menit](#-24-standardisasi-4-paket-bimbingan-privat-opsi-a--durasi-90-menit)
    - [24.1 Matriks 4 Paket Belajar (Opsi A)](#241-matriks-4-paket-belajar-opsi-a)
    - [24.2 Standar Durasi 90 Menit & Keunggulan Privat 1-on-1](#242-standar-durasi-90-menit--keunggulan-privat-1-on-1)
    - [24.3 Akses Publik Halaman Biaya & Proteksi Privasi Pengajar](#243-akses-publik-halaman-biaya--proteksi-privasi-pengajar)
25: [⚖️ 25. Modul Pembagian Finansial Otomatis (Revenue Sharing Engine) & Infaq 10%](#-25-modul-pembagian-finansial-otomatis-revenue-sharing-engine--infaq-10)
    - [25.1 Formula Matematis Bagi Hasil Sesi Belajar](#251-formula-matematis-bagi-hasil-sesi-belajar)
    - [25.2 Isolasi Informasi Finansial Guru (Anti-Envy Isolation)](#252-isolasi-informasi-finansial-guru-anti-envy-isolation)
    - [25.3 Penyajian Multi-Role Dashboard (Admin, Mentor, Wali Santri)](#253-penyajian-multi-role-dashboard-admin-mentor-wali-santri)
26. [🌿 26. Penyelarasan Frontend Editorial, Konsolidasi Seeder Bersih, & Ujian Guru](#-26-penyelarasan-frontend-editorial-konsolidasi-seeder-bersih--ujian-guru)
    - [26.1 Konsolidasi Seeder Bersih & Pemulihan Taksonomi Blog (Kategori & Tag)](#261-konsolidasi-seeder-bersih--pemulihan-taksonomi-blog-kategori--tag)
    - [26.2 Validasi Ikon Program & Spasi Vertikal Header Terhadap Fixed Navbar](#262-validasi-ikon-program--spasi-vertikal-header-terhadap-fixed-navbar)
    - [26.3 Aksesibilitas Kontras Tinggi CTA Section (WCAG AAA Pure White & Emerald)](#263-aksesibilitas-kontras-tinggi-cta-section-wcag-aaa-pure-white--emerald)
    - [26.4 Redesain Komprehensif Portal Blog (/blog)](#264-redesain-komprehensif-portal-blog-blog)
    - [26.5 Standarisasi Desain Seleksi Guru (/mentor/recruitment/take-test)](#265-standarisasi-desain-seleksi-guru-mentorrecruitmenttake-test)
27. [🛡️ 27. Arsitektur Keamanan Sistem (Laravel Security Hardening & Compliance Alignment)](#-27-arsitektur-keamanan-sistem-laravel-security-hardening--compliance-alignment)
    - [27.1 Kepatuhan Standar Laravel Cloud Compliance (SOC 2 Type 2, HIPAA, ISO 27001, OWASP Top 10)](#271-kepatuhan-standar-laravel-cloud-compliance-soc-2-type-2-hipaa-iso-27001-owasp-top-10)
    - [27.2 Implementasi 8 Pilar Keamanan Laravel (Danushaka Dissanayaka)](#272-implementasi-8-pilar-keamanan-laravel-danushaka-dissanayaka)
    - [27.3 Lapisan Pertahanan HTTP Security Headers & Content Security Policy (CSP)](#273-lapisan-pertahanan-http-security-headers--content-security-policy-csp)
    - [27.4 Sistem Rate Limiting & Proteksi Brute-Force Terdistribusi](#274-sistem-rate-limiting--proteksi-brute-force-terdistribusi)
    - [27.5 Audit Mass Assignment, Enkripsi Sesi, & Sanitasi File Upload Klien](#275-audit-mass-assignment-enkripsi-sesi--sanitasi-file-upload-klien)
    - [27.6 Konfigurasi Web Server Apache (.htaccess) & Isolasi Berkas Lingkungan (.env)](#276-konfigurasi-web-server-apache-htaccess--isolasi-berkas-lingkungan-env)
    - [27.7 Hasil Pengujian Keamanan Otomatis & Kesiapan Produksi (Production Ready)](#277-hasil-pengujian-keamanan-otomatis--kesiapan-produksi-production-ready)
28. [🎯 28. Arsitektur Strategi Marketing Konversi Tinggi & Optimalisasi Etalase Biaya](#-28-arsitektur-strategi-marketing-konversi-tinggi--optimalisasi-etalase-biaya)
    - [28.1 Latar Belakang & Eliminasi Blunder Transparansi Dapur Finansial](#281-latar-belakang--eliminasi-blunder-transparansi-dapur-finansial)
    - [28.2 Empat Hook Emosional Utama & Trust Anchors di Beranda](#282-empat-hook-emosional-utama--trust-anchors-di-beranda)
    - [28.3 Tabel Komparasi Nilai "TPA Tradisional vs Privat Al-Hikmah 1-on-1"](#283-tabel-komparasi-nilai-tpa-tradisional-vs-privat-al-hikmah-1-on-1)
    - [28.4 Reframing Investasi "Rp 20.000/Hari" & Alokasi Infaq Jariyah Dakwah](#284-reframing-investasi-rp-20000hari--alokasi-infaq-jariyah-dakwah)
    - [28.5 Redesain Kartu Membership Pass & Briefing Kurikulum Personal Santri Baru](#285-redesain-kartu-membership-pass--briefing-kurikulum-personal-santri-baru)
    - [28.6 Standardisasi Spasi Vertikal Fixed-Navbar Subpage (.editorial-page-header)](#286-standardisasi-spasi-vertikal-fixed-navbar-subpage-editorial-page-header)

---

## 📌 1. RINGKASAN EKSEKUTIF, FONDASI PRODUK & NILAI MANFAAT APLIKASI

**AL-HIKMAH LMS** adalah platform manajemen pendampingan belajar Al-Qur'an terpadu berbasis web yang dirancang khusus untuk memfasilitasi anak-anak dan dewasa dalam belajar membaca Al-Qur'an (Iqra/Tahsin), menghafal (Tahfidz), memahami Tajwid & Makharijul Huruf, Fiqih Nisa, Bahasa Arab Dasar, Nahwu & Sharaf, serta pembiasaan Adab & Doa Harian.

Platform ini mentransformasikan operasional lembaga bimbingan Al-Qur'an dari sistem manual menjadi ekosistem digital otomatis yang menghubungkan **Santri (Student)**, **Wali Santri (Parent)**, **Guru Pembimbing (Mentor)**, **Calon Guru (Mentor Applicant)**, dan **Manajemen Lembaga (Admin)** secara transparan, menyenangkan, akuntabel, dan real-time.

```mermaid
graph LR
    A["Wali Santri (Parent)"] -->|"Daftar & Pilih Jadwal (Tanpa Tagihan Instan)"| B["Sistem Al-Hikmah LMS"]
    B -->|"Review Jadwal & Smart Matchmaking AI"| F["Admin Lembaga"]
    F -->|"OPSI A: Setujui (Anti-Bentrok) / OPSI B: Jadwal Alternatif"| B
    B -->|"Terbitkan Invoice & Kunci Jadwal Pasca-Bayar"| A
    C["Santri (Student)"] -->|"Login 'santri123', Gamifikasi, & Setoran"| B
    E["Guru Resmi (Mentor)"] -->|"Input Slot 0-6, Libur Rutin, Multi-AI Soal PDF"| B
    F -->|"PA-EWS: Dropout Risk, Velocity, & Revenue Forecast"| B
```

### 1.1 Platform, Tech Stack, & 4 Persona Pengguna Inti

1. **Platform**: Web responsive lintas perangkat (Desktop, Tablet, dan Smartphone) yang dioptimalkan untuk orang tua, santri, guru, dan pengelola yayasan.
2. **Tech Stack Modern**:
   - **Backend Framework**: Laravel 12 (PHP 8.2+) dengan arsitektur Service Provider, Job/Queue, Console Scheduler, dan Event-Driven.
   - **Frontend & Templating**: Blade Views, Bootstrap 5.3.3 terintegrasi Custom CSS Tokens (Emerald Deep `#0d7a3e`, Warm Amber `#d97706`, Neutral Canvas `#fcfdfd`), Vanilla JavaScript modular, dan Vite asset bundling.
   - **Database & Storage**: MySQL 8.0+ dengan spatial indices (`ST_Distance_Sphere`), Eloquent ORM, serta DataTables berbasis aset lokal.
3. **Empat Persona Pengguna (User Personas)**:
   - **Wali Santri (Orang Tua)**: Mencari bimbingan Al-Qur'an terpercaya, sabar, privat 1-on-1 untuk ananda dengan jadwal fleksibel, transparansi investasi, kemudahan booking uji coba gratis, dan pemantauan rapor perkembangan berkala via ponsel.
   - **Santri (Anak-anak & Remaja)**: Mempelajari Iqra, Tahsin, Tajwid, dan Tahfidz dengan pendampingan ramah anak, penuh adab, serta termotivasi oleh gamifikasi poin Fastabiqul Khoirot dan 15 lencana prestasi.
   - **Guru / Mentor Al-Qur'an**: Pendidik bersanad dan terverifikasi yang membimbing santri secara privat, mencatat evaluasi mutaba'ah per sesi, serta memantau jadwal dan akumulasi honor secara transparan dan berkehormatan.
   - **Admin / Pengelola Lembaga**: Mengelola seluruh siklus operasional: kurikulum, seleksi guru (ATS), pencocokan pintar (Smart Matchmaking AI), pemantauan jadwal bebas guru, penagihan invoice, dan pembukuan bagi hasil otomatis.

### 1.2 Positioning Produk, Brand Commitments & 4 Prinsip Desain

1. **Positioning Produk**:  
   Bukan sekadar kursus mengaji kilat atau marketplace guru umum lepas (*gig platform*). AL-HIKMAH berfokus pada **pendampingan santun 1 Guru 1 Santri (90 menit penuh)**, kurikulum personal berbasis evaluasi awal makhraj/tajwid, garansi kecocokan pendidik, dan keterbukaan rapor mutaba'ah digital setiap sesi ke orang tua.
2. **Brand Commitments & Tone of Voice**:
   - **Identitas**: AL-HIKMAH (Bimbingan Al-Qur'an & LMS Generasi Qur'ani).
   - **Tone of Voice**: Santun, hangat, mengayomi, profesional, menenangkan, penuh adab dan amanah.
   - **Visual Personality**: *Islamic Editorial Minimalist*, bersih, elegan, terpercaya, bebas dari ornamen berlebihan atau elemen generik (*AI slop*).
3. **Empat Prinsip Desain Produk**:
   - 🛡️ **Amanah & Transparansi**: Informasi biaya, kurikulum, dan fasilitas disajikan jujur tanpa klaim palsu atau biaya tersembunyi.
   - 🌸 **Ketenangan & Kehangatan**: Desain menghadirkan ketenangan (*sakinah*), kenyamanan visual, dan rasa percaya bagi orang tua.
   - ⚡ **Kemudahan Akses**: Navigasi intuitif, mobile-first, tombol aksi jelas, dan ramah keyboard (WCAG AA/AAA).
   - 📖 **Respek terhadap Konten Al-Qur'an**: Tipografi dan tata letak menghormati adab dan keagungan Al-Qur'an dengan font Arab `Amiri` terstandarisasi.

### 1.3 Kapabilitas Inti & Batasan Sistem (Capabilities & Constraints)

- **Model Bimbingan**: Privat 1-on-1 (durasi 90 menit per sesi) dengan opsi *Home Visit* (guru datang ke rumah) atau *Online Interaktif*.
- **Pintu Masuk Bebas Risiko**: Booking sesi uji coba / placement test awal 15 menit gratis tanpa kewajiban bayar di awal.
- **Standar Paket Belajar**: 4 paket intensitas bertingkat (*Tunas Istiqomah*, *Bimbingan Mumtaz*, *Akselerasi Itqan*, *Mahir Tahfidz*).
- **Proteksi Dapur Finansial (Gated Pricing)**: Rincian harga paket bimbingan dan simulasi investasi hanya dapat diakses setelah Orang Tua mendaftar dan login (`/biaya`), sedangkan formula margin bagi hasil internal lembaga tersimpan aman di level backend.
- **Konsultasi WhatsApp Instan**: Tautan WhatsApp otomatis terintegrasi untuk pendampingan customer service cepat.
- **Dukungan Tema Konsisten**: Mendukung mode terang (*Light*) dan gelap (*Dark*) yang nyaman di mata santri dan wali.

### 1.4 Metrik Bisnis & Dampak Operasional:

| Parameter Kinerja | Sebelum Digitalisasi (Manual) | Dengan AL-HIKMAH LMS (v10.9) | Peningkatan Efisiensi |
| :--- | :--- | :--- | :---: |
| **Akurasi Alokasi Privat 1-on-1** | Sering terjadi bentrok slot jam yang sama | Proteksi slot 1-on-1 ketat di AI & Dropdown OPSI A | **100% Zero Double-Booking** |
| **Kepatuhan Syariat Gender Santri** | Gender guru tercampur tanpa filter ketat umur | Rule 10 Tahun Otomatis (L < 10th & P -> Ustazah, L $\ge$ 10th -> Ustadz) | **100% Sesuai Syariat** |
| **Koordinasi Jadwal Bebas Guru** | Chat manual di grup WA, admin sering lupa | Day-Off Monitoring Center & Toggle Libur Rutin Mingguan | **Transparan di Admin Dashboard** |
| **Stabilitas Jadwal Pasca-Bayar** | Orang tua mengubah hari seenaknya saat kelas jalan | Jadwal dikunci permanen (*locked*) begitu lunas & aktif | **Operasional Terjadwal Rapi** |
| **Input Jadwal Ketersediaan Guru** | Ketik jam bebas manual berulang kali | Angka Slot 0–6 + 1-Click Import/Export Format WhatsApp | **Selesai dalam $< 30$ Detik** |
| **Pencegahan Santri Berhenti (Dropout)**| Terlambat terdeteksi saat santri sudah keluar | Deteksi dini 14 hari sebelumnya dengan 4-Factor Weighted Model | **Menurunkan Churn $\ge 35\%$** |
| **Pembuatan Paket Ujian Santri** | Guru mengetik manual berjam-jam | Multi-AI Generator (5 Provider) + Cetak PDF A4 Siap Pakai | **Selesai dalam $< 3$ Detik** |
| **Konsistensi UI DataTables** | jQuery error, pagination bertabrakan | DataTables universal berbasis lokal `public/assets/DataTables` | **100% Responsif & Cepat** |

### 1.5 Konsolidasi Dokumen Rekayasa & PRD ke dalam Single Source of Truth

Seluruh dokumen rekayasa awal (*Product Requirements Documents / Issue Trackers*) yang sebelumnya digunakan sebagai cetak biru teknis telah **100% tuntas diimplementasikan, diverifikasi melalui automated test suite, dan disatukan secara komprehensif ke dalam dokumen `tentang.md` ini**:

| Dokumen PRD Awal | Cakupan Fitur Utama | Status Implementasi | Rujukan Bab di `tentang.md` |
| :--- | :--- | :---: | :--- |
| **`PRODUCT.md`** | Fondasi Spesifikasi Produk, Tech Stack Modern (Laravel 12 + Blade + Vite), 4 Persona Pengguna Inti, Positioning Produk Privat 1-on-1, Brand Commitments & Islamic Editorial Design Principles | ✅ **Selesai 100%** | [Bab 1.1–1.3](#11-platform-tech-stack--4-persona-pengguna-inti) & [Bab 22](#-22-standarisasi-antarmuka-publik--penyelarasan-frontend-antislop-ui) |
| **`revisi-total.md`** | Revisi Total Frontend, Standardisasi 4 Paket Bimbingan Privat (Tunas Istiqomah, Bimbingan Mumtaz, Akselerasi Itqan, Mahir Tahfidz @ 90 Mnt), Sistem Finansial Bagi Hasil & Infaq 10%, Proteksi Gated Pricing `/biaya` (Role Parent & Admin), serta Benchmark Taqiyya Bimbel (`taqiyyabimbel.my.id`) | ✅ **Selesai 100%** | [Bab 24](#-24-standardisasi-4-paket-bimbingan-privat-opsi-a--durasi-90-menit), [Bab 25](#-25-modul-pembagian-finansial-otomatis-revenue-sharing-engine--infaq-10), [Bab 26](#-26-penyelarasan-frontend-editorial-konsolidasi-seeder-bersih--ujian-guru), & [Bab 27](#-27-arsitektur-keamanan-sistem-laravel-security-hardening--compliance-alignment) |
| **`marketing.md`** | Arsitektur Strategi Marketing Konversi Tinggi: 5 Pilar Penjualan, Tabel Komparasi Nilai TPA vs Al-Hikmah 1-on-1, Daily Cost Reframing Rp 20.000/Hari, Naskah Hook Empati Wali Santri di Beranda, dan Checklist Infaq Jariyah Dakwah | ✅ **Selesai 100%** | [Bab 28](#-28-arsitektur-strategi-marketing-konversi-tinggi--optimalisasi-etalase-biaya) |
| **`issue.md`** | Revitalisasi Rekrutmen ATS 7-Tahap, Live Probation Hub, Orientasi 4 Pilar, dan Rating Evaluasi Sesi Orang Tua | ✅ **Selesai 100%** | [Bab 2](#-2-modul-rekrutmen-ujian-kompetensi--masa-percobaan-guru) & [Bab 7](#-7-modul-portal-wali-santri--penguncian-jadwal-pasca-bayar) |
| **`issue_profile.md`** | Portal Detail Profil Guru Admin (`/admin/staff/{id}`), Masking & Verifikasi Rekening Bank, Dokumen CV/Sanad, Hand-Over Wizard | ✅ **Selesai 100%** | [Bab 2.7](#27-detail-profil-lengkap-guru-adminstaffid-verifikasi-rekening-bank--student-hand-over-wizard) |
| **`matching.md`** | Smart Matchmaking AI v3.0, Integrasi Google Calendar, Smart Load Balancing (Burnout Protection), Cosine Similarity Gaya Belajar | ✅ **Selesai 100%** | [Bab 5](#-5-modul-smart-matchmaking-v30-external-calendar-sync--dynamic-load-balancing) |
| **`profile.md`** | Manajemen Profil Multi-Role (Parent, Student, Mentor, Admin), Single Source of Location Truth, Inheritance Titik Peta Navigasi | ✅ **Selesai 100%** | [Bab 20](#-20-modul-manajemen-profil-multi-role--sinkronisasi-lokasi-terpusat) |
| **`warning.md`** | Predictive Analytics & Early Warning System (PA-EWS), Model Dropout Risk, Learning Velocity, Revenue Forecast, 1-Click WA Intervention | ✅ **Selesai 100%** | [Bab 6](#-6-predictive-analytics--early-warning-system-pa-ews) |

Dengan tuntasnya seluruh fase pengujian dan adopsi produksi, berkas PRD kerja tersebut telah dihapus secara bersih dari repositori untuk menjaga kerapian struktur basis kode (*clean repository hygiene*), di mana `tentang.md` menjadi satu-satunya dokumen panduan arsitektur resmi (*single source of truth*) AL-HIKMAH LMS.

---

## 🎓 2. MODUL REKRUTMEN, UJIAN KOMPETENSI, & MASA PERCOBAAN GURU

Modul ini mendigitalisasi siklus hidup rekrutmen pengajar Al-Qur'an secara terpadu, mulai dari pendaftaran berkas, ujian kompetensi berbasis web, wawancara, hingga masa percobaan (*probation*).

### 2.1 Alur Seleksi & Kategori Ujian Kompetensi
1. **Tajwid Test**: Hukum Nun Sukun/Tanwin, Mim Sukun, Mad Far'i, dan Waqaf/Ibtida'.
2. **Makharijul Huruf**: Titik keluar huruf hijaiyah (Al-Halq, Al-Lisan, Asy-Syafatain, Al-Jauf, Al-Khaisyum).
3. **Tahsin & Fashahah**: Kelancaran bacaan, sifatul huruf (Hams, Jahr, Isti'la, Istithalah), dan gharib.

### 2.2 Isolasi Hak Akses Dashboard (Gating Lifecycle)
- **Calon Guru (Tahap Seleksi)**: Hanya dapat melihat laman informasi lamaran, kartu ujian, dan hasil seleksi. Seluruh menu operasional bimbingan disembunyikan (*hidden & protected*).
- **Guru Resmi / Disetujui (Approved)**: Seluruh fitur bimbingan dan modul operasional terbuka penuh.

### 2.3 Fitur Manajemen Cuti & Guru Pengganti (Mentor Leave & Substitute)
Memungkinkan mentor mengajukan cuti dan admin menunjuk guru pengganti sementara (*substitute mentor*) dari daftar mentor aktif yang berkapasitas optimal.

### 2.4 ATS Pipeline 7-Tahapan & 1-Klik Verifikasi Berkas (Paket 15 Soal Seleksi AI & Notifikasi WhatsApp)
Sistem Applicant Tracking System (ATS) dirancang khusus untuk mempermudah panitia seleksi dalam memproses pelamar guru:
1. **Navigasi 7 Tab Filter Pipeline**: 
   - `Semua Pelamar` (Total berkas masuk)
   - `Menunggu Berkas` (`submitted` / `document_review`)
   - `Tes Dijadwalkan` (`test_scheduled`)
   - `Tes Selesai` (`test_completed`)
   - `Wawancara Dijadwalkan` (`interview_scheduled`)
   - `Wawancara Selesai` (`interview_completed`)
   - `Diterima / Probation` (`approved`)
   - `Ditolak` (`rejected`)
   Tiap tab dilengkapi badge counter dinamis yang mencerminkan beban kerja seleksi real-time.
2. **1-Klik Verifikasi Berkas & Auto-Test Generation**:
   - Ketika admin menekan tombol *"Setujui Berkas & Jadwalkan Tes"*, sistem secara atomik memverifikasi dokumen pelamar, meng-generate paket 15 soal ujian kompetensi Al-Qur'an dan pedagogi (gabungan tajwid pilihan ganda, essay makhraj, dan simulasi kasus bimbingan), dan langsung menugaskan sesi tes ke calon guru.
   - Sistem secara otomatis mengirimkan notifikasi resmi via **WhatsApp Gateway** ke nomor ponsel calon guru dengan instruksi pengerjaan 2x24 jam beserta tautan portal seleksi.
3. **Penjadwalan Wawancara & Microteaching Terintegrasi**:
   - Admin dapat menjadwalkan sesi wawancara tatap muka maupun online (Google Meet) lengkap dengan catatan persiapan microteaching.
   - Undangan wawancara dan tautan virtual meeting dikirimkan seketika via WhatsApp ke calon guru.
4. **Otomasi Akun & Penerimaan Calon Guru**:
   - Saat admin menyetujui penerimaan (*Accept Candidate*), sistem otomatis mengaktifkan profil mentor ke status `probation`, menginisialisasi pencatatan masa percobaan 90 hari di tabel `mentor_probation_trackings`, dan mengirimkan kredensial akun mengajar via WhatsApp.

### 2.5 Revitalisasi Modul Masa Percobaan (Live Probation Hub, 4 Pilar Orientasi, & Lencana M01)
Modul masa percobaan (*probation*) bertransformasi menjadi pusat evaluasi mutu pengajar baru:
1. **Sinkronisasi Metrik Riil LMS (1-Klik Live Sync)**:
   - Tombol *"🔄 Sinkronkan Data Aktual LMS"* pada portal admin menghitung metrik kehadiran riil mengajar dari tabel `learning_sessions`:
     $$\text{Attendance Rate} = \min\left(100\%, \frac{\text{Sesi Selesai (Completed)}}{\text{Sesi Jadwal Terlewat}} \times 100\%\right)$$
   - Akumulasi rating dari tabel `mentor_feedback` yang diinput oleh wali santri disinkronkan secara presisi ke `average_rating`.
   - Sistem mendeteksi secara otomatis apakah sesi perdana telah terlaksana (`first_session_conducted = true`) dan mencatat stempel waktu `last_synced_at`.
2. **Widget Monitoring 4 Pilar Orientasi di Dasbor Guru Probation & Hub Pembekalan Mandiri**:
   - Di dasbor utama mentor berstatus probation, hadir widget interaktif dengan penghitung mundur sisa masa percobaan (*countdown days remaining* dari 90 hari).
   - Checklist interaktif 4 Modul Orientasi Wajib:
     - 📘 *Modul 1: Orientasi Lembaga & SOP Pengajaran Al-Hikmah*
     - 📗 *Modul 2: Standar Mutaba'ah & Penilaian Tajwid Terpadu*
     - 📙 *Modul 3: Simulasi Praktik Sesi Perdana di LMS*
     - 📕 *Modul 4: Komunikasi Efektif & Pelaporan ke Wali Santri*
   - Indikator progres target kelulusan: Tingkat kehadiran mengajar ($\ge 90\%$) dan rating evaluasi wali santri ($\ge 4.50$).
   - **Portal Khusus Pusat Orientasi & Panduan Guru Baru (`/mentor/orientation`)**:
     - Menggantikan tautan tombol yang sebelumnya mengarah ke profil menjadi portal orientasi interaktif khusus (`mentor.orientation.index`).
     - Menyediakan kurikulum komprehensif 4 modul: visi-misi yayasan, SOP kehadiran & busana syar'i, rubrik tajwid 4 dimensi (Makhraj, Sifat, Mad, Ghunnah), panduan teknis sesi KBM di LMS, dan adab komunikasi santun dengan wali santri.
     - Dilengkapi tombol konfirmasi mandiri *"Tandai Selesai Mempelajari"* (`mentor.orientation.complete`) yang otomatis memperbarui status modul di database, mencatat audit log aktivitas mentor (`MentorActivityLog`), dan menyinkronkan status 4/4 modul tuntas secara real-time.
3. **Tata Kelola Keputusan Akhir (*Final Decision Engine*)**:
   - 🟢 **Lulus Menjadi Guru Tetap**: Status mentor dinaikkan menjadi `active`, dan sistem otomatis menyematkan **Lencana Kehormatan `M01 - Mentor Certified`** ke dalam riwayat sertifikasi mengajar.
   - 🟡 **Perpanjang Masa Percobaan**: Menambahkan waktu 30 hari ke `probation_end_date` dengan catatan bimbingan perbaikan bagi calon guru.
   - 🔴 **Terminasi / Tidak Melanjutkan**: Menonaktifkan akun mentor (`status = 'inactive'`, `is_active = false`) dengan jaminan keamanan re-alokasi santri binaan ke guru pengganti.
4. **Integrasi Widget Monitoring Progres Orientasi & Probation ke Admin Dashboard & Tabel Manajemen Probation**:
   - **Admin Dashboard (`/admin/dashboard`)**: Pada kartu *"Pusat Rekrutmen Guru & Evaluasi AI"*, kini hadir antarmuka tab ganda interaktif (*Tab 1: Pelamar Calon Guru Baru* & *Tab 2: Guru Masa Percobaan & 4 Modul Orientasi*). Admin dan Koordinator Akademik dapat langsung memantau sisa hari evaluasi (penghitung mundur 90 hari), persentase tuntas 4 modul orientasi ($X/4$ Tuntas), visual badge M1 (SOP), M2 (Tajwid), M3 (KBM LMS), M4 (Komunikasi Wali), tingkat presensi riil ($\ge 90\%$), rating wali santri ($\ge 4.50$), serta peringatan darurat tenggat dekat H-14 secara real-time.
   - **Tabel Manajemen Probation Admin (`/admin/mentors/probation`)**: Dilengkapi kolom khusus *"4 Modul Orientasi"* berisikan badge kelulusan modul ($4/4$ Tuntas berwarna hijau atau $X/4$ Selesai berwarna kuning), *progress bar* proporsional, serta indikator pil warna untuk setiap modul M1–M4 dengan tooltip deskripsi.


### 2.6 Cron Sinkronisasi Harian Masa Percobaan & Peringatan Otomatis WhatsApp H-14
Untuk mengeliminasi risiko kelalaian supervisi guru baru menjelang batas akhir 90 hari masa percobaan:
1. **Scheduled Daily Sync Engine (`probation:daily-sync`)**:
   - Dijadwalkan otomatis pada pukul **00:00 WIB** setiap hari via `routes/console.php`.
   - Melakukan sinkronisasi metrik LMS aktual (`syncTrackingStats`) untuk seluruh guru berstatus `active` probation (kehadiran riil, rata-rata rating wali santri, dan status tuntas modul).
   - Menghitung sisa hari masa percobaan secara presisi:
     $$\text{Remaining Days} = \text{Batas Akhir (end\_date)} - \text{Hari Ini}$$
2. **Kriteria Deteksi Otomatis Guru Berisiko (*At-Risk Detection*)**:
   - Guru diklasifikasikan berstatus **Berisiko (H-14)** jika:
     $$\text{Sisa Hari} \le 14 \quad \text{DAN} \quad \left(\text{Rating} < 4.50 \quad \lor \quad \text{Kehadiran} < 90.0\% \quad \lor \quad \text{Modul} < 4\right)$$
3. **Peringatan Otomatis WhatsApp Gateway ke Admin Lembaga**:
   - Jika terdeteksi satu atau lebih guru berisiko, sistem otomatis merangkai rekapitulasi darurat dan mengirimkan pesan WhatsApp ke nomor resmi Admin (`ADMIN_PHONE`):
     - Menampilkan nama guru, sisa hari, persentase kehadiran aktual vs target, rating bintang, dan status modul orientasi.
     - Menyertakan tautan langsung ke portal evaluasi admin `/admin/mentors/probation`.

### 2.7 Detail Profil Lengkap Guru (`/admin/staff/{id}`), Verifikasi Rekening Bank & Student Hand-Over Wizard
Sebagai bagian dari penyempurnaan tata kelola sumber daya pengajar (SDM) dan jaminan keberlangsungan belajar santri:
1. **Portal Detail Profil Guru Komprehensif (5 Kartu Utama)**:
   - Akses navigasi baru tombol *"Profil"* pada tabel staf admin (`/admin/staff`) mengarah langsung ke route `admin.staff.show` (`/admin/staff/{id}`).
   - Menyajikan profil guru secara holistik dan terstruktur melalui 5 kartu visual:
     - 🪪 **1. Informasi Pribadi & Kontak**: Foto profil berbingkai, NIP, Nama Lengkap & Gelar Akademik/Agama, Jenis Kelamin, Email terverifikasi, Nomor WhatsApp dengan tautan chat langsung, status kepegawaian (Aktif/Probation/Cuti), serta alamat domisili.
     - 📜 **2. Informasi Profesional & Sanad**: Tingkat pendidikan terakhir, nama perguruan tinggi/institusi, bidang studi, jumlah hafalan Al-Qur'an (Juz), silsilah transmisi sanad qira'ah, rekam jejak pengalaman mengajar, serta keahlian/spesialisasi materi bimbingan.
     - 📂 **3. Dokumen & Berkas Lamaran**: Pratinjau dan tautan unduh berkas asli saat rekrutmen (Curriculum Vitae/CV, Portofolio, Sertifikat Tahfidz/Sanad, Ijazah Terakhir) yang tersinkronisasi langsung dari data pendaftaran awal (`mentor_applications`).
     - 💳 **4. Rekening Bank & Verifikasi Finansial (Data Protection)**:
       - Menampilkan Nama Bank, Nama Pemilik Rekening, dan Nomor Rekening.
       - **Perlindungan Privasi Finansial**: Nomor rekening secara default disensor/dimask (*masked* `****1234`) demi keamanan finansial guru, dengan tombol saklar interaktif (ikon mata 👁️) untuk membuka sensor secara instan.
       - **Verifikasi Rekening Bank Resmi**: Menampilkan badge status verifikasi (`Telah Diverifikasi` / `Belum Diverifikasi`), tanggal verifikasi, nama admin pemverifikasi, dan catatan verifikasi terakhir.
       - Dilengkapi tombol *"Verifikasi Rekening Bank"* yang membuka modal verifikasi dengan catatan audit (misal *"Buku tabungan valid atas nama mentor bersangkutan"*).
       - Ketika diapprove via AJAX (`POST /admin/staff/{id}/verify-bank`), sistem mencatatkan entri log ganda ke `MentorActivityLog` (kategori: `bank_account_verification`) dan `FinancialAuditLog` (kategori: `mentor_bank_verification`) untuk transparansi audit lembaga.
     - 📊 **5. Statistik & Metrik Mengajar Aktual**: Menghitung secara real-time total santri binaan aktif, total jam sesi bimbingan yang telah diselesaikan, rata-rata rating kepuasan wali santri (bintang 1–5), serta persentase kehadiran mengajar.
2. **Student Hand-Over Wizard pada Keputusan Terminasi Masa Percobaan**:
   - Jika hasil evaluasi 90 hari masa percobaan menyatakan guru tidak memenuhi standar dan diputuskan untuk **Terminasi / Tidak Melanjutkan** (`terminated`):
     - Jika guru tersebut memiliki santri binaan aktif ($> 0$), sistem secara cerdas memunculkan **Hand-Over Wizard Section** di dalam modal evaluasi admin (`/admin/recruitment/probations/{id}`).
     - Admin **wajib** memilih Guru Pengganti (*Substitute Mentor*) dari daftar guru aktif yang memiliki kapasitas tersedia sebelum keputusan terminasi dapat disimpan.
     - Melalui `MentorProbationService::evaluateProbation` & `review`, sistem secara atomik mengeksekusi pengalihan santri:
       - Mencatatkan riwayat mutasi resmi pada `student_mutation_logs` dengan alasan `mentor_probation_terminated` beserta ID mentor pengganti.
       - Menonaktifkan status relasi guru lama pada tabel pivot `mentor_student` (`is_active = false`).
       - Menghubungkan santri ke guru pengganti pada slot waktu, hari, dan jam bimbingan yang identik sehingga jadwal santri tidak terganggu.
       - Memperbarui `Enrollment` dan memindahkan seluruh sesi bimbingan masa depan (`learning_sessions` berstatus `scheduled`) ke ID guru baru.
       - Mencatat mutasi ke `MentorActivityLog` dan `FinancialAuditLog`.
3. **Integrasi Akses Cepat Detail Akun Guru di Seluruh Titik Admin Dashboard & Tabel Manajemen**:
   - **Kartu Statistik SDM Dasbor**: Tautan cepat `Kelola SDM →` langsung membuka direktori profil pengajar.
   - **Monitoring Guru Libur & Cuti**: Nama guru dan tombol aksi *"Detail Akun"* terhubung langsung ke `/admin/staff/{id}`.
   - **Tabel Pengguna & Hak Akses**: Pengguna dengan role *Mentor* dilengkapi tombol langsung *"Detail Akun"* untuk inspeksi berkas CV, sanad, dan rekening bank.
   - **Widget Guru Probation & 4 Modul Orientasi**: Nama guru dan tombol *"Profil"* mengarah langsung ke detail profil mentor.
   - **Parent Monitoring Panel (Ulasan Guru)**: Nama pembimbing pada tabel feedback dapat diklik untuk memeriksa profil guru yang diulas.
   - **Aksi Cepat Admin**: Tombol navigasi *"Database Guru & Detail Akun"* tersemat di kartu aksi cepat dashboard.
   - **Tabel Guru Pendamping (`/admin/mentors`) & Masa Percobaan (`/admin/mentors/probation`)**: Setiap baris tabel dilengkapi tombol *"Detail Akun"* / *"Profil"* dan tautan nama langsung ke profil lengkap guru.

### 2.8 Sistem Pendukung Keputusan (SPK) Pemilihan Guru Teladan Berbasis Analytical Hierarchy Process (AHP Saaty)
Mengadopsi metodologi ilmiah **Analytical Hierarchy Process (AHP)** dari riset *SEMNAS RISTEK 2021* (`5038-9113-1-SM.pdf`), modul ini mentransformasikan evaluasi subjektif pengajar menjadi sistem pengambilan keputusan terukur matematis yang terintegrasi langsung dengan data aktual LMS (Zero Manual Questionnaires):
1. **Dekomposisi 5 Kriteria Baku Pedagogis Islami**:
   - $C_1$ **Kedisiplinan & Kehadiran** ($w \approx 19.0\%$): Menghitung tingkat kehadiran sesi mengajar riil (`attendance_rate`), ketepatan waktu memulai sesi, dan riwayat presensi dari tabel `learning_sessions`.
   - $C_2$ **Kualitas Pedagogi & Mutaba'ah** ($w \approx 36.6\%$, Bobot Tertinggi): Mengukur rata-rata nilai Tajwid santri binaan (`avg_tajwid_score`), persentase ketuntasan target hafalan (`target_achievement_rate`), dan kelengkapan catatan mutaba'ah di tabel `progress` & `hifz_targets`.
   - $C_3$ **Akhlak, Adab & Komunikasi** ($w \approx 19.0\%$): Menilai kesabaran guru, rata-rata adab santri (`avg_adab_score`), dimensi empati komunikasi di `mentor_feedback`, serta status bebas komplain (pengurangan penalti dari `mentor_intervention_tickets`).
   - $C_4$ **Kepuasan Wali Santri** ($w \approx 19.0\%$): Menghitung akumulasi rating bintang (1–5) dari umpan balik wali santri pasca-sesi dengan normalisasi skala 0–100.
   - $C_5$ **Pengembangan Diri & Keaktifan Lembaga** ($w \approx 6.6\%$): Mengukur ketuntasan 4 modul orientasi (`mentor_probation_trackings`), kepemilikan sanad qira'ah, keaktifan pelatihan, dan lencana kehormatan.
2. **Validasi Rasio Konsistensi Matematis ($CR \le 10\%$) & Fitur Simulasi Bobot Live**:
   - Menggunakan Skala Fundamental Saaty 1–9 ($a_{ji} = 1 / a_{ij}$) dengan tabel *Random Consistency Index* ($IR = 1.12$ untuk $n=5$).
   - Menghitung Nilai Eigen Maksimum ($\lambda_{\text{maks}}$), *Consistency Index* ($CI = \frac{\lambda_{\text{maks}} - n}{n - 1}$), dan *Consistency Ratio* ($CR = \frac{CI}{IR}$).
   - **Preset Terkalibrasi**: Matriks baku lembaga memiliki $CR = 0.089\%$ ($0.0009 \ll 10\%$), membuktikan pertimbangan berpasangan sangat konsisten secara ilmiah.
   - **Fitur Simulasi Bobot Live (Poin B)**: Pada modal kalibrasi `/admin/mentors/ahp-ranking`, pimpinan yayasan dapat menguji simulasi dampak perubahan bobot terhadap peringkat Top 5 secara real-time sebelum disimpan permanen, lengkap dengan peringatan otomatis jika $CR > 10\%$.
3. **Podium Juara, Leaderboard Lengkap, & Catatan Khusus Pimpinan (Poin A)**:
   - Menyajikan Podium Top 3 Ustadz/Ustazah Teladan berhias medali emas 🥇, perak 🥈, perunggu 🥉, dan rekomendasi alokasi bonus reward resmi:
     - **Juara 1 (Teladan Utama)**: Bonus Rp 1.000.000
     - **Juara 2 (Teladan Madya)**: Bonus Rp 750.000
     - **Juara 3 (Teladan Muda)**: Bonus Rp 500.000
     - **Top 4–5 (Apresiasi Berprestasi)**: Bonus Rp 250.000
   - **Grafik Radar 5 Dimensi (ApexCharts)**: Visualisasi jaring laba-laba interaktif membandingkan kekuatan pedagogis ketiga juara.
   - **Catatan Khusus Admin**: Kolom catatan pada tabel ranking untuk merekam prestasi luar biasa (misal: *"Mengkhatamkan 5 santri tajwid Mumtaz dalam 1 periode"*) atau kasus pembinaan khusus pimpinan yayasan.
4. **Otomasi Notifikasi WhatsApp Gateway (Poin C)**:
   - Tombol *"Umumkan via WhatsApp"* mengeksekusi pengiriman pesan resmi terpersonalisasi:
     - **Guru Juara**: Pesan apresiasi tahniah, rincian skor AHP, dan nominal bonus reward yang disalurkan.
     - **Guru Non-Juara**: Pesan evaluatif dan pembinaan konstruktif yang secara cerdas menganalisis kriteria dengan skor terendah sebagai arahan perbaikan periode berikutnya.
5. **Cetak Surat Keputusan (SK) Resmi Pimpinan Lembaga Format A4**:
   - Fitur cetak satu klik menghasilkan dokumen formal Surat Keputusan Yayasan Pendidikan Al-Hikmah lengkap dengan nomor SK formal, konsiderans menimbang/mengingat, diktum penetapan juara, tabel lampiran nilai 5 kriteria, dan kolom tanda tangan basah pimpinan lembaga siap cetak A4 (`@media print`).
6. **Widget Personal "My Performance Score" di Dasbor Guru (Poin D)**:
   - Pada `/mentor/dashboard`, hadir kartu personal terdedikasi yang menyajikan skor komposit AHP pribadi, peringkat berjalan di lembaga, progress bar 5 pilar kompetensi, saran peningkatan mutu terarah, dan catatan apresiasi pimpinan.

---

## 🤖 3. MODUL AI AUTO-GENERATE SOAL, BANK SOAL, & LEMBAR UJIAN PDF

### 3.1 Format Lembar Ujian Siap Cetak A4 & Kunci Jawaban Guru
Mendukung cetak lembar ujian A4 santri dan lembar kunci jawaban pegangan guru secara instan dan rapi.

### 3.2 Arsitektur Universal Multi-Provider AI & UI Selector
Mentor dapat memilih model AI yang diinginkan:
- **Auto Smart Failover** (Rekomendasi Utama)
- **Google Gemini 2.5 Flash**
- **DeepSeek AI**
- **Alibaba Qwen**
- **OpenAI GPT**
- **Anthropic Claude**

### 3.3 Smart Cascade Failover Engine (Zero Downtime)
Failover otomatis antar-provider jika terjadi *rate-limit* atau *timeout*, memastikan pembuatan soal tidak pernah gagal.

### 3.4 Bank Kurikulum Offline Al-Hikmah (Fallback Engine)
Perlindungan lapis terakhir kurikulum offline terkurasi untuk 10 program pembelajaran jika seluruh provider eksternal offline.

---

## ⏰ 4. MODUL KETERSEDIAAN MENGAJAR ANGKA SLOT (0–6) & KEBIJAKAN ANTI-BENTROK 1-ON-1

### 4.1 Standarisasi Master Angka Slot Waktu (0–6)

Untuk menyederhanakan komunikasi jam mengajar yang sebelumnya rumit, Al-Hikmah LMS menetapkan 7 angka slot terstandarisasi:

| Angka Slot | Waktu (WIB) | Kategori Waktu | Keterangan Operasional |
| :---: | :---: | :--- | :--- |
| **0** | `05:00` | 🌄 Sebelum / Ba'da Subuh | Halaqah Subuh / Fajar |
| **1** | `08:00` | 🌅 Pagi Hari | Sesi Pagi 1 (Anak Pra-Sekolah / Dhuha) |
| **2** | `10:00` | ☀️ Menjelang Dzuhur | Sesi Pagi 2 (Menjelang Siang) |
| **3** | `13:00` | 🌤️ Ba'da Dzuhur | Sesi Siang 1 (Pasca Sholat Dzuhur) |
| **4** | `16:00` | 🌇 Ba'da Ashar | Sesi Sore (Pasca Sekolah Formal) |
| **5** | `18:30` | 🌙 Ba'da Maghrib | Sesi Utama Malam 1 (Halaqah Maghrib) |
| **6** | `20:00` | 🌌 Ba'da Isya | Sesi Utama Malam 2 (Halaqah Isya) |

### 4.2 Smart WhatsApp Text Importer & Exporter Jadwal
* **Export ke WhatsApp (1-Klik)**: Mentor cukup mengklik tombol `📋 Salin Format WhatsApp` di `/mentor/availability` untuk membagikan jadwalnya ke grup WhatsApp lembaga:
  ```text
  Nama : Ustazah Fatimah Az-Zahra
  Senin : 1 2 4
  Selasa : 4 5
  Rabu : 1 2 4
  Kamis : 1 2 3 4
  Jumat : 4 5
  Sabtu : 1 2 3
  Ahad : Libur
  ```
* **Import dari WhatsApp (Smart Parser)**: Admin atau mentor dapat mem-paste teks format di atas ke dalam modal import, dan sistem otomatis mencentang seluruh checkbox slot hari yang bersesuaian secara instan.

### 4.3 Fitur Hari Bebas (Libur Rutin) Mentor & Day-Off Monitoring Center
1. **Pengaturan Mandiri oleh Mentor**: Pada `/mentor/availability`, mentor dapat mencentang toggle *"Tetapkan Sebagai Hari Libur / Hari Bebas"* pada hari rutin yang diinginkan (misal hari Ahad).
2. **Day-Off Monitoring Center**:
   - Di **Admin Dashboard** (`/admin/dashboard`): Hadir widget khusus yang menampilkan guru-guru yang memilih hari libur pekan ini beserta alasan/catatannya.
   - Di **Matriks Ketersediaan Admin** (`/admin/mentors/availability`): Menampilkan banner rekapitulasi libur guru terdaftar (misal: *"Ustazah Fatimah Az-Zahra: Libur Ahad"*).

### 4.4 Prinsip Bimbingan Privat 1-on-1 & Proteksi Anti-Bentrok (Zero Double-Booking)
Dalam bimbingan Al-Qur'an privat Al-Hikmah, setiap sesi belajar bersifat **1-on-1 (satu guru hanya membimbing satu santri per slot waktu)**:
- **Kapasitas Slot**: Setiap slot mengajar memiliki kapasitas maksimal 1 santri (`0/1` = Buka, `1/1` = Penuh).
- **Deteksi Bentrok Otomatis**: Method [`Mentor::hasScheduleConflict()`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Mentor.php) dan [`Mentor::isAvailableForSchedule()`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Mentor.php) memeriksa keterisian slot di tabel pivot `mentor_student` berdasarkan `slot_number` dan `time_assigned`.
- **Diskualifikasi AI**: Jika guru telah memiliki santri aktif di slot jam & hari yang diminta santri baru, skor slot langsung diberikan **`0.0`** sehingga guru tersebut otomatis **gugur dan tidak muncul dalam rekomendasi**.

### 4.5 Alur Penugasan Admin (OPSI A: Setujui vs OPSI B: Tawarkan Alternatif)
Pada halaman alokasi admin (`/admin/enrollments/{id}/edit`):
1. **OPSI A: Setujui Jadwal Orang Tua**:
   - Dropdown *"Pilih Mentor Pembimbing \*"* hanya memuat mentor yang **lolos verifikasi jadwal** (`$availableMentorsForOptionA`).
   - Mentor yang memiliki bentrok jadwal privat di hari & jam tersebut **tidak akan pernah muncul di dropdown**.
   - Jika seluruh mentor bentrok (misal di jam sibuk sore), dropdown otomatis dinonaktifkan, muncul alert peringatan, dan tombol submit dinonaktifkan (`disabled`) dengan label *"Jadwal Bentrok (Gunakan OPSI B)"*.
2. **OPSI B: Tawarkan Alternatif Jadwal (Counter-Offer)**:
   - Jika jadwal yang diminta santri penuh, admin dapat mengajukan saran hari atau jam alternatif kepada wali santri (misal mengusulkan hari Rabu/Kamis).

---

## 🧮 5. MODUL SMART MATCHMAKING v3.0, EXTERNAL CALENDAR SYNC, & DYNAMIC LOAD BALANCING

Modul penjodohan santri dan guru pembimbing (v3.0) mengintegrasikan kalender eksternal, penyeimbang beban kerja dinamis untuk mencegah kejenuhan guru (*burnout*), serta algoritma kecocokan gaya belajar (*pedagogical fit*) berbasis aljabar vektor *Cosine Similarity*.

### 5.1 Formula Multi-Kriteria 6 Dimensi v3.0 Berbobot
Algoritma [`MentorMatchingService`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorMatchingService.php) menghitung skor kecocokan multi-dimensi (0–100%):

$$\text{Skor Akhir} = (W_{\text{gender}} \times 20\%) + (W_{\text{lokasi}} \times 15\%) + (W_{\text{slot}} \times 20\%) + (W_{\text{spesialisasi}} \times 15\%) + (W_{\text{beban}} \times 15\%) + (W_{\text{pedagogi}} \times 15\%) + \text{Boost} - \text{Penalty}$$

- **Kesesuaian Gender ($20\%$)**: Aturan mutlak syariat usia 10 tahun (skor 100% atau gugur 0%).
- **Jarak Geografis ($15\%$)**: Berbasis `ST_Distance_Sphere` MySQL native untuk bimbingan tatap muka (offline) radius maksimal 25 km, bernilai penuh 100% untuk bimbingan daring (online).
- **Ketersediaan Slot & Kalender ($20\%$)**: Memeriksa ketersediaan angka slot 0–6 di LMS dan jadwal sibuk pribadi pada kalender eksternal.
- **Keahlian & Sanad ($15\%$)**: Kesesuaian sanad qira'ah dan kompetensi mentor dengan program (Tahsin, Tahfidz, Fiqih, Bahasa Arab).
- **Keseimbangan Beban ($15\%$)**: Menilai proporsi keterisian beban bimbingan guru terhadap rata-rata sistem dan kapasitas maksimal.
- **Gaya Belajar & Retensi ($15\%$)**: Keselarasan vektor preferensi santri dengan karakteristik pedagogis guru ditambah rekam jejak retensi santri serupa.
- **Faktor Penguat (Boost)**: Tambahan $+5\%$ untuk pemegang Lencana Teladan (M01/M03) atau rating $\ge 4.9$, serta hingga $+10\%$ untuk guru berkinerja tinggi pada snapshot komposit bulanan.
- **Faktor Pengurang (Penalty)**: Penalti $-15\%$ jika jam belajar mendekati waktu sholat maghrib/isya, atau jika beban guru melampaui batas aman kelelahan mengajar.

### 5.2 Integrasi Kalender Eksternal (Google Calendar OAuth & Catatan Kesiapan API)
1. **Pencegahan Bentrok Eksternal (Zero External Conflict)**:
   - Guru dapat menghubungkan akun Google Calendar pada portal ketersediaan (`/mentor/availability`).
   - Sistem membaca status slot waktu *Busy* dan *Free* agenda pribadi guru (Privacy Mode tanpa membaca rincian judul acara pribadi).
   - Jika terdapat agenda sibuk pada jam bimbingan yang diminta santri baru, skor slot otomatis bernilai `0.0` (guru gugur dari rekomendasi dengan keterangan yang jelas).
2. **Arsitektur Controller & Rute**:
   - [`MentorCalendarController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Mentor/MentorCalendarController.php) mengelola alur:
     - `GET /mentor/calendar/connect`: Mengarahkan ke izin Google OAuth2.
     - `GET /mentor/calendar/callback`: Pertukaran token akses dan penyimpanan terenkripsi di `mentor_calendar_syncs`.
     - `POST /mentor/calendar/disconnect`: Pemutusan integrasi kalender secara bersih.
     - `GET /mentor/calendar/sync`: Sinkronisasi slot sibuk on-demand.
3. ⚠️ **Catatan Status Kesiapan API Token Google Calendar**:
   > **INFORMASI PENTING PENGEMBANGAN**:  
   > Seluruh infrastruktur kode, controller, rute web, migrasi basis data, dan penanganan bentrok eksternal pada [`CalendarSyncService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/CalendarSyncService.php) **telah selesai dibangun dan teruji 100%**.  
   > Namun demikian, **API Token Google Calendar saat ini belum diaktifkan di lingkungan produksi** (masih beroperasi dalam *simulation mode / pending credentials* menunggu pendaftaran resmi `GOOGLE_CALENDAR_CLIENT_ID` dan `GOOGLE_CALENDAR_CLIENT_SECRET` pada Google Cloud Console).  
   > Sistem menerapkan *graceful fallback*: jika token belum aktif, tombol antarmuka menampilkan notifikasi panduan tanpa menimbulkan kegagalan (crash) pada halaman ketersediaan guru.

### 5.3 Smart Load Balancing & Proteksi Kelelahan Guru (Burnout Prevention)
Untuk mencegah ketimpangan beban kerja guru populer dan menjaga stabilitas mental pengajar:
1. **Formula Indeks Risiko Kelelahan (Burnout Risk Index)**:
   Dihitung oleh [`SmartLoadBalancerService`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/SmartLoadBalancerService.php):
   $$\text{Burnout Index} = (\text{Rasio Keterisian Slot} \times 50\%) + (\text{Penurunan Rating 30 Hari} \times 30\%) + (\text{Jam Mengajar Berturut-turut} \times 20\%)$$
   Tingkat risiko diklasifikasikan ke dalam 4 tingkatan: *Low* ($\le 40\%$), *Medium* ($41–60\%$), *High* ($61–80\%$), dan *Critical* ($> 80\%$).
2. **Saklar Pembinaan Otomatis (Load Throttling & Cooldown)**:
   - Guru dengan kapasitas penuh ($\ge 100\%$) atau indeks risiko kritis otomatis berstatus `is_throttled = true`.
   - Guru berstatus *throttled* otomatis dikesampingkan dari rekomendasi utama penugasan santri baru sampai beban kerja kembali proporsional.
3. **Otomasi Evaluasi Tengah Malam**:
   - Console scheduler pada [`routes/console.php`](file:///c:/xampp/htdocs/al-hikmah-lms/routes/console.php) mengevaluasi seluruh profil beban kerja guru aktif setiap hari pukul 00:30 WIB (`evaluate-mentor-burnout-daily`).

### 5.4 Vektor Keselarasan Gaya Belajar Santri & Profil Pedagogis Guru (Cosine Similarity)
1. **Kuesioner Gaya Belajar Wali Santri**:
   Pada formulir pendaftaran santri baru ([`resources/views/parent/enrollments/create.blade.php`](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/parent/enrollments/create.blade.php)), wali santri mengisi preferensi 4 pilar belajar:
   - **Visual ($V$)**: Ketertarikan pada gambar, diagram tajwid warna, dan kartu huruf.
   - **Auditori ($A$)**: Kenyamanan menyimak irama tilawah dan talaqqi berulang.
   - **Kinestetik ($K$)**: Kebutuhan gerak aktif dan praktik menulis huruf hijaiyah.
   - **Patience Need ($P$)**: Tingkat kebutuhan guru yang ekstra sabar dan ramah anak.
2. **Aljabar Vektor Cosine Similarity**:
   Kecocokan dihitung secara deterministik internal (zero external cost, waktu eksekusi $< 5$ milidetik) antara vektor santri ($S$) dan vektor kapabilitas guru ($M$):
   $$\text{Sim}(S, M) = \frac{V_s \cdot V_m + A_s \cdot A_m + K_s \cdot K_m + P_s \cdot P_m}{\sqrt{V_s^2 + A_s^2 + K_s^2 + P_s^2} \times \sqrt{V_m^2 + A_m^2 + K_m^2 + P_m^2}} \times 100\%$$
   Skor pedagogi akhir memadukan $70\%$ keselarasan vektor dan $30\%$ rekam jejak retensi santri historis guru (`historical_retention_rate`).

### 5.5 Aturan Syariat Gender Berbasis Usia 10 Tahun
1. **Santri Perempuan**: WAJIB dibimbing oleh **Ustazah (Perempuan)** (Skor Ustadz = $0.0$, diskualifikasi mutlak).
2. **Program Khusus Muslimah**: WAJIB dibimbing oleh **Ustazah (Perempuan)**.
3. **Santri Laki-laki Usia di Bawah 10 Tahun (`age < 10`)**: WAJIB dibimbing oleh **Ustazah (Perempuan)** untuk pendekatan keibuan dan kesabaran usia dini (Skor Ustadz = $0.0$).
4. **Santri Laki-laki Usia 10 Tahun ke Atas (`age >= 10`)**: WAJIB dibimbing oleh **Ustadz (Laki-laki)** untuk pembinaan keteladanan rijalul Qur'an (Skor Ustazah = $0.0$).

### 5.6 Explainable AI (Why Not...? Tooltip Inspection)
Menyajikan transparansi alasan mengapa guru lain tidak masuk ke peringkat 3 Besar:
- *"Santri laki-laki < 10 tahun wajib dibimbing oleh Ustazah (perempuan)."*
- *"Jadwal mentor bentrok dengan santri privat (1-on-1) lain pada hari & jam yang diminta."*
- *"Terdapat agenda pribadi di Google Calendar pada jam tersebut."*
- *"Mentor sedang dalam masa pemulihan beban mengajar (Load Throttling)."*
- *"Status mentor sedang cuti / hari bebas rutin."*
- *"Jarak lokasi melebihi batas ideal Home Visit."*

### 5.7 Family Blacklist Engine & Auto-Assign $\ge 95\%$
- **Family Blacklist**: Jika wali santri pernah mengajukan mutasi/komplain ketidakcocokan terhadap seorang guru di masa lalu (`student_mutation_logs`), guru tersebut otomatis berstatus blacklist untuk keluarga tersebut (Skor = $0.0$).
- **Auto-Assign**: Pendaftaran dengan skor kecocokan sempurna ($\ge 95\%$) dapat otomatis dialokasikan oleh sistem untuk percepatan operasional lembaga.

---

## 🔮 6. PREDICTIVE ANALYTICS & EARLY WARNING SYSTEM (PA-EWS)

Modul analitik masa depan yang mentransformasikan operasional lembaga dari model **reaktif** menjadi **proaktif preskriptif**.

### 6.1 Model 1: Dropout & Churn Risk Prediction (4-Factor Ensemble)
Menghitung probabilitas risiko santri berhenti ($S_{\text{dropout}}$ 0–100%):
1. **Presensi 30 Hari Terakhir ($35\%$)**: Mengukur rasio ketidakhadiran dan absen beruntun.
2. **Kesehatan Finansial & Tagihan ($30\%$)**: Keterlambatan pembayaran SPP dan invoice tertunda.
3. **Stagnasi Progres Hafalan ($20\%$)**: Tidak ada setoran baru atau capaian ayat di bawah target.
4. **Keterlibatan Wali Santri ($15\%$)**: Keaktifan konfirmasi presensi dan pemberian feedback.

Level Risiko:
- 🟢 **Low Risk ($< 40\%$)**: Kondisi belajar stabil.
- 🟡 **Medium Risk ($40\% - 69\%$)**: Perlu perhatian berkala mentor.
- 🔴 **High Risk ($\ge 70\%$)**: Kritis, memicu alert prioritas tinggi ke admin.

### 6.2 Model 2: Learning Velocity & Proyeksi Tanggal Khatam (ETA)
- Menghitung rata-rata kecepatan setoran santri: $V = \frac{\text{Jumlah Ayat Dihafal}}{\text{Hari Aktif}}$.
- Memproyeksikan estimasi tanggal tuntas Juz/Surah saat ini berdasarkan sisa ayat yang belum disetor.

### 6.3 Model 3: Revenue Forecasting 6 Bulan dengan Pengali Musiman
- Menggunakan regresi linier OLS dari 12 bulan data penerimaan kas historis.
- Mengintegrasikan faktor musiman Islami: Ramadhan ($+15\%$), Idul Fitri ($+20\%$), Masa Libur Sekolah ($-15\%$).
- Mengaplikasikan diskon churn rate santri aktif untuk menghasilkan proyeksi kas realistis.

### 6.4 Model 4: Teacher Performance Trend & Early Coaching Triggers
- Menganalisis kemiringan grafik (*slope*) dari 6 bulan snapshot komposit guru.
- Jika terdeteksi tren penurunan performa berturut-turut, sistem memicu notifikasi pembinaan 1-on-1 ke admin sebelum terjadi penurunan kualitas belajar santri.

### 6.5 1-Click WhatsApp Quick Intervention Hub
Admin dapat mengeksekusi pesan silaturahmi langsung ke nomor WhatsApp wali santri hanya dengan 1 klik, menggunakan template terformat dinamis sesuai faktor risiko yang terdeteksi.

---

## 👨‍👩‍👧 7. MODUL PORTAL WALI SANTRI & PENGUNCIAN JADWAL PASCA-BAYAR

### 7.1 Alur Pendaftaran Tanpa Auto-Bayar (Status Waiting Admin)
Ketika wali santri mendaftarkan anaknya melalui modal pendaftaran landing page (`modal-daftar.blade.php`):
- Pendaftaran masuk berstatus **`waiting_admin`** (Menunggu Verifikasi Jadwal Lembaga).
- **Invoice/tagihan TIDAK langsung terbit otomatis** sebelum jadwal diverifikasi oleh admin pengelola.
- Hal ini mencegah situasi orang tua membayar jadwal yang ternyata penuh atau bentrok.

### 7.2 Fitur Edit Hari & Jam Sebelum Pembayaran
- Sebelum pembayaran dilakukan, orang tua memiliki keleluasaan menyesuaikan preferensi hari belajar, estimasi jam, dan metode belajar melalui `/parent/enrollments/{id}/edit`.
- Antarmuka dilengkapi informasi ramah dan panduan ketersediaan.

### 7.3 Kunci Permanen Jadwal Belajar Pasca-Bayar (`isActive()`)
- Begitu pembayaran lunas dan status santri menjadi **Aktif** (`$enrollment->isActive() || $enrollment->payment?->status === 'paid'`):
  - Hari dan jam bimbingan **dikunci permanen (*locked*)**.
  - Akses edit ditutup otomatis di level Controller dan View dengan badge *"🔒 Jadwal Terkunci Permanen"*.
  - Perubahan jadwal pasca-lunas harus melalui permohonan mutasi resmi ke admin lembaga.

### 7.4 Sistem Evaluasi Bimbingan & Rating Multi-Kategori Pasca Sesi (Pending Review & Sinkronisasi Skor Guru)
Untuk menjaga mutu pembelajaran Al-Qur'an dan memberikan wadah apresiasi serta masukan dari orang tua:
1. **Grid Kartu Pending Review di Dashboard Wali Santri**:
   - Jika ananda telah menyelesaikan sesi bimbingan yang belum dinilai, muncul banner interaktif berbingkai kuning emas di `/parent/dashboard` yang memuat hingga 3 sesi bimbingan terakhir.
   - Setiap kartu menyajikan nama ananda, nama guru pembimbing, tanggal sesi, serta tombol aksi *"⭐ Beri Nilai"* yang langsung memicu modal ulasan.
2. **Modal Evaluasi 4 Aspek Penilaian & Quick Chips**:
   - **Rating Bintang Utama (1–5)**: Nilai kepuasan umum bimbingan dengan label deskriptif (*Perlu Perbaikan* s.d. *Sangat Memuaskan*).
   - **Rincian 4 Dimensi Mutu Pembelajaran**:
     - 🗣️ *Kualitas Komunikasi & Kejelasan Penyampaian* (1–5)
     - ⏰ *Ketepatan Waktu Mulai & Berakhirnya Sesi* (1–5)
     - 📖 *Kesesuaian Metode Mengajar & Kesabaran Guru* (1–5)
     - 📈 *Dampak terhadap Progres & Semangat Belajar Anak* (1–5)
   - **Tagar Cepat Interaktif (Quick Chips)**: Wali santri dapat memilih tag sentimen instan: `#SangatSabar`, `#TepatWaktu`, `#PenyampaianJelas`, `#SantriSemangat`, `#MakhrajDetail`, dan `#PerluVariasi`.
   - **Catatan Apresiasi / Masukan & Saklar Anonim**: Kolom pesan tertulis opsional dan opsi kirim ulasan secara anonim (*is_anonymous*) untuk menjaga kenyamanan wali santri.
3. **Sinkronisasi Otomatis Skor Mentor & Peringatan Dini (EWS)**:
   - Nilai rata-rata dari seluruh ulasan langsung memperbarui atribut `rating` pada profil publik guru di tabel `mentors`.
   - Jika guru masih dalam masa percobaan (*probation*), metrik `average_rating` pada `mentor_probation_trackings` ikut tersinkronisasi secara otomatis.
   - Ulasan dengan rating $\le 3$ dalam 7 hari terakhir secara otomatis dideteksi oleh `AlertService` dan masuk ke tab **Warning Alert** di Admin Dashboard (`warn_low_mentor_feedback`) untuk penanganan mutu proaktif.
4. **Integrasi Tombol Rating & Badge Permanen di Daftar Jadwal Belajar (`parent.schedules.list`)**:
   - Pada halaman riwayat dan jadwal sesi bimbingan santri (`/parent/schedules`), setiap sesi yang telah berstatus `completed` kini dilengkapi indikator evaluasi interaktif:
     - Jika sesi bimbingan telah selesai namun belum dinilai, tampil tombol *"⭐ Beri Nilai"* yang langsung memicu modal evaluasi bimbingan pasca-sesi.
     - Jika wali santri telah memberikan ulasan, tombol otomatis berganti menjadi badge permanen elegan *"⭐ {rating}.0 Dinilai"* sehingga wali santri mendapatkan konfirmasi visual yang jelas bahwa penilaian telah terekam.
   - Sesi bimbingan dimuat dengan *eager-loading* relasi `'feedback'` sehingga bebas dari masalah query N+1 dan memuat data secara instan.

### 7.5 Mesin Deteksi Sentimen Komplain & Tiket Intervensi Koordinator Akademik
Sebagai wujud perlindungan mutu pembelajaran dan pencegahan dini mutasi santri:
1. **Analisis Sentimen Otomatis pada Masukan Wali Santri**:
   - Setiap ulasan yang disubmit melalui modal rating pasca sesi dianalisis oleh `MentorFeedbackService::analyzeSentimentAndDetectComplaint`.
   - Kriteria Pemicu Tiket Intervensi:
     - Rating bintang kepuasan $\le 2$ (Tingkat Keparahan: Kritis / Tinggi).
     - Rating bintang $= 3$ dengan deteksi kata kunci komplain (Ketidakhadiran/Keterlambatan: *"terlambat"*, *"telat"*, *"tidak hadir"*, *"absen"*; Sikap/Pedagogi: *"kasar"*, *"marah"*, *"tidak sabar"*; Ketidakpuasan: *"tidak jelas"*, *"minta ganti"*, *"mutasi"*).
2. **Penerbitan Tiket Intervensi Mandiri (`mentor_intervention_tickets`)**:
   - Sistem menerbitkan nomor tiket unik format `TIK-YYYYMMDD-XXXX` dengan tingkat keparahan (`critical`, `high`, `medium`).
   - Tiket otomatis terhubung dengan data mentor, santri, orang tua, ulasan mentah, dan sesi bimbingan terkait.
   - Mengirimkan pesan darurat seketika via WhatsApp Gateway ke Admin / Koordinator Akademik.
3. **Portal Resolusi & Rekonsiliasi Koordinator Akademik (`/admin/intervention-tickets`)**:
   - Dilengkapi filter status navigasi: *Semua*, *Terbuka (Open)*, *Sedang Ditangani (In Progress)*, *Selesai (Resolved)*, dan *Eskalasi ke Mutasi*.
   - Halaman detail tiket menampilkan analisis sentimen, ulasan lengkap, rincian 4 dimensi rating, kartu kontak WhatsApp 1-klik untuk konfirmasi ke orang tua dan pembinaan ke guru.
   - Form pencatatan *Action Plan* (Langkah Mitigasi) dan *Resolution Notes* (Hasil Mediasi).
4. **Eskalasi Terkelola ke Mutasi Santri (Family Blacklist Engine)**:
   - Jika rekonsiliasi menemui jalan buntu dan orang tua menghendaki pergantian guru, admin dapat mengeksekusi tombol *"Eskalasi ke Mutasi Santri"*.
   - Tiket berpindah status ke `escalated_to_mutation`, dan sistem mencatatkan entri resmi pada `student_mutation_logs` dengan alasan `dissatisfaction` serta menautkan nomor tiket intervensi untuk mengaktifkan Family Blacklist Engine bagi keluarga santri tersebut.
5. **Sinkronisasi 4 Dashboard Terpadu (Single Source of Truth)**:
   - **Dashboard Admin**: Menampilkan metrik tiket terbuka di tab *"Tiket Intervensi"* pada panel pemantauan wali santri serta tombol aksi cepat.
   - **Dashboard Wali Santri**: Menampilkan kartu status tindak lanjut masukan yang menenangkan orang tua bahwa aduannya sedang aktif ditangani Koordinator Akademik.
   - **Dashboard Guru/Mentor**: Menampilkan catatan pembinaan & evaluasi mutu akademik konstruktif saat ada arahan koordinator.
   - **Dashboard Santri**: Menampilkan banner guru pembimbing binaan aktif yang menenangkan psikologis belajar ananda.

---

## 🎮 8. MODUL RUANG BELAJAR SANTRI & GAMIFIKASI ISLAMI TERPADU

### 8.1 Sistem Poin Fastabiqul Khoirot
- Setoran Hafalan Harian: `+10 Poin` (`+5 Poin` jika tajwid Mumtaz $\ge 90$)
- Muroja'ah Mandiri 1 Juz: `+25 Poin`
- Target Mingguan Tuntas: `+50 Poin`
- Streak Istiqomah 7 Hari: `+100 Poin` | 30 Hari: `+300 Poin`
- Khatam 1 Juz Baru: `+500 Poin` | Khatam 30 Juz: `+5.000 Poin`

### 8.2 15 Lencana Prestasi Santri (B01–B15) & Peta 30 Juz
Visualisasi interaktif peta capaian 30 Juz Al-Qur'an (Surat, Ayat, Persen Mutqin) dan katalog lencana Islami yang memotivasi santri istiqomah belajar.

---

## 🔐 9. OTOMASI AKUN SANTRI & KEBIJAKAN KEAMANAN PASSWORD

### 9.1 Format Email Bersih Bebas Karakter Acak
Setiap santri baru otomatis dibuatkan akun dengan format email profesional tanpa karakter acak:
$$\text{Format:} \quad \text{namalengkapbersih}@\text{alhikmah.com} \quad (\text{contoh: } \text{hikmatulhasanah@alhikmah.com})$$

### 9.2 Password Default `santri123` & Banner Peringatan Keamanan
- Password awal santri distandarisasi menjadi `santri123`.
- Kredensial akun santri ditampilkan secara transparan di dashboard orang tua.
- Jika santri login dengan password default, muncul banner peringatan keamanan yang mengarahkan santri mengganti password demi privasi akun.

---

## 📊 10. STANDARISASI UNIVERSAL DATATABLES BERBASIS ASET LOKAL

Seluruh tabel data di Al-Hikmah LMS telah distandarisasi menggunakan aset lokal resmi:
- **Lokasi Aset**: `public/assets/DataTables/datatables.min.css`, `public/assets/DataTables/datatables.min.js`, dan `public/assets/js/datatables-init.js`.
- **Bebas Ketergantungan CDN & Bebas Error jQuery**: Inisialisasi menggunakan Vanilla JS observer pada class `.datatable` dengan konfigurasi responsif, pencarian multi-kolom, dan paginasi (10, 25, 50, Semua).
- **Query Controller**: Menggunakan query murni `->get()` pada tabel client-side (seperti `/parent/schedules/list`) untuk menghilangkan bentrok pagination Blade dengan pagination DataTables.

---

## 📰 11. MODUL BLOG & LITERASI EDUKASI ISLAMI
Manajemen artikel dakwah, panduan tajwid, fiqih ibadah, tips menghafal Al-Qur'an, dan wawasan Islam terpadu dengan editor WYSIWYG dan taksonomi kategori/tagar.

---

## 🕌 12. FITUR JADWAL SHOLAT & KOMPAS ARAH KIBLAT REAL-TIME
Integrasi API Kementerian Agama Republik Indonesia untuk jadwal sholat 5 waktu otomatis berdasarkan lokasi santri/guru, serta kompas kiblat interaktif berbasis sensor kompas perangkat.

---

## 💳 13. INTEGRASI PAYMENT GATEWAY PAKASIR & INVOICE REAL-TIME
Penerbitan tagihan biaya pendaftaran dan SPP bulanan otomatis, QRIS, Virtual Account, serta penanganan webhook notifikasi pembayaran real-time yang langsung mengaktifkan kelas santri.

---

## ⭐ 14. MATRIKS HAK AKSES PENGGUNA (ROLE PERMISSION MATRIX)

```
+-------------------------------------------------------------------------------------------------------------------------+
| MATRIKS HAK AKSES PENGGUNA AL-HIKMAH LMS (v8.9)                                                                         |
+-------------------------------------------------------------------------------------------------------------------------+
| Fitur / Modul                         | Calon Guru (App) | Guru (Probation) | Guru (Resmi) | Wali Santri | Santri | Admin |
+-------------------------------------------------------------------------------------------------------------------------+
| Pendaftaran & Tracking Seleksi Guru   |        ✅         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Pengerjaan Ujian Kompetensi Online    |        ✅         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| 7-Tab Recruitment ATS & 1-Klik Verif  |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Dashboard Utama Operasional           |        ❌         |        ✅        |      ✅      |     ✅      |   ✅   |   ✅   |
| Widget 4 Pilar Orientasi Probation Hub|        ❌         |        ✅        |      ❌      |     ❌      |   ❌   |   ✅   |
| Evaluasi Kelulusan & Lencana M01      |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Bank Soal & Multi-AI Generator PDF    |        ❌         |        ❌        |      ✅      |     ❌      |   ❌   |   ✅   |
| Input Angka Slot 0-6 & Libur Rutin    |        ❌         |        ✅        |      ✅      |     ❌      |   ❌   |   ✅   |
| Catat Rapor Mutaba'ah & Progres       |        ❌         |        ✅        |      ✅      |     ❌      |   ❌   |   ✅   |
| Scorecard Kinerja & Goal Setting Guru |        ❌         |        ✅        |      ✅      |     ❌      |   ❌   |   ✅   |
| Edit Preferensi Jadwal (Sebelum Bayar)|        ❌         |        ❌        |      ❌      |     ✅      |   ❌   |   ✅   |
| Konfirmasi Presensi Anak              |        ❌         |        ❌        |      ❌      |     ✅      |   ❌   |   ❌   |
| Ulasan & Rating Multi-Kategori Pasca  |        ❌         |        ❌        |      ❌      |     ✅      |   ❌   |   ✅   |
| Dashboard Predictive Analytics (PA-EWS)|       ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Day-Off Monitoring Center Guru        |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Alokasi Guru OPSI A/B (Anti-Bentrok)  |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| 1-Click WhatsApp Early Intervention   |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Revenue Forecasting 6 Bulan           |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Export Laporan Prediktif (Excel/CSV)  |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
| Pusat Peringatan Operasional 3-Tier   |        ❌         |        ❌        |      ❌      |     ❌      |   ❌   |   ✅   |
+-------------------------------------------------------------------------------------------------------------------------+
```

---

## 🔔 15. SISTEM NOTIFIKASI & ALERT TERPUSAT (CENTRALIZED ALERT SYSTEM)

1. **Notifikasi Dalam Aplikasi (In-App)**: Indikator lonceng dengan badge counter belum dibaca di navbar.
2. **Konektor WhatsApp Gateway**: Notifikasi pendaftaran, tagihan, jadwal belajar, dan pesan intervensi dini otomatis.
3. **Pusat Peringatan 3-Tier Admin (`AlertService.php`)**:
   - 🔴 **Kritis**: Santri risiko dropout $\ge 70\%$, tagihan overdue $>30$ hari, beban guru overload, dan **Tiket Intervensi Komplain Wali Santri Belum Ditangani (`crit_open_intervention_tickets`)**.
   - 🟡 **Perhatian**: Tagihan mendekati jatuh tempo, cuti guru, santri absen beruntun, ulasan feedback rendah $\le 3$ (`warn_low_mentor_feedback`).
   - 🟢 **Info**: Pendaftaran santri baru, khatam juz, ulasan bintang 5.

---

## 🗄️ 16. PENJELASAN SELURUH DATABASE (58 TABEL UTAMA)

Basis data relasional MySQL/MariaDB Al-Hikmah LMS mencakup 58 tabel terindeks:

1. `users`: Akun pengguna dan autentikasi.
2. `roles`: Master peran pengguna (admin, mentor, parent, student).
3. `programs`: Master program belajar Al-Qur'an dan biaya paket.
4. `articles`: Artikel dakwah dan konten blog edukasi.
5. `blog_categories`: Kategori artikel blog.
6. `blog_tags`: Tagar taksonomi artikel.
7. `article_tag`: Pivot relasi artikel dan tagar.
8. `questions`: Bank soal evaluasi santri (pilihan ganda dan essay).
9. `students`: Profil data santri dan agregat gamifikasi.
10. `mentors`: Profil guru Al-Qur'an, sanad, spesialisasi, dan rating.
11. `parent_profiles`: Profil dan nomor darurat wali santri.
12. `enrollments`: Data pendaftaran, pilihan hari/jam, status verifikasi, dan status lunas.
13. `learning_sessions`: Jadwal dan log sesi bimbingan belajar.
14. `progress`: Catatan mutaba'ah hafalan santri (surat, ayat, adab).
15. `payments`: Riwayat invoice tagihan, status bayar, dan referensi payment gateway.
16. `galleries`: Dokumentasi foto kegiatan halaqah belajar.
17. `gallery_categories`: Kategori album galeri kegiatan.
18. `mentor_availabilities`: Data ketersediaan angka slot (0–6), status libur rutin mingguan (`is_holiday`), dan catatan mentor.
19. `mentor_student`: Pivot penugasan santri privat ke guru (memuat `slot_number`, `time_assigned`, `time_label`, `day_assigned`, `is_active`).
20. `messages`: Log komunikasi pesan mentor dan orang tua.
21. `session_confirmations`: Log konfirmasi kehadiran sesi oleh wali santri.
22. `mentor_activity_logs`: Rekam jejak aktivitas operasional guru.
23. `mentor_leaves`: Pengajuan cuti tanggal tertentu dan penunjukan guru pengganti.
24. `contact_messages`: Pesan formulir kontak publik landing page.
25. `settings`: Pengaturan global konfigurasi lembaga.
26. `notifications`: Data notifikasi in-app pengguna.
27. `badges`: Master katalog lencana Islami (B01–B15, M01–M07).
28. `student_badges`: Pivot perolehan lencana santri.
29. `hifz_targets`: Target hafalan harian santri.
30. `juz_progress`: Peta capaian hafalan 30 Juz santri.
31. `hifz_milestones`: Target capaian jangka panjang santri.
32. `leaderboard_snapshots`: Snapshot peringkat santri harian/mingguan.
33. `gamification_points`: Buku besar riwayat poin santri.
34. `password_reset_logs`: Log audit reset password akun santri.
35. `financial_audit_logs`: Log audit transaksi dan perubahan finansial.
36. `mentor_applications`: Berkas lamaran calon guru Al-Qur'an (memuat `interview_scheduled_at`, `interview_meeting_link`, `interview_type`, `interview_notes`, dengan indeks komposit `status` & `current_stage`).
37. `mentor_tests`: Sesi ujian kompetensi online calon guru (paket 15 soal kurasi/AI).
38. `mentor_probation_trackings`: Evaluasi masa percobaan 90 hari guru baru (memuat `application_id`, `orientation_modules_status`, `last_synced_at`, `attendance_rate`, `average_rating`).
39. `matching_logs`: Log audit pencocokan Smart Matchmaking AI.
40. `student_mutation_logs`: Log komplain wali santri (Family Blacklist Engine).
41. `mentor_performance_snapshots`: Snapshot bulanan skor komposit guru.
42. `mentor_feedback`: Master ulasan dan rating bimbingan dari wali santri (memuat `session_id`, `overall_rating`, `quick_tags`, `is_anonymous`, `comment`, dan `mentor_response`).
43. `mentor_feedback_ratings`: Rincian rating 4 dimensi penilaian guru (komunikasi, ketepatan waktu, metode mengajar, progres anak).
44. `mentor_insights`: Ringkasan AI coaching dan rencana aksi preskriptif.
45. `mentor_goals`: Target kinerja pribadi guru.
46. `mentor_self_assessments`: Formulir evaluasi diri bulanan guru.
47. `mentor_incentives`: Catatan insentif dan kelayakan bonus finansial guru.
48. `learning_session_methods`: Pelacakan metode belajar per sesi (Learning Analytics).
49. `student_dropout_predictions`: Snapshot harian risiko churn santri (PA-EWS).
50. `student_learning_velocities`: Analisis laju hafalan (ayat/hari) dan proyeksi khatam.
51. `revenue_forecasts`: Proyeksi arus kas 6 bulan berbasis regresi linier dan musim.
52. `predictive_analytics_audit_logs`: Audit trail intervensi WhatsApp dan ekspor laporan.
53. `mentor_intervention_tickets`: Master tiket intervensi & penanganan komplain wali santri (memuat `ticket_number`, `mentor_id`, `student_id`, `parent_id`, `session_id`, `feedback_id`, `severity`, `complaint_category`, `status`, `action_plan`, `resolution_notes`, `handled_by`, `resolved_at`).
54. `mentor_calendar_syncs`: Integrasi OAuth2 kalender eksternal (Google/Outlook/iCal), sync token, status privasi, dan cache slot jam sibuk agenda pribadi guru.
55. `mentor_load_balance_profiles`: Profil kapasitas beban kerja mengajar guru, indeks risiko burnout, saklar throttling perlindungan kelelahan, dan riwayat masa pembinaan (*coaching cooldown*).
56. `student_learning_styles`: Vektor preferensi gaya belajar santri (skor Visual, Auditori, Kinestetik, kebutuhan kesabaran guru, dan tempo belajar).
57. `mentor_pedagogical_profiles`: Profil kapabilitas pedagogis guru (skala Visual, Auditori, Kinestetik, Kesabaran, dan tingkat retensi historis).
58. `mentor_student_match_histories`: Rekam jejak kecocokan historis penugasan santri ke guru pembimbing, durasi pekan retensi, dan riwayat permohonan mutasi.

---

## 🧠 17. PENJELASAN SELURUH MODEL, SERVICE, & CONTROLLER INTI

### A. Model Eloquent Kunci:
- [`Mentor.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Mentor.php): Dilengkapi method [`hasScheduleConflict()`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Mentor.php#L195) dan [`isAvailableForSchedule()`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Mentor.php#L237) dengan relasi pivot lengkap `mentor_student`, `interventionTickets`, `calendarSync`, `loadBalanceProfile`, `pedagogicalProfile`, dan `matchHistories`.
- [`MentorAvailability.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorAvailability.php): Master konstanta slot 0–6 (`SLOT_MAP`), mapping hari, konversi waktu ke angka slot (`getSlotNumberFromTime`), dan method `hasSlot()`.
- [`Enrollment.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Enrollment.php): Logika alur status pendaftaran, sinkronisasi kuota lunas (`syncToMentorStudent`), dan kalkulasi tanggal sesi perdana (`calculateFirstSessionDate`).
- [`Student.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Student.php): Profil santri, relasi pivot dengan mentor, pemantauan gamifikasi, dan relasi `learningStyle`.
- [`MentorCalendarSync.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorCalendarSync.php): Manajemen kredensial OAuth2 Google Calendar terenkripsi otomatis, status koneksi, dan cache jadwal sibuk.
- [`MentorLoadBalanceProfile.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorLoadBalanceProfile.php): Model profil beban mengajar, batas kapasitas santri aktif, skor risiko burnout, dan cooldown pembinaan.
- [`StudentLearningStyle.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/StudentLearningStyle.php): Model profil gaya belajar santri berdasarkan kuesioner wali santri saat pendaftaran.
- [`MentorPedagogicalProfile.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorPedagogicalProfile.php): Model karakteristik pedagogis guru dan persentase retensi historis.
- [`MentorStudentMatchHistory.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorStudentMatchHistory.php): Model histori kecocokan santri dan guru untuk evaluasi retensi jangka panjang.
- [`MentorApplication.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorApplication.php): Siklus hidup lamaran guru, tahapan seleksi (stage 1–5), data interview, berkas dokumen, dan stempel verifikasi.
- [`MentorProbationTracking.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorProbationTracking.php): Pelacakan evaluasi masa percobaan 90 hari, sinkronisasi metrik kehadiran aktual, rating wali santri, dan status modul orientasi.
- [`MentorFeedback.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorFeedback.php): Model ulasan pasca sesi bimbingan dengan relasi rincian kategori rating multi-dimensi dan `interventionTicket`.
- [`MentorInterventionTicket.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/MentorInterventionTicket.php): Model tiket intervensi penanganan komplain wali santri dengan relasi mentor, student, parent, feedback, session, handler, serta helper badge status & severity.

### B. Service Layer Utama:
- [`MentorRecruitmentService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorRecruitmentService.php): Manajemen pipeline seleksi calon guru, 1-klik verifikasi berkas atomik (`approveDocumentAndScheduleTest`), integrasi paket 15 soal kompetensi, penjadwalan wawancara, dan disposisi pesan resmi WhatsApp.
- [`MentorProbationService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorProbationService.php): Sinkronisasi live metrik LMS kehadiran aktual dan rating ulasan (`syncTrackingStats`), tata kelola keputusan evaluasi kelulusan (`evaluateProbation`), perpanjangan masa percobaan, dan penghargaan lencana `M01 - Mentor Certified`.
- [`MentorFeedbackService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorFeedbackService.php): Pengelolaan ulasan pasca sesi bimbingan, analisis sentimen otomatis (`analyzeSentimentAndDetectComplaint`) pendeteksi komplain, penerbitan tiket intervensi otomatis ke koordinator akademik, notifikasi darurat WhatsApp Admin, dan pembaruan otomatis rating profil guru.
- [`MentorAccountService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorAccountService.php): Otomasi pembuatan/aktivasi akun mentor saat disetujui, inisialisasi relasi `mentor_probation_trackings`, dan pengiriman kredensial login via WhatsApp.
- [`MentorAvailabilityService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorAvailabilityService.php): Penyimpanan ketersediaan slot, parser format WhatsApp, dan validasi kapasitas slot privat 1-on-1.
- [`CalendarSyncService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/CalendarSyncService.php): Integrasi kalender eksternal, penanganan alur OAuth2 Google Calendar, sinkronisasi token, dan validasi irisan agenda sibuk pribadi dengan jadwal santri.
- [`SmartLoadBalancerService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/SmartLoadBalancerService.php): Evaluasi rasio beban aktif mentor, kalkulasi Burnout Risk Index, dan aktivasi otomatis throttling perlindungan kelelahan guru.
- [`MentorMatchingService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/MentorMatchingService.php): Smart Matchmaking AI v3.0 (6 kriteria multi-dimensi berbobot, Cosine Similarity preferensi belajar santri vs pedagogis mentor, integrasi kalender eksternal, proteksi burnout throttling, aturan gender 10 tahun, dan evaluasi multi-hari).
- [`AlertService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/AlertService.php): Pemindaian anomali operasional 3-tier harian lembaga, termasuk deteksi tiket intervensi komplain terbuka (`crit_open_intervention_tickets`) dan feedback rendah ($\le 3$).
- [`DropoutPredictionService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/PredictiveAnalytics/DropoutPredictionService.php): Kalkulasi 4 pilar skor risiko dropout santri.
- [`LearningVelocityService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/PredictiveAnalytics/LearningVelocityService.php): Analitik kecepatan setoran dan estimasi khatam.
- [`RevenueForecastService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/PredictiveAnalytics/RevenueForecastService.php): Proyeksi pendapatan 6 bulan dengan regresi linier.

### C. Controller Utama:
- [`Admin\AdminInterventionTicketController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/AdminInterventionTicketController.php): Manajemen resolusi tiket intervensi komplain wali santri (navigasi status tab, rincian keluhan, pencatatan rencana aksi mitigasi, hasil mediasi, dan eskalasi ke mutasi santri / Family Blacklist).
- [`Admin\AdminRecruitmentController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/AdminRecruitmentController.php): Manajemen navigasi 7-tab ATS pelamar, verifikasi berkas 1-klik, penjadwalan tes/wawancara, dan keputusan penerimaan.
- [`Admin\AdminProbationController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/AdminProbationController.php): Monitoring masa percobaan guru baru, 1-klik sinkronisasi metrik aktual LMS (`syncLiveMetrics`), dan penentuan keputusan kelulusan.
- [`Parent\ParentFeedbackController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Parent/ParentFeedbackController.php): Pemrosesan ulasan pasca sesi bimbingan dari wali santri dengan validasi multi-rating dan opsi anonim.
- [`Admin\EnrollmentController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/EnrollmentController.php): Penugasan santri, filter ketersediaan mentor OPSI A (`$availableMentorsForOptionA`), validasi bentrok di `accept()`, inspeksi visual Pedagogy & Load Balancing score, dan pengajuan jadwal alternatif OPSI B.
- [`Mentor\AvailabilityController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Mentor/AvailabilityController.php): Pengaturan jadwal slot 0–6, penetapan hari bebas, dan inspeksi indikator beban kerja guru.
- [`Mentor\MentorCalendarController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Mentor/MentorCalendarController.php): Penanganan integrasi Google Calendar (koneksi OAuth, callback, pemutusan akun, dan sinkronisasi manual).
- [`Admin\MentorAvailabilityController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/MentorAvailabilityController.php): Matriks 7 hari keterisian slot guru dan quick assign santri.
- [`Parent\EnrollmentController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Parent/EnrollmentController.php): Pendaftaran santri baru dengan kuesioner 4 dimensi gaya belajar anak, edit jadwal sebelum bayar, dan proteksi lock pasca-bayar.

---

## ⚙️ 18. CONSOLE COMMANDS & BACKGROUND SCHEDULER

1. `php artisan probation:daily-sync --notify`: Sinkronisasi harian metrik aktual LMS untuk guru masa percobaan (probation) dan pengiriman peringatan evaluasi H-14 ke WhatsApp Admin jika KPI belum terpenuhi (Pukul 00:00 WIB).
2. `php artisan analytics:snapshot-predictive`: Menghitung snapshot harian analitik prediktif (Pukul 01:00 WIB).
3. `php artisan mentor:snapshot-performance`: Menghitung skor komposit bulanan guru (Tiap tgl 1 pkl 00:05 WIB).
4. `php artisan gamification:refresh-leaderboard`: Snapshot peringkat santri harian (Pukul 00:00 WIB).
5. `php artisan alerts:scan`: Memindai anomali sistem 3 kali sehari (Pukul 06:00, 12:00, 18:00 WIB).
6. `php artisan queue:work`: Memproses antrean pesan WhatsApp dan email secara asynchronous.
7. `Schedule Task (00:30 WIB - evaluate-mentor-burnout-daily)`: Evaluasi harian indeks kelelahan dan Smart Load Balancing untuk seluruh mentor aktif (`SmartLoadBalancerService::calculateBurnoutIndex`).

---

## 📁 19. STRUKTUR FOLDER PROYEK

```
al-hikmah-lms/
├── app/
│   ├── Actions/Mentors/AssignStudentAction.php      # Action Alokasi Santri
│   ├── Console/Commands/                            # Scheduled Artisan Commands (DailyProbationSyncCommand)
│   ├── Enums/                                       # Enum Status Pendaftaran, Tipe Alert, Notifikasi
│   ├── Http/Controllers/
│   │   ├── Admin/                                   # Controller Admin (Tickets, Enrollment, Analytics, Mentors)
│   │   ├── Mentor/                                  # Controller Mentor (Availability, Sessions, Questions)
│   │   └── Parent/                                  # Controller Orang Tua (Enrollment, Children, Feedbacks)
│   ├── Models/                                      # 58 Eloquent Models
│   └── Services/                                    # Service Layer Bisnis & AI Matching
├── database/
│   ├── migrations/                                  # Migrasi Skema Basis Data Lengkap
│   └── seeders/                                     # Database Seeders Lengkap
├── public/
│   └── assets/
│       ├── DataTables/                              # DataTables Lokal Resmi (CSS & JS)
│       └── js/datatables-init.js                    # Inisialisasi Universal DataTables
├── resources/views/
│   ├── admin/                                       # Tampilan Portal Manajemen Admin
│   ├── mentor/                                      # Tampilan Portal Pengajar / Guru
│   ├── parent/                                      # Tampilan Portal Wali Santri
│   └── student/                                     # Tampilan Portal Ruang Belajar Santri
└── routes/
    ├── console.php                                  # Penjadwalan Cron Job
    └── web.php                                      # Rute Web Aplikasi
```

---

## 👤 20. MODUL MANAJEMEN PROFIL MULTI-ROLE & SINKRONISASI LOKASI TERPUSAT

Modul Manajemen Profil Multi-Role (v8.9) menyediakan antarmuka terpersonalisasi, modern, dan aman bagi keempat peran utama dalam sistem (**Admin**, **Guru/Mentor**, **Orang Tua/Parent**, dan **Santri/Student**).

### 20.1 Matriks Fungsionalitas Profil Per Peran:

| Fitur Profil | Administrator (`admin`) | Pengajar / Guru (`mentor`) | Wali Santri (`parent`) | Santri (`student`) |
| :--- | :---: | :---: | :---: | :---: |
| **Foto Avatar Kustom** | ✅ (Maks. 2MB) | ✅ (Maks. 2MB) | ✅ (Maks. 2MB) | ✅ (Maks. 2MB) |
| **Fallback Avatar Islami** | ✅ Initial (#0d7a3e) | ✅ Initial (#0d7a3e) | ✅ Initial (#0d7a3e) | ✅ Initial (#0d7a3e) |
| **Nama Lengkap & Panggilan** | ✅ Nama Lengkap | ✅ Nama Lengkap | ✅ Nama Lengkap | ✅ Lengkap & *Nickname* |
| **Email & Nomor WhatsApp** | ✅ Email & No HP | ✅ Email & No HP | ✅ Email & Kontak Darurat | 🔒 Email Readonly |
| **Biodata & Spesialisasi** | ❌ | ✅ Bio, Sanad & Kurikulum | ❌ | ❌ |
| **Portofolio Rekrutmen Guru**| ❌ | ✅ **Lengkap (Sinkron `/bergabung`)** | ❌ | ❌ |
| **Dokumen CV & Sertifikat** | ❌ | ✅ **Unduh & Update Mandiri** | ❌ | ❌ |
| **Alamat Domisili Keluarga** | ❌ | ✅ Alamat Domisili & Kota | ✅ **Single Source of Truth** | 🔒 Otomatis Terwariskan |
| **Tautan Peta (Maps Share Link)** | ❌ | ❌ | ✅ Fleksibel (Google/Waze/OSM) | 🔒 Otomatis Terwariskan |
| **Rekening Honor Mengajar** | ❌ | ✅ Langsung Aktif Mandiri | ❌ | ❌ |
| **Keamanan Ganti Password** | ✅ Password Lama+Baru | ✅ Password Lama+Baru | ✅ Password Lama+Baru | ✅ Mandiri & Log Audit |
| **KPI Gamifikasi & Ruang Belajar**| ❌ | ❌ | ❌ | ✅ Poin, Streak, & Lencana |

```mermaid
graph TD
    subgraph "Single Source of Location & Navigation Flow"
        P["Wali Santri (Parent)"] -->|"Input Alamat & Link Share Peta"| DB[("Database: parents.maps_link")]
        DB -->|"Inheritance Otomatis ($student->maps_link)"| S["Profil Ruang Belajar Santri"]
        DB -->|"Tombol '🗺️ Buka Rute Peta Lokasi'"| M1["Detail Santri Guru (mentor.students.show)"]
        DB -->|"Link Rute Google Maps"| M2["Jadwal Mengajar Guru (mentor.sessions.index)"]
    end
    subgraph "Recruitment to Profile Synchronization Flow"
        B["Form Pendaftaran /bergabung"] -->|"Pendidikan, Hafalan, Sanad, CV, Sertifikat"| MA[("Database: mentor_applications & mentors")]
        MA -->|"Tampil Lengkap & Terkelola Mandiri"| MP["Profil Guru (/mentor/profile)"]
    end
```

### 20.2 Keunggulan Teknis & Desain:
1. **Single Source of Location Truth**: Data alamat dan tautan peta diinput satu kali oleh orang tua pada menu `parent.profile.edit`, lalu secara otomatis diwariskan ke semua profil anak santri (`$student->maps_link` & `$student->effective_address`). Santri tidak perlu menginput ulang dan data selalu sinkron.
2. **Fleksibilitas Tautan Peta (*Share Link*) & Fitur Uji Interaktif**: Sistem menerima tautan peta navigasi fleksibel (`http://` atau `https://` dari Google Maps, Apple Maps, Waze, maupun OpenStreetMap) dengan tombol interaktif **"Uji Titik Peta"** yang membuka rute baru pada tab browser terpisah sebelum disimpan.
3. **Akses Navigasi Langsung Mentor (1-Click Map Navigation)**: Pada jadwal sesi belajar tatap muka/offline di `mentor.sessions.index`, guru disajikan tombol rute peta langsung yang terhubung ke lokasi rumah santri (diwariskan dari wali santri), mempermudah mobilisasi guru privat tanpa perlu bertanya ulang.
4. **Pencairan Honor Guru Mandiri (*Direct Banking*)**: Guru dapat memperbarui nomor rekening dan nama bank langsung di profilnya, yang seketika aktif untuk pencairan honor mengajar tanpa hambatan birokrasi *approval* berulang.
5. **Sinkronisasi Portofolio Rekrutmen Penuh (`/bergabung` ↔ `/mentor/profile`)**: Seluruh data kualifikasi yang diisi ustadz/ustazah saat mendaftar (Pendidikan Terakhir, Institusi/Kampus, Tanggal Lahir, Jenis Kelamin, Kota/Kabupaten, Jumlah Hafalan Juz, Pengalaman Mengajar, Silsilah Sanad, serta berkas CV & Sertifikat) tampil lengkap di profil guru, dapat diunduh, dan diperbarui kapan saja secara mandiri.
6. **Sinkronisasi Data Dua Arah & FormRequest Terpusat**: Seluruh pembaruan profil divalidasi oleh `ProfileUpdateRequest` dengan pesan validasi berbahasa Indonesia yang bersahabat. Pembaruan nama oleh santri secara otomatis menyinkronkan data `users.name` dan `students.full_name` sehingga riwayat sertifikat dan absensi tetap konsisten.
7. **Keamanan Unggah Avatar & Fallback Islami**: Validasi ketat ukuran berkas maksimal 2MB (format JPG, PNG, WEBP) dengan instant JavaScript preview (`window.previewAvatar`). Jika pengguna belum mengunggah foto, sistem menyajikan avatar fallback UI-Avatars berlatar warna primer kebanggaan Al-Hikmah `#0d7a3e` yang elegan.
8. **Dukungan Penuh Dark Mode & Kompatibilitas Style.css**: Seluruh kartu profil, badge, input form, dan preview foto telah disesuaikan dengan skema warna gelap (*dark theme*) Al-Hikmah LMS melalui integrasi di `public/assets/css/style.css` dan `public/assets/js/scripts.js`.
9. **Ganti Password Santri dengan Audit Trail**: Santri dapat mengganti password *default* `santri123` menjadi password pribadi rahasia secara mandiri, di mana setiap perubahan tercatat rapi pada tabel audit `password_reset_logs` untuk menjaga keamanan akun.

---

## 🧪 21. HASIL PENGUJIAN OTOMATIS & QUALITY ASSURANCE (100% GREEN PASS)

Sistem telah diuji secara komprehensif menggunakan framework pengujian **Pest PHP** dengan cakupan pengujian lengkap terhadap seluruh modul operasional, finansial, analitik, dan manajemen profil:

```bash
# Hasil Uji Fitur Utama (Pest PHP):
   PASS  tests/Feature/Mentor/MentorOrientationTest.php (6 tests, 29 assertions)
   PASS  tests/Unit/AhpRankingServiceTest.php (4 tests, 23 assertions)
   PASS  tests/Feature/AdminAhpRankingTest.php (9 tests, 32 assertions)
   PASS  tests/Feature/AdminMentorProfileDetailTest.php (6 tests, 21 assertions)
   PASS  tests/Feature/AdminMentorProfileDetailTest.php (6 tests, 21 assertions)
   PASS  tests/Feature/ProfileManagementTest.php (12 tests, 88 assertions)
   PASS  tests/Feature/MentorRecruitment/StatusTrackerDesignTest.php (5 tests, 21 assertions)
   PASS  tests/Feature/AboutPageDesignTest.php (1 test, 19 assertions)
   PASS  tests/Feature/Admin/DailyProbationSyncCommandTest.php (3 tests, 9 assertions)
   PASS  tests/Feature/Parent/ComplaintSentimentInterventionTest.php (4 tests, 30 assertions)
   PASS  tests/Feature/MentorRecruitment/RecruitmentPipelineTest.php (4 tests, 32 assertions)
   PASS  tests/Feature/MentorRecruitment/ProbationLiveMetricsTest.php (4 tests, 23 assertions)
   PASS  tests/Feature/Parent/ParentFeedbackFlowTest.php (5 tests, 24 assertions)
   PASS  tests/Feature/Admin/AdminDashboardIntegrationTest.php (4 tests, 21 assertions)
   PASS  tests/Feature/MentorRecruitment/AIEvaluationTest.php (2 tests, 8 assertions)
   PASS  tests/Feature/MentorRecruitment/RecruitmentFlowTest.php (7 tests, 27 assertions)
   PASS  tests/Feature/MentorRecruitment/ProbationTrackingTest.php (5 tests, 31 assertions)
   PASS  tests/Feature/MentorMatchingServiceTest.php (9 tests, 34 assertions)
   PASS  tests/Feature/MentorMatchingV3Test.php (4 tests, 12 assertions)
   PASS  tests/Feature/MentorMatchingServiceTest.php (9 tests, 34 assertions)
   PASS  tests/Feature/MentorAvailabilityTest.php (11 tests, 56 assertions)
   PASS  tests/Feature/Admin/PredictiveAnalyticsDashboardTest.php (5 tests)
   PASS  tests/Feature/Admin/MentorPerformanceDashboardTest.php (10 tests)
   PASS  tests/Feature/StudentDashboardTest.php (10 tests)
   PASS  ... (Seluruh Modul Operasional, Gamifikasi, AI Soal, Finansial, ATS LMS, Intervensi Sentimen, Profil Multi-Role, & Smart Matchmaking v3.0)

   Tests:    100% Green Pass (415 Tests Passed, 1845 Assertions)
   Code Style: vendor/bin/pint --format agent (100% PSR-12 / Laravel Compliant)
```

---

## 🎨 22. STANDARISASI ANTARMUKA PUBLIK & PENYELARASAN FRONTEND ANTISLOP-UI

Sebagai bagian dari komitmen terhadap mutu visual premium, kejelasan alur pengguna (*user journey*), dan penegakan prinsip panduan **`/antislop-ui`**, **`/antislop-copywriting`**, dan **`/antislop-layoutmobile`**, seluruh antarmuka publik dan dashboard operasional telah diselaraskan dengan standar arsitektur desain:

### 22.1 Penyelarasan Halaman Tentang Kami (`/tentang-kami`):
1. **Subpage Header / Hero Standar**:
   - Mengadopsi kombinasi arsitektur `<section class="breadcrumb_bg page-hero">` dengan wrapper `.breadcrumb_iner_item` dan atribut interaktif `data-reveal` yang seragam dengan halaman program, biaya, dan tahfidz.
   - Mengintegrasikan `.text-gradient` pada penekanan judul, `.section-badge` resmi dengan ikon kompas, serta breadcrumb navigasi.
2. **Sinkronisasi Anchor Dropdown Navbar & Active Nav Tracker**:
   - Section telah disesuaikan menjadi `id="filosofi"` (Pendekatan Utama) dan `id="nilai"` (Standar Kualifikasi Guru) yang terhubung langsung dengan menu dropdown *Tentang Kami* pada navbar.
   - Fitur *Smooth Scroll* dan *Active Nav Tracker* pada `public/assets/js/scripts.js` otomatis mengaktifkan menu saat pengguna melakukan scroll.
3. **Penerapan Komponen Desain Resmi dari `public/assets/css/style.css`**:
   - **Rekap Data Riil**: Menggunakan komponen resmi `.lms-stat-card`, `.stat-icon-wrap`, `.stat-value`, `.stat-label`, dan `.stat-meta` dari Section 5 `style.css` yang terikat langsung ke data riil `$totalStudents`, `$totalMentors`, dan `$totalPrograms` tanpa metrik buatan (`100+`, `15+`).
   - **Kartu Filosofi & Metode**: Memanfaatkan komponen `.why-card` dan `.why-icon` (Section 14).
   - **Kartu Nilai & Kualifikasi Pengajar**: Memanfaatkan komponen `.nilai-card` dan `.nilai-icon` (Section 15).
   - **Transparansi Wali Santri**: Menggunakan `.harapan-list`, `.harapan-item`, dan `.harapan-icon` (Section 16).
   - **Kutipan Filosofi**: Memanfaatkan `.quote-wrapper` dan `.quotes` (baris 7272) yang adaptif terhadap tema terang maupun gelap (`data-bs-theme="dark"`).
4. **Tombol Brand Resmi**:
   - Mengeliminasi tombol Bootstrap generik (`btn btn-primary`, `btn btn-outline-secondary`) dan menggantinya dengan token tombol brand:
     - `.btn-primary-custom`: Tombol gradien hijau dengan efek *hover elevation* dan *shine reflection*.
     - `.btn-outline-custom`: Tombol outline rapi dengan efek hover border hijau dan *theme contrast*.
     - `.btn_1` & `.btn-outline-light-custom`: Untuk kontras pada latar belakang gradien CTA.
5. **Section Ajakan / CTA Resmi**:
   - Mengadopsi arsitektur `.cta-section`, `.cta-overlay`, `.cta-content`, `.cta-icon`, `.cta-title` (`.text-gradient-light`), `.cta-subtitle`, `.cta-buttons`, dan `.btn-outline-light-custom` dari Section 25 `style.css`.

### 22.2 Redesain Pelacak Status Lamaran Publik (`/cek-status-lamaran`):
1. **Dual-Stepper Visual Tracking**:
   - Menyajikan 5 tahapan seleksi transparan: *1. Pendaftaran Berkas*, *2. Tes Kompetensi AI*, *3. Wawancara & Microteaching*, *4. Masa Percobaan (Probation)*, dan *5. Guru Resmi Tetap*.
   - Menyajikan visualisasi jalur status dinamis: *Jalur Sukses Hijau*, *Jalur Revisi Oranye*, atau *Jalur Pembatalan Merah*.
2. **Pencarian Terpadu & Normalisasi Nomor Ponsel Fleksibel**:
   - Bilah pencarian tunggal terpadu (`.tracker-search-bar`) yang mendukung pencarian kode registrasi unik (`APP-YYYYMM-XXXX`) maupun nomor WhatsApp pelamar.
   - Normalisasi otomatis awalan nomor ponsel (`+62`, `62`, atau `08`) untuk mencegah kegagalan pencarian akibat perbedaan format penulisan.
3. **Kartu Status Kontekstual & Integrasi Aksi Cepat**:
   - Memberikan kartu instruksi langsung sesuai tahapan: akses tombol pengerjaan tes kompetensi jika berstatus `test_scheduled`, tautan Google Meet jika berstatus `interview_scheduled`, atau tautan portal orientasi mandiri jika berstatus `approved`.

### 22.3 Aksesibilitas Keyboard & Semantik HTML5:
- Seluruh layout publik di `resources/views/layouts/landing.blade.php` telah dibungkus dengan elemen semantik `<main id="main-content">` serta tautan lompat aksesibilitas keyboard (`<a href="#main-content" class="skip-to-main">Langsung ke konten utama</a>`).
- Seluruh formulir dilengkapi atribut `aria-label`, penanganan kontras WCAG AA, serta navigasi keyboard yang ramah pengguna.

### 22.4 Eliminasi Warna Biru Navbar & Harmonisasi Palet Hijau-Emas:
1. **Pembersihan State & Badge Biru Bootstrap**:
   - Seluruh badge dan tag berwarna biru/sian standar Bootstrap (`bg-primary-subtle text-primary`, `bg-info-subtle text-info`) pada `resources/views/partials/navbar.blade.php` diganti menggunakan palet khas Islami Al-Hikmah: hijau zamrud muda (`bg-success-subtle text-success`) dan emas hangat (`bg-warning-subtle text-warning`).
   - Mencegah benturan kontras visual yang menyilaukan mata dan menyulitkan keterbacaan teks bagi wali santri dan calon guru.
2. **Override CSS Spesifik Navigasi (#mainNavbar)**:
   - Menambahkan aturan CSS eksplisit pada `public/assets/css/style.css` agar kelas aktif dropdown navigasi (`.dropdown-item.active`, `.dropdown-item:active`, `:focus`) tidak lagi menggunakan warna biru default Bootstrap (`#0d6efd`).
   - Pada mode terang, state aktif menggunakan `background: var(--primary-lighter)` dan `color: var(--primary)` dengan font-weight semi-tebal.
   - Pada mode gelap (`[data-bs-theme="dark"]`), state aktif mengadopsi `background-color: rgba(34, 197, 94, 0.16)` dan teks `var(--primary)`.
3. **Kepatuhan Guard Hak Akses Navigasi**:
   - Menjaga 100% aturan isolasi hak akses peran: menu Karir Guru hanya tampil untuk guest (`@guest`), menu Informasi Biaya & Paket hanya dapat diakses oleh Parent & Admin (`@auth @if (auth()->user()->isParent() || auth()->user()->isAdmin())`), serta tombol aksi dinamis `Mulai Perjalanan` bagi pengunjung baru.

### 22.5 Modern Split-Screen Onboarding Guru Mitra (`/bergabung`):
1. **Arsitektur Dual-Side Layout (Form Kiri, Gambar Kanan)**:
   - Mengubah antarmuka rekrutmen pengajar di `resources/views/public/mentor-recruitment/register.blade.php` dan `resources/views/bergabung.blade.php` menjadi layout modern split-screen `.mentor-split-card`.
   - **Sisi Kiri (`.mentor-form-side`)**:
     - Formulir pendaftaran terstruktur dengan 3 seksi logis: *1. Informasi Pribadi & Akun Login*, *2. Kualifikasi Pendidikan, Hafalan & Sanad*, dan *3. Unggah Berkas Persyaratan (CV & Sertifikat)*.
     - Dilengkapi tombol interaktif intip/sembunyikan kata sandi (`btn-password-toggle`) pada kolom input password dan konfirmasi password.
   - **Sisi Kanan (`.mentor-image-side`)**:
     - Visual berlatar belakang foto tematik Al-Qur'an resolusi tinggi (`assets/img/auth-bg.jpg`) dengan gradien hijau zamrud-slate gelap (`mentor-image-overlay`).
     - Kutipan Hadits Keutamaan Pengajar Al-Qur'an dari HR. Al-Bukhari No. 5027.
     - 3 Kartu Manfaat Bermitra: *Bimbingan Bersanad & Terarah*, *Fleksibilitas Waktu & Wilayah (Online/Home Visit)*, serta *Ujrah Profesional & Amanah*.
     - Alur ringkas 3 langkah seleksi rekrutmen dan tombol cepat konsultasi via WhatsApp ke sekretariat lembaga.
2. **Pembersihan Komponen Obsolet**:
   - Menghilangkan banner `breadcrumb_bg` dan section kriteria `why-card` yang sebelumnya memberatkan bagian atas halaman, sehingga calon guru langsung disambut dengan antarmuka formulir yang bersih, profesional, dan fokus.

### 22.6 Elevasi Desain Halaman Metode Belajar (`/metode`):
1. **3 Kartu Pilihan Metode Belajar Interaktif**:
   - *Privat Guru Datang ke Rumah (Home Visit)*: Dilengkapi animasi denyut halus `badge-pulse` pada label rekomendasi keluarga.
   - *Privat Online Tatap Layar Interaktif*: Solusi fleksibel dari mana saja dengan rekaman evaluasi tajwid.
   - *Hybrid Fleksibel*: Kombinasi seimbang antara tatap muka langsung dan pendampingan daring.
2. **Tabel Matriks Perbandingan Fitur Terpadu**:
   - Menghadirkan `.table-comparison-wrapper` yang membandingkan 6 dimensi layanan (Lokasi, Interaksi Guru, Media Pembelajaran, Fleksibilitas Waktu, Ketersediaan Wilayah, dan Rekomendasi Usia) secara transparan dan mudah dipahami wali santri.
3. **Pilihan Ritme & Intensitas Belajar**:
   - 4 kartu ritme bimbingan (2x, 3x, 4x, dan 5x pertemuan per pekan) dengan efek angkat kartu (`hover-lift`).
4. **Cakupan Wilayah Jabodetabek & Integrasi Peran**:
   - Grid cakupan layanan Home Visit untuk area Jakarta, Bogor, Depok, Tangerang, dan Bekasi, dilengkapi tombol verifikasi alamat ke admin.
   - Menjaga kepatuhan teks tombol aksi peran: `Informasi Pendampingan` bagi Parent dan `Informasi Pendampingan (Kamu Administrator)` bagi Admin.

### 22.7 Overhaul Antislop Halaman Biaya & Paket (`/biaya`):
1. **Gaya Bahasa Lugas & Tulus (Antislop Copywriting)**:
   - Menghilangkan frasa klise AI dan hiperbola komersial. Seluruh teks disusun menggunakan bahasa Indonesia yang santun, bersahaja, dan mengedepankan nilai ibadah.
2. **Kotak Investasi Pendaftaran Santri Baru Transparan**:
   - Menampilkan kotak rincian registrasi santri baru sebesar Rp 150.000 dengan jabaran detail: sesi penentuan level awal (*initial assessment*), buku mutabaah hafalan digital, modul kaidah tajwid berjenjang, dan laporan evaluasi bulanan.
3. **Kartu Paket Belajar Berjenjang (`.paket-card-v2`)**:
   - Penyajian harga dinamis terintegrasi basis data, label usia/level santri, durasi minggu terstruktur, dan deteksi status pendaftaran santri aktif bagi wali santri terautentikasi.
   - Teks tombol aksi terstandarisasi: `Pilih Program & Jadwal`.
4. **4 Jaminan Layanan Syar'i Lembaga**:
   - *Garansi Kesesuaian Guru*, *Sesi Pengganti Syar'i*, *Rapor Mutaba'ah Berkala*, dan *Komitmen Tanpa Biaya Tersembunyi*.
5. **Akordion Tanya Jawab Investasi**:
   - Menjawab pertanyaan lazim wali santri perihal skema cicilan/termin, pergantian guru jika tidak cocok, serta prosedur izin santri sakit/uzur.

### 22.8 Animasi Mikro Elegan & Kepatuhan WCAG AA / Aksesibilitas Gerak:
1. **Animasi Mikro Berkelanjutan**:
   - Keyframe animasi denyut halus `@keyframes badgePulse` untuk menarik fokus tanpa mengganggu konsentrasi membaca.
   - Efek kartu melayang vertikal halus `.hover-lift` (`transform: translateY(-4px)`) dengan transisi kubik alami.
2. **Aksesibilitas Sensitivitas Gerak (`prefers-reduced-motion`)**:
   - Seluruh animasi dinonaktifkan secara otomatis bagi pengguna yang mengaktifkan preferensi *reduce motion* di tingkat sistem operasi:
     ```css
     @media (prefers-reduced-motion: reduce) {
         .badge-pulse,
         .hover-lift:hover {
             animation: none !important;
             transform: none !important;
         }
     }
     ```
3. **Auto Cache Busting Aset Statis**:
   - Seluruh pemanggilan `style.css` pada layout utama kini dilengkapi stempel waktu otomatis `?v={{ filemtime(...) }}` untuk mencegah masalah cache browser saat pembaruan CSS diterapkan.

---

## 🌟 23. DESAIN MINIMALIS MODERN & FITUR BOOKING SESI UJI COBA GRATIS 15 MENIT (PLACEMENT TEST)

### 23.1 Latar Belakang & Nilai Manfaat Penempatan Awal:
1. **Pemberdayaan Wali Santri Baru Tanpa Beban**:
   - Banyak calon wali santri yang ragu memulai bimbingan karena belum mengetahui kemampuan awal membaca Al-Qur'an ananda, atau ragu dengan kenyamanan metode daring/tatap muka.
   - Sesi uji coba 15 menit (*Placement Test*) memfasilitasi pertemuan singkat gratis bersama ustadz/ustadzah terpercaya untuk mencairkan suasana, mengevaluasi kelancaran makharijul huruf, dan memberikan rekomendasi kurikulum tanpa paksaan membeli paket.
2. **Kesesuaian Pedagogis Sejak Pertemuan Perdana**:
   - Menghindari ketidakcocokan materi (misal: santri yang sudah siap masuk Al-Qur'an tidak perlu dipaksakan mengulang Iqra dari awal, atau sebaliknya santri yang butuh pemantapan makhraj dasar tidak kewalahan).

### 23.2 Arsitektur Basis Data `trial_bookings` & Status Lifecycle:
Tabel baru `trial_bookings` dirancang khusus untuk merekam proses pra-registrasi penempatan santri:
- **Atribut Utama**:
  - `parent_name` & `child_name`: Identitas wali dan calon santri.
  - `whatsapp`: Nomor kontak WhatsApp yang telah dinormalisasi ke standar internasional (awalan `62`).
  - `child_age` & `gender`: Informasi usia dan jenis kelamin untuk memastikan keselarasan syariat pengajar (batasan usia 10 tahun).
  - `trial_focus`: Fokus asesmen (*iqra_placement*, *tahsin_tajwid*, *tahfidz_hafalan*, *bahasa_arab*).
  - `preferred_date` & `preferred_time_slot`: Preferensi slot waktu (Pagi, Siang, Sore, Malam).
  - `learning_method`: Pilihan metode (*online* atau *offline*).
  - `status`: Lifecycle tahapan (`pending` -> `contacted` -> `scheduled` -> `completed` / `cancelled`).
  - `assigned_mentor_id` & `scheduled_at`: Guru yang ditugaskan serta waktu temu yang disepakati.
  - `assessment_result`: Catatan evaluasi hasil placement test oleh guru sebagai referensi paket belajar.

### 23.3 Integrasi Otomasi Notifikasi WhatsApp Koordinasi Jadwal:
- Saat formulir dikirimkan (baik via AJAX di modal atau form reguler), sistem secara otomatis menghasilkan tautan langsung WhatsApp ke koordinator akademik lembaga (`https://api.whatsapp.com/send?phone=...`).
- Pesan otomatis memuat ringkasan lengkap: nama wali, nama santri, usia, fokus evaluasi, slot waktu, dan metode belajar sehingga admin dapat merespons dalam hitungan menit.

### 23.4 Penyelarasan Frontend Antislop Suite (UI, Human, LayoutMobile, Copywriting):
1. **Estetika Minimalis Modern**:
   - Menolak tren visual klise AI: tidak menggunakan gradien ungu-biru generik, tidak ada tumpukan efek kaca (*glassmorphism*) berlebihan, dan tidak ada bentuk kapsul seragam di semua komponen.
   - Menggunakan palet utama hijau zamrud Al-Hikmah (`#0d7a3e`, `#15803d`) dengan aksen emas hangat (`#d97706`), dipadukan dengan tipografi tegas, berjarak nyaman, dan bersahaja.
2. **Kepatuhan Aksesibilitas WCAG AA & Kemanusiaan**:
   - Seluruh kontrol form uji coba (`.trial-input`) memiliki target sentuh minimal 46px (melampaui standar 44px) yang nyaman ditekan ibu jari pada layar ponsel pintar.
   - Rasio kontras teks di atas 4.5:1 pada mode terang maupun mode gelap (`data-bs-theme="dark"`).
   - Modal dilengkapi penanganan keyboard penuh: navigasi logis via `Tab`, penguncian fokus (*focus trapping*), dan penutupan cepat menggunakan tombol `Escape`.
3. **Respon Cepat & State UI Lengkap**:
   - Dilengkapi 3 state interaksi: *Loading state* dengan indikator spinner halus, *Error state* yang menjelaskan kendala isian secara santun, dan *Success state* dengan ucapan hamdalah serta tombol langsung hubungi WhatsApp.
4. **Copywriting Alami Tanpa Em Dash**:
   - Teks antarmuka bebas dari karakter em dash (`—`) dan bebas dari jargon pemasaran berlebihan, mengedepankan ketulusan nilai dakwah Al-Qur'an.

---

## 💎 24. STANDARDISASI 4 PAKET BIMBINGAN PRIVAT (OPSI A) & DURASI 90 MENIT

### 24.1 Matriks 4 Paket Belajar (Opsi A)
Berdasarkan penyelarasan produk bersama manajemen, AL-HIKMAH LMS mengadopsi 4 paket pembelajaran terstandarisasi dengan model bimbingan privat intensif 1 Guru 1 Santri:

| Nama Paket | Frekuensi Pertemuan | Durasi per Sesi | Investasi Bulanan | Rincian & Fasilitas Pembelajaran |
| :--- | :---: | :---: | :---: | :--- |
| **Paket Tunas Istiqomah** | 4x / bulan (1x / pekan) | 90 Menit | **Rp 600.000** | Model privat intensif (1 Guru 1 Santri), modul materi, lembar mutabaah hafalan, dan laporan evaluasi berkala ke orang tua. |
| **Paket Bimbingan Mumtaz** <br>*(Paling Diminati)* | 8x / bulan (2x / pekan) | 90 Menit | **Rp 1.200.000** | Ritme belajar seimbang untuk pembentukan makhraj dan kelancaran membaca Al-Qur'an tartil. |
| **Paket Akselerasi Itqan** | 12x / bulan (3x / pekan) | 90 Menit | **Rp 1.800.000** | Bimbingan intensif 3 kali sepekan untuk akselerasi bacaan Al-Qur'an tartil tingkat lanjut. |
| **Program Unggulan: Mahir Tahfidz Al-Qur'an** | 18x / bulan (Halaqah Intensif) | 90 Menit | **Rp 2.700.000** | Program unggulan khusus tahfidz: talaqqi harian, setoran hafalan baru, murajaah terjadwal mutqin, dan bimbingan guru hafidz/hafidzah. |

### 24.2 Standar Durasi 90 Menit & Keunggulan Privat 1-on-1
Seluruh program menggunakan alokasi waktu **90 menit per pertemuan**. Durasi ini memberikan waktu yang sangat leluasa bagi guru pembimbing untuk:
1. Mencairkan suasana (*ice-breaking*) dan doa pembuka (5-10 menit).
2. Menyimak tilawah / setoran hafalan secara teliti tanpa tergesa-gesa (50-60 menit).
3. Mengoreksi makhraj dan sifatul huruf secara mendalam (15 menit).
4. Memberikan nasihat adab Islami, doa penutup, serta pengisian buku mutaba'ah (10 menit).

### 24.3 Proteksi Gated Pricing Halaman Biaya (/biaya) & Isolasi Privasi Pengajar
- **Proteksi Akses Gated Pricing (`/biaya`)**: Informasi harga retail paket dan nominal investasi bulanan bersifat terkunci (*gated*), hanya dapat diakses oleh Orang Tua/Wali Santri (`parent`) dan Administrator (`admin`) yang telah terdaftar dan login (`@auth`).
- **Isolasi Tamu, Mentor, dan Santri (403 Forbidden)**: Tamu publik belum login (*guest*), santri (*student*), dan mentor/pengajar (*mentor*) dibatasi secara ketat dan langsung menerima respon `403 Forbidden` saat mencoba mengakses rute `/biaya`. Hal ini menjaga kemurnian niat dan objektivitas pengajar agar berfokus penuh pada mutu bimbingan tanpa terdistraksi margin retail.
- **Beranda Publik Bebas Angka Rupiah Mentah**: Halaman beranda publik ([`home.blade.php`](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/home.blade.php)) bersih dari kalkulator simulasi harga mentah, melainkan mengedepankan proposisi nilai bimbingan santun: 1 Guru 1 Santri, durasi 90 menit penuh, rapor mutaba'ah rutin, serta kemudahan pendaftaran sesi uji coba gratis 15 menit (*placement test*).

### 24.4 Standardisasi Desain & Benchmark Referensi Taqiyya Bimbel (taqiyyabimbel.my.id)
Sebagai tolok ukur platform edtech anak era 2026, AL-HIKMAH LMS mengadopsi standar antarmuka modern yang terbukti efektif pada **Taqiyya Bimbel** (`taqiyyabimbel.my.id`):
1. **Tipografi & Hirarki Visual**:
   - Memadukan `Plus Jakarta Sans` dan `Inter` yang kokoh, tegas, dan mudah dibaca oleh orang tua di perangkat mobile maupun desktop.
   - Micro-badges lembut (*pill badges*) untuk status akreditasi, rasio bimbingan (1 Guru 1 Santri), dan penanda program unggulan.
2. **Komposisi Hero & Trust Signals**:
   - Hero banner ramah keluarga dengan *floating trust cards* ("Pendampingan Santun: 1 Guru fokus mendampingi 1 Santri", durasi 90 menit per sesi, "Rapor Mutaba'ah Rutin").
   - Jalur konversi tanpa hambatan: Tombol uji coba gratis 15 menit (*placement test*) terintegrasi modal interaktif dan konsultasi WhatsApp instan.
3. **Privasi & Ekosistem Multi-Role**:
   - Mengikuti keunggulan model Taqiyya yang memisahkan portal Siswa/Ortu dan Tutor/Guru secara profesional.
   - Pada AL-HIKMAH LMS, privasi disempurnakan dengan *Gated Pricing*: harga retail dan rincian investasi terkunci aman bagi publik/mentor, menjaga objektivitas dan keikhlasan ustadz/ustadzah.
4. **Nuansa Visual Quiet Luxury Islamic**:
   - Tetap setia pada identitas Al-Qur'an melalui palet Emerald Islami (`#0d7a3e`), aksen Amber Gold hangat, serta tipografi Arab `Amiri` yang anggun untuk ayat/doa tanpa slop visual.

---

## ⚖️ 25. MODUL PEMBAGIAN FINANSIAL OTOMATIS (REVENUE SHARING ENGINE) & INFAQ 10%

### 25.1 Formula Matematis Bagi Hasil Sesi Belajar
Sistem menghitung alokasi finansial per sesi bimbingan yang telah diselesaikan (*status = completed*) secara deterministik pada [`RevenueAnalyticsService.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Services/RevenueAnalyticsService.php):

$$\text{Tarif Dasar Sesi} = \text{Rp } 150.000$$

1. **Honor Pengajar (Mentor Fee)**:
   $$\text{Honor Mentor} = \text{Rp } 100.000 \text{ per sesi bimbingan}$$
2. **Alokasi Kas Yayasan (Owner Gross)**:
   $$\text{Kas Yayasan (Gross)} = \text{Tarif Dasar} - \text{Honor Mentor} = \text{Rp } 150.000 - \text{Rp } 100.000 = \text{Rp } 50.000$$
3. **Alokasi Infaq Dakwah ("Untuk Allah SWT")**:
   Dihitung sebesar 10% dari alokasi kas yayasan/owner:
   $$\text{Infaq Dakwah (10\%)} = 10\% \times \text{Rp } 50.000 = \text{Rp } 5.000 \text{ per sesi}$$
4. **Pendapatan Bersih Yayasan (Owner Net)**:
   $$\text{Kas Bersih Yayasan} = \text{Kas Gross} - \text{Infaq Dakwah} = \text{Rp } 50.000 - \text{Rp } 5.000 = \text{Rp } 45.000 \text{ per sesi}$$

### 25.2 Isolasi Informasi Finansial Guru (Anti-Envy Isolation)
Untuk memelihara kebersihan hati para ustadz dan ustadzah pengajar Al-Qur'an:
- Mentor **hanya mengetahui hak honorarium pribadinya** (Rp 100.000 / sesi selesai).
- Mentor **tidak pernah melihat** harga retail paket yang dibayarkan wali santri ke yayasan.
- Mentor **tidak pernah melihat** margin kotor yayasan (Rp 50.000) maupun infaq lembaga.
- Kebijakan ini mengeliminasi potensi prasangka (*su'udzon*) atau kecemburuan antar-pengajar, sehingga guru dapat sepenuhnya mengikhlaskan niat dalam mendidik santri.

### 25.3 Penyajian Multi-Role Dashboard (Admin, Mentor, Wali Santri)
1. **Admin Dashboard (`/admin/dashboard`)**:
   Menampilkan kartu *"Sistem Bagi Hasil Sesi Belajar (Revenue Sharing)"*:
   - Total sesi selesai (akumulasi & bulan berjalan).
   - Total honorarium mentor tersalurkan (Rp 100.000 x sesi).
   - Total kas bersih yayasan (Rp 45.000 x sesi).
   - Total titipan infaq dakwah Al-Qur'an & beasiswa dhuafa 10% (Rp 5.000 x sesi).
2. **Mentor Dashboard (`/mentor/dashboard`)**:
   Menampilkan kartu *"Rekapitulasi Honor Mengajar Saya"*:
   - Akumulasi honor mengajar bulan ini & total keseluruhan (Rp 100.000/sesi).
   - Estimasi honor dari sesi terjadwal mendatang.
   - Catatan apresiasi keikhlasan mengajar dan audit pencairan resmi.
3. **Parent Dashboard (`/parent/dashboard`)**:
   Menampilkan kartu *"Transparansi Infaq & Keberkahan Sesi Belajar"*:
   - Total sesi bimbingan ananda yang telah tuntas.
   - Catatan bahwa 10% dari alokasi kas yayasan disalurkan untuk sedekah dakwah Al-Qur'an, sehingga setiap sesi belajar yang diikuti ananda bernilai amal jariyah bagi keluarga.

---

## 🌿 26. PENYELARASAN FRONTEND EDITORIAL, KONSOLIDASI SEEDER BERSIH, & UJIAN GURU

### 26.1 Konsolidasi Seeder Bersih & Pemulihan Taksonomi Blog (Kategori & Tag)
Sebagai pemenuhan arahan arsitektur data bersih (*clean database hygiene*):
1. **Dua Administrator Resmi**: Basis data secara ketat hanya menyisakan dua akun admin resmi:
   - **Hikmatul Hasanah** (`hikmah@gmail.com` / `0857-8668-9008`)
   - **Dandi Hermawan** (`dandihermawan87@gmail.com` / `089699451818`)
2. **Pemulihan Taksonomi Blog & Literasi**:
   - Seluruh seeder taksonomi edukasi (`BlogCategorySeeder`, `BlogTagSeeder`, `ArticleSeeder`) dipulihkan dan diintegrasikan ke dalam `DatabaseSeeder.php`.
   - Mengisi 5 Kategori Kurikulum Blog (Metode & Tips Belajar, Tahsin & Tajwid, Tahfidz Al-Qur'an, Adab & Parenting Islami, Wawasan Keislaman), 14 Tagar Populer, serta artikel bimbingan edukatif resmi.
   - Mengamankan data master Program Pembelajaran (10 Program) dan Galeri Dokumentasi (5 Galeri).

### 26.2 Validasi Ikon Program & Spasi Vertikal Header Terhadap Fixed Navbar
1. **Eliminasi Ikon Rusak / Unrendered Icon**:
   - Memperbaiki ketidakcocokan kelas Bootstrap Icons pada paket belajar, di mana `bi-seedling` (kelas Font Awesome yang tidak valid di Bootstrap Icons) digantikan secara universal menjadi `bi-flower1` (ikon resmi kecambah/tunas mekar).
   - Menambahkan mekanisme *defensive icon fallback* pada `resources/views/program.blade.php` agar setiap ikon modul memiliki fallback yang presisi dan tidak pernah rusak.
2. **Breathing Room Header Terhadap Fixed Navbar**:
   - Menyesuaikan nilai *top padding* pada seluruh komponen hero dan breadcrumb (`.page-hero`, `.tracker-hero`, `.breadcrumb_bg`):
     $$\text{Padding Top} = \text{calc}(\text{var}(--\text{navbar-height}) + 4.25\text{rem})$$
   - Mengeliminasi kesan terpotong (*overlap*) akibat bilah navigasi melayang (*fixed-top navbar*), memberikan jarak visual yang lega, lapang, dan nyaman dibaca di layar desktop maupun mobile.

### 26.3 Aksesibilitas Kontras Tinggi CTA Section (WCAG AAA Pure White & Emerald)
1. **Solusi Keterbacaan Banner CTA Utama ("Mari Menanam Kebaikan Sejak Hari Ini")**:
   - Mengatasi bentrok spesifisitas CSS pada `.cta-section`, di mana teks judul dan subjudul sebelumnya tertimpa warna gelap `--text-heading` / `--text-secondary`.
   - Memastikan judul `.editorial-title` di dalam `.cta-section` tampil dalam warna putih murni (`#ffffff !important`) beraksen hijau mint lembut (`#6ee7b7`).
   - Memastikan subjudul tampil dengan kontras tinggi (`rgba(255, 255, 255, 0.92) !important`) di atas latar belakang gradasi hijau zamrud.
2. **Dua Tombol Aksi Kontras Tinggi**:
   - Tombol Utama (*Daftar Gratis Sekarang*): Berubah menjadi kartu pil putih solid (`#ffffff`) dengan teks hijau zamrud tua tebal (`#064e3b`), memberikan kontras visual maksimal yang menarik perhatian pengunjung.
   - Tombol Sekunder (*Mulai Belajar*): Tombol kaca tembus pandang (*frosted glass* `rgba(255, 255, 255, 0.15)`) dengan garis tepi putih tegas (`border: 2px solid rgba(255, 255, 255, 0.75)`) dan teks putih terang.

### 26.4 Redesain Komprehensif Portal Blog (/blog)
1. **Hero Header Khusus Pembaca Literasi**:
   - Menggantikan container generik dengan `.page-hero` yang memiliki jarak aman dari fixed navbar, dilengkapi lencana kurikulum *"Literasi & Edukasi Qur'ani"*, judul dinamis berbasis kategori/tagar, serta ringkasan bernada editorial hangat.
2. **Arsitektur Kartu Artikel (.blog-card)**:
   - Mengadopsi tata letak modern responsif: rasio gambar terstandarisasi (`aspect-ratio: 16/10; object-fit: cover;`), efek zoom halus saat disentuh kursor (*hover micro-animation*), penanda tanggal berstempel waktu lokal, pill kategori hijau zamrud, estimasi waktu baca, serta metrik pembaca (*views count*).
3. **Sidebar Navigasi Edukatif**:
   - Formulir pencarian artikel dengan tombol bertema zamrud.
   - Navigasi kategori interaktif dengan badge penghitung jumlah artikel aktual.
   - Widget artikel terpopuler & thumbnail mini.
   - Tag cloud interaktif dengan pill bulat modern.
   - Kotak konsultasi belajar santri langsung terhubung ke WhatsApp resmi pimpinan/admin.

### 26.5 Standarisasi Desain Seleksi Guru (/mentor/recruitment/take-test)
1. **Harmonisasi Palet Hijau Zamrud (Islamic Editorial Minimalist)**:
   - Menghapus total warna biru generic Bootstrap (`#0d6efd`, `bg-primary`, `border-primary`) yang sebelumnya tidak selaras dengan tema lembaga.
   - Mengubah kartu pengantar ujian menjadi gradasi mewah Deep Emerald (`linear-gradient(135deg, #064e3b 0%, #047857 100%)`) berhias kotak statistik buram (*frosted glass*) untuk total soal dan durasi pengerjaan.
2. **Palet Tiga Standar Kompetensi**:
   - *Tajwid Test*: Garis tepi Deep Emerald (`#064e3b`).
   - *Makharijul Huruf*: Garis tepi Amber Emas (`#b45309`).
   - *Tahsin & Metodologi*: Garis tepi Dark Teal (`#0f766e`).
3. **Komponen Pilihan Ganda (Radio Options)**:
   - Keadaan Normal: Permukaan warna batu hangat (`#fafaf9`) dengan garis tepi lembut.
   - Keadaan Terpilih (`:has(input:checked)`): Berubah seketika menjadi hijau zamrud lembut (`#ecfdf5`), garis tepi hijau pekat (`#064e3b`), lingkaran lencana opsi huruf (A/B/C/D) menjadi hijau tua solid dengan huruf putih, serta teks jawaban berganti menjadi warna hijau zamrud tegas.

### 26.6 Penerapan Antislop-Copywriting & Rekonstruksi Bersih CTA & Blog Beranda (home.blade.php)
1. **Eliminasi Anomali Duplikasi Tombol CTA (@guest vs @auth / @else)**:
   - Menghapus tumpang tindih sintaks `@guest ... @endguest @auth ... @else ... @endauth` pada Section CTA (`#kontak`) yang sebelumnya menyebabkan pengunjung non-login melihat 5 tombol aksi sekaligus secara ganda.
   - Mengonsolidasikan struktur menjadi percabangan tunggal `@auth ... @else ... @endauth`:
     - Pengunjung Tamu (*Guest*): Disuguhkan tepat 3 tombol bermakna (*Coba Sesi Uji Coba Gratis*, *Lihat Pilihan Program*, dan *Tanya via WhatsApp*).
     - Pengguna Login (*Auth*): Disuguhkan 2 tombol navigasi yang disesuaikan secara dinamis menurut peran (`Parent`, `Student`, `Mentor`, `Admin`).
2. **Penerapan Antislop-Copywriting (Tanpa Buzzword & Tanpa Inflasi Signifikansi)**:
   - Menghapus klaim abstrak dan metafora puitis hampa (*"Mari Menanam Kebaikan Sejak Hari Ini... perjalanan besar"*).
   - Mengganti teks dengan proposisi nilai yang nyata, jujur, dan beradab: *"Mulai Belajar Al-Qur'an Bersama Guru Bersanad"*, dengan penjelasan konkret: *"Bimbingan privat 1-on-1 dengan jadwal fleksibel dan pemantauan mutaba'ah berkala. Coba sesi penempatan 15 menit tanpa biaya untuk mengukur level bacaan ananda."*
   - Menjamin tidak ada em-dash (`—`), tidak ada huruf kapital berseru, dan tidak ada klaim statistik palsu.
3. **Standardisasi Kartu Blog Beranda (.blog-card)**:
   - Mengganti `.editorial-card` yang sebelumnya berantakan dengan kelas `.blog-card` terstandarisasi.
   - Menggunakan rasio gambar seragam (`height: 210px; object-fit: cover;`), penanda tanggal terformat lokal Indonesia (`d M Y`), pill kategori kurikulum hijau lembut, estimasi waktu baca (`reading_time_label`), dan tautan aksi eksplisit (*"Baca artikel →"*).

---

## 🛡️ 27. ARSITEKTUR KEAMANAN SISTEM (LARAVEL SECURITY HARDENING & COMPLIANCE ALIGNMENT)

Mengacu pada standar kepatuhan **Laravel Cloud Compliance** ([laravel.com/cloud/docs/compliance](https://laravel.com/cloud/docs/compliance)) dan prinsip baku **8 Security Best Practices in Laravel** ([Danushaka Dissanayaka](https://medium.com/@dsjayamal/8-security-best-practices-in-laravel-ad7513798cfb)), AL-HIKMAH LMS telah memperketat seluruh postur keamanan sistem melalui pendekatan pertahanan berlapis (*defense-in-depth*).

### 27.1 Kepatuhan Standar Laravel Cloud Compliance
Platform dirancang selaras dengan standar tata kelola keamanan cloud modern:
1. **SOC 2 Type 2 Trust Services Criteria**:
   - **Keamanan (Security)**: Seluruh endpoint web dan API dilindungi oleh middleware keamanan terpusat, rate limiting bertingkat, dan isolasi otorisasi berbasis peran (Role-Based Access Control / RBAC) yang ketat (`admin`, `mentor`, `parent`, `student`).
   - **Kerahasiaan (Confidentiality)**: Data sensitif (seperti nomor rekening bank, dokumen sanad, dan token integrasi) diisolasi, disensor (*masked* secara default di UI), serta dilindungi dari paparan publik.
   - **Ketersediaan (Availability)**: Dilengkapi mekanisme proteksi DoS, cascade failover multi-provider AI (Google Gemini, DeepSeek, Qwen, Claude, GPT), bank kurikulum offline darurat, serta penanganan rate-limiting agar server tidak mengalami starvation kuota.
2. **Kesiapan HIPAA (Health & Personal Data Safeguards)**:
   - Menerapkan perlindungan data privasi keluarga dan rekam data anak santri.
   - Mengisolasi riwayat sesi bimbingan, catatan evaluasi akhlak, dan data identitas santri agar hanya dapat diakses oleh wali santri bersangkutan, mentor pembimbing yang ditugaskan, dan koordinator akademik.
3. **Penyelarasan Roadmap ISO 27001 & OWASP Top 10 Coverage**:
   - **A01: Broken Access Control**: Middleware `EnsureUserHasRole` dan `EnsureParentHasPaidProgram` memvalidasi setiap hak akses sebelum rute dieksekusi. Tindakan mutasi dilarang keras menggunakan HTTP GET.
   - **A02: Cryptographic Failures**: Enforce TLS/HTTPS pada lingkungan produksi (`URL::forceScheme('https')`), HTTP Strict Transport Security (HSTS) berdurasi 1 tahun (`max-age=31536000; includeSubDomains; preload`), serta hashing password menggunakan algoritma bcrypt terstandarisasi.
   - **A03: Injection**: 100% kueri database menggunakan Eloquent ORM dan parameter binding PDO (Zero Raw Concatenation).
   - **A04: Insecure Design**: Validasi ketersediaan privat 1-on-1 (*Zero Double-Booking*) dan validasi batasan muatan mentor (*Burnout Protection*).
   - **A05: Security Misconfiguration**: Pemblokiran akses web server ke berkas `.env`, `.git`, `.yml`, `.sql`, `.log`, dan penonaktifan directory indexing. Penanganan error ramah pengguna tanpa membocorkan stack trace database saat `APP_DEBUG=false`.
   - **A07: Identification and Authentication Failures**: Throttling login (maksimal 5 percobaan per menit per email + IP), perlindungan brute-force form reset password, dan regenerasi ID sesi pasca-login.

### 27.2 Implementasi 8 Pilar Keamanan Laravel (Danushaka Dissanayaka)

| No | Prinsip Keamanan | Risiko Tanpa Proteksi | Implementasi Resmi di AL-HIKMAH LMS |
| :---: | :--- | :--- | :--- |
| **1** | **Larangan `$request->all()` (Mass Assignment Prevention)** | Penyerang dapat menyuntikkan kolom ilegal seperti `is_admin`, `status`, atau `role` | Seluruh controller menggunakan FormRequest atau `$request->only([...])` dan `$request->validated()`. 100% model Eloquent menggunakan `$fillable` eksplisit tanpa satupun `$guarded = []`. |
| **2** | **Pengamanan File Upload Klien** | Eksekusi file berbahaya (PHP/webshell) atau path traversal | Validasi ketat MIME type (`mimes:pdf` untuk CV, `mimes:pdf,jpg,jpeg,png` untuk sertifikat), batasan ukuran file (`max:2048` KB), penyimpanan pada direktori privat (`storage/app/private/`), serta sanitasi nama berkas klien menggunakan `strip_tags(basename(...))`. |
| **3** | **Proteksi CSRF & Larangan State Mutation pada GET** | Serangan pemalsuan permintaan antar-situs (CSRF) pada aksi administratif | Seluruh aksi mutasi (buat, ubah, hapus, verifikasi) wajib menggunakan kata kerja HTTP `POST`, `PUT`, `PATCH`, atau `DELETE` ber-token `@csrf`. Pengecualian CSRF hanya diberikan secara spesifik untuk webhook resmi Pakasir Payment Gateway yang divalidasi via signature. |
| **4** | **Proteksi Berkas `.env` Mutlak** | Kebocoran kredensial database, API key AI, dan kunci enkripsi aplikasi | Konfigurasi ganda Apache `.htaccess` (di root proyek dan di `public/`) memblokir akses langsung ke seluruh dotfiles (`.env`, `.git`), berkas `.yml`, `.sql`, `.sh`, serta log server. |
| **5** | **Pencegahan XSS pada Template Blade** | Injeksi skrip jahat ke peramban pengguna lain melalui nama santri/ulasan | 100% output dinamis Blade menggunakan sintaks auto-escaping `{{ $data }}` yang mengeksekusi `htmlspecialchars()` secara otomatis. Penggunaan `{!! !!}` hanya diterapkan pada konten blog yang telah lolos sanitasi admin. |
| **6** | **Penonaktifan Debug Mode di Lingkungan Produksi** | Tampilan error Laravel (Ignition) membocorkan struktur tabel, variabel `.env`, dan kueri SQL | Pengaturan baku `APP_DEBUG=false` di lingkungan produksi, didukung halaman error kustom yang elegan dan bersahabat (`401`, `402`, `403`, `404`, `419`, `429`, `500`, `503`) tanpa membocorkan rincian teknis internal server. |
| **7** | **Rate Limiting Terdistribusi (Brute-Force & Abuse Protection)** | Serangan brute-force password, spam formulir publik, dan pemborosan kuota API AI | Diterapkannya sistem RateLimiter bertingkat: login (5x/menit), kontak (5x/menit), booking uji coba (5x/menit), pendaftaran guru (5x/menit), pelacak status (10x/menit), dan pembuatan bank soal AI (10x/menit). |
| **8** | **HTTP Security Headers & Content Security Policy (CSP)** | Clickjacking, pencurian sesi via iframe, MIME sniffing, dan injeksi resource luar | Middleware `SecurityHeaders` menginjeksikan 7 header keamanan esensial pada seluruh siklus response HTTP secara otomatis. |

### 27.3 Lapisan Pertahanan HTTP Security Headers & Content Security Policy (CSP)
Middleware [`App\Http\Middleware\SecurityHeaders`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Middleware/SecurityHeaders.php) secara otomatis menginjeksikan header pada setiap siklus HTTP request di grup middleware `web`:

1. **`X-Frame-Options: SAMEORIGIN`**: Melindungi aplikasi dari serangan pembajakan klik (*clickjacking*) dengan melarang pemuatan halaman LMS di dalam iframe domain luar.
2. **`X-Content-Type-Options: nosniff`**: Memaksa peramban mematuhi MIME type yang dideklarasikan server, mencegah eksekusi skrip berbahaya berkedok berkas gambar.
3. **`X-XSS-Protection: 1; mode=block`**: Mengaktifkan filter XSS reflektif bawaan peramban generasi lama.
4. **`Referrer-Policy: strict-origin-when-cross-origin`**: Mencegah kebocoran parameter URL sensitif saat navigasi menuju domain eksternal.
5. **`Permissions-Policy: camera=(), microphone=(), geolocation=(self), payment=(self)`**: Membatasi akses peramban terhadap API perangkat keras secara prinsip *least-privilege*.
6. **`Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`**: Memaksa koneksi HTTPS terenkripsi selama minimal 1 tahun pada peramban klien.
7. **`Content-Security-Policy` (CSP)**:
   - `default-src 'self'`: Seluruh resource secara default hanya boleh berasal dari origin yang sama.
   - `script-src`: Mengizinkan skrip lokal, inline script penting Blade/Vite, serta CDN terpercaya (cdnjs, unpkg, jsdelivr).
   - `style-src`: Mengizinkan stylesheet lokal, inline style Bootstrap/Antislop, Google Fonts, dan CDN terdaftar.
   - `font-src`: Mengizinkan Google Fonts dan ikon Bootstrap/FontAwesome.
   - `connect-src`: Mengizinkan koneksi API resmi jadwal sholat (Aladhan, MyQuran) dan payment gateway (Pakasir).
   - `frame-ancestors 'self'`: Mencegah embedding iframe dari domain luar.
   - `form-action 'self' https://app.pakasir.com`: Membatasi target pengiriman form hanya ke internal LMS dan gateway Pakasir.
   - `object-src 'none'`: Memblokir total pemuatan plugin Flash/Java applet yang usang dan rentan.

### 27.4 Sistem Rate Limiting & Proteksi Brute-Force Terdistribusi
Dikonfigurasi secara terpusat pada [`AppServiceProvider::configureRateLimiting()`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Providers/AppServiceProvider.php):

```mermaid
graph TD
    User["Permintaan Pengguna / Bot"] --> Throttle{"Evaluasi Rate Limiter"}
    Throttle -->|"POST /login (≤ 5x / mnt)"| AuthPass["Lolos Autentikasi"]
    Throttle -->|"POST /login (> 5x / mnt)"| Block429["429 Too Many Requests (Cooldown 60 Detik)"]
    Throttle -->|"POST /kontak (≤ 5x / mnt)"| ContactPass["Pesan Diteruskan"]
    Throttle -->|"POST /kontak (> 5x / mnt)"| Block429
    Throttle -->|"POST /uji-coba-gratis (≤ 5x / mnt)"| BookingPass["Booking Diproses"]
    Throttle -->|"POST /uji-coba-gratis (> 5x / mnt)"| Block429
    Throttle -->|"POST /mentor/questions/* (≤ 10x / mnt)"| AIPass["Generate Soal AI"]
    Throttle -->|"POST /mentor/questions/* (> 10x / mnt)"| Block429
```

- **`login`**: Dibatasi 5 percobaan per menit berbasis kunci gabungan `strtolower(email) . '|' . IP`. Mencegah serangan kamus (*dictionary attacks*) dan *credential stuffing*.
- **`contact`**: Dibatasi 5 pengiriman per menit per IP, mencegah spam kotak masuk admin.
- **`trial_booking`**: Dibatasi 5 pemesanan per menit per IP, mencegah bot membanjiri antrean uji coba penempatan gratis.
- **`mentor_apply`**: Dibatasi 5 pendaftaran per menit per IP, mencegah serangan DoS pengisian berkas pelamar.
- **`status_tracker`**: Dibatasi 10 pengecekan per menit per IP, mencegah penyerang melakukan enumerasi nomor telepon pelamar secara membabi-buta.
- **`ai_generation`**: Dibatasi 10 generasi soal per menit per user mentor, mengamankan saldo token API dan menghindari pemblokiran *rate-limit* dari vendor AI eksternal.

### 27.5 Audit Mass Assignment, Enkripsi Sesi, & Sanitasi File Upload Klien
1. **Audit Total Kueri & Mass Assignment**:
   - Seluruh pemanggilan `$request->all()` pada alur kritis ketersediaan (`AvailabilityController`) dan rekrutmen admin (`AdminRecruitmentController`) telah diganti secara presisi menjadi `$request->only([...])`.
   - Tidak ada model yang menggunakan `$guarded = []`.
2. **Pengerasan Cookie Sesi**:
   - `config/session.php` dikonfigurasi dengan:
     - `'http_only' => true`: Menghalangi pencurian sesi via serangan JavaScript XSS (`document.cookie`).
     - `'same_site' => 'lax'`: Mencegah kebocoran sesi pada permintaan lintas-situs (CSRF defense).
     - `'secure' => env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production')`: Memastikan cookie hanya dikirimkan via kanal HTTPS saat aplikasi berstatus production.
3. **Sanitasi File Upload Klien**:
   - Nama berkas asli saat diunggah calon guru disanitasi menggunakan `strip_tags(basename($file->getClientOriginalName()))` sebelum disimpan ke basis data, mengeliminasi risiko injeksi skrip atau manipulasi direktori nama berkas (*path traversal*).
   - Berkas sensitif (CV dan sertifikat) disimpan pada disk `private` (`storage/app/private/mentor_applications/...`), bukan di folder publik.

### 27.6 Konfigurasi Web Server Apache (.htaccess) & Isolasi Berkas Lingkungan (.env)
1. **Root `.htaccess` (Pencegahan Kesalahan Konfigurasi Host)**:
   - Mengantisipasi skenario di mana Apache DocumentRoot mengarah ke root repositori, berkas root `.htaccess` secara otomatis memblokir akses ke berkas `.env`, direktori `storage/logs`, folder framework (`app/`, `bootstrap/`, `database/`, `config/`), dan meneruskan seluruh lalu lintas web ke direktori `/public/`.
2. **Public `.htaccess` (Perlindungan Baris Terdepan)**:
   - Menambahkan aturan pemblokiran berkas tersembunyi (*dotfiles*): `RewriteRule "(^|/)\.(?!well-known)" - [F,L]`.
   - Menolak akses ke ekstensi berbahaya: `<FilesMatch "\.(env|sql|log|sh|yml|yaml|git|lock)$"> Deny from all </FilesMatch>`.
   - Menonaktifkan penelusuran direktori secara global (`Options -Indexes`).

### 27.7 Hasil Pengujian Keamanan Otomatis & Kesiapan Produksi (Production Ready)
Seluruh lapisan keamanan telah diverifikasi melalui pengujian fitur otomatis [`tests/Feature/SecurityHardeningTest.php`](file:///c:/xampp/htdocs/al-hikmah-lms/tests/Feature/SecurityHardeningTest.php) dengan hasil **8 Passed (51 assertions, 100% Green)**:

```text
PASS  Tests\Feature\SecurityHardeningTest
✓ security headers are present on web responses
✓ hsts header is present on secure requests
✓ contact form has rate limiting
✓ trial booking has rate limiting
✓ status tracker has rate limiting
✓ session cookie configuration is secure
✓ file upload rejects dangerous file extensions
✓ login route has rate limiting

Tests:    8 passed (51 assertions)
Duration: 3.40s
```

---

## 🎯 28. ARSITEKTUR STRATEGI MARKETING KONVERSI TINGGI & OPTIMALISASI ETALASE BIAYA

### 28.1 Latar Belakang & Eliminasi Blunder Transparansi Dapur Finansial
Dalam strategi penetapan harga (*pricing strategy*) jasa pendidikan Al-Qur'an, terdapat kecenderungan awal untuk menampilkan rincian alokasi biaya internal ke etalase publik (misalnya memajang pembagian Rp 100.000 untuk Ustadz, Rp 45.000 untuk operasional yayasan, dan Rp 5.000 untuk infaq).

Namun, hasil evaluasi pemasaran dan psikologi konsumen membuktikan bahwa memajang kalkulasi dapur finansial secara mentah ke publik merupakan kesalahan pemasaran (*marketing blunder*):
1. **Memicu Skeptisisme Wali Santri**: Orang tua secara psikologis akan menganalisis margin operasional lembaga daripada berfokus pada manfaat kurikulum dan kenyamanan anak.
2. **Menurunkan Marwah Pendidik**: Menampilkan nominal gaji guru secara eksplisit di katalog publik dapat mengurangi wibawa (*muru'ah*) ustadz dan ustadzah pembimbing.
3. **Kesan Defensif & Menimbulkan Keraguan**: Lembaga terkesan merasa bersalah atau meminta izin atas penetapan harga jasanya.

**Solusi Arsitektural**:
Seluruh formula pembagian proporsional internal (Guru : Yayasan : Infaq Dakwah) tetap terkunci rapi di dalam modul backend (*Revenue Sharing Engine* bab 25). Sementara pada antarmuka publik (`/` dan `/biaya`), strategi dibungkus dengan pendekatan *Pure High-Value Marketing* yang bertumpu pada empati orang tua, pembuktian efisiensi waktu, pembingkaian nilai (*reframing*), dan kepastian kualitas.

```mermaid
graph TD
    subgraph Dapur Internal [Backend & Admin Dashboard Al-Hikmah]
        Formula["Formula Bagi Hasil Sesi (Bab 25)"]
        GuruPay["Hak Penuh Ustadz: Rp 100.000"]
        OpsPay["Operasional & Platform: Rp 45.000"]
        InfaqPay["Infaq Dakwah 10%: Rp 5.000"]
        Formula --> GuruPay
        Formula --> OpsPay
        Formula --> InfaqPay
    end

    subgraph Etalase Publik [Frontend Beranda & Halaman Biaya]
        ParentView["Orang Tua / Calon Wali Santri"]
        HookEmpati["Hook Frustrasi & Rasa Bersalah Orang Tua"]
        Komparasi["Tabel Nilai: 90 Mnt Privat vs 5 Mnt Antre TPA"]
        Reframing["Investasi Rp 20.000/Hari (Seharga Jajan Anak)"]
        JariyahBadge["Sertifikasi Infaq Jariyah Dakwah Lembaga"]
        ParentView --> HookEmpati
        ParentView --> Komparasi
        ParentView --> Reframing
        ParentView --> JariyahBadge
    end
```

### 28.2 Empat Hook Emosional Utama & Trust Anchors di Beranda
Diimplementasikan pada [`resources/views/home.blade.php`](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/home.blade.php):

1. **Hero Headline (Parent Frustration & Guilt Relief)**:
   - *Headline*: *"Mau Ajarkan Anak Ngaji Sendiri tapi Sering Kehabisan Sabar setelah Lelah Bekerja?"*
   - *Fungsi Psikologis*: Menghapus rasa bersalah orang tua modern yang kesulitan meluangkan energi sabar untuk mendampingi anak setelah pulang kantor. Al-Hikmah hadir sebagai mitra terpercaya, bukan penghakim.

2. **Floating Assessment Badge (Penyelamat Waktu yang Buntu)**:
   - *Pesan*: *"Sudah berbulan-bulan belajar tapi bacaan ananda masih tersendat? Kami siap mengevaluasi tajwid & makhraj ananda lebih dulu."*
   - *Fungsi Psikologis*: Menyentuh orang tua yang resah karena anak sudah lama mengaji di tempat lain namun belum ada perkembangan berarti.

3. **Section Nilai Guru (Jaminan Kesabaran & Sanad)**:
   - *Headline*: *"Bukan Sekadar Bisa Baca, tapi Guru yang Punya Waktu & Kesabaran Mendengarkan."*
   - *Fungsi Psikologis*: Menjamin suasana belajar ramah anak tanpa bentakan atau tekanan terburu-buru.

4. **Bottom CTA (Investasi Bekal Akhirat)**:
   - *Headline*: *"Hadiah Terbaik untuk Masa Depan Ananda: Bacaan Al-Qur'an yang Tartil dan Terjaga Seumur Hidup."*
   - *Fungsi Psikologis*: Mengangkat bimbingan Al-Qur'an dari sekadar pengeluaran rutin menjadi warisan abadi yang pahalanya terus mengalir.

### 28.3 Tabel Komparasi Nilai "TPA Tradisional vs Privat Al-Hikmah 1-on-1"
Diterapkan pada [`resources/views/biaya.blade.php`](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/biaya.blade.php) tepat sebelum rincian paket:

| Parameter Evaluasi | Mengaji Biasa / TPA Tradisional | Privat Eksklusif AL-HIKMAH |
| :--- | :--- | :--- |
| **Rasio Perhatian Guru** | 1 Guru mengajar 15 sampai 25 santri sekaligus | 1 Guru mendampingi 1 Santri (fokus 100%) |
| **Waktu Disimak Guru** | Cuma 5 sampai 7 menit per hari (sisanya antre dan bermain) | 90 Menit Penuh (disimak runtut tanpa terburu-buru) |
| **Laporan Progres Belajar** | Tidak ada atau buku paraf manual mingguan | Rapor digital real-time langsung ke HP orang tua |
| **Kurikulum Belajar** | Disamaratakan seluruh kelas tanpa melihat bakat | Personal, disesuaikan dengan ritme dan karakter anak |
| **Hasil & Kecepatan Belajar** | Berbulan-bulan jalan di tempat karena minim perhatian | 3 sampai 4 kali lebih cepat lancar dan mutqin |

### 28.4 Reframing Investasi "Rp 20.000/Hari" & Alokasi Infaq Jariyah Dakwah
1. **Reframing Waktu Emas Anak**:
   - Satu sesi privat 90 menit di Al-Hikmah setara dengan 3 pekan waktu mengantre di pengajian umum. Orang tua menghemat masa keemasan (*golden age*) ananda agar tidak terbuang sia-sia.
2. **Reframing Pengeluaran Harian**:
   - Investasi bimbingan dianalogikan mulai *Rp 20.000 per hari* (seharga segelas es kopi susu atau jajanan sore), namun memberi dampak kecakapan Al-Qur'an seumur hidup.
3. **Inklusi Infaq Jariyah Dakwah**:
   - Pada checklist setiap paket bimbingan disematkan klausul: *"Sudah termasuk infaq jariyah dakwah Al-Qur'an"*.
   - Mengalihkan fokus dari hitungan biaya komersial menjadi kemitraan dakwah yang menghadirkan ketenangan batin (*spiritual peace of mind*) bagi Ayah dan Bunda.

### 28.5 Redesain Kartu Membership Pass & Briefing Kurikulum Personal Santri Baru
Bagian registrasi santri baru pada [`resources/views/biaya.blade.php`](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/biaya.blade.php) direvitalisasi menjadi kartu keanggotaan privat (*Membership Pass*):
- **Identitas Personal**: Menyapa wali santri dengan *"Ahlan wa Sahlan, [Nama Wali Santri]"* lengkap dengan lencana akun terverifikasi.
- **4 Pilar Komitmen Bimbingan**:
  - *Kurikulum Personal*: Disesuaikan ritme dan karakter anak.
  - *Satu Guru Satu Santri*: Pendampingan 90 menit penuh tanpa distraksi.
  - *Rapor Digital Terpadu*: Pantauan mutabaah langsung dari gawai.
  - *Infaq Jariyah Dakwah*: Berkah mengalir bagi keluarga santri.
- **Kartu Investasi Registrasi Awal**: Penataan nominal Rp 150.000 (1x di awal) dengan tipografi numerik tebal (`tnum-price`), lencana hijau, dan ringkasan benefit asesmen makhraj, tajwid, dan portal santri.

### 28.6 Standardisasi Spasi Vertikal Fixed-Navbar Subpage (.editorial-page-header)
Mengatasi layout di mana bagian header subpage terlalu dempet dengan bilah navigasi melayang (*fixed navbar*), ditambahkan sistem kelas utilitas terpadu pada [`public/assets/css/style.css`](file:///c:/xampp/htdocs/al-hikmah-lms/public/assets/css/style.css):

```css
/* --- 8. Editorial Subpage Header (Fixed Navbar Safe Spacing) --- */
.editorial-page-header,
main#main-content > section:first-child.py-5 {
    padding-top: calc(var(--navbar-height) + 3rem) !important;
    padding-bottom: 3.5rem;
}

@media (min-width: 992px) {
    .editorial-page-header,
    main#main-content > section:first-child.py-5 {
        padding-top: calc(var(--navbar-height) + 4.5rem) !important;
        padding-bottom: 4rem;
    }
}
```

- **Perlindungan Otomatis**: Selector `main#main-content > section:first-child.py-5` menjamin seluruh halaman turunan (seperti `/biaya`, `/kontak`, `/faq`, `/galeri`) otomatis mendapatkan ruang aman sebesar ~64px di bawah navbar tanpa penyesuaian manual berulang.
- **Keseimbangan Simetris**: Mengeliminasi kelas ganda `py-4` di dalam container, menciptakan jarak atas dan bawah judul yang seimbang dan mewah (*editorial proportion*).

---

## BAB 29: STRATEGI KONVERSI BERJENJANG PROGRAM TAHFIDZ & INTEGRASI MULTI-DASHBOARD SESI UJI COBA GRATIS 15 MENIT (VERSI 11.0)

### 29.1 Latar Belakang & Eliminasi Tombol Pendaftaran Bertumpuk
Sebelumnya pada [`resources/views/tahfidz.blade.php`](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/tahfidz.blade.php), terdapat fenomena redundansi tombol pendaftaran (menampilkan 4 hingga 5 tombol sekaligus: *Daftar Program Tahfidz*, *Daftar Akun Wali Santri*, *Daftar Gratis*, dan *Konsultasi & Daftar Tahfidz*). Hal ini membebani kognitif calon orang tua (*decision fatigue*) dan menurunkan rasio konversi.

### 29.2 Dua Aksi Utama Berjenjang (Tiered Conversion Funnel)
Antarmuka disederhanakan menjadi 2 aksi utama berjenjang dengan tingkat friksi terendah:
1. **Bagi Pengunjung Publik / Tamu (Guest)**:
   - **Primary CTA (Konversi Tertinggi / Zero Financial Friction)**: *"Coba Sesi Uji Coba Gratis 15 Menit"* (Membuka `#trialModal` yang otomatis mengarahkan fokus ke `tahfidz_hafalan` atau Evaluasi Kelancaran Hafalan & Murajaah). Orang tua tidak dibebani komitmen biaya di awal sehingga lebih terdorong mencoba.
   - **Secondary CTA (Pendekatan Ramah WhatsApp)**: *"Konsultasi via WhatsApp"* (Tautan langsung dengan teks ramah: *"Assalamualaikum Admin, saya ingin konsultasi mengenai program Tahfidz Al-Qur'an untuk ananda"*).
2. **Bagi Wali Santri Terautentikasi (`@auth` Parent)**:
   - *"Daftarkan Anak ke Tahfidz"* (membuka modal pendaftaran terhubung `#tahfidzLoggedInModal`).
   - *"Informasi Pendampingan & Biaya"* (menuju route `biaya`).

### 29.3 Edukasi Santun Penambahan Program Aktif bagi Wali Santri
Jika orang tua telah memiliki santri yang aktif di salah satu program bimbingan (misal: Paket 4x/8x dari `biaya.blade.php`), sistem secara cerdas menampilkan kartu informasi santun di halaman `/tahfidz`:
- Menampilkan daftar ananda dan program yang sedang diikuti.
- Menjelaskan bahwa pendaftaran program Tahfidz dipersilakan, namun sangat disarankan berkonsultasi via WhatsApp terlebih dahulu agar kapasitas hafalan dan waktu istirahat ananda tetap seimbang.
- Menyediakan tombol cepat: *"Konsultasi Penambahan Program (WhatsApp)"*.

### 29.4 Penegakan Privasi Harga (*Gated Pricing*)
Kartu *"Program Unggulan Eksklusif: Mahir Tahfidz Al-Qur'an (Rp 2.700.000 / bulan)"* yang sebelumnya terpampang di halaman publik `/tahfidz` **dihapus total**. Tindakan ini menegakkan aturan bahwa seluruh nominal rupiah dan simulasi investasi privat terlindungi di balik autentikasi wali santri (`/biaya`), menjaga privasi margin lembaga dan mencegah kebocoran harga ke publik.

### 29.5 Kebijakan Siklus Hidup Akun Sesi Uji Coba (Tanpa Auto-Delete)
Setelah sesi 15 menit selesai:
1. **Akun & Data Tidak Dihapus Otomatis**: Data calon santri dan nomor WhatsApp tetap tersimpan permanen di basis data agar hasil catatan evaluasi guru tidak hilang dan admin dapat melakukan tindak lanjut ramah.
2. **Perubahan Status Bertahap**:
   - `pending` -> Pendaftaran baru diterima, menunggu koordinasi admin.
   - `scheduled` -> Jadwal temu 15 menit ditetapkan bersama guru.
   - `completed` -> Sesi evaluasi 15 menit selesai. Guru memasukkan catatan hasil asesmen makhraj/tajwid/hafalan.
3. **Pintu Masuk Bimbingan Resmi**: Pada dashboard orang tua, sesi yang telah selesai menampilkan tombol *"Pilih Paket Bimbingan Resmi"* yang langsung mengarahkan ke halaman `/biaya` tanpa perlu mengetik ulang data profil anak.

### 29.6 Integrasi Pemantauan Real-Time di 3 Dashboard
1. **Admin Dashboard ([`Admin\DashboardController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/DashboardController.php))**:
   - Panel monitor pendaftar sesi gratis 15 menit lengkap dengan indikator badge pending, data anak, wali santri, fokus asesmen, waktu preferensi, dan tombol 1-klik chat WhatsApp follow-up.
2. **Parent Dashboard ([`Parent\ParentDashboardController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Parent/ParentDashboardController.php))**:
   - Widget status pendaftaran sesi uji coba ananda (jadwal temu, guru pendamping, dan catatan hasil evaluasi).
3. **Mentor Dashboard ([`Mentor\DashboardController.php`](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Mentor/DashboardController.php))**:
   - Widget tugas sesi uji coba & placement test 15 menit yang ditugaskan kepada guru bersangkutan, lengkap dengan kontak wali untuk koordinasi talaqqi singkat.

---

**Disahkan oleh:** Tim Manajemen & Pengembang AL-HIKMAH LMS  
**Status Dokumen:** Living Specification & Single Source of Truth  
**Tanggal:** 17 September 2026


