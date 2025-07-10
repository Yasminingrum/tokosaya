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
                    <tbody id="productsTableBody">
                        <!-- Sample Data - Replace with dynamic data -->
                        <tr>
                            <td><input type="checkbox" class="product-checkbox" value="1"></td>
                            <td>
                                <img src="/images/placeholder-product.jpg" class="product-thumbnail"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>
                                <div>
                                    <strong>Samsung Galaxy S23</strong>
                                    <br><small class="text-muted">SKU: SGS23-256-BLK</small>
                                    <br><small class="text-muted">Created: Jan 15, 2024</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">Electronics</span>
                            </td>
                            <td>
                                <strong>Rp 12,999,000</strong>
                                <br><small class="text-muted">Cost: Rp 10,500,000</small>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-boxes me-1"></i>45
                                </span>
                                <br><small class="text-muted">Min: 10</small>
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                                <br><small class="text-muted">Sales: 127</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="/admin/products/1/edit">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a></li>
                                        <li><a class="dropdown-item" href="/products/1" target="_blank">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="duplicateProduct(1)">
                                            <i class="fas fa-copy me-2"></i>Duplicate
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStock(1)">
                                            <i class="fas fa-boxes me-2"></i>Update Stock
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="toggleStatus(1)">
                                            <i class="fas fa-toggle-on me-2"></i>Toggle Status
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct(1)">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td><input type="checkbox" class="product-checkbox" value="2"></td>
                            <td>
                                <img src="/images/placeholder-product.jpg" class="product-thumbnail"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>
                                <div>
                                    <strong>Nike Air Max 270</strong>
                                    <br><small class="text-muted">SKU: NAM270-42-WHT</small>
                                    <br><small class="text-muted">Created: Jan 10, 2024</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning">Fashion</span>
                            </td>
                            <td>
                                <strong>Rp 1,899,000</strong>
                                <br><small class="text-muted">Cost: Rp 1,200,000</small>
                            </td>
                            <td>
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>8
                                </span>
                                <br><small class="text-muted">Min: 10</small>
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                                <br><small class="text-muted">Sales: 89</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="/admin/products/2/edit">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a></li>
                                        <li><a class="dropdown-item" href="/products/2" target="_blank">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="duplicateProduct(2)">
                                            <i class="fas fa-copy me-2"></i>Duplicate
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStock(2)">
                                            <i class="fas fa-boxes me-2"></i>Update Stock
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="toggleStatus(2)">
                                            <i class="fas fa-toggle-on me-2"></i>Toggle Status
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct(2)">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td><input type="checkbox" class="product-checkbox" value="3"></td>
                            <td>
                                <img src="/images/placeholder-product.jpg" class="product-thumbnail"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>
                                <div>
                                    <strong>MacBook Pro 14" M3</strong>
                                    <br><small class="text-muted">SKU: MBP14-M3-512-SLV</small>
                                    <br><small class="text-muted">Created: Dec 28, 2023</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">Electronics</span>
                            </td>
                            <td>
                                <strong>Rp 32,999,000</strong>
                                <br><small class="text-muted">Cost: Rp 28,500,000</small>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>0
                                </span>
                                <br><small class="text-muted">Min: 5</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">Out of Stock</span>
                                <br><small class="text-muted">Sales: 23</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="/admin/products/3/edit">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a></li>
                                        <li><a class="dropdown-item" href="/products/3" target="_blank">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="duplicateProduct(3)">
                                            <i class="fas fa-copy me-2"></i>Duplicate
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStock(3)">
                                            <i class="fas fa-boxes me-2"></i>Update Stock
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="toggleStatus(3)">
                                            <i class="fas fa-toggle-on me-2"></i>Toggle Status
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct(3)">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
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
