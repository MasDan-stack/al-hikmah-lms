@extends('layouts.landing')

@section('title', 'Tanya Jawab (FAQ) | AL-HIKMAH')
@section('meta_description', 'Pertanyaan yang sering diajukan seputar program bimbingan Al-Qur\'an, pencocokan jadwal, profil guru pembimbing, dan metode di AL-HIKMAH.')

@section('content')
<!-- ============================================ -->
<!-- 1. ETRAIN BREADCRUMB HEADER -->
<!-- ============================================ -->
<section class="breadcrumb_bg" aria-label="Header Tanya Jawab">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb_iner_item" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-question-circle"></i> Pusat Bantuan &amp; Informasi</div>
                    <h2>Sebelum Memulai <span class="text-gradient">Perjalanan Bersama Kami</span></h2>
                    <p>Temukan jawaban lengkap seputar metode, jadwal, guru pendamping, dan tata cara pendaftaran bimbingan Al-Qur'an.</p>

                    <!-- Live FAQ Search Bar -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border bg-white">
                                <span class="input-group-text bg-white border-0 ps-4 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="faqSearchInput" class="form-control border-0 py-3" placeholder="Ketik pertanyaan (jadwal, biaya, guru, offline)..." aria-label="Cari FAQ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="section-padding" aria-label="FAQ Accordion">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- FAQ Categories Filter Buttons -->
                <div class="d-flex justify-content-center flex-wrap gap-2 mb-4" data-reveal>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 active filter-faq-btn" data-category="all">Semua Kategori</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="umum">Umum & Metode</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="jadwal">Jadwal & Lokasi</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="biaya">Biaya & Pembayaran</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 filter-faq-btn" data-category="portal">Portal Orang Tua & Siswa</button>
                </div>

                <div class="accordion custom-accordion" id="faqAccordion">
                    <!-- FAQ 1 (Umum & Lokasi) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="jadwal">
                        <h3 class="accordion-header">
                            <button class="accordion-button rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                <i class="bi bi-house-door text-success me-2 fs-5"></i> Apakah guru/pendamping datang langsung ke rumah?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                Ya, benar! Untuk metode <strong>Offline / Home Visit</strong>, guru pembimbing AL-HIKMAH akan datang langsung ke kediaman Anda sesuai jadwal hari dan jam yang telah disepakati bersama. Anda cukup menyediakan ruang belajar yang tenang dan nyaman. Kami juga menyediakan pilihan kelas <strong>Online</strong> (via Zoom/Google Meet) serta metode <strong>Hybrid</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 (Umum) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="umum">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                                <i class="bi bi-people text-success me-2 fs-5"></i> Siapa saja yang bisa mengikuti program bimbingan?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                Program utama kami dirancang khusus untuk <strong>anak-anak usia 10–15 tahun</strong> dengan pendekatan santun, bersahabat, dan bertahap.<br><br>
                                Selain itu, kami juga menyediakan kelas bimbingan untuk <strong>peserta dewasa</strong> (yang ingin belajar membaca dari nol atau memperbaiki tahsin tajwid) serta <strong>kelas muslimah</strong> dengan pengajar ustadzah wanita.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 (Jadwal) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="jadwal">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                                <i class="bi bi-calendar-check text-success me-2 fs-5"></i> Apakah saya bisa menentukan hari dan jam belajar sendiri?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                Tentu saja bisa! Anda dapat memilih kombinasi hari bimbingan (misalnya Senin & Kamis, atau Selasa & Jumat) serta jam belajar yang paling cocok dengan rutinitas sekolah ananda. Admin akan mencocokkan jadwal pilihan Anda dengan kuota ketersediaan guru pembimbing terbaik.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 (Biaya & Pembayaran) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="biaya">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                                <i class="bi bi-wallet2 text-success me-2 fs-5"></i> Kapan saya harus melakukan pembayaran? Apakah harus bayar di awal?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                <strong>Tidak ada pembayaran di awal!</strong> AL-HIKMAH menerapkan prinsip <em>"Deal Dulu, Baru Bayar"</em>. Tagihan pendaftaran dan SPP baru akan diterbitkan setelah Anda dan pihak lembaga sepakat mengenai jadwal dan guru pembimbing yang ditugaskan. Pembayaran dapat dilakukan dengan mudah melalui QRIS, GoPay, dan Transfer Bank (Virtual Account).
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 (Jadwal & Lokasi) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="jadwal">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false">
                                <i class="bi bi-geo-alt text-success me-2 fs-5"></i> Wilayah mana saja yang dijangkau oleh layanan AL-HIKMAH?
                            </button>
                        </h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                Saat ini layanan Home Visit (guru datang ke rumah) melayani wilayah Jabodetabek, Bandung, dan sekitarnya. Namun untuk kelas Online, kami melayani santri dari seluruh penjuru Indonesia dan mancanegara. Silakan hubungi CS kami untuk memastikan ketersediaan guru terdekat di area Anda.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 6 (Portal & Evaluasi) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="portal">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false">
                                <i class="bi bi-graph-up-arrow text-success me-2 fs-5"></i> Bagaimana cara memantau hafalan dan nilai perkembangan anak?
                            </button>
                        </h3>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                Setiap orang tua mendapatkan akses ke <strong>Portal Orang Tua (Parent Portal)</strong>. Di sana Anda dapat melihat riwayat setiap sesi bimbingan, capaian ayat/surah, nilai kelancaran dan tajwid, grafik perkembangan 6 bulan, catatan guru, serta dapat mengunduh <strong>Laporan Progres format PDF Resmi</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 7 (Portal & Jadwal) -->
                    <div class="accordion-item shadow-sm rounded-4 mb-3 border-0 faq-card" data-category="portal">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false">
                                <i class="bi bi-clipboard2-check text-success me-2 fs-5"></i> Bagaimana jika ananda berhalangan hadir atau sakit?
                            </button>
                        </h3>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg">
                                Anda dapat mengonfirmasi kehadiran anak secara praktis melalui menu <strong>Jadwal Bimbingan Anak</strong> di Portal Orang Tua (memilih status <em>Izin</em> atau <em>Sakit</em> serta memberikan catatan ke guru pembimbing). Jadwal pengganti (*reschedule*) dapat dikoordinasikan langsung bersama guru pembimbing.
                            </div>
                        </div>
                    </div>
                </div>

                <div id="noFaqResults" class="text-center py-5 d-none bg-light rounded-4">
                    <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                    <h5 class="fw-bold text-dark">Pertanyaan Tidak Ditemukan</h5>
                    <p class="text-muted small mb-3">Tidak ada pertanyaan yang sesuai dengan kata kunci pencarian Anda.</p>
                    <a href="{{ route('contact') }}" class="btn btn-sm btn-primary-custom rounded-pill px-4">
                        <i class="bi bi-envelope-paper me-1"></i> Tanyakan Langsung via Kontak
                    </a>
                </div>
            </div>
        </div>

        <!-- HELP BANNER -->
        <div class="mt-5 p-4 p-md-5 rounded-4 bg-light border text-center" data-reveal>
            <div class="row align-items-center">
                <div class="col-md-8 text-md-start mb-3 mb-md-0">
                    <h4 class="fw-bold text-dark mb-1">Punya Pertanyaan Lain yang Belum Terjawab?</h4>
                    <p class="text-muted small mb-0">Konsultasikan kebutuhan keluarga Anda secara langsung kepada admin pengelola AL-HIKMAH.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('contact') }}" class="btn btn-primary-custom rounded-pill px-4 py-2 fw-bold">
                        <i class="bi bi-envelope-paper me-1"></i> Kirim Pesan
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
