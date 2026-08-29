@extends('layouts.admin')

@section('title', 'Manajemen Cuti & Guru Pengganti')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-badge mb-2"><i class="bi bi-calendar2-x-fill text-warning"></i> Manajemen SDM Guru</div>
            <h1 class="h3 fw-bold mb-1">Manajemen Cuti & <span class="text-gradient">Guru Pengganti</span></h1>
            <p class="text-muted small mb-0">Tinjau permohonan cuti guru pembimbing, tunjuk guru pengganti (*substitute*), dan kirim notifikasi otomatis ke wali santri.</p>
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

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Cuti Hari Ini</span>
                        <div class="rounded-3 p-2 bg-info-subtle text-info">
                            <i class="bi bi-calendar-check-fill fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-info">{{ $leavesToday }} <span class="fs-6 fw-normal text-muted">Guru</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Menunggu Approval</span>
                        <div class="rounded-3 p-2 bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-warning">{{ $pendingCount }} <span class="fs-6 fw-normal text-muted">Permohonan</span></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Disetujui</span>
                        <div class="rounded-3 p-2 bg-success-subtle text-success">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success">{{ $approvedCount }} <span class="fs-6 fw-normal text-muted">Disetujui</span></h3>
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
                    <h3 class="fw-bold mb-0 text-danger">{{ $rejectedCount }} <span class="fs-6 fw-normal text-muted">Ditolak</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tab Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg);">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="btn-group rounded-pill p-1 bg-light border" role="group">
                    <a href="{{ route('admin.mentors.leaves.index') }}" class="btn btn-sm rounded-pill px-3 {{ !$status ? 'btn-primary shadow-sm fw-bold' : 'btn-light text-secondary' }}">
                        Semua Status ({{ $leaves->count() }})
                    </a>
                    <a href="{{ route('admin.mentors.leaves.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-3 {{ $status === 'pending' ? 'btn-warning text-dark shadow-sm fw-bold' : 'btn-light text-secondary' }}">
                        Menunggu ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('admin.mentors.leaves.index', ['status' => 'approved']) }}" class="btn btn-sm rounded-pill px-3 {{ $status === 'approved' ? 'btn-success shadow-sm fw-bold' : 'btn-light text-secondary' }}">
                        Disetujui ({{ $approvedCount }})
                    </a>
                    <a href="{{ route('admin.mentors.leaves.index', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill px-3 {{ $status === 'rejected' ? 'btn-danger shadow-sm fw-bold' : 'btn-light text-secondary' }}">
                        Ditolak ({{ $rejectedCount }})
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg);">
        <div class="card-body p-4">
            @if($leaves->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold text-secondary mt-3">Tidak Ada Data Permohonan Cuti</h5>
                    <p class="text-muted small mb-0">Belum ada permohonan cuti guru yang sesuai dengan filter saat ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableAdminLeaves">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Guru Pemohon</th>
                                <th>Tanggal Cuti</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Guru Pengganti (*Substitute*)</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $index => $leave)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                                {{ strtoupper(substr($leave->mentor?->getDisplayName() ?? 'G', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $leave->mentor?->getDisplayName() }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="bi bi-telephone me-1"></i>{{ $leave->mentor?->user?->phone ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <i class="bi bi-calendar-event text-primary me-1"></i>
                                            {{ $leave->leave_date->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">{{ $leave->leave_date->translatedFormat('l') }}</small>
                                        @if($leave->leave_date->isToday())
                                            <span class="badge bg-danger rounded-pill ms-1">Hari Ini</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-secondary small">{{ $leave->reason ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                            </span>
                                        @elseif($leave->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                                <i class="bi bi-hourglass-split me-1"></i> Menunggu
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
                                                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($leave->substituteMentor->getDisplayName(), 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small">{{ $leave->substituteMentor->getDisplayName() }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $leave->substituteMentor->specialization ?? 'Pendamping' }}</div>
                                                </div>
                                            </div>
                                        @elseif($leave->status === 'approved')
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Tanpa Pengganti</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if($leave->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-2 py-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalApproveLeave{{ $leave->id }}"
                                                    title="Setujui & Tunjuk Pengganti">
                                                    <i class="bi bi-check-lg me-1"></i> Setujui
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalRejectLeave{{ $leave->id }}"
                                                    title="Tolak Permohonan">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalApproveLeave{{ $leave->id }}"
                                                    title="Ubah Pengganti">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif

                                            <form method="POST" action="{{ route('admin.mentors.leaves.destroy', $leave->id) }}" onsubmit="return confirm('Hapus riwayat cuti ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Setujui Cuti -->
                                <div class="modal fade" id="modalApproveLeave{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="bi bi-person-check-fill text-success me-2"></i>Persetujuan Cuti & Penugasan Pengganti
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.mentors.leaves.approve', $leave->id) }}">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <div class="row g-2 small">
                                                            <div class="col-6"><strong>Guru Pemohon:</strong><br>{{ $leave->mentor?->getDisplayName() }}</div>
                                                            <div class="col-6"><strong>Tanggal Cuti:</strong><br><span class="text-primary fw-bold">{{ $leave->leave_date->format('d M Y') }}</span></div>
                                                            <div class="col-12 mt-2"><strong>Alasan:</strong><br><span class="text-muted">{{ $leave->reason ?? '-' }}</span></div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-secondary small" for="substitute_mentor_{{ $leave->id }}">
                                                            Pilih Guru Pengganti (*Substitute Mentor*)
                                                        </label>
                                                        <select name="substitute_mentor_id" id="substitute_mentor_{{ $leave->id }}" class="form-select rounded-3">
                                                            <option value="">-- Tanpa Guru Pengganti (Sesi Mandiri / Disesuaikan) --</option>
                                                            @foreach($availableSubstitutes as $sub)
                                                                @if($sub->id !== $leave->mentor_id)
                                                                    <option value="{{ $sub->id }}" {{ $leave->substitute_mentor_id == $sub->id ? 'selected' : '' }}>
                                                                        {{ $sub->getDisplayName() }} ({{ $sub->specialization ?? 'Pendamping' }})
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted" style="font-size: 0.75rem;">Guru pengganti akan mendapatkan akses ke jadwal santri binaan pada tanggal ini.</small>
                                                    </div>

                                                    <div class="form-check form-switch mt-3">
                                                        <input class="form-check-input" type="checkbox" name="notify_parents" value="1" id="notifyParents{{ $leave->id }}" checked>
                                                        <label class="form-check-label small fw-semibold text-dark" for="notifyParents{{ $leave->id }}">
                                                            <i class="bi bi-whatsapp text-success me-1"></i> Kirim Notifikasi WhatsApp ke Wali Santri Binaan
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success rounded-pill px-4">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Simpan & Setujui
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Tolak Cuti -->
                                <div class="modal fade" id="modalRejectLeave{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger">
                                                    <i class="bi bi-x-circle-fill me-2"></i>Tolak Permohonan Cuti
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.mentors.leaves.reject', $leave->id) }}">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    <p class="small text-muted mb-3">
                                                        Apakah Anda yakin ingin menolak permohonan cuti dari <strong>{{ $leave->mentor?->getDisplayName() }}</strong> untuk tanggal <strong>{{ $leave->leave_date->format('d M Y') }}</strong>?
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-secondary small" for="rejection_note_{{ $leave->id }}">
                                                            Catatan / Alasan Penolakan (Opsional)
                                                        </label>
                                                        <textarea name="rejection_note" id="rejection_note_{{ $leave->id }}" rows="3" class="form-control rounded-3" placeholder="Contoh: Jadwal ujian tasmi' santri tidak dapat diundur / kuota pengganti penuh..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                                                        <i class="bi bi-x-circle me-1"></i> Tolak Cuti
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#tableAdminLeaves').DataTable({
                pageLength: 10,
                language: {
                    search: "Cari permohonan:",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ permohonan",
                    paginate: {
                        first: "«",
                        previous: "‹",
                        next: "›",
                        last: "»"
                    },
                    emptyTable: "Tidak ada data cuti yang sesuai."
                }
            });
        }
    });
</script>
@endpush
