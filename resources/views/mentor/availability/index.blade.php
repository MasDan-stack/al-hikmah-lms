@extends('layouts.mentor')

@section('title', 'Pengaturan Ketersediaan Mengajar')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history text-success me-2"></i>Pengaturan Ketersediaan Mengajar</h3>
        <p class="text-muted small mb-0">Tentukan hari dan jam mengajar Anda agar Admin dapat mengalokasikan santri sesuai jadwal kosong Anda.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('mentor.availability.update-bulk') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-success"></i>Jadwal Ketersediaan 7 Hari (Senin - Minggu)</h6>
                <span class="badge bg-light text-secondary border">Batas Kuota Default: {{ $mentor?->default_max_students_per_day ?? 5 }} Murid / Hari</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width: 160px;">Hari</th>
                                <th>Status Ketersediaan</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th style="width: 160px;">Max Murid / Hari</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daysOrder as $index => $day)
                                @php
                                    $avail = $availabilities->get($day);
                                    $isAvail = $avail ? (bool)$avail->is_available : true;
                                    $isHol = $avail ? (bool)$avail->is_holiday : false;
                                    $activeStatus = $isAvail && !$isHol;
                                    $startTime = $avail?->start_time ? substr($avail->start_time, 0, 5) : '08:00';
                                    $endTime = $avail?->end_time ? substr($avail->end_time, 0, 5) : '16:00';
                                    $maxStudents = $avail?->max_students ?? $mentor?->default_max_students_per_day ?? 5;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        {{ $days[$day] }}
                                        <input type="hidden" name="days[{{ $index }}][day]" value="{{ $day }}">
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <!-- Hidden input to guarantee value=0 is sent if unchecked -->
                                            <input type="hidden" name="days[{{ $index }}][is_available]" value="0">
                                            <input class="form-check-input availability-toggle" type="checkbox" role="switch" id="avail_{{ $day }}" name="days[{{ $index }}][is_available]" value="1" {{ $activeStatus ? 'checked' : '' }} data-label-id="label_{{ $day }}">
                                            <label class="form-check-label small fw-semibold" id="label_{{ $day }}" for="avail_{{ $day }}">
                                                @if($activeStatus)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                        <i class="bi bi-check-circle me-1"></i>Tersedia Mengajar
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                                                        <i class="bi bi-dash-circle me-1"></i>Libur / Tidak Mengajar
                                                    </span>
                                                @endif
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" name="days[{{ $index }}][start_time]" value="{{ $startTime }}" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" name="days[{{ $index }}][end_time]" value="{{ $endTime }}" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" name="days[{{ $index }}][max_students]" value="{{ $maxStudents }}" min="1" max="20" style="width: 100px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3 text-end">
                <button type="submit" class="btn btn-success-custom fw-bold px-4 shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Semua Jadwal
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.availability-toggle').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                const labelId = this.getAttribute('data-label-id');
                const labelEl = document.getElementById(labelId);
                if (labelEl) {
                    if (this.checked) {
                        labelEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i>Tersedia Mengajar</span>';
                    } else {
                        labelEl.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1"><i class="bi bi-dash-circle me-1"></i>Libur / Tidak Mengajar</span>';
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
