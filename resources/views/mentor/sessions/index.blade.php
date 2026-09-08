@extends('layouts.mentor')

@section('title', 'Jadwal Sesi Belajar | Mentor')
@section('header', 'Jadwal Sesi Belajar')
@section('subheader', 'Pantau jadwal mengajar, konfirmasi kehadiran santri, dan kelola status sesi')

@section('content')
<div class="container-fluid p-0">
    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Total Sesi Bimbingan</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $sessions->count() }} Sesi</h4>
                </div>
                <div class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-calendar-range"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Sesi Terjadwal</span>
                    <h4 class="fw-bold text-warning mb-0">{{ $sessions->where('status', 'scheduled')->count() }} Sesi</h4>
                </div>
                <div class="badge bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Sesi Selesai</span>
                    <h4 class="fw-bold text-success mb-0">{{ $sessions->where('status', 'completed')->count() }} Sesi</h4>
                </div>
                <div class="badge bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Santri Konfirmasi Hadir</span>
                    <h4 class="fw-bold text-info mb-0">{{ $sessions->where('confirmation.status', 'hadir')->count() }} Santri</h4>
                </div>
                <div class="badge bg-info-subtle text-info p-3 rounded-circle fs-4">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-calendar-check-fill text-success me-2"></i>Daftar Sesi Mengajar Santri</h5>
                <p class="text-muted small mb-0">Klik tombol status untuk memperbarui progres pelaksanaan sesi belajar.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('mentor.sessions.index', ['status' => 'all']) }}" 
                   class="btn btn-sm {{ ($status ?? 'all') === 'all' ? 'btn-success fw-bold text-white' : 'btn-light border' }} rounded-pill px-3">
                   Semua
                </a>
                <a href="{{ route('mentor.sessions.index', ['status' => 'scheduled']) }}" 
                   class="btn btn-sm {{ ($status ?? '') === 'scheduled' ? 'btn-warning fw-bold text-dark' : 'btn-light border' }} rounded-pill px-3">
                   <i class="bi bi-clock me-1"></i>Terjadwal
                </a>
                <a href="{{ route('mentor.sessions.index', ['status' => 'completed']) }}" 
                   class="btn btn-sm {{ ($status ?? '') === 'completed' ? 'btn-success fw-bold text-white' : 'btn-light border' }} rounded-pill px-3">
                   <i class="bi bi-check-circle me-1"></i>Selesai
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($sessions->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Sesi Belajar</h6>
                    <p class="small text-muted mb-0">Sesi bimbingan baru akan otomatis terjadwal ketika santri mengonfirmasi pendaftaran.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0 datatable" id="tableMentorSessions" style="min-width: 900px;">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 220px;">Hari & Waktu</th>
                                <th>Santri & Program</th>
                                <th>Wali & Kontak</th>
                                <th>Metode Belajar</th>
                                <th>Konfirmasi Kehadiran</th>
                                <th>Status Sesi</th>
                                <th class="text-end pe-4 no-sort" style="width: 140px;">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                @php
                                    $student = $session->student;
                                    $activeEnrollment = $student?->enrollments?->first();
                                    $programName = $activeEnrollment?->program?->name ?? $student?->programs?->first()?->name ?? 'Program Al-Hikmah';
                                    $parentPhone = $student?->getParentPhone();
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $parentPhone ?? '');
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">
                                            {{ $session->date ? \Carbon\Carbon::parse($session->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}
                                        </div>
                                        <span class="badge bg-light text-dark border font-monospace mt-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock-fill text-warning me-1"></i>{{ date('H:i', strtotime($session->time)) }} WIB
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $student?->getDisplayName() }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;">
                                            {{ $programName }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $student?->parent_name ?? $student?->parent?->user?->name ?? 'Wali Santri' }}</div>
                                        @if($parentPhone)
                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Assalamu'alaikum%20Bapak/Ibu%20wali%20santri%20{{ urlencode($student?->getDisplayName() ?? '') }}" 
                                               target="_blank" 
                                               class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none mt-1 d-inline-block px-2 py-1"
                                               style="font-size: 0.7rem;">
                                                <i class="bi bi-whatsapp me-1"></i>{{ $parentPhone }}
                                            </a>
                                        @else
                                            <small class="text-muted d-block">-</small>
                                        @endif
                                        <div class="small text-muted text-truncate mt-1" style="max-width: 180px; font-size: 0.72rem;" title="{{ $student?->effective_address }}">
                                            @if($student?->maps_link)
                                                <a href="{{ $student->maps_link }}" target="_blank" rel="noopener noreferrer" class="text-success fw-semibold text-decoration-none" title="Buka Rute Navigasi">
                                                    <i class="bi bi-pin-map-fill text-danger me-1"></i>{{ \Illuminate\Support\Str::limit($student->effective_address, 25) }}
                                                </a>
                                            @else
                                                <i class="bi bi-geo-alt me-1"></i>{{ $student?->effective_address ?: 'Lokasi Santri' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($session->method === 'offline')
                                            <div>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                    <i class="bi bi-house-door-fill me-1"></i> Offline (Home Visit)
                                                </span>
                                            </div>
                                            <div class="mt-1">
                                                @if($student?->maps_link)
                                                    <a href="{{ $student->maps_link }}" target="_blank" rel="noopener noreferrer" class="badge bg-success text-white rounded-pill px-2 py-1 text-decoration-none shadow-xs d-inline-flex align-items-center gap-1" title="Buka Rute Navigasi Google Maps/Waze">
                                                        <i class="bi bi-pin-map-fill"></i> <span>Buka Peta</span>
                                                        <i class="bi bi-box-arrow-up-right" style="font-size: 0.6rem;"></i>
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                                        <i class="bi bi-geo-alt me-1"></i>Titik peta belum ditambahkan wali
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif($session->method === 'online')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-camera-video-fill me-1"></i> Online
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-arrow-repeat me-1"></i> Hybrid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->confirmation)
                                            @if($session->confirmation->status === 'hadir')
                                                <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Hadir
                                                </span>
                                            @elseif($session->confirmation->status === 'izin')
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1" title="{{ $session->confirmation->notes }}">
                                                    <i class="bi bi-info-circle-fill me-1"></i> Izin
                                                </span>
                                            @elseif($session->confirmation->status === 'sakit')
                                                <span class="badge bg-danger text-white rounded-pill px-3 py-1" title="{{ $session->confirmation->notes }}">
                                                    <i class="bi bi-heart-pulse-fill me-1"></i> Sakit
                                                </span>
                                            @endif
                                            @if($session->confirmation->notes)
                                                <small class="d-block text-muted fst-italic mt-1" style="font-size: 0.68rem; max-width: 150px;">
                                                    "{{ \Illuminate\Support\Str::limit($session->confirmation->notes, 30) }}"
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-secondary rounded-pill border px-3 py-1">
                                                <i class="bi bi-hourglass-split me-1"></i> Belum Konfirmasi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-check2 me-1"></i>Selesai
                                            </span>
                                        @elseif($session->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-x me-1"></i>Dibatalkan
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-clock me-1"></i>Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('mentor.sessions.update-status', $session->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm rounded-pill shadow-xs" onchange="this.form.submit()" style="font-size: 0.75rem;">
                                                <option value="scheduled" {{ $session->status === 'scheduled' ? 'selected' : '' }}>⏳ Terjadwal</option>
                                                <option value="completed" {{ $session->status === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                                <option value="cancelled" {{ $session->status === 'cancelled' ? 'selected' : '' }}>❌ Batalkan</option>
                                            </select>
                                        </form>
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
@endsection
