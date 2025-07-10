@extends('layouts.admin')

@section('title', 'Reviews Management - TokoSaya Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reviews Management</h1>
            <p class="text-muted mb-0">Moderate and manage customer product reviews</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportReviews()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button class="btn btn-success" onclick="bulkApprove()">
                <i class="fas fa-check me-1"></i>Bulk Approve
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Reviews
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">23</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved Reviews
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,247</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Rating
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4.2</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Flagged Reviews
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">5</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="reviewFilters" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search Reviews</label>
                    <input type="text" class="form-control" id="searchReviews" placeholder="Customer name, product, content...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="flagged">Flagged</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rating</label>
                    <select class="form-select" id="ratingFilter">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date Range</label>
                    <select class="form-select" id="dateFilter">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom Range</option>
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

    <!-- Reviews List -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Reviews List</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-success" onclick="bulkAction('approve')" disabled id="bulkApproveBtn">
                    <i class="fas fa-check me-1"></i>Approve Selected
                </button>
                <button class="btn btn-sm btn-danger" onclick="bulkAction('reject')" disabled id="bulkRejectBtn">
                    <i class="fas fa-times me-1"></i>Reject Selected
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllReviews" onchange="toggleSelectAllReviews()"></th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Pending Review -->
                        <tr class="review-row" data-status="pending">
                            <td><input type="checkbox" class="review-checkbox" value="1"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/avatar-placeholder.jpg" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <strong>Sarah Johnson</strong>
                                        <br><small class="text-muted">sarah.j@email.com</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/placeholder-product.jpg" class="rounded me-2" width="40" height="40">
                                    <div>
                                        <strong>Samsung Galaxy S23</strong>
                                        <br><small class="text-muted">SKU: SGS23-256</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <small class="ms-1">5.0</small>
                                </div>
                            </td>
                            <td>
                                <div class="review-content">
                                    <p class="mb-1">"Amazing phone! The camera quality is outstanding and battery life is excellent. Highly recommend!"</p>
                                    <div class="review-images">
                                        <img src="/images/review-photo1.jpg" class="review-image" width="30" height="30">
                                        <img src="/images/review-photo2.jpg" class="review-image" width="30" height="30">
                                        <span class="badge bg-info">+2 photos</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>Jan 15, 2024</small>
                                <br><small class="text-muted">2 hours ago</small>
                            </td>
                            <td>
                                <span class="badge bg-warning">Pending</span>
                                <br><small class="text-muted">Helpful: 0</small>
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm">
                                    <button class="btn btn-success btn-sm" onclick="approveReview(1)">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="rejectReview(1)">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="viewReview(1)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Approved Review -->
                        <tr class="review-row" data-status="approved">
                            <td><input type="checkbox" class="review-checkbox" value="2"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/avatar-placeholder.jpg" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <strong>Mike Chen</strong>
                                        <br><small class="text-muted">mike.chen@email.com</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/placeholder-product.jpg" class="rounded me-2" width="40" height="40">
                                    <div>
                                        <strong>Nike Air Max 270</strong>
                                        <br><small class="text-muted">SKU: NAM270-42</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-muted"></i>
                                    <small class="ms-1">4.0</small>
                                </div>
                            </td>
                            <td>
                                <div class="review-content">
                                    <p class="mb-1">"Great shoes, very comfortable for running. The design is sleek and modern. Good value for money."</p>
                                </div>
                            </td>
                            <td>
                                <small>Jan 10, 2024</small>
                                <br><small class="text-muted">5 days ago</small>
                            </td>
                            <td>
                                <span class="badge bg-success">Approved</span>
                                <br><small class="text-muted">Helpful: 12</small>
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm">
                                    <button class="btn btn-warning btn-sm" onclick="unpublishReview(2)">
                                        <i class="fas fa-eye-slash"></i> Unpublish
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="viewReview(2)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteReview(2)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Flagged Review -->
                        <tr class="review-row" data-status="flagged">
                            <td><input type="checkbox" class="review-checkbox" value="3"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/avatar-placeholder.jpg" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <strong>Anonymous User</strong>
                                        <br><small class="text-muted">Flag reason: Spam</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/placeholder-product.jpg" class="rounded me-2" width="40" height="40">
                                    <div>
                                        <strong>MacBook Pro 14"</strong>
                                        <br><small class="text-muted">SKU: MBP14-M3</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-muted"></i>
                                    <i class="far fa-star text-muted"></i>
                                    <i class="far fa-star text-muted"></i>
                                    <i class="far fa-star text-muted"></i>
                                    <small class="ms-1">1.0</small>
                                </div>
                            </td>
                            <td>
                                <div class="review-content">
                                    <p class="mb-1 text-danger">"This is a fake review with suspicious content. Click here to win prizes!!!"</p>
                                    <small class="text-danger"><i class="fas fa-flag"></i> Flagged as spam</small>
                                </div>
                            </td>
                            <td>
                                <small>Jan 8, 2024</small>
                                <br><small class="text-muted">1 week ago</small>
                            </td>
                            <td>
                                <span class="badge bg-danger">Flagged</span>
                                <br><small class="text-muted">Reports: 3</small>
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm">
                                    <button class="btn btn-success btn-sm" onclick="approveReview(3)">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="rejectReview(3)">
                                        <i class="fas fa-ban"></i> Reject
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="viewReview(3)">
                                        <i class="fas fa-eye"></i> Investigate
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Reviews pagination" class="mt-3">
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

<!-- Review Detail Modal -->
<div class="modal fade" id="reviewDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/images/avatar-placeholder.jpg" class="rounded-circle me-3" width="60" height="60">
                                    <div>
                                        <strong id="modalCustomerName">Customer Name</strong>
                                        <br><small id="modalCustomerEmail">customer@email.com</small>
                                        <br><small class="text-muted">Member since: <span id="modalMemberSince">Jan 2023</span></small>
                                    </div>
                                </div>
                                <div class="customer-stats">
                                    <small><strong>Total Orders:</strong> <span id="modalCustomerOrders">5</span></small><br>
                                    <small><strong>Total Reviews:</strong> <span id="modalCustomerReviews">12</span></small><br>
                                    <small><strong>Average Rating:</strong> <span id="modalCustomerAvgRating">4.2</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Product Information</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/images/placeholder-product.jpg" class="rounded me-3" width="60" height="60">
                                    <div>
                                        <strong id="modalProductName">Product Name</strong>
                                        <br><small id="modalProductSKU">SKU: ABC123</small>
                                        <br><small class="text-muted">Category: <span id="modalProductCategory">Electronics</span></small>
                                    </div>
                                </div>
                                <div class="product-stats">
                                    <small><strong>Average Rating:</strong> <span id="modalProductRating">4.5</span></small><br>
                                    <small><strong>Total Reviews:</strong> <span id="modalProductReviews">89</span></small><br>
                                    <small><strong>Price:</strong> <span id="modalProductPrice">Rp 1,999,000</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>Review Details</h6>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating-display">
                                    <span id="modalRatingStars"></span>
                                    <strong class="ms-2" id="modalRatingValue">5.0</strong>
                                </div>
                                <small class="text-muted" id="modalReviewDate">Jan 15, 2024</small>
                            </div>
                            <h6 id="modalReviewTitle">Review Title</h6>
                            <p id="modalReviewContent">Review content will appear here...</p>

                            <div id="modalReviewImages" class="review-images-gallery mt-3" style="display: none;">
                                <h6>Attached Images</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <!-- Images will be loaded here -->
                                </div>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-thumbs-up me-1"></i>Helpful votes: <span id="modalHelpfulCount">0</span>
                                    <span class="ms-3"><i class="fas fa-flag me-1"></i>Reports: <span id="modalReportCount">0</span></span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="modalApproveBtn" onclick="approveFromModal()">
                    <i class="fas fa-check me-1"></i>Approve
                </button>
                <button type="button" class="btn btn-danger" id="modalRejectBtn" onclick="rejectFromModal()">
                    <i class="fas fa-times me-1"></i>Reject
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-xs {
    font-size: .7rem;
}

.review-content {
    max-width: 300px;
}

.review-content p {
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.review-image {
    border-radius: 4px;
    cursor: pointer;
    transition: transform 0.2s;
}

.review-image:hover {
    transform: scale(1.1);
}

.rating-stars {
    font-size: 0.9rem;
}

.rating-stars i {
    margin-right: 2px;
}

.review-row {
    transition: all 0.3s ease;
}

.review-row:hover {
    background-color: #f8f9fc;
}

.review-row[data-status="flagged"] {
    background-color: #fdf2f2;
}

.review-row[data-status="pending"] {
    background-color: #fefcf3;
}

.btn-group-vertical .btn {
    margin-bottom: 2px;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fc;
    font-size: 0.85rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.85rem;
}

.badge {
    font-size: 0.7rem;
}

.customer-stats small,
.product-stats small {
    display: block;
    margin-bottom: 0.25rem;
}

.review-images-gallery img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.review-images-gallery img:hover {
    transform: scale(1.05);
}

.rating-display {
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .review-content {
        max-width: 200px;
    }

    .btn-group-vertical .btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    .table th,
    .table td {
        font-size: 0.8rem;
    }

    .rating-stars {
        font-size: 0.8rem;
    }
}
</style>

<script>
let selectedReviews = [];
let currentReviewId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateBulkActionButtons();
});

// Select all reviews
function toggleSelectAllReviews() {
    const selectAll = document.getElementById('selectAllReviews');
    const checkboxes = document.querySelectorAll('.review-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateSelectedReviews();
}

// Update selected reviews
function updateSelectedReviews() {
    const checkboxes = document.querySelectorAll('.review-checkbox:checked');
    selectedReviews = Array.from(checkboxes).map(cb => cb.value);
    updateBulkActionButtons();

    // Update select all state
    const selectAll = document.getElementById('selectAllReviews');
    const allCheckboxes = document.querySelectorAll('.review-checkbox');

    if (selectedReviews.length === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (selectedReviews.length === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
    }
}

// Update bulk action buttons
function updateBulkActionButtons() {
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');

    if (selectedReviews.length > 0) {
        bulkApproveBtn.disabled = false;
        bulkRejectBtn.disabled = false;
        bulkApproveBtn.innerHTML = `<i class="fas fa-check me-1"></i>Approve Selected (${selectedReviews.length})`;
        bulkRejectBtn.innerHTML = `<i class="fas fa-times me-1"></i>Reject Selected (${selectedReviews.length})`;
    } else {
        bulkApproveBtn.disabled = true;
        bulkRejectBtn.disabled = true;
        bulkApproveBtn.innerHTML = '<i class="fas fa-check me-1"></i>Approve Selected';
        bulkRejectBtn.innerHTML = '<i class="fas fa-times me-1"></i>Reject Selected';
    }
}

// Add event listeners to checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('review-checkbox')) {
        updateSelectedReviews();
    }
});

// Filter functions
function applyFilters() {
    const search = document.getElementById('searchReviews').value;
    const status = document.getElementById('statusFilter').value;
    const rating = document.getElementById('ratingFilter').value;
    const dateRange = document.getElementById('dateFilter').value;

    console.log('Applying filters:', { search, status, rating, dateRange });
    showToast('Filters applied successfully', 'success');

    // Here you would normally make an AJAX call to filter reviews
    filterReviewsInTable(status, rating);
}

function resetFilters() {
    document.getElementById('reviewFilters').reset();
    showToast('Filters reset', 'info');

    // Show all reviews
    const allRows = document.querySelectorAll('.review-row');
    allRows.forEach(row => row.style.display = '');
}

// Filter reviews in table (client-side demo)
function filterReviewsInTable(status, rating) {
    const rows = document.querySelectorAll('.review-row');

    rows.forEach(row => {
        let showRow = true;

        // Filter by status
        if (status && row.dataset.status !== status) {
            showRow = false;
        }

        // Filter by rating
        if (rating) {
            const ratingText = row.querySelector('.rating-stars small').textContent;
            const reviewRating = parseInt(ratingText);
            if (reviewRating !== parseInt(rating)) {
                showRow = false;
            }
        }

        row.style.display = showRow ? '' : 'none';
    });
}

// Review actions
function approveReview(reviewId) {
    if (confirm('Approve this review?')) {
        console.log('Approving review:', reviewId);
        showToast('Review approved successfully', 'success');
        updateReviewStatus(reviewId, 'approved');
    }
}

function rejectReview(reviewId) {
    const reason = prompt('Reason for rejection (optional):');
    if (reason !== null) {
        console.log('Rejecting review:', reviewId, 'Reason:', reason);
        showToast('Review rejected successfully', 'success');
        updateReviewStatus(reviewId, 'rejected');
    }
}

function unpublishReview(reviewId) {
    if (confirm('Unpublish this review? It will no longer be visible to customers.')) {
        console.log('Unpublishing review:', reviewId);
        showToast('Review unpublished successfully', 'success');
        updateReviewStatus(reviewId, 'unpublished');
    }
}

function deleteReview(reviewId) {
    if (confirm('Delete this review permanently? This action cannot be undone.')) {
        console.log('Deleting review:', reviewId);
        showToast('Review deleted successfully', 'success');

        // Remove row from table
        const row = document.querySelector(`tr .review-checkbox[value="${reviewId}"]`).closest('tr');
        row.remove();
    }
}

function viewReview(reviewId) {
    currentReviewId = reviewId;

    // Sample data - replace with actual API call
    const reviewData = {
        1: {
            customer: {
                name: 'Sarah Johnson',
                email: 'sarah.j@email.com',
                memberSince: 'Jan 2023',
                totalOrders: 5,
                totalReviews: 12,
                avgRating: 4.2
            },
            product: {
                name: 'Samsung Galaxy S23',
                sku: 'SGS23-256-BLK',
                category: 'Electronics',
                rating: 4.5,
                reviews: 89,
                price: 'Rp 12,999,000'
            },
            review: {
                rating: 5,
                title: 'Amazing Product!',
                content: 'Amazing phone! The camera quality is outstanding and battery life is excellent. Highly recommend!',
                date: 'Jan 15, 2024',
                helpfulCount: 0,
                reportCount: 0,
                images: ['/images/review-photo1.jpg', '/images/review-photo2.jpg']
            }
        },
        2: {
            customer: {
                name: 'Mike Chen',
                email: 'mike.chen@email.com',
                memberSince: 'Mar 2022',
                totalOrders: 8,
                totalReviews: 15,
                avgRating: 4.1
            },
            product: {
                name: 'Nike Air Max 270',
                sku: 'NAM270-42-WHT',
                category: 'Fashion',
                rating: 4.2,
                reviews: 156,
                price: 'Rp 1,899,000'
            },
            review: {
                rating: 4,
                title: 'Good Quality Shoes',
                content: 'Great shoes, very comfortable for running. The design is sleek and modern. Good value for money.',
                date: 'Jan 10, 2024',
                helpfulCount: 12,
                reportCount: 0,
                images: []
            }
        },
        3: {
            customer: {
                name: 'Anonymous User',
                email: 'suspicious@email.com',
                memberSince: 'Jan 2024',
                totalOrders: 0,
                totalReviews: 1,
                avgRating: 1.0
            },
            product: {
                name: 'MacBook Pro 14" M3',
                sku: 'MBP14-M3-512-SLV',
                category: 'Electronics',
                rating: 4.8,
                reviews: 23,
                price: 'Rp 32,999,000'
            },
            review: {
                rating: 1,
                title: 'Suspicious Review',
                content: 'This is a fake review with suspicious content. Click here to win prizes!!!',
                date: 'Jan 8, 2024',
                helpfulCount: 0,
                reportCount: 3,
                images: []
            }
        }
    };

    const data = reviewData[reviewId];
    if (data) {
        populateReviewModal(data);
        const modal = new bootstrap.Modal(document.getElementById('reviewDetailModal'));
        modal.show();
    }
}

function populateReviewModal(data) {
    // Customer info
    document.getElementById('modalCustomerName').textContent = data.customer.name;
    document.getElementById('modalCustomerEmail').textContent = data.customer.email;
    document.getElementById('modalMemberSince').textContent = data.customer.memberSince;
    document.getElementById('modalCustomerOrders').textContent = data.customer.totalOrders;
    document.getElementById('modalCustomerReviews').textContent = data.customer.totalReviews;
    document.getElementById('modalCustomerAvgRating').textContent = data.customer.avgRating;

    // Product info
    document.getElementById('modalProductName').textContent = data.product.name;
    document.getElementById('modalProductSKU').textContent = 'SKU: ' + data.product.sku;
    document.getElementById('modalProductCategory').textContent = data.product.category;
    document.getElementById('modalProductRating').textContent = data.product.rating;
    document.getElementById('modalProductReviews').textContent = data.product.reviews;
    document.getElementById('modalProductPrice').textContent = data.product.price;

    // Review details
    document.getElementById('modalRatingStars').innerHTML = generateStarRating(data.review.rating);
    document.getElementById('modalRatingValue').textContent = data.review.rating + '.0';
    document.getElementById('modalReviewTitle').textContent = data.review.title;
    document.getElementById('modalReviewContent').textContent = data.review.content;
    document.getElementById('modalReviewDate').textContent = data.review.date;
    document.getElementById('modalHelpfulCount').textContent = data.review.helpfulCount;
    document.getElementById('modalReportCount').textContent = data.review.reportCount;

    // Review images
    const imagesContainer = document.getElementById('modalReviewImages');
    if (data.review.images && data.review.images.length > 0) {
        imagesContainer.style.display = 'block';
        const imageGallery = imagesContainer.querySelector('.d-flex');
        imageGallery.innerHTML = data.review.images.map(img =>
            `<img src="${img}" class="review-image-large" onclick="openImageModal('${img}')">`
        ).join('');
    } else {
        imagesContainer.style.display = 'none';
    }
}

function generateStarRating(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-warning"></i>';
        } else {
            stars += '<i class="far fa-star text-muted"></i>';
        }
    }
    return stars;
}

function approveFromModal() {
    if (currentReviewId) {
        approveReview(currentReviewId);
        bootstrap.Modal.getInstance(document.getElementById('reviewDetailModal')).hide();
    }
}

function rejectFromModal() {
    if (currentReviewId) {
        rejectReview(currentReviewId);
        bootstrap.Modal.getInstance(document.getElementById('reviewDetailModal')).hide();
    }
}

function updateReviewStatus(reviewId, newStatus) {
    const row = document.querySelector(`tr .review-checkbox[value="${reviewId}"]`).closest('tr');
    const statusBadge = row.querySelector('.badge');

    // Update status badge
    statusBadge.className = 'badge';
    switch (newStatus) {
        case 'approved':
            statusBadge.classList.add('bg-success');
            statusBadge.textContent = 'Approved';
            break;
        case 'rejected':
            statusBadge.classList.add('bg-danger');
            statusBadge.textContent = 'Rejected';
            break;
        case 'unpublished':
            statusBadge.classList.add('bg-secondary');
            statusBadge.textContent = 'Unpublished';
            break;
    }

    // Update row data attribute
    row.dataset.status = newStatus;

    // Update action buttons
    const actionCell = row.querySelector('td:last-child');
    updateActionButtons(actionCell, newStatus);
}

function updateActionButtons(cell, status) {
    let buttonsHTML = '';

    switch (status) {
        case 'approved':
            buttonsHTML = `
                <div class="btn-group-vertical btn-group-sm">
                    <button class="btn btn-warning btn-sm" onclick="unpublishReview(${currentReviewId})">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </button>
                    <button class="btn btn-info btn-sm" onclick="viewReview(${currentReviewId})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteReview(${currentReviewId})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `;
            break;
        case 'rejected':
            buttonsHTML = `
                <div class="btn-group-vertical btn-group-sm">
                    <button class="btn btn-success btn-sm" onclick="approveReview(${currentReviewId})">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-info btn-sm" onclick="viewReview(${currentReviewId})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteReview(${currentReviewId})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `;
            break;
        default:
            buttonsHTML = `
                <div class="btn-group-vertical btn-group-sm">
                    <button class="btn btn-success btn-sm" onclick="approveReview(${currentReviewId})">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="rejectReview(${currentReviewId})">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <button class="btn btn-info btn-sm" onclick="viewReview(${currentReviewId})">
                        <i class="fas fa-eye"></i> View
                    </button>
                </div>
            `;
    }

    cell.innerHTML = buttonsHTML;
}

// Bulk actions
function bulkAction(action) {
    if (selectedReviews.length === 0) {
        showToast('Please select reviews first', 'warning');
        return;
    }

    let message = '';
    switch (action) {
        case 'approve':
            message = `Approve ${selectedReviews.length} selected reviews?`;
            break;
        case 'reject':
            message = `Reject ${selectedReviews.length} selected reviews?`;
            break;
    }

    if (confirm(message)) {
        console.log(`Bulk ${action} for reviews:`, selectedReviews);
        showToast(`Successfully ${action}ed ${selectedReviews.length} reviews`, 'success');

        // Update UI for selected reviews
        selectedReviews.forEach(reviewId => {
            updateReviewStatus(reviewId, action === 'approve' ? 'approved' : 'rejected');
        });

        // Clear selection
        selectedReviews = [];
        document.getElementById('selectAllReviews').checked = false;
        document.querySelectorAll('.review-checkbox').forEach(cb => cb.checked = false);
        updateBulkActionButtons();
    }
}

function bulkApprove() {
    bulkAction('approve');
}

// Export function
function exportReviews() {
    const format = prompt('Export format (excel/csv):', 'excel');
    if (format) {
        console.log('Exporting reviews as:', format);
        showToast('Export started, download will begin shortly', 'info');

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
document.getElementById('searchReviews').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Image modal function
function openImageModal(imageSrc) {
    // Create image modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${imageSrc}" class="img-fluid" style="max-height: 500px;">
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

// Real-time updates simulation
setInterval(() => {
    // Simulate new pending reviews
    const pendingCount = document.querySelector('.border-left-warning .h5');
    const currentCount = parseInt(pendingCount.textContent);

    if (Math.random() > 0.9) { // 10% chance
        pendingCount.textContent = currentCount + 1;
        showToast('New review received and awaiting moderation', 'info');
    }
}, 30000); // Check every 30 seconds
</script>

@endsection
