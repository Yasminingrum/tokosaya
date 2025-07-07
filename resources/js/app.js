// TokoSaya App JavaScript with Alpine.js
import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Custom JavaScript
document.addEventListener('DOMContentLoaded', function() {

    // Initialize TokoSaya App
    window.TokoSaya = {
        // Auto-hide carousel indicators on mobile
        initCarousel() {
            if (window.innerWidth < 768) {
                const indicators = document.querySelector('.carousel-indicators');
                if (indicators) {
                    indicators.style.display = 'none';
                }
            }
        },

        // Newsletter subscription
        initNewsletter() {
            const newsletterForm = document.querySelector('#newsletterForm');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = this.querySelector('input[type="email"]').value;
                    if (email) {
                        // You can replace this with actual AJAX call
                        this.classList.add('opacity-50');
                        setTimeout(() => {
                            alert('Terima kasih! Anda akan segera mendapatkan update dari kami.');
                            this.reset();
                            this.classList.remove('opacity-50');
                        }, 1000);
                    }
                });
            }
        },

        // Product card interactions
        initProductCards() {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('-translate-y-1');
                });

                card.addEventListener('mouseleave', function() {
                    this.classList.remove('-translate-y-1');
                });
            });
        },

        // Smooth scrolling for anchor links
        initSmoothScroll() {
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        },

        // Flash Sale Timer
        initFlashSaleTimer() {
            const timerElements = {
                hours: document.getElementById('hours'),
                minutes: document.getElementById('minutes'),
                seconds: document.getElementById('seconds')
            };

            if (timerElements.hours && timerElements.minutes && timerElements.seconds) {
                // Set timer to 24 hours from now (example)
                const endTime = new Date().getTime() + (24 * 60 * 60 * 1000);

                const updateTimer = () => {
                    const now = new Date().getTime();
                    const timeLeft = endTime - now;

                    if (timeLeft > 0) {
                        const hours = Math.floor(timeLeft / (1000 * 60 * 60));
                        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                        timerElements.hours.textContent = hours.toString().padStart(2, '0');
                        timerElements.minutes.textContent = minutes.toString().padStart(2, '0');
                        timerElements.seconds.textContent = seconds.toString().padStart(2, '0');
                    } else {
                        // Timer expired
                        timerElements.hours.textContent = '00';
                        timerElements.minutes.textContent = '00';
                        timerElements.seconds.textContent = '00';
                    }
                };

                updateTimer(); // Initial call
                setInterval(updateTimer, 1000); // Update every second
            }
        },

        // Search functionality
        initSearch() {
            const searchInput = document.querySelector('#search-input');
            const searchSuggestions = document.querySelector('#search-suggestions');

            if (searchInput && searchSuggestions) {
                let searchTimeout;

                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length >= 2) {
                        searchTimeout = setTimeout(() => {
                            // You can replace this with actual AJAX call to search endpoint
                            fetch(`/search-suggestions?q=${encodeURIComponent(query)}`)
                                .then(response => response.json())
                                .then(data => {
                                    displaySearchSuggestions(data.suggestions);
                                })
                                .catch(error => {
                                    console.error('Search error:', error);
                                });
                        }, 300);
                    } else {
                        searchSuggestions.innerHTML = '';
                        searchSuggestions.classList.add('hidden');
                    }
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                        searchSuggestions.classList.add('hidden');
                    }
                });
            }

            function displaySearchSuggestions(suggestions) {
                if (suggestions && suggestions.length > 0) {
                    const html = suggestions.map(item => `
                        <a href="${item.url}" class="block px-4 py-2 hover:bg-gray-100 text-sm">
                            <div class="font-medium">${item.name}</div>
                            <div class="text-gray-500 text-xs">${item.type}</div>
                        </a>
                    `).join('');

                    searchSuggestions.innerHTML = html;
                    searchSuggestions.classList.remove('hidden');
                } else {
                    searchSuggestions.classList.add('hidden');
                }
            }
        },

        // Initialize all components
        init() {
            this.initCarousel();
            this.initNewsletter();
            this.initProductCards();
            this.initSmoothScroll();
            this.initFlashSaleTimer();
            this.initSearch();

            console.log('🚀 TokoSaya initialized with Tailwind CSS and Alpine.js');
        }
    };

    // Initialize the app
    TokoSaya.init();
});

// Alpine.js Components
window.productCard = () => ({
    isWishlisted: false,

    toggleWishlist() {
        this.isWishlisted = !this.isWishlisted;
        // You can add AJAX call here to update wishlist on server
        console.log('Wishlist toggled:', this.isWishlisted);
    },

    addToCart() {
        // Add to cart functionality
        console.log('Added to cart');
        // You can add AJAX call here
    }
});

window.cartCounter = () => ({
    count: 0,

    increment() {
        this.count++;
    },

    decrement() {
        if (this.count > 0) {
            this.count--;
        }
    }
});

window.mobileMenu = () => ({
    isOpen: false,

    toggle() {
        this.isOpen = !this.isOpen;
    },

    close() {
        this.isOpen = false;
    }
});
