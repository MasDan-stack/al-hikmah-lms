@extends('layouts.parent')

@section('title', 'Dashboard Utama Orang Tua')
@section('header', 'Dashboard Utama')
@section('subheader', 'Ringkasan capaian hafalan anak, jadwal bimbingan, dan status tagihan')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1️⃣ Kartu Statistik Utama -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Jumlah Anak Binaan</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalChildrenCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-3">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Sesi Bulan Ini</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $monthSessionsCount }}</h3>
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
                        <div class="text-muted small fw-semibold">Rata-rata Tajwid Anak</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $avgTajwidScore }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger-subtle text-danger fs-3">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Tagihan Pending</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $pendingPaymentsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2️⃣ Quick Action Buttons -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('parent.children.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-people me-2"></i>Lihat Semua Anak
        </a>
        <a href="{{ route('parent.payments.history') }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">
            <i class="bi bi-receipt me-2"></i>Histori Pembayaran
        </a>
        <a href="{{ route('parent.messages.create') }}" class="btn btn-outline-info rounded-pill px-4 fw-bold">
            <i class="bi bi-chat-text me-2"></i>Hubungi Mentor
        </a>
        <a href="{{ route('parent.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="bi bi-calendar-week me-2"></i>Jadwal Bimbingan
        </a>
    </div>

    <div class="row g-4">
        <!-- 3️⃣ Progres Anak Terbaru -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-check me-2 text-success"></i>Capaian Hafalan & Progres Terbaru</h5>
                    <a href="{{ route('parent.children.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Detail</a>
                </div>
                <div class="card-body p-4">
                    @if($recentProgresses->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada catatan progres bimbingan terbaru untuk anak Anda.
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentProgresses as $prog)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="fw-bold text-dark fs-6">{{ $prog->student?->user?->name ?? $prog->student?->full_name }}</div>
                                        <small class="text-muted">{{ $prog->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="small text-secondary mb-2">
                                        <i class="bi bi-book me-1"></i>{{ $prog->surah_start ?? 'Surah' }} (Juz {{ $prog->juz ?? 1 }}) | Pembimbing: {{ $prog->mentor?->user?->name ?? 'Ustaz' }}
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-success-subtle text-success">Tajwid: {{ $prog->nilai_tajwid ?? '-' }}</span>
                                        <span class="badge bg-primary-subtle text-primary">Fluent: {{ $prog->nilai_fluent ?? '-' }}</span>
                                        <span class="badge bg-info-subtle text-info">Adab: {{ $prog->nilai_adab ?? '-' }}</span>
                                    </div>
                                    @if($prog->catatan_evaluasi)
                                        <div class="bg-light p-2 rounded-3 mt-2 small text-dark">
                                            <i class="bi bi-chat-square-text me-1 text-muted"></i><em>"{{ $prog->catatan_evaluasi }}"</em>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4️⃣ Jadwal Bimbingan Mendatang (7 Hari Ke Depan) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Jadwal 7 Hari Ke Depan</h5>
                    <a href="{{ route('parent.schedules.index') }}" class="btn btn-sm btn-link text-decoration-none">Kalender Full</a>
                </div>
                <div class="card-body p-4">
                    @if($upcomingSessions->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            Tidak ada jadwal bimbingan mendatang dalam 7 hari ke depan.
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($upcomingSessions as $ses)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-primary">{{ $ses->date ? \Carbon\Carbon::parse($ses->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }} ({{ date('H:i', strtotime($ses->time)) }} WIB)</span>
                                        @if($ses->method === 'offline')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 border border-success-subtle">Offline</span>
                                        @elseif($ses->method === 'online')
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 border border-primary-subtle">Online</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info rounded-pill px-2 border border-info-subtle">Hybrid</span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold text-dark">{{ $ses->student?->user?->name ?? $ses->student?->full_name }}</div>
                                    <small class="text-muted d-block">Mentor: {{ $ses->mentor?->user?->name ?? 'Ustaz/Ustazah' }}</small>
                                    <div class="mt-2">
                                        <a href="{{ route('parent.schedules.show', $ses->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Detail & Konfirmasi Kehadiran
                                        </a>
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
