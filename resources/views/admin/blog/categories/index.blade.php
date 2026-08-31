@extends('layouts.admin')

@section('title', 'Kategori Blog | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-heading">
                <i class="bi bi-tags text-success me-2"></i>Kategori Blog & Artikel
            </h3>
            <p class="text-muted small mb-0">Pengelompokan materi literasi untuk memudahkan navigasi pembaca.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Artikel
            </a>
            <button class="btn btn-success rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <x-datatable id="blogCategoriesTable" data-export="true">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Artikel</th>
                        <th class="text-center">Status</th>
                        <th class="text-end no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $cat->sort_order }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 fs-6" style="background-color: {{ $cat->color }}20; color: {{ $cat->color }}; border: 1px solid {{ $cat->color }}40;">
                                    <i class="bi {{ $cat->icon }} me-1"></i>{{ $cat->name }}
                                </span>
                            </td>
                            <td><code>{{ $cat->slug }}</code></td>
                            <td><span class="small text-muted">{{ Str::limit($cat->description, 60) ?: '-' }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">
                                    {{ $cat->articles_count }} Artikel
                                </span>
                            </td>
                            <td class="text-center">
                                @if($cat->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary rounded-start-pill" 
                                            data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.blog.categories.destroy', $cat->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-end-pill" {{ $cat->articles_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Edit -->
                                <div class="modal fade text-start" id="editCategoryModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <form action="{{ route('admin.blog.categories.update', $cat->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil text-success me-2"></i>Edit Kategori</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control rounded-3" value="{{ $cat->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Deskripsi</label>
                                                        <textarea name="description" class="form-control rounded-3" rows="2">{{ $cat->description }}</textarea>
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Ikon Bootstrap</label>
                                                            <input type="text" name="icon" class="form-control rounded-3" value="{{ $cat->icon }}" placeholder="bi-book">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Warna Label (Hex)</label>
                                                            <input type="color" name="color" class="form-control form-control-color w-100 rounded-3" value="{{ $cat->color }}">
                                                        </div>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Urutan Tampil</label>
                                                            <input type="number" name="sort_order" class="form-control rounded-3" value="{{ $cat->sort_order }}">
                                                        </div>
                                                        <div class="col-6 d-flex align-items-end">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_active_{{ $cat->id }}" {{ $cat->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label small fw-semibold" for="edit_active_{{ $cat->id }}">Status Aktif</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success rounded-pill fw-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada kategori blog.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-datatable>
        </div>
    </div>
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.blog.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-folder-plus text-success me-2"></i>Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Metodologi Tahsin" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="Deskripsi singkat kategori..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ikon Bootstrap</label>
                            <input type="text" name="icon" class="form-control rounded-3" value="bi-tag" placeholder="bi-book">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Warna Label</label>
                            <input type="color" name="color" class="form-control form-control-color w-100 rounded-3" value="#0d7a3e">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" class="form-control rounded-3" value="0">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="create_is_active" checked>
                                <label class="form-check-label small fw-semibold" for="create_is_active">Status Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill fw-bold">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
