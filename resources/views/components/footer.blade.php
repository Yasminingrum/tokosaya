<!-- Modern Minimalist Footer - Konsisten dengan Header dan Home -->
<footer class="bg-[var(--text-dark)] text-white py-[var(--space-3xl)] pb-[var(--space-xl)]">

    @push('styles')
    <style>
        /* Variabel CSS yang konsisten dengan home.blade.php */
        :root {
            --primary: #f8bbd9;
            --primary-dark: #f4a6cd;
            --primary-light: #fce7f1;
            --teal: #5fb3b4;
            --teal-dark: #4a9b9c;
            --teal-light: #b8e0e1;
            --cream: #fef7f0;
            --text-dark: #2d3748;
            --text-medium: #4a5568;
            --text-light: #718096;
            --text-muted: #a0aec0;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Base Styles */
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--space-md);
        }

        /* Newsletter Section */
        .newsletter-section {
            background: linear-gradient(135deg, var(--teal), var(--teal-dark));
            color: white;
            padding: var(--space-3xl) 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: var(--space-3xl);
        }

        .newsletter-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>');
            pointer-events: none;
        }

        .newsletter-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--space-md);
        }

        .newsletter-subtitle {
            font-size: 1.125rem;
            margin-bottom: var(--space-xl);
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            gap: var(--space-md);
            max-width: 400px;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1;
            padding: 0.875rem 1.25rem;
            border: none;
            border-radius: var(--radius-xl);
            font-size: 0.875rem;
            background-color: white;
            color: var(--text-dark);
            transition: var(--transition);
        }

        .newsletter-input:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .newsletter-button {
            padding: 0.875rem 1.5rem;
            background-color: var(--primary);
            color: var(--text-dark);
            border: none;
            border-radius: var(--radius-xl);
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
            white-space: nowrap;
        }

        .newsletter-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Main Footer Content */
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-2xl);
            margin-bottom: var(--space-2xl);
        }

        /* Company Info */
        .company-logo {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }

        .logo-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, var(--primary), var(--teal));
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .logo-text {
            color: white;
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .logo-subtext {
            color: #cbd5e0;
            margin: 0;
            font-size: 0.875rem;
        }

        .company-description {
            color: #cbd5e0;
            margin-bottom: var(--space-lg);
            line-height: 1.6;
        }

        /* Social Media */
        .social-links {
            display: flex;
            gap: var(--space-md);
        }

        .social-link {
            width: 2.5rem;
            height: 2.5rem;
            background-color: #4a5568;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e0;
            transition: var(--transition);
            text-decoration: none;
        }

        .social-link:hover {
            transform: translateY(-2px);
        }

        .social-link.instagram:hover {
            background-color: var(--primary);
            color: var(--text-dark);
        }

        .social-link.facebook:hover {
            background-color: var(--teal);
            color: white;
        }

        .social-link.twitter:hover {
            background-color: var(--teal);
            color: white;
        }

        .social-link.youtube:hover {
            background-color: var(--danger);
            color: white;
        }

        /* Footer Navigation */
        .footer-nav-title {
            color: white;
            margin-bottom: var(--space-lg);
            font-size: 1.125rem;
            font-weight: 600;
        }

        .footer-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-nav-item {
            margin-bottom: var(--space-sm);
        }

        .footer-nav-link {
            color: #cbd5e0;
            text-decoration: none;
            transition: var(--transition);
            display: block;
            padding: 0.25rem 0;
        }

        .footer-nav-link:hover {
            color: var(--primary);
            padding-left: 0.5rem;
        }

        /* Contact Info */
        .contact-item {
            margin-bottom: var(--space-lg);
        }

        .contact-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .contact-label {
            color: #cbd5e0;
            margin: 0;
            font-size: 0.875rem;
        }

        .contact-value {
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-value:hover {
            color: var(--primary);
        }

        .contact-subtext {
            color: #a0aec0;
            margin: 0;
            font-size: 0.75rem;
        }

        /* Payment & Shipping */
        .footer-divider {
            border-top: 1px solid #4a5568;
            padding-top: var(--space-xl);
            margin-bottom: var(--space-xl);
        }

        .methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--space-xl);
        }

        .methods-title {
            color: #cbd5e0;
            margin-bottom: var(--space-md);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .methods-container {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-sm);
        }

        .method-badge {
            background-color: white;
            border-radius: var(--radius-md);
            padding: 0.5rem;
            width: 3rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .method-badge:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .method-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-dark);
            text-transform: uppercase;
        }

        /* Trust Badges */
        .trust-badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-lg);
        }

        .trust-indicators {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-lg);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            transition: var(--transition);
        }

        .trust-item:hover {
            transform: translateY(-2px);
        }

        .trust-icon {
            font-size: 1.125rem;
        }

        .trust-text {
            color: #cbd5e0;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .certifications {
            display: flex;
            gap: var(--space-md);
        }

        .certification-badge {
            background-color: white;
            border-radius: var(--radius-md);
            padding: 0.5rem;
            transition: var(--transition);
        }

        .certification-badge:hover {
            transform: translateY(-2px);
        }

        .certification-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Bottom Footer */
        .bottom-footer {
            border-top: 1px solid #4a5568;
            padding-top: var(--space-xl);
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-lg);
        }

        .copyright {
            color: #a0aec0;
            margin: 0;
            font-size: 0.875rem;
        }

        .legal-links {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-lg);
        }

        .legal-link {
            color: #cbd5e0;
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .legal-link:hover {
            color: var(--primary);
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, var(--primary), var(--teal));
            color: white;
            border: none;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            cursor: pointer;
            transition: var(--transition);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-to-top:hover {
            transform: translateY(0) scale(1.1);
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .newsletter-form {
                flex-direction: column;
                max-width: 100%;
            }

            .newsletter-input {
                margin-bottom: var(--space-sm);
            }

            .newsletter-button {
                width: 100%;
                justify-content: center;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .trust-badges, .footer-content {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .back-to-top {
                width: 2.5rem;
                height: 2.5rem;
                bottom: 1rem;
                right: 1rem;
            }

            .footer-nav-title {
                font-size: 1rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer-animate {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Accessibility */
        .footer-nav-link:focus,
        .newsletter-button:focus,
        .back-to-top:focus,
        .social-link:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
                animation: none !important;
            }

            .back-to-top:hover {
                transform: none !important;
            }
        }
    </style>
    @endpush

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="newsletter-bg"></div>
        <div class="footer-container">
            <h2 class="newsletter-title">Dapatkan Update Terbaru</h2>
            <p class="newsletter-subtitle">Berlangganan newsletter untuk penawaran eksklusif dan produk terbaru</p>

            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                @csrf
                <input type="email" name="email" placeholder="Masukkan email Anda" required class="newsletter-input" aria-label="Masukkan email untuk berlangganan newsletter">
                <button type="submit" class="newsletter-button">
                    <i class="fas fa-paper-plane"></i> Berlangganan
                </button>
            </form>
        </div>
    </section>

    <!-- Main Footer Content -->
    <div class="footer-container">
        <div class="footer-grid">
            <!-- Company Info -->
            <div class="footer-animate">
                <div class="company-logo">
                    <div class="logo-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h3 class="logo-text">Toko<span style="color: var(--primary);">Saya</span></h3>
                        <p class="logo-subtext">Belanja Mudah & Terpercaya</p>
                    </div>
                </div>

                <p class="company-description">
                    Platform e-commerce modern yang menghadirkan pengalaman berbelanja online terbaik dengan produk berkualitas dan pelayanan terpercaya.
                </p>

                <!-- Social Media -->
                <div class="social-links">
                    <a href="#" class="social-link instagram" aria-label="Kunjungi Instagram kami">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link facebook" aria-label="Kunjungi Facebook kami">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="social-link twitter" aria-label="Kunjungi Twitter kami">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link youtube" aria-label="Kunjungi YouTube kami">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-animate">
                <h3 class="footer-nav-title">Navigasi</h3>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <a href="{{ route('home') }}" class="footer-nav-link">Beranda</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('products.index') }}" class="footer-nav-link">Semua Produk</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('categories.index') }}" class="footer-nav-link">Kategori</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('products.brand') }}" class="footer-nav-link">Brand</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('products.index', ['featured' => 1]) }}" class="footer-nav-link">Produk Unggulan</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('products.index', ['sale' => 1]) }}" class="footer-nav-link">Promo & Diskon</a>
                    </li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="footer-animate">
                <h3 class="footer-nav-title">Bantuan</h3>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <a href="{{ route('contact') }}" class="footer-nav-link">Hubungi Kami</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('faq') }}" class="footer-nav-link">FAQ</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('shopping-guide') }}" class="footer-nav-link">Panduan Belanja</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('return-policy') }}" class="footer-nav-link">Kebijakan Return</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('warranty') }}" class="footer-nav-link">Garansi Produk</a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="{{ route('track-order') }}" class="footer-nav-link">Lacak Pesanan</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-animate">
                <h3 class="footer-nav-title">Kontak</h3>

                <!-- Customer Service -->
                <div class="contact-item">
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                        <div class="contact-icon" style="background-color: var(--teal);">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <p class="contact-label">Customer Service</p>
                            <a href="tel:+6280412345678" class="contact-value">0804-1-234-5678</a>
                            <p class="contact-subtext">24/7 - Gratis</p>
                        </div>
                    </div>
                </div>

                <!-- Email Support -->
                <div class="contact-item">
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                        <div class="contact-icon" style="background-color: var(--success);">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="contact-label">Email Support</p>
                            <a href="mailto:support@tokosaya.id" class="contact-value">support@tokosaya.id</a>
                            <p class="contact-subtext">Respon dalam 24 jam</p>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="contact-item">
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                        <div class="contact-icon" style="background-color: #25d366;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="contact-label">WhatsApp</p>
                            <a href="https://wa.me/6281234567890" target="_blank" class="contact-value">+62 812-3456-7890</a>
                            <p class="contact-subtext">Chat langsung</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Shipping Methods -->
        <div class="footer-divider">
            <div class="methods-grid">
                <!-- Payment Methods -->
                <div>
                    <h4 class="methods-title">Metode Pembayaran</h4>
                    <div class="methods-container">
                        @php
                        $paymentMethods = ['visa', 'mastercard', 'bca', 'mandiri', 'bni', 'gopay', 'ovo', 'dana'];
                        @endphp
                        @foreach($paymentMethods as $payment)
                        <div class="method-badge">
                            <div class="method-text">{{ $payment }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Partners -->
                <div>
                    <h4 class="methods-title">Partner Pengiriman</h4>
                    <div class="methods-container">
                        @php
                        $shippingMethods = ['jne', 'jnt', 'sicepat', 'pos', 'tiki', 'anteraja'];
                        @endphp
                        @foreach($shippingMethods as $shipping)
                        <div class="method-badge">
                            <div class="method-text">{{ strtoupper($shipping) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust Badges -->
        <div class="footer-divider">
            <div class="trust-badges">
                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="fas fa-shield-alt trust-icon" style="color: var(--success);"></i>
                        <span class="trust-text">100% Original</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-lock trust-icon" style="color: var(--teal);"></i>
                        <span class="trust-text">SSL Secure</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-undo trust-icon" style="color: var(--warning);"></i>
                        <span class="trust-text">30 Hari Return</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-shipping-fast trust-icon" style="color: var(--primary);"></i>
                        <span class="trust-text">Gratis Ongkir</span>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="certifications">
                    <div class="certification-badge">
                        <div class="certification-text">SSL</div>
                    </div>
                    <div class="certification-badge">
                        <div class="certification-text">VERIFIED</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="bottom-footer">
            <div class="footer-content">
                <!-- Copyright -->
                <div>
                    <p class="copyright">© {{ date('Y') }} TokoSaya. All rights reserved.</p>
                </div>

                <!-- Legal Links -->
                <div class="legal-links">
                    <a href="{{ route('privacy') }}" class="legal-link">Kebijakan Privasi</a>
                    <a href="{{ route('terms') }}" class="legal-link">Syarat & Ketentuan</a>
                    <a href="{{ route('about') }}" class="legal-link">Tentang Kami</a>
                    <a href="{{ route('sitemap') }}" class="legal-link">Sitemap</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top" aria-label="Kembali ke atas" onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

@push('scripts')
<script>
// Newsletter form handling
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            button.disabled = true;

            // Let form submit naturally, but provide visual feedback
            setTimeout(() => {
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }, 3000);
        });

        // Email validation
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                const email = this.value;
                const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

                if (email.length > 0) {
                    if (isValid) {
                        this.style.borderColor = 'var(--success)';
                        this.style.boxShadow = '0 0 0 3px rgba(104, 211, 145, 0.1)';
                    } else {
                        this.style.borderColor = 'var(--danger)';
                        this.style.boxShadow = '0 0 0 3px rgba(252, 129, 129, 0.1)';
                    }
                } else {
                    this.style.borderColor = 'transparent';
                    this.style.boxShadow = 'none';
                }
            });
        }
    }

    // Back to top functionality
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Show/hide back to top button
    let isBackToTopVisible = false;

    function toggleBackToTop() {
        const backToTopBtn = document.getElementById('back-to-top');
        const shouldShow = window.pageYOffset > 300;

        if (shouldShow && !isBackToTopVisible) {
            backToTopBtn.classList.add('visible');
            isBackToTopVisible = true;
        } else if (!shouldShow && isBackToTopVisible) {
            backToTopBtn.classList.remove('visible');
            isBackToTopVisible = false;
        }
    }

    // Throttled scroll listener
    let ticking = false;

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(toggleBackToTop);
            ticking = true;
            setTimeout(() => { ticking = false; }, 100);
        }
    }

    window.addEventListener('scroll', requestTick);

    // Initialize back to top button
    toggleBackToTop();
});

// Footer animation on scroll
const footerElements = document.querySelectorAll('.footer-animate');
if ('IntersectionObserver' in window) {
    const footerObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    footerElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        footerObserver.observe(el);
    });
}

// Link tracking for analytics (optional)
document.querySelectorAll('footer a').forEach(link => {
    link.addEventListener('click', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'footer_link_click', {
                link_text: this.textContent.trim(),
                link_url: this.href
            });
        }
    });
});

// Social media tracking
document.querySelectorAll('footer a[href*="facebook"], footer a[href*="instagram"], footer a[href*="twitter"], footer a[href*="youtube"]').forEach(link => {
    link.addEventListener('click', function() {
        const platform = this.href.includes('facebook') ? 'facebook' :
                         this.href.includes('instagram') ? 'instagram' :
                         this.href.includes('twitter') ? 'twitter' :
                         'youtube';

        if (typeof gtag !== 'undefined') {
            gtag('event', 'social_media_click', {
                platform: platform,
                location: 'footer'
            });
        }
    });
});

// Handle newsletter success/error messages from session
@if(session('newsletter_success'))
    window.showNotification('{{ session('newsletter_success') }}', 'success');
@endif

@if(session('newsletter_error'))
    window.showNotification('{{ session('newsletter_error') }}', 'error');
@endif
</script>
@endpush
