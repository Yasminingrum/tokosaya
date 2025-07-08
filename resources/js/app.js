// TokoSaya Modern App JavaScript
import './bootstrap';

// Alpine.js for reactive components
import Alpine from 'alpinejs';

// Initialize Alpine.js
window.Alpine = Alpine;

// Alpine.js global stores
document.addEventListener('alpine:init', () => {
    // Cart store
    Alpine.store('cart', {
        items: [],
        count: 0,
        total: 0,

        init() {
            this.loadCartData();
        },

        async loadCartData() {
            try {
                const response = await fetch('/api/cart');
                const data = await response.json();
                this.items = data.items || [];
                this.count = data.count || 0;
                this.total = data.total || 0;
            } catch (error) {
                console.log('Could not load cart data');
            }
        },

        async addItem(productId, quantity = 1) {
            try {
                showLoading();
                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: productId, quantity })
                });

                const data = await response.json();
                if (data.success) {
                    this.loadCartData();
                    showToast('Produk berhasil ditambahkan ke keranjang', 'success');
                } else {
                    showToast(data.message || 'Gagal menambahkan produk', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            } finally {
                hideLoading();
            }
        },

        async updateQuantity(itemId, quantity) {
            try {
                const response = await fetch(`/api/cart/update/${itemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ quantity })
                });

                const data = await response.json();
                if (data.success) {
                    this.loadCartData();
                }
            } catch (error) {
                console.error('Failed to update cart item');
            }
        },

        async removeItem(itemId) {
            try {
                const response = await fetch(`/api/cart/remove/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.loadCartData();
                    showToast('Produk dihapus dari keranjang', 'success');
                }
            } catch (error) {
                console.error('Failed to remove cart item');
            }
        }
    });

    // Wishlist store
    Alpine.store('wishlist', {
        items: [],
        count: 0,

        init() {
            this.loadWishlistData();
        },

        async loadWishlistData() {
            try {
                const response = await fetch('/api/wishlist');
                const data = await response.json();
                this.items = data.items || [];
                this.count = data.count || 0;
            } catch (error) {
                console.log('Could not load wishlist data');
            }
        },

        async toggle(productId) {
            try {
                const response = await fetch('/api/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: productId })
                });

                const data = await response.json();
                if (data.success) {
                    this.loadWishlistData();
                    showToast(data.message, 'success');
                    return data.added;
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            }
        }
    });

    // UI store for global UI state
    Alpine.store('ui', {
        loading: false,
        mobileMenuOpen: false,
        searchOpen: false,

        setLoading(state) {
            this.loading = state;
        },

        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },

        toggleSearch() {
            this.searchOpen = !this.searchOpen;
        }
    });
});

// Start Alpine.js
Alpine.start();

// Modern TokoSaya App Class
class TokoSayaApp {
    constructor() {
        this.initializeApp();
    }

    initializeApp() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupEventListeners();
            this.initializeComponents();
            this.setupIntersectionObserver();
            this.setupScrollEffects();
            this.initializeSearch();
            console.log('🚀 TokoSaya Modern App initialized');
        });
    }

    setupEventListeners() {
        // Smooth scroll for anchor links
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="#"]')) {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });

        // Global loading states for forms
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('loading-form')) {
                showLoading();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Escape key to close modals/search
            if (e.key === 'Escape') {
                Alpine.store('ui').searchOpen = false;
                Alpine.store('ui').mobileMenuOpen = false;
            }

            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                Alpine.store('ui').toggleSearch();
                setTimeout(() => {
                    const searchInput = document.querySelector('.search-input');
                    if (searchInput) searchInput.focus();
                }, 100);
            }
        });
    }

    initializeComponents() {
        // Initialize product cards
        this.initProductCards();

        // Initialize newsletter
        this.initNewsletter();

        // Initialize lazy loading
        this.initLazyLoading();

        // Initialize tooltips
        this.initTooltips();

        // Initialize cart functionality
        this.initCartFunctionality();
    }

    initProductCards() {
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            // Add to cart functionality
            const addToCartBtn = card.querySelector('.add-to-cart-btn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productId = addToCartBtn.dataset.productId;
                    Alpine.store('cart').addItem(productId);
                });
            }

            // Wishlist toggle
            const wishlistBtn = card.querySelector('.wishlist-btn');
            if (wishlistBtn) {
                wishlistBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const productId = wishlistBtn.dataset.productId;
                    const added = await Alpine.store('wishlist').toggle(productId);

                    // Update button appearance
                    const icon = wishlistBtn.querySelector('i');
                    if (added) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                    }
                });
            }

            // Quick view functionality
            const quickViewBtn = card.querySelector('.quick-view-btn');
            if (quickViewBtn) {
                quickViewBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productId = quickViewBtn.dataset.productId;
                    this.openQuickView(productId);
                });
            }
        });
    }

    initNewsletter() {
        const newsletterForm = document.querySelector('#newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const email = newsletterForm.querySelector('input[type="email"]').value;
                const submitBtn = newsletterForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                try {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                    submitBtn.disabled = true;

                    const response = await fetch('/api/newsletter/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Berhasil berlangganan newsletter!', 'success');
                        newsletterForm.reset();
                    } else {
                        showToast(data.message || 'Gagal berlangganan', 'error');
                    }
                } catch (error) {
                    showToast('Terjadi kesalahan', 'error');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }
    }

    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    }
                });
            }, { rootMargin: '50px 0px' });

            document.querySelectorAll('img[data-src]').forEach(img => {
                img.classList.add('lazy');
                imageObserver.observe(img);
            });
        }
    }

    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');

        tooltipElements.forEach(element => {
            let tooltip;

            element.addEventListener('mouseenter', () => {
                tooltip = this.createTooltip(element.dataset.tooltip);
                document.body.appendChild(tooltip);
                this.positionTooltip(element, tooltip);
            });

            element.addEventListener('mouseleave', () => {
                if (tooltip && tooltip.parentNode) {
                    tooltip.parentNode.removeChild(tooltip);
                }
            });
        });
    }

    createTooltip(text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.innerHTML = text;
        return tooltip;
    }

    positionTooltip(element, tooltip) {
        const elementRect = element.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();

        tooltip.style.position = 'absolute';
        tooltip.style.top = `${elementRect.top - tooltipRect.height - 8}px`;
        tooltip.style.left = `${elementRect.left + (elementRect.width - tooltipRect.width) / 2}px`;
        tooltip.style.zIndex = '9999';
    }

    initCartFunctionality() {
        // Quantity selectors
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quantity-plus')) {
                const input = e.target.parentNode.querySelector('input');
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event('change'));
            }

            if (e.target.matches('.quantity-minus')) {
                const input = e.target.parentNode.querySelector('input');
                const newValue = Math.max(1, parseInt(input.value) - 1);
                input.value = newValue;
                input.dispatchEvent(new Event('change'));
            }
        });

        // Quantity input changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('.quantity-input')) {
                const itemId = e.target.dataset.itemId;
                const quantity = Math.max(1, parseInt(e.target.value) || 1);
                e.target.value = quantity;

                if (itemId) {
                    Alpine.store('cart').updateQuantity(itemId, quantity);
                }
            }
        });
    }

    setupIntersectionObserver() {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                animationObserver.observe(el);
            });
        }
    }

    setupScrollEffects() {
        let lastScrollY = window.scrollY;

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            // Navbar scroll effects
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (currentScrollY > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }

                // Hide/show navbar on scroll
                if (currentScrollY > lastScrollY && currentScrollY > 200) {
                    navbar.style.transform = 'translateY(-100%)';
                } else {
                    navbar.style.transform = 'translateY(0)';
                }
            }

            // Back to top button
            const backToTopBtn = document.querySelector('.back-to-top');
            if (backToTopBtn) {
                if (currentScrollY > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            }

            lastScrollY = currentScrollY;
        });

        // Back to top functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.back-to-top') || e.target.closest('.back-to-top')) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    }

    initializeSearch() {
        const searchInput = document.querySelector('.search-input');
        const searchResults = document.querySelector('.search-results');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        this.performSearch(query);
                    }, 300);
                } else {
                    this.clearSearchResults();
                }
            });

            // Clear search on escape
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.clearSearchResults();
                    searchInput.blur();
                }
            });
        }

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.clearSearchResults();
            }
        });
    }

    async performSearch(query) {
        try {
            const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            this.displaySearchResults(data.suggestions || []);
        } catch (error) {
            console.error('Search error:', error);
            this.clearSearchResults();
        }
    }

    displaySearchResults(suggestions) {
        const searchResults = document.querySelector('.search-results');
        if (!searchResults) return;

        if (suggestions.length === 0) {
            searchResults.innerHTML = '<div class="p-4 text-gray-500 text-center">Tidak ada hasil ditemukan</div>';
        } else {
            const html = suggestions.map(item => `
                <a href="${item.url}" class="search-result-item">
                    <div class="flex items-center p-3 hover:bg-gray-50 transition-colors">
                        ${item.image ? `<img src="${item.image}" alt="${item.name}" class="w-10 h-10 object-cover rounded mr-3">` : ''}
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">${item.name}</div>
                            <div class="text-sm text-gray-500">${item.type}</div>
                            ${item.price ? `<div class="text-sm font-semibold text-blue-600">${item.price}</div>` : ''}
                        </div>
                    </div>
                </a>
            `).join('');

            searchResults.innerHTML = html;
        }

        searchResults.classList.remove('hidden');
    }

    clearSearchResults() {
        const searchResults = document.querySelector('.search-results');
        if (searchResults) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
        }
    }

    async openQuickView(productId) {
        try {
            showLoading();
            const response = await fetch(`/api/products/${productId}/quick-view`);
            const data = await response.json();

            if (data.success) {
                this.createQuickViewModal(data.product);
            }
        } catch (error) {
            showToast('Gagal memuat detail produk', 'error');
        } finally {
            hideLoading();
        }
    }

    createQuickViewModal(product) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-full overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-2xl font-bold">${product.name}</h2>
                        <button class="close-modal text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="product-images">
                            <img src="${product.images[0]?.url || '/images/placeholder.jpg'}"
                                 alt="${product.name}"
                                 class="w-full h-64 object-cover rounded-lg">
                        </div>
                        <div class="product-details">
                            <div class="text-3xl font-bold text-blue-600 mb-4">
                                ${formatCurrency(product.price)}
                            </div>
                            <div class="prose text-gray-600 mb-6">
                                ${product.description || 'Tidak ada deskripsi'}
                            </div>
                            <button class="btn-primary w-full add-to-cart-btn" data-product-id="${product.id}">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Close modal functionality
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.matches('.close-modal') || e.target.closest('.close-modal')) {
                document.body.removeChild(modal);
            }
        });

        // Add to cart from modal
        modal.querySelector('.add-to-cart-btn').addEventListener('click', () => {
            Alpine.store('cart').addItem(product.id);
            document.body.removeChild(modal);
        });
    }
}

// Utility Functions
function showLoading() {
    Alpine.store('ui').setLoading(true);
    document.body.classList.add('loading');
}

function hideLoading() {
    Alpine.store('ui').setLoading(false);
    document.body.classList.remove('loading');
}

function showToast(message, type = 'info', duration = 4000) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white shadow-lg transform transition-all duration-300 translate-x-full ${getToastClass(type)}`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${getToastIcon(type)} mr-2"></i>
            <span>${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Auto remove
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }, duration);
}

function getToastClass(type) {
    const classes = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    return classes[type] || classes.info;
}

function getToastIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

// Performance optimization
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Analytics tracking
function trackEvent(eventName, eventData = {}) {
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, eventData);
    }

    if (typeof fbq !== 'undefined') {
        fbq('track', eventName, eventData);
    }
}

// Modern event delegation
function delegate(element, eventType, selector, handler) {
    element.addEventListener(eventType, (e) => {
        if (e.target.matches(selector) || e.target.closest(selector)) {
            handler(e);
        }
    });
}

// Local storage helpers
const storage = {
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.warn('Could not save to localStorage');
        }
    },

    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            return defaultValue;
        }
    },

    remove(key) {
        try {
            localStorage.removeItem(key);
        } catch (e) {
            console.warn('Could not remove from localStorage');
        }
    }
};

// API helper
const api = {
    async request(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        };

        const config = { ...defaults, ...options };
        config.headers = { ...defaults.headers, ...options.headers };

        try {
            const response = await fetch(url, config);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }

            return await response.text();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    },

    get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    },

    post(url, data, options = {}) {
        return this.request(url, {
            ...options,
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    put(url, data, options = {}) {
        return this.request(url, {
            ...options,
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    }
};

// Initialize the app
const app = new TokoSayaApp();

// Export for global access
window.TokoSaya = {
    app,
    showToast,
    showLoading,
    hideLoading,
    formatCurrency,
    debounce,
    storage,
    api,
    trackEvent
};

// Service Worker registration for PWA capabilities
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('SW registered: ', registration);
            })
            .catch((registrationError) => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
