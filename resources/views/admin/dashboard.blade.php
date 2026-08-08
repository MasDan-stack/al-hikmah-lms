@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard AL-HIKMAH')
@section('subheader', 'Selamat datang, ' . auth()->user()->name . '!')

@section('content')
<div class="row g-4">
    <!-- Card: Total Murid -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-people fs-3 text-success"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Total Murid</h6>
                        <h3 class="fw-bold mb-0">{{ $totalStudents ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Pendamping -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-person-badge fs-3 text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Total Pendamping</h6>
                        <h3 class="fw-bold mb-0">{{ $totalMentors ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Sesi Hari Ini -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-calendar-event fs-3 text-warning"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0">Sesi Hari Ini</h6>
                        <h3 class="fw-bold mb-0">{{ $todaySessions ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Aktivitas Terbaru</h5>
            <a href="#" class="text-success text-decoration-none">Lihat semua</a>
        </div>
        <p class="text-muted">Belum ada aktivitas terbaru.</p>
    </div>
</div>
@endsection