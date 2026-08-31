@extends('layouts.mentor')

@section('title', 'Profil Saya')
@section('header', 'Pengaturan Profil Mentor')
@section('subheader', 'Informasi akun dan spesialisasi mengajar Anda')

<div class="container-fluid p-0">
    <!-- Flash Alert Notification Messages -->
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-1 fw-bold" style="width: 90px; height: 90px;">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Pendamping Al-Qur'an</span>
                    </div>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
                        <input type="text" class="form-control" value="{{ $mentor->full_name ?? $user->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">Nomor Telepon / WA</label>
                        <input type="text" class="form-control" value="{{ $user->phone ?? '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">Spesialisasi Mengajar</label>
                        <input type="text" class="form-control" value="{{ $mentor->specialization ?? 'Tahfidz & Tahsin Al-Qur\'an' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">Rating Bimbingan</label>
                        <input type="text" class="form-control" value="⭐ {{ $mentor->rating ?? 5.0 }} / 5.0" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary small">Biografi / Profil Singkat</label>
                        <textarea class="form-control" rows="3" readonly>{{ $mentor->bio ?? 'Pengajar Al-Qur\'an AL-HIKMAH.' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
