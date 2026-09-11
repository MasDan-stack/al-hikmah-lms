@extends('layouts.landing')

@section('title', $meta['title'] ?? 'Galeri Dokumentasi Belajar Al-Qur\'an | AL-HIKMAH')
@section('meta_description', $meta['description'] ?? 'Menyimpan momen perjalanan belajar Al-Qur\'an.')
@section('meta_image', $meta['image'] ?? '')

@push('meta')
    <meta property="og:title" content="{{ $meta['title'] ?? 'Galeri | AL-HIKMAH' }}">
    <meta property="og:description" content="{{ $meta['description'] ?? 'Menyimpan momen perjalanan belajar Al-Qur\'an.' }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset('assets/img/og-image.jpg') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endpush

@push('styles')
@endpush

@section('content')
    <!-- ============================================ -->
    <!-- 1. PAGE HEADER - EDITORIAL MINIMALIST -->
    <!-- ============================================ -->
    <section class="py-5 bg-body-tertiary border-bottom" aria-label="Header Galeri">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    <div data-reveal>
                        <h1 class="editorial-title mb-3">Dokumentasi &amp; Momen <span class="text-emerald-deep">Belajar Santri</span></h1>
                        <p class="editorial-subtitle mx-auto">
                            Menyimpan rekaman perjalanan nyata santri dan asatidz AL-HIKMAH dalam mempelajari dan mencintai Al-Qur'an dengan adab dan ketenangan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- FILTER BAR - PREMIUM -->
    <!-- ============================================ -->
    <section class="py-4" style="background:var(--bg-secondary);border-bottom:1px solid var(--border-color);">
        <div class="container">
            <div class="filter-bar-modern" data-reveal>
                <!-- Search & Filter Row -->
                <div class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <form action="{{ route('galeri') }}" method="GET" class="w-100">
                            @if (request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if (request('program_id'))
                                <input type="hidden" name="program_id" value="{{ request('program_id') }}">
                            @endif
                            @if (request('tag'))
                                <input type="hidden" name="tag" value="{{ request('tag') }}">
                            @endif
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                    placeholder="Cari judul, lokasi, atau deskripsi..." value="{{ request('q') }}">
                                <button class="btn btn-primary-custom" type="submit">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-7">
                        <div class="category-pills-modern">
                            <a href="{{ route('galeri', array_merge(request()->except(['category', 'page']))) }}"
                                class="pill-item {{ !request('category') || request('category') === 'all' ? 'active' : '' }}">
                                <i class="bi bi-grid-fill"></i> Semua
                            </a>
                            @foreach ($categories as $cat)
                                <a href="{{ route('galeri', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
                                    class="pill-item {{ request('category') === $cat->slug ? 'active' : '' }}">
                                    <i class="bi {{ $cat->icon }}"></i> {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sub Filter - Program & Tags -->
                <div class="sub-filter-modern">
                    <span class="filter-label"><i class="bi bi-funnel me-1"></i> Program:</span>
                    <a href="{{ route('galeri', array_merge(request()->except(['program_id', 'page']))) }}"
                        class="filter-tag {{ !request('program_id') || request('program_id') === 'all' ? 'active' : '' }}">
                        Semua
                    </a>
                    @foreach ($programs as $prog)
                        <a href="{{ route('galeri', array_merge(request()->except(['program_id', 'page']), ['program_id' => $prog->id])) }}"
                            class="filter-tag {{ request('program_id') == $prog->id ? 'active' : '' }}">
                            {{ $prog->name }}
                        </a>
                    @endforeach

                    <span class="filter-label ms-2"><i class="bi bi-tags me-1"></i> Tag:</span>
                    @foreach ($popularTags as $tagItem)
                        <a href="{{ route('galeri', array_merge(request()->except(['tag', 'page']), ['tag' => $tagItem])) }}"
                            class="filter-tag {{ request('tag') === $tagItem ? 'active' : '' }}">
                            #{{ $tagItem }}
                        </a>
                    @endforeach

                    @if (request()->hasAny(['category', 'program_id', 'tag', 'q']))
                        <a href="{{ route('galeri') }}" class="filter-tag text-danger border-danger"
                            style="background:rgba(220,53,69,0.05);">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- HERO SLIDER - PREMIUM -->
    <!-- ============================================ -->
    @if ($featuredGalleries->isNotEmpty() && !request()->hasAny(['category', 'program_id', 'tag', 'q']))
        <section class="py-4" style="background:var(--bg-primary);">
            <div class="container">
                <div id="galleryHeroCarousel" class="carousel slide carousel-fade hero-slider-premium"
                    data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-indicators">
                        @foreach ($featuredGalleries as $idx => $feat)
                            <button type="button" data-bs-target="#galleryHeroCarousel"
                                data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}"
                                aria-label="Slide {{ $idx + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach ($featuredGalleries as $idx => $feat)
                            <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                <img src="{{ $feat->asset_url }}" alt="{{ $feat->title }}"
                                    loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
                                <div class="carousel-caption">
                                    <span class="badge {{ $feat->category_meta['badge_class'] }}">
                                        <i class="bi {{ $feat->category_meta['icon'] }} me-1"></i>
                                        {{ $feat->category_label }}
                                    </span>
                                    <h3>{{ $feat->title }}</h3>
                                    <p>{{ Str::limit($feat->caption ?? $feat->description, 120) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryHeroCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryHeroCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Selanjutnya</span>
                    </button>
                </div>
            </div>
        </section>
    @endif

    <!-- ============================================ -->
    <!-- GALERI GRID - MODERN -->
    <!-- ============================================ -->
    <section class="section-padding" aria-label="Galeri">
        <div class="container">
            @if (request()->hasAny(['category', 'program_id', 'tag', 'q']))
                <div class="d-flex align-items-center justify-content-between mb-4 p-3 rounded-3"
                    style="background:var(--primary-lighter);color:var(--primary);border-radius:16px;">
                    <div>
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Filter Aktif:</strong>
                        @if (request('q'))
                            <span class="badge bg-primary text-white ms-1">"{{ request('q') }}"</span>
                        @endif
                        @if (request('category'))
                            <span
                                class="badge bg-primary text-white ms-1">{{ $categories[request('category')]['label'] ?? request('category') }}</span>
                        @endif
                        @if (request('program_id'))
                            <span class="badge bg-primary text-white ms-1">Program Terpilih</span>
                        @endif
                        @if (request('tag'))
                            <span class="badge bg-primary text-white ms-1">#{{ request('tag') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('galeri') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            @endif

            <div class="gallery-grid-modern">
                @forelse ($galleries as $item)
                    <div class="gallery-card-premium open-lightbox-btn" data-id="{{ $item->id }}"
                        data-title="{{ $item->title }}" data-category="{{ $item->category_label }}"
                        data-badge-class="{{ $item->category_meta['badge_class'] }}" data-image="{{ $item->asset_url }}"
                        data-date="{{ $item->formatted_date }}" data-location="{{ $item->location ?? 'AL-HIKMAH' }}"
                        data-program="{{ $item->program?->name ?? 'Program Umum' }}"
                        data-views="{{ $item->views_count }}" data-caption="{{ $item->caption }}"
                        data-description="{{ $item->description }}" data-tags="{{ json_encode($item->tags ?? []) }}"
                        data-share-url="{{ route('galeri', ['q' => $item->title]) }}" data-reveal>

                        <img src="{{ $item->asset_url }}" alt="{{ $item->title }}" loading="lazy">

                        <span class="card-top-badge {{ $item->category_meta['badge_class'] }}">
                            <i class="bi {{ $item->category_meta['icon'] }} me-1"></i> {{ $item->category_label }}
                        </span>

                        <span class="card-views">
                            <i class="bi bi-eye"></i> {{ number_format($item->views_count) }}
                        </span>

                        <div class="card-overlay">
                            <span class="badge">
                                <i class="bi {{ $item->category_meta['icon'] }} me-1"></i> {{ $item->category_label }}
                            </span>
                            <h5>{{ $item->title }}</h5>
                            <p>{{ $item->caption ?? Str::limit($item->description, 80) }}</p>
                        </div>
                    </div>
                @empty
                    <!-- Empty State - Engaging -->
                    <div class="col-12" style="grid-column:1/-1;">
                        <div class="empty-state-premium" data-reveal>
                            <i class="empty-icon bi bi-images"></i>
                            <h4>Belum Ada Dokumentasi Kegiatan</h4>
                            <p>Sepertinya belum ada foto yang sesuai dengan filter yang Anda pilih. Coba jelajahi kategori
                                lain atau lihat semua galeri.</p>
                            <div class="suggestions">
                                <a href="{{ route('galeri') }}" class="btn btn-primary-custom rounded-pill px-4">
                                    <i class="bi bi-grid me-2"></i> Semua Galeri
                                </a>
                                <a href="{{ route('galeri', ['category' => 'kegiatan_belajar_mengajar']) }}"
                                    class="btn btn-outline-success rounded-pill px-4">
                                    <i class="bi bi-book me-2"></i> Kegiatan Belajar
                                </a>
                                <a href="{{ route('galeri', ['category' => 'prestasi_santri']) }}"
                                    class="btn btn-outline-info rounded-pill px-4">
                                    <i class="bi bi-trophy me-2"></i> Prestasi Santri
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($galleries->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Navigasi halaman galeri">
                        <ul class="pagination pagination-custom">
                            {{-- Previous Page Link --}}
                            @if ($galleries->onFirstPage())
                                <li class="page-item disabled"><span class="page-link"><i
                                            class="bi bi-chevron-left"></i></span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $galleries->previousPageUrl() }}"
                                        rel="prev"><i class="bi bi-chevron-left"></i></a></li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($galleries->getUrlRange(1, $galleries->lastPage()) as $page => $url)
                                @if ($page == $galleries->currentPage())
                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link"
                                            href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($galleries->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $galleries->nextPageUrl() }}"
                                        rel="next"><i class="bi bi-chevron-right"></i></a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link"><i
                                            class="bi bi-chevron-right"></i></span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif

            <!-- CTA Pattern Cards -->
            <div class="row g-4 mt-2">
                <div class="col-lg-6" data-reveal>
                    <div class="editorial-card p-4 p-md-5 justify-content-center text-center">
                        <div class="editorial-icon-badge mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                            <i class="bi bi-chat-dots-fill"></i>
                        </div>
                        <h2 class="editorial-title fs-4 mb-2">Konsultasi Kebutuhan Belajar</h2>
                        <p class="editorial-subtitle text-muted mb-4 small">
                            Diskusikan jadwal, kurikulum privat, atau penempatan tingkat belajar ananda bersama tim konselor AL-HIKMAH.
                        </p>
                        <div>
                            <a href="https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20ingin%20info%20lebih%20lanjut%20tentang%20AL-HIKMAH"
                                class="btn-editorial-whatsapp px-4" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp"></i> Hubungi Konselor via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="100">
                    <div class="editorial-card editorial-card-featured p-4 p-md-5 justify-content-center text-center">
                        <div class="editorial-icon-badge mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h2 class="editorial-title fs-4 mb-2">Pilihan Program Bimbingan</h2>
                        <p class="editorial-subtitle text-muted mb-4 small">
                            Tersedia program Privat Anak, Tahsin Dewasa, Muslimah, Tahfidz Mutqin, serta Bahasa Arab Dasar.
                        </p>
                        <div>
                            <a href="{{ route('program') }}" class="btn-editorial-primary px-4">
                                <i class="bi bi-journal-bookmark"></i> Lihat Pilihan Program
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- TESTIMONIAL SECTION - EDITORIAL MINIMALIST -->
    <!-- ============================================ -->
    <section class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Testimoni Santri">
        <div class="container py-3">
            <div class="text-center mb-5" data-reveal>
                <h2 class="editorial-title fs-2 mb-2">Cerita Nyata <span class="text-emerald-deep">Keluarga Santri</span></h2>
                <p class="editorial-subtitle text-muted">Pengalaman nyata dari mereka yang telah merasakan pendampingan privat AL-HIKMAH.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="editorial-card p-4">
                        <div class="d-flex gap-1 text-warning mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-secondary flex-grow-1 mb-4" style="line-height: 1.6;">"Anak saya jauh lebih semangat belajar Al-Qur'an. Pendekatan asatidz sangat sabar, ramah, dan membuat anak nyaman tanpa beban."</p>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="editorial-icon-badge mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                <i class="bi bi-person-heart"></i>
                            </div>
                            <div>
                                <h3 class="fs-6 fw-bold mb-0">Orang Tua Santri</h3>
                                <span class="small text-muted">Program Privat Anak</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="editorial-card p-4">
                        <div class="d-flex gap-1 text-warning mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-secondary flex-grow-1 mb-4" style="line-height: 1.6;">"Saya belajar dari nol di usia dewasa. Pendampingnya sangat sabar, teliti membimbing makharijul huruf, dan tidak pernah membuat saya canggung."</p>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="editorial-icon-badge mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <h3 class="fs-6 fw-bold mb-0">Peserta Dewasa</h3>
                                <span class="small text-muted">Program Tahsin Dewasa</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="editorial-card p-4">
                        <div class="d-flex gap-1 text-warning mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-secondary flex-grow-1 mb-4" style="line-height: 1.6;">"Jadwal fleksibel, laporan mutaba'ah selalu tercatat rapi di sistem. Kami sebagai wali santri dapat memantau progres hafalan ananda dengan tenang."</p>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="editorial-icon-badge mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                <i class="bi bi-house-heart"></i>
                            </div>
                            <div>
                                <h3 class="fs-6 fw-bold mb-0">Wali Santri</h3>
                                <span class="small text-muted">Bimbingan Home Visit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small fst-italic">
                    <i class="bi bi-quote me-1"></i> Testimoni adalah representasi amanah pendampingan belajar Al-Qur'an yang terus kami jaga.
                </p>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- LIGHTBOX MODAL - PREMIUM -->
    <!-- ============================================ -->
    <div class="modal fade lightbox-premium" id="galleryDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Image Container -->
                        <div class="col-lg-7 lightbox-image-container">
                            <img id="modalImage" src="" alt="Dokumentasi Detail">
                            <button type="button" class="lightbox-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <!-- Details Container -->
                        <div class="col-lg-5 lightbox-details">
                            <span id="modalCategoryBadge" class="badge-category bg-success text-white">
                                <i class="bi bi-tag me-1"></i> <span id="modalCategory"></span>
                            </span>

                            <h3 id="modalTitle"></h3>

                            <div class="meta-items">
                                <span class="meta-item"><i class="bi bi-calendar3"></i> <span
                                        id="modalDate"></span></span>
                                <span class="meta-item"><i class="bi bi-geo-alt"></i> <span
                                        id="modalLocation"></span></span>
                                <span class="meta-item"><i class="bi bi-eye"></i> <span id="modalViews"></span></span>
                                <span class="meta-item"><i class="bi bi-journal-bookmark"></i> <span
                                        id="modalProgram"></span></span>
                            </div>

                            <div id="modalCaptionWrapper" class="p-3 mb-3 rounded-3 bg-body-tertiary border">
                                <p id="modalCaption" class="mb-0 fst-italic small text-secondary"></p>
                            </div>

                            <div class="description" id="modalDescription"></div>

                            <div class="tags-container" id="modalTags"></div>

                            <div class="share-section">
                                <span class="share-label"><i class="bi bi-share me-1"></i> Bagikan</span>
                                <a id="shareWA" href="#" target="_blank" class="share-btn whatsapp"
                                    title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                <a id="shareFB" href="#" target="_blank" class="share-btn facebook"
                                    title="Facebook"><i class="bi bi-facebook"></i></a>
                                <a id="shareTwitter" href="#" target="_blank" class="share-btn"
                                    title="Twitter/X"><i class="bi bi-twitter-x"></i></a>
                                <button type="button" class="share-btn" title="Salin Link" onclick="copyShareLink()"><i
                                        class="bi bi-link-45deg"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('galleryDetailModal');
            if (!modalElement) return;

            const galleryModal = new bootstrap.Modal(modalElement);
            let currentShareUrl = '';

            document.querySelectorAll('.open-lightbox-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const data = {
                        id: this.getAttribute('data-id'),
                        title: this.getAttribute('data-title'),
                        category: this.getAttribute('data-category'),
                        badgeClass: this.getAttribute('data-badge-class'),
                        image: this.getAttribute('data-image'),
                        date: this.getAttribute('data-date'),
                        location: this.getAttribute('data-location'),
                        program: this.getAttribute('data-program'),
                        views: this.getAttribute('data-views'),
                        caption: this.getAttribute('data-caption'),
                        description: this.getAttribute('data-description'),
                        tagsAttr: this.getAttribute('data-tags'),
                        shareUrl: this.getAttribute('data-share-url')
                    };

                    currentShareUrl = data.shareUrl || window.location.href;

                    document.getElementById('modalImage').src = data.image;
                    document.getElementById('modalTitle').textContent = data.title;

                    const catBadge = document.getElementById('modalCategoryBadge');
                    catBadge.textContent = data.category || 'Umum';
                    catBadge.className = 'badge-category ' + (data.badgeClass ||
                        'bg-success text-white');

                    document.getElementById('modalCategory').textContent = data.category || 'Umum';
                    document.getElementById('modalDate').textContent = data.date || '-';
                    document.getElementById('modalLocation').textContent = data.location ||
                        'AL-HIKMAH';
                    document.getElementById('modalProgram').textContent = data.program ||
                        'Program Umum';
                    document.getElementById('modalViews').textContent = data.views || '0';

                    const captionEl = document.getElementById('modalCaption');
                    const captionWrapper = document.getElementById('modalCaptionWrapper');
                    if (data.caption && data.caption.trim() !== '') {
                        captionEl.textContent = '"' + data.caption + '"';
                        captionWrapper.style.display = 'block';
                    } else {
                        captionWrapper.style.display = 'none';
                    }

                    document.getElementById('modalDescription').textContent = data.description ||
                        'Tidak ada keterangan rincian kegiatan tambahan.';

                    const tagsContainer = document.getElementById('modalTags');
                    tagsContainer.innerHTML = '';
                    if (data.tagsAttr && data.tagsAttr !== 'null') {
                        try {
                            const tags = JSON.parse(data.tagsAttr);
                            if (Array.isArray(tags)) {
                                tags.forEach(t => {
                                    const span = document.createElement('span');
                                    span.className = 'tag';
                                    span.textContent = '#' + t;
                                    tagsContainer.appendChild(span);
                                });
                            }
                        } catch (e) {
                            console.log('Tags parsing error:', e);
                        }
                    }

                    // Share links
                    const shareUrl = encodeURIComponent(currentShareUrl);
                    const shareText = encodeURIComponent('Lihat momen dokumentasi AL-HIKMAH: ' +
                        data.title);
                    document.getElementById('shareWA').href =
                        'https://api.whatsapp.com/send?text=' + shareText + '%20' + shareUrl;
                    document.getElementById('shareFB').href =
                        'https://www.facebook.com/sharer/sharer.php?u=' + shareUrl;
                    document.getElementById('shareTwitter').href =
                        'https://twitter.com/intent/tweet?text=' + shareText + '&url=' + shareUrl;

                    galleryModal.show();

                    // Increment view counter
                    if (data.id) {
                        fetch('/galeri/' + data.id + '/view', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        }).then(res => res.json()).then(data => {
                            if (data.incremented) {
                                document.getElementById('modalViews').textContent =
                                    parseInt(document.getElementById('modalViews')
                                        .textContent) + 1;
                            }
                        }).catch(() => {});
                    }
                });
            });
        });

        function copyShareLink() {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Tautan galeri berhasil disalin!');
                }).catch(() => {
                    alert('Salin manual: ' + window.location.href);
                });
            } else {
                alert('Salin manual: ' + window.location.href);
            }
        }
    </script>
@endpush
