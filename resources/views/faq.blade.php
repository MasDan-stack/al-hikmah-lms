@extends('layouts.landing')

@section('title', 'Tanya Jawab (FAQ) | AL-HIKMAH')
@section('meta_description', 'Pertanyaan yang sering diajukan seputar program bimbingan Al-Qur\'an, pencocokan jadwal, profil guru pembimbing, dan metode di AL-HIKMAH.')

@section('content')
<!-- ============================================ -->
<!-- ============================================ -->
<!-- 1. PAGE HEADER - EDITORIAL MINIMALIST -->
<!-- ============================================ -->
<section class="editorial-page-header" aria-label="Header Tanya Jawab">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 text-center" data-reveal>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tanya Jawab (FAQ)</li>
                    </ol>
                </nav>

                <div class="editorial-badge mx-auto">
                    <i class="bi bi-question-circle-fill"></i>
                    <span>Bantuan &amp; Tanya Jawab</span>
                </div>

                <h1 class="editorial-title">Pusat Bantuan &amp; <span class="text-emerald-deep">Tanya Jawab (FAQ)</span></h1>
                <p class="editorial-subtitle mx-auto">
                    Jawaban lengkap seputar metode bimbingan, fleksibilitas jadwal privat, kualifikasi asatidz, serta transparansi biaya belajar Al-Qur'an.
                </p>

                <!-- Live FAQ Search Bar -->
                <div class="row justify-content-center mt-4">
                    <div class="col-md-8 col-lg-7">
                        <div class="input-group-editorial">
                            <span class="field-icon"><i class="bi bi-search"></i></span>
                            <input type="text" id="faqSearchInput" class="form-control" placeholder="Ketik kata kunci (jadwal, biaya, guru, offline)..." aria-label="Cari FAQ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 2. FAQ CONTENT & ACCORDION -->
<!-- ============================================ -->
<section class="py-5" aria-label="FAQ Accordion">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- FAQ Categories Filter Buttons -->
                <div class="d-flex justify-content-center flex-wrap gap-2 mb-4" data-reveal>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 active filter-faq-btn" data-category="all">Semua Kategori</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="umum">Umum &amp; Metode</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="jadwal">Jadwal &amp; Lokasi</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="biaya">Biaya &amp; Pembayaran</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="portal">Portal Orang Tua &amp; Santri</button>
                </div>

                <div class="accordion custom-accordion" id="faqAccordion">
                    <!-- FAQ 1 (Umum & Lokasi) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="jadwal">
                        <h2 class="accordion-header">
                            <button class="accordion-button rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                <i class="bi bi-house-door text-emerald-deep me-2 fs-5"></i> Apakah guru/pendamping datang langsung ke rumah?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                Ya, benar! Untuk metode <strong>Offline / Home Visit</strong>, guru pembimbing AL-HIKMAH akan datang langsung ke kediaman Anda sesuai jadwal hari dan jam yang telah disepakati bersama. Anda cukup menyediakan ruang belajar yang tenang dan nyaman. Kami juga menyediakan pilihan kelas <strong>Online</strong> (via Zoom/Google Meet) serta metode <strong>Hybrid</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 (Umum) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="umum">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                                <i class="bi bi-people text-emerald-deep me-2 fs-5"></i> Siapa saja yang bisa mengikuti program bimbingan?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                Program bimbingan kami terbuka untuk <strong>anak-anak, remaja, hingga dewasa</strong> dengan pendekatan bertahap, santun, dan bersahabat.<br><br>
                                Kurikulum disesuaikan dengan ritme santri sejak tingkat Iqra, Tahsin dasar, hingga Tahfidz hafalan terarah. Kami juga menyediakan kelas bimbingan untuk <strong>peserta dewasa</strong> (yang ingin memperbaiki makhraj dan hukum tajwid) serta <strong>halaqah privat muslimah</strong> dengan asatidzah wanita.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 (Jadwal) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="jadwal">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                                <i class="bi bi-calendar-check text-emerald-deep me-2 fs-5"></i> Apakah saya bisa menentukan hari dan jam belajar sendiri?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                Tentu saja bisa! Anda dapat memilih kombinasi hari bimbingan (misalnya Senin &amp; Kamis, atau Selasa &amp; Jumat) serta jam belajar yang paling cocok dengan rutinitas sekolah ananda. Tim konselor akan mencocokkan jadwal pilihan Anda dengan ketersediaan asatidz pembimbing terbaik.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 (Biaya & Pembayaran) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="biaya">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                                <i class="bi bi-wallet2 text-emerald-deep me-2 fs-5"></i> Kapan saya harus melakukan pembayaran? Apakah harus bayar di awal?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                <strong>Tidak ada pembayaran di awal!</strong> AL-HIKMAH menerapkan prinsip <em>"Deal Dulu, Baru Bayar"</em>. Tagihan pendaftaran dan SPP baru akan diterbitkan setelah Anda dan pihak lembaga sepakat mengenai jadwal dan asatidz pembimbing yang ditugaskan. Pembayaran dapat dilakukan dengan mudah melalui QRIS, Transfer Bank (Virtual Account), dan e-Wallet.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 (Jadwal & Lokasi) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="jadwal">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false">
                                <i class="bi bi-geo-alt text-emerald-deep me-2 fs-5"></i> Wilayah mana saja yang dijangkau oleh layanan AL-HIKMAH?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                Saat ini layanan Home Visit (guru datang ke rumah) melayani wilayah Jabodetabek, Bandung, dan sekitarnya. Namun untuk kelas Online, kami melayani santri dari seluruh penjuru Indonesia dan mancanegara. Silakan hubungi konselor kami untuk memastikan ketersediaan guru terdekat di area domisili Anda.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 6 (Portal & Evaluasi) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="portal">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false">
                                <i class="bi bi-graph-up-arrow text-emerald-deep me-2 fs-5"></i> Bagaimana cara memantau hafalan dan nilai perkembangan anak?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                Setiap orang tua mendapatkan akses ke <strong>Portal Orang Tua (Parent Portal)</strong>. Di sana Anda dapat melihat riwayat setiap sesi bimbingan, capaian ayat dan surah, penilaian makhraj dan tajwid, grafik perkembangan berkala, catatan asatidz, serta mengunduh <strong>Laporan Progres Belajar Resmi</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 7 (Portal & Jadwal) -->
                    <div class="accordion-item shadow-sm rounded-3 mb-3 border faq-card" data-category="portal">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false">
                                <i class="bi bi-clipboard2-check text-emerald-deep me-2 fs-5"></i> Bagaimana jika ananda berhalangan hadir atau sakit?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body lh-lg text-secondary">
                                Anda dapat mengonfirmasi kehadiran anak secara praktis melalui menu <strong>Jadwal Bimbingan Anak</strong> di Portal Orang Tua (memilih status <em>Izin</em> atau <em>Sakit</em> serta memberikan catatan ke asatidz pembimbing). Jadwal pengganti (<em>reschedule</em>) dapat dikoordinasikan langsung bersama guru pembimbing.
                            </div>
                        </div>
                    </div>
                </div>

                <div id="noFaqResults" class="text-center py-5 d-none editorial-card">
                    <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                    <h3 class="fs-5 fw-bold text-heading mb-1">Pertanyaan Tidak Ditemukan</h3>
                    <p class="text-muted small mb-3">Tidak ada pertanyaan yang sesuai dengan kata kunci pencarian Anda.</p>
                    <div>
                        <a href="{{ route('contact') }}" class="btn-editorial-primary px-4">
                            <i class="bi bi-envelope-paper"></i> Tanyakan Langsung via Kontak
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- HELP BANNER -->
        <div class="mt-5 p-4 p-md-5 editorial-card editorial-card-featured" data-reveal>
            <div class="row align-items-center">
                <div class="col-md-8 text-md-start mb-3 mb-md-0">
                    <h3 class="editorial-title fs-4 mb-1">Punya Pertanyaan Lain yang Belum Terjawab?</h3>
                    <p class="text-secondary small mb-0">Konsultasikan kebutuhan keluarga Anda secara langsung kepada tim konselor pendidikan AL-HIKMAH.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('contact') }}" class="btn-editorial-primary px-4 py-2">
                        <i class="bi bi-envelope-paper"></i> Kirim Pesan Konsultasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearchInput');
    const faqCards = document.querySelectorAll('.faq-card');
    const noResults = document.getElementById('noFaqResults');
    const filterButtons = document.querySelectorAll('.filter-faq-btn');

    let activeCategory = 'all';

    function filterFAQs() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        faqCards.forEach(function(card) {
            const cardCategory = card.getAttribute('data-category');
            const cardText = card.textContent.toLowerCase();

            const matchesCategory = (activeCategory === 'all' || cardCategory === activeCategory);
            const matchesQuery = query === '' || cardText.includes(query);

            if (matchesCategory && matchesQuery) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterFAQs);
    }

    filterButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active', 'btn-success', 'text-white'));
            this.classList.add('active');
            activeCategory = this.getAttribute('data-category');
            filterFAQs();
        });
    });
});
</script>
@endpush
@endsection
