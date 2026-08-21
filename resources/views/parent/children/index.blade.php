@extends('layouts.parent')

@section('title', 'Daftar Anak Saya')
@section('header', 'Daftar Anak Binaan')
@section('subheader', 'Daftar ananda yang terdaftar di AL-HIKMAH LMS')

@section('content')
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Anak Binaan Saya</h4>
            <p class="text-muted small mb-0">Kelola data anak dan pantau progres pembelajaran Al-Qur'an mereka secara detail.</p>
        </div>
        <a href="{{ route('parent.profile.children') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Anak Baru
        </a>
    </div>

    @if($children->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="bi bi-person-x fs-1 text-secondary mb-3"></i>
            <h5 class="fw-bold text-dark">Belum Ada Anak Terdaftar</h5>
            <p class="text-muted small">Anda belum memiliki data anak terhubung di akun ini.</p>
            <div>
                <a href="{{ route('parent.profile.children') }}" class="btn btn-primary rounded-pill px-4">
                    Tambahkan Data Anak
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($children as $child)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3 position-relative">
                        <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                            <div class="rounded-circle p-3 bg-primary-subtle text-primary fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $child->user?->name ?? $child->full_name }}</h5>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Santri Aktif</span>
                            </div>
                        </div>

                        <div class="small text-secondary mb-3">
                            <div class="mb-1"><i class="bi bi-calendar-event me-2"></i>Usia: {{ $child->age }} Tahun</div>
                            <div class="mb-1"><i class="bi bi-gender-ambiguous me-2"></i>Jenis Kelamin: {{ $child->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                            <div class="mb-1"><i class="bi bi-geo-alt me-2"></i>Lokasi: {{ $child->location ?? 'Online / Home' }}</div>
                            <div>
                                <i class="bi bi-person-workspace me-2"></i>Mentor: 
                                @if($child->getActiveMentor())
                                    <strong class="text-dark">Ustadz/ah {{ $child->getActiveMentor()->getDisplayName() }}</strong>
                                @else
                                    <span class="text-muted">Belum ditentukan</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-auto d-grid gap-2">
                            <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-primary rounded-pill fw-bold">
                                <i class="bi bi-graph-up-arrow me-1"></i> Lihat Progres & Grafik
                            </a>
                            <a href="{{ route('parent.children.report', $child->id) }}" class="btn btn-outline-dark rounded-pill fw-bold" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Laporan PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
