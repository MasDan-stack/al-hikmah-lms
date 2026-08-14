@extends('layouts.auth-layout')

@section('title', '403 Akses Terbatas | AL-HIKMAH')
@section('subtitle', 'Akses Halaman Terbatas')

@section('auth-content')
    <div class="text-center py-2">
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--primary-lighter); border: 2px solid var(--border-color);">
                <i class="bi bi-shield-lock-fill fs-1" style="color: var(--primary);"></i>
            </div>
        </div>

        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-bold mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> 403 — Forbidden Access
        </span>

        <h4 class="fw-bold text-dark mb-2">Halaman Memerlukan Izin Akses</h4>
        
        <p class="text-muted small mb-4 px-2">
            {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk membuka halaman ini. Informasi ini hanya dapat diakses oleh Orang Tua / Wali dan Administrator yang telah masuk.' }}
        </p>

        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-primary-custom py-2 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk / Login Akun
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-custom py-2 rounded-pill fw-medium">
                <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection
