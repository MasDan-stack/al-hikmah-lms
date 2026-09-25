@extends('layouts.student')

@section('title', 'Progress 30 Juz Al-Qur\'an')
@section('header', 'Peta Capaian 30 Juz')
@section('subheader', 'Visualisasi lengkap capaian hafalan dan mutqin Anda dari Juz 1 hingga Juz 30.')

@section('content')
<!-- Ringkasan Global -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-light-subtle">
            <h3 class="fw-bold text-success mb-0">{{ $progressSummary['total_mutqin'] }}</h3>
            <small class="text-muted">Juz Mutqin</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-light-subtle">
            <h3 class="fw-bold text-primary mb-0">{{ $progressSummary['total_completed'] }}</h3>
            <small class="text-muted">Juz Khatam</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-light-subtle">
            <h3 class="fw-bold text-warning mb-0">{{ $progressSummary['total_active'] }}</h3>
            <small class="text-muted">Juz Sedang Berjalan</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-light-subtle">
            <h3 class="fw-bold text-info mb-0">{{ number_format($progressSummary['total_ayat_hafal']) }}</h3>
            <small class="text-muted">Total Ayat Terhafal</small>
        </div>
    </div>
</div>

<!-- Grid 30 Juz Cards -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill text-success me-2"></i>Daftar Lengkap 30 Juz</h6>
        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
            Rata-rata Capaian: {{ $progressSummary['overall_percentage'] }}%
        </span>
    </div>

    <div class="row g-3">
        @foreach($juzList as $juz)
            <div class="col-12 col-md-6 col-lg-4">
                @include('student.components.progress-bar-juz', ['juz' => $juz])
            </div>
        @endforeach
    </div>
</div>
@endsection
