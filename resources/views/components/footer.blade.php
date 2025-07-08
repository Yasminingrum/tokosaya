<!-- Modern Minimalist Footer -->
<footer class="bg-gray-900 text-white">
    <!-- Newsletter Section - Simplified -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-16">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-2xl mx-auto space-y-6" data-aos="fade-up">
                <h2 class="text-3xl font-bold">Dapatkan Update Terbaru</h2>
                <p class="text-blue-100">Berlangganan untuk penawaran eksklusif dan produk terbaru</p>

                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto"
                      x-data="{ email: '', loading: false }"
                      @submit.prevent="subscribeNewsletter()">
                    @csrf
                    <input type="email"
                           x-model="email"
                           placeholder="Email Anda"
                           class="flex-1 px-6 py-3 rounded-full text-gray-900 focus:ring-2 focus:ring-white focus:outline-none"
                           required>
                    <button type="submit"
                            :disabled="loading"
                            class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-colors disabled:opacity-50">
                        <span x-show="!loading">Berlangganan</span>
                        <span x-show="loading">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Footer Content - Clean Layout -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <!-- Company Info -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">TokoSaya</h2>
                            <p class="text-sm text-gray-400">Belanja Mudah & Terpercaya</p>
                        </div>
                    </div>

                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Platform e-commerce modern yang menghadirkan pengalaman berbelanja online terbaik dengan produk berkualitas dan pelayanan terpercaya.
                    </p>

                    <!-- Social Media - Minimalist -->
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors group">
                            <i class="fab fa-instagram text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors group">
                            <i class="fab fa-facebook text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-400 transition-colors group">
                            <i class="fab fa-twitter text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors group">
                            <i class="fab fa-youtube text-gray-400 group-hover:text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Navigasi</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="footer-link">Beranda</a></li>
                        <li><a href="{{ route('products.index') }}" class="footer-link">Semua Produk</a></li>
                        <li><a href="{{ route('categories.index') }}" class="footer-link">Kategori</a></li>
                        <li><a href="{{ route('brands.index') }}" class="footer-link">Brand</a></li>
                        <li><a href="{{ route('products.index', ['featured' => 1]) }}" class="footer-link">Produk Unggulan</a></li>
                        <li><a href="{{ route('products.index', ['sale' => 1]) }}" class="footer-link">Promo & Diskon</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Bantuan</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('contact') }}" class="footer-link">Hubungi Kami</a></li>
                        <li><a href="{{ route('faq') }}" class="footer-link">FAQ</a></li>
                        <li><a href="#" class="footer-link">Panduan Belanja</a></li>
                        <li><a href="#" class="footer-link">Kebijakan Return</a></li>
                        <li><a href="#" class="footer-link">Garansi Produk</a></li>
                        <li><a href="#" class="footer-link">Lacak Pesanan</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Kontak</h3>
                    <div class="space-y-4">

                        <!-- Customer Service -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-headset text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 text-sm">Customer Service</p>
                                <a href="tel:+6280412345678" class="text-white font-medium hover:text-blue-400 transition-colors">
                                    0804-1-234-5678
                                </a>
                                <p class="text-xs text-gray-400">24/7 - Gratis</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-envelope text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 text-sm">Email Support</p>
                                <a href="mailto:support@tokosaya.id" class="text-white font-medium hover:text-green-400 transition-colors">
                                    support@tokosaya.id
                                </a>
                                <p class="text-xs text-gray-400">Respon dalam 24 jam</p>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fab fa-whatsapp text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 text-sm">WhatsApp</p>
                                <a href="https://wa.me/6281234567890" target="_blank" class="text-white font-medium hover:text-green-400 transition-colors">
                                    +62 812-3456-7890
                                </a>
                                <p class="text-xs text-gray-400">Chat langsung</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment & Shipping - Compact -->
    <div class="border-t border-gray-800 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Payment Methods -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">Metode Pembayaran</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['visa', 'mastercard', 'bca', 'mandiri', 'bni', 'gopay', 'ovo', 'dana'] as $payment)
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-12 h-8">
                            <img src="{{ asset("images/payments/{$payment}.png") }}" alt="{{ ucfirst($payment) }}" class="h-4 object-contain">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Partners -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">Partner Pengiriman</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['jne', 'jnt', 'sicepat', 'pos', 'tiki', 'anteraja'] as $shipping)
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-12 h-8">
                            <img src="{{ asset("images/shipping/{$shipping}.png") }}" alt="{{ strtoupper($shipping) }}" class="h-4 object-contain">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Badges - Minimal -->
    <div class="border-t border-gray-800 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                <!-- Trust Indicators -->
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-green-400"></i>
                        <span class="text-gray-300">100% Original</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-lock text-blue-400"></i>
                        <span class="text-gray-300">SSL Secure</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-undo text-yellow-400"></i>
                        <span class="text-gray-300">30 Hari Return</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shipping-fast text-purple-400"></i>
                        <span class="text-gray-300">Gratis Ongkir</span>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/certifications/ssl.png') }}" alt="SSL Certificate" class="h-8 opacity-80">
                    <img src="{{ asset('images/certifications/verified.png') }}" alt="Verified" class="h-8 opacity-80">
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer - Clean -->
    <div class="border-t border-gray-800 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm">

                <!-- Copyright -->
                <div class="text-center md:text-left">
                    <p class="text-gray-400">
                        © {{ date('Y') }} TokoSaya. All rights reserved.
                    </p>
                </div>

                <!-- Legal Links -->
                <div class="flex flex-wrap items-center gap-6">
                    <a href="{{ route('privacy-policy') }}" class="footer-link">Kebijakan Privasi</a>
                    <a href="{{ route('terms-of-service') }}" class="footer-link">Syarat & Ketentuan</a>
                    <a href="{{ route('about') }}" class="footer-link">Tentang Kami</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button - Modern -->
    <button id="back-to-top"
            class="fixed bottom-8 right-8 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 transform translate-y-16 opacity-0 z-50 hover:scale-110"
            onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

@push('scripts')
<script>
// Newsletter subscription with better UX
function subscribeNewsletter() {
    const form = event.target;
    const email = form.querySelector('input[type="email"]').value;
    const button = form.querySelector('button[type="submit"]');

    // Set loading state
    this.loading = true;

    fetch('/api/newsletter/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showFooterNotification('Berhasil berlangganan newsletter!', 'success');
            form.reset();
            this.email = '';
        } else {
            showFooterNotification(data.message || 'Gagal berlangganan', 'error');
        }
    })
    .catch(error => {
        console.error('Newsletter error:', error);
        showFooterNotification('Terjadi kesalahan, silakan coba lagi', 'error');
    })
    .finally(() => {
        this.loading = false;
    });
}

// Back to top with smooth animation
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
        backToTopBtn.classList.remove('translate-y-16', 'opacity-0');
        backToTopBtn.classList.add('translate-y-0', 'opacity-100');
        isBackToTopVisible = true;
    } else if (!shouldShow && isBackToTopVisible) {
        backToTopBtn.classList.add('translate-y-16', 'opacity-0');
        backToTopBtn.classList.remove('translate-y-0', 'opacity-100');
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

// Footer notification system
function showFooterNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-8 left-8 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-y-16 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' :
        'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.remove('translate-y-16');
        notification.classList.add('translate-y-0');
    }, 100);

    setTimeout(() => {
        notification.classList.add('translate-y-16');
        notification.classList.remove('translate-y-0');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Lazy loading for footer images
if ('IntersectionObserver' in window) {
    const lazyImages = document.querySelectorAll('footer img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
}

// Footer animation on scroll
const footerElements = document.querySelectorAll('footer .container > div > div');
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

    footerElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
        footerObserver.observe(el);
    });
}

// Newsletter form validation
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('form[x-data]');
    if (newsletterForm) {
        const emailInput = newsletterForm.querySelector('input[type="email"]');

        emailInput.addEventListener('input', function() {
            const email = this.value;
            const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

            if (email.length > 0) {
                if (isValid) {
                    this.classList.remove('ring-red-300');
                    this.classList.add('ring-green-300');
                } else {
                    this.classList.remove('ring-green-300');
                    this.classList.add('ring-red-300');
                }
            } else {
                this.classList.remove('ring-red-300', 'ring-green-300');
            }
        });
    }
});

// Link tracking for analytics
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

// Export for global use
window.showFooterNotification = showFooterNotification;
</script>
@endpush

@push('styles')
<style>
/* Modern Footer Styles */
.footer-link {
    @apply text-gray-300 hover:text-white transition-colors duration-200 text-sm;
}

.footer-link:hover {
    @apply text-white;
}

/* Newsletter form styles */
.newsletter-input {
    @apply transition-all duration-300;
}

.newsletter-input:focus {
    @apply ring-2 ring-white ring-opacity-50;
}

/* Back to top button */
#back-to-top {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#back-to-top:hover {
    transform: translateY(0) scale(1.1);
}

/* Social media icons hover effect */
.social-icon {
    @apply transition-all duration-300;
}

.social-icon:hover {
    @apply transform scale-110;
}

/* Payment and shipping logos */
.payment-logo,
.shipping-logo {
    @apply transition-opacity duration-300 hover:opacity-80;
    filter: grayscale(0.2);
}

.payment-logo:hover,
.shipping-logo:hover {
    filter: grayscale(0);
}

/* Trust badges animation */
.trust-badge {
    @apply transition-all duration-300;
}

.trust-badge:hover {
    @apply transform scale-105;
}

/* Newsletter section gradient overlay */
.newsletter-section {
    position: relative;
    overflow: hidden;
}

.newsletter-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
    pointer-events: none;
}

/* Footer animation classes */
.footer-fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.footer-fade-in.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .footer-link {
        @apply text-base py-1;
    }

    #back-to-top {
        @apply w-10 h-10 bottom-6 right-6;
    }

    .social-icon {
        @apply w-8 h-8;
    }
}

@media (max-width: 480px) {
    .newsletter-section {
        @apply py-12;
    }

    .newsletter-section h2 {
        @apply text-2xl;
    }

    .payment-logo,
    .shipping-logo {
        @apply w-10 h-6;
    }
}

/* Dark mode enhancements */
@media (prefers-color-scheme: dark) {
    .newsletter-input {
        @apply bg-gray-800 text-white border-gray-700;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .footer-link {
        @apply underline;
    }

    .footer-link:hover {
        @apply no-underline;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .footer-fade-in,
    .social-icon,
    .trust-badge,
    #back-to-top {
        transition: none !important;
    }

    .social-icon:hover,
    .trust-badge:hover,
    #back-to-top:hover {
        transform: none !important;
    }
}

/* Custom scrollbar for footer content */
.footer-scroll::-webkit-scrollbar {
    width: 4px;
}

.footer-scroll::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}

.footer-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.footer-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Loading state for newsletter form */
.newsletter-loading {
    @apply opacity-75 pointer-events-none;
}

/* Success state animation */
@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.3);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.success-checkmark {
    animation: checkmark 0.6s ease-out;
}

/* Newsletter form focus styles */
.newsletter-form input:focus {
    @apply outline-none ring-2 ring-white ring-opacity-50;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

/* Footer grid responsive enhancement */
@media (min-width: 1024px) {
    .footer-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1fr;
        gap: 2rem;
    }
}

/* Smooth reveal animation for footer sections */
.footer-section {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.footer-section.revealed {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endpush
