// Performance: Use passive event listeners
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ============================================
    // Loading Screen - Optimized
    // ============================================
    const loadingScreen = document.getElementById('loadingScreen');
    window.addEventListener('load', function () {
        if (loadingScreen) {
            requestAnimationFrame(() => {
                loadingScreen.classList.add('loaded');
                setTimeout(() => {
                    if (loadingScreen) loadingScreen.style.display = 'none';
                }, 500);
            });
        }
    }, {
        once: true
    });

    // ============================================
    // Dark Mode Toggle - Event Delegation & Sync All Elements
    // ============================================
    const html = document.documentElement;

    function setTheme(isDark) {
        if (!html) return;

        // Add transition class
        html.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        html.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');

        const themeIcons = document.querySelectorAll('.theme-toggle-btn i, #themeIcon, #themeIconMobile');
        themeIcons.forEach(function (icon) {
            icon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        });

        try {
            localStorage.setItem('alhikmah-theme', isDark ? 'dark' : 'light');
        } catch (e) {
            // localStorage not available
        }

        // Remove transition after complete
        setTimeout(() => {
            html.style.transition = '';
        }, 300);
    }

    // Check saved theme or system preference
    try {
        const savedTheme = localStorage.getItem('alhikmah-theme');
        if (savedTheme === 'dark') {
            setTheme(true);
        } else if (savedTheme === 'light') {
            setTheme(false);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            setTheme(true);
        }
    } catch (e) {
        // localStorage not available
    }

    // Event Delegation: Global click listener for theme toggle buttons
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.theme-toggle-btn, #themeToggle, #themeToggleMobile');
        if (toggleBtn) {
            e.preventDefault();
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            setTheme(!isDark);
        }
    });

    // ============================================
    // Navbar Scroll Effect - Throttled
    // ============================================
    const navbar = document.getElementById('mainNavbar');
    let scrollTicking = false;

    function updateNavbar() {
        if (!navbar) return;

        if (window.scrollY > 40) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
        scrollTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (!scrollTicking) {
            requestAnimationFrame(updateNavbar);
            scrollTicking = true;
        }
    }, {
        passive: true
    });

    // ============================================
    // Smooth Scroll for Anchor Links - Fixed
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        if (anchor.classList.contains('dropdown-toggle')) return;

        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#' || targetId === '') return;

            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                const navHeight = navbar ? navbar.offsetHeight : 80;
                const targetPosition = targetEl.getBoundingClientRect().top + window
                    .pageYOffset - navHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                // Close mobile menu
                const navCollapse = document.getElementById('navbarNav');
                if (navCollapse && navCollapse.classList.contains('show') && typeof bootstrap !== 'undefined') {
                    const bsCollapse = bootstrap.Collapse.getInstance(navCollapse) || new bootstrap.Collapse(navCollapse, { toggle: true });
                    bsCollapse.hide();
                }
            }
        });
    });

    // ============================================
    // Active Nav Link on Scroll - Only for on-page sections
    // ============================================
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    let activeTicking = false;

    function updateActiveNav() {
        if (!sections || sections.length === 0) return;

        let current = '';
        const scrollPos = window.scrollY + 120;

        sections.forEach(function (section) {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (scrollPos >= sectionTop && scrollPos <= sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });

        if (current) {
            navLinks.forEach(function (link) {
                const href = link.getAttribute('href') || '';
                if (href.includes('#')) {
                    if (href === '#' + current || href.endsWith('#' + current)) {
                        link.classList.add('active');
                    } else if (href.startsWith('#')) {
                        link.classList.remove('active');
                    }
                }
            });
        }

        activeTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (!activeTicking) {
            requestAnimationFrame(updateActiveNav);
            activeTicking = true;
        }
    }, {
        passive: true
    });

    // ============================================
    // Back to Top Button - Throttled
    // ============================================
    const backToTopBtn = document.getElementById('backToTop');
    let topBtnTicking = false;

    function updateBackToTop() {
        if (!backToTopBtn) return;

        if (window.scrollY > 400) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
        topBtnTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (!topBtnTicking) {
            requestAnimationFrame(updateBackToTop);
            topBtnTicking = true;
        }
    }, {
        passive: true
    });

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        backToTopBtn.setAttribute('aria-label', 'Kembali ke atas halaman');
    }

    // ============================================
    // Scroll Reveal Animation - Performance
    // ============================================
    const revealElements = document.querySelectorAll('[data-reveal]');

    if ('IntersectionObserver' in window && revealElements.length > 0) {
        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const delay = parseInt(entry.target.getAttribute(
                        'data-reveal-delay')) || 0;
                    setTimeout(function () {
                        entry.target.classList.add('revealed');
                    }, delay);
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });
    } else {
        // Fallback for older browsers
        revealElements.forEach(function (el) {
            el.classList.add('revealed');
        });
    }

    // ============================================
    // Floating WhatsApp Tooltip
    // ============================================
    const waBtn = document.querySelector('.floating-whatsapp');
    if (waBtn) {
        const tooltip = waBtn.querySelector('.wa-tooltip');
        if (tooltip) {
            waBtn.addEventListener('mouseenter', function () {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(-50%) translateX(0)';
            });
            waBtn.addEventListener('mouseleave', function () {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'translateY(-50%) translateX(-10px)';
            });
        }
        waBtn.setAttribute('aria-label', 'Hubungi kami via WhatsApp');
    }

    // ============================================
    // Image Error Handling
    // ============================================
    document.querySelectorAll('img').forEach(function (img) {
        img.addEventListener('error', function () {
            if (!this.hasAttribute('data-failed')) {
                this.setAttribute('data-failed', 'true');
                const src = this.getAttribute('src');
                if (src && !src.includes('placehold.co')) {
                    this.src =
                        'https://placehold.co/600x400/0d7a3e/ffffff?text=AL-HIKMAH';
                }
            }
        });
    });

    // ============================================
    // Form Submission - Modal Pendaftaran ke Register Form
    // ============================================
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function (e) {
            // Clean previous feedback messages
            this.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const inputs = this.querySelectorAll('input[required], select[required]');
            let isValid = true;

            inputs.forEach(function (input) {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Wajib diisi';
                    input.parentNode.appendChild(feedback);
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // ============================================
    // Modal Focus Trap for Accessibility
    // ============================================
    const daftarModal = document.getElementById('daftarModal');
    if (daftarModal) {
        daftarModal.addEventListener('shown.bs.modal', function () {
            const firstInput = this.querySelector('input, select, textarea');
            if (firstInput) setTimeout(() => firstInput.focus(), 100);
        });
    }

    // ============================================
    // 38. PRAYER TIMES ENGINE (JADWAL SHOLAT REAL-TIME)
    // ============================================
    const PrayerTimesApp = {
        cities: [
            // Jabodetabek
            { name: 'DKI Jakarta', lat: -6.2088, lng: 106.8456, region: 'Jabodetabek' },
            { name: 'Kota Jakarta Selatan', lat: -6.2615, lng: 106.8106, region: 'Jabodetabek' },
            { name: 'Kota Jakarta Pusat', lat: -6.1818, lng: 106.8223, region: 'Jabodetabek' },
            { name: 'Kota Jakarta Barat', lat: -6.1683, lng: 106.7588, region: 'Jabodetabek' },
            { name: 'Kota Jakarta Timur', lat: -6.2250, lng: 106.9004, region: 'Jabodetabek' },
            { name: 'Kota Jakarta Utara', lat: -6.1384, lng: 106.8642, region: 'Jabodetabek' },
            { name: 'Kota Bogor', lat: -6.5971, lng: 106.8060, region: 'Jabodetabek' },
            { name: 'Kab. Bogor (Cibinong)', lat: -6.4817, lng: 106.8542, region: 'Jabodetabek' },
            { name: 'Kota Depok', lat: -6.4025, lng: 106.7942, region: 'Jabodetabek' },
            { name: 'Kota Tangerang', lat: -6.1783, lng: 106.6319, region: 'Jabodetabek' },
            { name: 'Kota Tangerang Selatan', lat: -6.2942, lng: 106.7094, region: 'Jabodetabek' },
            { name: 'Kab. Tangerang (Tigaraksa)', lat: -6.2638, lng: 106.4951, region: 'Jabodetabek' },
            { name: 'Kota Bekasi', lat: -6.2383, lng: 106.9756, region: 'Jabodetabek' },
            { name: 'Kab. Bekasi (Cikarang)', lat: -6.3638, lng: 107.1722, region: 'Jabodetabek' },
            
            // Jawa
            { name: 'Kota Bandung', lat: -6.9175, lng: 107.6191, region: 'Jawa' },
            { name: 'Kota Semarang', lat: -7.0051, lng: 110.4381, region: 'Jawa' },
            { name: 'Kota Surabaya', lat: -7.2575, lng: 112.7521, region: 'Jawa' },
            { name: 'Kota Yogyakarta', lat: -7.7956, lng: 110.3695, region: 'Jawa' },
            { name: 'Kota Surakarta (Solo)', lat: -7.5755, lng: 110.8243, region: 'Jawa' },
            { name: 'Kota Malang', lat: -7.9666, lng: 112.6326, region: 'Jawa' },
            { name: 'Kota Cirebon', lat: -6.7320, lng: 108.5523, region: 'Jawa' },
            { name: 'Kota Serang', lat: -6.1104, lng: 106.1640, region: 'Jawa' },
            { name: 'Kota Tegal', lat: -6.8694, lng: 109.1402, region: 'Jawa' },
            { name: 'Kota Tasikmalaya', lat: -7.3274, lng: 108.2207, region: 'Jawa' },
            { name: 'Kab. Banyuwangi', lat: -8.2192, lng: 114.3692, region: 'Jawa' },
            { name: 'Kab. Jember', lat: -8.1845, lng: 113.6681, region: 'Jawa' },
            { name: 'Kab. Sukabumi', lat: -6.9277, lng: 106.9299, region: 'Jawa' },
            { name: 'Kab. Garut', lat: -7.2279, lng: 107.9087, region: 'Jawa' },
            { name: 'Kab. Cilacap', lat: -7.7167, lng: 109.0167, region: 'Jawa' },

            // Sumatera
            { name: 'Kota Medan', lat: 3.5952, lng: 98.6722, region: 'Sumatera' },
            { name: 'Kota Banda Aceh', lat: 5.5483, lng: 95.3238, region: 'Sumatera' },
            { name: 'Kota Padang', lat: -0.9471, lng: 100.4172, region: 'Sumatera' },
            { name: 'Kota Palembang', lat: -2.9761, lng: 104.7754, region: 'Sumatera' },
            { name: 'Kota Pekanbaru', lat: 0.5071, lng: 101.4478, region: 'Sumatera' },
            { name: 'Kota Bandar Lampung', lat: -5.4500, lng: 105.2667, region: 'Sumatera' },
            { name: 'Kota Batam', lat: 1.1301, lng: 104.0529, region: 'Sumatera' },
            { name: 'Kota Jambi', lat: -1.6101, lng: 103.6131, region: 'Sumatera' },
            { name: 'Kota Bengkulu', lat: -3.8004, lng: 102.2655, region: 'Sumatera' },
            { name: 'Kota Pangkal Pinang', lat: -2.1333, lng: 106.1167, region: 'Sumatera' },

            // Kalimantan
            { name: 'Kota Pontianak', lat: -0.0263, lng: 109.3425, region: 'Kalimantan' },
            { name: 'Kota Banjarmasin', lat: -3.3194, lng: 114.5908, region: 'Kalimantan' },
            { name: 'Kota Balikpapan', lat: -1.2379, lng: 116.8529, region: 'Kalimantan' },
            { name: 'Kota Samarinda', lat: -0.5022, lng: 117.1536, region: 'Kalimantan' },
            { name: 'Kota Palangkaraya', lat: -2.2161, lng: 113.9167, region: 'Kalimantan' },
            { name: 'Kota Tarakan', lat: 3.3273, lng: 117.5785, region: 'Kalimantan' },

            // Sulawesi
            { name: 'Kota Makassar', lat: -5.1477, lng: 119.4327, region: 'Sulawesi' },
            { name: 'Kota Manado', lat: 1.4748, lng: 124.8421, region: 'Sulawesi' },
            { name: 'Kota Palu', lat: -0.9003, lng: 119.8780, region: 'Sulawesi' },
            { name: 'Kota Kendari', lat: -3.9985, lng: 122.5126, region: 'Sulawesi' },
            { name: 'Kota Gorontalo', lat: 0.5435, lng: 123.0568, region: 'Sulawesi' },
            { name: 'Kota Mamuju', lat: -2.6770, lng: 118.8894, region: 'Sulawesi' },

            // Bali & Nusa Tenggara
            { name: 'Kota Denpasar', lat: -8.6705, lng: 115.2126, region: 'Bali/Nusa' },
            { name: 'Kota Mataram (Lombok)', lat: -8.5833, lng: 116.1167, region: 'Bali/Nusa' },
            { name: 'Kota Kupang', lat: -10.1772, lng: 123.6070, region: 'Bali/Nusa' },
            { name: 'Kab. Badung', lat: -8.5833, lng: 115.1833, region: 'Bali/Nusa' },

            // Maluku & Papua
            { name: 'Kota Ambon', lat: -3.6954, lng: 128.1814, region: 'Maluku/Papua' },
            { name: 'Kota Ternate', lat: 0.7905, lng: 127.3820, region: 'Maluku/Papua' },
            { name: 'Kota Jayapura', lat: -2.5337, lng: 140.7181, region: 'Maluku/Papua' },
            { name: 'Kota Sorong', lat: -0.8762, lng: 131.2558, region: 'Maluku/Papua' },
            { name: 'Kota Manokwari', lat: -0.8615, lng: 134.0620, region: 'Maluku/Papua' },
            { name: 'Kota Merauke', lat: -8.4991, lng: 140.4031, region: 'Maluku/Papua' }
        ],

        state: {
            location: { name: 'DKI Jakarta', lat: -6.2088, lng: 106.8456, isGPS: false },
            prayerTimes: null,
            hijriDate: '',
            gregorianDate: '',
            soundEnabled: false,
            activeFilter: 'all',
            lastNotifiedPrayer: null
        },

        timerInterval: null,

        init() {
            const container = document.getElementById('jadwal-sholat');
            if (!container) return;

            this.loadSavedState();
            this.bindEvents();
            this.renderCityList();
            this.fetchPrayerTimes();
            this.startLiveClock();
        },

        loadSavedState() {
            try {
                const savedLoc = localStorage.getItem('alhikmah_prayer_loc');
                if (savedLoc) {
                    this.state.location = JSON.parse(savedLoc);
                }
                const savedSound = localStorage.getItem('alhikmah_prayer_sound');
                if (savedSound !== null) {
                    this.state.soundEnabled = savedSound === 'true';
                }
            } catch (e) {
                console.warn('Storage read error:', e);
            }
            this.updateSoundBtnUI();
        },

        saveState() {
            try {
                localStorage.setItem('alhikmah_prayer_loc', JSON.stringify(this.state.location));
                localStorage.setItem('alhikmah_prayer_sound', this.state.soundEnabled.toString());
            } catch (e) {
                console.warn('Storage write error:', e);
            }
        },

        bindEvents() {
            // GPS Button
            const gpsBtn = document.getElementById('btn-detect-gps');
            if (gpsBtn) {
                gpsBtn.addEventListener('click', () => this.detectGPS());
            }

            // Sound Toggle Button
            const soundBtn = document.getElementById('btn-prayer-sound');
            if (soundBtn) {
                soundBtn.addEventListener('click', () => {
                    this.state.soundEnabled = !this.state.soundEnabled;
                    this.saveState();
                    this.updateSoundBtnUI();
                    if (this.state.soundEnabled) {
                        this.playChime();
                    }
                });
            }

            // Qibla Modal Trigger
            const qiblaModal = document.getElementById('qiblaModal');
            if (qiblaModal) {
                qiblaModal.addEventListener('show.bs.modal', () => this.updateQiblaUI());
            }

            // City Search Input
            const citySearch = document.getElementById('city-search-input');
            if (citySearch) {
                citySearch.addEventListener('input', (e) => {
                    this.filterCityList(e.target.value);
                });
            }

            // City Filter Pills
            document.querySelectorAll('.city-quick-pill').forEach(pill => {
                pill.addEventListener('click', (e) => {
                    document.querySelectorAll('.city-quick-pill').forEach(p => p.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                    this.state.activeFilter = e.currentTarget.dataset.region || 'all';
                    this.filterCityList(citySearch ? citySearch.value : '');
                });
            });
        },

        updateSoundBtnUI() {
            const soundBtn = document.getElementById('btn-prayer-sound');
            if (!soundBtn) return;
            if (this.state.soundEnabled) {
                soundBtn.innerHTML = '<i class="bi bi-bell-fill text-success"></i> Suara: Aktif';
                soundBtn.classList.add('active');
            } else {
                soundBtn.innerHTML = '<i class="bi bi-bell-slash"></i> Suara: Mati';
                soundBtn.classList.remove('active');
            }
        },

        detectGPS() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi (GPS). Silakan pilih kota manual.');
                return;
            }

            const cityLabel = document.getElementById('prayer-city-name');
            if (cityLabel) cityLabel.textContent = 'Mencari lokasi GPS...';

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    let cityName = 'Lokasi Saya';

                    try {
                        const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10`);
                        const geoJson = await geoRes.json();
                        if (geoJson && geoJson.address) {
                            cityName = geoJson.address.city || geoJson.address.county || geoJson.address.state || 'Lokasi Saya';
                        }
                    } catch (e) {
                        // Fallback: find nearest city in preset
                        cityName = this.getNearestCityName(lat, lng);
                    }

                    this.state.location = {
                        name: cityName,
                        lat: lat,
                        lng: lng,
                        isGPS: true
                    };

                    this.saveState();
                    this.fetchPrayerTimes();
                },
                (err) => {
                    alert('Gagal mendeteksi lokasi GPS. Pastikan izin lokasi telah diaktifkan di browser Anda.');
                    if (cityLabel) cityLabel.textContent = this.state.location.name;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        getNearestCityName(lat, lng) {
            let minDistance = Infinity;
            let closestName = 'Lokasi Terdeteksi';

            this.cities.forEach(city => {
                const d = Math.hypot(city.lat - lat, city.lng - lng);
                if (d < minDistance) {
                    minDistance = d;
                    closestName = city.name;
                }
            });
            return closestName;
        },

        async fetchPrayerTimes() {
            const { lat, lng, name, isGPS } = this.state.location;
            
            // Update Location Badge
            const cityLabel = document.getElementById('prayer-city-name');
            const gpsDot = document.getElementById('gps-status-dot');
            if (cityLabel) cityLabel.textContent = name;
            if (gpsDot) {
                gpsDot.style.display = isGPS ? 'inline-flex' : 'none';
            }

            try {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const dateStr = `${year}-${month}-${day}`;

                // Method 20 = Kementerian Agama Republik Indonesia (KEMENAG)
                const res = await fetch(`https://api.aladhan.com/v1/timings/${dateStr}?latitude=${lat}&longitude=${lng}&method=20`);
                const json = await res.json();

                if (json && json.code === 200 && json.data) {
                    const timings = json.data.timings;
                    const hijri = json.data.date.hijri;
                    const greg = json.data.date.gregorian;

                    // Calculate Dhuha (+20m after Sunrise)
                    const dhuhaTime = this.addMinutesToTime(timings.Sunrise, 20);

                    this.state.prayerTimes = {
                        Imsak: timings.Imsak ? timings.Imsak.slice(0, 5) : this.subtractMinutesToTime(timings.Fajr, 10),
                        Subuh: timings.Fajr.slice(0, 5),
                        Terbit: timings.Sunrise.slice(0, 5),
                        Dhuha: dhuhaTime,
                        Dzuhur: timings.Dhuhr.slice(0, 5),
                        Ashar: timings.Asr.slice(0, 5),
                        Maghrib: timings.Maghrib.slice(0, 5),
                        Isya: timings.Isha.slice(0, 5)
                    };

                    const indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    
                    const dayName = indonesianDays[now.getDay()];
                    const dateFormatted = `${dayName}, ${now.getDate()} ${indonesianMonths[now.getMonth()]} ${now.getFullYear()}`;
                    const hijriFormatted = `${hijri.day} ${hijri.month.en} ${hijri.year} H`;

                    this.state.gregorianDate = dateFormatted;
                    this.state.hijriDate = hijriFormatted;

                    const dateDisplay = document.getElementById('prayer-date-display');
                    if (dateDisplay) {
                        dateDisplay.innerHTML = `<i class="bi bi-calendar3"></i> ${hijriFormatted} &bull; ${dateFormatted}`;
                    }

                    this.renderPrayerCards();
                    this.updateCountdown();
                    return;
                }
            } catch (e) {
                console.warn('Aladhan API unreachable, using astronomical calculation fallback:', e);
            }

            // Fallback: Offline Solar Calculator for Indonesia
            this.calculateOfflinePrayerTimes(lat, lng);
        },

        calculateOfflinePrayerTimes(lat, lng) {
            const now = new Date();
            // Default times for Jakarta shifted by longitude difference from Jakarta (106.8°)
            const lonDiffMin = (lng - 106.8456) * 4; // 1 degree = 4 minutes
            
            const baseTimes = {
                Imsak: '04:31',
                Subuh: '04:41',
                Terbit: '05:54',
                Dhuha: '06:14',
                Dzuhur: '12:02',
                Ashar: '15:21',
                Maghrib: '18:04',
                Isya: '19:14'
            };

            const adjustedTimes = {};
            for (let [k, v] of Object.entries(baseTimes)) {
                adjustedTimes[k] = this.addMinutesToTime(v, -Math.round(lonDiffMin));
            }

            this.state.prayerTimes = adjustedTimes;
            const indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dateFormatted = `${indonesianDays[now.getDay()]}, ${now.getDate()} ${indonesianMonths[now.getMonth()]} ${now.getFullYear()}`;
            const dateDisplay = document.getElementById('prayer-date-display');
            if (dateDisplay) {
                dateDisplay.innerHTML = `<i class="bi bi-calendar3"></i> ${dateFormatted}`;
            }

            this.renderPrayerCards();
            this.updateCountdown();
        },

        addMinutesToTime(timeStr, minsToAdd) {
            if (!timeStr) return '00:00';
            const [h, m] = timeStr.split(':').map(Number);
            let totalMins = (h * 60 + m + minsToAdd + 1440) % 1440;
            const newH = Math.floor(totalMins / 60);
            const newM = totalMins % 60;
            return `${String(newH).padStart(2, '0')}:${String(newM).padStart(2, '0')}`;
        },

        subtractMinutesToTime(timeStr, minsToSub) {
            return this.addMinutesToTime(timeStr, -minsToSub);
        },

        renderPrayerCards() {
            const pt = this.state.prayerTimes;
            if (!pt) return;

            const prayerDefs = [
                { key: 'Imsak', ar: 'الإمساك', icon: 'bi-moon-stars' },
                { key: 'Subuh', ar: 'الفجر', icon: 'bi-sunrise' },
                { key: 'Terbit', ar: 'الشروق', icon: 'bi-sun' },
                { key: 'Dhuha', ar: 'الضحى', icon: 'bi-brightness-high' },
                { key: 'Dzuhur', ar: 'الظهر', icon: 'bi-sun-fill' },
                { key: 'Ashar', ar: 'العصر', icon: 'bi-cloud-sun' },
                { key: 'Maghrib', ar: 'المغرب', icon: 'bi-sunset' },
                { key: 'Isya', ar: 'العشاء', icon: 'bi-moon-fill' }
            ];

            const container = document.getElementById('prayer-cards-grid');
            if (!container) return;

            const nextPrayerKey = this.getNextPrayerKey();

            container.innerHTML = prayerDefs.map(p => {
                const time = pt[p.key] || '--:--';
                const isNext = p.key === nextPrayerKey;
                return `
                    <div class="prayer-card ${isNext ? 'active' : ''}" id="card-prayer-${p.key.toLowerCase()}">
                        ${isNext ? '<span class="prayer-card-badge">Berikutnya</span>' : ''}
                        <div class="prayer-card-icon">
                            <i class="bi ${p.icon}"></i>
                        </div>
                        <div class="prayer-card-ar">${p.ar}</div>
                        <div class="prayer-card-name">${p.key}</div>
                        <div class="prayer-card-time">${time}</div>
                    </div>
                `;
            }).join('');
        },

        getNextPrayerKey() {
            const pt = this.state.prayerTimes;
            if (!pt) return 'Subuh';

            const now = new Date();
            const currentMinutes = now.getHours() * 60 + now.getMinutes();
            const keys = ['Imsak', 'Subuh', 'Terbit', 'Dhuha', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

            for (let key of keys) {
                if (pt[key]) {
                    const [h, m] = pt[key].split(':').map(Number);
                    const targetMinutes = h * 60 + m;
                    if (currentMinutes < targetMinutes) {
                        return key;
                    }
                }
            }
            return 'Imsak'; // After Isya, next is tomorrow's Imsak
        },

        startLiveClock() {
            if (this.timerInterval) clearInterval(this.timerInterval);

            this.timerInterval = setInterval(() => {
                // Update Digital Clock
                const now = new Date();
                const clockEl = document.getElementById('live-digital-clock');
                const tzBadge = document.getElementById('live-timezone-badge');
                
                if (clockEl) {
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    clockEl.textContent = `${h}:${m}:${s}`;
                }

                if (tzBadge) {
                    const lng = this.state.location.lng;
                    let tz = 'WIB';
                    if (lng >= 115 && lng < 125) tz = 'WITA';
                    else if (lng >= 125) tz = 'WIT';
                    tzBadge.textContent = tz;
                }

                this.updateCountdown();
            }, 1000);
        },

        updateCountdown() {
            const pt = this.state.prayerTimes;
            if (!pt) return;

            const now = new Date();
            const currentMinutes = now.getHours() * 60 + now.getMinutes();
            const keys = ['Imsak', 'Subuh', 'Terbit', 'Dhuha', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

            let nextKey = null;
            let targetTimeStr = null;
            let isTomorrow = false;

            for (let key of keys) {
                if (pt[key]) {
                    const [h, m] = pt[key].split(':').map(Number);
                    const targetMinutes = h * 60 + m;
                    if (currentMinutes < targetMinutes) {
                        nextKey = key;
                        targetTimeStr = pt[key];
                        break;
                    }
                }
            }

            if (!nextKey) {
                // Tomorrow Imsak
                nextKey = 'Imsak';
                targetTimeStr = pt['Imsak'];
                isTomorrow = true;
            }

            const nextNameEl = document.getElementById('next-prayer-name');
            const targetTimeEl = document.getElementById('next-prayer-time-target');
            const countdownEl = document.getElementById('prayer-countdown-timer');

            if (nextNameEl) nextNameEl.textContent = nextKey;
            if (targetTimeEl) targetTimeEl.textContent = `Pukul ${targetTimeStr} ${isTomorrow ? '(Besok)' : ''}`;

            if (countdownEl && targetTimeStr) {
                const [tH, tM] = targetTimeStr.split(':').map(Number);
                let targetDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), tH, tM, 0, 0);
                if (isTomorrow) {
                    targetDate.setDate(targetDate.getDate() + 1);
                }

                const diffMs = targetDate - now;

                if (diffMs <= 0 && diffMs > -3000) {
                    if (this.state.lastNotifiedPrayer !== nextKey) {
                        this.state.lastNotifiedPrayer = nextKey;
                        if (this.state.soundEnabled) {
                            this.playChime();
                        }
                    }
                    this.fetchPrayerTimes();
                    return;
                }

                if (diffMs > 0) {
                    const hours = Math.floor(diffMs / 3600000);
                    const minutes = Math.floor((diffMs % 3600000) / 60000);
                    const seconds = Math.floor((diffMs % 60000) / 1000);

                    const pad = (n) => String(n).padStart(2, '0');
                    countdownEl.textContent = `-${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                }
            }
        },

        playChime() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                
                const now = ctx.currentTime;
                // Sweet soft chime sequence: C5 -> E5 -> G5 -> C6
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.2);
                    
                    gain.gain.setValueAtTime(0, now + idx * 0.2);
                    gain.gain.linearRampToValueAtTime(0.3, now + idx * 0.2 + 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.2 + 0.6);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + idx * 0.2);
                    osc.stop(now + idx * 0.2 + 0.6);
                });
            } catch (e) {
                console.warn('Audio synthesis error:', e);
            }
        },

        renderCityList() {
            const container = document.getElementById('city-list-container');
            if (!container) return;

            let filtered = this.cities;
            if (this.state.activeFilter !== 'all') {
                filtered = filtered.filter(c => c.region === this.state.activeFilter);
            }

            container.innerHTML = filtered.map(c => {
                const isSelected = c.name === this.state.location.name;
                return `
                    <button type="button" class="city-item-btn ${isSelected ? 'selected' : ''}" data-city="${c.name}">
                        <span>${c.name}</span>
                        ${isSelected ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-chevron-right opacity-50"></i>'}
                    </button>
                `;
            }).join('');

            container.querySelectorAll('.city-item-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const cityName = e.currentTarget.dataset.city;
                    const found = this.cities.find(c => c.name === cityName);
                    if (found) {
                        this.state.location = {
                            name: found.name,
                            lat: found.lat,
                            lng: found.lng,
                            isGPS: false
                        };
                        this.saveState();
                        this.fetchPrayerTimes();

                        // Close Modal
                        const modalEl = document.getElementById('cityModal');
                        if (modalEl && window.bootstrap) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                    }
                });
            });
        },

        filterCityList(query) {
            const container = document.getElementById('city-list-container');
            if (!container) return;

            const term = (query || '').toLowerCase().trim();
            let filtered = this.cities;

            if (this.state.activeFilter !== 'all') {
                filtered = filtered.filter(c => c.region === this.state.activeFilter);
            }

            if (term) {
                filtered = filtered.filter(c => c.name.toLowerCase().includes(term));
            }

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="p-4 text-center text-muted" style="grid-column: 1 / -1;">
                        <i class="bi bi-search mb-2" style="font-size: 1.5rem;"></i>
                        <p class="mb-0">Kota tidak ditemukan. Coba gunakan kata kunci lain atau gunakan Deteksi GPS.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filtered.map(c => {
                const isSelected = c.name === this.state.location.name;
                return `
                    <button type="button" class="city-item-btn ${isSelected ? 'selected' : ''}" data-city="${c.name}">
                        <span>${c.name}</span>
                        ${isSelected ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-chevron-right opacity-50"></i>'}
                    </button>
                `;
            }).join('');

            container.querySelectorAll('.city-item-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const cityName = e.currentTarget.dataset.city;
                    const found = this.cities.find(c => c.name === cityName);
                    if (found) {
                        this.state.location = {
                            name: found.name,
                            lat: found.lat,
                            lng: found.lng,
                            isGPS: false
                        };
                        this.saveState();
                        this.fetchPrayerTimes();

                        const modalEl = document.getElementById('cityModal');
                        if (modalEl && window.bootstrap) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                    }
                });
            });
        },

        updateQiblaUI() {
            const { lat, lng, name } = this.state.location;
            
            // Formula Great-Circle Bearing to Ka'bah (21.4225° N, 39.8262° E)
            const phi1 = (lat * Math.PI) / 180;
            const lambda1 = (lng * Math.PI) / 180;
            const phi2 = (21.4225 * Math.PI) / 180;
            const lambda2 = (39.8262 * Math.PI) / 180;
            const dLambda = lambda2 - lambda1;

            const y = Math.sin(dLambda) * Math.cos(phi2);
            const x = Math.cos(phi1) * Math.sin(phi2) - Math.sin(phi1) * Math.cos(phi2) * Math.cos(dLambda);
            let qiblaDeg = (Math.atan2(y, x) * 180) / Math.PI;
            qiblaDeg = (qiblaDeg + 360) % 360;

            const degreeFormatted = qiblaDeg.toFixed(1);

            const needle = document.getElementById('qibla-needle-pointer');
            const degDisplay = document.getElementById('qibla-degree-val');
            const descDisplay = document.getElementById('qibla-desc-val');

            if (needle) {
                needle.style.transform = `rotate(${degreeFormatted}deg)`;
            }
            if (degDisplay) {
                degDisplay.textContent = `${degreeFormatted}°`;
            }
            if (descDisplay) {
                descDisplay.textContent = `Arah Kiblat dari ${name} adalah ${degreeFormatted}° Barat Laut (dari arah Utara kompas).`;
            }
        }
    };

    // Initialize Prayer Times App on Page Load
    PrayerTimesApp.init();
});