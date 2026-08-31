@extends('layouts.admin')

@section('title', 'Tong Sampah Artikel | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-heading">
                <i class="bi bi-trash text-danger me-2"></i>Tong Sampah Artikel Blog
            </h3>
            <p class="text-muted small mb-0">Artikel yang dihapus sementara (Soft Deleted) dapat dipulihkan atau dihapus secara permanen.</p>
        </div>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Utama
        </a>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <x-datatable id="blogTrashTable" data-export="true" data-no-paging="true">
                <thead>
                    <tr>
                        <th style="width: 70px;">Cover</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Tanggal Dihapus</th>
                        <th class="text-end no-sort">Aksi Pemulihan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" 
                                     class="rounded-3 object-fit-cover shadow-sm opacity-75" width="60" height="45">
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $article->title }}</span>
                                <div class="small text-muted">Penulis: {{ $article->author_name }}</div>
                            </td>
                            <td>
                                @if($article->category)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                        {{ $article->category->name }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill">Tanpa Kategori</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">
                                    <i class="bi bi-clock-history me-1"></i>{{ $article->deleted_at->translatedFormat('d F Y H:i') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('admin.blog.restore', $article->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success rounded-start-pill" title="Pulihkan Artikel">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.blog.force-delete', $article->id) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('HAPUS PERMANEN? Artikel dan gambar sampul akan dihapus selamanya dari server!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-end-pill" title="Hapus Permanen">
                                            <i class="bi bi-x-circle me-1"></i>Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-trash3 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Tong sampah kosong. Tidak ada artikel yang dihapus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-datatable>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
