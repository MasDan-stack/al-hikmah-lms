# PRD: Halaman Detail Profil Mentor untuk Admin Panel
## AL-HIKMAH LMS — Feature Issue Document

> **Dokumen:** Product Requirements Document (PRD)
> **Fitur:** Admin Mentor Profile Detail Page
> **Status:** 🔴 Gap Teridentifikasi — Belum Diimplementasi
> **Prioritas:** HIGH — Blocking Operasional Pencairan Honor
> **Tanggal:** 06 September 2026
> **Author:** Tim Pengembang AL-HIKMAH LMS

---

## 1. Executive Summary

### Problem Statement

Data profil lengkap mentor — termasuk **nomor rekening bank**, **nama pemilik rekening**, **CV (Curriculum Vitae)**, dan **Sertifikat Sanad** — sudah tersimpan di database (tabel `mentors` dan `mentor_applications`) dan dapat diisi oleh mentor mandiri melalui `/mentor/profile`. Namun **admin lembaga tidak memiliki akses untuk melihat atau memverifikasi data tersebut** dari panel admin. Tidak ada route `admin.mentors.show`, tidak ada halaman detail mentor, dan halaman `/admin/staff` hanya menampilkan ringkasan agregat beban kerja — bukan detail individu.

### Proposed Solution

Membuat halaman **Admin Mentor Profile Detail** di route `/admin/staff/{id}` yang menampilkan profil lengkap setiap mentor secara read-only (dengan opsi aksi terbatas), mencakup: info pribadi, info profesional, data rekening bank, berkas dokumen (CV & Sertifikat), dan ringkasan statistik mengajar.

### Success Criteria

| KPI | Target |
|---|---|
| Admin dapat melihat detail profil mentor tanpa login sebagai mentor | 100% berhasil |
| Data rekening bank mentor tampil dalam waktu `< 500ms` | Halaman render `< 500ms` pada dataset hingga 200 mentor |
| CV & Sertifikat dapat dibuka/diunduh langsung dari panel admin | Unduhan berhasil 100% dari admin panel |
| Admin dapat melakukan verifikasi rekening (approve/flag) | Tombol aksi tersedia dan terekam di log |
| Tidak ada data sensitif (nomor rekening) yang bocor ke role selain `admin` | Middleware `role:admin` diterapkan 100% |

---

## 2. User Experience & Functionality

### User Personas

| Persona | Role | Kebutuhan Utama |
|---|---|---|
| **Koordinator Keuangan** | Admin | Melihat & memverifikasi nomor rekening guru sebelum transfer honor |
| **Koordinator Akademik** | Admin | Mengakses CV & Sanad guru untuk verifikasi kompetensi |
| **Kepala Lembaga** | Admin | Melihat overview profil lengkap seluruh tenaga pengajar |

### User Stories

#### Story 1 — Akses Profil Lengkap dari Daftar Guru
> *Sebagai Admin, saya ingin mengklik nama mentor di halaman `/admin/staff` sehingga saya dapat membuka halaman detail profil lengkap mentor tersebut.*

**Acceptance Criteria:**
- [ ] Setiap baris/kartu mentor di `/admin/staff` memiliki tombol/link **"Lihat Profil"** yang mengarah ke `/admin/staff/{id}`
- [ ] Halaman detail terbuka dalam `< 500ms`
- [ ] Halaman hanya dapat diakses oleh user dengan role `admin` (middleware `role:admin` aktif)
- [ ] Jika mentor tidak ditemukan, tampilkan halaman 404 yang informatif

#### Story 2 — Melihat Data Rekening Bank Mentor
> *Sebagai Admin/Koordinator Keuangan, saya ingin melihat nama bank, nomor rekening, dan nama pemilik rekening mentor sehingga saya dapat memproses pencairan honor dengan akurat.*

**Acceptance Criteria:**
- [ ] Tampilkan `bank_name`, `bank_account_number`, `bank_account_name` dari tabel `mentors`
- [ ] Nomor rekening ditampilkan dengan format **masked** secara default (contoh: `71234*****`) dengan tombol "Tampilkan" untuk reveal
- [ ] Jika rekening belum diisi, tampilkan badge `⚠️ Belum Dilengkapi` berwarna oranye
- [ ] Tombol **"Verifikasi Rekening"** tersedia dengan status: `Belum Diverifikasi` / `Terverifikasi` / `Perlu Klarifikasi`
- [ ] Aksi verifikasi terekam di `mentor_activity_logs`

#### Story 3 — Mengakses Berkas CV & Sertifikat Sanad
> *Sebagai Admin/Koordinator Akademik, saya ingin melihat dan mengunduh CV serta Sertifikat Sanad mentor sehingga saya dapat memverifikasi kelayakan kompetensi mengajar.*

**Acceptance Criteria:**
- [ ] Tampilkan status berkas CV: `Tersedia` (hijau) atau `Belum Ada` (abu-abu)
- [ ] Tombol **"Unduh CV"** dan **"Unduh Sertifikat"** tersedia jika berkas ada
- [ ] File dapat diunduh langsung menggunakan route yang sudah ada di sistem
- [ ] Tampilkan metadata: nama file, ukuran, tanggal upload terakhir

#### Story 4 — Melihat Statistik & Riwayat Mengajar
> *Sebagai Admin, saya ingin melihat ringkasan statistik mengajar mentor sehingga saya dapat mengevaluasi performa dan beban kerja secara cepat dari satu halaman.*

**Acceptance Criteria:**
- [ ] Tampilkan: total santri aktif, rating rata-rata, status probation/aktif, join date
- [ ] Tampilkan: spesialisasi, total juz hafalan, rantai sanad
- [ ] Link cepat ke halaman terkait: Performa (`/admin/performance/mentors/{id}`), Probation, Cuti

#### Story 5 — Navigasi Cepat (Quick Actions)
> *Sebagai Admin, saya ingin tombol aksi cepat di halaman profil mentor sehingga saya tidak perlu berpindah ke halaman lain untuk tugas rutin.*

**Acceptance Criteria:**
- [ ] Tombol **"Hubungi via WhatsApp"** (generate WA link dari nomor mentor)
- [ ] Tombol **"Lihat Performa Lengkap"** → `/admin/performance/mentors/{id}`
- [ ] Tombol **"Lihat Masa Percobaan"** (jika status = probation)
- [ ] Tombol **"Edit Profil"** (opsional, redirect ke form edit admin jika dibutuhkan)

### Non-Goals (Tidak Dibangun dalam Scope Ini)

- ❌ Admin **mengedit** profil mentor dari halaman ini (hanya read-only + verifikasi rekening)
- ❌ Fitur upload/replace dokumen oleh admin (mentor tetap manage dokumen sendiri)
- ❌ Halaman khusus untuk verifikasi CV secara formal dengan tanda tangan digital
- ❌ Integrasi dengan sistem payroll eksternal

---

## 3. Technical Specifications

### Architecture Overview

```
[Admin Browser]
      │
      ▼
GET /admin/staff/{id}
      │
      ▼
[Middleware: auth + role:admin]
      │
      ▼
[AdminStaffController@show]
      │
      ├─── Mentor::with(['user', 'students', 'mentorApplication'])->findOrFail($id)
      ├─── MentorApplication (CV & Sertifikat dokumen)
      ├─── MentorProbationTracking (jika status = probation)
      └─── MentorPerformanceSnapshot (statistik terkini)
      │
      ▼
[View: admin.staff.show]
      │
      ├─ Card: Info Pribadi & Kontak
      ├─ Card: Info Profesional & Keahlian
      ├─ Card: Berkas Dokumen (CV + Sertifikat)
      ├─ Card: Rekening Bank + Aksi Verifikasi
      └─ Card: Statistik Mengajar & Quick Actions
```

### Files to Create / Modify

#### [MODIFY] `app/Http/Controllers/Admin/AdminStaffController.php`
Tambahkan method `show(int $id)`:
```php
public function show(int $id): View
{
    $mentor = Mentor::with([
        'user',
        'students.user',
        'mentorApplication.documents',
        'probationTracking',
    ])->findOrFail($id);

    $cvDoc      = $mentor->mentorApplication?->documents
                    ->where('document_type', 'cv')->first();
    $certDoc    = $mentor->mentorApplication?->documents
                    ->where('document_type', 'certificate')->first();
    $latestSnap = MentorPerformanceSnapshot::where('mentor_id', $id)
                    ->latest('snapshot_month')->first();

    return view('admin.staff.show', compact('mentor', 'cvDoc', 'certDoc', 'latestSnap'));
}
```

#### [NEW] `resources/views/admin/staff/show.blade.php`
Halaman detail profil mentor dengan 5 section card:

| Section Card | Data Ditampilkan |
|---|---|
| 🧑 Info Pribadi | Nama, Gender, TTL, Kota, Kontak Darurat, No. HP |
| 📚 Info Profesional | Spesialisasi, Sanad, Pendidikan, Institusi, Juz Hafalan, Pengalaman |
| 📄 Berkas Dokumen | CV (status + unduh), Sertifikat Sanad (status + unduh) |
| 🏦 Rekening Bank | Nama Bank, No. Rekening (masked), Nama Pemilik, Tombol Verifikasi |
| 📊 Statistik & Aksi | Rating, Santri Aktif, Status, Join Date, Quick Action Buttons |

#### [MODIFY] `resources/views/admin/staff/index.blade.php`
Tambahkan link **"Lihat Profil"** pada setiap kartu mentor yang mengarah ke `route('admin.staff.show', $mentor->id)`.

#### [MODIFY] `routes/web.php`
Tambahkan 2 route baru di grup admin:
```php
// Admin Staff — Detail Profil Mentor
Route::get('/admin/staff/{id}', [AdminStaffController::class, 'show'])
     ->name('admin.staff.show');

// Admin Staff — Verifikasi Rekening Bank (AJAX POST)
Route::post('/admin/staff/{id}/verify-bank', [AdminStaffController::class, 'verifyBank'])
     ->name('admin.staff.verify-bank');
```

### Integration Points

| Komponen | Integrasi |
|---|---|
| **Model `Mentor`** | Relasi `mentorApplication`, `user`, `students` |
| **Model `MentorApplication`** | Relasi `documents` → ambil CV & Sertifikat |
| **Route Download Dokumen** | Reuse `route('admin.recruitment.applications.document', [$appId, $docId])` |
| **`MentorActivityLog`** | Catat aksi verifikasi rekening oleh admin |
| **Middleware `role:admin`** | Proteksi akses ke route `/admin/staff/{id}` |

### Database — Tidak Ada Migrasi Baru

Data yang dibutuhkan sudah tersedia di tabel yang ada:

| Tabel | Field yang Digunakan |
|---|---|
| `mentors` | `bank_name`, `bank_account_number`, `bank_account_name`, `emergency_contact`, semua field profil |
| `mentor_applications` | `status`, `final_score`, data interview |
| `mentor_application_documents` | `document_type`, `file_path`, `file_name`, `file_size` |
| `mentor_probation_trackings` | `attendance_rate`, `average_rating`, `probation_end_date` |
| `mentor_performance_snapshots` | Snapshot kinerja bulan terakhir |

> **✅ Zero Migration Required** — Tidak diperlukan migrasi database baru.

### Security & Privacy

| Aspek | Implementasi |
|---|---|
| **Autentikasi** | Middleware `auth` + `role:admin` pada semua route baru |
| **Nomor Rekening** | Ditampilkan masked secara default (`71234*****`), reveal via JS hanya saat diklik |
| **Download Dokumen** | Reuse controller yang sudah ada dengan validasi kepemilikan dokumen |
| **Log Audit** | Setiap aksi verifikasi rekening dicatat ke `mentor_activity_logs` dengan `admin_id` |
| **No Sensitive Data in URL** | Nomor rekening tidak pernah ditampilkan di URL/query string |

---

## 4. UI/UX Specification

### Layout Halaman

```
┌─────────────────────────────────────────────────────────────┐
│  ← Kembali ke Manajemen SDM   [WA] [Performa] [Probasi]    │
│  ─────────────────────────────────────────────────────────  │
│  [FOTO]  Ustazah Fatimah Az-Zahra                          │
│          ★ 4.85  │  12 Santri Aktif  │  🟢 Aktif           │
├───────────────────┬─────────────────────────────────────────┤
│  Info Pribadi     │  Info Profesional                       │
│  Rekening Bank 🏦 │  Berkas Dokumen 📄                      │
│                   │  Statistik Mengajar 📊                  │
└───────────────────┴─────────────────────────────────────────┘
```

### Status Badge Rekening Bank

| Kondisi | Badge |
|---|---|
| Rekening lengkap & terverifikasi | `✅ Terverifikasi` — hijau |
| Rekening diisi tapi belum diverifikasi | `🕐 Menunggu Verifikasi` — biru |
| Rekening belum diisi sama sekali | `⚠️ Belum Dilengkapi` — oranye |
| Rekening perlu klarifikasi | `❗ Perlu Klarifikasi` — merah |

---

## 5. Risks & Roadmap

### Phased Rollout

#### MVP (Sprint 1) — Halaman Read-Only
- ✅ Route `GET /admin/staff/{id}` + view `show.blade.php`
- ✅ Tampilkan semua data profil (pribadi, profesional, rekening, dokumen)
- ✅ Link "Lihat Profil" di halaman `/admin/staff`
- ✅ Download CV & Sertifikat dari admin panel

#### v1.1 (Sprint 2) — Verifikasi Rekening
- Tombol verifikasi rekening bank (Approve / Flag / Perlu Klarifikasi)
- Notifikasi WhatsApp ke mentor saat rekening berhasil diverifikasi
- Log audit verifikasi

#### v2.0 (Sprint 3) — Admin Edit Mode (Opsional)
- Form edit profil mentor oleh admin (untuk koreksi data)
- Admin dapat me-reset/update nomor rekening atas permintaan mentor
- Riwayat perubahan data profil (audit trail lengkap)

### Technical Risks

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Mentor belum punya `mentorApplication` (mentor lama) | CV/Sertifikat kosong | Gunakan `optional()` / `?->` operator + tampilkan state "Belum Ada" |
| File dokumen dihapus dari storage | Link unduh error 404 | Cek eksistensi file sebelum tampilkan tombol unduh |
| Performa query untuk mentor dengan banyak relasi | Halaman lambat | Eager load dengan `with()` + cache query 60 detik |
| Nomor rekening terekspose di HTML source | Security risk | Masked di render, reveal hanya via AJAX endpoint terpisah |

---

## 6. Verification Plan

### Automated Tests

```bash
# Buat test baru
php artisan make:test AdminMentorProfileDetailTest --pest

# Test yang harus lolos:
# ✅ admin dapat mengakses /admin/staff/{id}
# ✅ non-admin mendapat 403 redirect
# ✅ data rekening bank tampil (masked) untuk mentor yang sudah isi
# ✅ badge "Belum Dilengkapi" tampil untuk mentor yang belum isi rekening
# ✅ link download CV tersedia jika dokumen ada
# ✅ 404 dikembalikan untuk mentor ID yang tidak valid

php artisan test --filter=AdminMentorProfileDetailTest --compact
```

### Manual Verification Steps

- [ ] Login sebagai **Admin** → buka `/admin/staff` → klik "Lihat Profil" salah satu mentor
- [ ] Verifikasi semua 5 section card tampil dengan data yang benar
- [ ] Klik "Tampilkan" pada nomor rekening → nomor lengkap muncul
- [ ] Klik "Unduh CV" → file ter-download dengan benar
- [ ] Login sebagai **Mentor** → coba akses `/admin/staff/{id}` → harus redirect / 403
- [ ] Buka profil mentor yang **belum isi rekening** → badge oranye muncul

---

## 7. Open Questions

| # | Pertanyaan | Impact |
|---|---|---|
| 1 | Apakah admin juga perlu bisa **mengedit** profil mentor dari halaman ini, atau cukup read-only? | Menentukan apakah perlu form + route PUT |
| 2 | Apakah verifikasi rekening perlu notifikasi ke mentor via WhatsApp? | Menentukan integrasi WhatsApp Gateway |
| 3 | Apakah perlu kolom `bank_verified_at` & `bank_verified_by` baru di tabel `mentors`? | Menentukan apakah ada migrasi baru |
| 4 | Siapa yang berhak memverifikasi rekening — semua admin, atau hanya role koordinator keuangan? | Menentukan granularitas middleware/policy |

---

*Dokumen ini dibuat menggunakan skill `/prd` — AL-HIKMAH LMS Internal Documentation.*
