<!-- FOOTER -->
<footer class="footer" aria-label="Footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="footer-logo">
                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="45"
                            class="footer-logo-img">
                        <span class="brand-name">AL HIKMAH</span>
                    </a>
                    <p class="footer-description">Menemani perjalanan keluarga untuk mengenal, mencintai, dan
                        menghidupkan nilai-nilai Al-Qur'an dalam kehidupan.</p>
                    <div class="footer-socials">
                        <a href="https://www.instagram.com/{{ site_setting('instagram_handle', 'houseofalhikmah') }}/"
                            target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a>
                        <a href="{{ wa_url() }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Navigasi</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('roadmap') }}">Alur Belajar (Roadmap)</a></li>
                    <li><a href="{{ route('tentang-kami') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('program') }}">Program</a></li>
                    <li><a href="{{ route('metode') }}">Metode Belajar</a></li>
                    <li><a href="{{ route('faq') }}">Tanya Jawab (FAQ)</a></li>
                    @auth
                        @if (auth()->user()->isParent())
                            <li><a href="{{ route('biaya') }}">Biaya</a></li>
                        @elseif (auth()->user()->isAdmin())
                            <li><a href="{{ route('biaya') }}">Biaya (Admin)</a></li>
                        @endif
                    @endauth
                    <li><a href="{{ route('contact') }}">Hubungi Kami</a></li>
                    @guest
                        <li><a href="{{ route('bergabung') }}">Bergabung</a></li>
                    @endguest
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Program</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('program') }}">Iqra & Al-Qur'an</a></li>
                    <li><a href="{{ route('program') }}">Tahsin</a></li>
                    <li><a href="{{ route('tahfidz') }}">Tahfidz</a></li>
                    <li><a href="{{ route('program') }}">Adab & Doa Harian</a></li>
                    <li><a href="{{ route('program') }}">Kelas Muslimah</a></li>
                    <li><a href="{{ route('program') }}">Bahasa Arab</a></li>
                    @guest
                        <li><a href="{{ route('bergabung') }}">Menjadi Pendamping</a></li>
                    @endguest
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Kontak</h6>
                <ul class="footer-contact">
                    <li><i class="bi bi-whatsapp"></i><a href="{{ wa_url() }}" target="_blank"
                            rel="noopener">+{{ site_setting('whatsapp_number', '6285786689008') }}</a></li>
                    <li><i class="bi bi-instagram"></i><a
                            href="https://www.instagram.com/{{ site_setting('instagram_handle', 'houseofalhikmah') }}/"
                            target="_blank" rel="noopener">@<span>{{ site_setting('instagram_handle', 'houseofalhikmah') }}</span></a></li>
                    <li><i
                            class="bi bi-envelope"></i><span>{{ site_setting('email_contact', 'belajarquranalhikmah@gmail.com') }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="footer-copyright">"Semoga langkah kecil hari ini menjadi jalan menuju kebaikan yang panjang."
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="footer-copyright">© {{ date('Y') }} {{ site_setting('site_name', 'AL-HIKMAH') }} —
                    {{ site_setting('site_tagline', 'Menemani Generasi Qur\'ani Indonesia') }}</p>
            </div>
        </div>
    </div>
</footer>
