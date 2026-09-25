@extends('layouts.mentor')

@section('title', 'Target Capaian Mandiri')

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('mentor.performance.index') }}" class="text-decoration-none">Kinerja Saya</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Target Capaian</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-primary-emphasis d-flex align-items-center gap-2">
                🎯 Target Capaian Mandiri &amp; Milestone
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createGoalModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Buat Target Baru
            </button>
            <a href="{{ route('mentor.performance.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert / Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Goals Grid -->
    <div class="row g-4">
        @forelse($goals as $goal)
            @php
                $pct = $goal->target_value > 0 ? min(100, round(($goal->current_value / $goal->target_value) * 100)) : 0;
                $statusColor = match($goal->status) {
                    'achieved' => 'bg-success',
                    'failed' => 'bg-danger',
                    default => 'bg-primary'
                };
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-card-custom position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-light text-dark border rounded-pill text-uppercase" style="font-size: 0.7rem;">
                            {{ $goal->goal_type }}
                        </span>
                        <span class="badge {{ $statusColor }} rounded-pill px-3 py-1">
                            {{ ucfirst($goal->status) }}
                        </span>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">{{ $goal->title }}</h6>
                    <p class="text-muted small mb-3">
                        Periode: <strong>{{ $goal->period }}</strong>
                    </p>

                    <div class="mt-auto">
                        <div class="d-flex align-items-center justify-content-between mb-1 small">
                            <span class="text-muted">Progres Pencapaian</span>
                            <span class="fw-bold text-primary">{{ $pct }}%</span>
                        </div>
                        <div class="progress rounded-pill mb-2" style="height: 10px;">
                            <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                            <span>Tercapai: <strong>{{ $goal->current_value }}</strong></span>
                            <span>Target: <strong>{{ $goal->target_value }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-bullseye fs-1 d-block mb-2 text-primary"></i>
                Belum ada target mandiri yang dibuat. Tetapkan target bulan ini untuk memacu kualitas bimbingan!
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Target Baru -->
<div class="modal fade" id="createGoalModal" tabindex="-1" aria-labelledby="createGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('mentor.performance.goals.store') }}" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="createGoalModalLabel">
                    <i class="bi bi-bullseye text-primary me-1"></i> Buat Target Capaian Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Judul Target <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3" placeholder="Contoh: Menjaga kehadiran sesi mengajar 100%" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Tipe Target</label>
                        <select name="goal_type" class="form-select rounded-3">
                            <option value="attendance">Kehadiran (Attendance)</option>
                            <option value="rating">Rating Wali (Rating)</option>
                            <option value="retention">Retensi Santri (Retention)</option>
                            <option value="custom">Kustom (Custom)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Periode Bulan</label>
                        <input type="month" name="period" value="{{ now()->format('Y-m') }}" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nilai Target (Angka / Persentase) <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="target_value" class="form-control rounded-3" placeholder="Contoh: 95" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Target</button>
            </div>
        </form>
    </div>
</div>
@endsection
