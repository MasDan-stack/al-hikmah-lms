<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
        <div class="card-body p-4">
            <!-- Header Controls -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Kelola Program Belajar</h5>
                    <p class="text-muted small mb-0">Daftar paket bimbingan Al-Qur'an (Tahsin, Tahfidz, Tajwid, Iqra, dll.)</p>
                </div>
                <button wire:click="openCreateModal" class="btn btn-daftar text-white px-4 py-2">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Program
                </button>
            </div>

            <!-- Filters -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: var(--border-color);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Cari nama program..." style="border-color: var(--border-color); background: transparent; color: var(--text-primary);">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select wire:model.live="levelFilter" class="form-select" style="border-color: var(--border-color); background: transparent; color: var(--text-primary);">
                        <option value="">Semua Tingkat (Level)</option>
                        <option value="Pemula">Pemula</option>
                        <option value="Menengah">Menengah</option>
                        <option value="Lanjutan">Lanjutan</option>
                        <option value="Semua Tingkat">Semua Tingkat</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Nama Program</th>
                            <th class="border-0">Tingkat / Level</th>
                            <th class="border-0">Durasi</th>
                            <th class="border-0">Biaya</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $program->name }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 300px;">{{ $program->description ?? '-' }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($program->level) {
                                            'Pemula' => 'bg-info-subtle text-info',
                                            'Menengah' => 'bg-warning-subtle text-warning',
                                            'Lanjutan' => 'bg-danger-subtle text-danger',
                                            default => 'bg-success-subtle text-success'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-semibold">{{ $program->level }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-secondary"><i class="bi bi-clock me-1"></i>{{ $program->duration_weeks }} Minggu</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">Rp {{ number_format($program->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-end">
                                    <button wire:click="openEditModal({{ $program->id }})" class="btn btn-sm btn-outline-primary me-1 rounded-3">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $program->id }})" class="btn btn-sm btn-outline-danger rounded-3">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-50"></i>
                                    Tidak ada data program belajar yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $programs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Create / Edit -->
    @if($isModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: var(--text-primary);">
                            {{ $programId ? 'Edit Program Belajar' : 'Tambah Program Belajar Baru' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveProgram">
                        <div class="modal-body py-3">
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Nama Program <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Tahsin & Tajwid Dasar">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium small">Tingkat / Level <span class="text-danger">*</span></label>
                                <select wire:model="level" class="form-select @error('level') is-invalid @enderror">
                                    <option value="Pemula">Pemula</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Lanjutan">Lanjutan</option>
                                    <option value="Semua Tingkat">Semua Tingkat</option>
                                </select>
                                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Durasi (Minggu) <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="duration_weeks" class="form-control @error('duration_weeks') is-invalid @enderror" min="1">
                                    @error('duration_weeks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Biaya Program (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="price" class="form-control @error('price') is-invalid @enderror" min="0" step="50000">
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium small">Deskripsi Program</label>
                                <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Rincian materi dan target capaian..."></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="closeModal" class="btn btn-light rounded-3">Batal</button>
                            <button type="submit" class="btn btn-daftar text-white rounded-3 px-4">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Delete Confirmation -->
    @if($isDeleteModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning display-4 d-block mb-3"></i>
                        <h6 class="fw-bold mb-2">Konfirmasi Hapus</h6>
                        <p class="small text-muted mb-4">Apakah Anda yakin ingin menghapus program belajar ini?</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" wire:click="closeModal" class="btn btn-light rounded-3 px-3">Batal</button>
                            <button type="button" wire:click="deleteProgram" class="btn btn-danger rounded-3 px-3">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
