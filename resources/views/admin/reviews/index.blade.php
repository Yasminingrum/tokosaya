@extends('layouts.admin')

@section('title', 'Review Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-star me-2"></i>Review Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReviews('excel')">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReviews('csv')">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkActionsModal">
                <i class="fas fa-tasks me-1"></i> Bulk Actions
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalReviews">{{ number_format($statistics['total_reviews']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingReviews">{{ number_format($statistics['pending_reviews']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedReviews">{{ number_format($statistics['approved_reviews']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Rating</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="averageRating">{{ number_format($statistics['average_rating'], 1) }}/5</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star-half-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rating Distribution Chart -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rating Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="ratingChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Review Insights</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-success mb-0">{{ $statistics['verified_reviews'] }}</h4>
                            <small class="text-muted">Verified Reviews</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info mb-0">{{ $statistics['reviews_with_images'] }}</h4>
                        <small class="text-muted">With Images</small>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-warning mb-0">{{ $statistics['flagged_reviews'] }}</h4>
                            <small class="text-muted">Flagged Reviews</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-primary mb-0">{{ $statistics['helpful_reviews'] }}</h4>
                        <small class="text-muted">Marked Helpful</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h5 class="text-dark mb-1">{{ number_format($statistics['reviews_this_month']) }}</h5>
                    <small class="text-muted">Reviews This Month</small>
                    @if($statistics['reviews_growth'] > 0)
                        <div class="text-success small">
                            <i class="fas fa-arrow-up"></i> +{{ number_format($statistics['reviews_growth'], 1) }}% vs last month
                        </div>
                    @elseif($statistics['reviews_growth'] < 0)
                        <div class="text-danger small">
                            <i class="fas fa-arrow-down"></i> {{ number_format($statistics['reviews_growth'], 1) }}% vs last month
                        </div>
                    @else
                        <div class="text-muted small">No change vs last month</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form id="filtersForm" class="row g-3">
            <div class="col-md-3">
                <label for="searchInput" class="form-label">Search Reviews</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Product, user, content..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label for="statusFilter" class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Flagged</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="ratingFilter" class="form-label">Rating</label>
                <select class="form-select" id="ratingFilter">
                    <option value="">All Ratings</option>
                    <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4 Stars</option>
                    <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3 Stars</option>
                    <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2 Stars</option>
                    <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1 Star</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="verificationFilter" class="form-label">Verification</label>
                <select class="form-select" id="verificationFilter">
                    <option value="">All</option>
                    <option value="verified" {{ request('verification') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('verification') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="dateFilter" class="form-label">Date Range</label>
                <select class="form-select" id="dateFilter">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('date') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-secondary d-block" onclick="clearFilters()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">
                    Reviews List
                    <span class="badge bg-secondary ms-2" id="reviewCount">{{ $reviews->total() }} reviews</span>
                </h6>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <input type="checkbox" class="btn-check" id="selectAll" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="selectAll">Select All</label>

                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        Bulk Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('approve')">
                            <i class="fas fa-check text-success me-2"></i>Approve Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('reject')">
                            <i class="fas fa-times text-danger me-2"></i>Reject Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('flag')">
                            <i class="fas fa-flag text-warning me-2"></i>Flag Selected
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('export')">
                            <i class="fas fa-download text-primary me-2"></i>Export Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('delete')">
                            <i class="fas fa-trash text-danger me-2"></i>Delete Selected
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reviewsTable">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAllTable" class="form-check-input">
                        </th>
                        <th>Product & User</th>
                        <th>Rating & Review</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Helpful</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr data-review-id="{{ $review->id }}" class="{{ !$review->is_approved ? 'table-warning' : '' }}">
                        <td>
                            <input type="checkbox" class="form-check-input review-checkbox" value="{{ $review->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
                                @if($review->product->primary_image)
                                    <img src="{{ asset('storage/' . $review->product->primary_image) }}"
                                         alt="{{ $review->product->name }}"
                                         class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold mb-1">
                                        <a href="{{ route('admin.products.show', $review->product) }}" class="text-decoration-none">
                                            {{ Str::limit($review->product->name, 40) }}
                                        </a>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="fas fa-user me-1"></i>
                                        <a href="{{ route('admin.users.show', $review->user) }}" class="text-decoration-none">
                                            {{ $review->user->first_name }} {{ $review->user->last_name }}
                                        </a>
                                    </div>
                                    @if($review->is_verified)
                                        <span class="badge bg-success">Verified Purchase</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="mb-2">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="stars me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 0.8rem;"></i>
                                        @endfor
                                    </div>
                                    <span class="badge bg-secondary">{{ $review->rating }}/5</span>
                                </div>
                                @if($review->title)
                                    <div class="fw-bold small mb-1">{{ Str::limit($review->title, 50) }}</div>
                                @endif
                                @if($review->review)
                                    <div class="text-muted small">{{ Str::limit($review->review, 100) }}</div>
                                @endif
                                @if($review->images)
                                    <div class="mt-1">
                                        <span class="badge bg-info">{{ count($review->images) }} images</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <strong>{{ $review->created_at->format('d M Y') }}</strong><br>
                                <span class="text-muted">{{ $review->created_at->format('H:i') }}</span><br>
                                <span class="text-muted">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @if($review->is_approved)
                                    <span class="badge bg-success mb-1">Approved</span>
                                @else
                                    <span class="badge bg-warning mb-1">Pending</span>
                                @endif

                                @if($review->is_flagged)
                                    <span class="badge bg-danger">Flagged</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-primary">{{ $review->helpful_count }}</div>
                                <small class="text-muted">helpful votes</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" title="More Actions">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.reviews.show', $review) }}">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if(!$review->is_approved)
                                        <li>
                                            <a class="dropdown-item text-success" href="#" onclick="approveReview({{ $review->id }})">
                                                <i class="fas fa-check me-2"></i>Approve
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item text-warning" href="#" onclick="unapproveReview({{ $review->id }})">
                                                <i class="fas fa-times me-2"></i>Unapprove
                                            </a>
                                        </li>
                                    @endif

                                    @if(!$review->is_flagged)
                                        <li>
                                            <a class="dropdown-item text-warning" href="#" onclick="flagReview({{ $review->id }})">
                                                <i class="fas fa-flag me-2"></i>Flag Review
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item text-info" href="#" onclick="unflagReview({{ $review->id }})">
                                                <i class="fas fa-flag me-2"></i>Remove Flag
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <a class="dropdown-item text-primary" href="#" onclick="replyToReview({{ $review->id }})">
                                            <i class="fas fa-reply me-2"></i>Reply to Review
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="deleteReview({{ $review->id }})">
                                            <i class="fas fa-trash me-2"></i>Delete Review
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-star fa-3x mb-3"></i>
                                <h5>No Reviews Found</h5>
                                <p>No reviews match your search criteria.</p>
                                <button type="button" class="btn btn-primary" onclick="clearFilters()">
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($reviews->hasPages())
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col">
                <p class="text-muted mb-0">
                    Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
                </p>
            </div>
            <div class="col-auto">
                {{ $reviews->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Reply to Review Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply to Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Original Review:</label>
                        <div class="border rounded p-3 bg-light" id="originalReview">
                            <!-- Review content will be loaded here -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="replyContent" class="form-label">Your Reply *</label>
                        <textarea class="form-control" id="replyContent" name="reply" rows="4" required placeholder="Write your reply..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="markAsOfficialReply" name="official_reply" checked>
                        <label class="form-check-label" for="markAsOfficialReply">
                            Mark as official store reply
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-reply me-1"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize rating chart
    initializeRatingChart();

    // Search functionality with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterReviews();
        }, 500);
    });

    // Filter change handlers
    document.querySelectorAll('#filtersForm select').forEach(select => {
        select.addEventListener('change', filterReviews);
    });

    // Select all functionality
    document.getElementById('selectAllTable').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.review-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox handlers
    document.querySelectorAll('.review-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Reply form
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReply();
    });

    // Auto-refresh stats every 30 seconds
    setInterval(refreshStats, 30000);
});

function initializeRatingChart() {
    const ctx = document.getElementById('ratingChart').getContext('2d');

    const ratingData = @json($statistics['rating_distribution']);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
            datasets: [{
                label: 'Number of Reviews',
                data: [
                    ratingData[5] || 0,
                    ratingData[4] || 0,
                    ratingData[3] || 0,
                    ratingData[2] || 0,
                    ratingData[1] || 0
                ],
                backgroundColor: [
                    '#28a745',
                    '#6cb33f',
                    '#ffc107',
                    '#fd7e14',
                    '#dc3545'
                ],
                borderColor: [
                    '#1e7e34',
                    '#5a9a35',
                    '#e0a800',
                    '#e8650d',
                    '#c82333'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function filterReviews() {
    const formData = new FormData(document.getElementById('filtersForm'));
    const params = new URLSearchParams();

    // Add all non-empty form values to params
    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            params.append(key, value);
        }
    }

    // Add search value separately
    const searchValue = document.getElementById('searchInput').value.trim();
    if (searchValue !== '') {
        params.append('search', searchValue);
    }

    // Update URL and reload
    const url = new URL(window.location);
    url.search = params.toString();
    window.location.href = url.toString();
}

function clearFilters() {
    // Clear all form inputs
    document.getElementById('filtersForm').reset();
    document.getElementById('searchInput').value = '';

    // Remove all URL parameters
    const url = new URL(window.location);
    url.search = '';
    window.location.href = url.toString();
}

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.review-checkbox:checked');
    const bulkActionBtn = document.querySelector('.btn-group [data-bs-toggle="dropdown"]');

    if (selectedCheckboxes.length > 0) {
        bulkActionBtn.disabled = false;
        bulkActionBtn.textContent = `Bulk Actions (${selectedCheckboxes.length})`;
    } else {
        bulkActionBtn.disabled = true;
        bulkActionBtn.textContent = 'Bulk Actions';
    }
}

function bulkAction(action) {
    const selectedReviews = Array.from(document.querySelectorAll('.review-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedReviews.length === 0) {
        showAlert('Please select at least one review.', 'warning');
        return;
    }

    let confirmMessage = '';
    let actionText = '';

    switch(action) {
        case 'approve':
            confirmMessage = `Are you sure you want to approve ${selectedReviews.length} review(s)?`;
            actionText = 'Approving reviews...';
            break;
        case 'reject':
            confirmMessage = `Are you sure you want to reject ${selectedReviews.length} review(s)?`;
            actionText = 'Rejecting reviews...';
            break;
        case 'flag':
            confirmMessage = `Are you sure you want to flag ${selectedReviews.length} review(s)?`;
            actionText = 'Flagging reviews...';
            break;
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${selectedReviews.length} review(s)? This action cannot be undone.`;
            actionText = 'Deleting reviews...';
            break;
        case 'export':
            exportSelectedReviews(selectedReviews);
            return;
    }

    if (confirm(confirmMessage)) {
        performBulkAction(action, selectedReviews, actionText);
    }
}

function performBulkAction(action, reviewIds, actionText) {
    showLoading(actionText);

    fetch(`{{ route('admin.reviews.bulk-action') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            action: action,
            review_ids: reviewIds
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert(data.message, 'success');
            location.reload();
        } else {
            showAlert(data.message || 'An error occurred', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while processing the request.', 'danger');
        console.error('Error:', error);
    });
}

function approveReview(reviewId) {
    if (confirm('Are you sure you want to approve this review?')) {
        showLoading('Approving review...');

        fetch(`{{ route('admin.reviews.approve', '') }}/${reviewId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while approving review.', 'danger');
            console.error('Error:', error);
        });
    }
}

function unapproveReview(reviewId) {
    if (confirm('Are you sure you want to unapprove this review?')) {
        showLoading('Unapproving review...');

        fetch(`{{ route('admin.reviews.unapprove', '') }}/${reviewId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while unapproving review.', 'danger');
            console.error('Error:', error);
        });
    }
}

function flagReview(reviewId) {
    const reason = prompt('Please enter the reason for flagging this review:');
    if (reason && reason.trim() !== '') {
        showLoading('Flagging review...');

        fetch(`{{ route('admin.reviews.flag', '') }}/${reviewId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while flagging review.', 'danger');
            console.error('Error:', error);
        });
    }
}

function unflagReview(reviewId) {
    if (confirm('Are you sure you want to remove the flag from this review?')) {
        showLoading('Removing flag...');

        fetch(`{{ route('admin.reviews.unflag', '') }}/${reviewId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while removing flag.', 'danger');
            console.error('Error:', error);
        });
    }
}

function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        const additionalConfirm = confirm('This will permanently delete the review and all associated data. Are you absolutely sure?');
        if (additionalConfirm) {
            showLoading('Deleting review...');

            fetch(`{{ route('admin.reviews.destroy', '') }}/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showAlert(data.message, 'success');
                    location.reload();
                } else {
                    showAlert(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                showAlert('An error occurred while deleting review.', 'danger');
                console.error('Error:', error);
            });
        }
    }
}

function replyToReview(reviewId) {
    showLoading('Loading review details...');

    fetch(`{{ route('admin.reviews.show', '') }}/${reviewId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Populate modal with review data
            document.getElementById('originalReview').innerHTML = `
                <div class="d-flex align-items-center mb-2">
                    <div class="stars me-2">
                        ${Array.from({length: 5}, (_, i) =>
                            `<i class="fas fa-star ${i < data.review.rating ? 'text-warning' : 'text-muted'}"></i>`
                        ).join('')}
                    </div>
                    <span class="badge bg-secondary">${data.review.rating}/5</span>
                </div>
                ${data.review.title ? `<div class="fw-bold mb-2">${data.review.title}</div>` : ''}
                <div class="text-muted">${data.review.review || 'No review text'}</div>
                <div class="mt-2">
                    <small class="text-muted">
                        By ${data.review.user.first_name} ${data.review.user.last_name} on ${data.review.created_at}
                    </small>
                </div>
            `;

            // Store review ID for form submission
            document.getElementById('replyForm').dataset.reviewId = reviewId;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('replyModal'));
            modal.show();
        } else {
            showAlert('Failed to load review details.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while loading review details.', 'danger');
        console.error('Error:', error);
    });
}

function submitReply() {
    const form = document.getElementById('replyForm');
    const reviewId = form.dataset.reviewId;
    const formData = new FormData(form);

    showLoading('Sending reply...');

    fetch(`{{ route('admin.reviews.reply', '') }}/${reviewId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('replyModal')).hide();
            form.reset();
        } else {
            if (data.errors) {
                let errorMessage = 'Please fix the following errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${data.errors[field][0]}\n`;
                });
                showAlert(errorMessage, 'danger');
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while sending reply.', 'danger');
        console.error('Error:', error);
    });
}

function exportReviews(format) {
    showLoading('Preparing export...');

    const params = new URLSearchParams(window.location.search);
    params.append('export', format);

    const exportUrl = `{{ route('admin.reviews.export') }}?${params.toString()}`;

    // Create temporary link and click it
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `reviews-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => {
        hideLoading();
        showAlert(`Reviews exported successfully as ${format.toUpperCase()}.`, 'success');
    }, 1000);
}

function exportSelectedReviews(reviewIds) {
    showLoading('Preparing export...');

    fetch('{{ route("admin.reviews.export-selected") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            review_ids: reviewIds,
            format: 'excel'
        })
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Export failed');
    })
    .then(blob => {
        hideLoading();

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `selected-reviews-${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        showAlert('Selected reviews exported successfully.', 'success');
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while exporting reviews.', 'danger');
        console.error('Error:', error);
    });
}

function refreshStats() {
    fetch('{{ route("admin.reviews.stats") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalReviews').textContent = data.stats.total_reviews.toLocaleString();
            document.getElementById('pendingReviews').textContent = data.stats.pending_reviews.toLocaleString();
            document.getElementById('approvedReviews').textContent = data.stats.approved_reviews.toLocaleString();
            document.getElementById('averageRating').textContent = data.stats.average_rating.toFixed(1) + '/5';
        }
    })
    .catch(error => {
        console.error('Error refreshing stats:', error);
    });
}

// Utility functions
function showLoading(message = 'Loading...') {
    let overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        overlay.style.zIndex = '9999';
        overlay.innerHTML = `
            <div class="bg-white rounded p-4 text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div id="loadingMessage">${message}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    } else {
        document.getElementById('loadingMessage').textContent = message;
        overlay.style.display = 'flex';
    }
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '10000';
    alertDiv.style.minWidth = '300px';

    alertDiv.innerHTML = `
        ${message.replace(/\n/g, '<br>')}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+F for search
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }

    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('searchInput');
        if (searchInput === document.activeElement) {
            searchInput.value = '';
            filterReviews();
        }
    }

    // Ctrl+A to select all
    if (e.ctrlKey && e.key === 'a' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        document.getElementById('selectAllTable').click();
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.stars .fa-star {
    font-size: 0.8rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}

.table-warning {
    --bs-table-accent-bg: rgba(255, 193, 7, 0.1);
}

.badge {
    font-size: 0.7em;
}

.btn-group .dropdown-toggle-split {
    padding-left: 0.375rem;
    padding-right: 0.375rem;
}

#ratingChart {
    height: 200px !important;
}

@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-group {
        width: 100%;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .card-body .row {
        margin: 0;
    }

    .card-body .col-6 {
        padding: 0.25rem;
    }
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endpush
