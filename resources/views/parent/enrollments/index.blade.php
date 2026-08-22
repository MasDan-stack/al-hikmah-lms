@extends('layouts.parent')

@section('title', 'Daftar Pendaftaran Program | AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-journal-check text-success me-2"></i>Pendaftaran & Negosiasi Jadwal</h5>
                <p class="text-muted small mb-0">Kelola permohonan pendaftaran program dan status kesepakatan jadwal belajar santri.</p>
            </div>
            <div>
                <a href="{{ route('biaya') }}" class="btn btn-primary-custom rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Daftar Program Baru
                </a>
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

            <!-- Filter Status -->
            <div class="mb-4">
                <form action="{{ route('parent.enrollments.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <a href="{{ route('parent.enrollments.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3">
                        Semua Status
                    </a>
                    @foreach(\App\Enums\EnrollmentStatus::cases() as $statusCase)
                        <a href="{{ route('parent.enrollments.index', ['status' => $statusCase->value]) }}" class="btn btn-sm {{ request('status') == $statusCase->value ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3">
                            {{ $statusCase->label() }}
                        </a>
                    @endforeach
                </form>
            </div>

            @if($enrollments->isEmpty())
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/emerald/student-with-books.svg" alt="Empty" style="max-width: 180px;" class="mb-3 opacity-75">
                    <h6 class="fw-bold text-secondary mb-1">Belum Ada Pendaftaran Program</h6>
                    <p class="text-muted small mb-3">Anda belum memiliki permohonan pendaftaran program yang diajukan.</p>
                    <a href="{{ route('biaya') }}" class="btn btn-outline-success rounded-pill px-4">
                        Pilih Program Belajar
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable" id="tableParentEnrollments">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Santri / Anak</th>
                                <th>Program</th>
                                <th>Jadwal Diminta</th>
                                <th>Status</th>
                                <th>Tagihan</th>
                                <th class="text-end no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $index => $enrollment)
                                <tr>
                                    <td>{{ $enrollments->firstItem() + $index }}</td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $enrollment->student->getDisplayName() }}</span>
                                        <small class="text-muted">{{ $enrollment->student->age }} Tahun</small>
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
                                        <span class="badge bg-{{ $enrollment->status->badgeClass() }} px-3 py-2 rounded-pill">
                                            <i class="bi {{ $enrollment->status->icon() }} me-1"></i> {{ $enrollment->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($enrollment->payment)
                                            <span class="badge bg-{{ $enrollment->payment->status === 'paid' ? 'success' : 'warning' }} rounded-pill">
                                                {{ ucfirst($enrollment->payment->status) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('parent.enrollments.show', $enrollment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Detail & Negosiasi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
