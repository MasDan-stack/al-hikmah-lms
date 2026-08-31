<div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-people-fill text-primary me-2"></i>Distribusi Beban Kerja & Performa Guru
            </h5>
            <p class="text-muted small mb-0">Status alokasi santri binaan per mentor beserta deteksi kapasitas overload.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success">≤30 Optimal</span>
            <span class="badge bg-warning bg-opacity-10 text-warning">31-40 Sibuk</span>
            <span class="badge bg-danger bg-opacity-10 text-danger">&gt;40 Overload</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="staffWorkloadTable">
            <thead class="table-light small text-uppercase text-muted">
                <tr>
                    <th>Guru Pembimbing</th>
                    <th>Spesialisasi</th>
                    <th class="text-center">Santri Binaan</th>
                    <th>Status Beban</th>
                    <th class="text-center">Presensi Sesi</th>
                    <th class="text-center">Target Selesai</th>
                    <th>Status Hari Ini</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mentors as $mentor)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-placeholder rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($mentor['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="color: var(--text-primary);">{{ $mentor['name'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="bi bi-telephone"></i> {{ $mentor['phone'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $mentor['specialization'] }}</span>
                            <div class="text-warning small mt-1">
                                <i class="bi bi-star-fill"></i> {{ number_format($mentor['rating'], 1) }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold fs-6" style="color: var(--text-primary);">{{ $mentor['active_students_count'] }}</div>
                            <span class="text-muted small">Santri</span>
                        </td>
                        <td>
                            <span class="badge {{ $mentor['badge_class'] }} rounded-pill px-3 py-1">
                                @if ($mentor['capacity_status'] === 'overload')
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                @endif
                                {{ $mentor['status_label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="fw-semibold text-success">{{ $mentor['attendance_rate'] }}%</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $mentor['completed_sessions_count'] }} sesi</div>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold text-primary">{{ $mentor['completed_targets_count'] }}</div>
                            <span class="text-muted small" style="font-size: 0.75rem;">Capaian</span>
                        </td>
                        <td>
                            @if ($mentor['is_on_leave_today'])
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">
                                    <i class="bi bi-calendar-x me-1"></i>Sedang Cuti
                                </span>
                            @elseif ($mentor['is_active'])
                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i>Aktif Mengajar
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.mentors.availability') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Atur Jadwal & Alokasi">
                                <i class="bi bi-calendar-check me-1"></i>Alokasi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data guru pembimbing terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
