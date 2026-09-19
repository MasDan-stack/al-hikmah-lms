@extends('layouts.landing')

@section('title', isset($category) ? "Blog - Kategori {$category->name} | AL-HIKMAH" : (isset($tag) ? "Blog - Tag #{$tag->name} | AL-HIKMAH" : "Blog & Artikel Edukasi Islami | AL-HIKMAH LMS"))
@section('meta_description', 'Artikel dan panduan edukasi belajar Al-Qur\'an, tips mendampingi anak mengaji, metode Tahsin dan Tahfidz di AL-HIKMAH LMS.')

@section('content')
<!-- ============================================ -->
<!-- 1. PAGE HERO HEADER - EDITORIAL MINIMALIST -->
<!-- ============================================ -->
<section class="editorial-page-header text-center" aria-label="Header Blog AL-HIKMAH">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div data-reveal>
                    <div class="section-badge mx-auto mb-3">
                        <i class="bi bi-journal-bookmark-fill me-1"></i> Literasi &amp; Edukasi Qur'ani
                    </div>
                    <h1 class="editorial-title mb-3">
                        @if(isset($category))
                            Kategori: <span class="text-emerald-deep">{{ $category->name }}</span>
                        @elseif(isset($tag))
                            Tagar: <span class="text-emerald-deep">#{{ $tag->name }}</span>
                        @elseif(request('search'))
                            Pencarian: <span class="text-emerald-deep">"{{ request('search') }}"</span>
                        @else
                            Wawasan &amp; Edukasi <span class="text-emerald-deep">Qur'ani</span>
                        @endif
                    </h1>
                    <p class="editorial-subtitle mx-auto" style="max-width: 620px;">
                        Panduan belajar Al-Qur'an, adab dan tips mendampingi ananda mengaji, metode tahsin dan tahfidz, serta wawasan keislaman terpercaya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 2. BLOG CONTENT AREA -->
<!-- ============================================ -->
<section class="py-5" aria-label="Daftar Artikel Blog">
    <div class="container">
        @if(request('search') || isset($category) || isset($tag))
            <div class="d-flex align-items-center justify-content-between mb-4 p-3 rounded-3 bg-body-tertiary border" data-reveal>
                <div class="small">
                    <i class="bi bi-funnel me-1 text-emerald-deep"></i>
                    <span class="text-secondary fw-semibold">Filter Aktif:</span>
                    @if(request('search'))
                        <span class="badge bg-light text-dark border ms-1">"{{ request('search') }}"</span>
                    @endif
                    @if(isset($category))
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">{{ $category->name }}</span>
                    @endif
                    @if(isset($tag))
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1">#{{ $tag->name }}</span>
                    @endif
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </a>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column: Articles List -->
            <div class="col-lg-8">
                <div>
                    @forelse($articles as $index => $article)
                        <article class="blog-card mb-4" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="row g-0 align-items-stretch">
                                <div class="col-md-5">
                                    <div class="blog-card-img-wrap">
                                        <a href="{{ route('blog.show', $article->slug) }}" class="d-block h-100">
                                            <img src="{{ $article->cover_url }}" alt="{{ $article->title }}"
                                                 class="w-100 h-100 object-fit-cover" style="min-height: 220px;"
                                                 onerror="this.src='{{ asset('assets/img/' . (($index % 3) + 1) . '.jpg') }}'">
                                        </a>
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="badge bg-white text-dark border shadow-sm px-2.5 py-1.5 fw-semibold" style="font-size: 0.76rem;">
                                                <i class="bi bi-calendar3 me-1 text-emerald-deep"></i>
                                                {{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : $article->created_at->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 d-flex flex-column p-4">
                                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                        @if($article->category)
                                            <a href="{{ route('blog.category', $article->category->slug) }}" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none py-1.5 px-2.5">
                                                <i class="bi bi-folder2-open me-1"></i> {{ $article->category->name }}
                                            </a>
                                        @endif
                                        <span class="badge bg-light text-secondary border py-1.5 px-2.5">
                                            <i class="bi bi-clock me-1 text-emerald-deep"></i> {{ $article->reading_time_label }}
                                        </span>
                                    </div>

                                    <h2 class="fs-5 fw-bold text-heading mb-2 lh-snug">
                                        <a href="{{ route('blog.show', $article->slug) }}" class="text-heading text-decoration-none hover-emerald">
                                            {{ $article->title }}
                                        </a>
                                    </h2>

                                    <p class="text-secondary small flex-grow-1 mb-3" style="line-height: 1.65;">
                                        {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 130) }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <i class="bi bi-person-circle text-emerald-deep"></i>
                                            <span>{{ $article->author_name }}</span>
                                            <span>•</span>
                                            <span><i class="bi bi-eye me-1 text-emerald-deep"></i> {{ number_format($article->views_count) }}</span>
                                        </div>
                                        <a href="{{ route('blog.show', $article->slug) }}" class="small fw-bold text-emerald-deep text-decoration-none d-inline-flex align-items-center gap-1">
                                            Baca <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="blog-sidebar-widget text-center p-5" data-reveal>
                            <div class="editorial-icon-badge mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.6rem;">
                                <i class="bi bi-journal-x"></i>
                            </div>
                            <h2 class="fs-4 fw-bold text-heading mb-2">Belum Ada Artikel Ditemukan</h2>
                            <p class="text-muted small mb-4 mx-auto" style="max-width: 460px;">
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
                            <div>
                                <a href="{{ route('blog.index') }}" class="btn-editorial-primary px-4">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Semua Artikel
                                </a>
                            </div>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="d-flex justify-content-center mt-5">
                            <nav aria-label="Navigasi Halaman Blog">
                                <ul class="pagination pagination-custom">
                                    {{-- Previous Page Link --}}
                                    @if ($articles->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $articles->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a></li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                                        @if ($page == $articles->currentPage())
                                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($articles->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $articles->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Sidebar Widgets -->
            <div class="col-lg-4">
                <!-- 1. Search Widget -->
                <aside class="blog-sidebar-widget" data-reveal>
                    <h3 class="d-flex align-items-center gap-2">
                        <i class="bi bi-search text-emerald-deep"></i> Cari Artikel
                    </h3>
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="input-group-editorial">
                            <span class="field-icon"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari topik atau kata kunci..."
                                   value="{{ request('search') }}" required>
                            <button class="btn-field-action" type="submit" aria-label="Cari artikel">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </aside>

                <!-- 2. Post Category Widget -->
                <aside class="blog-sidebar-widget" data-reveal data-reveal-delay="100">
                    <h3 class="d-flex align-items-center gap-2">
                        <i class="bi bi-folder2-open text-emerald-deep"></i> Kategori Artikel
                    </h3>
                    <div class="d-flex flex-column gap-1">
                        <a href="{{ route('blog.index') }}" 
                           class="blog-cat-item {{ !isset($category) && !request('search') && !isset($tag) ? 'active' : '' }}">
                            <span><i class="bi bi-grid me-2"></i>Semua Kategori</span>
                            <span class="badge bg-light text-secondary border">{{ $categories->sum('published_articles_count') }}</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('blog.category', $cat->slug) }}" 
                               class="blog-cat-item {{ isset($category) && $category->id === $cat->id ? 'active' : '' }}">
                                <span><i class="bi {{ $cat->icon ?? 'bi-bookmark-check' }} me-2"></i>{{ $cat->name }}</span>
                                <span class="badge bg-light text-secondary border">{{ $cat->published_articles_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </aside>

                <!-- 3. Popular Post Widget (Recent) -->
                @if(isset($recentArticles) && $recentArticles->count() > 0)
                    <aside class="blog-sidebar-widget" data-reveal data-reveal-delay="200">
                        <h3 class="d-flex align-items-center gap-2">
                            <i class="bi bi-journal-text text-emerald-deep"></i> Artikel Terbaru
                        </h3>
                        <div class="d-flex flex-column gap-3">
                            @foreach($recentArticles as $recent)
                                <div class="d-flex gap-3 align-items-center">
                                    <a href="{{ route('blog.show', $recent->slug) }}" class="flex-shrink-0">
                                        <img src="{{ $recent->cover_url }}" alt="{{ $recent->title }}"
                                             class="rounded-3 object-fit-cover shadow-sm" style="width: 68px; height: 68px;"
                                             onerror="this.src='{{ asset('assets/img/1.jpg') }}'">
                                    </a>
                                    <div class="flex-grow-1 min-w-0">
                                        <h4 class="fs-6 fw-semibold mb-1 lh-sm" style="font-size: 0.88rem !important;">
                                            <a href="{{ route('blog.show', $recent->slug) }}" class="text-heading text-decoration-none hover-emerald">
                                                {{ Str::limit($recent->title, 52) }}
                                            </a>
                                        </h4>
                                        <div class="small text-muted" style="font-size: 0.78rem;">
                                            <i class="bi bi-calendar3 me-1 text-emerald-deep"></i>{{ $recent->published_date }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </aside>
                @endif

                <!-- 4. Tag Cloud Widget -->
                @if($tags->count() > 0)
                    <aside class="blog-sidebar-widget" data-reveal data-reveal-delay="300">
                        <h3 class="d-flex align-items-center gap-2">
                            <i class="bi bi-tags text-emerald-deep"></i> Tag Populer
                        </h3>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $t)
                                <a href="{{ route('blog.tag', $t->slug) }}" 
                                   class="blog-tag-pill {{ isset($tag) && $tag->id === $t->id ? 'active' : '' }}">
                                    #{{ $t->name }}
                                </a>
                            @endforeach
                        </div>
                    </aside>
                @endif

                <!-- 5. Consultation & CTA Widget -->
                <aside class="blog-sidebar-widget text-center p-4 border-2" style="border-color: rgba(6, 78, 59, 0.2) !important; background: linear-gradient(135deg, rgba(6, 78, 59, 0.04) 0%, rgba(4, 120, 87, 0.02) 100%);" data-reveal data-reveal-delay="400">
                    <div class="editorial-icon-badge mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.35rem;">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <h3 class="fs-6 fw-bold text-heading mb-2">Konsultasi Belajar Santri</h3>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;">
                        Ingin berdiskusi mengenai kurikulum atau penempatan level belajar privat yang tepat untuk ananda?
                    </p>
                    <a href="https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20ingin%20berkonsultasi%20mengenai%20program%20belajar%20Al-Hikmah" 
                       target="_blank" rel="noopener" class="btn-editorial-primary w-100 shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> Tanya Konselor via WhatsApp
                    </a>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
