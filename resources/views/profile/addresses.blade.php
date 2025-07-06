@extends('layouts.app')

@section('title', 'My Addresses - TokoSaya')
@section('meta_description', 'Manage your delivery addresses for faster checkout')

@section('content')
<div class="addresses-container py-5">
    <div class="container">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-lg-3 mb-4">
                @include('profile.partials.sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-1">My Addresses</h2>
                            <p class="text-muted mb-0">Manage your delivery addresses for faster checkout</p>
                        </div>
                        <div class="col-auto">
                            <button type="button"
                                    class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>
                                Add New Address
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Address Grid -->
                <div class="addresses-grid" x-data="addressManager">
                    @if($addresses->count() > 0)
                        <div class="row">
                            @foreach($addresses as $address)
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="address-card" data-address-id="{{ $address->id }}">
                                    <div class="address-header">
                                        <div class="address-label">
                                            <h6 class="mb-1">{{ $address->label }}</h6>
                                            @if($address->is_default)
                                                <span class="badge bg-primary">Default</span>
                                            @endif
                                        </div>
                                        <div class="address-actions">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light"
                                                        type="button"
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="#"
                                                           @click="editAddress({{ $address->id }})">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    @if(!$address->is_default)
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="#"
                                                           @click="setDefaultAddress({{ $address->id }})">
                                                            <i class="fas fa-star me-2"></i>Set as Default
                                                        </a>
                                                    </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger"
                                                           href="#"
                                                           @click="deleteAddress({{ $address->id }}, '{{ $address->label }}')">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="address-details">
                                        <div class="recipient-info mb-2">
                                            <strong>{{ $address->recipient_name }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $address->phone }}</span>
                                        </div>

                                        <div class="address-info">
                                            <p class="mb-1">{{ $address->address_line1 }}</p>
                                            @if($address->address_line2)
                                                <p class="mb-1">{{ $address->address_line2 }}</p>
                                            @endif
                                            <p class="mb-2 text-muted">
                                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                            </p>
                                        </div>

                                        @if($address->latitude && $address->longitude)
                                        <div class="address-map mb-3">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary w-100"
                                                    @click="showMap({{ $address->latitude }}, {{ $address->longitude }}, '{{ $address->label }}')">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                View on Map
                                            </button>
                                        </div>
                                        @endif

                                        <div class="address-usage">
                                            <small class="text-muted">
                                                <i class="fas fa-shopping-bag me-1"></i>
                                                Used in {{ $address->orders_count ?? 0 }} orders
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="empty-addresses text-center py-5">
                            <div class="empty-icon mb-4">
                                <i class="fas fa-map-marker-alt fa-4x text-muted"></i>
                            </div>
                            <h4 class="mb-3">No addresses saved</h4>
                            <p class="text-muted mb-4">
                                Add your first delivery address to make checkout faster and easier.
                            </p>
                            <button type="button"
                                    class="btn btn-primary btn-lg"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>
                                Add Your First Address
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Quick Tips -->
                <div class="address-tips mt-5">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                Tips for Better Delivery
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>Use clear landmarks in your address</small>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>Provide accurate phone number</small>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>Set your most used address as default</small>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>Include building name if applicable</small>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>Double-check postal code</small>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>Add multiple addresses for convenience</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalTitle">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add New Address
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addressForm" x-data="addressForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="label" class="form-label">Address Label *</label>
                            <input type="text"
                                   class="form-control"
                                   id="label"
                                   name="label"
                                   x-model="form.label"
                                   placeholder="e.g., Home, Office, etc."
                                   required>
                            <div class="invalid-feedback" x-show="errors.label" x-text="errors.label"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="recipient_name" class="form-label">Recipient Name *</label>
                            <input type="text"
                                   class="form-control"
                                   id="recipient_name"
                                   name="recipient_name"
                                   x-model="form.recipient_name"
                                   required>
                            <div class="invalid-feedback" x-show="errors.recipient_name" x-text="errors.recipient_name"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="tel"
                                       class="form-control"
                                       id="phone"
                                       name="phone"
                                       x-model="form.phone"
                                       placeholder="8xx-xxxx-xxxx"
                                       @input="formatPhone"
                                       required>
                            </div>
                            <div class="invalid-feedback" x-show="errors.phone" x-text="errors.phone"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Postal Code *</label>
                            <input type="text"
                                   class="form-control"
                                   id="postal_code"
                                   name="postal_code"
                                   x-model="form.postal_code"
                                   pattern="[0-9]{5}"
                                   maxlength="5"
                                   @input="validatePostalCode"
                                   required>
                            <div class="invalid-feedback" x-show="errors.postal_code" x-text="errors.postal_code"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address_line1" class="form-label">Address Line 1 *</label>
                        <textarea class="form-control"
                                  id="address_line1"
                                  name="address_line1"
                                  x-model="form.address_line1"
                                  rows="2"
                                  placeholder="Street address, house number, etc."
                                  required></textarea>
                        <div class="invalid-feedback" x-show="errors.address_line1" x-text="errors.address_line1"></div>
                    </div>

                    <div class="mb-3">
                        <label for="address_line2" class="form-label">Address Line 2</label>
                        <input type="text"
                               class="form-control"
                               id="address_line2"
                               name="address_line2"
                               x-model="form.address_line2"
                               placeholder="Apartment, suite, building name (optional)">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City *</label>
                            <input type="text"
                                   class="form-control"
                                   id="city"
                                   name="city"
                                   x-model="form.city"
                                   @input="loadStates"
                                   required>
                            <div class="invalid-feedback" x-show="errors.city" x-text="errors.city"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State/Province *</label>
                            <select class="form-select"
                                    id="state"
                                    name="state"
                                    x-model="form.state"
                                    required>
                                <option value="">Select State</option>
                                <template x-for="state in states" :key="state.value">
                                    <option :value="state.value" x-text="state.label"></option>
                                </template>
                            </select>
                            <div class="invalid-feedback" x-show="errors.state" x-text="errors.state"></div>
                        </div>
                    </div>

                    <!-- Location Picker -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Pin Location (Optional)
                        </label>
                        <div class="location-picker">
                            <button type="button"
                                    class="btn btn-outline-primary w-100"
                                    @click="openLocationPicker">
                                <i class="fas fa-crosshairs me-2"></i>
                                <span x-text="hasLocation ? 'Update Location' : 'Pin Your Location'"></span>
                            </button>
                            <div x-show="hasLocation" class="mt-2">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Location pinned successfully
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Set as Default -->
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="is_default"
                               name="is_default"
                               x-model="form.is_default">
                        <label class="form-check-label" for="is_default">
                            Set as default address
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit"
                            class="btn btn-primary"
                            :disabled="loading">
                        <span x-show="!loading">
                            <i class="fas fa-save me-2"></i>
                            <span x-text="editMode ? 'Update Address' : 'Save Address'"></span>
                        </span>
                        <span x-show="loading">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map me-2"></i>
                    Address Location
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="addressMap" style="height: 400px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Location Picker Modal -->
<div class="modal fade" id="locationPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-crosshairs me-2"></i>
                    Pin Your Location
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="locationPickerMap" style="height: 400px; width: 100%;"></div>
                <div class="p-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Drag the marker to pin your exact location for more accurate delivery.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmLocationBtn">
                    <i class="fas fa-check me-2"></i>
                    Confirm Location
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash me-2"></i>
                    Delete Address
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6>Are you sure you want to delete this address?</h6>
                    <p class="text-muted" id="deleteAddressName"></p>
                    <p class="text-danger">
                        <strong>This action cannot be undone.</strong>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>
                    Delete Address
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.addresses-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}

.page-header h2 {
    font-weight: 700;
    color: #1e293b;
}

.address-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    height: 100%;
}

.address-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: #e2e8f0;
}

.address-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.address-label h6 {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.address-actions .btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.address-details {
    color: #64748b;
    line-height: 1.6;
}

.recipient-info strong {
    color: #1e293b;
    font-weight: 600;
}

.address-info p {
    margin-bottom: 0.5rem;
}

.address-usage {
    padding-top: 1rem;
    border-top: 1px solid #f1f5f9;
    margin-top: 1rem;
}

.empty-addresses {
    background: white;
    border-radius: 16px;
    padding: 3rem 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    opacity: 0.5;
}

.address-tips .card {
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-left: 4px solid #f59e0b;
}

.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
}

.modal-header {
    border-bottom: 1px solid #f1f5f9;
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #f1f5f9;
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control,
.form-select {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.input-group-text {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    font-weight: 500;
}

.location-picker {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
}

.location-picker:hover {
    border-color: #9ca3af;
    background: #f9fafb;
}

.btn {
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
}

.btn-outline-primary {
    border: 2px solid #2563eb;
    color: #2563eb;
}

.btn-outline-primary:hover {
    background: #2563eb;
    color: white;
}

.dropdown-menu {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: #f8fafc;
    color: #2563eb;
}

.dropdown-item.text-danger:hover {
    background: #fef2f2;
    color: #dc2626;
}

.alert {
    border-radius: 12px;
    border: none;
}

.alert-info {
    background: #eff6ff;
    color: #1e40af;
    border-left: 4px solid #3b82f6;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.address-card {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.address-card:nth-child(1) { animation-delay: 0.1s; }
.address-card:nth-child(2) { animation-delay: 0.2s; }
.address-card:nth-child(3) { animation-delay: 0.3s; }
.address-card:nth-child(4) { animation-delay: 0.4s; }
.address-card:nth-child(5) { animation-delay: 0.5s; }
.address-card:nth-child(6) { animation-delay: 0.6s; }

/* Responsive Design */
@media (max-width: 768px) {
    .address-card {
        margin-bottom: 1rem;
    }

    .page-header .row {
        flex-direction: column;
    }

    .page-header .col-auto {
        margin-top: 1rem;
    }

    .page-header .btn {
        width: 100%;
    }

    .modal-dialog {
        margin: 1rem;
    }

    .empty-addresses {
        padding: 2rem 1rem;
    }
}

/* Map Styling */
.leaflet-container {
    border-radius: 12px;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
}

/* Loading States */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Validation States */
.form-control.is-valid {
    border-color: #10b981;
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback {
    display: block;
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Custom Checkbox */
.form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 0.375rem;
    border: 2px solid #d1d5db;
}

.form-check-input:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}

.form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
</style>
@endpush

@push('scripts')
<!-- Leaflet for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('addressManager', () => ({
        init() {
            // Initialize address manager
        },

        editAddress(addressId) {
            // Load address data and open modal
            fetch(`/api/addresses/${addressId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate form with address data
                        const form = document.getElementById('addressForm');
                        const formData = form.__x.$data;
                        formData.populateForm(data.address);
                        formData.editMode = true;
                        formData.editId = addressId;

                        // Update modal title
                        document.getElementById('addressModalTitle').innerHTML =
                            '<i class="fas fa-edit me-2"></i>Edit Address';

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
                        modal.show();
                    } else {
                        showToast('Failed to load address data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading address:', error);
                    showToast('Failed to load address data', 'error');
                });
        },

        setDefaultAddress(addressId) {
            if (confirm('Set this address as your default delivery address?')) {
                fetch(`/api/addresses/${addressId}/set-default`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Default address updated', 'success');
                        location.reload();
                    } else {
                        showToast(data.message || 'Failed to update default address', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error setting default address:', error);
                    showToast('Failed to update default address', 'error');
                });
            }
        },

        deleteAddress(addressId, addressLabel) {
            document.getElementById('deleteAddressName').textContent = addressLabel;

            const modal = new bootstrap.Modal(document.getElementById('deleteAddressModal'));
            modal.show();

            document.getElementById('confirmDeleteBtn').onclick = () => {
                fetch(`/api/addresses/${addressId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Address deleted successfully', 'success');
                        location.reload();
                    } else {
                        showToast(data.message || 'Failed to delete address', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting address:', error);
                    showToast('Failed to delete address', 'error');
                })
                .finally(() => {
                    modal.hide();
                });
            };
        },

        showMap(lat, lng, title) {
            const modal = new bootstrap.Modal(document.getElementById('mapModal'));
            modal.show();

            // Initialize map after modal is shown
            setTimeout(() => {
                const map = L.map('addressMap').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([lat, lng]).addTo(map).bindPopup(title).openPopup();
            }, 300);
        }
    }));

    Alpine.data('addressForm', () => ({
        loading: false,
        editMode: false,
        editId: null,
        hasLocation: false,

        form: {
            label: '',
            recipient_name: '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}',
            phone: '{{ auth()->user()->phone ?? "" }}',
            address_line1: '',
            address_line2: '',
            city: '',
            state: '',
            postal_code: '',
            latitude: null,
            longitude: null,
            is_default: false
        },

        states: [
            { value: 'Jawa Barat', label: 'Jawa Barat' },
            { value: 'Jawa Tengah', label: 'Jawa Tengah' },
            { value: 'Jawa Timur', label: 'Jawa Timur' },
            { value: 'DKI Jakarta', label: 'DKI Jakarta' },
            { value: 'Sumatera Utara', label: 'Sumatera Utara' },
            { value: 'Sumatera Selatan', label: 'Sumatera Selatan' },
            { value: 'Sumatera Barat', label: 'Sumatera Barat' },
            { value: 'Kalimantan Timur', label: 'Kalimantan Timur' },
            { value: 'Kalimantan Selatan', label: 'Kalimantan Selatan' },
            { value: 'Sulawesi Selatan', label: 'Sulawesi Selatan' },
            { value: 'Bali', label: 'Bali' },
            { value: 'Yogyakarta', label: 'Yogyakarta' }
        ],

        errors: {},

        init() {
            // Reset form when modal is shown
            document.getElementById('addAddressModal').addEventListener('show.bs.modal', () => {
                if (!this.editMode) {
                    this.resetForm();
                }
            });

            // Setup form submission
            document.getElementById('addressForm').addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitForm();
            });
        },

        populateForm(address) {
            this.form = {
                label: address.label,
                recipient_name: address.recipient_name,
                phone: address.phone,
                address_line1: address.address_line1,
                address_line2: address.address_line2 || '',
                city: address.city,
                state: address.state,
                postal_code: address.postal_code,
                latitude: address.latitude,
                longitude: address.longitude,
                is_default: address.is_default
            };

            this.hasLocation = !!(address.latitude && address.longitude);
        },

        resetForm() {
            this.form = {
                label: '',
                recipient_name: '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}',
                phone: '{{ auth()->user()->phone ?? "" }}',
                address_line1: '',
                address_line2: '',
                city: '',
                state: '',
                postal_code: '',
                latitude: null,
                longitude: null,
                is_default: false
            };

            this.editMode = false;
            this.editId = null;
            this.hasLocation = false;
            this.errors = {};

            // Reset modal title
            document.getElementById('addressModalTitle').innerHTML =
                '<i class="fas fa-plus-circle me-2"></i>Add New Address';
        },

        formatPhone() {
            let phone = this.form.phone.replace(/\D/g, '');

            if (phone.startsWith('62')) {
                phone = phone.substring(2);
            }

            if (phone.length > 3) {
                phone = phone.substring(0, 3) + '-' + phone.substring(3);
            }
            if (phone.length > 7) {
                phone = phone.substring(0, 7) + '-' + phone.substring(7);
            }

            this.form.phone = phone;
        },

        validatePostalCode() {
            const postalCode = this.form.postal_code.replace(/\D/g, '');
            this.form.postal_code = postalCode.substring(0, 5);
        },

        openLocationPicker() {
            const modal = new bootstrap.Modal(document.getElementById('locationPickerModal'));
            modal.show();

            // Initialize location picker map
            setTimeout(() => {
                this.initLocationPicker();
            }, 300);
        },

        initLocationPicker() {
            const defaultLat = this.form.latitude || -6.2088;
            const defaultLng = this.form.longitude || 106.8456;

            const map = L.map('locationPickerMap').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            // Update coordinates when marker is dragged
            marker.on('dragend', (e) => {
                const position = e.target.getLatLng();
                this.form.latitude = position.lat;
                this.form.longitude = position.lng;
            });

            // Confirm location button
            document.getElementById('confirmLocationBtn').onclick = () => {
                this.hasLocation = true;
                bootstrap.Modal.getInstance(document.getElementById('locationPickerModal')).hide();
                showToast('Location pinned successfully', 'success');
            };
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const url = this.editMode
                    ? `/api/addresses/${this.editId}`
                    : '/api/addresses';

                const method = this.editMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (data.success) {
                    showToast(
                        this.editMode ? 'Address updated successfully' : 'Address added successfully',
                        'success'
                    );

                    // Close modal and reload page
                    bootstrap.Modal.getInstance(document.getElementById('addAddressModal')).hide();
                    location.reload();
                } else {
                    this.errors = data.errors || {};
                    showToast(data.message || 'Please check the form for errors', 'error');
                }
            } catch (error) {
                console.error('Submit error:', error);
                showToast('Failed to save address', 'error');
            } finally {
                this.loading = false;
            }
        }
    }));
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0 show`;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px;';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endpush
