@extends('layouts.landing')

@section('title', $article->title . ' | Blog AL-HIKMAH')

@push('styles')
<!-- SEO Meta Tags & OpenGraph -->
<meta name="description" content="{{ Str::limit($article->excerpt ?? strip_tags($article->content), 160) }}">
<meta property="og:title" content="{{ $article->title }}">
<meta property="og:description" content="{{ Str::limit($article->excerpt ?? strip_tags($article->content), 160) }}">
<meta property="og:image" content="{{ $article->cover_url }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="article">
<meta name="twitter:card" content="summary_large_image">

<!-- JSON-LD Structured Data for Google Indexing -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BlogPosting",
  "headline": "{{ $article->title }}",
  "image": ["{{ $article->cover_url }}"],
  "datePublished": "{{ $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String() }}",
  "dateModified": "{{ $article->updated_at->toIso8601String() }}",
  "author": {
    "@@type": "Person",
    "name": "{{ $article->author_name }}"
  },
  "publisher": {
    "@@type": "Organization",
    "name": "AL-HIKMAH LMS",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ asset('assets/img/logo/logo.png') }}"
    }
  },
  "description": "{{ Str::limit($article->excerpt ?? strip_tags($article->content), 160) }}"
}
</script>
@endpush

@section('content')
<!-- ============================================ -->
<!-- 1. ETRAIN BREADCRUMB HEADER -->
<!-- ============================================ -->
<section class="breadcrumb_bg" aria-label="Header Artikel AL-HIKMAH">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb_iner_item" data-reveal>
                    <div class="section-badge mx-auto mb-2">
                        @if($article->category)
                            <i class="bi {{ $article->category->icon ?? 'bi-journal-richtext' }}"></i> {{ $article->category->name }}
                        @else
                            <i class="bi bi-journal-richtext"></i> Literasi Islami
                        @endif
                    </div>
                    <h2 class="px-lg-5">{{ $article->title }}</h2>
                    <p>
                        <a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a>
                        <span class="mx-2">•</span>
                        <a href="{{ route('blog.index') }}" class="text-decoration-none text-muted">Blog</a>
                        @if($article->category)
                            <span class="mx-2">•</span>
                            <a href="{{ route('blog.category', $article->category->slug) }}" class="text-decoration-none text-success fw-semibold">{{ $article->category->name }}</a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 2. ETRAIN SINGLE POST AREA -->
<!-- ============================================ -->
<section class="blog_area single-post-area py-5" aria-label="Konten Artikel Blog">
    <div class="container">
        <div class="row">
            <!-- Left Column: Single Post & Details -->
            <div class="col-lg-8 posts-list">
                <div class="single-post" data-reveal>
                    <!-- Featured Image -->
                    <div class="feature-img">
                        <img class="img-fluid" src="{{ $article->cover_url }}" alt="{{ $article->title }}"
                             onerror="this.src='{{ asset('assets/img/1.jpg') }}'">
                        @if($article->cover_caption)
                            <p class="text-muted small fst-italic mt-2 text-center">
                                <i class="bi bi-camera me-1"></i>{{ $article->cover_caption }}
                            </p>
                        @endif
                    </div>

                    <!-- Post Body Details -->
                    <div class="blog_details">
                        <h2>{{ $article->title }}</h2>
                        <ul class="blog-info-link mt-3 mb-4">
                            @if($article->category)
                                <li><a href="{{ route('blog.category', $article->category->slug) }}"><i class="bi bi-folder2-open"></i> {{ $article->category->name }}</a></li>
                            @endif
                            <li><a href="#"><i class="bi bi-person"></i> {{ $article->author_name }}</a></li>
                            <li><a href="#"><i class="bi bi-calendar3"></i> {{ $article->published_date }}</a></li>
                            <li><a href="#"><i class="bi bi-clock"></i> {{ $article->reading_time_label }}</a></li>
                            <li><a href="#"><i class="bi bi-eye"></i> {{ number_format($article->views_count) }} Tayangan</a></li>
                        </ul>

                        @if($article->excerpt)
                            <p class="excert">
                                {{ $article->excerpt }}
                            </p>
                        @endif

                        <!-- Main Article Body -->
                        <div class="blog-content-body py-2">
                            {!! $article->content !!}
                        </div>

                        <!-- Quote Callout -->
                        <div class="quote-wrapper">
                            <div class="quotes">
                                "Menuntut ilmu adalah kewajiban bagi setiap muslim. Dan sebaik-baik kalian adalah orang yang mempelajari Al-Qur'an dan mengamalkannya."
                            </div>
                        </div>

                        <!-- Tags List -->
                        @if($article->tags->count() > 0)
                            <div class="tag_cloud_widget pt-3 pb-2">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="small fw-bold text-muted me-1"><i class="bi bi-tags-fill text-success me-1"></i>Tagar:</span>
                                    <ul class="list p-0 m-0">
                                        @foreach($article->tags as $t)
                                            <li><a href="{{ route('blog.tag', $t->slug) }}">#{{ $t->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Navigation Top (Share & Stats) -->
                <div class="navigation-top" data-reveal>
                    <div class="d-sm-flex justify-content-between align-items-center text-center w-100">
                        <p class="like-info">
                            <span class="align-middle"><i class="bi bi-share-fill text-success"></i></span>
                            <span id="shareCountBadge">{{ number_format($article->shares_count) }}</span> kali dibagikan
                        </p>
                        <div class="col-sm-4 text-center my-2 my-sm-0">
                            <span class="small text-muted">Bagikan artikel ini:</span>
                        </div>
                        <ul class="social-icons">
                            <li>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . url()->current()) }}" 
                                   target="_blank" rel="noopener" onclick="trackArticleShare()" title="Bagikan ke WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                                   target="_blank" rel="noopener" onclick="trackArticleShare()" title="Bagikan ke Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" 
                                   target="_blank" rel="noopener" onclick="trackArticleShare()" title="Bagikan ke X / Twitter">
                                    <i class="bi bi-twitter-x"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
                                   target="_blank" rel="noopener" onclick="trackArticleShare()" title="Bagikan ke Telegram">
                                    <i class="bi bi-telegram"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="copyArticleUrl()" title="Salin Tautan">
                                    <i class="bi bi-link-45deg fs-5"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Prev & Next Article Navigation -->
                    @if(isset($prevArticle) || isset($nextArticle))
                        <div class="navigation-area w-100">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-6 col-12 nav-left flex-row d-flex justify-content-start align-items-center">
                                    @if(isset($prevArticle))
                                        <div class="thumb">
                                            <a href="{{ route('blog.show', $prevArticle->slug) }}">
                                                <img class="img-fluid" src="{{ $prevArticle->cover_url }}" alt="{{ $prevArticle->title }}" onerror="this.src='{{ asset('assets/img/1.jpg') }}'">
                                            </a>
                                        </div>
                                        <div class="detials text-start">
                                            <p><i class="bi bi-arrow-left me-1"></i> Artikel Sebelumnya</p>
                                            <a href="{{ route('blog.show', $prevArticle->slug) }}">
                                                <h4>{{ Str::limit($prevArticle->title, 36) }}</h4>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-6 col-md-6 col-12 nav-right flex-row d-flex justify-content-end align-items-center">
                                    @if(isset($nextArticle))
                                        <div class="detials text-end">
                                            <p>Artikel Selanjutnya <i class="bi bi-arrow-right ms-1"></i></p>
                                            <a href="{{ route('blog.show', $nextArticle->slug) }}">
                                                <h4>{{ Str::limit($nextArticle->title, 36) }}</h4>
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <a href="{{ route('blog.show', $nextArticle->slug) }}">
                                                <img class="img-fluid" src="{{ $nextArticle->cover_url }}" alt="{{ $nextArticle->title }}" onerror="this.src='{{ asset('assets/img/2.jpg') }}'">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Blog Author Bio Box -->
                <div class="blog-author" data-reveal>
                    <div class="media align-items-center d-flex">
                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="{{ $article->author_name }}">
                        <div class="media-body">
                            <a href="#">
                                <h4>{{ $article->author_name }}</h4>
                            </a>
                            <p>Tim Redaksi dan Asatidz AL-HIKMAH LMS yang berdedikasi menghadirkan panduan pembelajaran Al-Qur'an, wawasan tahsin, tahfidz, dan adab Islami bagi keluarga muslim.</p>
                        </div>
                    </div>
                </div>

                <!-- Related Articles Section -->
                @if(isset($relatedArticles) && $relatedArticles->count() > 0)
                    <div class="mt-5" data-reveal>
                        <h4 class="fw-bold mb-4 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                            <i class="bi bi-grid-fill text-success me-2"></i>Artikel Terkait Lainnya
                        </h4>
                        <div class="row g-4">
                            @foreach($relatedArticles as $rel)
                                <div class="col-md-4">
                                    <div class="single_special_cource h-100 bg-white rounded-4 border overflow-hidden p-0" style="border-color: var(--border-color) !important;">
                                        <div class="special_img_wrapper" style="height: 140px;">
                                            <img src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" style="height: 140px; width: 100%; object-fit: cover;"
                                                 onerror="this.src='{{ asset('assets/img/1.jpg') }}'">
                                        </div>
                                        <div class="p-3">
                                            <span class="badge bg-success-subtle text-success mb-2" style="font-size: 0.75rem;">{{ $rel->category->name ?? 'Edukasi' }}</span>
                                            <h5 class="fw-bold" style="font-size: 0.95rem; line-height: 1.4;">
                                                <a href="{{ route('blog.show', $rel->slug) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                                    {{ Str::limit($rel->title, 45) }}
                                                </a>
                                            </h5>
                                            <div class="small text-muted mt-2"><i class="bi bi-clock me-1"></i>{{ $rel->reading_time_label }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Sidebar Widgets -->
            <div class="col-lg-4">
                <div class="blog_right_sidebar">
                    <!-- 1. Search Widget -->
                    <aside class="single_sidebar_widget search_widget" data-reveal>
                        <form action="{{ route('blog.index') }}" method="GET">
                            <div class="form-group mb-0">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari kata kunci..." required>
                                    <div class="input-group-append">
                                        <button class="btn btn_1 py-2 px-3" type="submit" aria-label="Cari"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </aside>

                    <!-- 2. Post Category Widget -->
                    @if(isset($categories) && $categories->count() > 0)
                        <aside class="single_sidebar_widget post_category_widget" data-reveal data-reveal-delay="100">
                            <h4 class="widget_title">Kategori Artikel</h4>
                            <ul class="list cat-list">
                                @foreach($categories as $cat)
                                    <li class="{{ $article->category_id === $cat->id ? 'active' : '' }}">
                                        <a href="{{ route('blog.category', $cat->slug) }}" class="d-flex">
                                            <p class="mb-0"><i class="bi {{ $cat->icon ?? 'bi-bookmark-check' }} me-2 text-success"></i>{{ $cat->name }}</p>
                                            <span class="badge">{{ $cat->published_articles_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                    @endif

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
                    @if(isset($tags) && $tags->count() > 0)
                        <aside class="single_sidebar_widget tag_cloud_widget" data-reveal data-reveal-delay="300">
                            <h4 class="widget_title">Tag Populer</h4>
                            <ul class="list">
                                @foreach($tags as $t)
                                    <li>
                                        <a href="{{ route('blog.tag', $t->slug) }}">
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
                        <p class="text-muted small mb-4">Mulai perjalanan belajar Al-Qur'an terbaik bersama pendamping profesional AL-HIKMAH.</p>
                        <a href="https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20tertarik%20dengan%20artikel%20{{ urlencode($article->title) }}%20dan%20ingin%20konsultasi%20program%20belajar" 
                           target="_blank" rel="noopener" class="btn_1 w-100">
                            <i class="bi bi-whatsapp me-1"></i> Daftar / Tanya via WA
                        </a>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function trackArticleShare() {
    fetch('{{ route("blog.share", $article->slug) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data && data.shares_count) {
            const badge = document.getElementById('shareCountBadge');
            if(badge) badge.innerText = new Intl.NumberFormat('id-ID').format(data.shares_count);
        }
    })
    .catch(err => console.error(err));
}

function copyArticleUrl() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Tautan Disalin!',
                text: 'Tautan artikel berhasil disalin ke papan klip.',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('Tautan artikel berhasil disalin!');
        }
        trackArticleShare();
    });
}
</script>
@endpush
