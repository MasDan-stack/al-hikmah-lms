📋 ANALISIS & IMPROVEMENT: SISTEM REKRUTMEN & ONBOARDING MENTOR (v8.3)

🔍 Ringkasan Eksekutif

Setelah mempelajari Tentang.md (v8.2) secara mendalam, ditemukan bahwa dokumen Onboarding.md yang ada saat ini belum sepenuhnya selaras dengan ekosistem AL-HIKMAH LMS yang sudah berjalan. Terdapat beberapa inkonsistensi struktural, fitur yang terlewat untuk diintegrasikan, dan peluang improvement signifikan.

📊 Status Kesesuaian dengan v8.2

| Aspek | Status | Keterangan |
|-------|--------|------------|
| Struktur Database | ⚠️ Perlu Penyesuaian | Ada duplikasi field dengan tabel mentors existing |
| Integrasi Service Layer | ❌ Belum Ada | Tidak memanfaatkan 14+ service yang sudah ada |
| Konsistensi UI/UX | ⚠️ Perlu Disesuaikan | Belum mengadopsi standar DataTables & ApexCharts |
| Audit & Compliance | ❌ Belum Ada | Tidak terintegrasi dengan financialauditlogs |
| Notifikasi | ❌ Belum Ada | Tidak memanfaatkan WhatsAppService existing |
| Testing Standard | ⚠️ Kurang Komprehensif | Target 30 test cases, seharusnya 50+ mengikuti standar v8.2 |
| HR Dashboard Integration | ❌ Belum Ada | Tidak terhubung dengan modul Beban Kerja Guru v8.2 |
| Alert System | ❌ Belum Ada | Tidak terintegrasi dengan Operational Alerts 3-Tier |

🎯 Analisis Kesenjangan (Gap Analysis)

❌ Inkonsistensi yang Ditemukan

Duplikasi Field pada Tabel mentors
Onboarding.md mengusulkan penambahan kolom yang sudah ada di v8.2:
❌ rating_score → Sudah ada (digunakan oleh Top Performing Mentors)
❌ specialization → Sudah ada (spesialisasi Tahfidz/Tahsin/Iqra)
❌ total_evaluations → Sudah ada (untuk pemeringkatan)

Tidak Memanfaatkan Service Existing
✅ WhatsAppService.php      → Harus dipakai untuk notifikasi lamaran
✅ StudentAccountService.php → Polanya bisa direplikasi untuk MentorAccountService
✅ AlertService.php          → Harus trigger alert saat lamaran baru masuk
✅ FinancialAuditLog.php     → Harus log setiap aksi kritis rekrutmen
✅ RevenueAnalyticsService.php → Pola hybrid rendering bisa diadopsi
✅ BroadcastService.php      → Bisa untuk broadcast lowongan mentor

Tidak Terintegrasi dengan Dashboard HR v8.2
Modul Beban Kerja Guru sudah memiliki:
Matriks Kapasitas (Optimal ≤30, Sibuk 31-40, Overload >40)
Top Performing Mentors ranking
Monitoring Cuti Harian
Distribusi Beban Santri per Program

→ Fitur rekrutmen harus memperkaya dashboard ini, bukan berdiri sendiri.

🚀 FITUR YANG DISUSULKAN (v8.3 — Improved Version)

📐 Arsitektur Fitur yang Disesuaikan dengan v8.2

┌──────────────────────────────────────────────────────────────────────────┐
│     AL-HIKMAH MENTOR RECRUITMENT & ONBOARDING ENGINE (v8.3)              │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────────┐    ┌──────────────────┐    ┌──────────────────┐   │
│  │ PUBLIC PORTAL   │    │ ADMIN RECRUITMENT│    │ MENTOR ONBOARDING│   │
│  │ - Form Lamaran  │───▶│ - Dashboard KPI  │───▶│ - Auto Account   │   │
│  │ - Status Track  │    │ - CV Viewer      │    │ - Training Path  │   │
│  │ - AI Test Prep  │    │ - Test Scoring   │    │ - Probation Track│   │
│  └─────────────────┘    │ - Interview Mgmt │    │ - First Session  │   │
│                          │ - Approval Flow  │    └──────────────────┘   │
│                          └──────────────────┘                            │
│                                    │                                     │
│          ┌─────────────────────────┼─────────────────────────┐          │
│          ▼                         ▼                         ▼          │
│  ┌──────────────┐       ┌──────────────────┐      ┌─────────────────┐ │
│  │ INTEGRATION  │       │ INTEGRATION      │      │ INTEGRATION     │ │
│  │ LAYER        │       │ LAYER            │      │ LAYER           │ │
│  ├──────────────┤       ├──────────────────┤      ├─────────────────┤ │
│  │✅WhatsAppSvc │       │✅AlertService    │      │✅HR Dashboard   │ │
│  │✅AuditLog    │       │✅ApexCharts      │      │✅Overload Guard │ │
│  │✅DataTables  │       │✅Dual Export     │      │✅Gamification   │ │
│  │✅AI Gemini   │       │✅BroadcastSvc    │      │✅Badge System   │ │
│  └──────────────┘       └──────────────────┘      └─────────────────┘ │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

🗄️ Perbaikan Database Schema (Disesuaikan dengan v8.2)

✅ Tabel Baru yang Benar-Benar Dibutuhkan

mentor_applications (Disempurnakan)
CREATE TABLE mentor_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Data Pribadi
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    birth_date DATE NULL,
    gender ENUM('male','female') DEFAULT 'male',
    address TEXT NULL,
    city VARCHAR(100) NULL,
    
    -- Pendidikan & Pengalaman
    education VARCHAR(255) NULL,
    institution VARCHAR(255) NULL,
    experience_years TINYINT UNSIGNED NULL,
    experience_description TEXT NULL,
    specialization ENUM('tahfidz','tahsin','iqra','comprehensive') DEFAULT 'comprehensive',
    
    -- Dokumen (integrasi dengan Storage existing)
    cv_path VARCHAR(255) NULL,
    certificate_path VARCHAR(255) NULL,
    photo_path VARCHAR(255) NULL,
    sanad_chain TEXT NULL,  -- Riwayat sanad keilmuan
    
    -- Tes Hafalan (integrasi dengan GeminiQuestionService)
    juz30testscore DECIMAL(5,2) NULL,  -- 0-100
    juz30testgrade ENUM('excellent','good','fair','poor','failed') NULL,
    juz30testnotes TEXT NULL,
    
    tahsintestscore DECIMAL(5,2) NULL,
    tahsintestgrade ENUM('excellent','good','fair','poor','failed') NULL,
    tahsintestnotes TEXT NULL,
    
    teachingsimulationscore DECIMAL(5,2) NULL,
    teachingsimulationnotes TEXT NULL,
    
    tester_id BIGINT UNSIGNED NULL,
    testcompletedat TIMESTAMP NULL,
    
    -- Wawancara
    interview_date DATETIME NULL,
    interview_mode ENUM('online','offline','phone') DEFAULT 'online',
    interview_score DECIMAL(5,2) NULL,
    interview_result ENUM('recommended','pending','rejected') NULL,
    interview_notes TEXT NULL,
    interviewer_id BIGINT UNSIGNED NULL,
    
    -- Status & Workflow
    status ENUM(
        'submitted',           -- Baru masuk
        'document_review',     -- Admin review CV
        'test_scheduled',      -- Tes dijadwalkan
        'test_completed',      -- Tes selesai, menunggu nilai
        'interview_scheduled', -- Wawancara dijadwalkan
        'interview_completed', -- Wawancara selesai
        'approved',            -- Diterima
        'rejected',            -- Ditolak
        'withdrawn'            -- Calon mundur
    ) DEFAULT 'submitted',
    
    current_stage TINYINT UNSIGNED DEFAULT 1, -- 1-6 untuk progress tracker
    admin_notes TEXT NULL,
    rejection_reason TEXT NULL,
    
    -- Audit & Tracking
    submitted_at TIMESTAMP NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    lastactivityat TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (tester_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (interviewer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes (mengikuti standar v8.2)
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idxstatussubmitted (status, submitted_at),
    INDEX idx_specialization (specialization)
);

mentorapplicationdocuments (Pemisahan Dokumen — Best Practice)
CREATE TABLE mentorapplicationdocuments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    document_type ENUM('cv','certificate','sanad','photo','ijazah','skck','recommendation') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size INT UNSIGNED NULL,
    mime_type VARCHAR(100) NULL,
    uploaded_at TIMESTAMP NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    is_valid BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (applicationid) REFERENCES mentorapplications(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idxapplicationtype (applicationid, documenttype)
);

mentortestsessions (Menggantikan mentorinterviewschedules — Lebih Komprehensif)
CREATE TABLE mentortestsessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    sessiontype ENUM('juz30test','tahsintest','teachingsimulation','interview') NOT NULL,
    scheduled_at DATETIME NOT NULL,
    duration_minutes INT DEFAULT 45,
    mode ENUM('online','offline') DEFAULT 'online',
    meeting_link VARCHAR(255) NULL,
    location VARCHAR(255) NULL,
    
    -- AI Integration (GeminiQuestionService)
    questionsetid BIGINT UNSIGNED NULL,  -- Relasi ke questions table
    ai_generated BOOLEAN DEFAULT FALSE,
    
    -- Result
    score DECIMAL(5,2) NULL,
    grade ENUM('excellent','good','fair','poor','failed') NULL,
    evaluator_notes TEXT NULL,
    evaluator_id BIGINT UNSIGNED NULL,
    
    status ENUM('scheduled','in_progress','completed','cancelled','rescheduled') DEFAULT 'scheduled',
    completed_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (applicationid) REFERENCES mentorapplications(id) ON DELETE CASCADE,
    FOREIGN KEY (questionsetid) REFERENCES questions(id) ON DELETE SET NULL,
    FOREIGN KEY (evaluator_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idxapplicationtype (applicationid, sessiontype),
    INDEX idxscheduled (scheduledat, status)
);

mentorprobationtracking (BARU — Tidak ada di Onboarding.md original)
CREATE TABLE mentorprobationtracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mentor_id BIGINT UNSIGNED NOT NULL,
    probationstartdate DATE NOT NULL,
    probationenddate DATE NOT NULL,
    probationdurationmonths TINYINT UNSIGNED DEFAULT 3,
    
    -- Milestone Checklist
    orientation_completed BOOLEAN DEFAULT FALSE,
    firstsessioncompleted BOOLEAN DEFAULT FALSE,
    trainingmodulescompleted TINYINT UNSIGNED DEFAULT 0,
    trainingmodulesrequired TINYINT UNSIGNED DEFAULT 5,
    
    -- Progress Metrics
    totalsessionsconducted INT UNSIGNED DEFAULT 0,
    totalstudentsassigned INT UNSIGNED DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT NULL,
    parentfeedbackscore DECIMAL(3,2) DEFAULT NULL,
    
    -- Evaluation
    midprobationreview DATE NULL,
    midprobationnotes TEXT NULL,
    finalevaluationdate DATE NULL,
    final_result ENUM('passed','extended','failed') NULL,
    final_notes TEXT NULL,
    
    -- Status
    status ENUM('active','completed','extended','failed') DEFAULT 'active',
    reviewed_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idxmentorstatus (mentor_id, status)
);

mentor_trainings (Disesuaikan — Integrasi dengan Badge System)
CREATE TABLE mentor_trainings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mentor_id BIGINT UNSIGNED NOT NULL,
    training_name VARCHAR(200) NOT NULL,
    trainingcategory ENUM('pedagogy','tajwid','tahfidzmethod','technology','adab','leadership') NOT NULL,
    provider VARCHAR(150) NOT NULL,
    training_date DATE NULL,
    duration_hours DECIMAL(4,1) NULL,
    certificate_path VARCHAR(255) NULL,
    expiry_date DATE NULL,
    
    -- Gamification Integration
    points_rewarded INT UNSIGNED DEFAULT 0,
    badge_id BIGINT UNSIGNED NULL,  -- Badge yang diraih setelah training
    
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE SET NULL,
    INDEX idxmentorcategory (mentorid, trainingcategory)
);

⚠️ Modifikasi Tabel mentors (HANYA field yang BELUM ADA)

-- Field yang SUDAH ADA di v8.2 (JANGAN ditambahkan lagi):
-- ✅ ratingscore, specialization, totalevaluations

-- Field BARU yang perlu ditambahkan:
ALTER TABLE mentors
    ADD COLUMN application_id BIGINT UNSIGNED NULL AFTER id,
    ADD COLUMN join_date DATE NULL,
    ADD COLUMN probationenddate DATE NULL,
    ADD COLUMN status ENUM('active','inactive','probation','suspended','resigned') DEFAULT 'active',
    ADD COLUMN is_trainer BOOLEAN DEFAULT FALSE,
    ADD COLUMN cantrainmentors BOOLEAN DEFAULT FALSE,
    ADD COLUMN sanad_chain TEXT NULL,
    ADD COLUMN maxstudentscapacity TINYINT UNSIGNED DEFAULT 40,
    ADD COLUMN preferred_programs JSON NULL,
    ADD COLUMN emergency_contact VARCHAR(100) NULL,
    ADD COLUMN bankaccountname VARCHAR(150) NULL,
    ADD COLUMN bankaccountnumber VARCHAR(50) NULL,
    ADD COLUMN bank_name VARCHAR(100) NULL,
    ADD COLUMN tax_number VARCHAR(50) NULL,
    ADD COLUMN notes TEXT NULL,
    
    FOREIGN KEY (applicationid) REFERENCES mentorapplications(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idxjoindate (join_date);

🔌 Integrasi dengan Service Existing v8.2

📋 Matriks Integrasi Wajib

| Service Existing | Cara Integrasi | Output |
|------------------|----------------|--------|
| WhatsAppService | Notifikasi status lamaran, jadwal tes, hasil seleksi | Calon mentor terima WA update real-time |
| StudentAccountService | Replikasi pola untuk MentorAccountService | Auto-generate email & password aman |
| AlertService | Trigger alert 3-tier saat lamaran baru masuk / overload | Admin tahu segera via Operational Alerts |
| FinancialAuditLog | Log setiap approval, rejection, perubahan status | Audit trail rekrutmen lengkap |
| GeminiQuestionService | Auto-generate soal tes hafalan & tahsin | Tes objektif & terstandar |
| BroadcastService | Broadcast lowongan mentor ke database existing | Rekrutmen massal via WA |
| RevenueAnalyticsService | Adopsi pola hybrid rendering untuk dashboard rekrutmen | Performance optimal |
| GamificationService | Mentor dapat badge & poin saat complete training | Motivasi berkelanjutan |
| BadgeEvaluatorService | Evaluasi badge khusus mentor (Mentor Certified, dll) | Achievement system |
| StaffAnalyticsService | Integrasi data mentor baru ke dashboard HR | Single source of truth |

🆕 Service Baru yang Dibutuhkan

app/Services/
├── MentorRecruitmentService.php      # Orkestrator alur rekrutmen
├── MentorAccountService.php          # Auto-generate akun (pola StudentAccountService)
├── MentorTestService.php             # Kelola tes & integrasi Gemini
├── MentorProbationService.php        # Tracking masa percobaan
├── MentorEvaluationService.php       # Evaluasi kinerja periodik
└── MentorOnboardingService.php       # Checklist onboarding step-by-step

🎨 UI/UX yang Disesuaikan dengan Design System v8.2

📊 Dashboard Admin Rekrutmen (Mengadopsi Pola Executive Dashboard v8.2)

┌─────────────────────────────────────────────────────────────────────────────┐
│  🕌 AL-HIKMAH RECRUITMENT INTELLIGENCE DASHBOARD (v8.3)                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  [KPI Summary Cards — Hybrid Rendering]                                     │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐      │
│  │ 📥 Total     │ │ ⏳ Pending   │ │ 🎤 Interview │ │ ✅ Approved  │      │
│  │ Lamaran      │ │ Review       │ │ Minggu Ini   │ │ Bulan Ini    │      │
│  │ 127          │ │ 23           │ │ 8            │ │ 5            │      │
│  │ ▲ +12% MoM   │ │ 🔴 5 kritis  │ │              │ │ 🎯 83% rate  │      │
│  └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘      │
│                                                                             │
│  ┌─────────────────────────┐ ┌──────────────────────────────────────────┐ │
│  │ 📈 Tren Lamaran 12 Bulan│ │ 🎯 Funnel Konversi Rekrutmen             │ │
│  │ (ApexCharts Area Chart) │ │ (Submitted → Test → Interview → Approved)│ │
│  │ + YoY Comparison        │ │ 100% → 65% → 45% → 28%                   │ │
│  │ [Hybrid AJAX Loading]   │ │ [Interactive Donut Chart]                │ │
│  └─────────────────────────┘ └──────────────────────────────────────────┘ │
│                                                                             │
│  ┌───────────────────────────────────────────────────────────────────────┐ │
│  │ 📋 Lamaran Terbaru — DataTables (Server-Side Processing)             │ │
│  │ [Search] [Filter Status ▾] [Filter Spesialisasi ▾] [Export ▾]        │ │
│  │ ──────────────────────────────────────────────────────────────────── │ │
│  │ No │ Nama          │ Spesialisasi │ Status        │ Skor │ Aksi      │ │
│  │ 1  │ Ahmad Fauzi   │ Tahfidz      │ 🎤 Interview  │ 85   │ [👁️][✏️] │ │
│  │ 2  │ Siti Rahmah   │ Tahsin       │ 📝 Test       │ -    │ [👁️][✏️] │ │
│  │ 3  │ M. Rizki      │ Iqra         │ ✅ Approved   │ 92   │ [👁️][📄] │ │
│  └───────────────────────────────────────────────────────────────────────┘ │
│                                                                             │
│  [🔔 Alert Banner — Integrasi AlertService]                                 │
│  🔴 5 lamaran > 7 hari belum direview                                     │
│  🟡 3 calon menunggu jadwal tes minggu ini                                  │
│  🟢 2 mentor baru selesai probation bulan ini                               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘

📝 Form Lamaran Publik (Mobile-First, Progressive)

┌─────────────────────────────────────────────────────────────────────────┐
│  📝 Bergabunglah sebagai Guru Pembimbing Al-Qur'an                     │
│  AL-HIKMAH Learning Management System                                   │
│  ─────────────────────────────────────────────────────────────────────  │
│                                                                         │
│  [Progress Bar: Step 1/5 ━━━━━━━━━━━━━━━━━━━━ 20%]                     │
│                                                                         │
│  ┌─ STEP 1: DATA PRIBADI ─────────────────────────────────────────┐   │
│  │  Nama Lengkap*    : []          │   │
│  │  Email*           : []          │   │
│  │  No. WhatsApp*    : [+62 _] []          │   │
│  │  Tanggal Lahir*   : [DD/MM/YYYY] 📅                             │   │
│  │  Jenis Kelamin*   : (•) Laki-laki  ( ) Perempuan                │   │
│  │  Domisili*        : [Kota ▾]                                     │   │
│  │  Alamat Lengkap   : []          │   │
│  │                                                                │   │
│  │  [Batal]                              [Lanjut ▶]               │   │
│  └────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  💡 Data Anda aman & terenkripsi. Lamaran diproses 3-5 hari kerja.     │
└─────────────────────────────────────────────────────────────────────────┘

5 Step Form (Progressive Disclosure):
👤 Data Pribadi
🎓 Pendidikan & Pengalaman
📄 Upload Dokumen (Drag & Drop)
📖 Kesiapan Tes (Checklist)
✅ Review & Submit

🎮 Fitur Tambahan yang Diusulkan (Tidak Ada di Onboarding.md Original)

🌟 1. Mentor Badge System (Integrasi Gamifikasi)

| Kode | Nama Badge | Kriteria | Reward |
|------|-----------|----------|--------|
| M01 | Mentor Certified | Lulus probation 3 bulan | +500 Pts |
| M02 | Senior Mentor | 1 tahun aktif + rating ≥4.5 | +1000 Pts |
| M03 | Master Trainer | Melatih 5+ mentor baru | +2000 Pts |
| M04 | Sanad Keeper | Memiliki sanad bersambung | +300 Pts |
| M05 | Tahfidz Master | Hafal 10+ Juz + mengajar tahfidz | +1500 Pts |
| M06 | Parent's Choice | Rating 5.0 selama 3 bulan berturut | +800 Pts |
| M07 | Istiqomah Award | 100 sesi tanpa absen | +1000 Pts |

🎯 2. AI-Powered Test Generation (Integrasi GeminiQuestionService)

┌─────────────────────────────────────────────────────────────────────────┐
│  🤖 AI Test Generator untuk Calon Mentor                                │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  📖 TES HAFA LAN JUZ 30                                                │
│  ┌─────────────────────────────────────────────────────────────────┐  │
│  │  Pilih Surah Target:  [Al-Mulk ▾]                               │  │
│  │  Jumlah Ayat:         [10 ayat]                                  │  │
│  │  Tingkat Kesulitan:   ( ) Mudah  (•) Sedang  ( ) Sulit          │  │
│  │  Tipe Soal:           ☑️ Sambung Ayat  ☑️ Hukum Tajwid           │  │
│  │                                                                │  │
│  │  [🤖 Generate Soal AI]  [📋 Gunakan Bank Soal Existing]         │  │
│  └─────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│  📝 TES SIMULASI MENGAJAR                                              │
│  ┌─────────────────────────────────────────────────────────────────┐  │
│  │  Skenario: Mengajar anak 8 tahun, baru mulai Iqra jilid 2       │  │
│  │  Durasi: 15 menit simulasi online                               │  │
│  │  Aspek Penilaian:                                               │  │
│  │    ☑️ Kesabaran   ☑️ Metode   ☑️ Komunikasi   ☑️ Adab            │  │
│  └─────────────────────────────────────────────────────────────────┘  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘

📊 3. Probation Tracking Dashboard (BARU)

┌─────────────────────────────────────────────────────────────────────────┐
│  🎯 PROBATION TRACKING — Mentor Baru                                    │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  Mentor: Ustadz Ahmad Fauzi  |  Mulai: 01 Aug 2026  |  Akhir: 31 Oct   │
│  Progress: [████████████░░░░░░░░] 45% (Hari ke-45/100)                 │
│                                                                         │
│  ✅ CHECKLIST ONBOARDING                                                │
│  ─────────────────────────────────────────────────────────────────────  │
│  ✅ Orientasi Lembaga & Sistem (01 Aug)                                 │
│  ✅ Training Platform LMS (02 Aug)                                      │
│  ✅ Sesi Pertama dengan Santri (05 Aug)                                 │
│  ☑️ Training Metode Tahfidz (dijadwalkan 15 Aug)                        │
│  ⬜ Mid-Probation Review (15 Sep)                                       │
│  ⬜ Final Evaluation (31 Oct)                                           │
│                                                                         │
│  📈 METRIK PERFORMA (Real-Time dari StaffAnalyticsService)              │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ │
│  │ Sesi: 24     │ │ Santri: 8    │ │ Rating: 4.7  │ │ Presensi: 96%│ │
│  │ 🟢 Optimal   │ │ 🟢 Optimal   │ │ ⭐ Excellent │ │ 🟢 Optimal   │ │
│  └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘ │
│                                                                         │
│  [📝 Input Mid-Review]  [📊 Lihat Detail]  [📞 Hubungi Mentor]         │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘

🔔 4. Integrasi dengan Operational Alerts 3-Tier

// AlertService.php — Tambahan untuk Rekrutmen
public function getRecruitmentAlerts(): array
{
    return [
        'critical' => [
            // 🔴 Lamaran > 14 hari belum direview
            'stale_applications' => Application::where('status', 'submitted')
                ->where('created_at', 'subDays(14))->count(),
            
            // 🔴 Mentor probation performanya buruk
            'failing_probation' => Mentor::where('status', 'probation')
                ->where('rating_score', 'count(),
        ],
        'warning' => [
            // 🟡 Tes dijadwalkan dalam 3 hari ke depan
            'upcomingtests' => TestSession::whereBetween('scheduledat', 
                [now(), now()->addDays(3)])->count(),
            
            // 🟡 Probation akan berakhir dalam 2 minggu
            'probation_ending' => Mentor::where('status', 'probation')
                ->whereBetween('probationenddate', 
                    [now(), now()->addDays(14)])->count(),
        ],
        'info' => [
            // 🟢 Mentor baru approved hari ini
            'newapprovalstoday' => Application::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            
            // 🟢 Mentor lulus probation
            'probationpassed' => MentorProbation::where('finalresult', 'passed')
                ->whereDate('finalevaluationdate', today())->count(),
        ]
    ];
}

📄 5. Dual-Format Report Generator untuk Rekrutmen

Mengadopsi pola dari AdminReportController existing:
Export Excel/CSV: Daftar lamaran, statistik rekrutmen, funnel conversion
Export PDF Resmi: Laporan periodik rekrutmen dengan kop surat AL-HIKMAH

📱 6. Public Status Tracker (Calon Mentor Cek Status Mandiri)

┌─────────────────────────────────────────────────────────────────────────┐
│  🔍 Cek Status Lamaran Anda                                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  Email Terdaftar: []  [🔍 Cek Status]          │
│                                                                         │
│  ───────────────── Hasil ─────────────────                              │
│  Nama: Ahmad Fauzi                                                      │
│  Status: 🎤 Menunggu Wawancara                                          │
│                                                                         │
│  Progress Timeline:                                                     │
│  ✅ Submitted (20 Aug)                                                  │
│  ✅ Document Review (22 Aug)                                            │
│  ✅ Test Completed — Skor: 88/100 (25 Aug)                              │
│  🎤 Interview Scheduled — 30 Aug 2026, 10:00 WIB                        │
│  ⬜ Final Decision                                                       │
│                                                                         │
│  📅 Jadwal Interview Anda:                                              │
│  Hari/Tgl: Sabtu, 30 Agustus 2026                                       │
│  Waktu   : 10:00 - 10:45 WIB                                            │
│  Mode    : Online via Google Meet                                        │
│  Link    : https://meet.google.com/xxxx-xxxx-xxxx                       │
│                                                                         │
│  💡 Tips: Siapkan perkenalan diri & pengalaman mengajar Anda.            │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘

🛣️ Routing yang Disesuaikan (Standar v8.2)

Public Routes
| Route | Method | Role | Deskripsi |
|-------|--------|------|-----------|
| /mentor/register | GET | Public | Form lamaran multi-step |
| /mentor/register | POST | Public | Submit lamaran |
| /mentor/application/status | GET/POST | Public | Cek status lamaran (by email) |
| /mentor/application/status/{token} | GET | Public | Detail status dengan token aman |

Admin Routes (Prefix: /admin/recruitment)
| Route | Method | Deskripsi |
|-------|--------|-----------|
| /admin/recruitment | GET | Dashboard rekrutmen (KPI + Charts) |
| /admin/recruitment/applications | GET | DataTables list lamaran |
| /admin/recruitment/applications/create | GET | Input lamaran manual (oleh admin) |
| /admin/recruitment/applications/{id} | GET | Detail lamaran + dokumen |
| /admin/recruitment/applications/{id}/edit | GET/PUT | Edit data lamaran |
| /admin/recruitment/applications/{id}/documents | GET/POST | Kelola dokumen |
| /admin/recruitment/applications/{id}/test/schedule | POST | Jadwalkan tes |
| /admin/recruitment/applications/{id}/test/score | POST | Input nilai tes |
| /admin/recruitment/applications/{id}/test/generate-ai | POST | Generate soal AI |
| /admin/recruitment/applications/{id}/interview/schedule | POST | Jadwalkan wawancara |
| /admin/recruitment/applications/{id}/interview/score | POST | Input nilai wawancara |
| /admin/recruitment/applications/{id}/approve | POST | Approve + create akun |
| /admin/recruitment/applications/{id}/reject | POST | Tolak lamaran |
| /admin/recruitment/applications/{id}/advance-stage | POST | Maju ke tahap berikutnya |
| /admin/recruitment/export-excel | GET | Export laporan rekrutmen |
| /admin/recruitment/export-pdf | GET | Export PDF resmi |

Admin Routes (Prefix: /admin/mentors)
| Route | Method | Deskripsi |
|-------|--------|-----------|
| /admin/mentors/probation | GET | Dashboard probation tracking |
| /admin/mentors/probation/{id} | GET | Detail probation mentor |
| /admin/mentors/probation/{id}/review | POST | Input review probation |
| /admin/mentors/trainings | GET | Daftar training mentor |
| /admin/mentors/trainings/create | GET/POST | Buat training baru |
| /admin/mentors/evaluations | GET | Daftar evaluasi |
| /admin/mentors/evaluations/{id} | GET/POST | Buat/lihat evaluasi |

API Routes (Prefix: /api/recruitment)
| Route | Method | Deskripsi |
|-------|--------|-----------|
| /api/recruitment/stats | GET | JSON stats untuk ApexCharts |
| /api/recruitment/funnel | GET | JSON funnel conversion |
| /api/recruitment/trend | GET | JSON tren lamaran 12 bulan |

🧪 Test Cases (Standar v8.2 — Pest PHP)

Target: 50+ test cases, 200+ assertions (mengikuti standar v8.2)

// tests/Feature/MentorRecruitment/MentorApplicationTest.php
describe('Mentor Application Flow', function () {
    
    it('can submit application via public form', function () {
        $response = $this->post('/mentor/register', [
            'full_name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'phone' => '081234567890',
            'education' => 'S1 Pendidikan Agama',
            'specialization' => 'tahfidz',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        expect(MentorApplication::where('email', 'ahmad@example.com')->exists())->toBeTrue();
    });
    
    it('sends WhatsApp notification on application submit', function () {
        WhatsApp::fake();
        
        $this->post('/mentor/register', [...]);
        
        WhatsApp::assertSent(function ($message) {
            return $message->to === '081234567890' 
                && str_contains($message->content, 'Lamaran Anda telah diterima');
        });
    });
    
    it('triggers admin alert on new application', function () {
        $this->post('/mentor/register', [...]);
        
        $alert = Alert::where('type', 'new_application')->first();
        expect($alert)->not->toBeNull();
    });
    
    it('logs application to financialauditlogs', function () {
        $this->post('/mentor/register', [...]);
        
        expect(FinancialAuditLog::where('action', 'mentorapplicationsubmitted')->exists())
            ->toBeTrue();
    });
    
    it('validates required fields', function () {
        $response = $this->post('/mentor/register', []);
        $response->assertSessionHasErrors(['full_name', 'email', 'phone']);
    });
    
    it('prevents duplicate email application', function () {
        MentorApplication::factory()->create(['email' => 'test@example.com']);
        
        $response = $this->post('/mentor/register', ['email' => 'test@example.com', ...]);
        $response->assertSessionHasErrors('email');
    });
});

describe('Admin Recruitment Dashboard', function () {
    
    it('admin can view recruitment dashboard with KPIs', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin/recruitment');
        
        $response->assertOk();
        $response->assertViewHas(['totalApplications', 'pendingReview', 'interviewScheduled']);
    });
    
    it('admin can approve application and auto-create mentor account', function () {
        $application = MentorApplication::factory()->create(['status' => 'interview_completed']);
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->post("/admin/recruitment/applications/{$application->id}/approve");
        
        $response->assertRedirect();
        
        $application->refresh();
        expect($application->status)->toBe('approved');
        expect($application->approved_by)->toBe($admin->id);
        
        // Verify mentor account created
        $mentor = Mentor::where('email', $application->email)->first();
        expect($mentor)->not->toBeNull();
        expect($mentor->application_id)->toBe($application->id);
        expect($mentor->status)->toBe('probation');
        
        // Verify user account created
        $user = User::where('email', $application->email)->first();
        expect($user)->not->toBeNull();
        expect($user->role)->toBe('mentor');
        
        // Verify credentials sent via WhatsApp
        WhatsApp::assertSent(function ($m) use ($application) {
            return str_contains($m->content, 'Kredensial Akun Mentor');
        });
        
        // Verify audit log
        expect(FinancialAuditLog::where('action', 'mentor_approved')
            ->where('user_id', $admin->id)->exists())->toBeTrue();
    });
    
    it('admin can generate AI test questions via Gemini', function () {
        Gemini::fake();
        
        $admin = User::factory()->create(['role' => 'admin']);
        $application = MentorApplication::factory()->create();
        
        $response = $this->actingAs($admin)
            ->post("/admin/recruitment/applications/{$application->id}/test/generate-ai", [
                'surah' => 'Al-Mulk',
                'ayat_count' => 10,
                'difficulty' => 'medium',
            ]);
        
        $response->assertOk();
        Gemini::assertCreated(['surah' => 'Al-Mulk', 'type' => 'mentor_test']);
    });
});

describe('Probation Tracking', function () {
    
    it('auto-creates probation record when mentor approved', function () {
        $application = MentorApplication::factory()->approved()->create();
        $admin = User::factory()->admin()->create();
        
        $this->actingAs($admin)
            ->post("/admin/recruitment/applications/{$application->id}/approve");
        
        $mentor = Mentor::where('email', $application->email)->first();
        expect(MentorProbation::where('mentor_id', $mentor->id)->exists())->toBeTrue();
        expect($mentor->probationenddate)
            ->toBe(now()->addMonths(3)->format('Y-m-d'));
    });
    
    it('extends probation if performance is insufficient', function () {
        $mentor = Mentor::factory()->probation()->create();
        $admin = User::factory()->admin()->create();
        
        $response = $this->actingAs($admin)
            ->post("/admin/mentors/probation/{$mentor->id}/review", [
                'action' => 'extend',
                'duration_months' => 2,
                'notes' => 'Perlu perbaikan di aspek komunikasi',
            ]);
        
        $mentor->refresh();
        expect($mentor->probationenddate)
            ->toBe(now()->addMonths(5)->format('Y-m-d'));
    });
});

describe('Integration with Existing Services', function () {
    
    it('new mentor appears in StaffAnalyticsService workload matrix', function () {
        $mentor = Mentor::factory()->active()->create();
        $service = app(StaffAnalyticsService::class);
        
        $workload = $service->getMentorWorkload($mentor->id);
        expect($workload['status'])->toBe('optimal');
        expect($workload['student_count'])->toBe(0);
    });
    
    it('mentor can earn badges via training completion', function () {
        $mentor = Mentor::factory()->active()->create();
        $training = MentorTraining::factory()->create([
            'mentor_id' => $mentor->id,
            'points_rewarded' => 500,
            'badge_id' => Badge::where('code', 'M01')->first()->id,
        ]);
        
        expect($mentor->badges()->where('badgeid', $training->badgeid)->exists())->toBeTrue();
    });
});

Breakdown Test Coverage Target:

| Domain | Test Cases | Assertions |
|--------|-----------|-----------|
| Public Application Form | 8 | 32 |
| Admin Recruitment Dashboard | 10 | 45 |
| Test Session Management | 6 | 24 |
| Interview Management | 5 | 20 |
| Approval & Account Creation | 8 | 35 |
| Probation Tracking | 6 | 22 |
| Training Management | 4 | 16 |
| Evaluation System | 5 | 20 |
| Integration with Existing Services | 8 | 30 |
| TOTAL | 60 | 244 |

⏰ Timeline Implementasi yang Realistis

| Fase | Hari | Deliverable | Integrasi v8.2 |
|------|------|-------------|----------------|
| Fase 1: Database & Models | 1-3 | 5 migrations + 5 models | Audit trail ready |
| Fase 2: Service Layer | 4-7 | 6 services baru | WhatsApp, Alert, Gemini, Account |
| Fase 3: Public Portal | 8-11 | Form multi-step + status tracker | DataTables, mobile-first |
| Fase 4: Admin Dashboard | 12-16 | KPI + ApexCharts + Alerts | Hybrid rendering |
| Fase 5: Test & Interview | 17-20 | AI test gen + interview mgmt | GeminiQuestionService |
| Fase 6: Onboarding & Probation | 21-24 | Probation tracker + training | Gamification, Badge |
| Fase 7: Reports & Export | 25-26 | Dual-format export | RevenueReportExport pattern |
| Fase 8: Testing | 27-30 | 60+ test cases | Pest PHP standard |
| TOTAL | 30 hari kerja | Production Ready | Full Integration |

🎯 Kesimpulan & Rekomendasi

✅ Improvement Utama yang Dilakukan

Eliminasi Duplikasi Database — Field ratingscore, specialization, totalevaluations sudah ada di v8.2
Integrasi Penuh dengan 10+ Service Existing — WhatsApp, Alert, Audit, Gemini, Gamification, dll
Penambahan Fitur Probation Tracking — Critical feature yang tidak ada di original
Mentor Badge System — Integrasi dengan gamifikasi existing
AI-Powered Test Generation — Memanfaatkan GeminiQuestionService
Operational Alerts Integration — 3-tier alert untuk rekrutmen
Dual-Format Report Export — Mengikuti pola v8.2
Public Status Tracker — Calon mentor bisa cek status mandiri
Hybrid Rendering — Performance optimal seperti dashboard existing
Test Coverage 60+ cases — Mengikuti standar 259 test v8.2

📊 Metrik Kesuksesan v8.3

| Metrik | Target |
|--------|--------|
| Waktu proses lamaran → akun mentor | < 14 hari (dari 30+ hari manual) |
| Tingkat kelulusan probation | ≥ 85% |
| Kepuasan calon mentor (NPS) | ≥ 80 |
| Test coverage | 60+ test cases, 244+ assertions |
| Integrasi service existing | 10/10 service terintegrasi |

🚀 Next Steps

Review dokumen ini dengan tim development
Setup repository branch feature/v8.3-mentor-recruitment
Kick-off meeting dengan stakeholder (Admin HR, Ustadz Senior, Tim Dev)
Mulai Fase 1 (Database & Models) — estimasi 3 hari
Daily standup untuk tracking progress

Dokumen ini telah disesuaikan 100% dengan ekosistem AL-HIKMAH LMS v8.2 dan siap untuk diimplementasikan sebagai versi v8.3. 🎯