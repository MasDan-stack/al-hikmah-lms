@extends('layouts.mentor')

@section('title', 'Dashboard Mentor')
@section('header', 'Dashboard Utama')
@section('subheader', 'Ringkasan jadwal mengajar dan santri binaan')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cards Summary -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Sesi Hari Ini</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $todaySessionsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Santri Binaan</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $activeStudentsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-3">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Rata-rata Tajwid</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $avgTajwid }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info fs-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Sesi Mendatang</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $upcomingSessionsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('mentor.progress.create') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-pencil-square me-2"></i>Catat Progres Hafalan
        </a>
        <a href="{{ route('mentor.students.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-people me-2"></i>Lihat Semua Santri
        </a>
        <a href="{{ route('mentor.sessions.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="bi bi-calendar-check me-2"></i>Kalender Sesi Belajar
        </a>
    </div>

    <div class="row g-4">
        <!-- Jadwal Mengajar Hari Ini -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock me-2 text-primary"></i>Jadwal Mengajar Hari Ini</h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ today()->format('d M Y') }}</span>
                </div>
                <div class="card-body p-4">
                    @if($todaySessions->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada jadwal sesi mengajar untuk hari ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Santri</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySessions as $session)
                                        <tr>
                                            <td class="fw-bold text-primary">{{ $session->time }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $session->student?->user?->name ?? $session->student?->full_name }}</div>
                                                <small class="text-muted">{{ $session->notes }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info rounded-pill px-2">{{ ucfirst($session->method) }}</span>
                                            </td>
                                            <td>
                                                @if($session->status === 'completed')
                                                    <span class="badge bg-success-subtle text-success rounded-pill">Selesai</span>
                                                @elseif($session->status === 'cancelled')
                                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Batal</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Terjadwal</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('mentor.progress.create', ['student_id' => $session->student_id]) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                                    <i class="bi bi-check2-circle"></i> Input Progres
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Santri Binaan & Progres Terakhir -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-bookmark-fill me-2 text-success"></i>Progres Terakhir Santri</h5>
                </div>
                <div class="card-body p-4">
                    @if($recentProgress->isEmpty())
                        <div class="text-center py-4 text-muted">
                            Belum ada catatan progres terbaru.
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentProgress as $prog)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="fw-bold text-dark">{{ $prog->student?->user?->name ?? 'Santri' }}</div>
                                        <small class="text-muted">{{ $prog->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="small text-secondary mb-1">
                                        <i class="bi bi-book me-1"></i>{{ $prog->surah_start ?? 'Surah' }} (Juz {{ $prog->juz ?? 1 }})
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-success-subtle text-success small">Tajwid: {{ $prog->nilai_tajwid ?? '-' }}</span>
                                        <span class="badge bg-primary-subtle text-primary small">Adab: {{ $prog->nilai_adab ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
