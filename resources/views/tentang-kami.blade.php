@extends('layouts.landing')

@section('title', 'Tentang Kami | AL-HIKMAH')
@section('description', 'Kenali filosofi dan nilai-nilai yang menjadi landasan perjalanan AL-HIKMAH.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal>
                <i class="bi bi-info-circle"></i> Tentang Kami
            </div>
            <h1 class="section-title" data-reveal>
                Mengenal <span class="text-gradient">AL-HIKMAH</span> Lebih Dekat
            </h1>
            <p class="section-description mx-auto" data-reveal>
                Perjalanan kami dalam mendampingi anak dan keluarga untuk mengenal, mencintai, dan menghidupkan nilai-nilai
                Al-Qur'an.
            </p>
        </div>
    </section>

    <!-- Mengapa AL-HIKMAH Hadir -->
    <section class="section-padding" aria-label="Mengapa AL-HIKMAH Hadir">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0" data-reveal>
                    <div class="about-image-wrapper">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Suasana belajar Al-Qur'an" class="about-image"
                            loading="lazy">
                        <div class="about-decoration" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="200">
                    <div class="section-badge"><i class="bi bi-info-circle"></i> Perjalanan Kami</div>
                    <h2 class="section-title">
                        Karena Anak-Anak Kita Tidak Hanya Perlu Diajar, Tetapi Juga <span
                            class="text-gradient">Didampingi.</span>
                    </h2>
                    <p class="section-description" style="max-width:100%">
                        Di tengah kesibukan kehidupan, tidak semua keluarga memiliki kesempatan untuk mendampingi anak
                        belajar Al-Qur'an secara konsisten.
                    </p>
                    <p class="section-description" style="max-width:100%">
                        Ada yang terkendala waktu.<br>
                        Ada yang kesulitan menemukan pendamping yang sesuai.<br>
                        Ada pula yang ingin anaknya belajar dengan suasana yang lebih nyaman dan penuh kasih sayang.
                    </p>
                    <p class="section-description" style="max-width:100%">
                        Dari situlah AL-HIKMAH hadir.<br><br>
                        Bukan sekadar untuk mengajarkan cara membaca Al-Qur'an, tetapi untuk menemani setiap langkah kecil
                        dalam perjalanan mengenal dan mencintai Al-Qur'an.
                    </p>
                    <p style="font-weight:600;color:var(--primary);margin-top:15px;font-style:italic">
                        "Karena kami percaya, pendidikan terbaik bukan hanya tentang apa yang diketahui oleh seorang anak,
                        tetapi tentang kebaikan yang tumbuh dalam dirinya."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filosofi -->
    <section id="filosofi" class="section-padding section-alt" aria-label="Filosofi">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-reveal>
                    <div class="section-badge mx-auto"><i class="bi bi-lightbulb"></i> Filosofi Kami</div>
                    <h2 class="section-title">
                        Apa Arti <span class="text-gradient">AL-HIKMAH</span> bagi Kami?
                    </h2>
                    <p class="section-description mx-auto" style="max-width:700px">
                        Al-Hikmah bukan hanya tentang pengetahuan. Bagi kami, hikmah adalah ketika ilmu yang dipelajari
                        mampu membimbing seseorang untuk mengenal kebenaran, memperbaiki akhlak, dan menghadirkan kebaikan
                        dalam kehidupan.
                    </p>
                    <p class="section-description mx-auto" style="max-width:700px">
                        Karena itu, kami tidak ingin hanya membantu seseorang menjadi lebih mampu membaca Al-Qur'an. Kami
                        ingin menemani proses agar ilmu yang dipelajari perlahan menjadi sesuatu yang hidup dalam hati,
                        tercermin dalam akhlak, dan membawa manfaat bagi sesama.
                    </p>
                    <div class="row g-4 mt-4">
                        <div class="col-md-4" data-reveal data-reveal-delay="100">
                            <div class="why-card text-center">
                                <div class="why-icon"><i class="bi bi-book"></i></div>
                                <h4>Belajar dengan Ilmu</h4>
                                <p>Setiap huruf dipelajari dengan pemahaman, bukan sekadar pengulangan.</p>
                            </div>
                        </div>
                        <div class="col-md-4" data-reveal data-reveal-delay="200">
                            <div class="why-card text-center">
                                <div class="why-icon"><i class="bi bi-person-lines-fill"></i></div>
                                <h4>Bertumbuh dengan Adab</h4>
                                <p>Karena ilmu yang baik seharusnya melahirkan akhlak yang baik pula.</p>
                            </div>
                        </div>
                        <div class="col-md-4" data-reveal data-reveal-delay="300">
                            <div class="why-card text-center">
                                <div class="why-icon"><i class="bi bi-arrow-repeat"></i></div>
                                <h4>Berjalan dengan Istiqamah</h4>
                                <p>Langkah kecil yang terus-menerus lebih kami hargai daripada lompatan besar yang sekejap.
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="section-description mx-auto mt-4"
                        style="max-width:700px;font-weight:600;color:var(--primary);font-style:italic">
                        Belajar dengan ilmu. Bertumbuh dengan adab. Berjalan dengan istiqamah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nilai-Nilai -->
    <section id="nilai" class="section-padding" aria-label="Nilai-Nilai Kami">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <div class="section-badge mx-auto"><i class="bi bi-heart"></i> Nilai yang Kami Pegang</div>
                <h2 class="section-title">
                    Belajar Al-Qur'an<br><span class="text-gradient">Dimulai dari Hati</span>
                </h2>
                <p class="section-description mx-auto" style="max-width:650px">
                    Ilmu yang baik seharusnya melahirkan akhlak yang baik. Karena itu, dalam setiap proses belajar, kami
                    berusaha menjaga nilai-nilai ini.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-reveal>
                    <div class="nilai-card">
                        <div class="nilai-icon"><i class="bi bi-suit-heart"></i></div>
                        <h4>Ikhlas</h4>
                        <p>Mengajarkan dan belajar Al-Qur'an sebagai bentuk ibadah dan upaya mendekatkan diri kepada Allah.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="nilai-card">
                        <div class="nilai-icon"><i class="bi bi-book"></i></div>
                        <h4>Adab</h4>
                        <p>Karena sebelum ilmu menjadi cahaya, hati perlu belajar menghormati ilmu, pendamping, orang tua,
                            dan sesama.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="nilai-card">
                        <div class="nilai-icon"><i class="bi bi-emoji-smile"></i></div>
                        <h4>Kasih Sayang</h4>
                        <p>Kami percaya bahwa anak akan lebih mudah belajar ketika ia merasa aman, dihargai, dan disayangi.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="300">
                    <div class="nilai-card">
                        <div class="nilai-icon"><i class="bi bi-shield-check"></i></div>
                        <h4>Amanah</h4>
                        <p>Setiap murid adalah titipan yang harus didampingi dengan tanggung jawab dan kesungguhan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="400">
                    <div class="nilai-card">
                        <div class="nilai-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <h4>Istiqamah</h4>
                        <p>Kami tidak mengejar kesempurnaan dalam satu hari. Kami mengajarkan pentingnya terus melangkah,
                            sedikit demi sedikit.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="500">
                    <div class="nilai-card">
                        <div class="nilai-icon"><i class="bi bi-person-check"></i></div>
                        <h4>Keteladanan</h4>
                        <p>Karena pendidikan tidak hanya terjadi melalui apa yang diajarkan, tetapi juga melalui apa yang
                            dicontohkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Harapan -->
    <section class="section-padding section-alt" aria-label="Harapan">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0" data-reveal>
                    <div class="about-image-wrapper">
                        <img src="{{ asset('assets/img/3.jpg') }}" alt="Ilustrasi belajar Al-Qur'an" class="about-image"
                            loading="lazy">
                        <p
                            style="font-size:.75rem;color:var(--text-muted);margin-top:8px;text-align:center;font-style:italic">
                            suasana belajar</p>
                    </div>
                </div>
                <div class="col-lg-7" data-reveal data-reveal-delay="200">
                    <div class="section-badge"><i class="bi bi-lightbulb"></i> Harapan Kami</div>
                    <h2 class="section-title">
                        Bukan Sekadar Bisa<br><span class="text-gradient">Membaca Al-Qur'an</span>
                    </h2>
                    <p class="section-description" style="max-width:100%">
                        Kami berharap perjalanan belajar bersama AL-HIKMAH dapat menjadi lebih dari sekadar kemampuan
                        membaca atau menghafal. Kami ingin membantu menumbuhkan:
                    </p>
                    <div class="harapan-list mt-4">
                        <div class="harapan-item">
                            <div class="harapan-icon"><i class="bi bi-book"></i></div>
                            <div class="harapan-text">
                                <h5>Kecintaan kepada Al-Qur'an</h5>
                                <p>Agar Al-Qur'an menjadi bagian dari kehidupan.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon"><i class="bi bi-person-lines-fill"></i></div>
                            <div class="harapan-text">
                                <h5>Adab yang Baik</h5>
                                <p>Agar ilmu tercermin dalam sikap dan perilaku.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon"><i class="bi bi-moon-stars"></i></div>
                            <div class="harapan-text">
                                <h5>Kebiasaan Beribadah</h5>
                                <p>Agar anak terbiasa dengan doa dan nilai-nilai Islam.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon"><i class="bi bi-heart"></i></div>
                            <div class="harapan-text">
                                <h5>Kedekatan dengan Allah</h5>
                                <p>Agar belajar menjadi jalan mengenal Sang Pencipta.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon"><i class="bi bi-emoji-smile"></i></div>
                            <div class="harapan-text">
                                <h5>Percaya Diri Belajar Agama</h5>
                                <p>Agar tidak ada rasa malu memulai dari nol.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
