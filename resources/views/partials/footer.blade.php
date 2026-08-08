<!-- FOOTER -->
    <footer class="footer" aria-label="Footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <a href="index.html" class="footer-logo"><img src="assets/img/logo/logo.png"
                                alt="AL-HIKMAH Logo" height="45" class="footer-logo-img"><span class="brand-name">AL
                                HIKMAH</span></a>
                        <p class="footer-description">Menemani perjalanan keluarga untuk mengenal, mencintai, dan
                            menghidupkan nilai-nilai Al-Qur'an dalam kehidupan.</p>
                        <div class="footer-socials">
                            <a href="https://www.instagram.com/houseofalhikmah/" target="_blank"><i
                                    class="bi bi-instagram"></i></a>
                            <a href="https://wa.me/6285786689008" target="_blank"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title">Navigasi</h6>
                    <ul class="footer-links">
                        <li><a href="index.html">Beranda</a></li>
                        <li><a href="tentang-kami.html">Tentang Kami</a></li>
                        <li><a href="program.html">Program</a></li>
                        <li><a href="metode.html">Metode Belajar</a></li>
                        <li><a href="galeri.html">Galeri</a></li>
                        <li><a href="faq.html">FAQ</a></li>
                        <li><a href="biaya.html">Biaya</a></li>
                        <li><a href="index.html#kontak">Kontak</a></li>
                        <li><a href="bergabung.html">Bergabung</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Program</h6>
                    <ul class="footer-links">
                        <li><a href="program.html">Iqra & Al-Qur'an</a></li>
                        <li><a href="program.html">Tahsin</a></li>
                        <li><a href="tahfidz.html">Tahfidz</a></li>
                        <li><a href="program.html">Adab & Doa Harian</a></li>
                        <li><a href="program.html">Kelas Muslimah</a></li>
                        <li><a href="program.html">Bahasa Arab</a></li>
                        <li><a href="bergabung.html">Menjadi Pendamping</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Kontak</h6>
                    <ul class="footer-contact">
                        <li><i class="bi bi-whatsapp"></i><a href="https://wa.me/6285786689008">+62 857-8668-9008</a>
                        </li>
                        <li><i class="bi bi-instagram"></i><a
                                href="https://www.instagram.com/houseofalhikmah/">@houseofalhikmah</a></li>
                        <li><i class="bi bi-envelope"></i><span>belajarquranalhikmah@gmail.com</span></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="footer-copyright">"Semoga langkah kecil hari ini menjadi jalan menuju kebaikan yang
                        panjang."</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-copyright">© 2020 AL-HIKMAH — Menemani Generasi Qur'ani Indonesia</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WA + Back to Top -->
    <a href="https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20ingin%20bertanya%20tentang%20program%20belajar%20AL-HIKMAH"
        class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Hubungi via WhatsApp"><i
            class="bi bi-whatsapp"></i><span class="wa-tooltip">Berbincang dengan Kami</span></a>
    <button class="back-to-top" id="backToTop" aria-label="Kembali ke atas"><i class="bi bi-chevron-up"></i></button>

    <!-- Modal -->
    <div class="modal fade" id="daftarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-premium">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-success me-2"></i>Formulir
                        Pendaftaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Isi data berikut, tim kami akan segera menghubungi Anda maksimal 1x24
                        jam.</p>
                    <form id="registrationForm" action="#" method="POST" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label fw-semibold" for="namaLengkap">Nama Lengkap
                                    <span class="text-danger">*</span></label><input type="text" class="form-control"
                                    id="namaLengkap" name="nama" required autocomplete="name"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="noWhatsApp">Nomor WhatsApp
                                    <span class="text-danger">*</span></label><input type="tel" class="form-control"
                                    id="noWhatsApp" name="whatsapp" required autocomplete="tel"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="usiaPeserta">Usia
                                    Peserta</label><select class="form-select" id="usiaPeserta" name="usia">
                                    <option value="">Pilih usia...</option>
                                    <option>10-15 tahun (Anak)</option>
                                    <option>Dewasa (16-30 tahun)</option>
                                    <option>Dewasa (31-50 tahun)</option>
                                    <option>50+ tahun</option>
                                </select></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="lokasi">Lokasi <span
                                        class="text-danger">*</span></label><input type="text" class="form-control"
                                    id="lokasi" name="lokasi" placeholder="Kota/Kecamatan" required
                                    autocomplete="address-level2"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="programPilihan">Program
                                    Pilihan</label><select class="form-select" id="programPilihan" name="program">
                                    <option value="">Pilih program...</option>
                                    <option>Tahsin</option>
                                    <option>Tahfidz</option>
                                    <option>Belajar dari Nol</option>
                                    <option>Program Anak</option>
                                    <option>Program Dewasa</option>
                                    <option>Bahasa Arab</option>
                                </select></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="metodeBelajar">Metode
                                    Belajar</label><select class="form-select" id="metodeBelajar" name="metode">
                                    <option value="">Pilih metode...</option>
                                    <option>Online</option>
                                    <option>Offline (Home Visit)</option>
                                    <option>Hybrid (Kombinasi)</option>
                                </select></div>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4"><i
                                class="bi bi-send me-2"></i>Kirim Pendaftaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>