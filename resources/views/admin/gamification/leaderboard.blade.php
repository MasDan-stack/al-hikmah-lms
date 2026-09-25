@extends('layouts.admin')

@section('title', 'Leaderboard & Poin Gamifikasi | AL-HIKMAH')
@section('header', 'Monitoring Leaderboard Santri')
@section('subheader', 'Inspeksi peringkat santri berdasarkan perolehan poin gamifikasi dan manajemen cache')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <!-- Category Filter -->
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a href="{{ route('admin.gamification.leaderboard', ['category' => 'overall']) }}" 
                   class="nav-link rounded-pill {{ $category === 'overall' ? 'active bg-success' : 'bg-white text-muted shadow-sm' }}">
                    Overall
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.gamification.leaderboard', ['category' => 'anak']) }}" 
                   class="nav-link rounded-pill {{ $category === 'anak' ? 'active bg-primary' : 'bg-white text-muted shadow-sm' }}">
                    Santri Anak (≤12 Th)
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.gamification.leaderboard', ['category' => 'dewasa']) }}" 
                   class="nav-link rounded-pill {{ $category === 'dewasa' ? 'active bg-info text-dark' : 'bg-white text-muted shadow-sm' }}">
                    Santri Dewasa
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.gamification.leaderboard', ['category' => 'streak']) }}" 
                   class="nav-link rounded-pill {{ $category === 'streak' ? 'active bg-danger' : 'bg-white text-muted shadow-sm' }}">
                    Daily Streak
                </a>
            </li>
        </ul>

        <!-- Refresh Cache Button -->
        <form action="{{ route('admin.gamification.refresh') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-success rounded-pill px-4">
                <i class="bi bi-arrow-clockwise me-1"></i> Segarkan Cache Peringkat
            </button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Peringkat</th>
                        <th>Santri</th>
                        <th>Poin Gamifikasi</th>
                        <th>Streak Harian</th>
                        <th>Total Ayat</th>
                        <th>Juz Mutqin</th>
                        <th>Privasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $entry)
                        <tr>
                            <td>
                                @if($entry->rank === 1)
                                    <span class="badge bg-warning text-dark rounded-circle p-2 fs-6">🥇</span>
                                @elseif($entry->rank === 2)
                                    <span class="badge bg-secondary text-white rounded-circle p-2 fs-6">🥈</span>
                                @elseif($entry->rank === 3)
                                    <span class="badge bg-warning-subtle text-dark rounded-circle p-2 fs-6">🥉</span>
                                @else
                                    <span class="fw-bold text-muted fs-6">#{{ $entry->rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $entry->student_name }}</div>
                                @if(!empty($entry->privacy_leaderboard) && !empty($entry->raw_name) && $entry->raw_name !== $entry->student_name)
                                    <small class="text-muted">Nama Asli: {{ $entry->raw_name }} &bull; </small>
                                @endif
                                <small class="text-muted">ID Santri: #{{ $entry->student_id }}</small>
                            </td>
                            <td><strong class="text-warning fs-6">{{ number_format($entry->total_points) }} Pts</strong></td>
                            <td><span class="badge bg-danger-subtle text-danger rounded-pill"><i class="bi bi-fire"></i> {{ $entry->current_streak }} Hari</span></td>
                            <td>{{ number_format($entry->total_ayat) }} Ayat</td>
                            <td><span class="badge bg-success-subtle text-success rounded-pill">{{ $entry->total_juz_mutqin }} Juz</span></td>
                            <td>
                                @if(!empty($entry->privacy_leaderboard))
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill"><i class="bi bi-incognito me-1"></i> Disamarkan</span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill"><i class="bi bi-eye me-1"></i> Publik</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data pada kategori ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
