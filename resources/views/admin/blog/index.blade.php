@extends('layouts.admin')

@section('title', 'Manajemen Blog & Artikel | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-heading">
                <i class="bi bi-newspaper text-success me-2"></i>Manajemen Blog & Artikel Edukasi
            </h3>
            <p class="text-muted small mb-0">Kelola publikasi materi literasi Al-Qur'an, tips parenting islami, dan pengumuman lembaga.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.blog.create') }}" class="btn btn-success rounded-pill px-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Tulis Artikel Baru
            </a>
            <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-success rounded-pill px-3 fw-semibold">
                <i class="bi bi-tags me-1"></i>Kategori
            </a>
            <a href="{{ route('admin.blog.tags.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-hash me-1"></i>Tags
            </a>
            <a href="{{ route('admin.blog.trash') }}" class="btn btn-outline-danger rounded-pill px-3 fw-semibold">
                <i class="bi bi-trash me-1"></i>Tong Sampah
            </a>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary-subtle text-primary-emphasis p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Total Artikel</span>
                        <h4 class="fw-bold mb-0">{{ number_format($analytics['total_articles']) }}</h4>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success-subtle text-success-emphasis p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Artikel Tayang</span>
                        <h4 class="fw-bold mb-0">{{ number_format($analytics['published_count']) }}</h4>
                    </div>
                    <i class="bi bi-check2-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info-subtle text-info-emphasis p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Total Dibaca (Views)</span>
                        <h4 class="fw-bold mb-0">{{ number_format($analytics['total_views']) }}</h4>
                    </div>
                    <i class="bi bi-eye fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning-subtle text-warning-emphasis p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Total Dibagikan</span>
                        <h4 class="fw-bold mb-0">{{ number_format($analytics['total_shares']) }}</h4>
                    </div>
                    <i class="bi bi-share fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-stars text-success me-2"></i>Daftar Artikel Blog</h5>
            
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.blog.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm rounded-pill border-secondary-subtle" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                </select>
                <select name="category_id" class="form-select form-select-sm rounded-pill border-secondary-subtle" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @if(request()->filled('status') || request()->filled('category_id'))
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                @endif
            </form>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <x-datatable id="blogArticlesTable" data-export="true" data-no-paging="true">
                <thead>
                    <tr>
                        <th style="width: 70px;">Cover</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th class="text-center">Statistik</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Featured</th>
                        <th class="text-end no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" 
                                     class="rounded-3 object-fit-cover shadow-sm" width="60" height="45">
                            </td>
                            <td>
                                <a href="{{ route('admin.blog.edit', $article->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ Str::limit($article->title, 55) }}
                                </a>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $article->published_date }}
                                    <span class="ms-2"><i class="bi bi-clock me-1"></i>{{ $article->reading_time_label }}</span>
                                </div>
                            </td>
                            <td>
                                @if($article->category)
                                    <span class="badge rounded-pill" style="background-color: {{ $article->category->color }}20; color: {{ $article->category->color }}; border: 1px solid {{ $article->category->color }}40;">
                                        <i class="bi {{ $article->category->icon }} me-1"></i>{{ $article->category->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Tanpa Kategori</span>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-semibold text-secondary">{{ $article->author_name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border me-1" title="Views">
                                    <i class="bi bi-eye text-primary me-1"></i>{{ number_format($article->views_count) }}
                                </span>
                                <span class="badge bg-light text-dark border" title="Shares">
                                    <i class="bi bi-share text-success me-1"></i>{{ number_format($article->shares_count) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.blog.toggle-status', $article->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($article->status === 'published')
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-0" title="Klik untuk ubah ke Draft">
                                            <i class="bi bi-check-circle me-1"></i>Published
                                        </button>
                                    @elseif($article->status === 'scheduled')
                                        <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-2 py-0" title="Terjadwal">
                                            <i class="bi bi-clock-history me-1"></i>Scheduled
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-secondary rounded-pill px-2 py-0" title="Klik untuk Publish">
                                            <i class="bi bi-file-earmark me-1"></i>Draft
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.blog.toggle-featured', $article->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Toggle Featured">
                                        @if($article->is_featured)
                                            <i class="bi bi-star-fill fs-5 text-warning"></i>
                                        @else
                                            <i class="bi bi-star fs-5 text-muted opacity-50"></i>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="btn btn-outline-info rounded-start-pill" title="Pratinjau Publik">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ route('admin.blog.edit', $article->id) }}" class="btn btn-outline-primary" title="Edit Artikel">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.blog.destroy', $article->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pindahkan artikel ini ke tong sampah?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-end-pill" title="Hapus (Soft Delete)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada artikel blog yang ditemukan.
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
