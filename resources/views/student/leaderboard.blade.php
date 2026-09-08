@extends('layouts.student')

@section('title', 'Papan Peringkat Santri')
@section('header', 'Papan Peringkat Santri 🏆')
@section('subheader', 'Fastabiqul Khoirot — Berlomba-lomba dalam kebaikan dan menghafal kalam-Nya.')

@section('content')
<!-- Header Category Navigation & Privacy Settings -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <!-- Filter Tabs -->
        <ul class="nav nav-pills gap-2" id="leaderboardTabs">
            <li class="nav-item">
                <a href="{{ route('student.leaderboard', ['category' => 'overall']) }}" 
                   class="nav-link rounded-pill {{ $category === 'overall' ? 'active bg-success' : 'bg-light-subtle text-muted' }}">
                    <i class="bi bi-globe me-1"></i> Semua Santri
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('student.leaderboard', ['category' => 'anak']) }}" 
                   class="nav-link rounded-pill {{ $category === 'anak' ? 'active bg-primary' : 'bg-light-subtle text-muted' }}">
                    <i class="bi bi-person-hearts me-1"></i> Kategori Anak (≤12 Th)
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('student.leaderboard', ['category' => 'dewasa']) }}" 
                   class="nav-link rounded-pill {{ $category === 'dewasa' ? 'active bg-info text-dark' : 'bg-light-subtle text-muted' }}">
                    <i class="bi bi-person-badge me-1"></i> Kategori Dewasa
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('student.leaderboard', ['category' => 'streak']) }}" 
                   class="nav-link rounded-pill {{ $category === 'streak' ? 'active bg-danger' : 'bg-light-subtle text-muted' }}">
                    <i class="bi bi-fire me-1"></i> Paling Istiqomah (Streak)
                </a>
            </li>
        </ul>

        <!-- Privacy Toggle -->
        <form action="{{ route('student.privacy.toggle') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm {{ $student->privacy_leaderboard ? 'btn-outline-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-3">
                <i class="bi {{ $student->privacy_leaderboard ? 'bi-incognito' : 'bi-eye' }} me-1"></i>
                Status: {{ $student->privacy_leaderboard ? 'Nama Anonim (Santri #' . $student->id . ')' : 'Nama Publik' }}
            </button>
        </form>
    </div>
</div>

<!-- Podium Top 3 -->
@php
    $top1 = $leaderboard->firstWhere('rank', 1);
    $top2 = $leaderboard->firstWhere('rank', 2);
    $top3 = $leaderboard->firstWhere('rank', 3);
@endphp

@if($top1 || $top2 || $top3)
<div class="row g-3 mb-4 text-center align-items-end justify-content-center">
    <!-- Rank 2 (Silver) -->
    @if($top2)
    <div class="col-4 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light-subtle position-relative">
            <span class="fs-1">🥈</span>
            <div class="fw-bold text-truncate mt-1">{{ $top2->student_name }}</div>
            <div class="text-warning fw-bold small">{{ number_format($top2->total_points) }} pts</div>
            <small class="text-muted" style="font-size: 0.7rem;">{{ $top2->total_ayat }} Ayat</small>
            <div class="badge bg-secondary-subtle text-secondary rounded-pill mt-2">Juara 2</div>
        </div>
    </div>
    @endif

    <!-- Rank 1 (Gold) -->
    @if($top1)
    <div class="col-4 col-md-4">
        <div class="card border-0 shadow-lg rounded-4 p-4 text-white position-relative" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <span class="fs-1">👑</span>
            <h5 class="fw-bold text-truncate mt-1 mb-0">{{ $top1->student_name }}</h5>
            <div class="fw-bold fs-5 text-white">{{ number_format($top1->total_points) }} pts</div>
            <small class="text-white-50" style="font-size: 0.75rem;">{{ $top1->total_ayat }} Ayat &bull; {{ $top1->total_juz_mutqin }} Juz Mutqin</small>
            <div class="badge bg-white text-dark rounded-pill mt-2 px-3 py-1 fw-bold">Juara 1 🥇</div>
        </div>
    </div>
    @endif

    <!-- Rank 3 (Bronze) -->
    @if($top3)
    <div class="col-4 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light-subtle position-relative">
            <span class="fs-1">🥉</span>
            <div class="fw-bold text-truncate mt-1">{{ $top3->student_name }}</div>
            <div class="text-warning fw-bold small">{{ number_format($top3->total_points) }} pts</div>
            <small class="text-muted" style="font-size: 0.7rem;">{{ $top3->total_ayat }} Ayat</small>
            <div class="badge bg-warning-subtle text-dark rounded-pill mt-2">Juara 3</div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Full Leaderboard Table -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-list-ol text-success me-2"></i>Daftar Peringkat Lengkap</h6>
    
    <div class="row g-2">
        @forelse($leaderboard as $entry)
            <div class="col-12">
                @include('student.components.leaderboard-entry', ['entry' => $entry])
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-trophy" style="font-size: 2.5rem;"></i>
                <p class="mt-2 mb-0">Belum ada data peringkat pada kategori ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
