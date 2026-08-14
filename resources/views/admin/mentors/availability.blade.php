@extends('layouts.admin')

@section('title', 'Ketersediaan & Alokasi Mentor')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-week text-success me-2"></i>Matriks Ketersediaan & Alokasi Mentor</h3>
            <p class="text-muted small mb-0">Pantau jadwal kosong, beban mengajar santri, dan alokasikan santri baru ke pengajar yang tepat.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button type="button" class="btn btn-success-custom fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="bi bi-person-plus-fill me-1"></i> Alokasikan Santri Baru
            </button>
        </div>
    </div>

    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Alert Santri Belum Teralokasi -->
    @if($unassignedStudents->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-info-subtle mb-4">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle-fill text-info fs-3"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Terdapat {{ $unassignedStudents->count() }} Santri Belum Memiliki Mentor</h6>
                        <p class="text-secondary small mb-0">Segera alokasikan ke mentor yang masih memiliki kuota mengajar pada hari belajar yang sesuai.</p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#assignModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Alokasikan Sekarang
                </button>
            </div>
        </div>
    @endif

    <!-- Legend Indicator Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body py-2 px-4 d-flex align-items-center gap-3 flex-wrap small">
            <span class="fw-bold text-dark me-2"><i class="bi bi-palette-fill me-1 text-primary"></i>Keterangan Warna Matriks:</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                <i class="bi bi-check-circle me-1"></i>Tersedia (Kuota Ada)
            </span>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill">
                <i class="bi bi-exclamation-circle me-1"></i>Penuh (Kuota Penuh)
            </span>
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                <i class="bi bi-dash-circle me-1"></i>Libur / Tidak Mengajar
            </span>
        </div>
    </div>

    <!-- Matriks Ketersediaan Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-grid-3x3-gap-fill text-success me-2"></i>Matriks Jadwal 7 Hari Mentor</h6>
            <span class="badge bg-light text-secondary border">Total Mentor Aktif: {{ count($availabilityData) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="min-width: 900px;">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="text-start ps-4" style="width: 240px;">Nama Mentor</th>
                            @foreach($days as $day)
                                <th>{{ $dayLabels[$day] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availabilityData as $mentorId => $data)
                            @php
                                $mentor = $data['mentor'];
                                $schedule = $data['schedule'];
                            @endphp
                            <tr>
                                <td class="text-start ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $mentor->getDisplayName() }}</div>
                                    <small class="text-muted d-block">{{ $mentor->specialization ?? 'Pendamping Al-Qur\'an' }}</small>
                                </td>
                                @foreach($days as $day)
                                    @php
                                        $slot = $schedule[$day];
                                        $available = $slot['is_available'];
                                        $count = $slot['student_count'];
                                        $max = $slot['max_students'];
                                        $hasQuota = $slot['has_quota'];
                                    @endphp
                                    <td>
                                        @if(! $available)
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">
                                                <i class="bi bi-dash-circle me-1"></i>Libur
                                            </span>
                                        @elseif($count >= $max)
                                            <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill" title="Kuota Penuh">
                                                <i class="bi bi-exclamation-circle me-1"></i>Penuh ({{ $count }}/{{ $max }})
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" title="Kuota Tersedia">
                                                <i class="bi bi-check-circle me-1"></i>Tersedia ({{ $count }}/{{ $max }})
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">Belum ada data mentor aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Alokasi Santri Baru -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-person-plus me-2"></i>Alokasikan Santri ke Mentor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mentors.assign-student') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Pilih Santri -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Pilih Santri <span class="text-danger">*</span></label>
                        <select class="form-select" name="student_id" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($unassignedStudents as $st)
                                <option value="{{ $st->id }}">{{ $st->getDisplayName() }} ({{ $st->location ?? 'Online' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Mentor -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Pilih Mentor <span class="text-danger">*</span></label>
                        <select class="form-select" name="mentor_id" required>
                            <option value="">-- Pilih Mentor --</option>
                            @foreach($availabilityData as $mentorId => $data)
                                @php $m = $data['mentor']; @endphp
                                <option value="{{ $m->id }}">{{ $m->getDisplayName() }} ({{ $m->specialization ?? 'Tahfidz/Tahsin' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Hari Belajar -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Hari Belajar <span class="text-danger">*</span></label>
                        <select class="form-select" name="day" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach($days as $day)
                                <option value="{{ $day }}">{{ $dayLabels[$day] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success-custom fw-bold px-4">Simpan Alokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
