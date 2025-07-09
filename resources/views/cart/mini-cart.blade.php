{{-- resources/views/partials/mini-cart.blade.php --}}
@if($cartItems && $cartItems->count() > 0)
    <div class="mini-cart-header p-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">Keranjang Belanja</h6>
            <span class="badge bg-primary">{{ $summary['item_count'] }} item</span>
        </div>
    </div>

    <div class="mini-cart-items" style="max-height: 300px; overflow-y: auto;">
        @foreach($cartItems as $item)
            <div class="mini-cart-item p-3 border-bottom">
                <div class="row align-items-center g-2">
                    <div class="col-auto">
                        @if($item->product->images && $item->product->images->count() > 0)
                            <img src="{{ $item->product->images->first()->image_url }}"
                                 alt="{{ $item->product->name }}"
                                 class="rounded"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col">
                        <h6 class="mb-1 small fw-semibold">
                            {{ Str::limit($item->product->name, 30) }}
                        </h6>
                        @if($item->variant)
                            <small class="text-muted d-block">{{ $item->variant->variant_name }}: {{ $item->variant->variant_value }}</small>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted">{{ $item->quantity }}x</small>
                            <small class="fw-semibold text-primary">
                                Rp {{ number_format($item->total_price_cents / 100, 0, ',', '.') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mini-cart-footer p-3 border-top bg-light">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold">Total:</span>
            <span class="fw-bold text-primary">{{ $summary['formatted']['total'] }}</span>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-shopping-cart me-1"></i>Lihat Keranjang
            </a>
            @auth
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-credit-card me-1"></i>Checkout
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i>Login untuk Checkout
                </a>
            @endauth
        </div>
    </div>
@else
    <div class="mini-cart-empty p-4 text-center">
        <div class="text-muted mb-3">
            <i class="fas fa-shopping-cart fa-2x"></i>
        </div>
        <h6 class="mb-2">Keranjang Kosong</h6>
        <p class="small text-muted mb-3">Belum ada produk dalam keranjang</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-shopping-bag me-1"></i>Mulai Belanja
        </a>
    </div>
@endif

<style>
.mini-cart-item:hover {
    background-color: #f8f9fa;
}

.mini-cart-items::-webkit-scrollbar {
    width: 4px;
}

.mini-cart-items::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.mini-cart-items::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.mini-cart-items::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
