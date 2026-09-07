@extends('layouts.admin')

@section('title', 'Detail Profil Mentor - ' . ($mentor->full_name ?? $mentor->user?->name ?? 'Guru'))

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Manajemen SDM
            </a>
            <span class="text-muted">/</span>
            <span class="fw-semibold text-secondary small">Detail Profil Guru</span>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            @php
                $phoneClean = preg_replace('/[^0-9]/', '', $mentor->user?->phone ?? $mentor->emergency_contact ?? '');
            @endphp
            @if($phoneClean)
                <a href="https://wa.me/{{ $phoneClean }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 shadow-xs">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi via WhatsApp
                </a>
            @endif

            <a href="{{ route('admin.performance.mentors.show', $mentor->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs">
                <i class="bi bi-speedometer2 me-1"></i>Lihat Performa Lengkap
            </a>

            @if($mentor->status === 'probation' && $mentor->probationTracking)
                <a href="{{ route('admin.mentors.probation.show', $mentor->probationTracking->id) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 shadow-xs fw-semibold">
                    <i class="bi bi-shield-check me-1"></i>Masa Percobaan
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 🌟 Hero Profile Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0d7a3e 0%, #12a852 50%, #095c2e 100%); color: white;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4 text-center text-md-start">
                <!-- Avatar -->
                <div class="position-relative flex-shrink-0">
                    <img src="{{ $mentor->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($mentor->full_name ?? 'Mentor').'&background=0d7a3e&color=ffffff&size=200' }}" 
                         alt="{{ $mentor->full_name }}" 
                         class="rounded-circle shadow border border-4 border-white object-fit-cover"
                         style="width: 110px; height: 110px;">
                    @if($mentor->is_active)
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle p-2" title="Aktif Bertugas"></span>
                    @endif
                </div>

                <!-- Info Utama -->
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <h2 class="fw-bold mb-0 text-white">{{ $mentor->full_name ?? $mentor->user?->name ?? 'Guru Pembimbing' }}</h2>
                        
                        @if($mentor->status === 'probation')
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                                <i class="bi bi-hourglass-split me-1"></i>Masa Percobaan (Probation)
                            </span>
                        @elseif($mentor->is_active)
                            <span class="badge bg-white text-success rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                                <i class="bi bi-check-circle-fill me-1"></i>Aktif Mengajar
                            </span>
                        @else
                            <span class="badge bg-secondary text-white rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <p class="text-white-50 mb-3 small" style="max-width: 650px;">
                        {{ $mentor->bio ?: 'Pengajar dan Pembimbing Al-Qur\'an berdedikasi di AL-HIKMAH LMS.' }}
                    </p>

                    <!-- Meta Tags -->
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1.5 border border-white border-opacity-25">
                            <i class="bi bi-bookmark-star-fill text-warning me-1"></i>Spesialisasi: {{ $mentor->specialization ?: 'Tahsin & Tahfidz' }}
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1.5 border border-white border-opacity-25">
                            <i class="bi bi-book-fill text-white me-1"></i>Hafalan: {{ $mentor->hifz_total_juz ? $mentor->hifz_total_juz . ' Juz' : '-' }}
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1.5 border border-white border-opacity-25">
                            <i class="bi bi-star-fill text-warning me-1"></i>Rating: {{ number_format($mentor->rating ?? 5.0, 2) }} / 5.0
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1.5 border border-white border-opacity-25">
                            <i class="bi bi-people-fill text-white me-1"></i>{{ $mentor->students->where('pivot.is_active', true)->count() }} Santri Binaan
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Detail 2 Kolom -->
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-lg-6">
            <!-- 1. 🧑 Info Pribadi & Kontak -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>Informasi Pribadi & Kontak
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-normal ps-0" style="width: 35%;">Nama Lengkap</th>
                                <td class="fw-semibold text-dark">{{ $mentor->full_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Jenis Kelamin</th>
                                <td class="text-dark">
                                    @if($mentor->gender === 'L' || $mentor->gender === 'male')
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1"><i class="bi bi-gender-male me-1"></i>Laki-laki (Ustadz)</span>
                                    @elseif($mentor->gender === 'P' || $mentor->gender === 'female')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1"><i class="bi bi-gender-female me-1"></i>Perempuan (Ustazah)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Tempat, Tgl Lahir</th>
                                <td class="text-dark">
                                    {{ $mentor->city ? $mentor->city . ', ' : '' }}
                                    {{ $mentor->birth_date ? \Carbon\Carbon::parse($mentor->birth_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Email Akun</th>
                                <td class="text-dark">{{ $mentor->user?->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Nomor WhatsApp</th>
                                <td class="text-dark">
                                    @if($mentor->user?->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mentor->user->phone) }}" target="_blank" class="text-success fw-semibold text-decoration-none">
                                            <i class="bi bi-whatsapp me-1"></i>{{ $mentor->user->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted small">Belum diisi</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Kontak Darurat</th>
                                <td class="text-dark">{{ $mentor->emergency_contact ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Alamat Domisili</th>
                                <td class="text-dark">{{ $mentor->address ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. 🏦 Rekening Bank & Aksi Verifikasi -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-start border-4 border-warning">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bank text-warning me-2"></i>Rekening Bank Pencairan Honor
                    </h5>
                    <!-- Badge Status Rekening -->
                    @php
                        $hasBank = !empty($mentor->bank_name) && !empty($mentor->bank_account_number);
                        // Cek status verifikasi dari log terakhir atau default
                        $bankStatus = 'unverified';
                        if (!$hasBank) {
                            $bankStatus = 'empty';
                        } elseif ($latestBankLog && str_contains($latestBankLog->description, 'Terverifikasi')) {
                            $bankStatus = 'verified';
                        } elseif ($latestBankLog && str_contains($latestBankLog->description, 'Perlu Klarifikasi')) {
                            $bankStatus = 'needs_clarification';
                        }
                    @endphp

                    <span id="bankStatusBadge">
                        @if($bankStatus === 'verified')
                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Terverifikasi
                            </span>
                        @elseif($bankStatus === 'needs_clarification')
                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-1">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i>Perlu Klarifikasi
                            </span>
                        @elseif($hasBank)
                            <span class="badge bg-info-subtle text-info border border-info rounded-pill px-3 py-1">
                                <i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning rounded-pill px-3 py-1">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>⚠️ Belum Dilengkapi
                            </span>
                        @endif
                    </span>
                </div>

                <div class="card-body px-4 pb-4 pt-2">
                    @if($hasBank)
                        <table class="table table-borderless table-sm mb-3">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-normal ps-0" style="width: 35%;">Nama Bank</th>
                                    <td class="fw-bold text-dark fs-6">{{ $mentor->bank_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal ps-0">Nomor Rekening</th>
                                    <td class="text-dark">
                                        @php
                                            $rawNum = (string) $mentor->bank_account_number;
                                            $maskedNum = strlen($rawNum) > 4 ? substr($rawNum, 0, 4) . str_repeat('*', max(3, strlen($rawNum) - 6)) . substr($rawNum, -2) : '****';
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span id="accountNumberText" class="font-monospace fw-bold text-primary fs-6">{{ $maskedNum }}</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary border-0 py-0 px-1" id="toggleAccountBtn" onclick="toggleAccountNumber('{{ $rawNum }}', '{{ $maskedNum }}')" title="Tampilkan / Sembunyikan Nomor">
                                                <i class="bi bi-eye" id="toggleAccountIcon"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal ps-0">Atas Nama (Pemilik)</th>
                                    <td class="fw-semibold text-dark">{{ $mentor->bank_account_name ?: ($mentor->full_name ?? '-') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        @if($latestBankLog)
                            <div class="alert alert-light border small text-muted p-2 rounded-3 mb-3">
                                <i class="bi bi-info-circle me-1 text-primary"></i>
                                <strong>Log Terakhir:</strong> {{ $latestBankLog->description }}
                                <div class="text-secondary" style="font-size: 0.72rem;">{{ $latestBankLog->created_at?->diffForHumans() }}</div>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#verifyBankModal">
                                <i class="bi bi-patch-check-fill me-1"></i>Verifikasi Rekening Ini
                            </button>
                        </div>
                    @else
                        <div class="alert alert-warning-subtle border-warning-subtle rounded-3 p-3 mb-0">
                            <h6 class="fw-bold text-warning-emphasis mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Data Rekening Belum Diisi</h6>
                            <p class="small text-muted mb-0">Mentor ini belum memasukkan nama bank dan nomor rekening pencairan honor pada menu profilnya.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-lg-6">
            <!-- 3. 📚 Info Profesional & Keahlian -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-mortarboard-fill text-primary me-2"></i>Informasi Profesional & Sanad
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-normal ps-0" style="width: 35%;">Spesialisasi</th>
                                <td class="fw-semibold text-dark"><span class="badge bg-primary rounded-pill px-3 py-1">{{ $mentor->specialization ?: 'Tahfidz Al-Qur\'an' }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Silsilah Sanad</th>
                                <td class="text-dark">{{ $mentor->sanad_chain ?: 'Tidak dicantumkan' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Pendidikan Terakhir</th>
                                <td class="text-dark">{{ $mentor->education ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Institusi / Kampus</th>
                                <td class="text-dark">{{ $mentor->institution ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Jumlah Hafalan</th>
                                <td class="text-dark fw-bold text-success">{{ $mentor->hifz_total_juz ? $mentor->hifz_total_juz . ' Juz' : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Pengalaman Mengajar</th>
                                <td class="text-dark">{{ $mentor->experience_years ? $mentor->experience_years . ' Tahun' : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">Bio Singkat</th>
                                <td class="text-dark small text-secondary fst-italic">{{ $mentor->bio ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. 📄 Berkas Dokumen Persyaratan (CV & Sertifikat) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-folder2-open text-primary me-2"></i>Berkas Dokumen (CV & Sertifikat)
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <!-- Dokumen CV -->
                        <div class="col-sm-6">
                            <div class="card border p-3 h-100 rounded-3 bg-light shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                                        <div>
                                            <strong class="d-block text-dark small">Curriculum Vitae (CV)</strong>
                                            @if($cvDoc)
                                                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.7rem;">Belum Ada</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($cvDoc)
                                        <span class="badge bg-white text-muted border small">{{ round($cvDoc->file_size) }} KB</span>
                                    @endif
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    @if($cvDoc && ($mentor->application_id || $mentor->mentorApplication?->id))
                                        <a href="{{ route('admin.recruitment.applications.document', [$mentor->application_id ?? $mentor->mentorApplication->id, $cvDoc->id]) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill w-100 shadow-xs">
                                            <i class="bi bi-download me-1"></i>Unduh CV
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-light border rounded-pill w-100 text-muted" disabled>
                                            <i class="bi bi-slash-circle me-1"></i>Berkas Tidak Ada
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen Sertifikat Sanad -->
                        <div class="col-sm-6">
                            <div class="card border p-3 h-100 rounded-3 bg-light shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-image-fill text-info fs-3"></i>
                                        <div>
                                            <strong class="d-block text-dark small">Sertifikat / Sanad</strong>
                                            @if($certDoc)
                                                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.7rem;">Belum Ada</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($certDoc)
                                        <span class="badge bg-white text-muted border small">{{ round($certDoc->file_size) }} KB</span>
                                    @endif
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    @if($certDoc && ($mentor->application_id || $mentor->mentorApplication?->id))
                                        <a href="{{ route('admin.recruitment.applications.document', [$mentor->application_id ?? $mentor->mentorApplication->id, $certDoc->id]) }}" target="_blank" class="btn btn-sm btn-info text-white rounded-pill w-100 shadow-xs">
                                            <i class="bi bi-download me-1"></i>Unduh Sertifikat
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-light border rounded-pill w-100 text-muted" disabled>
                                            <i class="bi bi-slash-circle me-1"></i>Berkas Tidak Ada
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. 📊 Statistik Mengajar & Quick Overview -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-graph-up-arrow text-primary me-2"></i>Statistik Mengajar & Evaluasi
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Santri Aktif</small>
                                <span class="fw-bold text-primary fs-4">{{ $mentor->students->where('pivot.is_active', true)->count() }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Rating Wali</small>
                                <span class="fw-bold text-warning fs-4">⭐ {{ number_format($mentor->rating ?? 5.0, 1) }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Presensi Sesi</small>
                                <span class="fw-bold text-success fs-4">{{ $latestSnap->attendance_rate ?? 100 }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center text-muted small">
                        <span>Tanggal Bergabung: <strong>{{ $mentor->join_date ? \Carbon\Carbon::parse($mentor->join_date)->locale('id')->isoFormat('D MMMM Y') : ($mentor->created_at ? $mentor->created_at->locale('id')->isoFormat('D MMMM Y') : '-') }}</strong></span>
                        <a href="{{ route('admin.performance.mentors.show', $mentor->id) }}" class="text-primary fw-semibold text-decoration-none">
                            Tinjau Scorecard 360 &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Rekening Bank -->
<div class="modal fade" id="verifyBankModal" tabindex="-1" aria-labelledby="verifyBankModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="verifyBankModalLabel">
                    <i class="bi bi-bank text-warning me-2"></i>Verifikasi Rekening Bank
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyBankForm" onsubmit="submitBankVerification(event)">
                @csrf
                <div class="modal-body py-3">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="small text-muted">Bank: <strong>{{ $mentor->bank_name }}</strong></div>
                        <div class="small text-muted">Nomor Rekening: <strong class="text-primary">{{ $mentor->bank_account_number }}</strong></div>
                        <div class="small text-muted">Atas Nama: <strong>{{ $mentor->bank_account_name ?: ($mentor->full_name ?? '-') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Tentukan Status Verifikasi <span class="text-danger">*</span></label>
                        <select name="status" id="bankVerifyStatus" class="form-select rounded-3" required>
                            <option value="verified" {{ $bankStatus === 'verified' ? 'selected' : '' }}>✅ Terverifikasi (Siap Pencairan Honor)</option>
                            <option value="unverified" {{ $bankStatus === 'unverified' ? 'selected' : '' }}>🕐 Menunggu Verifikasi</option>
                            <option value="needs_clarification" {{ $bankStatus === 'needs_clarification' ? 'selected' : '' }}>❗ Perlu Klarifikasi (Nomor/Nama Tidak Sesuai)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi (Opsional)</label>
                        <textarea name="notes" id="bankVerifyNotes" class="form-control rounded-3" rows="3" placeholder="Contoh: Nomor rekening telah dicek cocok dengan nama di buku tabungan."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitVerifyBank" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let isRevealed = false;

    function toggleAccountNumber(full, masked) {
        const textElem = document.getElementById('accountNumberText');
        const iconElem = document.getElementById('toggleAccountIcon');
        if (!isRevealed) {
            textElem.innerText = full;
            iconElem.className = 'bi bi-eye-slash';
            isRevealed = true;
        } else {
            textElem.innerText = masked;
            iconElem.className = 'bi bi-eye';
            isRevealed = false;
        }
    }

    function submitBankVerification(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitVerifyBank');
        const status = document.getElementById('bankVerifyStatus').value;
        const notes = document.getElementById('bankVerifyNotes').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        fetch("{{ route('admin.staff.verify-bank', $mentor->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status, notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Simpan Verifikasi';

            if (data.success) {
                // Update badge di halaman
                const badgeContainer = document.getElementById('bankStatusBadge');
                if (status === 'verified') {
                    badgeContainer.innerHTML = '<span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i>Terverifikasi</span>';
                } else if (status === 'needs_clarification') {
                    badgeContainer.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-1"><i class="bi bi-exclamation-octagon-fill me-1"></i>Perlu Klarifikasi</span>';
                } else {
                    badgeContainer.innerHTML = '<span class="badge bg-info-subtle text-info border border-info rounded-pill px-3 py-1"><i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi</span>';
                }

                // Tutup modal
                const modalElem = document.getElementById('verifyBankModal');
                const modal = bootstrap.Modal.getInstance(modalElem);
                if (modal) modal.hide();

                alert(data.message);
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem.'));
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = 'Simpan Verifikasi';
            alert('Terjadi kesalahan jaringan: ' + error.message);
        });
    }
</script>
@endpush
