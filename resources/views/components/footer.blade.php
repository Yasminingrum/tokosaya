<!-- Footer -->
<footer class="bg-gray-900 text-white mt-auto">
    <!-- Newsletter Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h3 class="text-2xl lg:text-3xl font-bold mb-4">
                    Dapatkan Penawaran Terbaik
                </h3>
                <p class="text-lg mb-8 opacity-90">
                    Berlangganan newsletter untuk mendapatkan info produk terbaru, diskon eksklusif, dan penawaran menarik lainnya
                </p>

                <form action="{{ route('newsletter.subscribe') }}" method="POST"
                      class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto"
                      x-data="{ email: '', loading: false }"
                      @submit.prevent="subscribeNewsletter()">
                    @csrf
                    <input type="email"
                           name="email"
                           x-model="email"
                           placeholder="Masukkan email Anda"
                           class="flex-1 px-6 py-3 rounded-lg text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                           required>
                    <button type="submit"
                            :disabled="loading"
                            class="bg-yellow-400 text-gray-900 px-8 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">Berlangganan</span>
                        <span x-show="loading" class="flex items-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Memproses...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Footer Content -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                TokoSaya
                            </h2>
                            <p class="text-sm text-gray-400">Belanja Mudah & Terpercaya</p>
                        </div>
                    </div>

                    <p class="text-gray-300 mb-6 leading-relaxed">
                        TokoSaya adalah platform e-commerce terpercaya yang menyediakan jutaan produk berkualitas dengan harga terbaik.
                        Kami berkomitmen memberikan pengalaman berbelanja online yang aman, mudah, dan menyenangkan.
                    </p>

                    <!-- Social Media -->
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors group">
                            <i class="fab fa-facebook-f text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-pink-600 transition-colors group">
                            <i class="fab fa-instagram text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-400 transition-colors group">
                            <i class="fab fa-twitter text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-red-600 transition-colors group">
                            <i class="fab fa-youtube text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition-colors group">
                            <i class="fab fa-whatsapp text-gray-400 group-hover:text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Link Cepat</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Semua Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categories.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('brands.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Brand Terpercaya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index', ['featured' => 1]) }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Produk Unggulan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index', ['sale' => 1]) }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Promo & Diskon
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('compare.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Bandingkan Produk
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Layanan Pelanggan</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('contact') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Hubungi Kami
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faq') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('shipping-info') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Info Pengiriman
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('return-policy') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Kebijakan Return
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('warranty') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Garansi Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('track-order') }}" class="text-gray-300 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Lacak Pesanan
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Kontak Kami</h3>
                    <div class="space-y-4">
                        <!-- Phone -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-phone text-white text-sm"></i>
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

                        <!-- Address -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 text-sm">Alamat Kantor</p>
                                <address class="text-white not-italic leading-relaxed">
                                    Jl. Sudirman No. 123<br>
                                    Jakarta Pusat 10220<br>
                                    Indonesia
                                </address>
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

    <!-- Payment Methods & Shipping Partners -->
    <div class="border-t border-gray-800 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Payment Methods -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">Metode Pembayaran</h4>
                    <div class="flex flex-wrap gap-3">
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/visa.png') }}" alt="Visa" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/mastercard.png') }}" alt="Mastercard" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/bca.png') }}" alt="BCA" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/mandiri.png') }}" alt="Mandiri" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/bni.png') }}" alt="BNI" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/bri.png') }}" alt="BRI" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/gopay.png') }}" alt="GoPay" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/ovo.png') }}" alt="OVO" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/payments/dana.png') }}" alt="DANA" class="h-6 object-contain">
                        </div>
                    </div>
                </div>

                <!-- Shipping Partners -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">Partner Pengiriman</h4>
                    <div class="flex flex-wrap gap-3">
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/shipping/jne.png') }}" alt="JNE" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/shipping/jnt.png') }}" alt="J&T" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/shipping/sicepat.png') }}" alt="SiCepat" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/shipping/pos.png') }}" alt="Pos Indonesia" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/shipping/tiki.png') }}" alt="TIKI" class="h-6 object-contain">
                        </div>
                        <div class="bg-white rounded-lg p-2 flex items-center justify-center w-16 h-10">
                            <img src="{{ asset('images/shipping/anteraja.png') }}" alt="AnterAja" class="h-6 object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Certifications -->
    <div class="border-t border-gray-800 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-green-400 text-lg"></i>
                        <span class="text-gray-300 text-sm">100% Original</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-lock text-blue-400 text-lg"></i>
                        <span class="text-gray-300 text-sm">SSL Secure</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-undo text-yellow-400 text-lg"></i>
                        <span class="text-gray-300 text-sm">30 Hari Return</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shipping-fast text-purple-400 text-lg"></i>
                        <span class="text-gray-300 text-sm">Gratis Ongkir</span>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/certifications/ssl.png') }}" alt="SSL Certificate" class="h-8 opacity-80">
                    <img src="{{ asset('images/certifications/verified.png') }}" alt="Verified Merchant" class="h-8 opacity-80">
                    <img src="{{ asset('images/certifications/trusted.png') }}" alt="Trusted Store" class="h-8 opacity-80">
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Copyright -->
                <div class="text-center md:text-left">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} TokoSaya. All rights reserved.
                        <span class="text-gray-500">Made with ❤️ in Indonesia</span>
                    </p>
                </div>

                <!-- Legal Links -->
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    <a href="{{ route('privacy-policy') }}" class="text-gray-400 hover:text-white transition-colors">
                        Kebijakan Privasi
                    </a>
                    <a href="{{ route('terms-of-service') }}" class="text-gray-400 hover:text-white transition-colors">
                        Syarat & Ketentuan
                    </a>
                    <a href="{{ route('sitemap') }}" class="text-gray-400 hover:text-white transition-colors">
                        Sitemap
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors">
                        Tentang Kami
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="back-to-top"
            class="fixed bottom-8 right-8 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 transform translate-y-16 opacity-0 z-50"
            onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

@push('scripts')
<script>
// Newsletter subscription
function subscribeNewsletter() {
    const form = event.target;
    const formData = new FormData(form);

    Alpine.store('newsletter', { loading: true });

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showFooterNotification('Berhasil berlangganan newsletter!', 'success');
            form.reset();
        } else {
            showFooterNotification(data.message || 'Gagal berlangganan newsletter', 'error');
        }
    })
    .catch(error => {
        console.error('Newsletter subscription error:', error);
        showFooterNotification('Terjadi kesalahan, silakan coba lagi', 'error');
    })
    .finally(() => {
        Alpine.store('newsletter', { loading: false });
    });
}

// Back to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show/hide back to top button
window.addEventListener('scroll', function() {
    const backToTopBtn = document.getElementById('back-to-top');
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.remove('translate-y-16', 'opacity-0');
        backToTopBtn.classList.add('translate-y-0', 'opacity-100');
    } else {
        backToTopBtn.classList.add('translate-y-16', 'opacity-0');
        backToTopBtn.classList.remove('translate-y-0', 'opacity-100');
    }
});

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

// Footer link tracking
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

// Newsletter form validation
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('form[action*="newsletter"]');
    if (newsletterForm) {
        const emailInput = newsletterForm.querySelector('input[type="email"]');

        emailInput.addEventListener('input', function() {
            const email = this.value;
            const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

            if (email.length > 0) {
                if (isValid) {
                    this.classList.remove('border-red-300');
                    this.classList.add('border-green-300');
                } else {
                    this.classList.remove('border-green-300');
                    this.classList.add('border-red-300');
                }
            } else {
                this.classList.remove('border-red-300', 'border-green-300');
            }
        });
    }
});

// Social media link tracking
document.querySelectorAll('footer a[href*="facebook"], footer a[href*="instagram"], footer a[href*="twitter"], footer a[href*="youtube"], footer a[href*="whatsapp"]').forEach(link => {
    link.addEventListener('click', function() {
        const platform = this.href.includes('facebook') ? 'facebook' :
                         this.href.includes('instagram') ? 'instagram' :
                         this.href.includes('twitter') ? 'twitter' :
                         this.href.includes('youtube') ? 'youtube' :
                         this.href.includes('whatsapp') ? 'whatsapp' : 'unknown';

        if (typeof gtag !== 'undefined') {
            gtag('event', 'social_media_click', {
                platform: platform,
                location: 'footer'
            });
        }
    });
});

// Initialize Alpine.js stores for footer
document.addEventListener('alpine:init', () => {
    Alpine.store('newsletter', {
        loading: false
    });
});

// Footer animation on scroll
const footerElements = document.querySelectorAll('footer .container > div > div');
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
</script>
@endpush
