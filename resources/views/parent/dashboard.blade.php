@extends('layouts.admin')

@section('title', 'Dashboard Orang Tua')
@section('header', 'Panel Orang Tua / Wali')
@section('subheader', 'Pantau perkembangan hafalan & bimbingan Al-Qur\'an Ananda, Bpk/Ibu ' . (auth()->user()->name ?? '') . '.')

@section('content')
<!-- Row Statistik Cards -->
@livewire('dashboard-stats')

<div class="row g-4 mt-1">
    <div class="col-12 col-lg-8">
        @livewire('progress-tracker')
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Aksi & Laporan Orang Tua</h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('report.download', ['student' => auth()->user()->id]) }}" class="btn btn-daftar text-white justify-content-start py-2.5 px-3">
                        <i class="bi bi-file-earmark-pdf me-2 fs-5"></i> Download Laporan Capaian PDF
                    </a>
                </div>

                <hr class="my-4" style="border-color: var(--border-color);">

                <div class="p-3 rounded-3" style="background: var(--primary-lighter); border: 1px solid var(--border-color);">
                    <div class="d-flex items-center gap-2 mb-1">
                        <i class="bi bi-heart-fill text-danger"></i>
                        <span class="fw-bold text-success small">Dukungan Al-Qur'an Anak</span>
                    </div>
                    <p class="small text-secondary mb-0">
                        Jadwal sesi berikutnya & catatan tajwid akan terus diperbarui oleh Ustaz/Ustazah pembimbing.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
