@extends('layouts.admin')

@section('title', 'Kelola Permohonan Pendaftaran | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning-subtle text-warning-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Menunggu Review</span>
                        <h4 class="fw-bold mb-0">{{ $stats['waiting_admin'] }}</h4>
                    </div>
                    <i class="bi bi-hourglass-split fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info-subtle text-info-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Menunggu Respon Wali</span>
                        <h4 class="fw-bold mb-0">{{ $stats['waiting_parent'] }}</h4>
                    </div>
                    <i class="bi bi-chat-dots fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary-subtle text-primary-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Jadwal Deal (Siap Bayar)</span>
                        <h4 class="fw-bold mb-0">{{ $stats['confirmed'] }}</h4>
                    </div>
                    <i class="bi bi-check-circle fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success-subtle text-success-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Kelas Aktif</span>
                        <h4 class="fw-bold mb-0">{{ $stats['active'] }}</h4>
                    </div>
                    <i class="bi bi-award fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-journal-text me-2 text-success"></i>Antrean Permohonan & Negosiasi Jadwal</h5>
                <p class="text-muted small mb-0">Verifikasi ketersediaan mentor dan proses jadwal permohonan santri baru.</p>
            </div>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning border-0 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                </div>
            @endif

            <!-- Form Filter Search & Dates -->
            <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-2 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari santri atau program..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        @foreach(\App\Enums\EnrollmentStatus::cases() as $statusCase)
                            <option value="{{ $statusCase->value }}" {{ request('status') == $statusCase->value ? 'selected' : '' }}>
                                {{ $statusCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Dari Tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Sampai Tanggal">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary-custom flex-grow-1 rounded-pill">Filter</button>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                </div>
            </form>

            <form action="{{ route('admin.enrollments.bulk-accept') }}" method="POST" id="bulkForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>Santri & Wali</th>
                                <th>Program</th>
                                <th>Jadwal Request</th>
                                <th>Mentor Assigned</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="enrollment_ids[]" value="{{ $enrollment->id }}" class="form-check-input select-item" {{ $enrollment->isWaitingAdmin() && $enrollment->mentor_id ? '' : 'disabled' }}>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $enrollment->student->getDisplayName() }}</span>
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>Wali: {{ $enrollment->student->getParentNameAttribute() }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success d-block">{{ $enrollment->program->name }}</span>
                                        <small class="text-muted">{{ $enrollment->formatted_price }}</small>
                                    </td>
                                    <td>
                                        <span class="d-block small"><strong>Hari:</strong> {{ $enrollment->requested_days_label }}</span>
                                        <small class="text-muted">Jam: {{ $enrollment->requested_time_label }}</small>
                                    </td>
                                    <td>
                                        @if($enrollment->mentor)
                                            <span class="fw-bold text-primary small d-block"><i class="bi bi-person-badge me-1"></i>{{ $enrollment->mentor->getDisplayName() }}</span>
                                        @else
                                            <span class="text-muted small">Belum Ditunjuk</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status->badgeClass() }} px-3 py-2 rounded-pill">
                                            <i class="bi {{ $enrollment->status->icon() }} me-1"></i> {{ $enrollment->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-pencil-square me-1"></i> Proses
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Tidak ada data permohonan pendaftaran yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                    <div>
                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Setujui seluruh permohonan terpilih?')">
                            <i class="bi bi-check-all me-1"></i> Setujui Permohonan Terpilih (Batch)
                        </button>
                    </div>
                    <div>
                        {{ $enrollments->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.select-item:not(:disabled)').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
