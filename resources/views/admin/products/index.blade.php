@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
@php
    // Emergency fix untuk missing variables dan functions
    if (!isset($stats) || !isset($stats['active_products'])) {
        $stats = [
            'active_products' => DB::table('products')->where('status', 'active')->count(),
            'total_products' => DB::table('products')->count(),
            'inactive_products' => DB::table('products')->where('status', '!=', 'active')->count(),
            'low_stock_products' => DB::table('products')->whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
            'out_of_stock_products' => DB::table('products')->where('stock_quantity', 0)->count(),
            'featured_products' => DB::table('products')->where('featured', true)->count()
        ];
    }

    if (!function_exists('format_currency')) {
        function format_currency($cents, $showSymbol = true) {
            if (!is_numeric($cents)) $cents = 0;
            $rupiah = $cents / 100;
            $formatted = number_format($rupiah, 0, ',', '.');
            return $showSymbol ? 'Rp ' . $formatted : $formatted;
        }
    }

    if (!function_exists('format_stock_badge')) {
        function format_stock_badge($stock, $minLevel = 5) {
            if ($stock <= 0) {
                return '<span class="badge bg-danger">Habis</span>';
            } elseif ($stock <= $minLevel) {
                return '<span class="badge bg-warning">Menipis</span>';
            } else {
                return '<span class="badge bg-success">Tersedia</span>';
            }
        }
    }

    // Default data jika tidak ada
    $products = $products ?? collect();
    $categories = $categories ?? collect();
    $brands = $brands ?? collect();
@endphp

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-box mr-2"></i>Kelola Produk
            </h1>
            <p class="text-muted mb-0">Manajemen produk dan inventori toko</p>
        </div>
        <div>
            @if(Route::has('admin.products.create'))
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah Produk
                </a>
            @else
                <a href="#" class="btn btn-primary" onclick="alert('Route admin.products.create belum didefinisikan')">
                    <i class="fas fa-plus mr-2"></i>Tambah Produk
                </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Total Produk</div>
                            <div class="h5 mb-0">{{ number_format($stats['total_products'] ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Produk Aktif</div>
                            <div class="h5 mb-0">{{ number_format($stats['active_products'] ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Stok Menipis</div>
                            <div class="h5 mb-0">{{ number_format($stats['low_stock_products'] ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Stok Habis</div>
                            <div class="h5 mb-0">{{ number_format($stats['out_of_stock_products'] ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Produk Unggulan</div>
                            <div class="h5 mb-0">{{ number_format($stats['featured_products'] ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-secondary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Nonaktif</div>
                            <div class="h5 mb-0">{{ number_format($stats['inactive_products'] ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-pause-circle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filter & Pencarian
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Cari Produk</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Nama, SKU, atau deskripsi...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] ?? $category->id }}"
                                        {{ request('category') == ($category['id'] ?? $category->id) ? 'selected' : '' }}>
                                    {{ $category['name'] ?? $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="sort" class="form-label">Urutkan</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="price_cents" {{ request('sort') == 'price_cents' ? 'selected' : '' }}>Harga</option>
                            <option value="stock_quantity" {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>Stok</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Daftar Produk
            </h6>
            <span class="badge bg-info">
                Total: {{ $products->total() ?? $products->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            @if($products && $products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80px">Gambar</th>
                                <th>Produk</th>
                                <th width="120px">Kategori</th>
                                <th width="100px">Harga</th>
                                <th width="80px">Stok</th>
                                <th width="100px">Status</th>
                                <th width="120px">Dibuat</th>
                                <th width="120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @php
                                    // Handle both array and object format
                                    $productData = is_array($product) ? $product : (array) $product;

                                    $id = $productData['id'] ?? 0;
                                    $name = $productData['name'] ?? 'Unknown Product';
                                    $sku = $productData['sku'] ?? '';
                                    $price_cents = $productData['price_cents'] ?? 0;
                                    $stock_quantity = $productData['stock_quantity'] ?? 0;
                                    $min_stock_level = $productData['min_stock_level'] ?? 5;
                                    $status = $productData['status'] ?? 'draft';
                                    $featured = $productData['featured'] ?? false;
                                    $category_name = $productData['category_name'] ?? 'No Category';
                                    $created_at = $productData['created_at'] ?? now();
                                @endphp
                                <tr>
                                    <td>
                                        <img src="https://via.placeholder.com/60x60/f8f9fa/6c757d?text=IMG"
                                             alt="{{ $name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $name }}</strong>
                                            @if($featured)
                                                <i class="fas fa-star text-warning ms-1" title="Produk Unggulan"></i>
                                            @endif
                                        </div>
                                        <small class="text-muted">SKU: {{ $sku }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $category_name }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ format_currency($price_cents) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold">{{ number_format($stock_quantity) }}</div>
                                        {!! format_stock_badge($stock_quantity, $min_stock_level) !!}
                                    </td>
                                    <td>
                                        @switch($status)
                                            @case('active')
                                                <span class="badge bg-success">Aktif</span>
                                                @break
                                            @case('inactive')
                                                <span class="badge bg-secondary">Nonaktif</span>
                                                @break
                                            @case('draft')
                                                <span class="badge bg-warning">Draft</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(Route::has('admin.products.show'))
                                                <a href="{{ route('admin.products.show', $id) }}"
                                                   class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if(Route::has('admin.products.edit'))
                                                <a href="{{ route('admin.products.edit', $id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteProduct({{ $id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($products, 'links'))
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    Menampilkan {{ $products->firstItem() ?? 1 }} hingga {{ $products->lastItem() ?? $products->count() }}
                                    dari {{ $products->total() ?? $products->count() }} produk
                                </small>
                            </div>
                            <div class="col-md-6">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-box-open fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Tidak Ada Produk</h5>
                    <p class="text-muted mb-3">
                        @if(request()->hasAny(['search', 'category', 'status']))
                            Tidak ada produk yang sesuai dengan filter yang dipilih.
                        @else
                            Belum ada produk yang ditambahkan ke sistem.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ url()->current() }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-undo mr-2"></i>Reset Filter
                        </a>
                    @endif
                    @if(Route::has('admin.products.create'))
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Tambah Produk Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
    font-size: 0.85rem;
}

.table td {
    border-top: 1px solid #e3e6f0;
    font-size: 0.85rem;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

.btn-group .btn {
    border-radius: 0.35rem;
    margin-right: 2px;
}

.badge {
    font-size: 0.75rem;
}

.img-thumbnail {
    border: 1px solid #e3e6f0;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit filter form on change
    $('#filterForm select').change(function() {
        $('#filterForm').submit();
    });

    // Search with delay
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);
    });
});

// Delete Product
function deleteProduct(productId) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        // Check if delete route exists
        @if(Route::has('admin.products.destroy'))
            $.ajax({
                url: "{{ url('admin/products') }}/" + productId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Produk berhasil dihapus.');
                    location.reload();
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus produk.');
                }
            });
        @else
            alert('Fitur hapus produk belum tersedia. Route admin.products.destroy belum didefinisikan.');
        @endif
    }
}

// Show alert helper
function showAlert(message, type = 'info') {
    alert(message);
}
</script>
@endpush
