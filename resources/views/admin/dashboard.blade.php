@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard AL-HIKMAH')
@section('subheader', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Admin') . '!')

@section('content')
<!-- Row Statistik Cards -->
<div class="row g-4 mb-4">
    <!-- Card: Total Santri -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Total Santri</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $totalStudents ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: var(--primary-lighter); color: var(--primary);">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-success small">
                    <i class="bi bi-arrow-up-right me-1"></i>
                    <span>Aktif dalam pendampingan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Pendamping -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Pendamping</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $totalMentors ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: rgba(255, 193, 7, 0.15); color: #d97706;">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-muted small">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    <span>Pengajar Tajwid & Tahfidz</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Sesi Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Sesi Hari Ini</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $todaySessions ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: rgba(14, 165, 233, 0.15); color: #0284c7;">
                        <i class="bi bi-calendar-check-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-info small">
                    <i class="bi bi-clock-history me-1"></i>
                    <span>Jadwal aktif berjalan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Program Belajar -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Program Aktif</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $totalPrograms ?? 5 }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: rgba(168, 85, 247, 0.15); color: #9333ea;">
                        <i class="bi bi-journal-bookmark-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-purple small">
                    <i class="bi bi-book me-1"></i>
                    <span>Tahsin, Tahfidz, Iqra</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Activity & Quick Actions -->
<div class="row g-4">
    <!-- Aktivitas Terbaru -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Aktivitas & Progres Santri Terbaru</h5>
                        <p class="text-muted small mb-0">Catatan capaian hafalan dan nilai tajwid harian santri</p>
                    </div>
                    <a href="#" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Santri</th>
                                <th class="border-0">Kategori</th>
                                <th class="border-0">Capaian</th>
                                <th class="border-0">Status Sesi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted opacity-50"></i>
                                    Belum ada catatan aktivitas terbaru hari ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Info -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Aksi Cepat Admin</h5>

                <div class="d-grid gap-2">
                    <button class="btn btn-daftar text-white justify-content-start py-2.5 px-3 mb-2" data-bs-toggle="modal" data-bs-target="#daftarModal">
                        <i class="bi bi-person-plus-fill me-2 fs-5"></i> Tambah Santri Baru
                    </button>
                    <a href="#" class="btn btn-outline-success text-start py-2.5 px-3 rounded-pill mb-2">
                        <i class="bi bi-calendar-plus me-2 fs-5"></i> Buat Jadwal Sesi Belajar
                    </a>
                    <a href="#" class="btn btn-outline-secondary text-start py-2.5 px-3 rounded-pill">
                        <i class="bi bi-file-earmark-pdf me-2 fs-5"></i> Cetak Laporan Bulanan
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