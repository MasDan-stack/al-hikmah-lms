@extends('layouts.mentor')

@section('title', 'Pengajuan Cuti & Guru Pengganti')

@section('content')
<div class="container-fluid">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-badge mb-2"><i class="bi bi-calendar2-x-fill text-warning"></i> Manajemen Kehadiran & Cuti</div>
            <h1 class="h3 fw-bold mb-1">Pengajuan Cuti & <span class="text-gradient">Guru Pengganti</span></h1>
            <p class="text-muted small mb-0">Ajukan permohonan cuti mengajar dan pantau alokasi guru pengganti (*substitute mentor*) secara transparan.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary-custom rounded-3 px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAjukanCuti">
                <i class="bi bi-plus-circle me-2"></i> Ajukan Cuti Baru
            </button>
        </div>
    </div>

    <!-- Alert Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                <div>{{ session('warning') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Pengajuan</span>
                        <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                            <i class="bi bi-calendar-event fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-primary">{{ $totalLeaves }} <span class="fs-6 fw-normal text-muted">Hari</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Menunggu Approval</span>
                        <div class="rounded-3 p-2 bg-warning-subtle text-warning">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-warning">{{ $pendingLeaves }} <span class="fs-6 fw-normal text-muted">Hari</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Disetujui Admin</span>
                        <div class="rounded-3 p-2 bg-success-subtle text-success">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success">{{ $approvedLeaves }} <span class="fs-6 fw-normal text-muted">Hari</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Ditolak</span>
                        <div class="rounded-3 p-2 bg-danger-subtle text-danger">
                            <i class="bi bi-x-circle fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger">{{ $rejectedLeaves }} <span class="fs-6 fw-normal text-muted">Hari</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg);">
        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat & Status Cuti Anda</h5>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-2">
                {{ $leaves->count() }} Catatan Ditemukan
            </span>
        </div>
        <div class="card-body p-4">
            @if($leaves->isEmpty())
                <div class="text-center py-5">
                    <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold text-secondary">Belum Ada Pengajuan Cuti</h5>
                    <p class="text-muted small mb-3">Jika Anda berhalangan hadir atau membutuhkan cuti, silakan klik tombol di bawah.</p>
                    <button type="button" class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAjukanCuti">
                        <i class="bi bi-plus-circle me-1"></i> Ajukan Cuti Sekarang
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableMentorLeaves">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tanggal Cuti</th>
                                <th>Hari</th>
                                <th>Alasan Cuti</th>
                                <th>Status Approval</th>
                                <th>Guru Pengganti (*Substitute*)</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $index => $leave)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <i class="bi bi-calendar-event text-primary me-1"></i>
                                            {{ $leave->leave_date->format('d M Y') }}
                                        </div>
                                        @if($leave->leave_date->isToday())
                                            <span class="badge bg-danger rounded-pill mt-1">Hari Ini</span>
                                        @elseif($leave->leave_date->isFuture())
                                            <span class="badge bg-info-subtle text-info rounded-pill mt-1">Mendatang</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-1">Lampau</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary fw-semibold">
                                        {{ $leave->leave_date->translatedFormat('l') }}
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ $leave->reason ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                            </span>
                                        @elseif($leave->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                                <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">
                                                <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($leave->substituteMentor)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($leave->substituteMentor->getDisplayName(), 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small">{{ $leave->substituteMentor->getDisplayName() }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $leave->substituteMentor->specialization ?? 'Pendamping Al-Qur\'an' }}</div>
                                                </div>
                                            </div>
                                        @elseif($leave->status === 'approved')
                                            <span class="text-muted small fst-italic">Tanpa Pengganti (Sesi Disesuaikan)</span>
                                        @else
                                            <span class="text-muted small fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($leave->status === 'pending')
                                            <form method="POST" action="{{ route('mentor.leaves.destroy', $leave->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti tanggal {{ $leave->leave_date->format('d M Y') }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Batalkan Permohonan">
                                                    <i class="bi bi-trash3 me-1"></i> Batal
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
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

<!-- Modal Form Ajukan Cuti -->
<div class="modal fade" id="modalAjukanCuti" tabindex="-1" aria-labelledby="modalAjukanCutiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalAjukanCutiLabel">
                    <i class="bi bi-calendar2-plus-fill text-primary me-2"></i>Form Permohonan Cuti Mengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('mentor.leaves.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info rounded-3 small mb-3 border-0">
                        <i class="bi bi-info-circle-fill me-1"></i> Pengajuan cuti akan ditinjau oleh Admin Lembaga. Admin akan mengatur guru pengganti jika diperlukan agar santri binaan tetap terlaksana pembelajarannya.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="start_date">
                                Tanggal Mulai Cuti <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" class="form-control rounded-3" value="{{ old('start_date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="end_date">
                                Tanggal Selesai (Opsional)
                            </label>
                            <input type="date" name="end_date" id="end_date" class="form-control rounded-3" value="{{ old('end_date') }}" min="{{ now()->format('Y-m-d') }}">
                            <small class="text-muted" style="font-size: 0.75rem;">Kosongkan jika cuti hanya 1 hari.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small" for="reason">
                                Alasan Permohonan Cuti <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="3" class="form-control rounded-3" placeholder="Contoh: Keperluan keluarga mendesak / Sakit / Melaksanakan ibadah..." required>{{ old('reason') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
                        <i class="bi bi-send-fill me-1"></i> Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#tableMentorLeaves').DataTable({
                pageLength: 10,
                language: {
                    search: "Cari riwayat:",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ cuti",
                    paginate: {
                        first: "«",
                        previous: "‹",
                        next: "›",
                        last: "»"
                    },
                    emptyTable: "Tidak ada data cuti yang cocok."
                }
            });
        }
    });
</script>
@endpush
