@extends('layouts.parent')

@section('title', 'Daftar Pendaftaran Program | AL-HIKMAH')
@section('header', 'Pendaftaran & Negosiasi Jadwal')
@section('subheader', 'Kelola permohonan bimbingan dan status kesepakatan jadwal belajar ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-card border-bottom border-subtle pt-4 pb-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold text-heading mb-1">
                    <i class="bi bi-journal-check text-success me-2"></i>Daftar Pengajuan Jadwal Belajar
                </h5>
                <p class="text-muted small mb-0">Pantau proses pencocokan jadwal oleh lembaga dan status kesepakatan guru pembimbing.</p>
            </div>
            <div>
                <a href="{{ route('biaya') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm" style="background: var(--primary-gradient); border: none;">
                    <i class="bi bi-plus-lg me-1"></i> Daftar Program Baru
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-2 p-3" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-2 p-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                    <div class="fw-semibold">{{ session('warning') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Status Nav Pills -->
            <div class="mb-4">
                <div class="nav nav-pills flex-wrap gap-2">
                    <a href="{{ route('parent.enrollments.index') }}" class="nav-link rounded-pill {{ !request('status') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill me-1"></i> Semua Status
                    </a>
                    @foreach(\App\Enums\EnrollmentStatus::cases() as $statusCase)
                        <a href="{{ route('parent.enrollments.index', ['status' => $statusCase->value]) }}" class="nav-link rounded-pill {{ request('status') == $statusCase->value ? 'active' : '' }}">
                            <i class="bi {{ $statusCase->icon() }} me-1"></i> {{ $statusCase->label() }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if($enrollments->isEmpty())
                <div class="text-center py-5">
                    <div class="p-4 rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-journal-x fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-heading mb-1">Belum Ada Pengajuan Program</h5>
                    <p class="text-muted small mb-4">Anda belum memiliki permohonan pendaftaran program belajar yang sedang diajukan.</p>
                    <a href="{{ route('biaya') }}" class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-compass me-1"></i> Jelajahi Program Belajar
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable" id="tableParentEnrollments" data-no-paging="true">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Santri / Anak Binaan</th>
                                <th>Program Belajar</th>
                                <th>Jadwal & Metode</th>
                                <th>Status Negosiasi</th>
                                <th>Tagihan SPP</th>
                                <th class="text-end pe-3 no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $index => $enrollment)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">{{ $enrollments->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold fs-6 flex-shrink-0" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($enrollment->student->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <span class="fw-bold text-heading d-block">{{ $enrollment->student->getDisplayName() }}</span>
                                                <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $enrollment->student->age }} Tahun</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success d-block mb-1">{{ $enrollment->program->name }}</span>
                                        <span class="badge bg-light text-dark border border-subtle rounded-pill small">
                                            {{ $enrollment->formatted_price }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <span class="d-block text-heading fw-semibold mb-1">
                                                <i class="bi bi-calendar3 text-primary me-1"></i> {{ $enrollment->requested_days_label }}
                                            </span>
                                            <span class="text-muted d-block">
                                                <i class="bi bi-clock me-1"></i> {{ $enrollment->requested_time_label }}
                                            </span>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-1">
                                                {{ ucfirst($enrollment->learning_method ?? 'offline') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status->badgeClass() }} px-3 py-2 rounded-pill fw-semibold">
                                            <i class="bi {{ $enrollment->status->icon() }} me-1"></i> {{ $enrollment->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($enrollment->payment)
                                            <span class="badge bg-{{ $enrollment->payment->status === 'paid' ? 'success' : 'warning text-dark' }} px-3 py-2 rounded-pill fw-semibold">
                                                <i class="bi bi-credit-card me-1"></i> {{ ucfirst($enrollment->payment->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border border-subtle rounded-pill px-3 py-2">
                                                Belum Terbit
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('parent.enrollments.show', $enrollment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold">
                                            <i class="bi bi-arrow-right-circle me-1"></i> Detail & Jadwal
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
