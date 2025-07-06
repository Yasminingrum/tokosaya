@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
<!-- Main Header Section -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-sitemap me-2"></i>Category Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportCategories('excel')">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reorderCategories()">
                <i class="fas fa-sort me-1"></i> Reorder
            </button>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus me-1"></i> Add Category
        </button>
    </div>
</div>

<!-- Statistics Cards Section -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCategories">{{ number_format($statistics['total_categories']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sitemap fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeCategories">{{ number_format($statistics['active_categories']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalProducts">{{ number_format($statistics['total_products']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Max Depth</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="maxDepth">{{ $statistics['max_depth'] }} levels</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category View Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">Category Structure</h6>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="viewMode" id="treeView" value="tree" checked>
                            <label class="btn btn-outline-primary btn-sm" for="treeView">
                                <i class="fas fa-sitemap me-1"></i> Tree View
                            </label>

                            <input type="radio" class="btn-check" name="viewMode" id="listView" value="list">
                            <label class="btn btn-outline-primary btn-sm" for="listView">
                                <i class="fas fa-list me-1"></i> List View
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchCategories" placeholder="Search categories...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="levelFilter">
                            <option value="">All Levels</option>
                            <option value="0">Root Categories</option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3+</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Tree View Container -->
                <div id="treeViewContainer">
                    <div class="category-tree">
                        @foreach($categories->where('parent_id', null) as $category)
                            @include('admin.categories.partials.tree-item', ['category' => $category, 'level' => 0])
                        @endforeach
                    </div>
                </div>

                <!-- List View Container -->
                <div id="listViewContainer" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="categoriesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Parent</th>
                                    <th>Level</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allCategories as $category)
                                <tr data-category-id="{{ $category->id }}" class="category-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                     alt="{{ $category->name }}"
                                                     class="rounded me-3" width="40" height="40" style="object-fit: cover;">
                                            @elseif($category->icon)
                                                <div class="icon-container me-3" style="width: 40px; height: 40px;">
                                                    <i class="{{ $category->icon }} fa-2x text-primary"></i>
                                                </div>
                                            @else
                                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-folder text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $category->name }}</div>
                                                <div class="text-muted small">{{ $category->slug }}</div>
                                                @if($category->description)
                                                    <div class="text-muted small">{{ Str::limit($category->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge bg-info">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="text-muted">Root Category</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Level {{ $category->level }}</span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong class="text-primary">{{ $category->product_count }}</strong>
                                            @if($category->children_count > 0)
                                                <br><small class="text-muted">+{{ $category->total_products_with_children }} in subcategories</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm" style="width: 80px;">
                                            <input type="number" class="form-control sort-order-input"
                                                   value="{{ $category->sort_order }}"
                                                   data-category-id="{{ $category->id }}"
                                                   min="0" max="999">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCategory({{ $category->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editCategory({{ $category->id }})">
                                                        <i class="fas fa-edit me-2"></i>Edit Category
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="addSubcategory({{ $category->id }})">
                                                        <i class="fas fa-plus me-2"></i>Add Subcategory
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.products.index', ['category' => $category->id]) }}">
                                                        <i class="fas fa-box me-2"></i>View Products
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($category->is_active)
                                                    <li>
                                                        <a class="dropdown-item text-warning" href="#" onclick="toggleCategoryStatus({{ $category->id }}, 'deactivate')">
                                                            <i class="fas fa-ban me-2"></i>Deactivate
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item text-success" href="#" onclick="toggleCategoryStatus({{ $category->id }}, 'activate')">
                                                            <i class="fas fa-check-circle me-2"></i>Activate
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteCategory({{ $category->id }})">
                                                        <i class="fas fa-trash me-2"></i>Delete Category
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createCategoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="categoryName" name="name" required>
                                <div class="form-text">Category name will be used to generate the URL slug</div>
                            </div>

                            <div class="mb-3">
                                <label for="categorySlug" class="form-label">URL Slug</label>
                                <input type="text" class="form-control" id="categorySlug" name="slug">
                                <div class="form-text">Leave empty to auto-generate from name</div>
                            </div>

                            <div class="mb-3">
                                <label for="categoryDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="parentCategory" class="form-label">Parent Category</label>
                                        <select class="form-select" id="parentCategory" name="parent_id">
                                            <option value="">Root Category</option>
                                            @foreach($parentOptions as $option)
                                                <option value="{{ $option->id }}">{{ str_repeat('-- ', $option->level) }}{{ $option->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sortOrder" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sortOrder" name="sort_order" value="0" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                <label class="form-check-label" for="isActive">
                                    Active category (visible to customers)
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="categoryImage" class="form-label">Category Image</label>
                                <div class="image-upload-container">
                                    <div class="image-preview" id="imagePreview">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                        <p class="text-muted mt-2">Upload category image</p>
                                    </div>
                                    <input type="file" class="form-control mt-2" id="categoryImage" name="image" accept="image/*">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="categoryIcon" class="form-label">Icon Class (Font Awesome)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="iconPreview" class="fas fa-folder"></i></span>
                                    <input type="text" class="form-control" id="categoryIcon" name="icon" placeholder="fas fa-laptop">
                                </div>
                                <div class="form-text">
                                    <small>
                                        <a href="https://fontawesome.com/icons" target="_blank">Browse FontAwesome icons</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <hr>
                    <h6 class="text-primary mb-3"><i class="fas fa-search me-2"></i>SEO Settings</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metaTitle" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="metaTitle" name="meta_title" maxlength="160">
                                <div class="form-text">Recommended: 50-60 characters</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metaDescription" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="metaDescription" name="meta_description" rows="2" maxlength="320"></textarea>
                                <div class="form-text">Recommended: 150-160 characters</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="editFormContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category Tree Item Template -->
<template id="categoryTreeItemTemplate">
    <div class="tree-item" data-category-id="">
        <div class="tree-content">
            <div class="tree-toggle">
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="tree-icon">
                <i class="fas fa-folder"></i>
            </div>
            <div class="tree-label">
                <span class="category-name"></span>
                <span class="category-meta"></span>
            </div>
            <div class="tree-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editCategory()">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="addSubcategory()">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="tree-children"></div>
    </div>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View mode toggle
    document.querySelectorAll('input[name="viewMode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleViewMode(this.value);
        });
    });

    // Search functionality with debounce
    let searchTimeout;
    document.getElementById('searchCategories').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterCategories();
        }, 300);
    });

    // Filter change handlers
    document.getElementById('statusFilter').addEventListener('change', filterCategories);
    document.getElementById('levelFilter').addEventListener('change', filterCategories);

    // Category name to slug generation
    document.getElementById('categoryName').addEventListener('input', function(e) {
        const slug = generateSlug(e.target.value);
        document.getElementById('categorySlug').value = slug;
    });

    // Icon preview
    document.getElementById('categoryIcon').addEventListener('input', function(e) {
        const iconPreview = document.getElementById('iconPreview');
        iconPreview.className = e.target.value || 'fas fa-folder';
    });

    // Image preview
    document.getElementById('categoryImage').addEventListener('change', function(e) {
        previewImage(e.target, 'imagePreview');
    });

    // Sort order change handlers
    document.querySelectorAll('.sort-order-input').forEach(input => {
        input.addEventListener('change', function(e) {
            updateSortOrder(e.target.dataset.categoryId, e.target.value);
        });
    });

    // Form submissions
    document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createCategory();
    });

    // Tree item toggles
    document.addEventListener('click', function(e) {
        if (e.target.closest('.tree-toggle')) {
            toggleTreeItem(e.target.closest('.tree-item'));
        }
    });

    // Auto-refresh stats every 30 seconds
    setInterval(refreshStats, 30000);
});

// View Mode Functions
function toggleViewMode(mode) {
    const treeContainer = document.getElementById('treeViewContainer');
    const listContainer = document.getElementById('listViewContainer');

    if (mode === 'tree') {
        treeContainer.style.display = 'block';
        listContainer.style.display = 'none';
    } else {
        treeContainer.style.display = 'none';
        listContainer.style.display = 'block';
    }
}

// Filter Functions
function filterCategories() {
    const search = document.getElementById('searchCategories').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const level = document.getElementById('levelFilter').value;

    // Filter tree view
    document.querySelectorAll('.tree-item').forEach(item => {
        const categoryName = item.querySelector('.category-name')?.textContent.toLowerCase() || '';
        const categoryStatus = item.dataset.status;
        const categoryLevel = item.dataset.level;

        let visible = true;

        if (search && !categoryName.includes(search)) {
            visible = false;
        }

        if (status && categoryStatus !== status) {
            visible = false;
        }

        if (level && categoryLevel !== level) {
            visible = false;
        }

        item.style.display = visible ? 'block' : 'none';
    });

    // Filter list view
    document.querySelectorAll('.category-row').forEach(row => {
        const categoryName = row.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
        const statusBadge = row.querySelector('.badge')?.textContent.toLowerCase() || '';
        const levelBadge = row.querySelector('.badge.bg-secondary')?.textContent || '';

        let visible = true;

        if (search && !categoryName.includes(search)) {
            visible = false;
        }

        if (status && !statusBadge.includes(status)) {
            visible = false;
        }

        if (level && !levelBadge.includes(level)) {
            visible = false;
        }

        row.style.display = visible ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('searchCategories').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('levelFilter').value = '';
    filterCategories();
}

// Tree View Functions
function toggleTreeItem(treeItem) {
    const toggle = treeItem.querySelector('.tree-toggle i');
    const children = treeItem.querySelector('.tree-children');

    if (children.style.display === 'none' || !children.style.display) {
        children.style.display = 'block';
        toggle.className = 'fas fa-chevron-down';
    } else {
        children.style.display = 'none';
        toggle.className = 'fas fa-chevron-right';
    }
}

// CRUD Operations
function createCategory() {
    const form = document.getElementById('createCategoryForm');
    const formData = new FormData(form);

    showLoading('Creating category...');

    fetch('{{ route("admin.categories.store") }}', {
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
            bootstrap.Modal.getInstance(document.getElementById('createCategoryModal')).hide();
            location.reload();
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
        showAlert('An error occurred while creating category.', 'danger');
        console.error('Error:', error);
    });
}

function editCategory(categoryId) {
    showLoading('Loading category details...');

    fetch(`{{ route('admin.categories.show', '') }}/${categoryId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            populateEditForm(data.category);
            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        } else {
            showAlert('Failed to load category details.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while loading category details.', 'danger');
        console.error('Error:', error);
    });
}

function populateEditForm(category) {
    const formContent = document.getElementById('editFormContent');

    formContent.innerHTML = `
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="category_id" value="${category.id}">

        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="editCategoryName" class="form-label">Category Name *</label>
                    <input type="text" class="form-control" id="editCategoryName" name="name" value="${category.name}" required>
                </div>

                <div class="mb-3">
                    <label for="editCategorySlug" class="form-label">URL Slug</label>
                    <input type="text" class="form-control" id="editCategorySlug" name="slug" value="${category.slug}">
                </div>

                <div class="mb-3">
                    <label for="editCategoryDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="editCategoryDescription" name="description" rows="3">${category.description || ''}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="editParentCategory" class="form-label">Parent Category</label>
                            <select class="form-select" id="editParentCategory" name="parent_id">
                                <option value="">Root Category</option>
                                @foreach($parentOptions as $option)
                                    <option value="{{ $option->id }}" ${category.parent_id == '{{ $option->id }}' ? 'selected' : ''}>
                                        {{ str_repeat('-- ', $option->level) }}{{ $option->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="editSortOrder" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="editSortOrder" name="sort_order" value="${category.sort_order}" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active" ${category.is_active ? 'checked' : ''}>
                    <label class="form-check-label" for="editIsActive">
                        Active category (visible to customers)
                    </label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="editCategoryImage" class="form-label">Category Image</label>
                    <div class="image-upload-container">
                        <div class="image-preview" id="editImagePreview">
                            ${category.image ?
                                `<img src="${category.image_url}" alt="${category.name}" class="img-fluid rounded">` :
                                `<i class="fas fa-image fa-3x text-muted"></i><p class="text-muted mt-2">Upload category image</p>`
                            }
                        </div>
                        <input type="file" class="form-control mt-2" id="editCategoryImage" name="image" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="editCategoryIcon" class="form-label">Icon Class (Font Awesome)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i id="editIconPreview" class="${category.icon || 'fas fa-folder'}"></i></span>
                        <input type="text" class="form-control" id="editCategoryIcon" name="icon" value="${category.icon || ''}" placeholder="fas fa-laptop">
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <h6 class="text-primary mb-3"><i class="fas fa-search me-2"></i>SEO Settings</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editMetaTitle" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="editMetaTitle" name="meta_title" value="${category.meta_title || ''}" maxlength="160">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editMetaDescription" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="editMetaDescription" name="meta_description" rows="2" maxlength="320">${category.meta_description || ''}</textarea>
                </div>
            </div>
        </div>
    `;

    // Add event listeners for edit form
    document.getElementById('editCategoryName').addEventListener('input', function(e) {
        if (!document.getElementById('editCategorySlug').value) {
            document.getElementById('editCategorySlug').value = generateSlug(e.target.value);
        }
    });

    document.getElementById('editCategoryIcon').addEventListener('input', function(e) {
        document.getElementById('editIconPreview').className = e.target.value || 'fas fa-folder';
    });

    document.getElementById('editCategoryImage').addEventListener('change', function(e) {
        previewImage(e.target, 'editImagePreview');
    });

    // Update form submission handler
    document.getElementById('editCategoryForm').onsubmit = function(e) {
        e.preventDefault();
        updateCategory(category.id);
    };
}

function updateCategory(categoryId) {
    const form = document.getElementById('editCategoryForm');
    const formData = new FormData(form);

    showLoading('Updating category...');

    fetch(`{{ route('admin.categories.update', '') }}/${categoryId}`, {
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
            bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
            location.reload();
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
        showAlert('An error occurred while updating category.', 'danger');
        console.error('Error:', error);
    });
}

function addSubcategory(parentId) {
    // Open create modal with parent pre-selected
    document.getElementById('parentCategory').value = parentId;
    const modal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
    modal.show();
}

function toggleCategoryStatus(categoryId, action) {
    const actionText = action === 'activate' ? 'activate' : 'deactivate';
    const confirmText = `Are you sure you want to ${actionText} this category?`;

    if (confirm(confirmText)) {
        showLoading(`${actionText.charAt(0).toUpperCase() + actionText.slice(1)}ing category...`);

        fetch(`{{ route('admin.categories.toggle-status', '') }}/${categoryId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ action: action })
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
            showAlert('An error occurred while updating category status.', 'danger');
            console.error('Error:', error);
        });
    }
}

function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
        const additionalConfirm = confirm('This will also delete all subcategories and move products to uncategorized. Are you absolutely sure?');
        if (additionalConfirm) {
            showLoading('Deleting category...');

            fetch(`{{ route('admin.categories.destroy', '') }}/${categoryId}`, {
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
                showAlert('An error occurred while deleting category.', 'danger');
                console.error('Error:', error);
            });
        }
    }
}

function updateSortOrder(categoryId, sortOrder) {
    fetch(`{{ route('admin.categories.update-sort-order', '') }}/${categoryId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ sort_order: sortOrder })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Sort order updated successfully.', 'success');
        } else {
            showAlert('Failed to update sort order.', 'danger');
        }
    })
    .catch(error => {
        showAlert('An error occurred while updating sort order.', 'danger');
        console.error('Error:', error);
    });
}

function reorderCategories() {
    showAlert('Drag and drop reordering feature coming soon!', 'info');
}

function exportCategories(format) {
    showLoading('Preparing export...');

    const exportUrl = `{{ route('admin.categories.export') }}?format=${format}`;

    // Create temporary link and click it
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `categories-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => {
        hideLoading();
        showAlert(`Categories exported successfully as ${format.toUpperCase()}.`, 'success');
    }, 1000);
}

function refreshStats() {
    fetch('{{ route("admin.categories.stats") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalCategories').textContent = data.stats.total_categories.toLocaleString();
            document.getElementById('activeCategories').textContent = data.stats.active_categories.toLocaleString();
            document.getElementById('totalProducts').textContent = data.stats.total_products.toLocaleString();
            document.getElementById('maxDepth').textContent = data.stats.max_depth + ' levels';
        }
    })
    .catch(error => {
        console.error('Error refreshing stats:', error);
    });
}

// Utility Functions
function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-fluid rounded">`;
        };
        reader.readAsDataURL(file);
    }
}

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
</script>

<style>
/* General Styles */
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

/* Category Tree Styles */
.category-tree {
    max-height: 600px;
    overflow-y: auto;
}
.tree-item {
    margin-bottom: 0.5rem;
}
.tree-content {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    background-color: #fff;
    transition: all 0.3s ease;
}
.tree-content:hover {
    background-color: #f8f9fc;
    border-color: #4e73df;
}
.tree-toggle {
    width: 20px;
    cursor: pointer;
    color: #6c757d;
    margin-right: 0.5rem;
}
.tree-toggle:hover {
    color: #4e73df;
}
.tree-icon {
    width: 30px;
    margin-right: 0.75rem;
    color: #4e73df;
}
.tree-label {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.category-name {
    font-weight: 600;
    color: #333;
}
.category-meta {
    font-size: 0.875rem;
    color: #6c757d;
}
.tree-actions {
    display: flex;
    gap: 0.25rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.tree-content:hover .tree-actions {
    opacity: 1;
}
.tree-children {
    margin-left: 2rem;
    margin-top: 0.5rem;
    display: none;
}

/* Image Upload Styles */
.image-upload-container {
    border: 2px dashed #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    text-align: center;
    transition: border-color 0.3s ease;
}
.image-upload-container:hover {
    border-color: #4e73df;
}
.image-preview {
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
}
.image-preview img {
    max-width: 100%;
    max-height: 150px;
    object-fit: cover;
}

/* Table Styles */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}
.sort-order-input {
    width: 80px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }
    .btn-group {
        width: 100%;
    }
    .tree-children {
        margin-left: 1rem;
    }
    .tree-actions {
        opacity: 1;
    }
    .modal-dialog {
        margin: 0.5rem;
    }
    .table-responsive {
        font-size: 0.875rem;
    }
}

/* Scrollbar Styles */
.category-tree::-webkit-scrollbar {
    width: 8px;
}
.category-tree::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.category-tree::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
.category-tree::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Tree Level Styling */
.tree-item[data-level="0"] .tree-content {
    background-color: #f8f9fc;
    border-color: #4e73df;
    font-weight: 600;
}
.tree-item[data-level="1"] .tree-content {
    background-color: #fff;
    border-left: 3px solid #1cc88a;
}
.tree-item[data-level="2"] .tree-content {
    background-color: #fff;
    border-left: 3px solid #f6c23e;
}
.tree-item[data-level="3"] .tree-content {
    background-color: #fff;
    border-left: 3px solid #36b9cc;
}

/* Form Enhancements */
.form-control:focus,
.form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
.btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
}

/* Badge Improvements */
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}
.badge.bg-success {
    background-color: #1cc88a !important;
}
.badge.bg-secondary {
    background-color: #6c757d !important;
}
.badge.bg-warning {
    background-color: #f6c23e !important;
    color: #333;
}
.badge.bg-info {
    background-color: #36b9cc !important;
}

/* Modal Improvements */
.modal-lg {
    max-width: 900px;
}
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Card Improvements */
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}
.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* Button Improvements */
.btn {
    border-radius: 0.35rem;
    font-weight: 400;
}
.btn-sm {
    font-size: 0.8rem;
}

/* Loading Overlay Styles */
#loadingOverlay {
    backdrop-filter: blur(2px);
}
#loadingOverlay .bg-white {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Alert Styles */
.alert {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

/* Mobile Styles */
@media (max-width: 576px) {
    .h2 {
        font-size: 1.5rem;
    }
    .card-body {
        padding: 1rem;
    }
    .btn-toolbar .btn-group {
        margin-bottom: 0.5rem;
    }
    .modal-dialog {
        margin: 0.25rem;
    }
    .tree-content {
        padding: 0.5rem;
        flex-wrap: wrap;
    }
    .tree-actions {
        width: 100%;
        justify-content: center;
        margin-top: 0.5rem;
    }
    .tree-label {
        width: 100%;
        margin-bottom: 0.25rem;
    }
}
</style>
@endpush
