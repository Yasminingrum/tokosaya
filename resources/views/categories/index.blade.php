@extends('layouts.app')

@section('title', 'All Categories - TokoSaya')
@section('meta_description', 'Browse all product categories at TokoSaya. Find electronics, fashion, home & garden, and more with great deals.')

@push('styles')
<style>
    .category-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        background: linear-gradient(135deg, #954C2E 0%, #EFE4D2 100%);
        color: white;
        position: relative;
        min-height: 200px;
    }

    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg,
            rgba(255,255,255,0.1) 0%,
            rgba(255,255,255,0.05) 50%,
            rgba(0,0,0,0.05) 100%);
        pointer-events: none;
    }

    .category-card.electronics {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .category-card.fashion {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .category-card.home {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .category-card.sports {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .category-card.books {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .category-card.automotive {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .category-card.beauty {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }

    .category-card.toys {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    }

    .category-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.9;
    }

    .category-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .category-count {
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .category-description {
        opacity: 0.8;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6b7280;
    }

    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
        border-radius: 0 0 2rem 2rem;
    }

    .stats-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        color: white;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .filter-tabs {
        background: #f8fafc;
        border-radius: 12px;
        padding: 0.5rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 0.5rem;
    }

    .filter-tab {
        background: transparent;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        color: #64748b;
        font-weight: 500;
        transition: all 0.2s;
        flex: 1;
        text-align: center;
    }

    .filter-tab.active {
        background: white;
        color: #954C2E;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .featured-banner {
        background: linear-gradient(45deg, #ff6b6b, #feca57);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .category-icon {
            font-size: 2rem;
        }

        .category-name {
            font-size: 1.2rem;
        }

        .filter-tabs {
            flex-direction: column;
        }

        .filter-tab {
            flex: none;
        }
    }

    .category-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .category-link:hover {
        color: inherit;
    }

    .search-section {
        margin-bottom: 2rem;
    }

    .search-input {
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        border: 2px solid #e5e7eb;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: #954C2E;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Explore Categories</h1>
                    <p class="lead mb-4">Discover thousands of products across all categories with amazing deals and quality guarantee</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Shop All Products
                        </a>
                        <a href="#categories" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-arrow-down me-2"></i>Browse Categories
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">{{ $totalCategories ?? 0 }}</div>
                                <div>Categories</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">{{ $totalProducts ?? 0 }}</div>
                                <div>Products</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">{{ $totalBrands ?? 0 }}</div>
                                <div>Brands</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">24/7</div>
                                <div>Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>

        <!-- Search Section -->
        <div class="search-section">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('categories.index') }}">
                        <div class="input-group">
                            <input type="text" class="form-control search-input"
                                   name="search" value="{{ request('search') }}"
                                   placeholder="Search categories...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs" id="categoryFilters">
            <button class="filter-tab active" data-filter="all">All Categories</button>
            <button class="filter-tab" data-filter="popular">Most Popular</button>
            <button class="filter-tab" data-filter="newest">Newest</button>
            <button class="filter-tab" data-filter="az">A-Z</button>
        </div>

        <!-- Featured Categories Banner -->
        @if(isset($featuredCategories) && $featuredCategories->count() > 0)
        <div class="featured-banner">
            <h3 class="mb-2">🔥 Featured Categories This Week</h3>
            <p class="mb-3">Special discounts up to 50% off in these categories!</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                @foreach($featuredCategories->take(3) as $featured)
                <a href="{{ route('categories.show', $featured->slug) }}" class="btn btn-light">
                    {{ $featured->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Categories Grid -->
        <div id="categories" class="categories-section">
            @if($categories && $categories->count() > 0)
            <div class="row g-4" id="categoriesGrid">
                @foreach($categories as $index => $category)
                <div class="col-lg-3 col-md-4 col-sm-6" data-category="{{ strtolower($category->name) }}">
                    <div class="card category-card {{ getCategoryClass($category->name, $index) }} h-100">
                        <a href="{{ route('categories.show', $category->slug) }}" class="category-link">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <div class="category-icon">
                                    {!! getCategoryIcon($category->name) !!}
                                </div>
                                <h5 class="category-name">{{ $category->name }}</h5>
                                <p class="category-count">
                                    {{ number_format($category->products_count ?? 0) }} products
                                </p>
                                @if($category->description)
                                <p class="category-description">
                                    {{ Str::limit($category->description, 60) }}
                                </p>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Load More Button -->
            @if(method_exists($categories, 'hasPages') && $categories->hasPages())
            <div class="text-center mt-5">
                <div class="d-flex justify-content-center">
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            </div>
            @endif

            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <h3>No categories found</h3>
                <p class="text-muted">
                    @if(request('search'))
                        No categories match your search "{{ request('search') }}"
                    @else
                        Categories will appear here when they are added.
                    @endif
                </p>
                @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-primary">View All Categories</a>
                @endif
            </div>
            @endif
        </div>

        <!-- Popular Products by Category -->
        @if(isset($popularByCategory) && $popularByCategory->count() > 0)
        <section class="mt-5">
            <h3 class="fw-bold mb-4">Popular Products by Category</h3>
            @foreach($popularByCategory->take(3) as $category)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>{{ $category->name }}</h5>
                    <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-primary btn-sm">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($category->popularProducts->take(4) as $product)
                    <div class="col-lg-3 col-md-6">
                        @include('components.product-card', ['product' => $product])
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </section>
        @endif

        <!-- Newsletter Signup -->
        <section class="mt-5 mb-4">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center p-4">
                            <h4 class="mb-3">Stay Updated with New Categories</h4>
                            <p class="mb-4">Get notified when we add new product categories and special deals!</p>
                            <form class="row g-3 justify-content-center">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Enter your email">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-light">Subscribe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const categoriesGrid = document.getElementById('categoriesGrid');
    const categoryCards = categoriesGrid.querySelectorAll('[data-category]');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            filterCategories(filter);
        });
    });

    function filterCategories(filter) {
        const cards = Array.from(categoryCards);

        switch(filter) {
            case 'popular':
                // Sort by product count (descending)
                cards.sort((a, b) => {
                    const countA = parseInt(a.querySelector('.category-count').textContent.replace(/\D/g, ''));
                    const countB = parseInt(b.querySelector('.category-count').textContent.replace(/\D/g, ''));
                    return countB - countA;
                });
                break;

            case 'newest':
                // Reverse current order (assuming newest are last)
                cards.reverse();
                break;

            case 'az':
                // Sort alphabetically
                cards.sort((a, b) => {
                    const nameA = a.querySelector('.category-name').textContent.toLowerCase();
                    const nameB = b.querySelector('.category-name').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                });
                break;

            case 'all':
            default:
                // Keep original order - do nothing
                break;
        }

        // Clear and re-append cards
        categoriesGrid.innerHTML = '';
        cards.forEach(card => {
            categoriesGrid.appendChild(card);
        });

        // Add animation
        categoryCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }

    // Smooth scroll to categories
    document.querySelectorAll('a[href="#categories"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('categories').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Search form enhancement
    const searchForm = document.querySelector('form[action="{{ route("categories.index") }}"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');

        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                searchForm.submit();
            }
        });
    }

    // Add loading animation for category cards
    const categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const card = this.closest('.category-card');
            card.style.transform = 'scale(0.95)';
            card.style.opacity = '0.8';

            setTimeout(() => {
                window.location.href = this.href;
            }, 150);
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe category cards for scroll animations
    categoryCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});

// Helper function to get category icon
function getCategoryIcon(categoryName) {
    const icons = {
        'electronics': '<i class="fas fa-laptop"></i>',
        'fashion': '<i class="fas fa-tshirt"></i>',
        'home': '<i class="fas fa-home"></i>',
        'sports': '<i class="fas fa-futbol"></i>',
        'books': '<i class="fas fa-book"></i>',
        'automotive': '<i class="fas fa-car"></i>',
        'beauty': '<i class="fas fa-heart"></i>',
        'toys': '<i class="fas fa-gamepad"></i>',
        'default': '<i class="fas fa-tags"></i>'
    };

    const key = categoryName.toLowerCase();
    return icons[key] || icons['default'];
}

// Newsletter subscription (placeholder)
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;

    if (email) {
        alert('Thank you for subscribing! You will receive updates about new categories and deals.');
        this.reset();
    }
});
</script>
@endpush

@php
// Helper methods for the view
function getCategoryClass($categoryName, $index) {
    $classes = ['electronics', 'fashion', 'home', 'sports', 'books', 'automotive', 'beauty', 'toys'];
    $key = strtolower($categoryName);

    $classMap = [
        'electronics' => 'electronics',
        'fashion' => 'fashion',
        'clothing' => 'fashion',
        'home' => 'home',
        'garden' => 'home',
        'sports' => 'sports',
        'books' => 'books',
        'automotive' => 'automotive',
        'beauty' => 'beauty',
        'toys' => 'toys'
    ];

    foreach ($classMap as $keyword => $class) {
        if (strpos($key, $keyword) !== false) {
            return $class;
        }
    }

    return $classes[$index % count($classes)];
}

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
        if (strpos($key, $keyword) !== false) {
            return $icon;
        }
    }

    return '<i class="fas fa-tags"></i>';
}
@endphp
