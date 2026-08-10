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
            // Dark Mode Toggle - with Transition
            // ============================================
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;

            function setTheme(isDark) {
                if (!html || !themeIcon) return;

                // Add transition class
                html.style.transition = 'background-color 0.3s ease, color 0.3s ease';

                html.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
                themeIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-fill';

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

            // Check saved theme
            try {
                const savedTheme = localStorage.getItem('alhikmah-theme');
                if (savedTheme === 'dark') setTheme(true);
            } catch (e) {
                // localStorage not available
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    const isDark = html.getAttribute('data-bs-theme') === 'dark';
                    setTheme(!isDark);
                });

                // Accessibility: Add aria-label
                themeToggle.setAttribute('aria-label', 'Ganti tema gelap/terang');
            }

            // ============================================
            // Navbar Scroll Effect - Throttled
            // ============================================
            const navbar = document.getElementById('mainNavbar');
            let scrollTicking = false;

            function updateNavbar() {
                if (!navbar) return;

                if (window.scrollY > 50) {
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
                    if (targetId === '#') return;

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
                        if (navCollapse && navCollapse.classList.contains('show')) {
                            const bsCollapse = bootstrap.Collapse.getInstance(navCollapse) || new bootstrap.Collapse(navCollapse, { toggle: true });
                            bsCollapse.hide();
                        }
                    }
                });
            });

            // ============================================
            // Active Nav Link on Scroll - Throttled
            // ============================================
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            let activeTicking = false;

            function updateActiveNav() {
                let current = '';
                const scrollPos = window.scrollY + 100;

                sections.forEach(function (section) {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (scrollPos >= sectionTop && scrollPos <= sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(function (link) {
                    link.classList.remove('active');
                });

                if (current) {
                    navLinks.forEach(function (link) {
                        const href = link.getAttribute('href');
                        if (href === '#' + current && !link.classList.contains('dropdown-toggle')) {
                            link.classList.add('active');
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
            // ============================================
            // Form Submission - Validation & WhatsApp Format Fix
            // ============================================
            const registrationForm = document.getElementById('registrationForm');
            if (registrationForm) {
                registrationForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Clean previous feedback messages
                    this.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                    const inputs = this.querySelectorAll('input[required], select[required]');
                    let isValid = true;

                    inputs.forEach(function (input) {
                        if (!input.value.trim()) {
                            isValid = false;
                            input.classList.add('is-invalid');

                            // Add contextual error text
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Wajib diisi';
                            input.parentNode.appendChild(feedback);
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (isValid) {
                        const nama = this.querySelector('[name="nama"]')?.value || '-';
                        const whatsapp = this.querySelector('[name="whatsapp"]')?.value || '-';
                        const usia = this.querySelector('[name="usia"]')?.value || '-';
                        const lokasi = this.querySelector('[name="lokasi"]')?.value || '-';
                        const program = this.querySelector('[name="program"]')?.value || '-';
                        const metode = this.querySelector('[name="metode"]')?.value || '-';
                        
                        // Gunakan karakter \n asli agar encodeURIComponent menghasilkan %0A yang valid
                        let message = 'Assalamualaikum warahmatullahi wabarakatuh,\n\n';
                        message += 'Saya ingin mendaftar program belajar di AL-HIKMAH:\n\n';
                        message += '📛 Nama: ' + nama + '\n';
                        message += '📱 WhatsApp: ' + whatsapp + '\n';
                        message += '🎂 Usia: ' + usia + '\n';
                        message += '📍 Lokasi: ' + lokasi + '\n';
                        message += '📚 Program: ' + program + '\n';
                        message += '💻 Metode: ' + metode + '\n\n';
                        message += 'Mohon info lebih lanjut. Jazakallahu khairan.';
                        
                        const waNumber = '6285786689008';
                        window.open('https://wa.me/' + waNumber + '?text=' + encodeURIComponent(message), '_blank');
                        
                        // Close modal
                        const modalElement = document.getElementById('daftarModal');
                        if (modalElement && typeof bootstrap !== 'undefined') {
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            if (modal) modal.hide();
                        }
                        
                        // Reset form state
                        this.reset();
                        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        this.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
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
        });

        // Add Skip to Main Content link dynamically for accessibility
        (function () {
            const skipLink = document.createElement('a');
            skipLink.href = '#beranda';
            skipLink.className = 'skip-to-main';
            skipLink.textContent = 'Langsung ke konten utama';
            document.body.insertBefore(skipLink, document.body.firstChild);
        })();