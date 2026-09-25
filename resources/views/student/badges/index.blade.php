@extends('layouts.student')

@section('title', 'Koleksi Lencana Penghargaan')
@section('header', 'Lemari Lencana Prestasi 🎖️')
@section('subheader', 'Kumpulkan seluruh lencana istimewa hafalan Al-Qur\'an dan akhlaq mulia.')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-1">Capaian Lencana Anda</h6>
            <p class="text-muted small mb-0">Anda telah mengoleksi <strong>{{ $earnedBadges->count() }}</strong> dari <strong>{{ $allBadges->count() }}</strong> lencana yang tersedia.</p>
        </div>
        <div class="fs-4 fw-bold text-success">
            {{ $allBadges->count() > 0 ? round(($earnedBadges->count() / $allBadges->count()) * 100) : 0 }}%
        </div>
    </div>
    <div class="progress rounded-pill mt-3" style="height: 10px;">
        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $allBadges->count() > 0 ? ($earnedBadges->count() / $allBadges->count()) * 100 : 0 }}%;"></div>
    </div>
</div>

<!-- Badges per Kategori -->
@php
    $categories = [
        'milestone' => '🏁 Lencana Pencapaian Juz (Milestone)',
        'streak' => '🔥 Lencana Keistiqomahan (Streak)',
        'achievement' => '⭐ Lencana Kualitas & Ujian',
        'leaderboard' => '👑 Lencana Kejuaraan',
        'adab' => '🌸 Lencana Akhlaq & Adab',
    ];
@endphp

@foreach($categories as $catKey => $catTitle)
    @php
        $badgesInCat = $allBadges->where('category', $catKey);
    @endphp
    @if($badgesInCat->isNotEmpty())
        <div class="mb-4">
            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.85rem; letter-spacing: 0.8px;">
                {{ $catTitle }}
            </h6>
            <div class="row g-3">
                @foreach($badgesInCat as $badge)
                    @php
                        $isEarned = $earnedBadges->has($badge->id);
                        $earnedRecord = $isEarned ? $earnedBadges->get($badge->id) : null;
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('student.badges.hall-of-fame', $badge->code) }}" class="text-decoration-none text-reset">
                            @include('student.components.badge-card', [
                                'badge' => $badge,
                                'isEarned' => $isEarned,
                                'earnedAt' => $earnedRecord?->pivot?->earned_at,
                            ])
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
@endsection
