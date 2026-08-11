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
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Kelola Data Pendamping / Guru</h5>
                    <p class="text-muted small mb-0">Manajemen pengajar Al-Qur'an, spesialisasi, rating, dan status aktif</p>
                </div>
                <button wire:click="openCreateModal" class="btn btn-daftar text-white px-4 py-2">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Pendamping
                </button>
            </div>

            <!-- Filters -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: var(--border-color);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Cari nama, spesialisasi, atau email..." style="border-color: var(--border-color); background: transparent; color: var(--text-primary);">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select wire:model.live="activeFilter" class="form-select" style="border-color: var(--border-color); background: transparent; color: var(--text-primary);">
                        <option value="">Semua Status Aktif</option>
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Nama Pendamping</th>
                            <th class="border-0">Spesialisasi</th>
                            <th class="border-0">Kontak & Email</th>
                            <th class="border-0">Rating</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mentors as $mentor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($mentor->full_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $mentor->full_name }}</div>
                                            <div class="small text-muted">{{ $mentor->bio ? Str::limit($mentor->bio, 35) : 'Belum ada bio' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                                        {{ $mentor->specialization ?? 'Umum / Tahsin' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-dark fw-medium">{{ $mentor->user?->email ?? '-' }}</div>
                                    <div class="small text-muted"><i class="bi bi-whatsapp text-success me-1"></i>{{ $mentor->user?->phone ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-warning fw-bold small">
                                        <i class="bi bi-star-fill"></i>
                                        <span>{{ number_format($mentor->rating, 1) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <button wire:click="toggleActive({{ $mentor->id }})" class="btn btn-sm {{ $mentor->is_active ? 'btn-success-subtle text-success border-success-subtle' : 'btn-secondary-subtle text-secondary border-secondary-subtle' }} rounded-pill px-3">
                                        <i class="bi {{ $mentor->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }} me-1"></i>
                                        {{ $mentor->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </button>
                                </td>
                                <td class="text-end">
                                    <button wire:click="openEditModal({{ $mentor->id }})" class="btn btn-sm btn-outline-primary me-1 rounded-3">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $mentor->id }})" class="btn btn-sm btn-outline-danger rounded-3">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-person-x fs-2 d-block mb-2 opacity-50"></i>
                                    Tidak ada data pendamping yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $mentors->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Create / Edit -->
    @if($isModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: var(--text-primary);">
                            {{ $mentorId ? 'Edit Data Pendamping' : 'Tambah Pendamping Baru' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveMentor">
                        <div class="modal-body py-3">
                            @if(!$mentorId)
                                <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" wire:model.live="create_new_user" id="createNewUserSwitch">
                                        <label class="form-check-label fw-semibold text-dark" for="createNewUserSwitch">
                                            Buatkan Akun User Baru Otomatis
                                        </label>
                                    </div>
                                    <p class="small text-muted mb-0">Jika dinonaktifkan, Anda dapat menghubungkan profil pendamping ini ke akun user yang sudah terdaftar sebelumnya.</p>
                                </div>

                                @if($create_new_user)
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-medium small">Email User <span class="text-danger">*</span></label>
                                            <input type="email" wire:model="user_email" class="form-control @error('user_email') is-invalid @enderror" placeholder="mentor@alhikmah.id">
                                            @error('user_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-medium small">Password User <span class="text-danger">*</span></label>
                                            <input type="password" wire:model="user_password" class="form-control @error('user_password') is-invalid @enderror" placeholder="Minimal 6 karakter">
                                            @error('user_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small">Pilih Akun User Existing <span class="text-danger">*</span></label>
                                        <select wire:model="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                            <option value="">-- Pilih User --</option>
                                            @foreach($availableUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        </select>
                                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif
                            @endif

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Nama Lengkap Pendamping <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="Ustadz / Ustadzah...">
                                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium small">No. WhatsApp / Telepon</label>
                                    <input type="text" wire:model="user_phone" class="form-control @error('user_phone') is-invalid @enderror" placeholder="08123456789">
                                    @error('user_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Spesialisasi Mengajar</label>
                                    <input type="text" wire:model="specialization" class="form-control @error('specialization') is-invalid @enderror" placeholder="Tahsin, Tahfidz 30 Juz, Tajwid">
                                    @error('specialization') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-3">
                                    <label class="form-label fw-medium small">Rating (0 - 5)</label>
                                    <input type="number" step="0.1" min="0" max="5" wire:model="rating" class="form-control @error('rating') is-invalid @enderror">
                                    @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-3">
                                    <label class="form-label fw-medium small">Status Aktif</label>
                                    <select wire:model="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                        <option value="1">Aktif</option>
                                        <option value="0">Non-Aktif</option>
                                    </select>
                                    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium small">Biografi & Pengalaman</label>
                                <textarea wire:model="bio" class="form-control @error('bio') is-invalid @enderror" rows="3" placeholder="Profil singkat, latar belakang pendidikan Al-Qur'an..."></textarea>
                                @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <p class="small text-muted mb-4">Apakah Anda yakin ingin menghapus data pendamping ini?</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" wire:click="closeModal" class="btn btn-light rounded-3 px-3">Batal</button>
                            <button type="button" wire:click="deleteMentor" class="btn btn-danger rounded-3 px-3">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
