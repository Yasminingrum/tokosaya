```php
@extends('layouts.app')

@section('title', $category->name . ' - TokoSaya')
@section('meta_description', $category->description ?? 'Browse ' . $category->name . ' products at TokoSaya with great prices and quality guarantee.')

@push('styles')
<style>
    /* Category Hero Section */
    .category-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0;
        border-radius: 0 0 2rem 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .category-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="25" cy="75" r="1" fill="white" opacity="0.05"/><circle cx="75" cy="25" r="1" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }

    .category-icon-large {
        font-size: 4rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .category-stats {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    /* Filters Sidebar */
    .filters-sidebar {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        position: sticky;
        top: 20px;
    }

    .filter-group {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .filter-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .filter-title {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #374151;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .price-range-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .price-input {
        width: 80px;
        text-align: center;
        font-size: 0.85rem;
    }

    /* Mobile Filters */
    .mobile-filter-btn {
        display: none;
    }

    .filter-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        background: #2563eb;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        margin: 0.25rem;
        text-decoration: none;
    }

    .filter-chip-remove {
        background: none;
        border: none;
        color: white;
        margin-left: 0.5rem;
        cursor: pointer;
        opacity: 0.8;
    }

    .filter-chip-remove:hover {
        opacity: 1;
    }

    /* Product Grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .product-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }

    .product-image {
        height: 220px;
        object-fit: cover;
        width: 100%;
    }

    /* Controls */
    .sort-dropdown {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        min-width: 200px;
    }

    .view-toggle {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.25rem;
        display: flex;
        gap: 0.25rem;
    }

    .view-btn {
        background: transparent;
        border: none;
        padding: 0.5rem;
        border-radius: 6px;
        color: #6b7280;
        transition: all 0.2s;
    }

    .view-btn.active {
        background: #2563eb;
        color: white;
    }

    /* Subcategories */
    .subcategories {
        margin-bottom: 2rem;
    }

    .subcategory-chip {
        display: inline-block;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        margin: 0.25rem;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .subcategory-chip:hover {
        border-color: #2563eb;
        color: #2563eb;
        transform: translateY(-2px);
    }

    .subcategory-chip.active {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
    }

    /* Miscellaneous */
    .results-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6b7280;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #6b7280;
    }

    .load-more {
        text-align: center;
        margin-top: 3rem;
    }

    /* Responsive Design */
    @media (max-width: 991px) {
        .mobile-filter-btn {
            display: block;
        }

        .filters-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 300px;
            height: 100vh;
            z-index: 1050;
            transition: left 0.3s ease;
            overflow-y: auto;
        }

        .filters-sidebar.show {
            left: 0;
        }

        .filter-overlay.show {
            display: block;
        }

        .category-hero {
            padding: 2rem 0;
        }

        .category-icon-large {
            font-size: 2.5rem;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .results-summary {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .view-toggle {
            align-self: center;
        }
    }

    /* List View */
    .list-view .product-grid {
        grid-template-columns: 1fr;
    }

    .list-view .product-card {
        display: flex;
        flex-direction: row;
    }

    .list-view .product-image {
        width: 200px;
        height: 150px;
        flex-shrink: 0;
    }

    .list-view .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @include('categories.partials.hero', ['category' => $category, 'products' => $products])

    <div class="container">
        @include('categories.partials.subcategories', ['subcategories' => $subcategories ?? collect(), 'category' => $category])

        @include('categories.partials.active-filters', ['category' => $category, 'brands' => $brands ?? collect()])

        <div class="row">
            <div class="col-lg-3">
                @include('categories.partials.filters', [
                    'category' => $category,
                    'brands' => $brands ?? collect()
                ])
            </div>

            <div class="col-lg-9">
                @include('categories.partials.results', [
                    'products' => $products ?? collect(),
                    'category' => $category
                ])
            </div>
        </div>

        @include('categories.partials.related-categories', [
            'relatedCategories' => $relatedCategories ?? collect()
        ])
    </div>
</div>

<div class="filter-overlay" id="filterOverlay" onclick="toggleMobileFilters()"></div>
@endsection

@push('scripts')
<script>
let currentView = 'grid';

document.addEventListener('DOMContentLoaded', function() {
    initFilters();
    initBackToTop();
    loadViewPreference();
    initProductAnimations();
    initAnalytics();
});

function initFilters() {
    const form = document.getElementById('filtersForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.type === 'text' || input.type === 'number') {
            let timeoutId;
            input.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => form.submit(), 500);
            });
        } else {
            input.addEventListener('change', function() {
                form.submit();
            });
        }
    });

    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Applying...';
            submitBtn.disabled = true;
        }
    });

    initPriceValidation();
}

function initPriceValidation() {
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');

    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('change', function() {
            const minVal = parseInt(this.value) || 0;
            const maxVal = parseInt(maxPriceInput.value) || Infinity;
            if (minVal > maxVal && maxVal !== Infinity) {
                maxPriceInput.value = minVal;
            }
        });

        maxPriceInput.addEventListener('change', function() {
            const maxVal = parseInt(this.value) || Infinity;
            const minVal = parseInt(minPriceInput.value) || 0;
            if (maxVal < minVal && maxVal !== Infinity) {
                minPriceInput.value = maxVal;
            }
        });
    }
}

function toggleMobileFilters() {
    const sidebar = document.getElementById('filtersSidebar');
    const overlay = document.getElementById('filterOverlay');
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : 'auto';
}

function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

function setView(view) {
    currentView = view;
    const grid = document.getElementById('productsGrid');
    const buttons = document.querySelectorAll('.view-btn');

    buttons.forEach(btn => btn.classList.toggle('active', btn.dataset.view === view));
    grid.closest('.col-lg-9').classList.toggle('list-view', view === 'list');
    localStorage.setItem('preferredView', view);
    animateProducts();
}

function setPriceRange(min, max) {
    document.querySelector('input[name="min_price"]').value = min || '';
    document.querySelector('input[name="max_price"]').value = max || '';
    document.getElementById('filtersForm').submit();
}

function removeFilter(filterName) {
    const url = new URL(window.location);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
}

function removePriceFilter() {
    const url = new URL(window.location);
    url.searchParams.delete('min_price');
    url.searchParams.delete('max_price');
    window.location.href = url.toString();
}

function showMoreBrands() {
    const hiddenBrands = document.querySelectorAll('.brand-item.d-none');
    hiddenBrands.forEach(brand => brand.classList.remove('d-none'));
    event.target.style.display = 'none';
}

function loadViewPreference() {
    const savedView = localStorage.getItem('preferredView');
    if (savedView && savedView !== 'grid') setView(savedView);
}

function addToCart(productId) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check me-1"></i>Added!';
            button.classList.replace('btn-primary', 'btn-success');
            updateCartCounter(data.cart_count);
            showNotification('Product added to cart!', 'success');
        } else {
            throw new Error(data.message || 'Failed to add to cart');
        }
    })
    .catch(error => {
        button.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Error';
        button.classList.add('btn-danger');
        showNotification(error.message, 'error');
    })
    .finally(() => {
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success', 'btn-danger');
            button.classList.add('btn-primary');
            button.disabled = false;
        }, 2000);
    });
}

function addToWishlist(productId) {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    const isAdding = icon.classList.contains('far');

    if (isAdding) {
        icon.classList.replace('far', 'fas');
        button.classList.add('text-danger');
    } else {
        icon.classList.replace('fas', 'far');
        button.classList.remove('text-danger');
    }

    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.added ? 'Added to wishlist!' : 'Removed from wishlist', data.added ? 'success' : 'info');
        } else {
            throw new Error(data.message || 'Failed to update wishlist');
        }
    })
    .catch(error => {
        if (isAdding) {
            icon.classList.replace('fas', 'far');
            button.classList.remove('text-danger');
        } else {
            icon.classList.replace('far', 'fas');
            button.classList.add('text-danger');
        }
        showNotification(error.message, 'error');
    });
}

function quickView(productId) {
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    const content = document.getElementById('quickViewContent');

    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    modal.show();

    fetch(`/products/${productId}/quick-view`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = data.success ? data.html : `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>Error Loading Product</h5>
                    <p class="text-muted">${data.message || 'Failed to load product details'}</p>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>Error Loading Product</h5>
                    <p class="text-muted">${error.message}</p>
                </div>
            `;
        });
}

function initBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'auto' });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; position: fixed; box-shadow: 0 4px 15px rgba(0,0,0,0.2);';

    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

function updateCartCounter(count) {
    document.querySelectorAll('.cart-count').forEach(counter => {
        counter.textContent = count || 0;
    });
}

function initProductAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px 0px 0px' });

    animateProducts(observer);
}

function animateProducts(observer = null) {
    const productCards = document.querySelectorAll('.product-item');
    productCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        if (observer) observer.observe(card);
        else setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('filtersSidebar').classList.contains('show')) {
        toggleMobileFilters();
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});

if ('ontouchstart' in window) {
    document.body.classList.add('touch-device');
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('touchstart', () => card.style.transform = 'scale(0.98)');
        card.addEventListener('touchend', () => card.style.transform = '');
    });
}

function trackCategoryView(categoryId, categoryName) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'view_category', {
            category_id: categoryId,
            category_name: categoryName,
            page_location: window.location.href
        });
    }
}

function trackProductView(productId, productName) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'view_item', {
            item_id: productId,
            item_name: productName,
            category: '{{ $category->name ?? '' }}'
        });
    }
}

function initAnalytics() {
    @if(isset($category))
    trackCategoryView({{ $category->id ?? 0 }}, '{{ $category->name ?? '' }}');
    @endif
}
</script>
@endpush

@php
function getCategoryIcon($categoryName) {
    $key = strtolower($categoryName);
    $iconMap = [
        'electronics' => '<i class="fas fa-laptop"></i>',
        'fashion' => '<i class="fas fa-tshirt"></i>',
        'clothing' => '<i class="fas fa-tshirt"></i>',
        'home' => '<i class="fas fa-home"></i>',
        'garden' => '<i class="fas fa-seedling"></i>',
        'sports' => '<i class="fas fa-futbol"></i>',
        'books' => '<i class="fas fa-book"></i>',
        'automotive' => '<i class="fas fa-car"></i>',
        'beauty' => '<i class="fas fa-heart"></i>',
        'toys' => '<i class="fas fa-gamepad"></i>',
        'food' => '<i class="fas fa-utensils"></i>',
        'health' => '<i class="fas fa-heartbeat"></i>'
    ];

    foreach ($iconMap as $keyword => $icon) {
        if (strpos($key, $keyword) !== false) return $icon;
    }
    return '<i class="fas fa-tags"></i>';
}
@endphp

@include('categories.partials.quick-view-modal')
@include('categories.partials.back-to-top')
