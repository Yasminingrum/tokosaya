<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Checkout - TokoSaya')</title>
    <meta name="description" content="@yield('meta_description', 'Complete your purchase securely with TokoSaya. Multiple payment options, fast shipping, and 100% secure checkout process.')">
    <meta name="keywords" content="checkout, payment, secure shopping, online payment, TokoSaya">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('title', 'Secure Checkout - TokoSaya')">
    <meta property="og:description" content="@yield('meta_description', 'Complete your purchase securely with TokoSaya')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-checkout.jpg') }}">
    <meta property="og:site_name" content="TokoSaya">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Secure Checkout - TokoSaya')">
    <meta name="twitter:description" content="@yield('meta_description', 'Complete your purchase securely')">
    <meta name="twitter:image" content="{{ asset('images/twitter-checkout.jpg') }}">

    <!-- Security Headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Checkout Styles -->
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--gray-700);
            background-color: var(--gray-50);
            margin: 0;
            padding: 0;
        }

        .checkout-container {
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        /* Header Styles */
        .checkout-header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .checkout-header .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
        }

        .checkout-header .navbar-brand:hover {
            color: var(--primary-dark);
        }

        .checkout-steps-mobile {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 0;
            text-align: center;
            font-size: 0.875rem;
        }

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            color: var(--success-color);
            font-size: 0.875rem;
        }

        .security-badge i {
            margin-right: 0.5rem;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
            border-radius: 0 0 12px 12px !important;
            padding: 1.25rem 1.5rem;
        }

        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .btn-primary:disabled {
            background: var(--gray-300);
            color: var(--gray-500);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-outline-secondary {
            border: 2px solid var(--gray-300);
            color: var(--gray-700);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--gray-700);
            color: white;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }

        .alert-info {
            background: #e0f2fe;
            color: #0277bd;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .alert-light {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .checkout-container {
                padding-bottom: 1rem;
            }

            .card-body,
            .card-header,
            .card-footer {
                padding: 1rem;
            }

            .btn-lg {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }

            .checkout-header .navbar-brand {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .btn-lg {
                width: 100%;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
            }

            .d-flex.justify-content-between > * {
                margin-bottom: 0.5rem;
            }

            .d-flex.justify-content-between > *:last-child {
                margin-bottom: 0;
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .checkout-step {
            animation: fadeIn 0.5s ease-out;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Checkout Header -->
    <header class="checkout-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid px-0">
                    <!-- Logo -->
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <i class="fas fa-store me-2"></i>
                        TokoSaya
                    </a>

                    <!-- Security Badge -->
                    <div class="security-badge d-none d-md-flex">
                        <i class="fas fa-shield-alt"></i>
                        Secured Checkout
                    </div>

                    <!-- Help Button -->
                    <div class="checkout-help">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#helpModal">
                            <i class="fas fa-question-circle me-1"></i>
                            Help
                        </button>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile Steps Indicator -->
        <div class="checkout-steps-mobile d-md-none" x-data="{ currentStep: 1 }">
            <span x-text="`Step ${currentStep} of 3`"></span>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="checkout-footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center py-4">
                        <div class="checkout-security-badges mb-3">
                            <img src="{{ asset('images/ssl-secure.png') }}" alt="SSL Secure" class="security-badge-img me-3">
                            <img src="{{ asset('images/payment-secure.png') }}" alt="Secure Payment" class="security-badge-img me-3">
                            <img src="{{ asset('images/trusted-site.png') }}" alt="Trusted Site" class="security-badge-img">
                        </div>
                        <p class="text-muted mb-2">
                            <i class="fas fa-lock me-2"></i>
                            Your information is protected by 256-bit SSL encryption
                        </p>
                        <p class="text-muted small">
                            © {{ date('Y') }} TokoSaya. All rights reserved. |
                            <a href="{{ route('privacy') }}" class="text-decoration-none">Privacy Policy</a> |
                            <a href="{{ route('terms') }}" class="text-decoration-none">Terms of Service</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">
                        <i class="fas fa-headset me-2"></i>
                        Need Help?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="help-options">
                        <div class="help-option mb-3">
                            <h6>
                                <i class="fab fa-whatsapp text-success me-2"></i>
                                WhatsApp Support
                            </h6>
                            <p class="text-muted mb-2">Get instant help via WhatsApp</p>
                            <a href="https://wa.me/6281234567890?text=Hi, I need help with my checkout"
                               class="btn btn-success btn-sm" target="_blank">
                                <i class="fab fa-whatsapp me-1"></i>
                                Chat Now
                            </a>
                        </div>

                        <div class="help-option mb-3">
                            <h6>
                                <i class="fas fa-phone text-primary me-2"></i>
                                Phone Support
                            </h6>
                            <p class="text-muted mb-2">Call our customer service</p>
                            <a href="tel:+621234567890" class="btn btn-primary btn-sm">
                                <i class="fas fa-phone me-1"></i>
                                Call Now
                            </a>
                        </div>

                        <div class="help-option mb-3">
                            <h6>
                                <i class="fas fa-envelope text-info me-2"></i>
                                Email Support
                            </h6>
                            <p class="text-muted mb-2">Send us an email</p>
                            <a href="mailto:support@tokosaya.com" class="btn btn-info btn-sm">
                                <i class="fas fa-envelope me-1"></i>
                                Email Us
                            </a>
                        </div>

                        <div class="help-option">
                            <h6>
                                <i class="fas fa-question-circle text-warning me-2"></i>
                                FAQ
                            </h6>
                            <p class="text-muted mb-2">Find answers to common questions</p>
                            <a href="{{ route('faq') }}" class="btn btn-warning btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>
                                View FAQ
                            </a>
                        </div>
                    </div>

                    <div class="support-hours mt-4">
                        <h6>Support Hours</h6>
                        <ul class="list-unstyled text-muted small">
                            <li><strong>Monday - Friday:</strong> 9:00 AM - 9:00 PM</li>
                            <li><strong>Saturday:</strong> 9:00 AM - 6:00 PM</li>
                            <li><strong>Sunday:</strong> 10:00 AM - 4:00 PM</li>
                            <li><strong>WhatsApp:</strong> 24/7 Available</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
        <!-- Toasts will be dynamically added here -->
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Custom Checkout Scripts -->
    <script>
        // Global checkout utilities
        window.CheckoutUtils = {
            // Format currency for Indonesian Rupiah
            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount / 100);
            },

            // Validate Indonesian phone number
            validatePhone(phone) {
                const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
                return phoneRegex.test(phone.replace(/\s|-/g, ''));
            },

            // Format Indonesian phone number
            formatPhone(phone) {
                let cleaned = phone.replace(/\D/g, '');
                if (cleaned.startsWith('62')) {
                    cleaned = '0' + cleaned.substring(2);
                } else if (cleaned.startsWith('+62')) {
                    cleaned = '0' + cleaned.substring(3);
                }
                return cleaned.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
            },

            // Validate postal code
            validatePostalCode(code) {
                return /^\d{5}$/.test(code);
            },

            // Show toast notification
            showToast(message, type = 'info', duration = 5000) {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();

                const toastHTML = `
                    <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                toastContainer.insertAdjacentHTML('beforeend', toastHTML);

                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement, { delay: duration });
                toast.show();

                // Remove toast element after it's hidden
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            },

            // Validate credit card number using Luhn algorithm
            validateCreditCard(number) {
                const cleaned = number.replace(/\s/g, '');
                if (!/^\d{13,19}$/.test(cleaned)) return false;

                let sum = 0;
                let isEven = false;

                for (let i = cleaned.length - 1; i >= 0; i--) {
                    let digit = parseInt(cleaned.charAt(i));

                    if (isEven) {
                        digit *= 2;
                        if (digit > 9) {
                            digit -= 9;
                        }
                    }

                    sum += digit;
                    isEven = !isEven;
                }

                return sum % 10 === 0;
            },

            // Get credit card type
            getCreditCardType(number) {
                const cleaned = number.replace(/\s/g, '');

                if (/^4/.test(cleaned)) return 'visa';
                if (/^5[1-5]/.test(cleaned)) return 'mastercard';
                if (/^3[47]/.test(cleaned)) return 'amex';
                if (/^6(?:011|5)/.test(cleaned)) return 'discover';

                return 'unknown';
            },

            // Debounce function
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            // Save form data to session storage
            saveFormData(step, data) {
                try {
                    const checkoutData = JSON.parse(sessionStorage.getItem('checkoutData') || '{}');
                    checkoutData[step] = data;
                    sessionStorage.setItem('checkoutData', JSON.stringify(checkoutData));
                } catch (error) {
                    console.warn('Could not save form data:', error);
                }
            },

            // Load form data from session storage
            loadFormData(step) {
                try {
                    const checkoutData = JSON.parse(sessionStorage.getItem('checkoutData') || '{}');
                    return checkoutData[step] || {};
                } catch (error) {
                    console.warn('Could not load form data:', error);
                    return {};
                }
            },

            // Clear form data
            clearFormData() {
                try {
                    sessionStorage.removeItem('checkoutData');
                } catch (error) {
                    console.warn('Could not clear form data:', error);
                }
            }
        };

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        const originalText = submitButton.innerHTML;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                        // Re-enable after 30 seconds as fallback
                        setTimeout(() => {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        }, 30000);
                    }
                });
            });

            // Auto-save form data
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('change', CheckoutUtils.debounce(function() {
                    const form = this.closest('form');
                    if (form) {
                        const formData = new FormData(form);
                        const data = {};
                        for (let [key, value] of formData.entries()) {
                            data[key] = value;
                        }
                        CheckoutUtils.saveFormData('current_step', data);
                    }
                }, 1000));
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Security check - prevent back button on sensitive pages
            if (window.location.pathname.includes('/checkout')) {
                history.pushState(null, null, window.location.href);
                window.addEventListener('popstate', function() {
                    history.pushState(null, null, window.location.href);
                });
            }
        });

        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden - user switched tabs
                console.log('Page hidden - checkout paused');
            } else {
                // Page is visible - user returned
                console.log('Page visible - checkout resumed');
                // You could refresh data here if needed
            }
        });

        // Handle connection status
        window.addEventListener('online', function() {
            CheckoutUtils.showToast('Connection restored', 'success');
        });

        window.addEventListener('offline', function() {
            CheckoutUtils.showToast('Connection lost. Please check your internet.', 'warning');
        });

        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    @stack('scripts')
</body>
</html>
