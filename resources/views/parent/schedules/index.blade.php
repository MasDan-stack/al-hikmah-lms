@extends('layouts.parent')

@section('title', 'Jadwal Bimbingan Anak')
@section('header', 'Kalender Sesi Bimbingan')
@section('subheader', 'Jadwal bimbingan Al-Qur\'an seluruh ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-week text-primary me-2"></i>Jadwal Bimbingan Anak</h4>
            <p class="text-muted small mb-0">Pantau jadwal mengajar mendatang dan riwayat bimbingan ananda.</p>
        </div>
        <div>
            <a href="{{ route('parent.schedules.list') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-list-ul me-1"></i> Tampilan Tabel List
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Daftar Sesi Bimbingan Terdekat</h5>
        @if($sessions->isEmpty())
            <div class="text-center py-4 text-muted">
                Belum ada jadwal sesi bimbingan yang terdaftar.
            </div>
        @else
            <div class="row g-3">
                @foreach($sessions as $ses)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-3 p-3 shadow-sm bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-primary fs-6">{{ $ses->date->format('d M Y') }} ({{ $ses->time }})</span>
                                <span class="badge bg-info-subtle text-info rounded-pill">{{ ucfirst($ses->method) }}</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $ses->student?->user?->name ?? $ses->student?->full_name }}</h6>
                            <small class="text-muted d-block mb-3">Mentor: {{ $ses->mentor?->user?->name ?? 'Ustaz/Ustazah' }}</small>
                            <a href="{{ route('parent.schedules.show', $ses->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 w-100">
                                Detail & Konfirmasi Kehadiran
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
