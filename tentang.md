# 🕌 LAPORAN EKSEKUTIF PROYEK & PANDUAN APLIKASI: AL-HIKMAH LMS

> **Dokumen Resmi untuk Manajemen / Pimpinan Lembaga (Non-Technical Executive Guide)**  
> **Nama Sistem:** AL-HIKMAH Learning Management System (LMS)  
> **Status Aplikasi:** ✅ **100% Selesai, Teruji, & Siap Digunakan (Production Ready)**  
> **Versi:** 4.8 (Enterprise Edition - Complete DataTables Suite Across All Portals, Prerequisite Child Registration Gate, Adaptive Onboarding Stepper, State Gating & 3-Tier Navigation, 4-Layer Centralized Alert Architecture, Dynamic Gallery Category CRUD, Interactive Gallery Showcase, Trash Management Lifecycle, Anti-Spam View Counter, Atomic Reorder, Unified Navbars, Full Indonesian Localization)  
> **Tanggal Pembaruan:** 23 Agustus 2026  

---

## 📋 DAFTAR ISI LAPORAN

1. [📌 Ringkasan Eksekutif & Nilai Manfaat Aplikasi](#-1-ringkasan-eksekutif--nilai-manfaat-aplikasi)
2. [📊 Standardisasi & Implementasi DataTables di Seluruh Dashboard](#-2-standardisasi--implementasi-datatables-di-seluruh-dashboard)
3. [🧭 Standardisasi Desain Antarmuka & Navigasi Terpadu (Semua Portal)](#-3-standardisasi-desain-antarmuka--navigasi-terpadu-semua-portal)
4. [🔄 Tahapan & Alur Kerja Utama Aplikasi](#-4-tahapan--alur-kerja-utama-aplikasi)
5. [⭐ Penjelasan Seluruh Fitur Aplikasi (Berdasarkan Hak Akses)](#-5-penjelasan-seluruh-fitur-aplikasi-berdasarkan-hak-akses)
6. [🔔 Sistem Notifikasi & Alert Terpusat (Centralized Alert System)](#-6-sistem-notifikasi--alert-terpusat-centralized-alert-system)
7. [🗄️ Penjelasan Seluruh Database (Penyimpanan Data Lembaga)](#-7-penjelasan-seluruh-database-penyimpanan-data-lembaga)
8. [🎮 Penjelasan Seluruh Model & Controller (Pemroses Logika Sistem)](#-8-penjelasan-seluruh-model--controller-pemroses-logika-sistem)
9. [📁 Penjelasan Seluruh Struktur Folder Aplikasi](#-9-penjelasan-seluruh-struktur-folder-aplikasi)
10. [🧪 Hasil Pengujian & Quality Assurance (100% Green Pass)](#-10-hasil-pengujian--quality-assurance-100-green-pass)
11. [🎯 Kesimpulan & Rekomendasi Langkah Ke Depan](#-11-kesimpulan--rekomendasi-langkah-ke-depan)

---

## 📌 1. RINGKASAN EKSEKUTIF & NILAI MANFAAT APLIKASI

**AL-HIKMAH LMS** adalah platform manajemen pendampingan belajar Al-Qur'an terpadu berbasis web yang dirancang khusus untuk memfasilitasi anak-anak dan dewasa dalam belajar membaca Al-Qur'an (Iqra/Tahsin), menghafal (Tahfidz), memahami Tajwid, serta pembiasaan Adab & Doa Harian.

### 💡 Keunggulan Utama & Solusi Masalah yang Diterapkan:

1. **Standardisasi DataTables Terpadu di Seluruh Dashboard (Admin, Mentor, Parent)**:
   - Seluruh tabel data operasional di seluruh project menggunakan **DataTables 3.0.2 + Responsive 4.0.2** dengan antarmuka modern khas tema hijau Al-Hikmah, dukungan tema Gelap/Terang, pencarian instan multifilter (*instant multi-column search*), pengurutan dinamis (*sortable headers*), seleksi jumlah baris (*page length*), dan paginasi yang rapi tanpa perlu reload halaman.
   - Dilengkapi *Colspan Sanitizer* otomatis pada baris kosong (`@empty`) untuk mencegah crash DataTables, proteksi kolom aksi (`.no-sort`), serta rekalkulasi otomatis (*responsive recalculate*) saat berpindah tab atau membuka modal.

2. **Manajemen Dinamis Kategori Galeri Dokumentasi (`GalleryCategory`)**:
   - Administrator dapat mengelola (CRUD) kategori dokumentasi secara mandiri: menentukan nama kategori, grup kegiatan (*Kategori Utama*, *Acara Khusus*, *Prestasi & Kolaborasi*), ikon Bootstrap, warna label/badge, urutan tampil (*sort order*), dan status aktif/arsip.
   - Terintegrasi secara aman (*safe delete*) ke tabel `galleries` dengan relasi `category_id`, sinkronisasi otomatis ke form input admin, dan filter pills etalase publik.

3. **Standardisasi Navigasi & Tata Letak Lapang di Semua Portal**:
   - Seluruh halaman navigasi (Navbar Landing Page, Portal Administrator, Portal Guru/Mentor, Portal Orang Tua, dan Ruang Belajar Santri) menggunakan tata letak `container-fluid` yang lega, elegan, bebas kesan sempit (*not cramped*), dilengkapi ikon Bootstrap modern yang seragam dan intuitif.

4. **Pengalih Tema Gelap / Terang (Dark & Light Mode Engine)**:
   - Dilengkapi tombol pengalih tema (`#themeToggle`) dengan sinkronisasi `localStorage` dan deteksi preferensi sistem secara otomatis pada seluruh portal (Landing, Admin, Mentor, Parent, Student).

5. **Lokalisasi Bahasa Indonesia Baku (Senin - Minggu)**:
   - Seluruh format tanggal, nama hari (Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu), dan nama metode belajar disajikan dalam Bahasa Indonesia yang baku dan sinkron di seluruh komponen aplikasi.

6. **Filter Proteksi Santri Binaan Mentor (Strict Paid Gate)**:
   - Santri yang baru disetujui jadwalnya oleh Admin (`CONFIRMED`) namun belum melunasi pembayaran SPP/pendaftaran **TIDAK AKAN MUNCUL** di portal mentor (`/mentor/students`). Santri secara otomatis muncul hanya ketika pembayaran berhasil diverifikasi (`ACTIVE`).

7. **Mesin Metode Belajar Dinamis (`learning_method`)**:
   - Menghilangkan hardcode metode belajar. Sistem merekam metode belajar yang dipilih wali santri (`Offline / Home Visit`, `Online`, `Hybrid`) pada tabel `enrollments` dan menggunakannya secara konsisten saat meng-generate sesi bimbingan 4 minggu di `learning_sessions`.

8. **Status Konfirmasi Kehadiran Real-time & Notifikasi Mentor (`/mentor/dashboard`)**:
   - Ketika Orang Tua mengonfirmasi kehadiran anak (Hadir, Izin, Sakit) di portal wali (`/parent/schedules/{id}`), sistem secara otomatis mengirim notifikasi real-time in-app & WhatsApp ke Mentor Pembimbing.
   - Tabel Jadwal Mengajar di Dashboard Mentor menampilkan badge visual status konfirmasi orang tua secara langsung: 🟢 **Hadir**, 🟡 **Izin** (beserta catatan izin), 🔴 **Sakit**, atau ⚪ **Belum Konfirmasi**.

9. **Fitur Roadmap Alur Pendaftaran Interaktif (`/roadmap`)**:
   - Menyediakan panduan langkah demi langkah yang jelas dan terisolasi (tab khusus Orang Tua, Guru/Pendamping, dan Alur Pembayaran) dilengkapi *Dynamic Status Detection* (`Sedang Direview`, `Siap Bayar`, `Program Aktif`).

10. **Sistem Notifikasi & Alert Terpusat (`NotificationService`)**:
    - Livewire 3 Real-time Notification Bell (`<livewire:notification-bell />`) dengan polling berkala, lencana unread, drawer dropdown, penanda dibaca, dan redirect `action_url`.
    - Floating Toast Alert (`<x-flash-toast />`) dipasang di seluruh layout aplikasi.

11. **Alur Penjadwalan "Deal Dulu, Baru Bayar" (State Machine Invoicing)**:
    - Tagihan pendaftaran & SPP diterbitkan secara otomatis setelah Admin dan Wali Santri menyepakati jadwal & guru pembimbing (`CONFIRMED`).

12. **Export Data Pendaftaran ke Excel / CSV (`/admin/enrollments/export`)**:
    - Administrator dapat mengunduh seluruh rekapitulasi data pendaftaran santri ke format spreadsheet Excel (CSV UTF-8 BOM) dalam satu kali klik.

13. **Etalase Galeri Dokumentasi Kegiatan & Manajemen Tong Sampah (`/galeri` & `/admin/galleries`)**:
    - Pengelolaan penuh foto dokumentasi kegiatan oleh Admin (CRUD, Hero Slider toggle, Publish toggle, Drag-and-Drop Atomic Reorder, Filter Kategori/Program, serta Tong Sampah / SoftDeletes dengan rute Restore & Force Delete).
    - Etalase galeri interaktif publik dengan Hero Slideshow, Filter Kategori Utama & Event, Tag Cloud, Lightbox Modal Detail, Tracker Tayangan Anti-Spam Sesi, dan Tombol Multi-Share Social Media.

---

## 📊 2. STANDARDISASI & IMPLEMENTASI DATATABLES DI SELURUH DASHBOARD

Seluruh tabel penyajian data pada **3 Portal Utama (Admin, Mentor, Parent)** telah di-upgrade secara menyeluruh menggunakan library modern **DataTables v3.0.2 + Responsive Extension v4.0.2** dengan integrasi styling Bootstrap 5 dan tema warna hijau Al-Hikmah.

```mermaid
graph TD
    subgraph DataTablesEngine["⚙️ DataTables Suite Architecture"]
        Bundle["DataTables Core v3.0.2 + Responsive v4.0.2 (Local Assets)"]
        InitScript["datatables-init.js (Auto-Initializer, Empty Row Sanitizer & Modal/Tab Listener)"]
        CSSVars["Custom Styling (Emerald Green Active Page, Hover Glow, Dark Mode Table Striping)"]
    end

    subgraph Portals["🌐 Dashboard Implementation Matrix"]
        AdminTables["Admin Portal: 8 Modul / 10 Tables (Dashboard, Users, Payments, Enrollments, Active, Availability, Galleries, Categories, Contacts)"]
        MentorTables["Mentor Portal: 5 Modul / 6 Tables (Today Sessions, All Sessions, Students, Parents, Export Students, Export Progress)"]
        ParentTables["Parent Portal: 5 Modul (Enrollments, Payments, Schedules & Absensi, Children List, Progress Detail)"]
    end

    DataTablesEngine --> Portals
```

### 📋 Rincian Matriks Implementasi DataTables:

| No | Portal | Halaman / Modul | ID / Elemen Tabel | Fitur DataTables |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Admin** | Dashboard: Permohonan Jadwal | `data-datatable` (Top 5) | Auto-sort, Paging ringkas, Multi-search |
| 2 | **Admin** | Dashboard: Santri Aktif Terbaru | `data-datatable` (Top 5) | Search nama santri & program, responsive |
| 3 | **Admin** | Dashboard: Transaksi SPP Terbaru | `data-datatable` (Top 5) | Search status invoice, sorting tanggal |
| 4 | **Admin** | Manajemen Pengguna / Akun | `#tableAdminUsers` | Filter role, cari email/nama, 10/25/50 baris |
| 5 | **Admin** | Verifikasi & Pembayaran SPP | `#tableAdminPayments` | Filter status bayar, search invoice, urut nominal |
| 6 | **Admin** | Permohonan Pendaftaran | `#tableAdminEnrollments` | Search wali/santri/program/status pendaftaran |
| 7 | **Admin** | Rekapitulasi Santri Aktif | `#tableActiveEnrollments` | Search mentor pendamping, hari belajar, kontak |
| 8 | **Admin** | Matriks Jadwal 7 Hari Mentor | `#tableMentorAvailability` | Filter ketersediaan kuota mentor 7 hari |
| 9 | **Admin** | Galeri Foto & Dokumentasi | `#tableAdminGalleries` | Sort order, filter kategori, no-sort action |
| 10 | **Admin** | Kategori Galeri Dokumentasi | `#tableAdminGalleryCategories` | Search group, badge warna, reorder sort |
| 11 | **Admin** | Pesan & Formulir Kontak Masuk | `#tableAdminContacts` | Search subjek, email, nomor HP, status dibaca |
| 12 | **Mentor** | Dashboard: Sesi Mengajar Hari Ini | `#tableMentorTodaySessions` | Auto-sort jam mulai, status absensi santri |
| 13 | **Mentor** | Semua Sesi & Jadwal Mengajar | `#tableMentorSessions` | Filter tanggal, search santri, sort waktu |
| 14 | **Mentor** | Data Santri Binaan Aktif | `#tableMentorStudents` | Search santri, program, target juz hafalan |
| 15 | **Mentor** | Direktori Kontak Orang Tua | `#tableMentorParents` | Search nama wali, nomor WA, anak binaan |
| 16 | **Mentor** | Export Laporan: Ringkasan Santri | `#tableMentorExportStudents` | Paging 5 baris, cari santri untuk di-export |
| 17 | **Mentor** | Export Laporan: Catatan Progres | `#tableMentorExportProgress` | Multi-filter surah, tanggal, tajwid & fluent |
| 18 | **Parent** | Riwayat Pendaftaran Program | `#tableParentEnrollments` | Search program, mentor, status pendaftaran |
| 19 | **Parent** | Riwayat Transaksi & SPP | `#tableParentPaymentHistory` | Cari invoice, bukti transfer, status lunas |
| 20 | **Parent** | Jadwal & Konfirmasi Absensi | `#tableParentSchedules` | Urut tanggal bimbingan, status hadir/izin |
| 21 | **Parent** | Daftar Anak / Santri Terdaftar | `#tableParentChildren` | Search anak, gender, umur, aksi detail |
| 22 | **Parent** | Riwayat Capaian Hafalan Anak | `#tableChildProgressHistory` | Urut surah/juz, filter tanggal, catatan mentor |

### 🛠️ Fitur Teknis Helper `datatables-init.js`:
- **Pembersih Colspan Baris Kosong (`emptyTable`)**: Menghapus `<tr><td colspan="...">` bawaan Blade saat data kosong sebelum inisialisasi agar tidak terjadi error internal `_DT_CellIndex`.
- **Bahasa Indonesia Baku**: Penerjemahan otomatis seluruh string DataTables (*"Menampilkan _START_ sampai _END_ dari _TOTAL_ entri"*, *"Pencarian:"*, *"Tidak ada data yang ditemukan"*, dll).
- **Auto-Recalculate On Tab/Modal**: Event listener `shown.bs.tab` dan `shown.bs.modal` otomatis memicu `columns.adjust().responsive.recalc()` sehingga tabel tidak pernah terpotong saat dibuka dalam tab atau modal.

---

---

## 🧭 3. STANDARDISASI DESAIN ANTARMUKA & NAVIGASI TERPADU (SEMUA PORTAL)

```mermaid
graph TD
    subgraph Public["🌐 Portal Publik / Landing"]
        NavbarPublic["Navbar Utama (Beranda, Program, Alur Belajar, FAQ, Kontak, Theme Toggle, Bell, Login)"]
    end

    subgraph AdminPortal["🛡️ Portal Administrator"]
        AdminNav["Top Header: Title & Subtitle + Beranda + Livewire Bell + Theme Toggle + Admin Dropdown"]
        AdminSide["Sidebar: Dashboard, Permohonan Jadwal, Santri Aktif, Santri, Mentor, Kuota, Program, SPP, Kontak, Settings"]
    end

    subgraph MentorPortal["👨‍🏫 Portal Mentor / Guru"]
        MentorNav["Top Header: Title & Subtitle + Beranda + Livewire Bell + Theme Toggle + Mentor Dropdown"]
        MentorSide["Sidebar: Dashboard, Jadwal Sesi, Santri Binaan, Data Ortu, Ketersediaan, Catat Progres, Massal, Laporan, Chat"]
    end

    subgraph ParentPortal["👨‍👩‍👧 Portal Orang Tua / Santri"]
        ParentNav["Top Header: Title & Subtitle + Beranda + Livewire Bell + Theme Toggle + User Dropdown"]
        ParentSide["Sidebar: Dashboard, Anak & Progres, Jadwal & Absensi, Pendaftaran, Tagihan SPP, Chat Mentor, Profil"]
    end
```

| Komponen Navigasi | Portal Publik (`navbar.blade.php`) | Portal Admin (`admin.blade.php`) | Portal Mentor (`mentor.blade.php`) | Portal Orang Tua & Santri (`parent.blade.php`) |
| :--- | :--- | :--- | :--- | :--- |
| **Container & Padding** | `container-fluid px-3 px-xl-5` | `admin-header` + `admin-sidebar` (270px) | `admin-header` + `admin-sidebar` (270px) | `admin-header` + `admin-sidebar` (270px) |
| **DataTables Styling** | - | Emerald Green + Dark Mode Support | Emerald Green + Dark Mode Support | Emerald Green + Dark Mode Support |
| **Notifikasi Bell** | Livewire Bell Drawer | Livewire Bell Drawer | Livewire Bell Drawer | Livewire Bell Drawer |
| **Theme Toggle** | Desktop & Mobile Toggle | Top Header Toggle | Top Header Toggle | Top Header Toggle |
| **Tautan Beranda** | Logo & Menu Beranda | Tombol "Beranda" Pill | Tombol "Beranda" Pill | Tombol "Beranda" Pill |
| **Profile Menu** | Dropdown User & Logout | Dropdown Admin & Logout | Dropdown Mentor & Logout | Dropdown Wali/Santri & Logout |
| **Mobile Drawer** | Collapse Navbar Smooth | Toggle Sidebar Offcanvas | Toggle Sidebar Offcanvas | Toggle Sidebar Offcanvas |

---

## 🔄 4. TAHAPAN & ALUR KERJA UTAMA APLIKASI

Operasional aplikasi AL-HIKMAH LMS terbagi menjadi **5 Tahapan Utama**:

```mermaid
flowchart LR
    A[1. Roadmap & Zero-Friction Booking] --> B[2. Review Admin & Counter-Offer]
    B --> C[3. Deal Jadwal & Real-Time Alert]
    C --> D[4. Pembayaran Lunas & Auto-Activation]
    D --> E[5. Bimbingan, Absensi & Evaluasi Progres]
```

1. **Tahap 1 - Pendaftaran & Pengajuan Jadwal Awal (Wali Santri)**:
   - Orang Tua memilih program, mengisi profil santri, menentukan preferensi hari & jam, serta memilih metode bimbingan (`Offline`, `Online`, `Hybrid`).
2. **Tahap 2 - Review Jadwal & Alokasi Mentor (Admin)**:
   - Admin memeriksa kuota ketersediaan guru pembimbing. Admin dapat langsung menyetujui (`Accept`) atau mengirimkan tawaran alternatif jadwal (`Counter-Offer`).
3. **Tahap 3 - Kesepakatan Jadwal & Penerbitan Tagihan (State Machine)**:
   - Setelah jadwal disepakati (`CONFIRMED`), sistem secara otomatis menerbitkan invoice tagihan biaya pendaftaran & SPP di portal wali santri.
4. **Tahap 4 - Pembayaran Lunas & Aktivasi Otomatis (Payment Gate)**:
   - Begitu pembayaran terverifikasi (`ACTIVE`), sistem meng-generate 4 minggu sesi belajar otomatis dan menampilkan santri pada daftar binaan mentor.
5. **Tahap 5 - Bimbingan, Absensi & Evaluasi Progres Berkala**:
   - Orang Tua mengisi konfirmasi kehadiran santri (Hadir/Izin/Sakit). Mentor menerima alert, membimbing, mencatat hafalan/jilid/halaman harian, dan mencetak laporan evaluasi periodik PDF.

---

## ⭐ 5. PENJELASAN SELURUH FITUR APLIKASI (BERDASARKAN HAK AKSES)

### A. Hak Akses: Administrator Lembaga (`/admin`)
- **Dashboard Utama**: Menampilkan analitik santri aktif, mentor terdaftar, permohonan baru, dan total pemasukan SPP, dilengkapi tabel dinamis DataTables.
- **Permohonan Jadwal**: Menangani verifikasi permohonan baru, counter-offer jadwal, konfirmasi massal, dan export Excel/CSV.
- **Manajemen Galeri Kegiatan**: Mengelola penuh dokumentasi foto kegiatan (`/admin/galleries`), penentuan foto unggulan hero slider, status publikasi, penataan urutan tampilan secara atomik (`DB::transaction`), filter kategori & program, serta pengelolaan Tong Sampah (`SoftDeletes`) dengan fitur *Restore* dan *Force Delete* otomatis menghapus berkas fisik storage.
- **Santri & Sesi Aktif**: Memantau jadwal belajar yang sedang aktif berjalan.
- **Database Santri**: Manajemen data identitas santri seluruh program.
- **Guru Pendamping**: Manajemen data profil dan status keaktifan ustaz/ustazah.
- **Ketersediaan & Alokasi Mentor**: Matriks penentuan slot jadwal luang mentor dan penugasan santri.
- **Program Belajar**: Manajemen paket belajar, kurikulum, dan struktur biaya.
- **Tagihan & SPP Santri**: Monitoring pembayaran, pencatatan manual, dan pengiriman notifikasi pengingat SPP.
- **Pesan Konsultasi**: Manajemen pesan masuk dari formulir kontak publik.
- **Pengguna & Hak Akses**: Manajemen akun user dan penugasan peran (Admin, Mentor, Parent, Student).
- **Pengaturan Website**: Konfigurasi profil lembaga, kontak WhatsApp, nomor rekening, dan aset web.

### B. Hak Akses: Guru / Pendamping (`/mentor`)
- **Dashboard Mengajar**: Ringkasan jadwal hari ini lengkap dengan badge status kehadiran santri real-time dan DataTables.
- **Jadwal Sesi Mengajar**: Daftar seluruh sesi belajar dilengkapi filter status dan hari dalam Bahasa Indonesia.
- **Santri Binaan Resmi**: Daftar santri aktif yang telah lunas administrasi pembayarannya.
- **Data Orang Tua**: Kontak wali santri untuk koordinasi bimbingan.
- **Atur Ketersediaan**: Form pemilihan slot hari dan jam mengajar mentor (Senin s.d. Minggu).
- **Catat Progres Harian & Massal**: Formulir input perkembangan capaian santri (jilid, surat, ayat, nilai kelancaran, makhraj, dan adab).
- **Laporan Kinerja**: Unduh laporan rekapitulasi capaian mengajar ke format PDF resmi.
- **Pesan & Diskusi**: Ruang komunikasi langsung dengan wali santri.
- **Profil Mentor**: Pengaturan biodata diri dan spesialisasi mengajar.

### C. Hak Akses: Orang Tua / Wali Santri (`/parent`)
- **Dashboard Utama**: Ringkasan perkembangan ananda, jadwal belajar terdekat, dan status SPP.
- **Anak & Progres**: Rapor evaluasi belajar harian dan capaian hafalan anak dengan tabel interaktif.
- **Jadwal Belajar & Absensi**: Kalender sesi bimbingan dan formulir konfirmasi kehadiran (Hadir/Izin/Sakit).
- **Pendaftaran & Negosiasi**: Pendaftaran anak baru dan penanganan negosiasi jadwal dengan admin.
- **Tagihan & SPP**: Daftar invoice pembayaran pendaftaran & SPP bulanan beserta bukti bayar.
- **Pesan & Chat Mentor**: Chat langsung dengan guru pembimbing ananda.
- **Profil & Akun**: Manajemen data diri wali santri dan penambahan akun anak.

### D. Hak Akses: Santri (`/student`)
- **Dashboard Ruang Belajar**: Menampilkan statistik capaian tilawah, hafalan Al-Qur'an, dan jadwal mengaji hari ini dengan antarmuka yang bersih dan ramah anak.

### E. Portal Publik / Pengunjung (`/galeri`, `/program`, `/roadmap`, `/biaya`, `/faq`, `/kontak`)
- **Etalase Galeri Interaktif (`/galeri`)**: Etalase dokumentasi kegiatan AL-HIKMAH dengan Hero Slideshow Carousel, Filter Kategori Utama & Event, Filter Program Belajar, Tag Cloud, pencarian kata kunci, Lightbox Modal Viewer dengan rincian foto & takarir, tracker hitung tayang anti-spam sesi (`/galeri/{id}/view`), dan tombol Multi-Share ke WhatsApp, Facebook, serta Copy Link.

---

## 🔔 6. SISTEM NOTIFIKASI & ALERT TERPUSAT (CENTRALIZED ALERT SYSTEM)

Sistem notifikasi dan umpan balik pengguna di seluruh aplikasi AL-HIKMAH LMS dibangun secara berlapis (**4-Layer Alert & Feedback Architecture**) untuk menjamin pengalaman pengguna (UX) yang responsif, informatif, dan aman dari kesalahan:

```mermaid
flowchart TD
    A[Aksi Pengguna / Sistem] --> B{Lapisan Notifikasi}
    B --> C[Layer 1: In-Page Alert Banners]
    B --> D[Layer 2: Global Floating Toast <x-flash-toast />]
    B --> E[Layer 3: Livewire Notification Bell Drawer]
    B --> F[Layer 4: WhatsApp Gateway Outbound]
```

### 📋 4 Lapisan Sistem Alert & Notifikasi:
1. **Layer 1 - In-Page Alert Banners (Spesifik Halaman & Formulir)**:
   - **Banner Pesan Sukses (`session('success')`)**: Banner hijau rounded dengan ikon centang dan tombol dismiss, memberikan konfirmasi visual bahwa aksi tambah, ubah, hapus, pulihkan, atau toggle berhasil.
   - **Banner Pesan Peringatan & Gagal (`session('error')`, `session('warning')`)**: Banner merah/kuning untuk kegagalan aksi atau batas kuota.
   - **Validasi Kesalahan Formulir (`$errors->any()`)**: Menampilkan daftar rincian input yang belum lengkap/valid di bagian atas form serta penanda *is-invalid* pada field bersangkutan.
2. **Layer 2 - Global Floating Flash Toast Notifications (`<x-flash-toast />`)**:
   - Terpasang di seluruh layout utama (`layouts.admin`, `layouts.mentor`, `layouts.parent`, `layouts.landing`).
   - Menampilkan pop-up toast modern di pojok kanan atas layar dengan animasi otomatis menghilang setelah 5 detik.
3. **Layer 3 - Livewire 3 Real-Time Notification Bell**:
   - Terpasang pada seluruh bilah navigasi atas (Top Header Navbar) untuk Admin, Mentor, Orang Tua, dan Santri.
   - Dilengkapi lencana jumlah notifikasi belum dibaca (*unread count badge*), laci (*drawer*) riwayat notifikasi, tombol tandai telah dibaca (`markAsRead`), tandai semua dibaca (`markAllAsRead`), dan *auto-polling* berkala.
4. **Layer 4 - Integrasi WhatsApp Gateway Outbound**:
   - Mendukung pengiriman pesan WhatsApp otomatis ke nomor handphone pengguna ketika terjadi peristiwa penting (persetujuan jadwal bimbingan, tagihan SPP terbit, pengingat jatuh tempo, dan konfirmasi kehadiran santri).

---

## 🗄️ 7. PENJELASAN SELURUH DATABASE (PENYIMPANAN DATA LEMBAGA)

Terdapat 20 tabel utama dalam sistem database AL-HIKMAH LMS:

1. **`users`**: Data otentikasi dan akun pengguna (nama, email, password, role_id, no_whatsapp, avatar).
2. **`roles`**: Master peran sistem (`admin`, `mentor`, `parent`, `student`).
3. **`students`**: Profil santri (nama, tanggal lahir, jenis kelamin, tingkat pendidikan, wali_id).
4. **`mentors`**: Profil guru pembimbing (user_id, bio, spesialisasi, status ketersediaan).
5. **`programs`**: Paket bimbingan Al-Qur'an (Tahsin, Tahfidz, Tajwid, Bahasa Arab, dll).
6. **`galleries`**: Dokumentasi kegiatan AL-HIKMAH (id, title, slug, category, program_id, image_url, caption, description, event_date, location, tags, is_featured, is_published, sort_order, views_count, uploaded_by, deleted_at).
7. **`enrollments`**: Data permohonan pendaftaran, negosiasi jadwal, dan metode belajar (`learning_method`).
8. **`mentor_availabilities`**: Matriks slot hari dan jam luang setiap mentor.
9. **`mentor_student_allocations`**: Relasi penetapan guru pembimbing untuk setiap santri.
10. **`learning_sessions`**: Jadwal sesi belajar 4 minggu (tanggal, jam, metode belajar, mentor_id, student_id, status).
11. **`session_confirmations`**: Data konfirmasi kehadiran dari orang tua (session_id, status: Hadir/Izin/Sakit, catatan).
12. **`progress_reports`**: Catatan capaian harian santri (surat, ayat, jilid, halaman, tajwid, makhraj, adab).
13. **`payments`**: Data tagihan dan transaksi pembayaran (invoice_number, amount, status, payment_method, proof_file).
14. **`notifications`**: Riwayat notifikasi in-app pengguna.
15. **`contact_messages`**: Pesan konsultasi dari formulir kontak publik website.
16. **`messages`**: Pesan komunikasi langsung antara orang tua dan mentor.
17. **`settings`**: Konfigurasi website dan profil lembaga.
18. **`sessions`, `cache`, `jobs`, `failed_jobs`**: Tabel pendukung performa dan antrean Laravel.

---

## 🎮 8. PENJELASAN SELURUH MODEL & CONTROLLER (PEMROSES LOGIKA SISTEM)

### A. Model Eloquent Utama
1. **`App\Models\Gallery.php`**: Mengelola entitas galeri foto kegiatan, SoftDeletes lifecycle, konstanta taksonomi kategori (`CATEGORIES`) & rekomendasi tag (`DEFAULT_TAGS`), query scopes (`published`, `featured`, `category`, `programFilter`, `tagFilter`), serta accessors URL aset storage.
2. **`App\Models\Enrollment.php`**: Mengelola siklus hidup pendaftaran santri, konstanta hari Bahasa Indonesia (`DAYS`), metode belajar (`learning_method`), dan pembuatan sesi 4 minggu otomatis (`generateInitialLearningSessions`).
3. **`App\Models\Session.php`**: Mengelola data sesi belajar bimbingan beserta relasi konfirmasi kehadiran (`confirmation()`).
4. **`App\Models\SessionConfirmation.php`**: Mengelola data konfirmasi kehadiran orang tua.
5. **`App\Models\Notification.php`**: Mengelola entitas notifikasi sistem.
6. **`App\Models\ContactMessage.php`**: Mengelola pesan konsultasi publik.

### B. Controller, Observer & Service Utama
1. **`App\Http\Controllers\Admin\GalleryController.php`**: Mengolah CRUD dokumentasi kegiatan admin, toggle hero slider & publish, reorder atomik (`DB::transaction`), filter pencarian, serta manajemen Tong Sampah (`status=trashed`, `restore`, `forceDelete`).
2. **`App\Observers\GalleryObserver.php`**: Mengatur penghapusan berkas fisik gambar di storage publik saat gambar diganti (`updating`) atau saat data dihapus permanen (`forceDeleted`).
3. **`App\Http\Requests\StoreGalleryRequest.php` & `UpdateGalleryRequest.php`**: Validasi form data galeri dan pembersihan tag string menjadi array (`prepareForValidation`).
4. **`App\Services\NotificationService.php`**: Service terpusat pengiriman notifikasi in-app & WhatsApp.
5. **`Admin\EnrollmentController.php`**: Mengolah permohonan pendaftaran, konfirmasi deal jadwal (`accept`), penawaran alternatif (`offerAlternative`), konfirmasi masal (`bulkAccept`), serta pengunduhan data pendaftaran ke Excel/CSV (`export`).
6. **`LandingController.php`**: Mengolah etalase galeri publik (`galeri()`) dan tracker hitung tayangan anti-spam sesi (`incrementView()`).

---

## 📁 9. PENJELASAN SELURUH STRUKTUR FOLDER APLIKASI

```text
al-hikmah-lms/
├── 📁 app/                     --> Pusat Logika Utama Aplikasi (Model, Controller, Services, Livewire, Enums, Observers, Requests)
│   ├── 📁 Enums/               --> Enum Tipe Notifikasi & Status Enrollment (NotificationType.php, EnrollmentStatus.php)
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/    --> Pengendali alur kerja aplikasi (Landing, Contact, Admin, Mentor, Parent, Auth)
│   │   │   └── 📁 Admin/       --> Controller Admin (GalleryController, EnrollmentController, MentorAvailabilityController, PaymentController, Users)
│   │   └── 📁 Requests/       --> Form Requests Validasi (StoreGalleryRequest.php, UpdateGalleryRequest.php)
│   ├── 📁 Livewire/            --> Komponen Livewire Real-time (NotificationBell.php)
│   ├── 📁 Observers/           --> Eloquent Event Observers (GalleryObserver.php)
│   ├── 📁 Services/            --> Service Layanan (NotificationService.php, WhatsAppService.php)
│   └── 📁 Models/              --> Pengatur struktur data & relasi database (Gallery.php, Enrollment.php, User.php, dll)
├── 📁 bootstrap/               --> Inisialisasi sistem Laravel 12
├── 📁 config/                  --> Berkas konfigurasi sistem (app.php - Locale id & Timezone Asia/Jakarta)
├── 📁 database/                --> Migration, Factories, & Seeders (GallerySeeder.php, GalleryFactory.php)
├── 📁 public/                  --> Assets CSS (style.css), JS (scripts.js), Gambar Logo & Galeri
│   └── 📁 assets/              --> DataTables (datatables.min.js, datatables.min.css), js (datatables-init.js)
├── 📁 resources/views/         --> Tampilan Antarmuka Blade (.blade.php)
│   ├── 📁 layouts/             --> Master Layout (landing.blade.php, admin.blade.php, mentor.blade.php, parent.blade.php)
│   ├── 📁 partials/            --> Komponen UI (navbar.blade.php, footer.blade.php)
│   ├── 📁 admin/               --> Halaman Admin (galleries/index, create, edit, Enrollments, Mentors, Students, Users, Settings)
│   ├── 📄 galeri.blade.php     --> Halaman Etalase Galeri Dokumentasi Kegiatan Interaktif Publik
│   ├── 📄 roadmap.blade.php    --> Halaman Panduan Alur Pendaftaran Interaktif
│   ├── 📄 contact.blade.php    --> Halaman Formulir Konsultasi Hubungi Kami
│   └── 📄 faq.blade.php        --> Halaman Tanya Jawab Publik
├── 📁 routes/                  --> Alamat URL Website (web.php)
└── 📁 tests/                   --> Pengujian Otomatis Pest/PHPUnit (146 Passed Tests - 100% GREEN PASS)
    └── 📁 Feature/             --> Integration Tests (GalleryFeatureTest.php, MentorSessionAndAttendanceTest.php, dll)
```

---

## 🧪 10. HASIL PENGUJIAN & QUALITY ASSURANCE (100% GREEN PASS)

Kualitas sistem diuji secara ketat menggunakan **Automated Test Suite (Pest / PHPUnit Framework)**:

- **Total Pengujian**: **146 Test Cases (606 Assertions)**
- **Hasil**: ✅ **100% PASSED (0 FAILURES, 0 ERRORS)**
- **Format Kode**: Diformat ulang secara otomatis mengacu pada aturan PSR-12 menggunakan `vendor/bin/pint --format agent`.

### 📋 Cakupan Pengujian:
1. `GalleryCategoryFeatureTest.php` (11 Passed):
   - `non admin cannot access gallery categories management`
   - `admin can view gallery categories management list with stats`
   - `admin can create new gallery category with valid data`
   - `admin can update existing gallery category`
   - `admin can toggle category active status`
   - `admin can reorder categories atomically`
   - `admin can delete category and safely detach linked galleries`
   - `admin can view trashed gallery categories`
   - `admin can restore trashed gallery category`
   - `admin can permanently force delete gallery category`
   - `public gallery loads active categories from database`
2. `GalleryFeatureTest.php` (8 Passed):
   - `public user can view gallery page and active items`
   - `non admin cannot access admin gallery management`
   - `admin can view admin gallery management list`
   - `admin can upload new gallery item with tags and file`
   - `admin can soft delete gallery item move to trash and restore it`
   - `admin force delete permanently removes record and observer deletes storage file`
   - `view counter increments once per session anti spam`
   - `admin can reorder galleries atomically`
3. `MentorSessionAndAttendanceTest.php` (4 Passed)
4. `NotificationAlertSystemTest.php` (8 Passed)
5. `MentorRefinementTest.php` (10 Passed)
6. `EnrollmentNegotiationTest.php` (8 Passed)
7. `ParentStateGatingTest.php` (4 Passed):
   - `redirects to dashboard when state 1 (onboarding) parent accesses protected routes`
   - `redirects to dashboard when state 2 (transisi) parent accesses protected routes`
   - `allows access to protected routes when state 3 (active) parent accesses them`
   - `handles memoization correctly so repeated calls do not query database multiple times`
8. `ParentChildRegistrationGatingTest.php` (5 Passed):
   - `redirects direct registered parent to children registration when accessing biaya without children`
   - `displays onboarding state 1A (Isi Data Anak) on dashboard when parent has 0 children`
   - `successfully registers child from parent profile children page`
   - `allows parent to access biaya and displays state 1B on dashboard once child is registered`
   - `redirects parent without children when accessing enrollment create page`
9. Seluruh modul otentikasi, manajemen pengguna, pelaporan, dan pendaftaran lulus uji.

---

## 🎯 11. KESIMPULAN & REKOMENDASI LANGKAH KE DEPAN

### 🏁 Kesimpulan Akhir:
Aplikasi **AL-HIKMAH LMS (Versi 4.8)** berada dalam kondisi **100% Selesai, Teruji (146 Passed Tests - 100% GREEN PASS), dan Siap Digunakan (Production Ready)**. Seluruh tabel penyajian data di Dashboard Admin, Mentor, dan Parent kini menggunakan rangkaian **Modern DataTables Suite**, manajemen registrasi prasyarat data anak (Prerequisite Child Registration Gating), Onboarding Stepper adaptif, manajemen dinamis kategori galeri admin, etalase galeri interaktif publik, manajemen dokumentasi dengan tong sampah (*soft deletes*), pembersihan berkas otomatis, anti-spam tracker tayang sesi, reorder atomik, navigasi terpadu, sistem dark mode tersinkronisasi, lokalisasi Bahasa Indonesia baku, sistem state gating 3 lapis untuk wali santri, serta notifikasi real-time telah berfungsi sempurna, stabil, aman, cepat, dan modern.

### 🚀 Rekomendasi Langkah Ke Depan untuk Pimpinan / Manajemen:
1. **Go-Live / Deploy ke Server Production**: Aplikasi dapat langsung di-deploy ke server live (domain utama lembaga).
2. **Konfigurasi WhatsApp Gateway (Opsional)**: Memasukkan `WHATSAPP_API_KEY` di berkas `.env` server untuk mengaktifkan pengiriman WhatsApp otomatis ke HP Orang Tua & Mentor.
3. **Sosialisasi Admin & Pengajar**: Memberikan pelatihan singkat kepada admin mengenai pengelolaan foto dan kategori kegiatan serta kepada pengajar mengenai pelaporan progres hafalan.

---

*Laporan eksekutif resmi ini disusun khusus untuk jajaran Manajemen / Pimpinan Lembaga AL-HIKMAH.*

