@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Product Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Product
            </a>
            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                    data-bs-toggle="dropdown">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportProducts('excel')">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportProducts('csv')">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-upload me-2"></i>Import Products
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Products</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_products']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box-open fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Active Products</h6>
                            <h2 class="mb-0">{{ number_format($stats['active_products']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Low Stock</h6>
                            <h2 class="mb-0">{{ number_format($stats['low_stock']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Out of Stock</h6>
                            <h2 class="mb-0">{{ number_format($stats['out_of_stock']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filters & Search</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" action="{{ route('admin.products.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search Products</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name, SKU, or description">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                        {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock Status</label>
                        <select name="stock_status" class="form-select">
                            <option value="">All Stock</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Price Range</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" name="min_price"
                                       value="{{ request('min_price') }}" placeholder="Min">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="max_price"
                                       value="{{ request('max_price') }}" placeholder="Max">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort By</label>
                        <select name="sort_by" class="form-select">
                            <option value="created_at_desc" {{ request('sort_by') == 'created_at_desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="created_at_asc" {{ request('sort_by') == 'created_at_asc' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Price Low-High</option>
                            <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Price High-Low</option>
                            <option value="stock_asc" {{ request('sort_by') == 'stock_asc' ? 'selected' : '' }}>Stock Low-High</option>
                            <option value="stock_desc" {{ request('sort_by') == 'stock_desc' ? 'selected' : '' }}>Stock High-Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Items per page</label>
                        <select name="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Products List
                <span class="badge bg-secondary">{{ $products->total() }} total</span>
            </h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" id="bulkActionsBtn" disabled>
                        Bulk Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                            <i class="fas fa-check text-success me-2"></i>Activate Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                            <i class="fas fa-times text-warning me-2"></i>Deactivate Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('feature')">
                            <i class="fas fa-star text-primary me-2"></i>Feature Selected
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('delete')" class="text-danger">
                            <i class="fas fa-trash text-danger me-2"></i>Delete Selected
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                </th>
                                <th width="80">Image</th>
                                <th>Product Details</th>
                                <th width="120">Category</th>
                                <th width="120">Price</th>
                                <th width="100">Stock</th>
                                <th width="100">Status</th>
                                <th width="120">Sales</th>
                                <th width="120">Rating</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="product-checkbox"
                                               value="{{ $product->id }}" onchange="updateBulkActions()">
                                    </td>
                                    <td>
                                        <div class="product-image-thumb">
                                            @if($product->primary_image)
                                                <img src="{{ $product->primary_image }}"
                                                     alt="{{ $product->name }}"
                                                     class="img-thumbnail"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                   class="text-decoration-none">
                                                    {{ $product->name }}
                                                </a>
                                                @if($product->featured)
                                                    <i class="fas fa-star text-warning ms-1" title="Featured"></i>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                SKU: {{ $product->sku }} |
                                                ID: {{ $product->id }}
                                                @if($product->brand)
                                                    | {{ $product->brand->name }}
                                                @endif
                                            </small>
                                            @if($product->short_description)
                                                <p class="mb-0 small text-muted">
                                                    {{ Str::limit($product->short_description, 60) }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-light text-dark">
                                                {{ $product->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            <strong>{{ format_currency($product->price_cents) }}</strong>
                                            @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                                <br>
                                                <small class="text-muted text-decoration-line-through">
                                                    {{ format_currency($product->compare_price_cents) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            @php
                                                $stockClass = 'text-success';
                                                if ($product->stock_quantity == 0) {
                                                    $stockClass = 'text-danger';
                                                } elseif ($product->stock_quantity <= $product->min_stock_level) {
                                                    $stockClass = 'text-warning';
                                                }
                                            @endphp
                                            <span class="{{ $stockClass }} fw-bold">
                                                {{ number_format($product->stock_quantity) }}
                                            </span>
                                            @if($product->min_stock_level)
                                                <br>
                                                <small class="text-muted">
                                                    Min: {{ $product->min_stock_level }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->status_color }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                        @if($product->track_stock && $product->stock_quantity <= $product->min_stock_level)
                                            <br>
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div>{{ number_format($product->sale_count) }}</div>
                                        <small class="text-muted">
                                            {{ format_currency($product->revenue_cents) }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($product->rating_count > 0)
                                            <div class="rating-display">
                                                <span class="fw-bold">{{ number_format($product->rating_average, 1) }}</span>
                                                <div class="star-rating small">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $product->rating_average)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @elseif($i - 0.5 <= $product->rating_average)
                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <small class="text-muted">
                                                    ({{ $product->rating_count }})
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">No ratings</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.products.show', $product) }}"
                                               class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.show', $product) }}"
                                               class="btn btn-outline-info" title="Preview" target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="deleteProduct({{ $product->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Products Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'category_id', 'brand_id', 'status', 'stock_status']))
                            No products match your current filters. Try adjusting your search criteria.
                        @else
                            You haven't added any products yet. Click "Add Product" to get started.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'category_id', 'brand_id', 'status', 'stock_status']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Your First Product
                        </a>
                    @endif
                </div>
            @endif
        </div>
        @if($products->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }}
                        of {{ $products->total() }} results
                    </div>
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select CSV/Excel File</label>
                        <input type="file" class="form-control" name="import_file"
                               accept=".csv,.xlsx,.xls" required>
                        <small class="form-text text-muted">
                            Supported formats: CSV, Excel (.xlsx, .xls). Maximum file size: 10MB.
                        </small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="updateExisting"
                                   name="update_existing" value="1">
                            <label class="form-check-label" for="updateExisting">
                                Update existing products (match by SKU)
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0 small">
                            <li>First row should contain column headers</li>
                            <li>Required columns: name, sku, price, category_id</li>
                            <li>Price should be in cents (e.g., 5000 for Rp 50.00)</li>
                            <li><a href="{{ route('admin.products.download-template') }}">Download template file</a></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Products</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.product-image-thumb img {
    border-radius: 6px;
}

.star-rating i {
    font-size: 0.8rem;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #e5e5e5;
}

.badge {
    font-size: 0.75rem;
}

.rating-display {
    white-space: nowrap;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e5e5e5;
}

.product-checkbox:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.table-responsive {
    max-height: 70vh;
}

@media (max-width: 768px) {
    .btn-group .btn {
        margin-bottom: 0.25rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-group-sm .btn {
        padding: 0.125rem 0.25rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
let selectedProducts = [];

// Toggle select all functionality
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    productCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateBulkActions();
}

// Update bulk actions button state
function updateBulkActions() {
    const productCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');

    selectedProducts = Array.from(productCheckboxes).map(cb => cb.value);

    bulkActionsBtn.disabled = selectedProducts.length === 0;
    bulkActionsBtn.textContent = selectedProducts.length > 0
        ? `Bulk Actions (${selectedProducts.length})`
        : 'Bulk Actions';

    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.product-checkbox');
    selectAllCheckbox.indeterminate = selectedProducts.length > 0 && selectedProducts.length < allCheckboxes.length;
    selectAllCheckbox.checked = selectedProducts.length === allCheckboxes.length && allCheckboxes.length > 0;
}

// Handle bulk actions
function bulkAction(action) {
    if (selectedProducts.length === 0) {
        alert('Please select at least one product.');
        return;
    }

    let message = '';
    let url = '';

    switch(action) {
        case 'activate':
            message = `Are you sure you want to activate ${selectedProducts.length} products?`;
            url = '{{ route("admin.products.bulk-activate") }}';
            break;
        case 'deactivate':
            message = `Are you sure you want to deactivate ${selectedProducts.length} products?`;
            url = '{{ route("admin.products.bulk-deactivate") }}';
            break;
        case 'feature':
            message = `Are you sure you want to feature ${selectedProducts.length} products?`;
            url = '{{ route("admin.products.bulk-feature") }}';
            break;
        case 'delete':
            message = `Are you sure you want to delete ${selectedProducts.length} products? This action cannot be undone.`;
            url = '{{ route("admin.products.bulk-delete") }}';
            break;
    }

    if (confirm(message)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add selected products
        selectedProducts.forEach(productId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = productId;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

// Delete single product
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.products.index') }}/${productId}`;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

// Export products
function exportProducts(format) {
    const url = `{{ route('admin.products.export') }}?format=${format}`;
    window.open(url, '_blank');
}

// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            form.submit();
        });
    });

    // Initialize bulk actions state
    updateBulkActions();

    // Real-time search with debounce
    const searchInput = form.querySelector('input[name="search"]');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            form.submit();
        }, 500);
    });
});

// Stock level warning colors
document.addEventListener('DOMContentLoaded', function() {
    const stockCells = document.querySelectorAll('[data-stock]');
    stockCells.forEach(cell => {
        const stock = parseInt(cell.dataset.stock);
        const minStock = parseInt(cell.dataset.minStock) || 5;

        if (stock === 0) {
            cell.classList.add('text-danger');
        } else if (stock <= minStock) {
            cell.classList.add('text-warning');
        } else {
            cell.classList.add('text-success');
        }
    });
});
</script>
@endpush
