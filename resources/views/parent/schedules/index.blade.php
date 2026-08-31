@extends('layouts.parent')

@section('title', 'Jadwal Bimbingan Anak | AL-HIKMAH')
@section('header', 'Kalender Sesi Bimbingan')
@section('subheader', 'Jadwal bimbingan Al-Qur\'an seluruh ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-week text-primary me-2"></i>Jadwal Bimbingan Anak</h4>
            <p class="text-muted small mb-0">Pantau jadwal mengajar rutin mingguan dan daftar sesi bimbingan ananda.</p>
        </div>
        <div>
            <a href="{{ route('parent.schedules.list') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-list-ul me-1"></i> Tampilan Tabel Riwayat
            </a>
        </div>
    </div>

    <!-- Section 1: Jadwal Bimbingan Rutin Mingguan -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Jadwal Bimbingan Rutin Mingguan</h5>
        @if(isset($activeEnrollments) && $activeEnrollments->isNotEmpty())
            <div class="row g-3">
                @foreach($activeEnrollments as $enr)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 bg-primary-subtle text-primary-emphasis rounded-4 p-3 shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary rounded-pill px-3 py-1">{{ $enr->program->name }}</span>
                                <span class="badge bg-white text-primary rounded-pill fw-bold">{{ $enr->status->label() }}</span>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $enr->student?->getDisplayName() }}</h6>
                            <p class="small mb-1"><strong>Guru Pembimbing:</strong> Ustadz/ah {{ $enr->mentor?->getDisplayName() ?? 'Ditentukan' }}</p>
                            <p class="small mb-1"><strong>Hari Rutin:</strong> {{ $enr->effective_days_label }}</p>
                            <p class="small mb-0"><strong>Waktu:</strong> {{ $enr->effective_time_label }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-3 text-muted bg-light rounded-3">
                <p class="small mb-0">Belum ada jadwal rutin mingguan yang aktif.</p>
            </div>
        @endif
    </div>

    <!-- Section 2: Sesi Bimbingan Mendatang -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Daftar Sesi Bimbingan Mendatang</h5>
        @if($sessions->isEmpty())
            <div class="text-center py-4 text-muted bg-light rounded-3">
                <i class="bi bi-calendar-x fs-1 opacity-50 d-block mb-2"></i>
                Belum ada jadwal sesi bimbingan mendatang yang terdaftar.
            </div>
        @else
            <div class="row g-3">
                @foreach($sessions as $ses)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-3 p-3 shadow-sm bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-primary fs-6">{{ $ses->date ? \Carbon\Carbon::parse($ses->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ date('H:i', strtotime($ses->time)) }} WIB</span>
                                    @if($ses->method === 'offline')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 border border-success-subtle">Offline</span>
                                    @elseif($ses->method === 'online')
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 border border-primary-subtle">Online</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2 border border-info-subtle">Hybrid</span>
                                    @endif
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $ses->student?->getDisplayName() }}</h6>
                                <small class="text-muted d-block mb-3">Mentor: Ustadz/ah {{ $ses->mentor?->getDisplayName() ?? 'Guru Pembimbing' }}</small>
                            </div>
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
