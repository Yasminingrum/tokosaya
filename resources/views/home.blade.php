@extends('layouts.app')

@section('title', 'TokoSaya - Belanja Mudah, Terpercaya')
@section('description', 'Platform e-commerce modern dengan ribuan produk berkualitas, pengiriman cepat, dan pengalaman berbelanja terbaik.')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <div class="py-5">
                    <p class="text-primary text-uppercase fw-bold mb-3">Selamat Datang di</p>
                    <h1 class="display-4 fw-bold mb-4">
                        Toko<span class="text-primary">Saya</span>
                    </h1>
                    <p class="lead text-muted mb-4">
                        Temukan produk berkualitas dengan pengalaman berbelanja yang tak terlupakan.
                        Platform e-commerce terpercaya dengan ribuan produk pilihan.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Jelajahi Produk
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-tags me-2"></i>Lihat Kategori
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="p-5">
                    <div class="bg-white rounded-4 p-5 shadow-sm">
                        <i class="fas fa-store display-1 text-primary mb-4"></i>
                        <h3 class="fw-bold">TokoSaya</h3>
                        <p class="text-muted">Belanja Mudah & Terpercaya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="text-center h-100">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-shipping-fast text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Pengiriman Cepat</h5>
                    <p class="text-muted small">Gratis ongkir minimal Rp 250rb</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center h-100">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-shield-alt text-success fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">100% Aman</h5>
                    <p class="text-muted small">Transaksi terenkripsi</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center h-100">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-medal text-warning fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Kualitas Terjamin</h5>
                    <p class="text-muted small">Produk 100% original</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center h-100">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-headset text-info fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Support 24/7</h5>
                    <p class="text-muted small">Tim siap membantu</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Links -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Jelajahi TokoSaya</h2>
            <p class="text-muted">Temukan apa yang Anda butuhkan</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-box text-primary fs-4"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Semua Produk</h5>
                        <p class="text-muted mb-4">Jelajahi ribuan produk berkualitas dari berbagai kategori</p>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            Lihat Produk <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-tags text-success fs-4"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Kategori</h5>
                        <p class="text-muted mb-4">Cari produk berdasarkan kategori yang Anda inginkan</p>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-success">
                            Lihat Kategori <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-info-circle text-info fs-4"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Tentang Kami</h5>
                        <p class="text-muted mb-4">Pelajari lebih lanjut tentang TokoSaya dan komitmen kami</p>
                        <a href="{{ route('about') }}" class="btn btn-outline-info">
                            Pelajari <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">Dapatkan Update Terbaru</h2>
                <p class="mb-4">Berlangganan newsletter untuk penawaran eksklusif dan produk terbaru</p>

                <form class="row g-2 justify-content-center" id="newsletterForm">
                    @csrf
                    <div class="col-auto flex-fill" style="max-width: 300px;">
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="Masukkan email Anda"
                               required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Berlangganan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.min-vh-50 {
    min-height: 50vh;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 TokoSaya Homepage loaded');

    // Load initial cart count
    testLoadCartCount();

    // Newsletter form
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            button.disabled = true;

            setTimeout(() => {
                alert('✅ Terima kasih! Anda telah berlangganan newsletter kami.');
                this.reset();
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1500);
        });
    }
});

// Test functions for cart
function testAddToCart() {
    console.log('🛒 Testing add to cart...');
    document.getElementById('apiStatus').className = 'badge bg-warning';
    document.getElementById('apiStatus').textContent = 'Testing...';

    // Simulate add to cart dengan data dummy
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: 1, // ID dummy
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('📦 Cart response:', data);

        if (data.success) {
            document.getElementById('apiStatus').className = 'badge bg-success';
            document.getElementById('apiStatus').textContent = 'Success';

            alert('✅ Test berhasil! Item ditambahkan ke cart.\nCart count akan update otomatis.');

            // Update cart count
            testLoadCartCount();
        } else {
            document.getElementById('apiStatus').className = 'badge bg-danger';
            document.getElementById('apiStatus').textContent = 'Error';

            alert('❌ Test gagal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('❌ Cart error:', error);

        document.getElementById('apiStatus').className = 'badge bg-danger';
        document.getElementById('apiStatus').textContent = 'Failed';

        alert('❌ Error: ' + error.message);
    });
}

function testLoadCartCount() {
    console.log('🔄 Loading cart count...');

    fetch('/cart/count')
        .then(response => response.json())
        .then(data => {
            console.log('📊 Cart count:', data);

            const count = data.count || 0;
            document.getElementById('cartStatus').textContent = count;

            // Update cart badge di header juga
            if (typeof loadCartCount === 'function') {
                loadCartCount();
            }
        })
        .catch(error => {
            console.error('❌ Failed to load cart count:', error);
            document.getElementById('cartStatus').textContent = '?';
        });
}

function testCartAPI() {
    console.log('🧪 Testing cart API endpoints...');

    const tests = [
        { name: 'Cart Count', url: '/cart/count' },
        { name: 'Cart Data', url: '/cart/data' }
    ];

    let results = [];

    Promise.all(tests.map(test =>
        fetch(test.url)
            .then(response => response.json())
            .then(data => ({ name: test.name, status: 'OK', data }))
            .catch(error => ({ name: test.name, status: 'ERROR', error: error.message }))
    )).then(allResults => {
        results = allResults;

        let message = '🧪 API Test Results:\n\n';
        results.forEach(result => {
            message += `${result.name}: ${result.status}\n`;
            if (result.data) {
                message += `  Data: ${JSON.stringify(result.data)}\n`;
            }
            if (result.error) {
                message += `  Error: ${result.error}\n`;
            }
            message += '\n';
        });

        alert(message);
    });
}

// Global add to cart function (jika belum ada)
if (typeof window.addToCart === 'undefined') {
    window.addToCart = function(productId, quantity = 1) {
        return fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                testLoadCartCount(); // Update display
                alert('✅ Produk berhasil ditambahkan ke keranjang!');
            } else {
                alert('❌ ' + (data.message || 'Gagal menambahkan produk'));
            }
            return data;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan');
            throw error;
        });
    };
}
</script>
@endpush
