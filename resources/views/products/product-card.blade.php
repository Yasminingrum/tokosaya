{{-- File: resources/views/components/product-card.blade.php --}}
{{-- PERBAIKAN PRODUCT CARD DENGAN ERROR HANDLING --}}

<div class="card h-100 product-card">
    <div class="position-relative">
        {{-- PERBAIKAN: Gunakan null coalescing dan error handling untuk gambar --}}
        <img src="{{ $product->primary_image ?? asset('images/placeholder-product.jpg') }}"
             class="card-img-top"
             alt="{{ $product->name ?? 'Produk' }}"
             style="height: 200px; object-fit: cover;"
             loading="lazy"
             onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">

        {{-- PERBAIKAN: Cek semua property sebelum calculate discount --}}
        @if(isset($product->compare_price_cents) && isset($product->price_cents) &&
            $product->compare_price_cents && $product->price_cents &&
            $product->compare_price_cents > $product->price_cents)
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge bg-danger">
                    -{{ number_format((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100) }}%
                </span>
            </div>
        @endif

        {{-- PERBAIKAN: Cek stock_quantity dengan default value --}}
        @if(($product->stock_quantity ?? 0) <= 5 && ($product->stock_quantity ?? 0) > 0)
            <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-warning">Low Stock</span>
            </div>
        @endif
    </div>

    <div class="card-body d-flex flex-column">
        <h6 class="card-title">
            <a href="{{ route('products.show', $product->slug ?? $product->id) }}"
               class="text-decoration-none text-dark">
                {{ Str::limit($product->name ?? 'Produk Tanpa Nama', 50) }}
            </a>
        </h6>

        <div class="mb-2">
            {{-- PERBAIKAN: Cek category relationship exists --}}
            @if(isset($product->category) && $product->category)
                <small class="text-muted">{{ $product->category->name ?? 'Kategori' }}</small>
            @else
                <small class="text-muted">Uncategorized</small>
            @endif

            {{-- PERBAIKAN: Cek brand relationship exists --}}
            @if(isset($product->brand) && $product->brand)
                <small class="text-muted"> • {{ $product->brand->name }}</small>
            @endif
        </div>

        {{-- PERBAIKAN: Cek rating_average exists dan lebih dari 0 --}}
        @if(isset($product->rating_average) && $product->rating_average > 0)
        <div class="mb-2">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= floor($product->rating_average))
                    <i class="fas fa-star text-warning"></i>
                @elseif($i - 0.5 <= $product->rating_average)
                    <i class="fas fa-star-half-alt text-warning"></i>
                @else
                    <i class="far fa-star text-muted"></i>
                @endif
            @endfor
            <small class="text-muted">({{ $product->rating_count ?? 0 }})</small>
        </div>
        @endif

        <div class="mt-auto">
            <div class="mb-2">
                <span class="fw-bold text-primary fs-6">
                    {{-- PERBAIKAN: Pastikan price_cents ada dengan default 0 --}}
                    Rp {{ number_format(($product->price_cents ?? 0) / 100, 0, ',', '.') }}
                </span>

                {{-- PERBAIKAN: Cek semua kondisi untuk compare price --}}
                @if(isset($product->compare_price_cents) && isset($product->price_cents) &&
                    $product->compare_price_cents && $product->price_cents &&
                    $product->compare_price_cents > $product->price_cents)
                    <br>
                    <small class="text-muted text-decoration-line-through">
                        Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                    </small>
                @endif
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm"
                        onclick="addToWishlist({{ $product->id }})"
                        title="Add to Wishlist">
                    <i class="far fa-heart"></i>
                </button>

                {{-- PERBAIKAN: Cek stock dengan default 0 --}}
                @if(($product->stock_quantity ?? 0) > 0)
                    <button class="btn btn-primary btn-sm flex-grow-1"
                            onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                    </button>
                @else
                    <button class="btn btn-outline-secondary btn-sm flex-grow-1" disabled>
                        Out of Stock
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
