@extends('layouts.admin')

@section('title', 'Manajemen Masa Percobaan Mentor')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="bi bi-person-workspace text-success me-2"></i>Monitoring Masa Percobaan Guru (Probation)</h1>
            <p class="text-muted small mb-0">Pantau performa, orientasi 3 modul, rasio kehadiran, dan evaluasi kelulusan mentor tetap.</p>
        </div>
        <a href="{{ route('admin.recruitment.applications.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-mortarboard me-1"></i> Ke Pusat Rekrutmen
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header py-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-table me-1"></i> Tabel Guru Pembimbing Masa Uji Coba</h6>
                <small class="text-muted">Data dilengkapi sorting, pencarian real-time, dan ekspor dokumen</small>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2">{{ count($probations) }} Total Mentor Terdata</span>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0 datatable" id="tableMentorProbations" data-export="true" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Mentor</th>
                            <th>Spesialisasi</th>
                            <th>Tgl Mulai</th>
                            <th>Tgl Berakhir</th>
                            <th>Sisa Waktu</th>
                            <th>4 Modul Orientasi</th>
                            <th>Rating Wali</th>
                            <th>Presensi</th>
                            <th>Status</th>
                            <th class="text-end no-sort">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($probations as $index => $probation)
                            @php
                                $endDate = Carbon\Carbon::parse($probation->end_date);
                                $daysLeft = (int) now()->diffInDays($endDate, false);
                                $isExpiring = $daysLeft > 0 && $daysLeft <= 14;
                                $isExpired = $daysLeft <= 0;
                                $completedMods = $probation->getCompletedModulesCount();
                                $percentMods = min(100, (int) round(($completedMods / 4) * 100));
                                $mod1 = $probation->isModuleCompleted('mod1');
                                $mod2 = $probation->isModuleCompleted('mod2');
                                $mod3 = $probation->isModuleCompleted('mod3');
                                $mod4 = $probation->isModuleCompleted('mod4');
                            @endphp
                        <tr>
                            <td class="fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">
                                    @if($probation->mentor_id)
                                        <a href="{{ route('admin.staff.show', $probation->mentor_id) }}" class="text-dark text-decoration-none hover-primary" title="Lihat Detail Profil & Rekening Bank">
                                            {{ $probation->mentor?->getDisplayName() ?? 'Mentor' }} <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size: 0.68rem;"></i>
                                        </a>
                                    @else
                                        {{ $probation->mentor?->getDisplayName() ?? 'Mentor' }}
                                    @endif
                                </div>
                                <small class="text-muted">{{ $probation->mentor?->user?->email ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $probation->mentor?->specialization ?? 'Tahfidz' }}</span></td>
                            <td class="small">{{ Carbon\Carbon::parse($probation->start_date)->format('d/m/Y') }}</td>
                            <td class="small">
                                {{ $endDate->format('d/m/Y') }}
                                @if($isExpired && $probation->status === 'active')
                                    <span class="badge bg-danger ms-1">Habis Tempo</span>
                                @elseif($isExpiring && $probation->status === 'active')
                                    <span class="badge bg-warning text-dark ms-1">Tenggat Dekat</span>
                                @endif
                            </td>
                            <td>
                                @if($daysLeft > 0)
                                    <span class="fw-semibold text-primary">{{ $daysLeft }} Hari</span>
                                @else
                                    <span class="text-muted">Selesai</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @if($completedMods === 4)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i> 4/4 Tuntas
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-hourglass-split me-1"></i> {{ $completedMods }}/4 Selesai
                                        </span>
                                    @endif
                                </div>
                                <div class="progress rounded-pill mt-1" style="height: 4px; width: 85px;">
                                    <div class="progress-bar {{ $completedMods === 4 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $percentMods }}%;"></div>
                                </div>
                                <div class="d-flex gap-1 mt-1" style="font-size: 0.62rem;">
                                    <span class="badge {{ $mod1 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 1: SOP Pengajaran">M1</span>
                                    <span class="badge {{ $mod2 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 2: Tajwid & Mutaba'ah">M2</span>
                                    <span class="badge {{ $mod3 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 3: Sesi Perdana LMS">M3</span>
                                    <span class="badge {{ $mod4 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 4: Komunikasi Wali">M4</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-star-fill me-1"></i>{{ number_format($probation->average_rating ?? 5.00, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold {{ ($probation->attendance_rate ?? 100) >= 80 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($probation->attendance_rate ?? 100.0, 1) }}%
                                </span>
                            </td>
                            <td>
                                @if($probation->status === 'active')
                                    <span class="badge bg-primary px-3 py-1 rounded-pill">Aktif</span>
                                @elseif($probation->status === 'passed')
                                    <span class="badge bg-success px-3 py-1 rounded-pill">Lulus</span>
                                @elseif($probation->status === 'extended')
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Diperpanjang</span>
                                @elseif($probation->status === 'terminated')
                                    <span class="badge bg-danger px-3 py-1 rounded-pill">Diberhentikan</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ $probation->status }}</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.mentors.probation.show', $probation->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bi bi-pencil-square me-1"></i> Kelola & Evaluasi
                                </a>
                                @if($probation->mentor_id)
                                    <a href="{{ route('admin.staff.show', $probation->mentor_id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 ms-1" title="Detail Akun & Berkas Guru">
                                        <i class="bi bi-person-badge"></i> Profil
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Belum ada data guru dalam masa percobaan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
