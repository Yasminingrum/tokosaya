@extends('layouts.admin')

@section('title', 'Manage Products - TokoSaya Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Products Management</h1>
            <p class="text-muted mb-0">Manage your product inventory and stock levels</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportProducts()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Add Product
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search Products</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Product name, SKU...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="1">Electronics</option>
                        <option value="2">Fashion</option>
                        <option value="3">Home & Garden</option>
                        <option value="4">Sports</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stock Status</label>
                    <select class="form-select" id="stockFilter">
                        <option value="">All Stock</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Actions</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-times me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card shadow mb-4" id="bulkActionsCard" style="display: none;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><span id="selectedCount">0</span> products selected</strong>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm" onclick="bulkAction('activate')">
                        <i class="fas fa-check me-1"></i>Activate
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="bulkAction('deactivate')">
                        <i class="fas fa-pause me-1"></i>Deactivate
                    </button>
                    <button class="btn btn-info btn-sm" onclick="bulkAction('update_stock')">
                        <i class="fas fa-boxes me-1"></i>Update Stock
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Image</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
@extends('layouts.admin')

@section('title', 'Manage Products - TokoSaya Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Products Management</h1>
            <p class="text-muted mb-0">Manage your product inventory and stock levels</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportProducts()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Add Product
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Search Products</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="searchInput"
                               value="{{ request('search') }}" placeholder="Product name, SKU...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" id="categoryFilter">
                        <option value="">All Categories</option>
                        @php
                            // Get categories from database
                            $categories = \App\Models\Category::where('status', 'active')->get();
                        @endphp
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stock Status</label>
                    <select class="form-select" name="stock_status" id="stockFilter">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Actions</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card shadow mb-4" id="bulkActionsCard" style="display: none;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><span id="selectedCount">0</span> products selected</strong>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm" onclick="bulkAction('activate')">
                        <i class="fas fa-check me-1"></i>Activate
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="bulkAction('deactivate')">
                        <i class="fas fa-pause me-1"></i>Deactivate
                    </button>
                    <button class="btn btn-info btn-sm" onclick="bulkAction('update_stock')">
                        <i class="fas fa-boxes me-1"></i>Update Stock
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Image</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @php
                            // Get products from database using the ProductController logic
                            $query = \App\Models\Product::with(['category', 'brand', 'primaryImage'])
                                ->select([
                                    'id', 'name', 'slug', 'sku', 'price_cents', 'compare_price_cents',
                                    'stock_quantity', 'min_stock_level', 'category_id', 'brand_id',
                                    'status', 'created_at', 'sale_count'
                                ]);

                            // Apply filters from request
                            if (request('search')) {
                                $search = request('search');
                                $query->where(function($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('sku', 'like', "%{$search}%");
                                });
                            }

                            if (request('category_id')) {
                                $query->where('category_id', request('category_id'));
                            }

                            if (request('status')) {
                                $query->where('status', request('status'));
                            }

                            if (request('stock_status')) {
                                switch (request('stock_status')) {
                                    case 'in_stock':
                                        $query->where('stock_quantity', '>', 'min_stock_level');
                                        break;
                                    case 'low_stock':
                                        $query->whereColumn('stock_quantity', '<=', 'min_stock_level')
                                              ->where('stock_quantity', '>', 0);
                                        break;
                                    case 'out_of_stock':
                                        $query->where('stock_quantity', 0);
                                        break;
                                }
                            }

                            $products = $query->orderBy('created_at', 'desc')->paginate(20);
                        @endphp

                        @forelse($products as $product)
                            <tr>
                                <td><input type="checkbox" class="product-checkbox" value="{{ $product->id }}"></td>
                                <td>
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->url ?? '/images/placeholder-product.jpg' }}"
                                             class="product-thumbnail"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                                             alt="{{ $product->name }}">
                                    @else
                                        <img src="/images/placeholder-product.jpg"
                                             class="product-thumbnail"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                                             alt="No Image">
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <br><small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                                        <br><small class="text-muted">Created: {{ $product->created_at->format('M d, Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-info">{{ $product->category->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Category</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product->formatted_price ?? 'Rp ' . number_format(($product->price_cents ?? 0) / 100, 0, ',', '.') }}</strong>
                                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                        <br><small class="text-muted text-decoration-line-through">
                                            {{ 'Rp ' . number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $stockClass = 'bg-success';
                                        $stockIcon = 'fas fa-boxes';

                                        if ($product->stock_quantity == 0) {
                                            $stockClass = 'bg-danger';
                                            $stockIcon = 'fas fa-times';
                                        } elseif ($product->stock_quantity <= $product->min_stock_level) {
                                            $stockClass = 'bg-warning';
                                            $stockIcon = 'fas fa-exclamation-triangle';
                                        }
                                    @endphp

                                    <span class="badge {{ $stockClass }}">
                                        <i class="{{ $stockIcon }} me-1"></i>{{ $product->stock_quantity }}
                                    </span>
                                    <br><small class="text-muted">Min: {{ $product->min_stock_level ?? 0 }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = $product->status == 'active' ? 'bg-success' :
                                                      ($product->status == 'inactive' ? 'bg-warning' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($product->status) }}</span>
                                    <br><small class="text-muted">Sales: {{ $product->sale_count ?? 0 }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('products.show', $product->slug) }}" target="_blank">
                                                <i class="fas fa-eye me-2"></i>View
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="duplicateProduct({{ $product->id }})">
                                                <i class="fas fa-copy me-2"></i>Duplicate
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStock({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }})">
                                                <i class="fas fa-boxes me-2"></i>Update Stock
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="toggleStatus({{ $product->id }})">
                                                <i class="fas fa-toggle-on me-2"></i>Toggle Status
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct({{ $product->id }})">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-box text-muted fa-2x mb-2"></i>
                                    <br>No products found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <nav aria-label="Products pagination" class="mt-3">
                    {{ $products->appends(request()->query())->links() }}
                </nav>
            @endif
        </div>
    </div>
</div>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Products pagination" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal fade" id="stockUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="stockUpdateForm">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="currentStock" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Adjustment</label>
                        <select class="form-select" id="adjustmentType">
                            <option value="add">Add Stock</option>
                            <option value="subtract">Subtract Stock</option>
                            <option value="set">Set Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="adjustmentQuantity" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="adjustmentNotes" rows="2" placeholder="Reason for stock adjustment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStockUpdate()">Update Stock</button>
            </div>
        </div>
    </div>
</div>

<style>
.product-thumbnail {
    border: 2px solid #e3e6f0;
    transition: all 0.3s ease;
}

.product-thumbnail:hover {
    border-color: #4e73df;
    transform: scale(1.1);
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fc;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.dropdown-toggle::after {
    margin-left: 0.5rem;
}

.table-responsive {
    border-radius: 0.375rem;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.form-select:focus,
.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
}

#bulkActionsCard {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-checkbox:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}
</style>

<script>
// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});

// Select all functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    const count = checkboxes.length;

    document.getElementById('selectedCount').textContent = count;

    const bulkActionsCard = document.getElementById('bulkActionsCard');
    if (count > 0) {
        bulkActionsCard.style.display = 'block';
    } else {
        bulkActionsCard.style.display = 'none';
    }

    // Update select all checkbox state
    const selectAll = document.getElementById('selectAll');
    const allCheckboxes = document.querySelectorAll('.product-checkbox');

    if (count === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (count === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
    }
}

// Add event listeners to product checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-checkbox')) {
        updateSelectedCount();
    }
});

// Filter functions
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const stock = document.getElementById('stockFilter').value;

    // Simulate filtering - replace with actual AJAX call
    console.log('Applying filters:', { search, category, status, stock });
    showToast('Filters applied successfully', 'success');
}

function resetFilters() {
    document.getElementById('filterForm').reset();
    applyFilters();
    showToast('Filters reset', 'info');
}

// Product actions
function updateStock(productId) {
    // Simulate getting product data
    const productData = {
        1: { name: 'Samsung Galaxy S23', stock: 45 },
        2: { name: 'Nike Air Max 270', stock: 8 },
        3: { name: 'MacBook Pro 14" M3', stock: 0 }
    };

    const product = productData[productId];
    if (product) {
        document.getElementById('productName').value = product.name;
        document.getElementById('currentStock').value = product.stock;
        document.getElementById('adjustmentQuantity').value = '';
        document.getElementById('adjustmentNotes').value = '';

        const modal = new bootstrap.Modal(document.getElementById('stockUpdateModal'));
        modal.show();
    }
}

function saveStockUpdate() {
    const form = document.getElementById('stockUpdateForm');
    const formData = new FormData(form);

    // Simulate API call
    console.log('Updating stock...', Object.fromEntries(formData));

    bootstrap.Modal.getInstance(document.getElementById('stockUpdateModal')).hide();
    showToast('Stock updated successfully', 'success');

    // Refresh table data
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function toggleStatus(productId) {
    if (confirm('Are you sure you want to toggle the status of this product?')) {
        // Simulate API call
        console.log('Toggling status for product:', productId);
        showToast('Product status updated', 'success');

        // Update UI
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

function duplicateProduct(productId) {
    if (confirm('This will create a copy of this product. Continue?')) {
        console.log('Duplicating product:', productId);
        showToast('Product duplicated successfully', 'success');

        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        console.log('Deleting product:', productId);
        showToast('Product deleted successfully', 'success');

        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

// Bulk actions
function bulkAction(action) {
    const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);

    if (selectedProducts.length === 0) {
        showToast('Please select products first', 'warning');
        return;
    }

    let message = '';
    switch (action) {
        case 'activate':
            message = `Activate ${selectedProducts.length} selected products?`;
            break;
        case 'deactivate':
            message = `Deactivate ${selectedProducts.length} selected products?`;
            break;
        case 'update_stock':
            message = `Update stock for ${selectedProducts.length} selected products?`;
            break;
        case 'delete':
            message = `Delete ${selectedProducts.length} selected products? This cannot be undone.`;
            break;
    }

    if (confirm(message)) {
        console.log(`Bulk ${action} for products:`, selectedProducts);
        showToast(`Bulk ${action} completed successfully`, 'success');

        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

// Export function
function exportProducts() {
    const format = prompt('Export format (excel/csv):', 'excel');
    if (format) {
        console.log('Exporting products as:', format);
        showToast('Export started, download will begin shortly', 'info');

        // Simulate download
        setTimeout(() => {
            showToast('Export completed successfully', 'success');
        }, 2000);
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${getToastIcon(type)} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });

    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function getToastIcon(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        case 'info': return 'info-circle';
        default: return 'bell';
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Auto-save filters in localStorage
function saveFilters() {
    const filters = {
        search: document.getElementById('searchInput').value,
        category: document.getElementById('categoryFilter').value,
        status: document.getElementById('statusFilter').value,
        stock: document.getElementById('stockFilter').value
    };
    localStorage.setItem('productFilters', JSON.stringify(filters));
}

function loadFilters() {
    const saved = localStorage.getItem('productFilters');
    if (saved) {
        const filters = JSON.parse(saved);
        document.getElementById('searchInput').value = filters.search || '';
        document.getElementById('categoryFilter').value = filters.category || '';
        document.getElementById('statusFilter').value = filters.status || '';
        document.getElementById('stockFilter').value = filters.stock || '';
    }
}

// Load saved filters on page load
document.addEventListener('DOMContentLoaded', loadFilters);

// Save filters when they change
document.getElementById('filterForm').addEventListener('change', saveFilters);
</script>

@endsection
