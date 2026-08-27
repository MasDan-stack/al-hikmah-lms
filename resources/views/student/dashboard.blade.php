@extends('layouts.student')

@section('title', 'Dashboard Santri')
@section('header', 'Assalamu\'alaikum, ' . ($student->getDisplayName()) . '!')
@section('subheader', 'Mari perkuat hafalan dan raih lencana kemuliaan Al-Qur\'an hari ini.')

@section('content')
<!-- Row 1: Header Stats & Countdown Widget -->
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Poin & Streak Card -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-star-fill fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($student->total_points ?: 0) }}</h3>
                    <small class="text-muted">Total Poin Gamifikasi</small>
                </div>
            </div>
            <div class="pt-3 border-top d-flex justify-content-between text-muted small">
                <span><i class="bi bi-fire text-danger"></i> Streak: <strong>{{ $student->current_streak ?: 0 }} Hari</strong></span>
                <span><i class="bi bi-award-fill text-primary"></i> Badges: <strong>{{ $earnedBadges->count() }}/{{ $totalBadgesCount }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Stat 2: Progress Juz Ringkas -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-check2-circle fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-success">{{ $progressSummary['total_mutqin'] }} <span class="fs-6 text-muted">Juz</span></h3>
                    <small class="text-muted">Juz Mutqin (Lulus Ujian)</small>
                </div>
            </div>
            <div class="pt-3 border-top d-flex justify-content-between text-muted small">
                <span>Aktif: <strong>{{ $progressSummary['total_active'] }} Juz</strong></span>
                <span>Terhafal: <strong>{{ number_format($progressSummary['total_ayat_hafal']) }} Ayat</strong></span>
            </div>
        </div>
    </div>

    <!-- Stat 3: Peringkat Leaderboard -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-trophy-fill fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-info">#{{ $myRankEntry->rank ?? '-' }}</h3>
                    <small class="text-muted">Posisi Leaderboard Keseluruhan</small>
                </div>
            </div>
            <div class="pt-3 border-top d-flex justify-content-between text-muted small">
                <span>Kategori: <strong>Overall</strong></span>
                <a href="{{ route('student.leaderboard') }}" class="text-success text-decoration-none fw-semibold">Lihat Semua &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Countdown & Target Hari Ini -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        @include('student.components.countdown-widget', ['milestone' => $activeMilestone])
    </div>
    <div class="col-12 col-lg-6">
        @include('student.components.target-card', ['target' => $todayTarget])
    </div>
</div>

<!-- Row 3: Progress Per Juz & Leaderboard Highlights -->
<div class="row g-4 mb-4">
    <!-- Progress 4 Juz Teratas -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-bar-chart-steps fs-5"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Progress Hafalan Al-Qur'an</h6>
                </div>
                <a href="{{ route('student.progress.juz') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    Lihat 30 Juz &rarr;
                </a>
            </div>

            <div class="mb-3">
                @php
                    $highlightJuz = $progressSummary['juz_list']->where('ayat_hafal', '>', 0)->take(4);
                    if ($highlightJuz->isEmpty()) {
                        $highlightJuz = $progressSummary['juz_list']->take(4);
                    }
                @endphp

                @foreach($highlightJuz as $juz)
                    @include('student.components.progress-bar-juz', ['juz' => $juz])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Leaderboard Top 5 -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-trophy-fill fs-5"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Top Santri Berprestasi</h6>
                </div>
                <a href="{{ route('student.leaderboard') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3">
                    Leaderboard &rarr;
                </a>
            </div>

            <div>
                @foreach($leaderboard->take(4) as $entry)
                    @include('student.components.leaderboard-entry', ['entry' => $entry])
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Koleksi Lencana Terbaru -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-award-fill fs-5"></i>
            </div>
            <h6 class="fw-bold mb-0">Koleksi Lencana Terbaru Anda</h6>
        </div>
        <a href="{{ route('student.badges') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            Buka Lemari Lencana &rarr;
        </a>
    </div>

    <div class="row g-3">
        @forelse($earnedBadges as $badge)
            <div class="col-6 col-md-3">
                @include('student.components.badge-card', ['badge' => $badge, 'isEarned' => true, 'earnedAt' => $badge->pivot->earned_at])
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">
                <i class="bi bi-award" style="font-size: 2.5rem;"></i>
                <p class="small mt-2 mb-0">Belum ada lencana yang diraih. Lakukan setoran pertama untuk membuka badge 🌱 <strong>Penyemai Qur'an</strong>!</p>
            </div>
        @endforelse
    </div>
</div>

@include('student.components.celebration-modal')
@endsection
