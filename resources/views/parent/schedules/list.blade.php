@extends('layouts.parent')

@section('title', 'Tabel Riwayat Sesi Bimbingan')
@section('header', 'Daftar Sesi Bimbingan')
@section('subheader', 'Riwayat dan jadwal lengkap sesi belajar tatap muka & daring anak Anda')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-calendar3 text-success me-2"></i>Riwayat & Jadwal Sesi Belajar
            </h4>
            <p class="text-muted small mb-0">Cari, sortir, dan pantau seluruh sesi bimbingan anak binaan Anda secara interaktif.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('parent.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-calendar-week me-1"></i> Kalender Ringkasan
            </a>
        </div>
    </div>

    <!-- Filter Status Pills -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('parent.schedules.list', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-success text-white' : 'btn-light border text-secondary' }} rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
            <i class="bi bi-grid-fill me-1"></i> Semua Sesi ({{ $status === 'all' ? $sessions->count() : '' }})
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'scheduled']) }}" class="btn btn-sm {{ $status === 'scheduled' ? 'btn-warning text-dark fw-bold' : 'btn-light border text-secondary' }} rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
            <i class="bi bi-clock-history me-1"></i> Terjadwal
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'in_progress']) }}" class="btn btn-sm {{ $status === 'in_progress' ? 'btn-info text-white' : 'btn-light border text-secondary' }} rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
            <i class="bi bi-play-circle-fill me-1"></i> Sedang Berlangsung
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-success text-white' : 'btn-light border text-secondary' }} rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
            <i class="bi bi-check-circle-fill me-1"></i> Selesai
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status === 'cancelled' ? 'btn-danger text-white' : 'btn-light border text-secondary' }} rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
            <i class="bi bi-x-circle-fill me-1"></i> Dibatalkan
        </a>
    </div>

    <!-- Card Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-body p-4">
            @if($sessions->isEmpty())
                <div class="text-center py-5">
                    <div class="p-4 rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-calendar-x fs-1 text-secondary"></i>
                    </div>
                    <h5 class="fw-bold text-heading mb-1">Belum Ada Sesi Bimbingan</h5>
                    <p class="text-muted small mb-0">Tidak ada sesi bimbingan yang sesuai dengan filter kriteria status ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable w-100" id="tableParentSchedules">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Hari, Tanggal & Waktu</th>
                                <th>Santri Binaan</th>
                                <th>Program & Metode</th>
                                <th>Guru Pembimbing</th>
                                <th class="text-center">Status Sesi</th>
                                <th class="text-end pe-3 no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $index => $ses)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-2 rounded-3 bg-success-subtle text-success flex-shrink-0">
                                                <i class="bi bi-calendar-event fs-5"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block">
                                                    {{ $ses->date ? \Carbon\Carbon::parse($ses->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}
                                                </span>
                                                <span class="badge bg-light text-secondary border px-2 py-0.5 mt-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-clock me-1 text-primary"></i>{{ $ses->time ? date('H:i', strtotime($ses->time)) . ' WIB' : '08:00 WIB' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-6 flex-shrink-0" style="width: 36px; height: 36px;">
                                                {{ strtoupper(substr($ses->student?->full_name ?? $ses->student?->user?->name ?? 'S', 0, 2)) }}
                                            </div>
                                            <div>
                                                <span class="fw-bold text-heading d-block">{{ $ses->student?->full_name ?? $ses->student?->user?->name ?? 'Santri' }}</span>
                                                <small class="text-muted">{{ $ses->student?->age ? $ses->student->age . ' Tahun' : 'Santri' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark d-block mb-1">
                                            {{ $ses->program?->name ?? $ses->enrollment?->program?->name ?? 'Tahfidz Al-Qur\'an' }}
                                        </span>
                                        @if($ses->method === 'offline')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 border border-success-subtle small">
                                                <i class="bi bi-house-door-fill me-1"></i> Offline
                                            </span>
                                        @elseif($ses->method === 'online')
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 border border-primary-subtle small">
                                                <i class="bi bi-camera-video-fill me-1"></i> Online
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 border border-info-subtle small">
                                                <i class="bi bi-arrow-repeat me-1"></i> Hybrid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                <i class="bi bi-person-workspace"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-heading d-block">
                                                    {{ $ses->mentor?->getDisplayName() ?? $ses->mentor?->user?->name ?? 'Ustaz/ah' }}
                                                </span>
                                                <small class="text-muted">{{ $ses->mentor?->specialization ?? 'Pengajar Al-Qur\'an' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($ses->status === 'completed')
                                            <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill fw-semibold border border-success-subtle">
                                                <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                            </span>
                                        @elseif($ses->status === 'in_progress')
                                            <span class="badge bg-info-subtle text-info px-3 py-1.5 rounded-pill fw-semibold border border-info-subtle">
                                                <i class="bi bi-play-circle-fill me-1"></i> Berlangsung
                                            </span>
                                        @elseif($ses->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger px-3 py-1.5 rounded-pill fw-semibold border border-danger-subtle">
                                                <i class="bi bi-x-circle-fill me-1"></i> Dibatalkan
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1.5 rounded-pill fw-semibold border border-warning-subtle">
                                                <i class="bi bi-clock-fill me-1"></i> Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        @if($ses->status === 'completed')
                                            @if($ses->feedback)
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2.5 py-1.5 rounded-pill me-1 fw-bold" title="Rating Diberikan: {{ number_format($ses->feedback->overall_rating, 1) }}/5">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($ses->feedback->overall_rating, 1) }}
                                                </span>
                                            @else
                                                <button type="button" class="btn btn-sm btn-warning rounded-pill px-2.5 py-1 fw-bold text-dark me-1 shadow-xs" onclick="openFeedbackModal('{{ $ses->id }}', '{{ $ses->mentor_id }}', '{{ addslashes($ses->mentor?->getDisplayName() ?? 'Ustaz/ah') }}')" title="Beri Penilaian Mentor">
                                                    <i class="bi bi-star-fill me-1"></i>Beri Nilai
                                                </button>
                                            @endif
                                        @endif
                                        <a href="{{ route('parent.schedules.show', $ses->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold shadow-xs">
                                            <i class="bi bi-eye-fill me-1"></i> Detail
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

@include('parent.partials.feedback-modal')
@endsection
