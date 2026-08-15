@extends('layouts.auth-layout')

@section('title', '403 Akses Terbatas | AL-HIKMAH')
@section('subtitle', 'Akses Halaman Terbatas')

@section('auth-content')
    <div class="text-center py-2">
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 80px; height: 80px; background: var(--primary-lighter); border: 2px solid var(--border-color);">
                <i class="bi bi-shield-lock-fill fs-1 text-danger"></i>
            </div>
        </div>

        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> 403 — Akses Terbatas (Forbidden)
        </span>

        <h4 class="fw-bold text-dark mb-2">Halaman Memerlukan Izin Akses Khusus</h4>
        
        @auth
            @php
                $user = auth()->user();
                $roleLabel = match(true) {
                    $user->isAdmin() => 'Administrator Lembaga',
                    $user->isMentor() => 'Guru / Pendamping',
                    $user->isParent() => 'Orang Tua / Wali Santri',
                    $user->isStudent() => 'Santri',
                    default => 'Pengguna Terdaftar'
                };
                $dashboardRoute = match(true) {
                    $user->isAdmin() => route('admin.dashboard'),
                    $user->isMentor() => route('mentor.dashboard'),
                    $user->isParent() => route('parent.dashboard'),
                    default => route('home')
                };
                $dashboardLabel = match(true) {
                    $user->isAdmin() => 'Dashboard Admin',
                    $user->isMentor() => 'Dashboard Guru/Pendamping',
                    $user->isParent() => 'Dashboard Orang Tua',
                    default => 'Halaman Utama'
                };
            @endphp

            <div class="alert alert-warning border-0 rounded-4 text-start small mb-4 p-3 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-1 fw-bold text-warning-emphasis">
                    <i class="bi bi-person-badge fs-5"></i>
                    <span>Informasi Akun Anda:</span>
                </div>
                <div class="text-secondary ps-4">
                    Saat ini Anda masuk sebagai <span class="badge bg-primary text-white fw-semibold">{{ $roleLabel }}</span> (<strong>{{ $user->email }}</strong>).
                    Halaman ini dikhususkan untuk hak akses peran yang berbeda.
                </div>
            </div>

            <p class="text-muted small mb-4 px-2">
                {{ $exception->getMessage() ?: 'Halaman ini tidak dapat dibuka menggunakan akun Anda saat ini. Silakan kembali ke dashboard akun Anda atau keluar jika ingin menggunakan akun lain.' }}
            </p>

            <div class="d-grid gap-2">
                <a href="{{ $dashboardRoute }}" class="btn btn-primary-custom py-2 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-speedometer2 me-2"></i> Kembali ke {{ $dashboardLabel }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger py-2 rounded-pill fw-medium w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar / Ganti Akun Lain
                    </button>
                </form>
            </div>
        @else
            <p class="text-muted small mb-4 px-2">
                {{ $exception->getMessage() ?: 'Maaf, Anda belum masuk atau tidak memiliki izin untuk membuka halaman ini. Silakan login terlebih dahulu menggunakan akun yang sesuai.' }}
            </p>

            <div class="d-grid gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary-custom py-2 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk / Login Akun
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-custom py-2 rounded-pill fw-medium">
                    <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
                </a>
            </div>
        @endauth
    </div>
@endsection
