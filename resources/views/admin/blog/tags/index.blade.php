@extends('layouts.admin')

@section('title', 'Tags Blog | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-heading">
                <i class="bi bi-hash text-success me-2"></i>Tags Taksonomi Blog
            </h3>
            <p class="text-muted small mb-0">Kata kunci label untuk fitur rekomendasi artikel terkait berbasis kesamaan topik.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Artikel
            </a>
            <button class="btn btn-success rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createTagModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah Tag Baru
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

            <x-datatable id="blogTagsTable" data-export="true">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Nama Tag</th>
                        <th>Slug</th>
                        <th class="text-center">Jumlah Artikel Tertaut</th>
                        <th class="text-end no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $tag)
                        <tr>
                            <td class="text-muted">{{ $tag->id }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-2 fs-6 fw-semibold">
                                    #{{ $tag->name }}
                                </span>
                            </td>
                            <td><code>{{ $tag->slug }}</code></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">
                                    {{ $tag->articles_count }} Artikel
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary rounded-start-pill" 
                                            data-bs-toggle="modal" data-bs-target="#editTagModal{{ $tag->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.blog.tags.destroy', $tag->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus tag ini? Tag akan dilepaskan dari artikel tertaut tanpa menghapus artikelnya.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-end-pill">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Edit -->
                                <div class="modal fade text-start" id="editTagModal{{ $tag->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <form action="{{ route('admin.blog.tags.update', $tag->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil text-success me-2"></i>Edit Tag</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nama Tag <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control rounded-3" value="{{ $tag->name }}" required>
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
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada tag taksonomi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-datatable>
        </div>
    </div>
</div>

<!-- Modal Create Tag -->
<div class="modal fade" id="createTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.blog.tags.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-hash text-success me-2"></i>Tambah Tag Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tag <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Tips Mengaji" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill fw-bold">Tambah Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
