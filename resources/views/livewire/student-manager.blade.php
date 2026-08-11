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
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Kelola Data Murid / Santri</h5>
                    <p class="text-muted small mb-0">Manajemen data santri (usia 10-15 thn), relasi Orang Tua, dan domisili</p>
                </div>
                <button wire:click="openCreateModal" class="btn btn-daftar text-white px-4 py-2">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Santri
                </button>
            </div>

            <!-- Filters -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: var(--border-color);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Cari nama santri, lokasi, atau email..." style="border-color: var(--border-color); background: transparent; color: var(--text-primary);">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select wire:model.live="genderFilter" class="form-select" style="border-color: var(--border-color); background: transparent; color: var(--text-primary);">
                        <option value="">Semua Gender</option>
                        <option value="L">Laki-laki (Ikhwan)</option>
                        <option value="P">Perempuan (Akhwat)</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Nama Santri</th>
                            <th class="border-0">Usia & Gender</th>
                            <th class="border-0">Orang Tua / Wali</th>
                            <th class="border-0">Lokasi / Domisili</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle {{ $student->gender === 'L' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger' }} fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $student->full_name }}</div>
                                            <div class="small text-muted">{{ $student->user?->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $student->age }} Tahun</div>
                                    <span class="badge {{ $student->gender === 'L' ? 'bg-info-subtle text-info' : 'bg-pink-subtle text-danger' }} rounded-pill px-2.5 py-0.5 small">
                                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                                <td>
                                    @if($student->parent)
                                        <div class="fw-medium text-dark">{{ $student->parent->user?->name ?? 'Orang Tua' }}</div>
                                        <div class="small text-muted"><i class="bi bi-telephone text-success me-1"></i>{{ $student->parent->emergency_phone ?? $student->parent->user?->phone ?? '-' }}</div>
                                    @else
                                        <span class="text-muted small">Belum terhubung</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-secondary"><i class="bi bi-geo-alt me-1"></i>{{ $student->location ?? '-' }}</span>
                                </td>
                                <td class="text-end">
                                    <button wire:click="openEditModal({{ $student->id }})" class="btn btn-sm btn-outline-primary me-1 rounded-3">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $student->id }})" class="btn btn-sm btn-outline-danger rounded-3">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-people-fill fs-2 d-block mb-2 opacity-50"></i>
                                    Tidak ada data santri yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $students->links() }}
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
                            {{ $studentId ? 'Edit Data Santri' : 'Tambah Santri Baru' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveStudent">
                        <div class="modal-body py-3">
                            @if(!$studentId)
                                <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" wire:model.live="create_new_user" id="createNewStudentUserSwitch">
                                        <label class="form-check-label fw-semibold text-dark" for="createNewStudentUserSwitch">
                                            Buatkan Akun User Baru Otomatis
                                        </label>
                                    </div>
                                    <p class="small text-muted mb-0">Jika dinonaktifkan, Anda dapat memilih akun user existing yang sudah dibuat.</p>
                                </div>

                                @if($create_new_user)
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-medium small">Email User Santri <span class="text-danger">*</span></label>
                                            <input type="email" wire:model="user_email" class="form-control @error('user_email') is-invalid @enderror" placeholder="santri@alhikmah.id">
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
                                    <label class="form-label fw-medium small">Nama Lengkap Santri <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="Nama santri...">
                                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-3">
                                    <label class="form-label fw-medium small">Usia (Tahun) <span class="text-danger">*</span></label>
                                    <input type="number" min="3" max="30" wire:model="age" class="form-control @error('age') is-invalid @enderror">
                                    @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-3">
                                    <label class="form-label fw-medium small">Gender <span class="text-danger">*</span></label>
                                    <select wire:model="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Orang Tua / Wali (Opsional)</label>
                                    <select wire:model="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">-- Pilih Orang Tua / Wali --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->user?->name ?? 'Orang Tua #'.$parent->id }} ({{ $parent->emergency_phone ?? 'Tanpa No. HP' }})</option>
                                        @endforeach
                                    </select>
                                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Kota / Domisili</label>
                                    <input type="text" wire:model="location" class="form-control @error('location') is-invalid @enderror" placeholder="Jakarta Selatan, Bandung, dll.">
                                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium small">Catatan Khusus Santri</label>
                                <textarea wire:model="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Target hafalan, kebutuhan khusus, dll..."></textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <p class="small text-muted mb-4">Apakah Anda yakin ingin menghapus data santri ini?</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" wire:click="closeModal" class="btn btn-light rounded-3 px-3">Batal</button>
                            <button type="button" wire:click="deleteStudent" class="btn btn-danger rounded-3 px-3">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
