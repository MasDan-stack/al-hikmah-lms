# Product Requirements Document (PRD): Admin Dashboard Enhancements (v8.2)

## 1. Executive Summary

- **Problem Statement**: Dasbor admin AL-HIKMAH LMS saat ini (v8.1) masih bersifat "summary statis" (hanya menampilkan angka total) tanpa visualisasi tren, breakdown per program, atau *insight* operasional yang *actionable*. Hal ini menghambat manajemen dalam mengambil keputusan strategis, memantau beban kerja mentor, dan mengelola arus kas (tagihan jatuh tempo).
- **Proposed Solution**: Membangun dasbor analitik lanjutan (v8.2) yang mencakup metrik pertumbuhan (Growth Analytics), visualisasi pendapatan (Revenue Dashboard), manajemen SDM (Staff/HR Dashboard), pusat peringatan operasional (Alerts Center), dan fitur ekspor laporan keuangan.
- **Success Criteria**:
  - Waktu ekstraksi laporan keuangan berkurang sebesar 80% (dari manual ke ekspor Excel/PDF otomatis).
  - *Response time* terhadap isu operasional (tagihan *overdue* atau *overload* santri pada mentor) < 24 jam berkat *Operational Alerts Center*.
  - Kejelasan distribusi beban mengajar mentor divisualisasikan 100%.

---

## 2. User Experience & Functionality

### User Personas
- **Admin Keuangan**: Memantau arus kas, tagihan, dan mengekspor laporan.
- **Admin Akademik/HR**: Memantau kehadiran guru, beban mengajar, dan alokasi santri.
- **Manajemen/Kepala Lembaga**: Membutuhkan ringkasan tren pendapatan, pertumbuhan (*growth*), dan churn rate santri.

### User Stories & Acceptance Criteria (AC)

**Story 1: Revenue & Financial Analytics**
> As a Manajemen/Admin Keuangan, I want to see an interactive revenue dashboard so that I can track financial performance and overdue payments.
- **AC**:
  - Menampilkan grafik interaktif pendapatan bulanan dan tahunan.
  - Memecah *revenue* berdasarkan program (Tahfidz, Tahsin, Bahasa Arab).
  - Menampilkan metrik *Year-over-Year* (YoY) dan *Month-over-Month* (MoM).
  - Menampilkan alert untuk tagihan *overdue* (lewat jatuh tempo).

**Story 2: Staff & HR Management**
> As an Admin Akademik, I want to view a dashboard showing mentor workloads and performance so that I can distribute students evenly.
- **AC**:
  - Menampilkan total mentor aktif dan cuti.
  - Memiliki peringatan otomatis (*Red Alert*) untuk mentor yang *overload* (>40 santri).
  - Menampilkan *Top Performing Mentor* berdasarkan kehadiran dan keterikatan santri.

**Story 3: Operational Alerts Center**
> As an Admin, I want a centralized alert system so that I don't miss critical operational issues.
- **AC**:
  - Membedakan *alerts* menjadi Kritis (🔴), Perhatian (🟡), dan Info (🟢).
  - Menampilkan notifikasi tagihan overdue >30 hari, mentor *overload*, dan pendaftaran yang belum dialokasi.

**Story 4: Export Laporan Keuangan**
> As an Admin Keuangan, I want to export revenue data to Excel/PDF so that I can prepare official accounting reports.
- **AC**:
  - Dapat memfilter laporan berdasarkan rentang tanggal.
  - File yang dihasilkan berformat `.xlsx` (menggunakan Laravel Excel) atau `.pdf`.

**Story 5: WhatsApp Mass Broadcast**
> As an Admin, I want to broadcast WhatsApp messages to specific parent groups so that I can announce holidays or events efficiently.
- **AC**:
  - Dapat memilih audiens target (Semua, Per Program, Per Mentor).
  - Menggunakan *Rate Limiter* untuk mencegah pemblokiran WhatsApp.

### Non-Goals
- Tidak termasuk penggajian (*payroll*) otomatis mentor (Masuk dalam v8.3 / *Future Enhancement*).
- Tidak mencakup sistem AI prediksi pendapatan kompleks pada fase MVP ini.

---

## 3. Technical Specifications

### Architecture Overview
- **Frontend**: Menggunakan komponen *Blade* Laravel yang dipadukan dengan **Chart.js** (atau ApexCharts) untuk grafik *real-time* dan interaktif.
- **Backend Analytics**: *Controller* analitik terpisah (misalnya `AdminRevenueController`, `AdminStaffController`) untuk mengumpulkan kueri berat, mencegah beban berlebih pada dasbor utama.
- **Queue/Jobs**: Broadcast WhatsApp dan pembuatan laporan PDF berskala besar akan diproses secara asinkron menggunakan *Laravel Queues*.

### Integration Points
- **Export Library**: Menggunakan paket `maatwebsite/excel` untuk laporan Excel/CSV.
- **WhatsApp API**: Integrasi *existing* (Fonnte/Watzap) dengan penambahan sistem *job queue/rate limiting* (maksimal 50-100 pesan/menit).

### Security & Privacy
- Semua data pendapatan dan agregasi performa mentor dikunci secara ketat dan hanya dapat diakses melalui peran `isAdmin()`.
- *Financial Audit Log* akan melacak setiap perubahan data keuangan (Siapa yang mengubah status pembayaran, kapan, dan dari IP mana).

---

## 4. Risks & Roadmap

### Phased Rollout Strategy

- **Fase 1 (Sprint 1): Foundation & Financial Visibility (Priority 1)**
  - Revenue Dashboard + Grafik Interaktif (Chart.js)
  - Operational Alerts Center
  - Export Laporan (Excel/PDF)
- **Fase 2 (Sprint 2): HR & Growth Analytics (Priority 1)**
  - Staff/HR Dashboard (Beban Mengajar Mentor)
  - Growth & Retention Analytics (Churn rate, Retention, LTV)
- **Fase 3 (Sprint 3): Communication (Priority 2)**
  - Broadcast WhatsApp Massal
  - Quick Actions Panel (Aksi cepat di UI)
- **Fase 4 (Future / Priority 3)**
  - Projected Revenue
  - Mentor Payroll Calculator
  - Financial Audit Logs

### Technical Risks
- **Kinerja *Query* Database**: Kalkulasi analitik untuk LTV (*Lifetime Value*) atau grafik 12 bulan terakhir berpotensi memberatkan database. **Mitigasi**: Menggunakan sistem *caching* (Redis/File Cache) yang di-refresh setiap jam (`remember()`) atau *Daily Aggregation Table*.
- **Pemblokiran WhatsApp (Banned)**: Siaran massal berisiko memicu proteksi spam WhatsApp. **Mitigasi**: Menyebarkan beban pengiriman menggunakan *Rate Limiter* dan memastikan variasi waktu pengiriman (`delay()` pada *Job*).

---

> **Keputusan Tertunda (Pending Decisions):**
> 1. **Library Grafik**: Kami akan menggunakan **Chart.js** untuk kecepatan implementasi dan keringanannya.
> 2. **Format Export**: Kami akan memprioritaskan format **Excel** menggunakan `maatwebsite/excel` untuk pelaporan tahap awal.