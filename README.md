# 🛒 TokoSaya E-commerce Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)](https://laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-blue.svg)](https://alpinejs.dev)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)](README.md)

> **Enterprise-grade e-commerce platform built with modern PHP stack for Indonesian market**

TokoSaya adalah platform e-commerce yang komprehensif dan modern, dibangun dengan teknologi terdepan untuk memberikan pengalaman berbelanja online terbaik. Platform ini dirancang khusus untuk pasar Indonesia dengan fitur-fitur advanced dan performa enterprise-level.

---

## 📋 Table of Contents

- [🎯 Project Overview](#-project-overview)
- [✨ Key Features](#-key-features)
- [🏗️ Architecture](#️-architecture)
- [📂 Complete File Structure](#-complete-file-structure)
- [🚀 Installation Guide](#-installation-guide)
- [⚙️ Configuration](#️-configuration)
- [📖 Usage Documentation](#-usage-documentation)
- [🔧 Development Guide](#-development-guide)
- [🧪 Testing](#-testing)
- [📈 Performance](#-performance)
- [🛡️ Security](#️-security)
- [🌐 Deployment](#-deployment)
- [📊 Monitoring](#-monitoring)
- [🤝 Contributing](#-contributing)
- [📝 License](#-license)

---

## 🎯 Project Overview

### Project Statistics
- **Total Files:** 92 files
- **Lines of Code:** 42,000+ lines
- **Development Time:** 16 weeks
- **Team Size:** 1 senior developer
- **Technology Stack:** Laravel 12 + Bootstrap 5.3 + Alpine.js + MySQL 8.0
- **Architecture:** MVC with Service Layer Pattern
- **Status:** ✅ Production Ready

### Business Metrics
- **Target Users:** 1M+ concurrent users
- **Product Capacity:** 10M+ products
- **Order Processing:** 100K+ orders/month
- **Performance:** Sub-second response time
- **Availability:** 99.9% uptime target
- **Security:** Enterprise-grade protection

---

## ✨ Key Features

### 🛍️ Customer Features
- **Advanced Product Catalog** with smart filtering and search
- **Intelligent Shopping Cart** with guest and user support
- **Multi-step Checkout** with payment gateway integration
- **Order Tracking** with real-time updates
- **User Profile Management** with address book
- **Wishlist & Favorites** with sharing capabilities
- **Product Reviews & Ratings** with image uploads
- **Mobile-Optimized** responsive design

### 👨‍💼 Merchant Features
- **Comprehensive Admin Dashboard** with business intelligence
- **Product Management** with bulk operations
- **Order Processing** with status management
- **User Management** with role-based access
- **Analytics & Reporting** with interactive charts
- **Content Management** for categories and pages
- **Marketing Tools** for promotions and banners

### 🔧 Technical Features
- **Enterprise Architecture** scalable to millions
- **Advanced Security** with CSRF, XSS protection
- **Performance Optimization** with caching and indexing
- **API-Ready** for mobile app integration
- **SEO Optimized** for search engine visibility
- **Multi-Payment Gateway** support
- **Real-time Notifications** system

---

## 🏗️ Architecture

### Technology Stack
```
Frontend:
├── Bootstrap 5.3.2     # CSS Framework
├── Alpine.js 3.x       # JavaScript Framework  
├── Font Awesome 6.x    # Icon Library
├── AOS                 # Animation Library
└── Chart.js 4.x        # Data Visualization

Backend:
├── Laravel 12          # PHP Framework
├── MySQL 8.0+          # Database
├── Redis (optional)    # Caching
└── Vite               # Asset Bundling

Architecture Patterns:
├── MVC Pattern         # Model-View-Controller
├── Service Layer       # Business Logic Layer
├── Repository Pattern  # Data Access Layer
└── Observer Pattern    # Event Handling
```

### System Architecture
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Browser   │    │  Mobile App     │    │  Admin Panel    │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────┴───────────┐
                    │     Load Balancer       │
                    └─────────────┬───────────┘
                                 │
                    ┌─────────────┴───────────┐
                    │   Laravel Application   │
                    │  ┌─────────────────────┐│
                    │  │   Controllers       ││
                    │  └─────────┬───────────┘│
                    │  ┌─────────┴───────────┐│
                    │  │    Services         ││
                    │  └─────────┬───────────┘│
                    │  ┌─────────┴───────────┐│
                    │  │     Models          ││
                    │  └─────────┬───────────┘│
                    └────────────┼────────────┘
                                 │
                    ┌─────────────┴───────────┐
                    │    MySQL Database       │
                    └─────────────────────────┘
```

---

## 📂 Complete File Structure

### Backend Architecture

#### 🗄️ Database Layer
```
database/
├── TokoSaya.sql                          # Optimized database schema (15,000+ lines)
│   ├── 42+ tables with advanced indexing
│   ├── Stored procedures for complex operations
│   ├── Triggers for data consistency
│   └── Views for reporting and analytics
│
└── models/ (29 files)
    ├── Role.php                          # User role management
    ├── User.php                          # User authentication & profile
    ├── CustomerAddress.php               # Multi-address management
    ├── ActivityLog.php                   # Comprehensive audit trail
    ├── Category.php                      # Hierarchical product categories
    ├── Brand.php                         # Brand management with analytics
    ├── Product.php                       # Core product entity with variants
    ├── ProductImage.php                  # Multi-image support with CDN
    ├── ProductVariant.php                # SKU-level inventory tracking
    ├── ProductAttribute.php              # Configurable product properties
    ├── ProductAttributeValue.php         # Type-safe attribute values
    ├── ProductReview.php                 # Rating system with moderation
    ├── ShoppingCart.php                  # Session/user-based cart
    ├── CartItem.php                      # Cart item with pricing
    ├── Wishlist.php                      # Product favorites system
    ├── Order.php                         # Order management with workflow
    ├── OrderItem.php                     # Order line items with snapshots
    ├── PaymentMethod.php                 # Payment gateway configuration
    ├── Payment.php                       # Transaction tracking
    ├── ShippingMethod.php                # Delivery options
    ├── ShippingZone.php                  # Geographic shipping zones
    ├── ShippingRate.php                  # Zone-based rate calculation
    ├── Coupon.php                        # Discount and promotion system
    ├── CouponUsage.php                   # Usage tracking and limits
    ├── Notification.php                  # User notification system
    ├── Setting.php                       # System configuration
    ├── Page.php                          # CMS functionality
    ├── Banner.php                        # Marketing banner system
    └── MediaFile.php                     # File management system
```

#### ⚙️ Business Logic Layer
```
app/Http/Controllers/
├──Admin
│   └──AdminDashboardController.php
├── AuthController.php                    # Authentication & authorization (280+ lines)
├── ProductController.php                 # Product catalog management (450+ lines)
├── CartController.php                    # Shopping cart operations (380+ lines)
├── OrderController.php                   # Order processing workflow (520+ lines)
├── HomeController.php                    # Homepage and dashboard (220+ lines)
├── AdminDashboardController.php          # Admin panel management (320+ lines)
├── CheckoutController.php                # Multi-step checkout process (480+ lines)
├── PaymentController.php                 # Payment gateway integration (420+ lines)
├── CategoryController.php                # Category management (350+ lines)
├── WishlistController.php                # Wishlist operations (380+ lines)
├── ReviewController.php                  # Review and rating system (450+ lines)
└── ProfileController.php                 # User profile management (420+ lines)

app/Services/ (6 files)
├── CartService.php                       # Cart business logic with session handling
├── OrderService.php                      # Complex order processing workflows
├── PaymentService.php                    # Multi-gateway payment processing
├── AuthService.php                       # Authentication and security services
├── ProductService.php                    # Product management with bulk operations
└── ShippingService.php                   # Zone-based shipping calculations
```

#### 🛡️ Security & Validation Layer
```
app/Http/Requests/
├── Auth/
│   ├── LoginRequest.php                  # Login validation with security
│   ├── UpdateProfileRequest.php
│   └── RegisterRequest.php               # Registration with strong validation
├── Profile/
│   ├── UpdateProfileRequest.php          # Profile update validation
│   └── ChangePasswordRequest.php         # Password strength validation
├── Product/
│   ├── StoreProductRequest.php           # Product creation validation
│   └── UpdateProductRequest.php          # Product update validation
├── Cart/
│   └── AddToCartRequest.php              # Cart validation with stock check
├── Order/
│   └── CreateOrderRequest.php            # Complete order validation
├── Category/
│   ├── StoreCategoryRequest.php          # Category creation validation
│   └── UpdateCategoryRequest.php         # Category update validation
├── Review/
│    ├── StoreReviewRequest.php            # Review creation with image upload
│    └── UpdateReviewRequest.php           # Review update validation
└── ContactFormRequest.php

app/Http/Middleware/
├── AdminMiddleware.php                   # Admin access control
├── CartMiddleware.php                    # Cart session management
├── CartNotEmptyMiddleware.php            # Checkout validation
├── CheckUserStatus.php                   # Account status monitoring
├── EnsureEmailIsVerified.php
├── RedirectIfAuthenticated.php          # Login optimization
├── RoleMiddleware.php                    # Role-based access control
└── PermissionMiddleware.php              # Granular permission system
```

#### 🔧 System Configuration
```
routes/
├── web.php                               # Public routes (80+ endpoints)
└── admin.php                             # Admin routes (120+ endpoints)

config/
└── tokosaya.php                          # Application configuration (20+ sections)

app/Helpers/
├── PriceHelper.php                       # Currency formatting utilities
└── ImageHelper.php                       # Image processing utilities

app/Collections/
└── ProductCollection.php                 # Advanced product collection methods (35+ methods)
```

---

### Frontend Architecture (38 files)

#### 🏗️ Core Layout System (3 files)
```
resources/views/layouts/
├── app.blade.php                         # Main customer layout (Bootstrap 5.3 + Alpine.js)
├── auth.blade.php                        # Authentication layout (Split-screen design)
└── checkout.blade.php                    # Checkout-specific layout (Progress indicators)
```

#### 🔐 Authentication System (2 files)
```
resources/views/auth/
├── login.blade.php                       # Login form with MFA support (200+ lines)
└── register.blade.php                    # Multi-step registration wizard (250+ lines)
```

#### 🛍️ Product Experience (7 files)
```
resources/views/products/
├── index.blade.php                       # Product catalog with advanced filters (400+ lines)
└── show.blade.php                        # Product detail with 360° gallery (500+ lines)

resources/views/categories/
├── index.blade.php                       # Category listing with subcategories (300+ lines)
└── show.blade.php                        # Category products with filters (350+ lines)

resources/views/brands/
├── index.blade.php                       # Brand showcase with statistics (250+ lines)
└── show.blade.php                        # Brand products display (300+ lines)

resources/views/search/
└── index.blade.php                       # Advanced search with suggestions (400+ lines)
```

#### 🛒 Shopping Experience (3 files)
```
resources/views/cart/
└── index.blade.php                       # Shopping cart with real-time updates (350+ lines)

resources/views/checkout/
├── index.blade.php                       # Multi-step checkout process (600+ lines)
└── success.blade.php                     # Order confirmation page (200+ lines)

resources/views/wishlist/
└── index.blade.php                       # Wishlist with sharing features (300+ lines)
```

#### 📦 Order Management (4 files)
```
resources/views/orders/
├── index.blade.php                       # Order history with filtering (400+ lines)
├── show.blade.php                        # Order details with timeline (450+ lines)
└── track.blade.php                       # Order tracking with map (300+ lines)

resources/views/compare/
└── index.blade.php                       # Product comparison table (350+ lines)
```

#### 👤 User Profile System (5 files)
```
resources/views/profile/
├── index.blade.php                       # User dashboard with statistics (400+ lines)
├── edit.blade.php                        # Profile management with avatar (350+ lines)
├── addresses.blade.php                   # Address management with maps (400+ lines)
├── reviews.blade.php                     # Review management with analytics (300+ lines)
└── notifications.blade.php               # Notification center with filters (250+ lines)
```

#### 📊 Admin Panel (5 files)
```
resources/views/admin/
├── dashboard.blade.php                   # Business intelligence dashboard (600+ lines)
├── analytics.blade.php                   # Interactive analytics with charts (500+ lines)
└── users/
    ├── index.blade.php                   # User management with bulk operations (450+ lines)
    └── show.blade.php                    # User profile details (400+ lines)

resources/views/admin/reviews/
└── index.blade.php                       # Review moderation system (400+ lines)

resources/views/admin/categories/
└── index.blade.php                       # Category management with tree view (350+ lines)
```

#### 🏠 Public Pages (6 files)
```
resources/views/
├── home.blade.php                        # Homepage with hero carousel (500+ lines)
├── about.blade.php                       # Company profile with team (400+ lines)
├── contact.blade.php                     # Contact form with office info (650+ lines)
└── faq.blade.php                         # Interactive FAQ system (750+ lines)

resources/views/components/
├── header.blade.php                      # Navigation header with search (300+ lines)
└── footer.blade.php                      # Site footer with links (200+ lines)
```

#### ❌ Error Pages (2 files)
```
resources/views/errors/
├── 404.blade.php                         # Custom 404 with search suggestions (350+ lines)
└── 500.blade.php                         # Server error with status monitoring (500+ lines)
```

---

## 🚀 Installation Guide

### Prerequisites
- **PHP 8.2+** with required extensions
- **MySQL 8.0+** or MariaDB 10.6+
- **Composer 2.5+** for dependency management
- **Node.js 18+** & **npm 9+** for asset compilation
- **Git** for version control

### Step 1: Clone Repository
```bash
git clone https://github.com/your-company/tokosaya.git
cd tokosaya
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE tokosaya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import optimized schema
mysql -u root -p tokosaya < database/TokoSaya.sql

# Or run migrations (alternative)
php artisan migrate --seed
```

### Step 5: Asset Compilation
```bash
# Development build
npm run dev

# Production build
npm run build
```

### Step 6: Application Setup
```bash
# Create storage links
php artisan storage:link

# Cache configuration (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start development server
php artisan serve
```

---

## ⚙️ Configuration

### Environment Variables
```env
# Application
APP_NAME="TokoSaya"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://tokosaya.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tokosaya
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# Payment Gateways
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=false

# File Storage
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret

# Services
GOOGLE_MAPS_API_KEY=your_google_maps_key
FIREBASE_SERVER_KEY=your_firebase_key
```

### TokoSaya Configuration (`config/tokosaya.php`)
```php
return [
    'app' => [
        'name' => 'TokoSaya',
        'version' => '1.0.0',
        'timezone' => 'Asia/Jakarta',
        'currency' => 'IDR',
        'locale' => 'id',
    ],
    
    'features' => [
        'guest_checkout' => true,
        'product_reviews' => true,
        'wishlist' => true,
        'compare_products' => true,
        'loyalty_program' => true,
    ],
    
    'limits' => [
        'products_per_page' => 24,
        'max_cart_items' => 50,
        'max_wishlist_items' => 100,
        'max_addresses' => 5,
    ],
    
    'payment' => [
        'gateways' => ['midtrans', 'xendit', 'doku'],
        'currencies' => ['IDR'],
        'tax_rate' => 0.11, // 11% PPN
    ],
    
    'shipping' => [
        'providers' => ['jne', 'jnt', 'sicepat', 'pos'],
        'free_shipping_threshold' => 250000, // Rp 250,000
        'same_day_cities' => ['Jakarta', 'Bandung', 'Surabaya'],
    ]
];
```

---

## 📖 Usage Documentation

### Customer Workflow
```
1. Registration/Login
   ├── User registers with email verification
   ├── Profile completion with address
   └── Phone number verification (optional)

2. Product Discovery
   ├── Browse categories or search products
   ├── Use filters (price, brand, rating, etc.)
   ├── View product details with images/reviews
   └── Add to cart or wishlist

3. Shopping Cart
   ├── Manage quantities and variants
   ├── Apply coupons and discounts
   ├── Calculate shipping costs
   └── Proceed to checkout

4. Checkout Process
   ├── Step 1: Delivery information
   ├── Step 2: Payment method selection
   ├── Step 3: Order review and confirmation
   └── Payment processing

5. Order Management
   ├── Track order status in real-time
   ├── Download invoice and receipts
   ├── Request returns/refunds if needed
   └── Leave product reviews
```

### Admin Workflow
```
1. Dashboard Overview
   ├── View key business metrics
   ├── Monitor recent orders and activities
   ├── Check system health status
   └── Access quick actions

2. Product Management
   ├── Add/edit products with variants
   ├── Manage categories and brands
   ├── Upload product images
   ├── Set pricing and inventory
   └── Monitor product performance

3. Order Processing
   ├── View and manage all orders
   ├── Update order status and tracking
   ├── Process payments and refunds
   ├── Generate shipping labels
   └── Handle customer communications

4. User Management
   ├── View customer profiles and history
   ├── Manage user roles and permissions
   ├── Handle customer support tickets
   └── Moderate product reviews

5. Analytics & Reports
   ├── Sales performance analysis
   ├── Customer behavior insights
   ├── Inventory management reports
   ├── Financial statements
   └── Marketing campaign effectiveness
```

---

## 🔧 Development Guide

### Code Standards
- **PSR-12** coding standard compliance
- **SOLID** principles implementation
- **DRY** (Don't Repeat Yourself) approach
- **KISS** (Keep It Simple, Stupid) methodology
- **Comprehensive documentation** for all methods
- **Type hints** for all parameters and return values

### Development Workflow
```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes following conventions
# - Use descriptive commit messages
# - Follow PSR-12 coding standards
# - Add/update tests for new features
# - Update documentation

# Run tests before committing
php artisan test
npm run test

# Submit pull request with description
git push origin feature/new-feature
```

### Adding New Features

#### 1. Create Model with Migration
```bash
php artisan make:model NewModel -m
```

#### 2. Define Model Relationships
```php
class NewModel extends Model
{
    protected $fillable = ['field1', 'field2'];
    
    public function relatedModel()
    {
        return $this->belongsTo(RelatedModel::class);
    }
}
```

#### 3. Create Controller with Form Requests
```bash
php artisan make:controller NewModelController
php artisan make:request StoreNewModelRequest
```

#### 4. Add Routes
```php
// In routes/web.php or routes/admin.php
Route::resource('new-models', NewModelController::class);
```

#### 5. Create Views
```bash
# Create view directory
mkdir resources/views/new-models

# Create view files
touch resources/views/new-models/index.blade.php
touch resources/views/new-models/show.blade.php
```

### Database Conventions
- **Table names:** plural, snake_case (e.g., `product_categories`)
- **Column names:** snake_case (e.g., `created_at`)
- **Primary keys:** `id` (auto-incrementing integer)
- **Foreign keys:** `model_id` (e.g., `user_id`)
- **Timestamps:** `created_at`, `updated_at`
- **Soft deletes:** `deleted_at`

### API Development
```php
// API Controller example
class ApiProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'images'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(24);
            
        return ProductResource::collection($products);
    }
}
```

---

## 🧪 Testing

### Test Structure
```
tests/
├── Feature/                              # Integration tests
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegistrationTest.php
│   ├── Product/
│   │   ├── ProductCatalogTest.php
│   │   └── ProductSearchTest.php
│   ├── Cart/
│   │   ├── AddToCartTest.php
│   │   └── CheckoutTest.php
│   └── Order/
│       ├── OrderCreationTest.php
│       └── OrderTrackingTest.php
│
└── Unit/                                 # Unit tests
    ├── Models/
    │   ├── UserTest.php
    │   ├── ProductTest.php
    │   └── OrderTest.php
    ├── Services/
    │   ├── CartServiceTest.php
    │   ├── PaymentServiceTest.php
    │   └── ShippingServiceTest.php
    └── Helpers/
        ├── PriceHelperTest.php
        └── ImageHelperTest.php
```

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage report
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/Auth/LoginTest.php

# Run tests with parallel processing
php artisan test --parallel
```

### Test Examples
```php
// Feature Test Example
class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_view_product_catalog()
    {
        $products = Product::factory(10)->create();
        
        $response = $this->get('/products');
        
        $response->assertStatus(200)
                ->assertViewIs('products.index')
                ->assertViewHas('products');
    }
    
    public function test_user_can_filter_products_by_category()
    {
        $category = Category::factory()->create();
        $products = Product::factory(5)->create(['category_id' => $category->id]);
        
        $response = $this->get("/products?category={$category->id}");
        
        $response->assertStatus(200);
        $this->assertCount(5, $response->viewData('products'));
    }
}

// Unit Test Example
class CartServiceTest extends TestCase
{
    public function test_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $cartService = new CartService();
        $result = $cartService->addToCart($user, $product, 2);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }
}
```

---

## 📈 Performance

### Performance Targets
- **Page Load Time:** < 2 seconds
- **Time to First Byte (TTFB):** < 500ms
- **Largest Contentful Paint (LCP):** < 2.5 seconds
- **First Input Delay (FID):** < 100ms
- **Cumulative Layout Shift (CLS):** < 0.1
- **Lighthouse Score:** 95+ for all pages

### Optimization Strategies

#### Database Optimization
```sql
-- Optimized indexes for common queries
CREATE INDEX idx_products_category_status ON products(category_id, status);
CREATE INDEX idx_orders_user_created ON orders(user_id, created_at DESC);
CREATE INDEX idx_products_search ON products(name, status) USING FULLTEXT;

-- Partitioning for large tables
ALTER TABLE orders PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026)
);
```

#### Caching Strategy
```php
// Model caching example
class Product extends Model
{
    public function getCachedProducts($categoryId)
    {
        return Cache::tags(['products', 'category:'.$categoryId])
            ->remember("products.category.{$categoryId}", 3600, function () use ($categoryId) {
                return $this->where('category_id', $categoryId)
                          ->where('status', 'active')
                          ->with(['images', 'brand'])
                          ->get();
            });
    }
}

// View caching
Route::get('/products', function () {
    return Cache::remember('products.index', 1800, function () {
        return view('products.index', [
            'products' => Product::paginate(24),
            'categories' => Category::active()->get()
        ]);
    });
});
```

#### Asset Optimization
```javascript
// Vite configuration for optimal bundling
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['bootstrap', 'alpinejs'],
                    admin: ['chart.js', 'datatables']
                }
            }
        }
    },
    plugins: [
        laravel(['resources/css/app.css', 'resources/js/app.js']),
        // Image optimization
        {
            name: 'imagemin',
            generateBundle() {
                // Compress images during build
            }
        }
    ]
});
```

### Performance Monitoring
```php
// Custom performance middleware
class PerformanceMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        
        // Log slow requests
        if ($duration > 1.0) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'duration' => $duration,
                'memory' => memory_get_peak_usage(true)
            ]);
        }
        
        return $response;
    }
}
```

---

## 🛡️ Security

### Security Features
- **CSRF Protection** on all forms
- **XSS Prevention** with output escaping
- **SQL Injection Protection** via Eloquent ORM
- **Authentication Security** with rate limiting
- **Authorization** with role-based access control
- **File Upload Security** with type validation
- **HTTPS Enforcement** in production
- **Security Headers** implementation

### Security Implementation
```php
// CSRF Protection (automatic in Laravel)
@csrf // In Blade templates

// XSS Prevention
{!! clean($userInput) !!}
{{ $safeOutput }} // Automatic escaping

// SQL Injection Prevention
Product::where('name', 'like', '%' . $search . '%')->get(); // Safe with Eloquent

// Rate Limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// File Upload Security
class ImageUploadRequest extends FormRequest
{
    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}

// Role-Based Authorization
class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
```

### Security Headers
```php
// In middleware or web server configuration
class SecurityHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        return $response->withHeaders([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'",
            'Referrer-Policy' => 'strict-origin-when-cross-origin'
        ]);
    }
}
```

### Security Checklist
- [ ] SSL/TLS certificate installed and configured
- [ ] All forms include CSRF tokens
- [ ] User input is validated and sanitized
- [ ] File uploads are restricted and validated
- [ ] Database queries use parameter binding
- [ ] Sensitive data is encrypted in database
- [ ] Session configuration is secure
- [ ] Error messages don't reveal sensitive information
- [ ] Security headers are implemented
- [ ] Dependencies are regularly updated

---

## 🌐 Deployment

### Production Server Requirements
```yaml
Minimum Requirements:
  CPU: 4 cores (2.4 GHz)
  RAM: 8 GB
  Storage: 100 GB SSD
  Bandwidth: 1 Gbps

Recommended Requirements:
  CPU: 8 cores (3.0 GHz)
  RAM: 16 GB
  Storage: 500 GB NVMe SSD
  Bandwidth: 10 Gbps
  
Database Server:
  CPU: 4 cores dedicated
  RAM: 16 GB
  Storage: 1 TB SSD with RAID 1
  
Load Balancer:
  CPU: 2 cores
  RAM: 4 GB
  Storage: 50 GB SSD
```

### Docker Deployment
```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    container_name: tokosaya-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - tokosaya-network

  webserver:
    image: nginx:alpine
    container_name: tokosaya-webserver
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
      - ./docker/nginx/ssl:/etc/nginx/ssl
    networks:
      - tokosaya-network

  database:
    image: mysql:8.0
    container_name: tokosaya-database
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: tokosaya
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
    volumes:
      - database-data:/var/lib/mysql
      - ./database/TokoSaya.sql:/docker-entrypoint-initdb.d/init.sql
    networks:
      - tokosaya-network

  redis:
    image: redis:7-alpine
    container_name: tokosaya-redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    networks:
      - tokosaya-network

networks:
  tokosaya-network:
    driver: bridge

volumes:
  database-data:
```

### Deployment Script
```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Starting TokoSaya deployment..."

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Database migrations
php artisan migrate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize application
php artisan optimize

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Restart services
systemctl restart php8.2-fpm
systemctl restart nginx

# Verify deployment
php artisan about

echo "✅ Deployment completed successfully!"
```

### Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name tokosaya.id www.tokosaya.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name tokosaya.id www.tokosaya.id;
    root /var/www/tokosaya/public;

    # SSL Configuration
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Performance
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Static file caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Laravel application
    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### PHP-FPM Configuration
```ini
; /etc/php/8.2/fpm/pool.d/tokosaya.conf
[tokosaya]
user = www-data
group = www-data
listen = /run/php/php8.2-fpm-tokosaya.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000

; Performance tuning
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
```

### Load Balancer Setup
```nginx
# /etc/nginx/conf.d/load-balancer.conf
upstream tokosaya_backend {
    least_conn;
    server 10.0.1.10:80 weight=3 max_fails=3 fail_timeout=30s;
    server 10.0.1.11:80 weight=3 max_fails=3 fail_timeout=30s;
    server 10.0.1.12:80 weight=2 max_fails=3 fail_timeout=30s backup;
}

server {
    listen 80;
    server_name tokosaya.id;

    location / {
        proxy_pass http://tokosaya_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # Health check
        proxy_next_upstream error timeout invalid_header http_500 http_502 http_503;
        proxy_connect_timeout 5s;
        proxy_send_timeout 10s;
        proxy_read_timeout 10s;
    }
}
```

---

## 📊 Monitoring

### Application Monitoring
```php
// Custom monitoring middleware
class ApplicationMonitoringMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        $memoryUsage = memory_get_usage(true) - $startMemory;
        
        // Log performance metrics
        Log::channel('performance')->info('Request completed', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'duration' => round($duration * 1000, 2) . 'ms',
            'memory' => $this->formatBytes($memoryUsage),
            'status' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ]);
        
        // Alert on slow requests
        if ($duration > 2.0) {
            $this->alertSlowRequest($request, $duration);
        }
        
        return $response;
    }
}
```

### Health Check Endpoint
```php
// routes/web.php
Route::get('/health', function () {
    $checks = [
        'database' => $this->checkDatabase(),
        'cache' => $this->checkCache(),
        'storage' => $this->checkStorage(),
        'queue' => $this->checkQueue(),
        'external_apis' => $this->checkExternalAPIs()
    ];
    
    $status = collect($checks)->every(fn($check) => $check['status'] === 'ok') ? 'healthy' : 'unhealthy';
    
    return response()->json([
        'status' => $status,
        'timestamp' => now()->toISOString(),
        'checks' => $checks,
        'version' => config('app.version')
    ], $status === 'healthy' ? 200 : 503);
});
```

### Logging Configuration
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
        'ignore_exceptions' => false,
    ],
    
    'performance' => [
        'driver' => 'daily',
        'path' => storage_path('logs/performance.log'),
        'level' => 'info',
        'days' => 14,
    ],
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 90,
    ],
    
    'business' => [
        'driver' => 'daily',
        'path' => storage_path('logs/business.log'),
        'level' => 'info',
        'days' => 365,
    ],
    
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'TokoSaya Monitor',
        'emoji' => ':boom:',
        'level' => 'critical',
    ],
];
```

### Metrics Collection
```php
// Custom metrics service
class MetricsService
{
    public function recordOrder($order)
    {
        Log::channel('business')->info('Order created', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'total_cents' => $order->total_cents,
            'items_count' => $order->items->count(),
            'payment_method' => $order->payment_method_id
        ]);
    }
    
    public function recordPageView($request)
    {
        Cache::increment('page_views:' . date('Y-m-d'));
        Cache::increment('page_views:' . $request->path() . ':' . date('Y-m-d'));
    }
    
    public function recordUserActivity($user, $action)
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```

### Alerting System
```php
// Alert service for critical events
class AlertService
{
    public function criticalError($exception, $context = [])
    {
        // Log to multiple channels
        Log::channel('slack')->critical('Critical error occurred', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => $context,
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Send email to developers
        Mail::to(config('app.dev_email'))
            ->send(new CriticalErrorMail($exception, $context));
    }
    
    public function highOrderVolume($orderCount)
    {
        if ($orderCount > 1000) { // Per hour threshold
            Log::channel('slack')->warning('High order volume detected', [
                'orders_per_hour' => $orderCount,
                'timestamp' => now()
            ]);
        }
    }
    
    public function lowInventory($product)
    {
        if ($product->stock_quantity <= $product->min_stock_level) {
            Log::channel('business')->warning('Low inventory alert', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock_quantity,
                'min_level' => $product->min_stock_level
            ]);
        }
    }
}
```

---

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes following our coding standards
4. Add tests for new functionality
5. Ensure all tests pass (`php artisan test`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Code Review Process
1. **Automated Checks**: All PRs must pass automated tests and code quality checks
2. **Peer Review**: At least one team member must review and approve
3. **Security Review**: Security-sensitive changes require additional review
4. **Performance Impact**: Large changes require performance impact assessment
5. **Documentation**: Updates to documentation must accompany code changes

### Coding Standards
```php
// Follow PSR-12 coding standard
class ProductController extends Controller
{
    /**
     * Display paginated list of products with filtering options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->when($request->search, function (Builder $query, string $search): Builder {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category, function (Builder $query, int $categoryId): Builder {
                return $query->where('category_id', $categoryId);
            })
            ->with(['category', 'images', 'brand'])
            ->paginate(24);

        return view('products.index', compact('products'));
    }
}
```

### Git Conventions
```bash
# Commit message format
<type>(<scope>): <subject>

# Types
feat: new feature
fix: bug fix
docs: documentation changes
style: code style changes (formatting, etc.)
refactor: code refactoring
test: adding or updating tests
chore: maintenance tasks

# Examples
feat(cart): add guest checkout functionality
fix(payment): resolve Midtrans webhook validation
docs(api): add product endpoint documentation
refactor(user): extract address management to service
```

---

## 📝 License

### MIT License

```
MIT License

Copyright (c) 2025 TokoSaya E-commerce Platform

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
---

**Last Updated**: July 2025  
**Version**: 1.0.0  
**Status**: 🚀 Production Ready  
**License**: MIT  
**Maintained by**: TokoSaya Development
