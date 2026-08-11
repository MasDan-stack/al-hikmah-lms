@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard AL-HIKMAH')
@section('subheader', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Admin') . '!')

@section('content')
<!-- Row Statistik Cards (Livewire) -->
@livewire('dashboard-stats')

<!-- Section Activity & Quick Actions -->
<div class="row g-4">
    <!-- Aktivitas Terbaru (Livewire) -->
    <div class="col-12 col-lg-8">
        @livewire('progress-tracker')
    </div>

    <!-- Quick Actions & Info -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Aksi Cepat Admin</h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-daftar text-white text-start py-2.5 px-3 mb-2 rounded-pill">
                        <i class="bi bi-person-plus-fill me-2 fs-5"></i> Kelola & Tambah Santri
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-success text-start py-2.5 px-3 rounded-pill mb-2">
                        <i class="bi bi-calendar-plus me-2 fs-5"></i> Jadwal Sesi Belajar
                    </a>
                    <a href="{{ route('report.download') }}" target="_blank" class="btn btn-outline-secondary text-start py-2.5 px-3 rounded-pill">
                        <i class="bi bi-file-earmark-pdf me-2 fs-5"></i> Cetak Laporan Bulanan (PDF)
                    </a>
                </div>

                <hr class="my-4" style="border-color: var(--border-color);">

                <div class="p-3 rounded-3" style="background: var(--primary-lighter); border: 1px solid var(--border-color);">
                    <div class="d-flex items-center gap-2 mb-1">
                        <i class="bi bi-info-circle-fill text-success"></i>
                        <span class="fw-bold text-success small">Laravel Boost Info</span>
                    </div>
                    <p class="small text-secondary mb-0">
                        Sistem LMS ini menggunakan skema terstandarisasi Laravel 12 & Livewire 4.3.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection