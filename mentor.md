✅ F. MODUL DASHBOARD MENTOR (Role-Based Dashboard)
Tujuan: Memberikan antarmuka khusus bagi mentor/guru untuk mengelola aktivitas mengajar mereka secara efisien dan terpusat.

Akses: http://127.0.0.1:8000/mentor/dashboard

F.1. KOMPONEN DASHBOARD MENTOR
Komponen	Deskripsi	Teknologi
Ringkasan Hari Ini	Kartu informasi: Jumlah sesi hari ini, santri yang akan datang, sesi yang sudah selesai	Livewire Counter
Jadwal Mengajar Hari Ini	Daftar sesi belajar hari ini dengan waktu, santri, program, dan status (Online/Offline/Hybrid)	Livewire TodaySchedule
Sesi Mendatang (Next 7 Days)	Kalender/daftar sesi 7 hari ke depan dengan filter minggu ini	Livewire UpcomingSessions
Santri Aktif	Daftar santri yang menjadi binaan mentor beserta progres terakhir (hafalan, tajwid, adab)	Livewire MyStudents
Capaian Hafalan Terbaru	Grafik batang atau daftar 5 santri dengan progres hafalan tertinggi/terendah	Chart.js atau Livewire Chart
Notifikasi & Pengingat	Notifikasi dari admin (perubahan jadwal, informasi penting)	Livewire MentorNotifications
Aksi Cepat	Tombol: "Catat Progres", "Lihat Semua Santri", "Kalender Sesi"	Blade Component
F.2. FITUR KHUSUS MENTOR (Selain Dashboard)
Fitur	Fungsi	Route/URL
Manajemen Sesi Belajar	Mentor dapat melihat, mengkonfirmasi, atau membatalkan sesi yang dijadwalkan	/mentor/sessions
Catat Progres Hafalan	Form cepat untuk mencatat hafalan (Surah/Ayat/Juz) + nilai tajwid + catatan adab	/mentor/progress/create
Daftar Santri Binaan	Tabel semua santri yang menjadi tanggung jawab mentor	/mentor/students
Detail Santri	Riwayat lengkap hafalan, nilai, dan laporan PDF per santri	/mentor/students/{id}
Laporan Per Mentor	Ekspor laporan kinerja mentor (jumlah sesi, rata-rata nilai santri)	/mentor/reports
Pengaturan Akun	Edit profil, foto, spesialisasi mengajar	/mentor/profile
F.3. USER STORY MENTOR
Sebagai mentor, saya ingin melihat dashboard yang berisi ringkasan jadwal mengajar saya hari ini dan 7 hari ke depan, sehingga saya bisa mempersiapkan diri sebelum sesi dimulai.

Sebagai mentor, saya ingin mencatat progres hafalan santri dengan cepat dan mudah, tanpa harus membuka banyak halaman.

Sebagai mentor, saya ingin melihat daftar semua santri binaan saya beserta capaian terakhir mereka, agar saya tahu siapa yang perlu perhatian lebih.

Sebagai mentor, saya ingin menerima notifikasi jika ada perubahan jadwal dari admin, agar saya tidak ketinggalan informasi.

F.4. WIREFRAME KONSEP DASHBOARD MENTOR
text
┌─────────────────────────────────────────────────────────────┐
│  🕌 AL-HIKMAH LMS  |  Mentor: Ustadz Ahmad  |  [Profil]  │
├─────────────────────────────────────────────────────────────┤
│  📊 DASHBOARD MENTOR                                       │
├──────────────┬──────────────┬──────────────┬──────────────┤
│ 📅 Sesi Hari │ 👨‍🎓 Santri   │ 📈 Rata-rata  │ ⏳ Sesi       │
│ Ini: 4       │ Aktif: 12    │ Nilai: 87    │ Mendatang: 6 │
├──────────────┴──────────────┴──────────────┴──────────────┤
│  ⏰ JADWAL MENGAJAR HARI INI                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 08:00 - 09:00  │ Ahmad F.  │ Tahfidz │ Online  │ ✅ │  │
│  │ 09:30 - 10:30  │ Siti A.   │ Tahsin  │ Offline │ ⏳ │  │
│  │ 13:00 - 14:00  │ Budi S.   │ Iqra    │ Hybrid  │ ⏳ │  │
│  │ 15:00 - 16:00  │ Rani M.   │ Tajwid  │ Online  │ ⏳ │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                            │
│  📋 SANTRI BINAAN & PROGRES TERAKHIR                      │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Nama     │ Surah Terakhir │ Juz │ Tajwid │ Adab    │  │
│  │ Ahmad F. │ Al-Baqarah 255 │ 3   │ 85     │ Baik    │  │
│  │ Siti A.  │ An-Nisa 1-10   │ 4   │ 90     │ Sangat  │  │
│  │ Budi S.  │ Al-Fatihah     │ 1   │ 75     │ Cukup   │  │
│  │ Rani M.  │ Al-Mulk 1-5    │ 29  │ 88     │ Baik    │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                            │
│  🔔 NOTIFIKASI (2)                                         │
│  • Sesi Budi S. diubah ke jam 14:00 (Admin)               │
│  • Pengumuman: Rapat mentor Sabtu 14/08                   │
├─────────────────────────────────────────────────────────────┤
│  [📝 Catat Progres] [👨‍🎓 Semua Santri] [📅 Kalender]     │
└─────────────────────────────────────────────────────────────┘
F.5. STRUKTUR DATABASE UNTUK MENTOR
Tabel	Kolom Tambahan untuk Mentor
users	mentor_specialization (spesialisasi: Tahfidz/Tahsin/Iqra)
learning_sessions	mentor_id (foreign key ke users), confirmed_by_mentor (boolean)
progress	mentor_id (pencatat progres)
mentor_student	Tabel pivot: mentor_id, student_id, assigned_date
F.6. TEKNIS IMPLEMENTASI
Komponen	Detail
Route	Route::middleware(['auth', 'role:mentor'])->group()
Controller	MentorDashboardController, MentorSessionController, MentorProgressController
Livewire	TodaySchedule, UpcomingSessions, MyStudents, MentorNotifications
View	mentor/dashboard.blade.php, mentor/sessions/index.blade.php, mentor/students/index.blade.php
Permission	Hanya user dengan role mentor yang bisa akses semua route /mentor/*
F.7. REVISI ROADMAP (Ditambahkan ke Bulan 1)
Bulan	Fokus	Deliverable (Tambahan)
Bulan 1	Dashboard Mentor	- Tampilan dashboard mentor
- Komponen jadwal hari ini & 7 hari
- Daftar santri binaan
✅ KESIMPULAN TAMBAHAN
Dengan penambahan modul Mentor Dashboard ini, maka:

Role Mentor memiliki pengalaman pengguna yang utuh dan tidak sekadar "bisa login" saja.

Efisiensi kerja mentor meningkat karena semua informasi penting tersedia dalam satu halaman.

Sistem RBAC menjadi lebih seimbang (Admin, Mentor, Orang Tua, Santri semuanya punya dashboard fungsional).