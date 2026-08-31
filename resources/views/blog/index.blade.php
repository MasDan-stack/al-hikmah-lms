@extends('layouts.landing')

@section('title', isset($category) ? "Blog - Kategori {$category->name} | AL-HIKMAH" : (isset($tag) ? "Blog - Tag #{$tag->name} | AL-HIKMAH" : "Blog & Artikel Edukasi Islami | AL-HIKMAH LMS"))

@section('content')
<!-- ============================================ -->
<!-- 1. ETRAIN BREADCRUMB HEADER -->
<!-- ============================================ -->
<section class="breadcrumb_bg" aria-label="Header Blog AL-HIKMAH">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb_iner_item" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-journal-richtext"></i> Literasi &amp; Edukasi Islami</div>
                    <h2>
                        @if(isset($category))
                            Kategori: <span class="text-gradient">{{ $category->name }}</span>
                        @elseif(isset($tag))
                            Tagar: <span class="text-gradient">#{{ $tag->name }}</span>
                        @elseif(request('search'))
                            Pencarian: <span class="text-gradient">"{{ request('search') }}"</span>
                        @else
                            Blog &amp; Artikel <span class="text-gradient">AL-HIKMAH</span>
                        @endif
                    </h2>
                    <p>Panduan belajar Al-Qur'an, tips mendampingi anak mengaji, metode Tahsin/Tahfidz, dan wawasan keislaman terkini.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 2. ETRAIN BLOG AREA -->
<!-- ============================================ -->
<section class="blog_area py-5" aria-label="Daftar Artikel Blog">
    <div class="container">
        <div class="row">
            <!-- Left Column: Articles List -->
            <div class="col-lg-8 mb-5 mb-lg-0">
                <div class="blog_left_sidebar">
                    @forelse($articles as $index => $article)
                        <article class="blog_item" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="blog_item_img">
                                <img class="card-img rounded-0" src="{{ $article->cover_url }}" alt="{{ $article->title }}"
                                     onerror="this.src='{{ asset('assets/img/' . (($index % 3) + 1) . '.jpg') }}'">
                                <a href="{{ route('blog.show', $article->slug) }}" class="blog_item_date">
                                    <h3>{{ $article->published_at ? $article->published_at->format('d') : $article->created_at->format('d') }}</h3>
                                    <p>{{ $article->published_at ? $article->published_at->translatedFormat('M') : $article->created_at->translatedFormat('M') }}</p>
                                </a>
                            </div>

                            <div class="blog_details">
                                <a class="d-inline-block" href="{{ route('blog.show', $article->slug) }}">
                                    <h2>{{ $article->title }}</h2>
                                </a>
                                <p>{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 180) }}</p>
                                <ul class="blog-info-link">
                                    @if($article->category)
                                        <li><a href="{{ route('blog.category', $article->category->slug) }}"><i class="bi bi-folder2-open"></i> {{ $article->category->name }}</a></li>
                                    @endif
                                    <li><a href="{{ route('blog.show', $article->slug) }}"><i class="bi bi-person"></i> {{ $article->author_name }}</a></li>
                                    <li><a href="{{ route('blog.show', $article->slug) }}"><i class="bi bi-clock"></i> {{ $article->reading_time_label }}</a></li>
                                    <li><a href="{{ route('blog.show', $article->slug) }}"><i class="bi bi-eye"></i> {{ number_format($article->views_count) }} Tayangan</a></li>
                                </ul>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-5 bg-white rounded-4 p-4 border" style="border-color: var(--border-color) !important;">
                            <div class="mb-3 text-muted">
                                <i class="bi bi-journal-x display-3"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Belum Ada Artikel Ditemukan</h4>
                            <p class="text-muted mb-4">
                                @if(request('search'))
                                    Tidak ada artikel yang cocok dengan kata kunci <strong>"{{ request('search') }}"</strong>.
                                @elseif(isset($category))
                                    Belum ada artikel yang dipublikasikan dalam kategori <strong>"{{ $category->name }}"</strong>.
                                @elseif(isset($tag))
                                    Belum ada artikel dengan tagar <strong>"#{{ $tag->name }}"</strong>.
                                @else
                                    Nantikan artikel edukatif menarik dari kami segera.
                                @endif
                            </p>
                            <a href="{{ route('blog.index') }}" class="btn_1">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Semua Artikel
                            </a>
                        </div>
                    @endforelse

                    <!-- Pagination (Etrain Style) -->
                    @if($articles->hasPages())
                        <nav class="blog-pagination justify-content-center d-flex mt-5" aria-label="Navigasi Halaman Blog">
                            <ul class="pagination">
                                {{-- Tombol Previous --}}
                                @if ($articles->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link" aria-label="Sebelumnya">
                                            <i class="bi bi-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a href="{{ $articles->previousPageUrl() }}" class="page-link" rel="prev" aria-label="Sebelumnya">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Nomor Halaman --}}
                                @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                                    @if ($page == $articles->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Tombol Next --}}
                                @if ($articles->hasMorePages())
                                    <li class="page-item">
                                        <a href="{{ $articles->nextPageUrl() }}" class="page-link" rel="next" aria-label="Berikutnya">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link" aria-label="Berikutnya">
                                            <i class="bi bi-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>

            <!-- Right Column: Sidebar Widgets -->
            <div class="col-lg-4">
                <div class="blog_right_sidebar">
                    <!-- 1. Search Widget -->
                    <aside class="single_sidebar_widget search_widget" data-reveal>
                        <form action="{{ route('blog.index') }}" method="GET">
                            <div class="form-group mb-0">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari kata kunci..."
                                           value="{{ request('search') }}" required>
                                    <div class="input-group-append">
                                        <button class="btn btn_1 py-2 px-3" type="submit" aria-label="Cari"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </aside>

                    <!-- 2. Post Category Widget -->
                    <aside class="single_sidebar_widget post_category_widget" data-reveal data-reveal-delay="100">
                        <h4 class="widget_title">Kategori Artikel</h4>
                        <ul class="list cat-list">
                            <li class="{{ !isset($category) && !request('search') && !isset($tag) ? 'active' : '' }}">
                                <a href="{{ route('blog.index') }}" class="d-flex">
                                    <p class="mb-0"><i class="bi bi-grid-fill me-2 text-success"></i>Semua Kategori</p>
                                    <span class="badge">{{ $categories->sum('published_articles_count') }}</span>
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li class="{{ isset($category) && $category->id === $cat->id ? 'active' : '' }}">
                                    <a href="{{ route('blog.category', $cat->slug) }}" class="d-flex">
                                        <p class="mb-0"><i class="bi {{ $cat->icon ?? 'bi-bookmark-check' }} me-2 text-success"></i>{{ $cat->name }}</p>
                                        <span class="badge">{{ $cat->published_articles_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>

                    <!-- 3. Popular Post Widget (Recent) -->
                    @if(isset($recentArticles) && $recentArticles->count() > 0)
                        <aside class="single_sidebar_widget popular_post_widget" data-reveal data-reveal-delay="200">
                            <h3 class="widget_title">Artikel Terbaru</h3>
                            @foreach($recentArticles as $recent)
                                <div class="media post_item">
                                    <img src="{{ $recent->cover_url }}" alt="{{ $recent->title }}"
                                         onerror="this.src='{{ asset('assets/img/1.jpg') }}'">
                                    <div class="media-body">
                                        <a href="{{ route('blog.show', $recent->slug) }}">
                                            <h3>{{ Str::limit($recent->title, 48) }}</h3>
                                        </a>
                                        <p><i class="bi bi-calendar3 me-1"></i>{{ $recent->published_date }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </aside>
                    @endif

                    <!-- 4. Tag Cloud Widget -->
                    @if($tags->count() > 0)
                        <aside class="single_sidebar_widget tag_cloud_widget" data-reveal data-reveal-delay="300">
                            <h4 class="widget_title">Tag Populer</h4>
                            <ul class="list">
                                @foreach($tags as $t)
                                    <li>
                                        <a href="{{ route('blog.tag', $t->slug) }}" class="{{ isset($tag) && $tag->id === $t->id ? 'active' : '' }}">
                                            #{{ $t->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                    @endif

                    <!-- 5. Consultation & CTA Widget -->
                    <aside class="single_sidebar_widget text-center" data-reveal data-reveal-delay="400" style="background: linear-gradient(135deg, var(--bg-primary) 0%, var(--primary-lighter) 100%);">
                        <div class="p-2 bg-success text-white rounded-circle d-inline-flex mb-3" style="width: 50px; height: 50px; align-items: center; justify-content: center;">
                            <i class="bi bi-whatsapp fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Konsultasi Belajar</h4>
                        <p class="text-muted small mb-4">Ingin berkonsultasi mengenai program yang cocok untuk putra-putri Anda? Hubungi tim kami sekarang.</p>
                        <a href="https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20ingin%20berkonsultasi%20mengenai%20program%20belajar%20Al-Hikmah" 
                           target="_blank" rel="noopener" class="btn_1 w-100">
                            <i class="bi bi-whatsapp me-1"></i> Tanya via WhatsApp
                        </a>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
