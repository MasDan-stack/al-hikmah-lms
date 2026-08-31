@extends('layouts.landing')

@section('title', 'Tentang Kami | AL-HIKMAH')
@section('description', 'Kenali filosofi dan nilai-nilai yang menjadi landasan perjalanan AL-HIKMAH.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Tentang Kami">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-info-circle"></i> Profil Lembaga</div>
                        <h2>Mengenal <span class="text-gradient">AL-HIKMAH</span> Lebih Dekat</h2>
                        <p>Perjalanan kami dalam mendampingi anak dan keluarga untuk mengenal, mencintai, dan menghidupkan nilai-nilai Al-Qur'an.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. KISAH KAMI (ETRAIN LEARNING PART) -->
    <!-- ============================================ -->
    <section class="learning_part" aria-label="Mengapa AL-HIKMAH Hadir">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-reveal>
                    <div class="learning_img">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Suasana belajar Al-Qur'an"
                             onerror="this.src='{{ asset('assets/img/etrain/learning_img.png') }}'">
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-4">
                        <div class="section-badge mb-2"><i class="bi bi-heart-pulse"></i> Perjalanan Kami</div>
                        <h2 class="section-title text-start mb-3">
                            Karena Anak-Anak Kita Tidak Hanya Perlu Diajar, Tetapi Juga <span class="text-gradient">Didampingi.</span>
                        </h2>
                        <p class="text-secondary mb-3">
                            Di tengah kesibukan kehidupan, tidak semua keluarga memiliki kesempatan untuk mendampingi anak belajar Al-Qur'an secara konsisten. Ada yang terkendala waktu, ada yang kesulitan menemukan guru yang cocok, dan ada pula yang ingin suasana belajar lebih hangat dan penuh kasih sayang.
                        </p>
                        <p class="text-secondary mb-4">
                            Dari situlah AL-HIKMAH hadir. Bukan sekadar untuk mengajarkan cara membaca, tetapi untuk menemani setiap langkah kecil dalam perjalanan mencintai Al-Qur'an.
                        </p>
                        <div class="p-3 bg-white rounded-4 shadow-sm border-start border-4 border-success mb-4">
                            <p class="mb-0 fst-italic text-success fw-semibold">
                                "Pendidikan terbaik bukan hanya tentang apa yang diketahui oleh seorang anak, tetapi tentang kebaikan yang tumbuh dalam dirinya."
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. REAL-TIME STATISTIK COUNTER (ETRAIN MEMBER COUNTER) -->
    <!-- ============================================ -->
    <section class="member_counter my-0" aria-label="Statistik Lembaga">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-4" data-reveal>
                    <div class="single_member_counter">
                        <span>{{ isset($totalStudents) && $totalStudents > 0 ? $totalStudents : '100+' }}</span>
                        <h4>Santri Terdaftar</h4>
                    </div>
                </div>
                <div class="col-6 col-md-4" data-reveal data-reveal-delay="100">
                    <div class="single_member_counter">
                        <span>{{ isset($totalMentors) && $totalMentors > 0 ? $totalMentors : '15+' }}</span>
                        <h4>Pendamping Aktif</h4>
                    </div>
                </div>
                <div class="col-12 col-md-4" data-reveal data-reveal-delay="200">
                    <div class="single_member_counter">
                        <span>{{ isset($totalPrograms) && $totalPrograms > 0 ? $totalPrograms : '6' }}</span>
                        <h4>Program Pembelajaran</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. FILOSOFI KAMI (ETRAIN FEATURE BOXES) -->
    <!-- ============================================ -->
    <section id="filosofi" class="feature_part" aria-label="Filosofi Lembaga">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-lightbulb"></i> Filosofi Kami</div>
                    <h2 class="section-title">Apa Arti <span class="text-gradient">AL-HIKMAH</span> bagi Kami?</h2>
                    <p class="section-description mx-auto">
                        Al-Hikmah adalah ketika ilmu yang dipelajari mampu membimbing seseorang untuk mengenal kebenaran, memperbaiki akhlak, dan menghadirkan kebaikan dalam kehidupan nyata.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="single_feature text-center">
                        <div class="single_feature_icon mx-auto">
                            <i class="bi bi-book-half"></i>
                        </div>
                        <h4>Belajar dengan Ilmu</h4>
                        <p>Setiap huruf dipelajari dengan pemahaman tajwid dan makhraj yang benar, bukan sekadar hafalan tanpa arti.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="single_feature text-center">
                        <div class="single_feature_icon mx-auto">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h4>Bertumbuh dengan Adab</h4>
                        <p>Karena ilmu yang berkah selalu diawali dengan adab yang baik terhadap Al-Qur'an, guru, dan orang tua.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="single_feature text-center">
                        <div class="single_feature_icon mx-auto">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h4>Berjalan dengan Istiqamah</h4>
                        <p>Langkah kecil yang dilakukan secara konsisten dan sabar jauh lebih bernilai dari lompatan besar yang sekejap.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. NILAI-NILAI UTAMA AL-HIKMAH -->
    <!-- ============================================ -->
    <section id="nilai" class="learning_part bg-white" aria-label="Nilai-Nilai Kami">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <div class="section-badge mx-auto mb-2"><i class="bi bi-heart"></i> Nilai-Nilai Lembaga</div>
                <h2 class="section-title">Belajar Al-Qur'an <span class="text-gradient">Dimulai dari Hati</span></h2>
                <p class="section-description mx-auto">Prinsip dasar yang selalu kami jaga dalam setiap interaksi pembelajaran dengan para santri.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-reveal>
                    <div class="p-4 bg-light rounded-4 border h-100 transition-hover">
                        <div class="fs-2 text-success mb-2"><i class="bi bi-suit-heart-fill"></i></div>
                        <h4 class="fw-bold fs-5">Ikhlas</h4>
                        <p class="text-secondary small mb-0">Mengajarkan dan belajar Al-Qur'an semata-mata sebagai ibadah dan upaya mendekatkan diri kepada Allah SWT.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="p-4 bg-light rounded-4 border h-100 transition-hover">
                        <div class="fs-2 text-primary mb-2"><i class="bi bi-book"></i></div>
                        <h4 class="fw-bold fs-5">Adab</h4>
                        <p class="text-secondary small mb-0">Sebelum ilmu menjadi cahaya, hati perlu dibimbing untuk menghormati ilmu, guru, dan sesama.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="p-4 bg-light rounded-4 border h-100 transition-hover">
                        <div class="fs-2 text-warning mb-2"><i class="bi bi-emoji-smile"></i></div>
                        <h4 class="fw-bold fs-5">Kasih Sayang</h4>
                        <p class="text-secondary small mb-0">Anak-anak akan lebih mudah menyerap kebaikan saat mereka merasa aman, dihargai, dan dicintai.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="300">
                    <div class="p-4 bg-light rounded-4 border h-100 transition-hover">
                        <div class="fs-2 text-info mb-2"><i class="bi bi-shield-check"></i></div>
                        <h4 class="fw-bold fs-5">Amanah</h4>
                        <p class="text-secondary small mb-0">Setiap amanah dari orang tua kami jaga dengan penuh rasa tanggung jawab dan keterbukaan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="400">
                    <div class="p-4 bg-light rounded-4 border h-100 transition-hover">
                        <div class="fs-2 text-success mb-2"><i class="bi bi-arrow-repeat"></i></div>
                        <h4 class="fw-bold fs-5">Istiqamah</h4>
                        <p class="text-secondary small mb-0">Kami mengajarkan pentingnya komitmen melangkah setahap demi setahap dalam memelihara hafalan dan bacaan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="500">
                    <div class="p-4 bg-light rounded-4 border h-100 transition-hover">
                        <div class="fs-2 text-primary mb-2"><i class="bi bi-person-check-fill"></i></div>
                        <h4 class="fw-bold fs-5">Keteladanan</h4>
                        <p class="text-secondary small mb-0">Pendidikan sejati terjadi bukan hanya melalui kata-kata, tetapi lewat akhlak yang dicontohkan.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('program') }}" class="btn_1">
                    <i class="bi bi-journal-bookmark"></i> Lihat Program Belajar
                </a>
            </div>
        </div>
    </section>
@endsection
