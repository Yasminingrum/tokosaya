@extends('layouts.admin')

@section('title', 'Edit Product: ' . $product->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Product</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Edit: {{ Str::limit($product->name, 30) }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>View Product
            </a>
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-success" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>Preview Live
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <!-- Product Status Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="product-status-indicator me-3">
                            <span class="badge bg-{{ $product->status_color }} fs-6">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <div class="metric">
                        <div class="h4 mb-0">{{ format_currency($product->price_cents) }}</div>
                        <small class="text-muted">Current Price</small>
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <div class="metric">
                        <div class="h4 mb-0 {{ $product->stock_quantity <= $product->min_stock_level ? 'text-warning' : 'text-success' }}">
                            {{ number_format($product->stock_quantity) }}
                        </div>
                        <small class="text-muted">Stock Level</small>
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <div class="metric">
                        <div class="h4 mb-0">{{ number_format($product->sale_count) }}</div>
                        <small class="text-muted">Total Sales</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="duplicateProduct()">
                            <i class="fas fa-copy me-1"></i>Duplicate
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProduct()">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name', $product->name) }}"
                                           placeholder="Enter product name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                           name="sku" value="{{ old('sku', $product->sku) }}"
                                           placeholder="e.g., LAPTOP-001" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Changing SKU may affect inventory tracking
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                           name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                           placeholder="e.g., 1234567890123">
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Brand</label>
                                    <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                    {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Short Description</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              name="short_description" rows="3"
                                              placeholder="Brief product description"
                                              maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="shortDescCount">{{ strlen($product->short_description ?? '') }}</span>/500 characters
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Detailed Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              name="description" rows="8"
                                              placeholder="Detailed product description">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Product Images</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="openImageUploader()">
                            <i class="fas fa-plus me-1"></i>Add Images
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row" id="currentImagesGrid">
                            @forelse($product->images as $image)
                                <div class="col-md-3 col-sm-4 col-6 mb-3" data-image-id="{{ $image->id }}">
                                    <div class="image-item {{ $image->is_primary ? 'primary' : '' }}">
                                        <img src="{{ $image->image_url }}" class="img-fluid"
                                             style="height: 150px; width: 100%; object-fit: cover; border-radius: 8px;">

                                        @if($image->is_primary)
                                            <div class="primary-badge">Primary</div>
                                        @endif

                                        <div class="image-actions">
                                            @if(!$image->is_primary)
                                                <button type="button" class="btn btn-sm btn-success"
                                                        onclick="setPrimaryImage({{ $image->id }})" title="Set as Primary">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                    onclick="editImageAlt({{ $image->id }}, '{{ $image->alt_text }}')" title="Edit Alt Text">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="removeImage({{ $image->id }})" title="Remove">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No images uploaded</h6>
                                    <button type="button" class="btn btn-primary" onclick="openImageUploader()">
                                        <i class="fas fa-upload me-2"></i>Upload Images
                                    </button>
                                </div>
                            @endforelse
                        </div>

                        <!-- Hidden file input -->
                        <input type="file" id="imageUploader" name="new_images[]" multiple
                               accept="image/*" style="display: none;" onchange="handleNewImages(event)">
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pricing & Inventory</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Regular Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                               name="price" value="{{ old('price', $product->price_cents / 100) }}"
                                               placeholder="0" min="0" step="100" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Compare Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('compare_price') is-invalid @enderror"
                                               name="compare_price" value="{{ old('compare_price', $product->compare_price_cents ? $product->compare_price_cents / 100 : '') }}"
                                               placeholder="0" min="0" step="100">
                                    </div>
                                    @error('compare_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Cost Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('cost_price') is-invalid @enderror"
                                               name="cost_price" value="{{ old('cost_price', $product->cost_price_cents ? $product->cost_price_cents / 100 : '') }}"
                                               placeholder="0" min="0" step="100">
                                    </div>
                                    @error('cost_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                               name="track_stock" value="1" id="trackStock"
                                               {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="trackStock">
                                            Track inventory for this product
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" id="stockFields">
                                <div class="mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                           name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                           placeholder="0" min="0">
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3" id="minStockFields">
                                <div class="mb-3">
                                    <label class="form-label">Min Stock Level</label>
                                    <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror"
                                           name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}"
                                           placeholder="5" min="0">
                                    @error('min_stock_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3" id="maxStockFields">
                                <div class="mb-3">
                                    <label class="form-label">Max Stock Level</label>
                                    <input type="number" class="form-control @error('max_stock_level') is-invalid @enderror"
                                           name="max_stock_level" value="{{ old('max_stock_level', $product->max_stock_level) }}"
                                           placeholder="1000" min="1">
                                    @error('max_stock_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Reserved Stock</label>
                                    <input type="number" class="form-control"
                                           value="{{ $product->reserved_quantity }}" readonly>
                                    <div class="form-text text-muted">
                                        Stock reserved for pending orders
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                           name="allow_backorder" value="1" id="allowBackorder"
                                           {{ old('allow_backorder', $product->allow_backorder) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allowBackorder">
                                        Allow customers to purchase when out of stock
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Weight (grams)</label>
                                    <input type="number" class="form-control @error('weight_grams') is-invalid @enderror"
                                           name="weight_grams" value="{{ old('weight_grams', $product->weight_grams) }}"
                                           placeholder="0" min="0">
                                    @error('weight_grams')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Length (mm)</label>
                                    <input type="number" class="form-control @error('length_mm') is-invalid @enderror"
                                           name="length_mm" value="{{ old('length_mm', $product->length_mm) }}"
                                           placeholder="0" min="0">
                                    @error('length_mm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Width (mm)</label>
                                    <input type="number" class="form-control @error('width_mm') is-invalid @enderror"
                                           name="width_mm" value="{{ old('width_mm', $product->width_mm) }}"
                                           placeholder="0" min="0">
                                    @error('width_mm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Height (mm)</label>
                                    <input type="number" class="form-control @error('height_mm') is-invalid @enderror"
                                           name="height_mm" value="{{ old('height_mm', $product->height_mm) }}"
                                           placeholder="0" min="0">
                                    @error('height_mm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">SEO Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                   name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                                   placeholder="Will use product name if empty" maxlength="160">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="metaTitleCount">{{ strlen($product->meta_title ?? '') }}</span>/160 characters
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SEO Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                      name="meta_description" rows="3"
                                      placeholder="Brief description for search engines"
                                      maxlength="320">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="metaDescCount">{{ strlen($product->meta_description ?? '') }}</span>/320 characters
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product URL Slug</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/products/') }}/</span>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                       name="slug" value="{{ old('slug', $product->slug) }}"
                                       placeholder="product-url-slug">
                            </div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                ⚠️ Changing the URL will break existing links and affect SEO
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="discontinued" {{ old('status', $product->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                       name="featured" value="1" id="featured"
                                       {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">
                                    Featured Product
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                       name="digital" value="1" id="digital"
                                       {{ old('digital', $product->digital) ? 'checked' : '' }}>
                                <label class="form-check-label" for="digital">
                                    Digital Product
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="stat-row">
                            <span>Total Views:</span>
                            <strong>{{ number_format($product->view_count) }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>Total Sales:</span>
                            <strong>{{ number_format($product->sale_count) }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>Revenue:</span>
                            <strong>{{ format_currency($product->revenue_cents) }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>Reviews:</span>
                            <strong>{{ $product->rating_count }} reviews</strong>
                        </div>
                        <div class="stat-row">
                            <span>Rating:</span>
                            <strong>{{ $product->rating_average }}/5.0</strong>
                        </div>
                        <div class="stat-row">
                            <span>Created:</span>
                            <strong>{{ $product->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>Last Update:</span>
                            <strong>{{ $product->updated_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Product
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                                <i class="fas fa-edit me-2"></i>Save as Draft
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="previewProduct()">
                                <i class="fas fa-eye me-2"></i>Preview Changes
                            </button>
                            <hr>
                            <button type="button" class="btn btn-outline-primary" onclick="duplicateProduct()">
                                <i class="fas fa-copy me-2"></i>Duplicate Product
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="resetChanges()">
                                <i class="fas fa-undo me-2"></i>Reset Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Version History -->
                @if($product->versions && $product->versions->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Version History</h5>
                    </div>
                    <div class="card-body">
                        <div class="version-history">
                            @foreach($product->versions->take(5) as $version)
                                <div class="version-item">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $version->created_at->diffForHumans() }}</small>
                                        <button type="button" class="btn btn-xs btn-outline-primary"
                                                onclick="restoreVersion({{ $version->id }})">
                                            Restore
                                        </button>
                                    </div>
                                    <div class="version-changes">
                                        <small>{{ $version->changes_summary }}</small>
                                    </div>
                                </div>
                            @endforeach
                            @if($product->versions->count() > 5)
                                <div class="text-center mt-2">
                                    <a href="{{ route('admin.products.versions', $product) }}" class="btn btn-sm btn-outline-secondary">
                                        View All Versions
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Image Alt Text Modal -->
<div class="modal fade" id="altTextModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Image Alt Text</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Alt Text</label>
                    <input type="text" class="form-control" id="altTextInput"
                           placeholder="Describe the image for accessibility">
                    <div class="form-text">
                        Help screen readers and search engines understand the image content
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAltText()">Save Alt Text</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.metric {
    text-align: center;
}

.metric .h4 {
    font-weight: 600;
    margin-bottom: 4px;
}

.product-status-indicator .badge {
    padding: 6px 12px;
}

.image-item {
    position: relative;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-item:hover {
    border-color: #0d6efd;
}

.image-item.primary {
    border-color: #198754;
    box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.2);
}

.image-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-item:hover .image-actions {
    opacity: 1;
}

.primary-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(25, 135, 84, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-row {
    display: flex;
    justify-content: between;
    padding: 6px 0;
    border-bottom: 1px solid #f0f0f0;
}

.stat-row:last-child {
    border-bottom: none;
}

.version-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.version-item:last-child {
    border-bottom: none;
}

.version-changes {
    margin-top: 4px;
    font-style: italic;
}

.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e5e5e5;
}

@media (max-width: 768px) {
    .metric {
        margin-bottom: 1rem;
    }

    .stat-row {
        flex-direction: column;
        text-align: left;
    }

    .image-actions {
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<script>
let currentEditingImageId = null;

// Image management functions
function openImageUploader() {
    document.getElementById('imageUploader').click();
}

function handleNewImages(event) {
    const files = Array.from(event.target.files);
    const formData = new FormData();

    files.forEach(file => {
        formData.append('images[]', file);
    });
    formData.append('product_id', '{{ $product->id }}');
    formData.append('_token', '{{ csrf_token() }}');

    // Show loading state
    const grid = document.getElementById('currentImagesGrid');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'col-12 text-center py-3';
    loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading images...';
    grid.appendChild(loadingDiv);

    fetch('{{ route("admin.products.upload-images", $product) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to show new images
        } else {
            alert(data.message || 'Failed to upload images');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Failed to upload images');
    })
    .finally(() => {
        loadingDiv.remove();
        event.target.value = ''; // Clear file input
    });
}

function setPrimaryImage(imageId) {
    fetch(`{{ route('admin.products.set-primary-image', $product) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ image_id: imageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to update primary image display
        } else {
            alert(data.message || 'Failed to set primary image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to set primary image');
    });
}

function editImageAlt(imageId, currentAlt) {
    currentEditingImageId = imageId;
    document.getElementById('altTextInput').value = currentAlt || '';
    new bootstrap.Modal(document.getElementById('altTextModal')).show();
}

function saveAltText() {
    const altText = document.getElementById('altTextInput').value;

    fetch(`{{ route('admin.products.update-image-alt', $product) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            image_id: currentEditingImageId,
            alt_text: altText
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('altTextModal')).hide();
            // Update the UI or show success message
        } else {
            alert(data.message || 'Failed to update alt text');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update alt text');
    });
}

function removeImage(imageId) {
    if (!confirm('Are you sure you want to remove this image?')) {
        return;
    }

    fetch(`{{ route('admin.products.remove-image', $product) }}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ image_id: imageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-image-id="${imageId}"]`).remove();

            // Check if no images left
            const remainingImages = document.querySelectorAll('[data-image-id]');
            if (remainingImages.length === 0) {
                document.getElementById('currentImagesGrid').innerHTML = `
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No images uploaded</h6>
                        <button type="button" class="btn btn-primary" onclick="openImageUploader()">
                            <i class="fas fa-upload me-2"></i>Upload Images
                        </button>
                    </div>
                `;
            }
        } else {
            alert(data.message || 'Failed to remove image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove image');
    });
}

// Stock tracking toggle
function toggleStockTracking() {
    const trackStock = document.getElementById('trackStock').checked;
    const stockFields = document.getElementById('stockFields');
    const minStockFields = document.getElementById('minStockFields');
    const maxStockFields = document.getElementById('maxStockFields');

    if (trackStock) {
        stockFields.style.display = 'block';
        minStockFields.style.display = 'block';
        maxStockFields.style.display = 'block';
        document.querySelector('input[name="stock_quantity"]').required = true;
    } else {
        stockFields.style.display = 'none';
        minStockFields.style.display = 'none';
        maxStockFields.style.display = 'none';
        document.querySelector('input[name="stock_quantity"]').required = false;
    }
}

// Character counters
function updateCharacterCounters() {
    const shortDesc = document.querySelector('textarea[name="short_description"]');
    const metaTitle = document.querySelector('input[name="meta_title"]');
    const metaDescription = document.querySelector('textarea[name="meta_description"]');

    if (shortDesc) {
        document.getElementById('shortDescCount').textContent = shortDesc.value.length;
    }
    if (metaTitle) {
        document.getElementById('metaTitleCount').textContent = metaTitle.value.length;
    }
    if (metaDescription) {
        document.getElementById('metaDescCount').textContent = metaDescription.value.length;
    }
}

// Quick actions
function saveAsDraft() {
    document.querySelector('select[name="status"]').value = 'draft';
    document.getElementById('productForm').submit();
}

function previewProduct() {
    // Open current product page in new tab
    window.open('{{ route("products.show", $product) }}', '_blank');
}

function duplicateProduct() {
    if (confirm('Create a duplicate of this product?')) {
        fetch('{{ route("admin.products.duplicate", $product) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.edit_url;
            } else {
                alert(data.message || 'Failed to duplicate product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to duplicate product');
        });
    }
}

function deleteProduct() {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.products.destroy", $product) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

function resetChanges() {
    if (confirm('Reset all changes? This will reload the page and lose any unsaved changes.')) {
        location.reload();
    }
}

function restoreVersion(versionId) {
    if (confirm('Restore this version? Current changes will be lost.')) {
        fetch(`{{ route('admin.products.restore-version', $product) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ version_id: versionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to restore version');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to restore version');
        });
    }
}

// Form validation before submit
document.getElementById('productForm').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="name"]').value.trim();
    const sku = document.querySelector('input[name="sku"]').value.trim();
    const category = document.querySelector('select[name="category_id"]').value;
    const price = document.querySelector('input[name="price"]').value;

    if (!name || !sku || !category || !price || price <= 0) {
        e.preventDefault();
        alert('Please fill in all required fields: Name, SKU, Category, and Price');
        return false;
    }

    // Show loading state
    const submitBtns = document.querySelectorAll('button[type="submit"]');
    submitBtns.forEach(btn => {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        btn.disabled = true;
    });
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Character counter listeners
    ['textarea[name="short_description"]', 'input[name="meta_title"]', 'textarea[name="meta_description"]'].forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.addEventListener('input', updateCharacterCounters);
        }
    });

    // Stock tracking toggle
    document.getElementById('trackStock').addEventListener('change', toggleStockTracking);

    // Initialize stock fields visibility
    toggleStockTracking();

    // Initialize character counters
    updateCharacterCounters();

    // Auto-save draft every 5 minutes
    setInterval(function() {
        if (document.querySelector('select[name="status"]').value === 'draft') {
            // Auto-save logic could be implemented here
            console.log('Auto-save check...');
        }
    }, 300000); // 5 minutes

    // Warn about unsaved changes
    let formChanged = false;
    const form = document.getElementById('productForm');
    const originalFormData = new FormData(form);

    form.addEventListener('input', function() {
        formChanged = true;
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    form.addEventListener('submit', function() {
        formChanged = false; // Don't warn when submitting
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S or Cmd+S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.getElementById('productForm').submit();
    }

    // Ctrl+Shift+P or Cmd+Shift+P to preview
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'P') {
        e.preventDefault();
        previewProduct();
    }
});
</script>
@endpush
