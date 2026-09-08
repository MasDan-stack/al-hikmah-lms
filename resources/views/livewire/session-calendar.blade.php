<div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Jadwal Sesi Belajar Santri</h5>
                <p class="text-muted small mb-0">Kelola dan pantau sesi bimbingan Al-Qur'an online/offline</p>
            </div>
            <div class="d-flex gap-2">
                <select wire:model.live="filterStatus" class="form-select form-select-sm rounded-pill px-3">
                    <option value="all">Semua Status</option>
                    <option value="scheduled">Terjadwal</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Santri</th>
                        <th class="border-0">Pendamping</th>
                        <th class="border-0">Program</th>
                        <th class="border-0">Waktu Sesi</th>
                        <th class="border-0">Mode</th>
                        <th class="border-0">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $session->student?->user?->name ?? 'Santri' }}</div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $session->mentor?->user?->name ?? 'Ustaz/Ustazah' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                    Tahsin & Tahfidz
                                </span>
                            </td>
                            <td>
                                <div class="small fw-medium text-dark">{{ $session->date ? \Carbon\Carbon::parse($session->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}</div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $session->time ? date('H:i', strtotime($session->time)) : '' }} WIB</small>
                            </td>
                            <td>
                                @if($session->method === 'offline')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Offline</span>
                                @elseif($session->method === 'online')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2">Online</span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">Hybrid</span>
                                @endif
                            </td>
                            <td>
                                @if($session->status === 'completed')
                                    <span class="badge bg-success-subtle text-success rounded-pill">Selesai</span>
                                @elseif($session->status === 'cancelled')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Dibatalkan</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Terjadwal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 text-muted opacity-50"></i>
                                Belum ada jadwal sesi belajar terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
