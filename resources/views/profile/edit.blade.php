@extends('layouts.app')

@section('title', 'Edit Profile - TokoSaya')
@section('meta_description', 'Update your personal information, contact details, and account preferences')

@section('content')
<div class="profile-edit-container py-5">
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
                            <h2 class="mb-1">Edit Profile</h2>
                            <p class="text-muted mb-0">Update your personal information and preferences</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" x-data="profileForm">
                    @csrf
                    @method('PUT')

                    <!-- Form content tetap sama -->
                </form>

                <!-- Account Deletion Section -->
                <div class="card shadow-sm border-danger mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Danger Zone
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-danger mb-2">Delete Account</h6>
                                <p class="text-muted mb-0">
                                    Once you delete your account, there is no going back. Please be certain.
                                    All your orders, reviews, and personal data will be permanently removed.
                                </p>
                            </div>
                            <div class="col-auto">
                                <button type="button"
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteAccountModal">
                                    <i class="fas fa-trash me-2"></i>
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6 class="alert-heading">This action cannot be undone!</h6>
                    <p class="mb-0">Deleting your account will:</p>
                    <ul class="mt-2 mb-0">
                        <li>Permanently remove all your personal data</li>
                        <li>Cancel any pending orders</li>
                        <li>Delete your order history and reviews</li>
                        <li>Remove your wishlist and preferences</li>
                    </ul>
                </div>

                <form id="deleteAccountForm" action="{{ route('profile.delete_account') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Form fields tetap sama -->
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit"
                        form="deleteAccountForm"
                        class="btn btn-danger"
                        id="confirmDeleteBtn"
                        disabled>
                    <i class="fas fa-trash me-2"></i>
                    Delete My Account
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-edit-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e2e8f0;
    border-radius: 16px 16px 0 0 !important;
    padding: 1.25rem 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

.profile-picture-upload {
    text-align: center;
}

.avatar-preview {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.avatar-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(37, 99, 235, 0.9);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: all 0.3s ease;
}

.avatar-preview:hover .avatar-overlay {
    opacity: 1;
}

.avatar-overlay i {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.avatar-overlay span {
    font-size: 0.875rem;
    font-weight: 500;
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
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.input-group-text {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    font-weight: 500;
}

.preferences-group {
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.form-check-input {
    width: 3rem;
    height: 1.5rem;
    border-radius: 1rem;
    background-color: #e5e7eb;
    border: none;
}

.form-check-input:checked {
    background-color: #2563eb;
}

.form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-check-label {
    margin-left: 0.75rem;
    cursor: pointer;
}

.upload-requirements {
    padding: 0.75rem;
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 8px;
    margin-top: 0.75rem;
}

.form-actions {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6b7280;
    color: #6b7280;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}

.btn-outline-danger {
    border: 2px solid #ef4444;
    color: #ef4444;
}

.btn-outline-danger:hover {
    background: #ef4444;
    color: white;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.save-status {
    padding: 0.5rem 1rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
}

.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
}

.modal-header {
    padding: 1.5rem 1.5rem 0;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 0 1.5rem 1.5rem;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.25rem;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border-left: 4px solid #ef4444;
}

.page-header h2 {
    font-weight: 700;
    color: #1e293b;
}

/* Animation for form elements */
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

.card {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }
.card:nth-child(4) { animation-delay: 0.4s; }

/* Responsive Design */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }

    .card-header {
        padding: 1rem;
    }

    .avatar-image {
        width: 80px;
        height: 80px;
    }

    .form-actions .d-flex {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        margin-bottom: 0.75rem;
    }

    .form-actions .btn:last-child {
        margin-bottom: 0;
    }

    .preferences-group {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .page-header .row {
        flex-direction: column;
    }

    .page-header .col-auto {
        margin-top: 1rem;
    }

    .page-header .btn {
        width: 100%;
    }
}

/* Loading States */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Focus States for Accessibility */
.form-control:focus,
.form-select:focus,
.btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Success States */
.form-control.is-valid {
    border-color: #10b981;
}

.form-control.is-valid:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Custom File Upload Styling */
.custom-file-upload {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    background: #f3f4f6;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    text-align: center;
    transition: all 0.3s ease;
}

.custom-file-upload:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

.custom-file-upload i {
    font-size: 2rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

/* Tooltip Styling */
.tooltip {
    font-size: 0.875rem;
}

.tooltip-inner {
    background: #1f2937;
    color: white;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
}

/* Progress Indicators */
.progress {
    height: 4px;
    border-radius: 2px;
    background: #f1f5f9;
}

.progress-bar {
    border-radius: 2px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('profileForm', () => ({
        loading: false,
        lastSaved: null,

        form: {
            first_name: '{{ old("first_name", auth()->user()->first_name) }}',
            last_name: '{{ old("last_name", auth()->user()->last_name) }}',
            email: '{{ old("email", auth()->user()->email) }}',
            phone: '{{ old("phone", auth()->user()->phone) }}',
            date_of_birth: '{{ old("date_of_birth", auth()->user()->date_of_birth?->format("Y-m-d")) }}',
            gender: '{{ old("gender", auth()->user()->gender) }}'
        },

        originalForm: {},

        init() {
            this.originalForm = { ...this.form };
            this.setupAutoSave();
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                this.showToast('Please select an image file', 'error');
                event.target.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                this.showToast('Image size must be less than 5MB', 'error');
                event.target.value = '';
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = (e) => {
                // Update preview image
                const preview = event.target.closest('[x-data]');
                preview.__x.$data.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        formatPhone() {
            let phone = this.form.phone.replace(/\D/g, '');

            // Remove leading 62 or +62
            if (phone.startsWith('62')) {
                phone = phone.substring(2);
            }

            // Add formatting
            if (phone.length > 3) {
                phone = phone.substring(0, 3) + '-' + phone.substring(3);
            }
            if (phone.length > 7) {
                phone = phone.substring(0, 7) + '-' + phone.substring(7);
            }

            this.form.phone = phone;
        },

        resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                this.form = { ...this.originalForm };

                // Reset file inputs
                document.querySelectorAll('input[type="file"]').forEach(input => {
                    input.value = '';
                });

                // Reset checkboxes
                document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = checkbox.defaultChecked;
                });

                // Reset selects
                document.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });

                this.showToast('Form reset successfully', 'info');
            }
        },

        setupAutoSave() {
            // Auto-save draft every 30 seconds if there are changes
            setInterval(() => {
                if (this.hasChanges()) {
                    this.saveDraft();
                }
            }, 30000);
        },

        hasChanges() {
            return JSON.stringify(this.form) !== JSON.stringify(this.originalForm);
        },

        saveDraft() {
            try {
                localStorage.setItem('profileFormDraft', JSON.stringify(this.form));
                console.log('Draft saved');
            } catch (error) {
                console.warn('Could not save draft:', error);
            }
        },

        loadDraft() {
            try {
                const draft = localStorage.getItem('profileFormDraft');
                if (draft) {
                    this.form = JSON.parse(draft);
                    this.showToast('Draft loaded', 'info');
                }
            } catch (error) {
                console.warn('Could not load draft:', error);
            }
        },

        showToast(message, type = 'info') {
            // Create toast notification
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
    }));
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form submission handling
    const form = document.querySelector('form[action*="profile.update"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                // Set loading state (Alpine.js will handle this)
                submitBtn.disabled = true;
            }
        });
    }

    // Delete account modal validation
    const deleteModal = document.getElementById('deleteAccountModal');
    const deleteForm = document.getElementById('deleteAccountForm');
    const confirmationInput = document.getElementById('delete_confirmation');
    const passwordInput = document.getElementById('delete_password');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    function validateDeleteForm() {
        const confirmationValid = confirmationInput.value.trim() === 'DELETE';
        const passwordValid = passwordInput.value.length > 0;

        confirmDeleteBtn.disabled = !(confirmationValid && passwordValid);

        if (confirmationValid) {
            confirmationInput.classList.add('is-valid');
            confirmationInput.classList.remove('is-invalid');
        } else {
            confirmationInput.classList.add('is-invalid');
            confirmationInput.classList.remove('is-valid');
        }
    }

    if (confirmationInput && passwordInput) {
        confirmationInput.addEventListener('input', validateDeleteForm);
        passwordInput.addEventListener('input', validateDeleteForm);
    }

    // Real-time form validation
    const inputs = document.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (this.value) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    }

    // Phone validation
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            const phoneRegex = /^8[0-9]{2}-[0-9]{4}-[0-9]{4}$/;
            if (this.value && !phoneRegex.test(this.value)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');

                // Show tooltip with format
                this.setAttribute('title', 'Format: 8xx-xxxx-xxxx');
                new bootstrap.Tooltip(this).show();
            } else if (this.value) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    }

    // Date of birth validation
    const dobInput = document.getElementById('date_of_birth');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const minAge = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());

            if (selectedDate > minAge) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                this.setCustomValidity('You must be at least 13 years old');
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                this.setCustomValidity('');
            }
        });
    }

    // Prevent accidental page leave with unsaved changes
    let formChanged = false;
    const formInputs = document.querySelectorAll('input, select, textarea');

    formInputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Clear form changed flag on form submission
    if (form) {
        form.addEventListener('submit', () => {
            formChanged = false;
        });
    }

    // Success message handling
    @if(session('success'))
        setTimeout(() => {
            showToast('{{ session('success') }}', 'success');
        }, 500);
    @endif

    @if($errors->any())
        setTimeout(() => {
            showToast('Please check the form for errors', 'error');
        }, 500);
    @endif
});

// Global toast function
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
