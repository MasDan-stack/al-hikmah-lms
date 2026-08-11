@extends('layouts.auth-layout')

@section('title', 'AL-HIKMAH | Dashboard Panel')
@section('subtitle', 'Selamat datang di Panel LMS AL-HIKMAH')

@section('auth-content')
<div class="text-center py-3">
    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center p-3 mb-3 text-success">
        <i class="bi bi-person-heart fs-1"></i>
    </div>
    <h4 class="fw-bold mb-2">Ahlan wa Sahlan, {{ auth()->user()->name }}!</h4>
    <p class="text-muted small mb-4">
        Anda masuk sebagai <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-2 py-1">{{ auth()->user()->role->label ?? 'Anggota' }}</span>.
    </p>

    <div class="card border-0 shadow-sm text-start mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
        <div class="card-body p-3">
            <h6 class="fw-bold text-success mb-2"><i class="bi bi-journal-check me-2"></i>Status & Activity Panel</h6>
            <p class="small text-muted mb-0">
                Jadwal pendampingan, jurnal capaian hafalan, dan catatan pembimbingan Al-Qur'an Anda tercatat secara aman dalam sistem AL-HIKMAH LMS.
            </p>
        </div>
    </div>

    <div class="d-grid gap-2">
        <a href="{{ route('report.download') }}" target="_blank" class="btn btn-outline-success rounded-pill justify-content-center py-2">
            <i class="bi bi-file-earmark-pdf me-2"></i> Cetak Laporan Perkembangan (PDF)
        </a>
        <a href="{{ route('home') }}" class="btn btn-daftar justify-content-center">
            <i class="bi bi-house me-2"></i> Kembali ke Beranda Utama
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill mt-1">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </button>
        </form>
    </div>
</div>
@endsection
