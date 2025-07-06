@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Add New Product</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="step-progress">
                <div class="step-item active" data-step="1">
                    <div class="step-icon"><i class="fas fa-info-circle"></i></div>
                    <div class="step-title">Basic Information</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-icon"><i class="fas fa-images"></i></div>
                    <div class="step-title">Images & Gallery</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-icon"><i class="fas fa-tags"></i></div>
                    <div class="step-title">Pricing & Inventory</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-icon"><i class="fas fa-shipping-fast"></i></div>
                    <div class="step-title">Shipping & SEO</div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <!-- Step 1: Basic Information -->
        <div class="step-content" id="step-1">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Product Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                                   name="sku" value="{{ old('sku') }}" required>
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateSKU()">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                        </div>
                                        @error('sku')
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
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                                                  name="short_description" rows="3">{{ old('short_description') }}</textarea>
                                        @error('short_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Detailed Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  name="description" rows="5">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Attributes Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Attributes</h5>
                        </div>
                        <div class="card-body">
                            <div id="attributesContainer">
                                @if($attributes->count() > 0)
                                    @foreach($attributes as $attribute)
                                        <div class="mb-3">
                                            <label class="form-label">{{ $attribute->name }}</label>
                                            @if($attribute->type === 'text')
                                                <input type="text" class="form-control" name="attributes[{{ $attribute->id }}]">
                                            @elseif($attribute->type === 'number')
                                                <input type="number" class="form-control" name="attributes[{{ $attribute->id }}]">
                                            @elseif($attribute->type === 'boolean')
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="attributes[{{ $attribute->id }}]" value="1">
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                            @elseif($attribute->type === 'select' && $attribute->options)
                                                <select class="form-select" name="attributes[{{ $attribute->id }}]">
                                                    <option value="">Select {{ $attribute->name }}</option>
                                                    @foreach($attribute->options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No attributes available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Status Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="featured" value="1" id="featured" {{ old('featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">Featured Product</label>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="digital" value="1" id="digital" {{ old('digital') ? 'checked' : '' }}>
                                <label class="form-check-label" for="digital">Digital Product</label>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="saveAsDraft()">
                                <i class="fas fa-save me-2"></i>Save as Draft
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="previewProduct()">
                                <i class="fas fa-eye me-2"></i>Preview Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Images & Gallery -->
        <div class="step-content d-none" id="step-2">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Images</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="image-upload-area border-dashed p-4 text-center mb-4"
                                 ondrop="handleImageDrop(event)"
                                 ondragover="handleDragOver(event)"
                                 ondragenter="handleDragEnter(event)"
                                 ondragleave="handleDragLeave(event)">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Drag & Drop Images Here</h5>
                                <p class="text-muted">or</p>
                                <input type="file" id="imageInput" class="d-none" multiple accept="image/*" onchange="handleImageSelect(event)">
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('imageInput').click()">
                                    <i class="fas fa-upload me-2"></i>Choose Images
                                </button>
                            </div>

                            <div id="imagePreviewGrid" class="row g-3 d-none">
                                <!-- Image previews will be rendered here -->
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Image Guidelines</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled small">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>High resolution (1200x1200px)</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>First image is primary</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Multiple angles</li>
                                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>No watermarks</li>
                                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>No blurry images</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Pricing & Inventory -->
        <div class="step-content d-none" id="step-3">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Pricing Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Pricing Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                   name="price" value="{{ old('price') }}" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Compare Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('compare_price') is-invalid @enderror"
                                                   name="compare_price" value="{{ old('compare_price') }}">
                                        </div>
                                        @error('compare_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Cost Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('cost_price') is-invalid @enderror"
                                                   name="cost_price" value="{{ old('cost_price') }}">
                                        </div>
                                        @error('cost_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Profit Margin</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="profitMargin" readonly>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Inventory Management</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="track_stock" id="trackStock"
                                       value="1" {{ old('track_stock', true) ? 'checked' : '' }} onchange="toggleStockTracking()">
                                <label class="form-check-label" for="trackStock">Track inventory</label>
                            </div>

                            <div class="row" id="stockFields">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                               name="stock_quantity" value="{{ old('stock_quantity', 0) }}">
                                        @error('stock_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Min Stock Level</label>
                                        <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror"
                                               name="min_stock_level" value="{{ old('min_stock_level', 5) }}">
                                        @error('min_stock_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Max Stock Level</label>
                                        <input type="number" class="form-control @error('max_stock_level') is-invalid @enderror"
                                               name="max_stock_level" value="{{ old('max_stock_level', 100) }}">
                                        @error('max_stock_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="allow_backorder" id="allowBackorder"
                                       value="1" {{ old('allow_backorder') ? 'checked' : '' }}>
                                <label class="form-check-label" for="allowBackorder">Allow backorders</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Pricing Preview Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Pricing Preview</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Customer Sees:</label>
                                <div class="h4 text-success" id="currentPriceDisplay">Rp 0</div>
                                <div class="text-muted text-decoration-line-through d-none" id="comparePriceDisplay"></div>
                                <span class="badge bg-danger d-none" id="discountBadge">
                                    Save <span id="discountAmount">0%</span>
                                </span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Profit Analysis:</label>
                                <div class="small">
                                    <div class="d-flex justify-content-between">
                                        <span>Selling Price:</span>
                                        <span id="sellingPriceDisplay">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Cost Price:</span>
                                        <span id="costPriceDisplay">Rp 0</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Profit:</span>
                                        <span id="profitDisplay" class="text-success">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Margin:</span>
                                        <span id="marginDisplay">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Shipping & SEO -->
        <div class="step-content d-none" id="step-4">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Shipping Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Weight (g)</label>
                                        <input type="number" class="form-control @error('weight_grams') is-invalid @enderror"
                                               name="weight_grams" value="{{ old('weight_grams', 0) }}">
                                        @error('weight_grams')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Length (mm)</label>
                                        <input type="number" class="form-control @error('length_mm') is-invalid @enderror"
                                               name="length_mm" value="{{ old('length_mm', 0) }}">
                                        @error('length_mm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Width (mm)</label>
                                        <input type="number" class="form-control @error('width_mm') is-invalid @enderror"
                                               name="width_mm" value="{{ old('width_mm', 0) }}">
                                        @error('width_mm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Height (mm)</label>
                                        <input type="number" class="form-control @error('height_mm') is-invalid @enderror"
                                               name="height_mm" value="{{ old('height_mm', 0) }}">
                                        @error('height_mm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">SEO Optimization</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">SEO Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                       name="meta_title" value="{{ old('meta_title') }}" maxlength="160">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="metaTitleCount">0</span>/160 characters
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">SEO Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                          name="meta_description" rows="3" maxlength="320">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="metaDescCount">0</span>/320 characters
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ url('/products/') }}/</span>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                           name="slug" value="{{ old('slug') }}">
                                </div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- SEO Preview Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">SEO Preview</h5>
                        </div>
                        <div class="card-body">
                            <div class="seo-preview">
                                <div class="seo-preview-title text-primary" id="seoPreviewTitle">Product Name - TokoSaya</div>
                                <div class="seo-preview-url text-success small" id="seoPreviewUrl">{{ url('/products/product-name') }}</div>
                                <div class="seo-preview-description text-muted small" id="seoPreviewDescription">Product description will appear here...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Preview Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Shipping Preview</h5>
                        </div>
                        <div class="card-body">
                            <div class="dimensions-display mb-2">
                                <span id="dimensionsDisplay">0 × 0 × 0 mm</span>
                                <div class="text-muted small">Weight: <span id="weightDisplay">0g</span></div>
                            </div>
                            <div class="text-muted small">Shipping costs calculated based on these dimensions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)" disabled>
                        <i class="fas fa-arrow-left me-2"></i>Previous
                    </button>

                    <div class="step-indicator">
                        <span class="current-step">1</span> of <span class="total-steps">4</span>
                    </div>

                    <div>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                            Next <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success d-none" id="submitBtn">
                            <i class="fas fa-check me-2"></i>Create Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
/* Progress Steps */
.step-progress {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 0 20px;
}

.step-progress::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 50px;
    right: 50px;
    height: 2px;
    background: #e5e5e5;
    z-index: 1;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e5e5;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step-item.active .step-icon {
    background: #0d6efd;
    color: white;
}

.step-item.completed .step-icon {
    background: #198754;
    color: white;
}

.step-title {
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    color: #6c757d;
}

.step-item.active .step-title {
    color: #0d6efd;
    font-weight: 600;
}

/* Image Upload */
.image-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.image-upload-area:hover,
.image-upload-area.drag-over {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

/* Image Preview */
.image-preview-item {
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.image-preview-item.primary {
    border-color: #198754;
}

.image-preview-item .image-actions {
    position: absolute;
    top: 5px;
    right: 5px;
}

/* SEO Preview */
.seo-preview {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
}

.seo-preview-title {
    color: #1a0dab;
    font-size: 18px;
    margin-bottom: 3px;
    line-height: 1.2;
}

.seo-preview-url {
    color: #006621;
    font-size: 14px;
    margin-bottom: 5px;
}

.seo-preview-description {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .step-progress {
        padding: 0 10px;
    }
    .step-icon {
        width: 30px;
        height: 30px;
    }
    .step-title {
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Step Navigation
let currentStep = 1;
const totalSteps = 4;

function changeStep(direction) {
    const newStep = currentStep + direction;
    if (newStep < 1 || newStep > totalSteps) return;

    // Validate current step before proceeding
    if (direction > 0 && !validateStep(currentStep)) {
        return;
    }

    // Hide current step
    document.getElementById(`step-${currentStep}`).classList.add('d-none');
    document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');

    // Show new step
    currentStep = newStep;
    document.getElementById(`step-${currentStep}`).classList.remove('d-none');
    document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');

    // Update navigation buttons
    updateNavigationButtons();

    // Update step indicator
    document.querySelector('.current-step').textContent = currentStep;
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn.disabled = currentStep === 1;

    if (currentStep === totalSteps) {
        nextBtn.classList.add('d-none');
        submitBtn.classList.remove('d-none');
    } else {
        nextBtn.classList.remove('d-none');
        submitBtn.classList.add('d-none');
    }
}

// Step Validation
function validateStep(step) {
    switch(step) {
        case 1:
            const name = document.querySelector('input[name="name"]').value;
            const sku = document.querySelector('input[name="sku"]').value;
            const category = document.querySelector('select[name="category_id"]').value;

            if (!name || !sku || !category) {
                alert('Please fill in required fields: Product Name, SKU, and Category');
                return false;
            }
            break;
        case 3:
            const price = document.querySelector('input[name="price"]').value;
            if (!price || price <= 0) {
                alert('Please enter a valid price');
                return false;
            }
            break;
    }
    return true;
}

// Image Handling
let selectedImages = [];

function handleImageSelect(event) {
    const files = Array.from(event.target.files);
    processImages(files);
}

function handleImageDrop(event) {
    event.preventDefault();
    const files = Array.from(event.dataTransfer.files);
    processImages(files);
    event.target.classList.remove('drag-over');
}

function handleDragOver(event) {
    event.preventDefault();
}

function handleDragEnter(event) {
    event.preventDefault();
    event.target.classList.add('drag-over');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.target.classList.remove('drag-over');
}

function processImages(files) {
    const validFiles = files.filter(file => {
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image file`);
            return false;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert(`${file.name} is larger than 5MB`);
            return false;
        }
        return true;
    });

    validFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            addImagePreview(file, e.target.result);
        };
        reader.readAsDataURL(file);
    });
}

function addImagePreview(file, src) {
    const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    selectedImages.push({ id: imageId, file: file, src: src });
    renderImagePreviews();
}

function renderImagePreviews() {
    const container = document.getElementById('imagePreviewGrid');

    if (selectedImages.length === 0) {
        container.classList.add('d-none');
        return;
    }

    container.classList.remove('d-none');

    const imageHTML = selectedImages.map((image, index) => `
        <div class="col-md-3 col-sm-4 col-6 mb-3">
            <div class="image-preview-item ${index === 0 ? 'primary' : ''}" data-image-id="${image.id}">
                <img src="${image.src}" class="img-fluid w-100" style="height: 120px; object-fit: cover;">
                <div class="image-actions">
                    ${index !== 0 ? `
                    <button type="button" class="btn btn-sm btn-success me-1" onclick="setPrimaryImage('${image.id}')" title="Set as primary">
                        <i class="fas fa-star"></i>
                    </button>` : ''}
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImage('${image.id}')" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    container.innerHTML = imageHTML;
}

function setPrimaryImage(imageId) {
    const imageIndex = selectedImages.findIndex(img => img.id === imageId);
    if (imageIndex > 0) {
        const [image] = selectedImages.splice(imageIndex, 1);
        selectedImages.unshift(image);
        renderImagePreviews();
    }
}

function removeImage(imageId) {
    selectedImages = selectedImages.filter(img => img.id !== imageId);
    renderImagePreviews();
}

// Price Calculations
function updatePricePreview() {
    const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
    const comparePrice = parseFloat(document.querySelector('input[name="compare_price"]').value) || 0;
    const costPrice = parseFloat(document.querySelector('input[name="cost_price"]').value) || 0;

    // Update displays
    document.getElementById('currentPriceDisplay').textContent = formatCurrency(price);
    document.getElementById('sellingPriceDisplay').textContent = formatCurrency(price);
    document.getElementById('costPriceDisplay').textContent = formatCurrency(costPrice);

    // Compare price
    const comparePriceDisplay = document.getElementById('comparePriceDisplay');
    const discountBadge = document.getElementById('discountBadge');

    if (comparePrice > price) {
        comparePriceDisplay.textContent = formatCurrency(comparePrice);
        comparePriceDisplay.classList.remove('d-none');

        const discountPercent = Math.round(((comparePrice - price) / comparePrice) * 100);
        document.getElementById('discountAmount').textContent = discountPercent + '%';
        discountBadge.classList.remove('d-none');
    } else {
        comparePriceDisplay.classList.add('d-none');
        discountBadge.classList.add('d-none');
    }

    // Profit calculation
    const profit = price - costPrice;
    const margin = costPrice > 0 ? (profit / price) * 100 : 0;

    document.getElementById('profitDisplay').textContent = formatCurrency(profit);
    document.getElementById('profitDisplay').className = profit >= 0 ? 'text-success' : 'text-danger';
    document.getElementById('marginDisplay').textContent = margin.toFixed(1) + '%';
    document.getElementById('profitMargin').value = margin.toFixed(1);
}

function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
}

// SEO Preview
function updateSEOPreview() {
    const name = document.querySelector('input[name="name"]').value || 'Product Name';
    const metaTitle = document.querySelector('input[name="meta_title"]').value || name;
    const metaDescription = document.querySelector('textarea[name="meta_description"]').value || 'Product description will appear here...';
    const slug = document.querySelector('input[name="slug"]').value || 'product-name';

    document.getElementById('seoPreviewTitle').textContent = metaTitle + ' - TokoSaya';
    document.getElementById('seoPreviewUrl').textContent = `{{ url('/products') }}/${slug}`;
    document.getElementById('seoPreviewDescription').textContent = metaDescription;
}

// Character Counters
function updateCharacterCounters() {
    const metaTitle = document.querySelector('input[name="meta_title"]');
    const metaDescription = document.querySelector('textarea[name="meta_description"]');

    document.getElementById('metaTitleCount').textContent = metaTitle.value.length;
    document.getElementById('metaDescCount').textContent = metaDescription.value.length;
}

// Dimensions Display
function updateDimensionsDisplay() {
    const length = document.querySelector('input[name="length_mm"]').value || 0;
    const width = document.querySelector('input[name="width_mm"]').value || 0;
    const height = document.querySelector('input[name="height_mm"]').value || 0;
    const weight = document.querySelector('input[name="weight_grams"]').value || 0;

    document.getElementById('dimensionsDisplay').textContent = `${length} × ${width} × ${height} mm`;
    document.getElementById('weightDisplay').textContent = `${weight}g`;
}

// Stock Tracking Toggle
function toggleStockTracking() {
    const trackStock = document.getElementById('trackStock').checked;
    document.getElementById('stockFields').style.display = trackStock ? 'flex' : 'none';
}

// SKU Generation
function generateSKU() {
    const name = document.querySelector('input[name="name"]').value;
    const category = document.querySelector('select[name="category_id"] option:checked').text;

    if (!name) {
        alert('Please enter product name first');
        return;
    }

    let sku = '';
    if (category && category !== 'Select Category') {
        sku += category.substring(0, 3).toUpperCase() + '-';
    }

    sku += name.substring(0, 5).toUpperCase().replace(/[^A-Z0-9]/g, '');
    sku += '-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0');

    document.querySelector('input[name="sku"]').value = sku;
}

// Slug Generation
function generateSlug(text) {
    return text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Quick Actions
function saveAsDraft() {
    document.querySelector('select[name="status"]').value = 'draft';
    document.getElementById('productForm').submit();
}

function previewProduct() {
    // Implement preview functionality
    alert('Preview functionality would be implemented here');
}

// Initialize on DOM Load
document.addEventListener('DOMContentLoaded', function() {
    // Price calculation listeners
    ['input[name="price"]', 'input[name="compare_price"]', 'input[name="cost_price"]'].forEach(selector => {
        document.querySelector(selector).addEventListener('input', updatePricePreview);
    });

    // SEO preview listeners
    ['input[name="name"]', 'input[name="meta_title"]', 'textarea[name="meta_description"]', 'input[name="slug"]'].forEach(selector => {
        document.querySelector(selector).addEventListener('input', updateSEOPreview);
    });

    // Character counters
    document.querySelector('input[name="meta_title"]').addEventListener('input', updateCharacterCounters);
    document.querySelector('textarea[name="meta_description"]').addEventListener('input', updateCharacterCounters);

    // Dimensions listeners
    ['input[name="length_mm"]', 'input[name="width_mm"]', 'input[name="height_mm"]', 'input[name="weight_grams"]'].forEach(selector => {
        document.querySelector(selector).addEventListener('input', updateDimensionsDisplay);
    });

    // Track stock toggle
    document.getElementById('trackStock').addEventListener('change', toggleStockTracking);

    // Auto-generate slug from name
    document.querySelector('input[name="name"]').addEventListener('input', function() {
        const slugInput = document.querySelector('input[name="slug"]');
        if (!slugInput.value || slugInput.value === generateSlug(slugInput.dataset.original || '')) {
            const newSlug = generateSlug(this.value);
            slugInput.value = newSlug;
            slugInput.dataset.original = newSlug;
        }
    });

    // Initialize displays
    updatePricePreview();
    updateSEOPreview();
    updateCharacterCounters();
    updateDimensionsDisplay();
    toggleStockTracking();
});

// Form Submission
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Add selected images
    selectedImages.forEach((image, index) => {
        formData.append('images[]', image.file);
        if (index === 0) {
            formData.append('primary_image_index', '0');
        }
    });

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Product...';
    submitBtn.disabled = true;

    // Submit form
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || 'An error occurred');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the product');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
@endpush
