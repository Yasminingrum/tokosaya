@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan - TokoSaya')
@section('meta_description', 'Halaman yang Anda cari tidak ditemukan. Kembali ke TokoSaya untuk melanjutkan belanja atau jelajahi produk terbaru kami.')

@push('styles')
<style>
    .error-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .error-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle cx="200" cy="200" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="800" cy="300" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="400" cy="600" r="2.5" fill="rgba(255,255,255,0.1)"/><circle cx="600" cy="800" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="100" cy="500" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="900" cy="700" r="3" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: float 20s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        25% { transform: translateY(-20px) rotate(90deg); }
        50% { transform: translateY(-10px) rotate(180deg); }
        75% { transform: translateY(-30px) rotate(270deg); }
    }

    .error-content {
        text-align: center;
        position: relative;
        z-index: 2;
        max-width: 600px;
        padding: 40px 20px;
    }

    .error-code {
        font-size: clamp(8rem, 15vw, 12rem);
        font-weight: 900;
        line-height: 0.9;
        margin-bottom: 20px;
        background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: bounce 2s infinite ease-in-out;
        position: relative;
    }

    .error-code::before {
        content: '404';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #ff6b6b, #ffa726);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: colorShift 3s infinite ease-in-out;
        z-index: -1;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        60% {
            transform: translateY(-10px);
        }
    }

    @keyframes colorShift {
        0%, 100% { opacity: 0; }
        50% { opacity: 0.3; }
    }

    .error-title {
        font-size: clamp(1.5rem, 4vw, 2.5rem);
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .error-message {
        font-size: clamp(1rem, 2.5vw, 1.25rem);
        margin-bottom: 40px;
        opacity: 0.9;
        line-height: 1.6;
    }

    .error-actions {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }

    .btn-primary-custom {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        min-width: 200px;
        justify-content: center;
    }

    .btn-primary-custom:hover {
        background: white;
        color: #667eea;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
    }

    .btn-secondary-custom {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: rgba(255, 255, 255, 0.9);
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        font-size: 1rem;
        min-width: 180px;
        justify-content: center;
    }

    .btn-secondary-custom:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
        color: white;
        transform: translateY(-2px);
    }

    .floating-shapes {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        z-index: 1;
    }

    .shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: floatShapes 15s infinite ease-in-out;
    }

    .shape:nth-child(1) {
        width: 60px;
        height: 60px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        width: 40px;
        height: 40px;
        top: 60%;
        left: 80%;
        animation-delay: 2s;
    }

    .shape:nth-child(3) {
        width: 80px;
        height: 80px;
        top: 80%;
        left: 20%;
        animation-delay: 4s;
    }

    .shape:nth-child(4) {
        width: 30px;
        height: 30px;
        top: 30%;
        left: 85%;
        animation-delay: 6s;
    }

    .shape:nth-child(5) {
        width: 50px;
        height: 50px;
        top: 70%;
        left: 70%;
        animation-delay: 8s;
    }

    @keyframes floatShapes {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
            opacity: 0.3;
        }
        25% {
            transform: translateY(-30px) rotate(90deg);
            opacity: 0.6;
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
            opacity: 0.4;
        }
        75% {
            transform: translateY(-40px) rotate(270deg);
            opacity: 0.7;
        }
    }

    .search-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        padding: 8px;
        margin: 30px 0;
        transition: all 0.3s ease;
        max-width: 400px;
        width: 100%;
    }

    .search-box:focus-within {
        border-color: rgba(255, 255, 255, 0.5);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
    }

    .search-input {
        background: transparent;
        border: none;
        color: white;
        font-size: 1rem;
        padding: 12px 20px;
        width: 100%;
        outline: none;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        padding: 12px 20px;
        border-radius: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .search-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .quick-links {
        margin-top: 40px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }

    .quick-link {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.9);
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .quick-link:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        transform: translateY(-2px);
    }

    .help-text {
        margin-top: 40px;
        font-size: 0.9rem;
        opacity: 0.8;
        line-height: 1.5;
    }

    .contact-info {
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        font-size: 0.9rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.8;
    }

    .contact-item i {
        font-size: 1rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .error-content {
            padding: 20px 15px;
        }

        .error-actions {
            gap: 12px;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            width: 100%;
            max-width: 280px;
        }

        .quick-links {
            flex-direction: column;
            align-items: center;
        }

        .quick-link {
            width: 100%;
            max-width: 200px;
            text-align: center;
        }

        .contact-info {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            margin: 20px 0;
        }
    }

    @media (max-width: 480px) {
        .error-content {
            padding: 15px 10px;
        }

        .error-message {
            margin-bottom: 30px;
        }

        .btn-primary-custom {
            padding: 12px 30px;
            font-size: 1rem;
        }

        .btn-secondary-custom {
            padding: 10px 25px;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="error-container">
    <!-- Floating Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="error-content" data-aos="fade-up">
        <div class="error-code">404</div>

        <h1 class="error-title">Oops! Halaman Tidak Ditemukan</h1>

        <p class="error-message">
            Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau mungkin Anda salah mengetik URL.
            Jangan khawatir, mari kita bantu Anda menemukan apa yang Anda butuhkan!
        </p>

        <!-- Search Box -->
        <div class="search-box mx-auto">
            <div class="d-flex align-items-center">
                <input type="text" class="search-input" id="errorPageSearch" placeholder="Cari produk atau halaman...">
                <button class="search-btn" type="button" onclick="performSearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Primary Actions -->
        <div class="error-actions">
            <a href="{{ route('home') }}" class="btn-primary-custom">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>

            <a href="{{ route('products.index') }}" class="btn-secondary-custom">
                <i class="fas fa-shopping-bag"></i>
                Lihat Semua Produk
            </a>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <a href="{{ route('categories.index') }}" class="quick-link">
                <i class="fas fa-th-large me-1"></i>
                Kategori
            </a>
            <a href="{{ route('brands.index') }}" class="quick-link">
                <i class="fas fa-tags me-1"></i>
                Brand
            </a>
            <a href="{{ route('wishlist.index') }}" class="quick-link">
                <i class="fas fa-heart me-1"></i>
                Wishlist
            </a>
            <a href="{{ route('orders.index') }}" class="quick-link">
                <i class="fas fa-box me-1"></i>
                Pesanan Saya
            </a>
            <a href="{{ route('contact') }}" class="quick-link">
                <i class="fas fa-headset me-1"></i>
                Bantuan
            </a>
        </div>

        <!-- Help Text -->
        <div class="help-text">
            <p class="mb-2">
                <strong>Beberapa alasan halaman tidak ditemukan:</strong>
            </p>
            <ul class="list-unstyled text-start" style="max-width: 400px; margin: 0 auto;">
                <li>• URL yang diketik salah atau tidak lengkap</li>
                <li>• Halaman telah dipindahkan atau dihapus</li>
                <li>• Link yang Anda klik sudah tidak valid</li>
                <li>• Produk yang dicari sudah tidak tersedia</li>
            </ul>
        </div>

        <!-- Contact Information -->
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <span>0804-1-500-400</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>help@tokosaya.id</span>
            </div>
            <div class="contact-item">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp Support</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search functionality
    const searchInput = document.getElementById('errorPageSearch');

    // Auto-focus search input after page load
    setTimeout(() => {
        searchInput.focus();
    }, 1000);

    // Search suggestions based on popular searches
    const searchSuggestions = [
        'iPhone',
        'Laptop',
        'Sepatu Nike',
        'Dress Wanita',
        'Tas',
        'Jam Tangan',
        'Headphone',
        'Smartphone',
        'Fashion Pria',
        'Elektronik'
    ];

    // Create suggestion dropdown
    const suggestionDropdown = document.createElement('div');
    suggestionDropdown.className = 'position-absolute w-100 mt-1 bg-white border rounded shadow-lg';
    suggestionDropdown.style.display = 'none';
    suggestionDropdown.style.zIndex = '1000';
    suggestionDropdown.style.maxHeight = '200px';
    suggestionDropdown.style.overflowY = 'auto';
    suggestionDropdown.style.color = '#333';

    searchInput.parentNode.parentNode.style.position = 'relative';
    searchInput.parentNode.parentNode.appendChild(suggestionDropdown);

    // Show suggestions on input
    searchInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();

        if (value.length > 1) {
            const matches = searchSuggestions.filter(suggestion =>
                suggestion.toLowerCase().includes(value)
            );

            if (matches.length > 0) {
                suggestionDropdown.innerHTML = matches.map(match =>
                    `<div class="p-2 border-bottom cursor-pointer suggestion-item" style="cursor: pointer;">${match}</div>`
                ).join('');
                suggestionDropdown.style.display = 'block';

                // Add click handlers to suggestions
                suggestionDropdown.querySelectorAll('.suggestion-item').forEach(item => {
                    item.addEventListener('click', function() {
                        searchInput.value = this.textContent;
                        suggestionDropdown.style.display = 'none';
                        performSearch();
                    });

                    item.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f8f9fa';
                    });

                    item.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = 'transparent';
                    });
                });
            } else {
                suggestionDropdown.style.display = 'none';
            }
        } else {
            suggestionDropdown.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.parentNode.parentNode.contains(e.target)) {
            suggestionDropdown.style.display = 'none';
        }
    });

    // Search on Enter key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            suggestionDropdown.style.display = 'none';
            performSearch();
        }
    });

    // Track 404 errors for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_not_found', {
            'event_category': '404_Error',
            'event_label': window.location.pathname,
            'value': 1
        });
    }

    // Log error details for debugging
    console.log('404 Error Details:', {
        url: window.location.href,
        referrer: document.referrer,
        userAgent: navigator.userAgent,
        timestamp: new Date().toISOString()
    });

    // Add some interactive elements for better UX
    const shapes = document.querySelectorAll('.shape');
    shapes.forEach((shape, index) => {
        shape.addEventListener('click', function() {
            this.style.animation = 'none';
            this.style.transform = 'scale(1.5)';
            this.style.opacity = '0.8';

            setTimeout(() => {
                this.style.animation = `floatShapes 15s infinite ease-in-out`;
                this.style.animationDelay = `${index * 2}s`;
                this.style.transform = 'scale(1)';
                this.style.opacity = '0.3';
            }, 500);
        });
    });

    // Add hover effect to error code
    const errorCode = document.querySelector('.error-code');
    errorCode.addEventListener('mouseenter', function() {
        this.style.animation = 'bounce 0.6s ease-in-out';
    });

    errorCode.addEventListener('animationend', function() {
        this.style.animation = 'bounce 2s infinite ease-in-out';
    });

    // Preload some pages for faster navigation
    const linksToPrefetch = [
        '{{ route("home") }}',
        '{{ route("products.index") }}',
        '{{ route("categories.index") }}'
    ];

    linksToPrefetch.forEach(url => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    });

    // Auto-redirect if user stays too long (optional)
    let redirectTimeout;

    function startRedirectTimer() {
        redirectTimeout = setTimeout(() => {
            if (confirm('Anda sudah lama di halaman ini. Ingin kembali ke beranda?')) {
                window.location.href = '{{ route("home") }}';
            }
        }, 30000); // 30 seconds
    }

    // Reset timer on user interaction
    ['click', 'keydown', 'mousemove', 'scroll'].forEach(event => {
        document.addEventListener(event, () => {
            clearTimeout(redirectTimeout);
            startRedirectTimer();
        }, { once: true });
    });

    startRedirectTimer();
});

// Global search function
function performSearch() {
    const searchTerm = document.getElementById('errorPageSearch').value.trim();

    if (searchTerm.length > 0) {
        // Redirect to search page with query
        const searchUrl = '{{ route("search.index") }}?q=' + encodeURIComponent(searchTerm);
        window.location.href = searchUrl;
    } else {
        // If no search term, go to products page
        window.location.href = '{{ route("products.index") }}';
    }
}

// Utility function to get URL parameters
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// If there's a search query in URL, populate search box
document.addEventListener('DOMContentLoaded', function() {
    const urlQuery = getUrlParameter('q');
    if (urlQuery) {
        document.getElementById('errorPageSearch').value = urlQuery;
    }
});

// Enhanced keyboard navigation
document.addEventListener('keydown', function(e) {
    switch(e.key) {
        case 'Escape':
            // Clear search and hide suggestions
            document.getElementById('errorPageSearch').value = '';
            document.querySelector('.search-box + div').style.display = 'none';
            break;
        case 'h':
        case 'H':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                window.location.href = '{{ route("home") }}';
            }
            break;
        case 'p':
        case 'P':
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                window.location.href = '{{ route("products.index") }}';
            }
            break;
    }
});

// Add smooth scroll behavior for better UX
document.documentElement.style.scrollBehavior = 'smooth';

// Report 404 to monitoring service (if available)
if (window.reportError) {
    window.reportError({
        type: '404',
        url: window.location.href,
        referrer: document.referrer,
        timestamp: Date.now()
    });
}
</script>
@endpush
