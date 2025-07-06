@extends('layouts.app')

@section('title', 'My Reviews - TokoSaya')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
            <li class="breadcrumb-item active">My Reviews</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">My Reviews</h1>
                    <p class="text-muted mb-0">Manage your product reviews and ratings</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sort me-2"></i>Sort by
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?sort=newest">Newest First</a></li>
                            <li><a class="dropdown-item" href="?sort=oldest">Oldest First</a></li>
                            <li><a class="dropdown-item" href="?sort=rating_high">Highest Rating</a></li>
                            <li><a class="dropdown-item" href="?sort=rating_low">Lowest Rating</a></li>
                            <li><a class="dropdown-item" href="?sort=helpful">Most Helpful</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-star text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $reviewStats['total_reviews'] }}</h5>
                    <p class="text-muted small mb-0">Total Reviews</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ number_format($reviewStats['average_rating'], 1) }}</h5>
                    <p class="text-muted small mb-0">Average Rating</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-thumbs-up text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $reviewStats['helpful_votes'] }}</h5>
                    <p class="text-muted small mb-0">Helpful Votes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock text-secondary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $reviewStats['pending_reviews'] }}</h5>
                    <p class="text-muted small mb-0">Pending Reviews</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Rating Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="rating-distribution">
                        @for($i = 5; $i >= 1; $i--)
                        @php
                            $count = $reviewStats['rating_distribution'][$i] ?? 0;
                            $percentage = $reviewStats['total_reviews'] > 0 ? ($count / $reviewStats['total_reviews']) * 100 : 0;
                        @endphp
                        <div class="rating-row d-flex align-items-center mb-2">
                            <div class="rating-stars me-3" style="min-width: 80px;">
                                @for($j = 1; $j <= 5; $j++)
                                    <i class="fas fa-star {{ $j <= $i ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="rating-count text-muted" style="min-width: 40px;">
                                {{ $count }}
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0">Your Reviews ({{ $reviews->total() }} reviews)</h6>
                </div>
                <div class="col-auto">
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Search reviews..." id="searchReviews" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                <div class="review-item border-bottom p-4" data-review-id="{{ $review->id }}">
                    <div class="row">
                        <!-- Product Info -->
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="product-image me-3">
                                    <img src="{{ $review->product->primary_image_url }}"
                                         alt="{{ $review->product->name }}"
                                         class="rounded"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <div class="product-info">
                                    <h6 class="mb-1">
                                        <a href="{{ route('products.show', $review->product) }}" class="text-decoration-none">
                                            {{ Str::limit($review->product->name, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        Order #{{ $review->orderItem->order->order_number ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Review Content -->
                        <div class="col-md-6">
                            <div class="review-content">
                                <!-- Rating -->
                                <div class="review-rating mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 text-muted small">{{ $review->rating }}/5</span>
                                </div>

                                <!-- Review Title -->
                                @if($review->title)
                                <h6 class="review-title mb-2">{{ $review->title }}</h6>
                                @endif

                                <!-- Review Text -->
                                <div class="review-text mb-2">
                                    <p class="mb-0">{{ $review->review }}</p>
                                </div>

                                <!-- Review Images -->
                                @if($review->images && count($review->images) > 0)
                                <div class="review-images mb-2">
                                    <div class="d-flex gap-2 flex-wrap">
                                        @foreach($review->images as $image)
                                        <img src="{{ $image }}"
                                             alt="Review image"
                                             class="rounded cursor-pointer review-image-thumbnail"
                                             style="width: 60px; height: 60px; object-fit: cover;"
                                             onclick="showImageModal('{{ $image }}')">
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Review Meta -->
                                <div class="review-meta">
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $review->created_at->format('M d, Y') }}
                                        </small>
                                        @if($review->is_verified)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Verified Purchase
                                        </span>
                                        @endif
                                        @if($review->helpful_count > 0)
                                        <small class="text-muted">
                                            <i class="fas fa-thumbs-up me-1"></i>
                                            {{ $review->helpful_count }} found helpful
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Actions -->
                        <div class="col-md-3">
                            <div class="review-actions">
                                <!-- Status Badge -->
                                <div class="mb-3">
                                    @if($review->is_approved)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Published
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Under Review
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    @if($review->canBeEdited())
                                    <button class="btn btn-outline-primary btn-sm" onclick="editReview({{ $review->id }})">
                                        <i class="fas fa-edit me-1"></i>Edit Review
                                    </button>
                                    @endif

                                    <a href="{{ route('products.show', $review->product) }}#review-{{ $review->id }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View on Product
                                    </a>

                                    @if($review->canBeDeleted())
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteReview({{ $review->id }})">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                    @endif
                                </div>

                                <!-- Review Stats -->
                                <div class="review-stats mt-3 p-2 bg-light rounded">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Views:</small>
                                        <small class="fw-medium">{{ $review->view_count ?? 0 }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Helpful:</small>
                                        <small class="fw-medium">{{ $review->helpful_count }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="p-4 border-top bg-light">
                    {{ $reviews->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-star text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">No reviews yet</h5>
                    <p class="text-muted mb-4">
                        Start shopping and share your experience with other customers!
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Reviews Section -->
    @if($pendingReviews->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-bottom">
                    <h6 class="mb-0 text-warning">
                        <i class="fas fa-clock me-2"></i>Pending Reviews ({{ $pendingReviews->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        You have {{ $pendingReviews->count() }} products waiting for your review.
                    </p>
                    <div class="row">
                        @foreach($pendingReviews as $item)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->product->primary_image_url }}"
                                             alt="{{ $item->product_name }}"
                                             class="rounded me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ Str::limit($item->product_name, 30) }}</h6>
                                            <small class="text-muted">
                                                Delivered {{ $item->order->delivered_at->diffForHumans() }}
                                            </small>
                                            <div class="mt-2">
                                                <button class="btn btn-primary btn-sm" onclick="writeReview({{ $item->id }})">
                                                    <i class="fas fa-star me-1"></i>Write Review
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Reviews</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('profile.reviews') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select class="form-select" name="rating">
                            <option value="">All Ratings</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Published</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Under Review</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control" name="date_from"
                                       value="{{ request('date_from') }}" placeholder="From">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control" name="date_to"
                                       value="{{ request('date_to') }}" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="with_images"
                                   {{ request('with_images') ? 'checked' : '' }} id="withImages">
                            <label class="form-check-label" for="withImages">
                                Only reviews with images
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('profile.reviews') }}" class="btn btn-outline-primary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reviewFormContent">
                    <!-- Review form will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Review image" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.review-item {
    transition: all 0.3s ease;
}

.review-item:hover {
    background-color: #f8f9fa;
}

.review-image-thumbnail {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.review-image-thumbnail:hover {
    transform: scale(1.05);
    border-color: #0d6efd;
}

.rating-row {
    transition: all 0.3s ease;
}

.rating-row:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 8px;
    margin: -8px 0;
}

.progress {
    transition: all 0.3s ease;
}

.review-stats {
    font-size: 0.85rem;
}

.empty-state-icon i {
    opacity: 0.3;
}

.card-header.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

@media (max-width: 768px) {
    .review-item .row > .col-md-3,
    .review-item .row > .col-md-6,
    .review-item .row > .col-md-3 {
        margin-bottom: 1rem;
    }

    .review-actions .d-grid {
        gap: 0.5rem !important;
    }

    .review-images .d-flex {
        gap: 0.5rem !important;
    }

    .review-image-thumbnail {
        width: 50px !important;
        height: 50px !important;
    }
}

.review-content p {
    line-height: 1.6;
}

.product-image img {
    border: 1px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchReviews');
    const searchBtn = document.getElementById('searchBtn');

    searchBtn.addEventListener('click', function() {
        performSearch();
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    function performSearch() {
        const searchTerm = searchInput.value.trim();
        const url = new URL(window.location);

        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }

        window.location.href = url.toString();
    }
});

// Write new review
function writeReview(orderItemId) {
    fetch(`/orders/items/${orderItemId}/review-form`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('reviewFormContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        })
        .catch(error => {
            showAlert('Error loading review form', 'error');
        });
}

// Edit existing review
function editReview(reviewId) {
    fetch(`/reviews/${reviewId}/edit-form`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('reviewFormContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        })
        .catch(error => {
            showAlert('Error loading review form', 'error');
        });
}

// Delete review
function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        fetch(`/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Review deleted successfully', 'success');
                // Remove the review item from DOM
                const reviewItem = document.querySelector(`[data-review-id="${reviewId}"]`);
                if (reviewItem) {
                    reviewItem.remove();
                }
                // Reload page to update statistics
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message || 'Error deleting review', 'error');
            }
        })
        .catch(error => {
            showAlert('Error deleting review', 'error');
        });
    }
}

// Show image in modal
function showImageModal(imageUrl) {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageUrl;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Alert helper
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Handle review form submission (when loaded via AJAX)
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('review-form')) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const actionUrl = e.target.action;
        const method = e.target.method || 'POST';

        fetch(actionUrl, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message || 'Review saved successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message || 'Error saving review', 'error');
            }
        })
        .catch(error => {
            showAlert('Error saving review', 'error');
        });
    }
});

// Smooth scroll for rating distribution clicks
document.querySelectorAll('.rating-row').forEach(row => {
    row.addEventListener('click', function() {
        const rating = this.querySelector('.fas.fa-star:not(.text-muted)').closest('.rating-stars').querySelectorAll('.fas.fa-star:not(.text-muted)').length;
        const url = new URL(window.location);
        url.searchParams.set('rating', rating);
        window.location.href = url.toString();
    });
});

// Add loading states for buttons
document.addEventListener('click', function(e) {
    if (e.target.matches('[onclick*="Review"]')) {
        const btn = e.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
        btn.disabled = true;

        // Restore button after 3 seconds if no response
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 3000);
    }
});
</script>
@endpush
@endsection
