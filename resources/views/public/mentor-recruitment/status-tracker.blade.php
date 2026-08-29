@extends('layouts.landing')

@section('title', 'Cek Status Lamaran Guru - AL-HIKMAH LMS')

@section('content')
<!-- Page Header -->
<section class="page-header section-padding" style="padding-top: 130px; background: linear-gradient(170deg, var(--bg-primary) 0%, var(--primary-lighter) 100%);">
    <div class="container text-center">
        <div class="section-badge mx-auto mb-3"><i class="bi bi-search me-1"></i> Pelacak Status v8.3</div>
        <h1 class="section-title fw-bold text-primary">Pelacak Status Lamaran Guru</h1>
        <p class="section-description mx-auto text-muted">Pantau tahapan seleksi calon Guru Pembimbing Al-Qur'an secara mandiri & real-time.</p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Search Form Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('mentor.recruitment.check-status') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-secondary small">Nomor WhatsApp Terdaftar saat Melamar</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                                    <input type="text" name="phone" class="form-control form-control-lg fs-6" placeholder="Contoh: 081234567890" value="{{ old('phone', request('phone')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4 d-grid pt-md-4">
                                <button type="submit" class="btn btn-primary btn-lg fs-6 fw-semibold py-2">
                                    <i class="bi bi-search me-1"></i> Cek Status Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hasil Pencarian Status -->
            @isset($application)
                <div class="card shadow border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge bg-light text-primary mb-1">Nomor Registrasi: {{ $application->application_code }}</span>
                            <h4 class="mb-0 fw-bold">{{ $application->full_name }}</h4>
                            <small class="text-white-50">Program Target: {{ $application->specialization }} &bull; Hafalan: {{ $application->hifz_total_juz }} Juz</small>
                        </div>
                        <div class="text-md-end">
                            <span class="badge bg-white text-dark px-3 py-2 fs-6">{!! $application->status_badge !!}</span>
                            <small class="d-block text-white-50 mt-1">Daftar: {{ Carbon\Carbon::parse($application->submitted_at)->format('d M Y') }}</small>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold mb-4 text-center text-secondary">Linimasa Tahapan Rekrutmen</h5>

                        <!-- Visual 5-Stage Stepper -->
                        @php
                            $stage = $application->current_stage;
                            $status = $application->status;
                        @endphp

                        <div class="position-relative m-4">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ $status === 'rejected' ? '100%' : (($stage - 1) * 25) }}%;" 
                                    aria-valuenow="{{ ($stage - 1) * 25 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="d-flex justify-content-between position-absolute top-0 start-0 w-100 translate-middle-y">
                                <!-- Tahap 1 -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm {{ $stage >= 1 ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 38px; height: 38px;">
                                        <i class="bi {{ $stage > 1 ? 'bi-check-lg' : 'bi-file-text' }}"></i>
                                    </div>
                                    <small class="d-block fw-semibold mt-2 {{ $stage >= 1 ? 'text-dark' : 'text-muted' }}">1. Pendaftaran</small>
                                </div>

                                <!-- Tahap 2 -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm {{ $stage >= 2 ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 38px; height: 38px;">
                                        <i class="bi {{ $stage > 2 ? 'bi-check-lg' : 'bi-folder-check' }}"></i>
                                    </div>
                                    <small class="d-block fw-semibold mt-2 {{ $stage >= 2 ? 'text-dark' : 'text-muted' }}">2. Berkas</small>
                                </div>

                                <!-- Tahap 3 -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm {{ $stage >= 3 ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 38px; height: 38px;">
                                        <i class="bi {{ $stage > 3 ? 'bi-check-lg' : 'bi-pencil-square' }}"></i>
                                    </div>
                                    <small class="d-block fw-semibold mt-2 {{ $stage >= 3 ? 'text-dark' : 'text-muted' }}">3. Ujian Tes</small>
                                </div>

                                <!-- Tahap 4 -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm {{ $stage >= 4 ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 38px; height: 38px;">
                                        <i class="bi {{ $stage > 4 ? 'bi-check-lg' : 'bi-chat-dots' }}"></i>
                                    </div>
                                    <small class="d-block fw-semibold mt-2 {{ $stage >= 4 ? 'text-dark' : 'text-muted' }}">4. Wawancara</small>
                                </div>

                                <!-- Tahap 5 -->
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm {{ $status === 'approved' ? 'bg-success text-white' : ($status === 'rejected' ? 'bg-danger text-white' : 'bg-light text-muted') }}" style="width: 38px; height: 38px;">
                                        <i class="bi {{ $status === 'approved' ? 'bi-patch-check-fill' : ($status === 'rejected' ? 'bi-x-lg' : 'bi-trophy') }}"></i>
                                    </div>
                                    <small class="d-block fw-semibold mt-2 {{ in_array($status, ['approved', 'rejected']) ? 'text-dark' : 'text-muted' }}">5. Keputusan</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3">
                            @if($status === 'submitted')
                                <div class="alert alert-info border-0 rounded-3 p-3">
                                    <h6 class="fw-bold"><i class="bi bi-info-circle me-1"></i>Lamaran Telah Diterima</h6>
                                    <p class="mb-0 small">Berkas Anda telah masuk dalam antrean review tim panitia seleksi AL-HIKMAH LMS. Proses review berkas memerlukan waktu 1–3 hari kerja.</p>
                                </div>
                            @elseif($status === 'document_review')
                                <div class="alert alert-primary border-0 rounded-3 p-3">
                                    <h6 class="fw-bold"><i class="bi bi-hourglass-split me-1"></i>Tahap Review Berkas Administrasi</h6>
                                    <p class="mb-0 small">Berkas dan portofolio keilmuan Anda sedang diverifikasi. Jadwal dan butir soal ujian tes kompetensi akan segera aktif di Dashboard Portal Calon Guru Anda.</p>
                                </div>
                            @elseif($status === 'test_scheduled')
                                <div class="alert alert-warning border-0 rounded-3 p-3">
                                    <h6 class="fw-bold"><i class="bi bi-calendar-check me-1"></i>Sesi Ujian Tes Telah Dijadwalkan</h6>
                                    <p class="mb-2 small">Soal ujian kompetensi telah disiapkan oleh panitia. Silakan masuk ke Dashboard Portal Calon Guru Anda untuk mulai mengerjakan tes kompetensi.</p>
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Dashboard & Kerjakan Tes
                                    </a>
                                </div>
                            @elseif($status === 'test_completed')
                                <div class="alert alert-info border-0 rounded-3 p-3">
                                    <h6 class="fw-bold"><i class="bi bi-check2-circle me-1"></i>Ujian Kompetensi Selesai Dikerjakan</h6>
                                    <p class="mb-0 small">Nilai ujian Anda: <strong>{{ $application->final_score ?? '-' }}/100</strong>. Tim panitia seleksi sedang mengagendakan jadwal wawancara & microteaching.</p>
                                </div>
                            @elseif($status === 'interview_scheduled')
                                <div class="alert alert-warning border-0 rounded-3 p-3">
                                    <h6 class="fw-bold"><i class="bi bi-camera-video me-1"></i>Jadwal Wawancara Diterbitkan</h6>
                                    <p class="mb-0 small">Anda telah diundang untuk sesi Wawancara & Microteaching. Silakan cek detail instruksi pada Dashboard Portal Calon Guru Anda.</p>
                                </div>
                            @elseif($status === 'approved')
                                <div class="alert alert-success border-0 rounded-3 p-3 text-center">
                                    <i class="bi bi-patch-check-fill fs-2 text-success mb-2 d-block"></i>
                                    <h5 class="fw-bold">Ahlan wa Sahlan! Anda Diterima Sebagai Guru Pembimbing</h5>
                                    <p class="mb-0 small">Selamat! Anda telah resmi diterima sebagai Guru Pembimbing AL-HIKMAH LMS (Masa Percobaan 3 Bulan). Silakan login ke Dashboard untuk melengkapi jadwal dan memulai orientasi.</p>
                                    <a href="{{ route('login') }}" class="btn btn-success mt-3 px-4 fw-semibold rounded-pill">Buka Dashboard Guru</a>
                                </div>
                            @elseif($status === 'rejected')
                                <div class="alert alert-danger border-0 rounded-3 p-3">
                                    <h6 class="fw-bold"><i class="bi bi-x-circle me-1"></i>Lamaran Belum Dapat Diterima</h6>
                                    <p class="mb-0 small">Terima kasih atas partisipasi Anda. Berdasarkan hasil evaluasi, saat ini kualifikasi Anda belum memenuhi kebutuhan kuota formasi bimbingan kami.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-light p-3 text-center">
                        <small class="text-muted">Butuh bantuan terkait rekrutmen? Hubungi Customer Service AL-HIKMAH via WhatsApp di nomor resmi lembaga.</small>
                    </div>
                </div>
            @endisset
        </div>
    </div>
</div>
@endsection
