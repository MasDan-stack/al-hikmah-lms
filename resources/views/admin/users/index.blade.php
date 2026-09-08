@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Role | AL-HIKMAH')
@section('header', 'Manajemen Pengguna')
@section('subheader', 'Kelola seluruh akun pengguna, kontak, dan hak akses peran (Role Access Control)')

@section('content')
<div class="container-fluid px-0">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Filter & Tombol Tambah -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-md-8">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center" id="formFilterUsers">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="userSearchInput" name="search" class="form-control bg-light border-0" placeholder="Cari nama, email, atau no. telp..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select id="userRoleFilter" name="role" class="form-select bg-light border-0">
                                <option value="">Semua Role</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>Mentor / Guru</option>
                                <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>Orang Tua / Wali</option>
                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Santri Binaan</option>
                            </select>
                        </div>
                        <div class="col-sm-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom flex-grow-1 rounded-3">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'role']))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filter">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="button" class="btn btn-primary-custom rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                        <i class="bi bi-person-plus-fill me-2"></i> Tambah Pengguna
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0 datatable" id="tableAdminUsers" data-export="true" data-no-search="true" data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Nama Pengguna</th>
                            <th>Hak Akses (Role)</th>
                            <th>Alamat Email</th>
                            <th>Nomor Telepon</th>
                            <th>Terdaftar Sejak</th>
                            <th class="text-end pe-3 no-sort">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            @if(auth()->id() === $user->id)
                                                <span class="badge bg-warning-subtle text-warning small"><i class="bi bi-star-fill me-1"></i> Akun Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $roleName = strtolower($user->role?->name ?? '');
                                    @endphp
                                    @if($roleName === 'admin')
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">
                                            <i class="bi bi-shield-lock-fill me-1"></i> Administrator
                                        </span>
                                    @elseif($roleName === 'mentor')
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                                            <i class="bi bi-person-badge-fill me-1"></i> Mentor / Guru
                                        </span>
                                    @elseif($roleName === 'parent')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                            <i class="bi bi-people-fill me-1"></i> Orang Tua / Wali
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fw-semibold">
                                            <i class="bi bi-book-half me-1"></i> Santri Binaan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-secondary fw-semibold">{{ $user->email }}</td>
                                <td>
                                    @if($user->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="text-decoration-none text-success fw-medium">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $user->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                                <td class="text-end pe-4">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1 btn-edit-user"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditUser"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phone="{{ $user->phone }}"
                                            data-role-id="{{ $user->role_id }}"
                                            data-action="{{ route('admin.users.update', $user->id) }}">
                                        <i class="bi bi-pencil me-1"></i> Edit Role
                                    </button>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Hapus User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                                    Tidak ada data pengguna yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($users, 'hasPages') && $users->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL TAMBAH PENGGUNA -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahUserLabel">
                        <i class="bi bi-person-plus text-primary me-2"></i> Tambah Pengguna Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Ahmad Dahlan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="user@alhikmah.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Peran / Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->label ?? ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter...">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT PENGGUNA & ROLE -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formEditUser" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditUserLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Data & Hak Akses Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="editUserName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" id="editUserEmail" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nomor Telepon / WhatsApp</label>
                        <input type="text" id="editUserPhone" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Peran / Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select id="editUserRoleId" name="role_id" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->label ?? ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Perubahan role akan otomatis menyinkronkan profil domain pengguna tersebut.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Ubah Kata Sandi (Opsional)</label>
                        <input type="password" name="password" class="form-control" minlength="8" placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Modal Edit User Handler
    const editModalEl = document.getElementById('modalEditUser');
    if (editModalEl) {
        editModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const action = button.getAttribute('data-action');
            const name   = button.getAttribute('data-name');
            const email  = button.getAttribute('data-email');
            const phone  = button.getAttribute('data-phone');
            const roleId = button.getAttribute('data-role-id');

            const form = editModalEl.querySelector('#formEditUser');
            form.action = action;

            editModalEl.querySelector('#editUserName').value = name || '';
            editModalEl.querySelector('#editUserEmail').value = email || '';
            editModalEl.querySelector('#editUserPhone').value = phone || '';
            editModalEl.querySelector('#editUserRoleId').value = roleId || '';
        });
    }

    // 2. Integrasi Live Search & Filter Role dengan DataTables
    const usersTableEl = document.getElementById('tableAdminUsers');
    const searchInput = document.getElementById('userSearchInput');
    const roleSelect = document.getElementById('userRoleFilter');
    const filterForm = document.getElementById('formFilterUsers');

    function getTableInstance() {
        if (!usersTableEl) return null;
        if (typeof DataTable !== 'undefined' && DataTable.isDataTable(usersTableEl)) {
            return new DataTable(usersTableEl);
        }
        return usersTableEl.__dataTable || null;
    }

    function applyRoleFilter(dt, roleVal) {
        if (!dt) return;
        const roleTerm = (roleVal || '').trim().toLowerCase();
        if (!roleTerm) {
            dt.column(1).search('').draw();
            return;
        }

        const roleKeywords = {
            'admin': 'Administrator',
            'mentor': 'Mentor',
            'parent': 'Orang Tua',
            'student': 'Santri'
        };

        const keyword = roleKeywords[roleTerm] || roleTerm;
        dt.column(1).search(keyword).draw();
    }

    if (searchInput) {
        let searchTimer = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const dt = getTableInstance();
                if (dt) {
                    dt.search(this.value.trim()).draw();
                }
            }, 120);
        });
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            const dt = getTableInstance();
            applyRoleFilter(dt, this.value);
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            const dt = getTableInstance();
            if (dt) {
                e.preventDefault();
                if (searchInput) {
                    dt.search(searchInput.value.trim());
                }
                if (roleSelect) {
                    applyRoleFilter(dt, roleSelect.value);
                }
                dt.draw();
            }
        });
    }
});
</script>
@endpush
@endsection
