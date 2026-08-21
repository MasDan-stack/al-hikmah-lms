@extends('layouts.admin')

@section('title', 'Manajemen Galeri Kegiatan')
@section('header', 'Galeri & Dokumentasi Kegiatan')
@section('subheader', 'Kelola foto kegiatan, momen belajar santri, dan foto unggulan hero slider.')

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
                <span class="fw-bold">Gagal Memproses Data! Periksa kesalahan berikut:</span>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistik Ringkas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-success-subtle text-success fs-4">
                        <i class="bi bi-images"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Foto Aktif</div>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary fs-4">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Dipublikasikan</div>
                        <h4 class="fw-bold mb-0">{{ $stats['published'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-card-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning fs-4">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Hero Slideshow</div>
                        <h4 class="fw-bold mb-0">{{ $stats['featured'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.galleries.index', ['status' => 'trashed']) }}" class="text-decoration-none">
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
                <a class="nav-link rounded-pill px-4 {{ !$isTrashedView ? 'active bg-success' : 'bg-light text-dark' }}" href="{{ route('admin.galleries.index') }}">
                    <i class="bi bi-collection me-1"></i> Data Aktif ({{ $stats['total'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $isTrashedView ? 'active bg-danger text-white' : 'bg-light text-dark' }}" href="{{ route('admin.galleries.index', ['status' => 'trashed']) }}">
                    <i class="bi bi-trash3 me-1"></i> Tong Sampah ({{ $stats['trashed'] }})
                </a>
            </li>
        </ul>

        @if (!$isTrashedView)
            <div class="d-flex gap-2">
                <a href="{{ route('admin.gallery-categories.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-tags me-1"></i> Kelola Kategori ({{ $stats['categories_count'] }})
                </a>
                <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary-custom rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Foto Baru
                </a>
            </div>
        @else
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Galeri Aktif
            </a>
        @endif
    </div>

    <!-- Filter & Action Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.galleries.index') }}" class="row g-2 align-items-center">
                @if ($isTrashedView)
                    <input type="hidden" name="status" value="trashed">
                @endif
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari judul/lokasi/deskripsi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="category" class="form-select">
                        <option value="all">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->group }} &raquo; {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="program_id" class="form-select">
                        <option value="all">Semua Program</option>
                        @foreach ($programs as $prog)
                            <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if (!$isTrashedView)
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publish</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                @endif
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'category', 'program_id']))
                        <a href="{{ route('admin.galleries.index', $isTrashedView ? ['status' => 'trashed'] : []) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Grid / Tabel Dokumentasi -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th>Judul & Takarir</th>
                        <th>Kategori & Program</th>
                        <th>Tanggal & Lokasi</th>
                        @if (!$isTrashedView)
                            <th class="text-center">Hero Slider</th>
                            <th class="text-center">Status</th>
                        @else
                            <th class="text-center">Dihapus Pada</th>
                        @endif
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="sortableGalleryList">
                    @forelse ($galleries as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>
                                <img src="{{ $item->asset_url }}" alt="{{ $item->title }}" class="rounded-3 object-fit-cover shadow-sm" width="70" height="50">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 280px;">{{ $item->caption ?? Str::limit($item->description, 50) }}</small>
                                @if (!empty($item->tags))
                                    <div class="mt-1">
                                        @foreach ($item->tags as $t)
                                            <span class="badge bg-light text-secondary border me-1" style="font-size: 0.7rem;">#{{ $t }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $item->category_meta['badge_class'] }} mb-1">
                                    <i class="bi {{ $item->category_meta['icon'] }} me-1"></i> {{ $item->category_label }}
                                </span>
                                @if ($item->program)
                                    <div class="small text-muted"><i class="bi bi-journal-bookmark text-success me-1"></i>{{ $item->program->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold"><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $item->formatted_date }}</div>
                                @if ($item->location)
                                    <div class="small text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $item->location }}</div>
                                @endif
                            </td>
                            @if (!$isTrashedView)
                                <td class="text-center">
                                    <form action="{{ route('admin.galleries.toggle', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="type" value="featured">
                                        <button type="submit" class="btn btn-sm {{ $item->is_featured ? 'btn-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-2 py-0" title="Klik untuk ubah status hero slider">
                                            <i class="bi {{ $item->is_featured ? 'bi-star-fill' : 'bi-star' }}"></i> {{ $item->is_featured ? 'Ya' : 'Tidak' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.galleries.toggle', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="type" value="publish">
                                        <button type="submit" class="btn btn-sm {{ $item->is_published ? 'btn-success' : 'btn-secondary' }} rounded-pill px-2 py-0">
                                            <i class="bi {{ $item->is_published ? 'bi-check-circle' : 'bi-eye-slash' }}"></i> {{ $item->is_published ? 'Publish' : 'Draft' }}
                                        </button>
                                    </form>
                                </td>
                            @else
                                <td class="text-center text-danger small">
                                    {{ $item->deleted_at->translatedFormat('d M Y H:i') }}
                                </td>
                            @endif
                            <td class="text-end">
                                @if (!$isTrashedView)
                                    <a href="{{ route('admin.galleries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit Data"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}" title="Pindahkan ke Sampah"><i class="bi bi-trash"></i></button>

                                    <!-- Modal Soft Delete -->
                                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 text-start">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-trash3 me-2"></i>Pindahkan ke Sampah</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <p class="mb-1">Pindahkan foto dokumentasi ke Tong Sampah:</p>
                                                    <p class="fw-bold text-dark">"{{ $item->title }}"?</p>
                                                    <small class="text-muted">Data akan disembunyikan dari publik dan dapat dipulihkan kapan saja.</small>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('admin.galleries.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Pindahkan ke Sampah</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Aksi Tong Sampah (Restore & Force Delete) -->
                                    <form action="{{ route('admin.galleries.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-1" title="Pulihkan Foto">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" data-bs-toggle="modal" data-bs-target="#forceDeleteModal{{ $item->id }}" title="Hapus Permanen">
                                        <i class="bi bi-x-circle me-1"></i> Hapus
                                    </button>

                                    <!-- Modal Force Delete -->
                                    <div class="modal fade" id="forceDeleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 text-start">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Permanen</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <p class="mb-1 text-danger fw-semibold">Peringatan: Tindakan ini tidak dapat dibatalkan!</p>
                                                    <p class="mb-1">Foto dokumentasi dan berkas fisik di server akan dimusnahkan:</p>
                                                    <p class="fw-bold text-dark">"{{ $item->title }}"</p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('admin.galleries.force-delete', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Hapus Permanen</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-images fs-1 d-block mb-2 opacity-50"></i>
                                {{ $isTrashedView ? 'Tong sampah galeri saat ini kosong.' : 'Belum ada dokumentasi galeri yang sesuai kriteria.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($galleries->hasPages())
            <div class="card-footer bg-transparent border-0 p-3">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
