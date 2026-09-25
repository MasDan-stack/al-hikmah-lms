@extends('layouts.admin')

@section('title', $isTrashedView ? 'Tong Sampah Kategori Galeri' : 'Manajemen Kategori Galeri')
@section('header', $isTrashedView ? 'Tong Sampah Kategori Galeri' : 'Kategori Galeri Kegiatan')
@section('subheader', $isTrashedView ? 'Daftar kategori yang dihapus sementara. Anda dapat memulihkan atau menghapusnya secara permanen.' : 'Kelola kelompok kategori, ikon, warna badge, dan urutan untuk dokumentasi kegiatan.')

@section('content')
<div class="container-fluid p-0">

    <!-- Flash Alert Notification Messages -->
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                <span class="fw-bold">Gagal Menyimpan Data! Periksa kesalahan berikut:</span>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistik Ringkas (4 Kartu Termasuk Tong Sampah) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-success-subtle text-success fs-4">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kategori Aktif</div>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary fs-4">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Status Aktif</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $stats['active'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-secondary-subtle text-secondary fs-4">
                        <i class="bi bi-eye-slash"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Nonaktif / Arsip</div>
                        <h4 class="fw-bold mb-0 text-muted">{{ $stats['inactive'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.gallery-categories.index', ['status' => 'trashed']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom {{ $isTrashedView ? 'border border-danger' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 bg-danger-subtle text-danger fs-4">
                            <i class="bi bi-trash3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tong Sampah</div>
                            <h4 class="fw-bold mb-0 text-danger">{{ $stats['trashed'] }}</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tab Navigasi Aktif vs Tong Sampah -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ !$isTrashedView ? 'active bg-success text-white' : 'bg-light text-dark' }}" href="{{ route('admin.gallery-categories.index') }}">
                    <i class="bi bi-tags me-1"></i> Data Aktif ({{ $stats['total'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $isTrashedView ? 'active bg-danger text-white' : 'bg-light text-dark' }}" href="{{ route('admin.gallery-categories.index', ['status' => 'trashed']) }}">
                    <i class="bi bi-trash3 me-1"></i> Tong Sampah ({{ $stats['trashed'] }})
                </a>
            </li>
        </ul>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-images me-1"></i> Galeri Foto ({{ $stats['total_galleries'] }})
            </a>

            @if (!$isTrashedView)
                <button type="button" class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kategori Baru
                </button>
            @else
                <a href="{{ route('admin.gallery-categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Kategori Aktif
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.gallery-categories.index') }}" class="row g-2 align-items-center">
                @if ($isTrashedView)
                    <input type="hidden" name="status" value="trashed">
                @endif
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Cari nama, slug, atau deskripsi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="group" class="form-select bg-light border-0 rounded-pill">
                        <option value="all">-- Semua Grup --</option>
                        @foreach ($groups as $gKey => $gLabel)
                            <option value="{{ $gKey }}" {{ request('group') === $gKey ? 'selected' : '' }}>{{ $gLabel }}</option>
                        @endforeach
                    </select>
                </div>
                @if (!$isTrashedView)
                    <div class="col-md-2">
                        <select name="status" class="form-select bg-light border-0 rounded-pill">
                            <option value="all">-- Semua Status --</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                @endif
                <div class="{{ !$isTrashedView ? 'col-md-2' : 'col-md-4' }} d-flex gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    @if (request()->hasAny(['search', 'group', 'status']))
                        <a href="{{ route('admin.gallery-categories.index', $isTrashedView ? ['status' => 'trashed'] : []) }}" class="btn btn-outline-secondary rounded-pill px-3" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Khusus Mode Tong Sampah -->
    @if ($isTrashedView)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle p-2 bg-warning-subtle text-warning fs-4">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div>
                <div class="fw-bold">Anda sedang melihat data di dalam Tong Sampah</div>
                <div class="small text-muted">Kategori yang berada di sini dapat dipulihkan (*Restore*) ke daftar aktif atau dihapus secara permanen (*Force Delete*).</div>
            </div>
        </div>
    @endif

    <!-- Tabel Data Kategori -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable" id="tableAdminGalleryCategories">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">Urutan</th>
                        <th>Kategori & Slug</th>
                        <th>Grup Kegiatan</th>
                        <th>Badge & Ikon Pratinjau</th>
                        <th class="text-center">Foto Terhubung</th>
                        <th class="text-center">{{ $isTrashedView ? 'Tanggal Dihapus' : 'Status' }}</th>
                        <th class="text-end pe-3 no-sort" style="width: {{ $isTrashedView ? '220px' : '140px' }};">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1">{{ $cat->sort_order }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $cat->name }}</div>
                                <code class="small text-muted">{{ $cat->slug }}</code>
                                @if ($cat->description)
                                    <div class="text-muted small mt-1 text-truncate" style="max-width: 320px;" title="{{ $cat->description }}">
                                        {{ $cat->description }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                                    {{ $cat->group }}
                                </span>
                            </td>
                            <td>
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span class="badge {{ $cat->badge_class }} rounded-pill px-3 py-2">
                                        <i class="bi {{ $cat->icon }} me-1"></i> {{ $cat->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if (!$isTrashedView)
                                    <a href="{{ route('admin.galleries.index', ['category' => $cat->slug]) }}" class="badge bg-success-subtle text-success text-decoration-none rounded-pill px-3 py-2 border border-success" title="Lihat foto dalam kategori ini">
                                        <i class="bi bi-images me-1"></i> {{ $cat->galleries_count }} Foto
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2">
                                        {{ $cat->galleries_count }} Foto
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!$isTrashedView)
                                    <form action="{{ route('admin.gallery-categories.toggle', $cat->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 py-1 border-0 {{ $cat->is_active ? 'btn-success text-white' : 'btn-secondary text-white' }}" title="Klik untuk ubah status">
                                            <i class="bi {{ $cat->is_active ? 'bi-check-circle' : 'bi-dash-circle' }} me-1"></i>
                                            {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 small">
                                        <i class="bi bi-clock-history me-1"></i> {{ $cat->deleted_at?->translatedFormat('d M Y, H:i') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if (!$isTrashedView)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-start-pill px-2"
                                                onclick="openEditCategoryModal({{ json_encode($cat) }})"
                                                title="Edit Kategori">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-end-pill px-2"
                                                onclick="confirmDeleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->galleries_count }})"
                                                title="Pindahkan ke Tong Sampah">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="d-inline-flex gap-1">
                                        <form action="{{ route('admin.gallery-categories.restore', $cat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1" title="Pulihkan kategori">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                                onclick="confirmForceDeleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                                                title="Hapus Permanen">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="display-6 text-muted mb-3"><i class="bi {{ $isTrashedView ? 'bi-trash3' : 'bi-tags' }}"></i></div>
                                <h6>{{ $isTrashedView ? 'Tong Sampah Kosong' : 'Belum Ada Data Kategori' }}</h6>
                                <p class="small mb-3">
                                    {{ $isTrashedView ? 'Tidak ada data kategori galeri yang sedang dihapus.' : 'Tambahkan kategori pertama untuk mengelompokkan dokumentasi foto kegiatan.' }}
                                </p>
                                @if (!$isTrashedView)
                                    <button type="button" class="btn btn-primary-custom btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                        + Tambah Kategori
                                    </button>
                                @else
                                    <a href="{{ route('admin.gallery-categories.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                        Kembali ke Kategori Aktif
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- 🟢 MODAL TAMBAH KATEGORI BARU               -->
<!-- ========================================== -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.gallery-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="createCategoryModalLabel">
                        <i class="bi bi-tag-fill text-success me-2"></i> Tambah Kategori Galeri
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="create_name" class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="create_name" class="form-control rounded-3" placeholder="Contoh: Kajian Ramadhan" required onkeyup="syncSlug(this.value, 'create_slug')">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="create_slug" class="form-label fw-bold">Slug URL <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="create_slug" class="form-control rounded-3" placeholder="kajian_ramadhan" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_group" class="form-label fw-bold">Grup Induk <span class="text-danger">*</span></label>
                            <select name="group" id="create_group" class="form-select rounded-3" required>
                                @foreach ($groups as $gKey => $gLabel)
                                    <option value="{{ $gKey }}">{{ $gKey }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="create_icon" class="form-label fw-bold">Ikon Bootstrap</label>
                            <select name="icon" id="create_icon" class="form-select rounded-3" onchange="updatePreviewBadge('create')">
                                @foreach ($iconOptions as $iVal => $iName)
                                    <option value="{{ $iVal }}" {{ $iVal === 'bi-images' ? 'selected' : '' }}>{{ $iName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_badge_class" class="form-label fw-bold">Warna Badge</label>
                            <select name="badge_class" id="create_badge_class" class="form-select rounded-3" onchange="updatePreviewBadge('create')">
                                @foreach ($badgeOptions as $bVal => $bName)
                                    <option value="{{ $bVal }}" {{ $bVal === 'bg-success' ? 'selected' : '' }}>{{ $bName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Pratinjau Tampilan Badge Real-time -->
                    <div class="card bg-light border-0 p-3 rounded-4 mb-3 text-center">
                        <small class="text-muted d-block mb-1">Pratinjau Tampilan Label di Publik:</small>
                        <div>
                            <span id="create_badge_preview" class="badge bg-success rounded-pill px-3 py-2 fs-6">
                                <i id="create_icon_preview" class="bi bi-images me-1"></i>
                                <span id="create_text_preview">Nama Kategori</span>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_description" class="form-label fw-bold">Deskripsi Ringkas (Opsional)</label>
                        <textarea name="description" id="create_description" rows="2" class="form-control rounded-3" placeholder="Jelaskan jenis kegiatan yang cocok dimasukkan ke kategori ini..."></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="create_sort_order" class="form-label fw-bold">Urutan Tampil (Sort Order)</label>
                            <input type="number" name="sort_order" id="create_sort_order" class="form-control rounded-3" value="0" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="create_is_active">Aktifkan Kategori</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 🟡 MODAL EDIT KATEGORI                     -->
<!-- ========================================== -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editCategoryModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Kategori Galeri
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3" required onkeyup="updatePreviewBadge('edit')">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="edit_slug" class="form-label fw-bold">Slug URL <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="edit_slug" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_group" class="form-label fw-bold">Grup Induk <span class="text-danger">*</span></label>
                            <select name="group" id="edit_group" class="form-select rounded-3" required>
                                @foreach ($groups as $gKey => $gLabel)
                                    <option value="{{ $gKey }}">{{ $gKey }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="edit_icon" class="form-label fw-bold">Ikon Bootstrap</label>
                            <select name="icon" id="edit_icon" class="form-select rounded-3" onchange="updatePreviewBadge('edit')">
                                @foreach ($iconOptions as $iVal => $iName)
                                    <option value="{{ $iVal }}">{{ $iName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_badge_class" class="form-label fw-bold">Warna Badge</label>
                            <select name="badge_class" id="edit_badge_class" class="form-select rounded-3" onchange="updatePreviewBadge('edit')">
                                @foreach ($badgeOptions as $bVal => $bName)
                                    <option value="{{ $bVal }}">{{ $bName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Pratinjau Tampilan Badge Real-time -->
                    <div class="card bg-light border-0 p-3 rounded-4 mb-3 text-center">
                        <small class="text-muted d-block mb-1">Pratinjau Tampilan Label di Publik:</small>
                        <div>
                            <span id="edit_badge_preview" class="badge bg-success rounded-pill px-3 py-2 fs-6">
                                <i id="edit_icon_preview" class="bi bi-images me-1"></i>
                                <span id="edit_text_preview">Nama Kategori</span>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-bold">Deskripsi Ringkas</label>
                        <textarea name="description" id="edit_description" rows="2" class="form-control rounded-3"></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="edit_sort_order" class="form-label fw-bold">Urutan Tampil (Sort Order)</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control rounded-3" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label fw-semibold" for="edit_is_active">Aktifkan Kategori</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check2-circle me-1"></i> Perbarui Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 🔴 MODAL PINDAHKAN KE TONG SAMPAH           -->
<!-- ========================================== -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="deleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="deleteCategoryModalLabel">
                        <i class="bi bi-trash3 me-2"></i> Pindahkan ke Tong Sampah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="display-5 text-danger mb-3"><i class="bi bi-trash3"></i></div>
                    <p class="mb-2">Apakah Anda yakin ingin memindahkan kategori <strong id="deleteCategoryName"></strong> ke Tong Sampah?</p>
                    <div id="deleteWarningText" class="alert alert-warning border-0 rounded-3 small text-start mb-0"></div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-trash3 me-1"></i> Ya, Pindahkan ke Sampah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ⛔ MODAL HAPUS PERMANEN (FORCE DELETE)      -->
<!-- ========================================== -->
<div class="modal fade" id="forceDeleteCategoryModal" tabindex="-1" aria-labelledby="forceDeleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="forceDeleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="forceDeleteCategoryModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Hapus Permanen Kategori
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="display-5 text-danger mb-3"><i class="bi bi-exclamation-octagon-fill"></i></div>
                    <h6 class="fw-bold mb-2">Tindakan ini tidak dapat dibatalkan!</h6>
                    <p class="mb-2">Kategori <strong id="forceDeleteCategoryName"></strong> akan dihapus selamanya dari database.</p>
                    <div class="alert alert-danger border-0 rounded-3 small text-start mb-0">
                        <i class="bi bi-info-circle me-1"></i> Seluruh riwayat data kategori ini akan hilang secara permanen.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-trash3-fill me-1"></i> Hapus Permanen Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function syncSlug(name, targetId) {
        const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '_')
            .replace(/_+/g, '_');
        document.getElementById(targetId).value = slug;
        updatePreviewBadge('create');
    }

    function updatePreviewBadge(prefix) {
        const nameInput = document.getElementById(prefix + '_name');
        const iconSelect = document.getElementById(prefix + '_icon');
        const badgeSelect = document.getElementById(prefix + '_badge_class');

        const badgePreview = document.getElementById(prefix + '_badge_preview');
        const iconPreview = document.getElementById(prefix + '_icon_preview');
        const textPreview = document.getElementById(prefix + '_text_preview');

        if (nameInput && textPreview) {
            textPreview.textContent = nameInput.value.trim() || 'Nama Kategori';
        }

        if (iconSelect && iconPreview) {
            iconPreview.className = 'bi ' + iconSelect.value + ' me-1';
        }

        if (badgeSelect && badgePreview) {
            badgePreview.className = 'badge ' + badgeSelect.value + ' rounded-pill px-3 py-2 fs-6';
        }
    }

    function openEditCategoryModal(cat) {
        const form = document.getElementById('editCategoryForm');
        form.action = "{{ url('admin/gallery-categories') }}/" + cat.id;

        document.getElementById('edit_name').value = cat.name || '';
        document.getElementById('edit_slug').value = cat.slug || '';
        document.getElementById('edit_group').value = cat.group || 'Kategori Utama';
        document.getElementById('edit_icon').value = cat.icon || 'bi-images';
        document.getElementById('edit_badge_class').value = cat.badge_class || 'bg-success';
        document.getElementById('edit_description').value = cat.description || '';
        document.getElementById('edit_sort_order').value = cat.sort_order || 0;
        document.getElementById('edit_is_active').checked = !!cat.is_active;

        updatePreviewBadge('edit');

        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }

    function confirmDeleteCategory(id, name, galleriesCount) {
        const form = document.getElementById('deleteCategoryForm');
        form.action = "{{ url('admin/gallery-categories') }}/" + id;

        document.getElementById('deleteCategoryName').textContent = '"' + name + '"';

        const warningDiv = document.getElementById('deleteWarningText');
        if (galleriesCount > 0) {
            warningDiv.innerHTML = '<i class="bi bi-info-circle me-1"></i> Terdapat <strong>' + galleriesCount + ' foto</strong> yang terhubung ke kategori ini. Foto-foto tersebut tidak akan terhapus, tetapi akan dialihkan ke status <em>Tanpa Kategori</em>.';
            warningDiv.style.display = 'block';
        } else {
            warningDiv.innerHTML = '<i class="bi bi-info-circle me-1"></i> Kategori ini tidak memiliki foto terkait dan dapat dipulihkan kembali sewaktu-waktu dari Tong Sampah.';
            warningDiv.style.display = 'block';
        }

        const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        modal.show();
    }

    function confirmForceDeleteCategory(id, name) {
        const form = document.getElementById('forceDeleteCategoryForm');
        form.action = "{{ url('admin/gallery-categories') }}/" + id + "/force-delete";

        document.getElementById('forceDeleteCategoryName').textContent = '"' + name + '"';

        const modal = new bootstrap.Modal(document.getElementById('forceDeleteCategoryModal'));
        modal.show();
    }
</script>
@endpush
