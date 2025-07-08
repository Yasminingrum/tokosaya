@extends('layouts.auth')

@section('title', 'Login')
@section('description', 'Masuk ke akun TokoSaya Anda untuk pengalaman belanja yang lebih personal')

@section('visual_content')
    <h2>Selamat Datang Kembali!</h2>
    <p>Masuk ke akun Anda dan nikmati pengalaman belanja yang tak terlupakan bersama TokoSaya.</p>

    <div class="auth-features">
        <div class="auth-feature">
            <i class="fas fa-shopping-cart"></i>
            <span>Keranjang tersimpan otomatis</span>
        </div>
        <div class="auth-feature">
            <i class="fas fa-heart"></i>
            <span>Wishlist produk favorit</span>
        </div>
        <div class="auth-feature">
            <i class="fas fa-truck"></i>
            <span>Lacak status pengiriman</span>
        </div>
        <div class="auth-feature">
            <i class="fas fa-star"></i>
            <span>Review dan rating produk</span>
        </div>
        <div class="auth-feature">
            <i class="fas fa-gift"></i>
            <span>Promo eksklusif member</span>
        </div>
        <div class="auth-feature">
            <i class="fas fa-headset"></i>
            <span>Customer service 24/7</span>
        </div>
    </div>
@endsection

@section('form_title', 'Masuk ke Akun')
@section('form_subtitle', 'Gunakan email dan password untuk mengakses akun Anda')

@section('form_content')
    {{-- Enhanced Error Display --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Login Gagal!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ session('error') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email Field -->
        <div class="form-floating mb-3">
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   placeholder="email@example.com"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   autofocus>
            <label for="email">
                <i class="fas fa-envelope me-2"></i>Email Address
            </label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback"></div>
            @enderror
        </div>

        <!-- Password Field -->
        <div class="form-floating password-field mb-3">
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   placeholder="Password"
                   required
                   autocomplete="current-password">
            <label for="password">
                <i class="fas fa-lock me-2"></i>Password
            </label>
            <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordIcon')">
                <i class="fas fa-eye" id="passwordIcon"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback"></div>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    <small>Ingat saya</small>
                </label>
            </div>
            <a href="{{ route('password.request') }}" class="form-link">
                <small>Lupa password?</small>
            </a>
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="btn btn-primary btn-auth w-100 mb-3"
                id="loginButton">
            <span class="btn-text">
                <i class="fas fa-sign-in-alt me-2"></i>
                Masuk ke Akun
            </span>
            <span class="btn-loading-spinner d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Memproses...
            </span>
        </button>

        <!-- Divider -->
        <div class="divider">
            <span>atau masuk dengan</span>
        </div>

        <!-- Social Login -->
        <div class="social-login">
            <a href="#" class="btn-social btn-google" onclick="socialLogin('google')">
                <i class="fab fa-google"></i>
                Masuk dengan Google
            </a>
            <a href="#" class="btn-social btn-facebook" onclick="socialLogin('facebook')">
                <i class="fab fa-facebook-f"></i>
                Masuk dengan Facebook
            </a>
        </div>

        <!-- Quick Actions for Demo -->
        @if(app()->environment('local'))
            <div class="mt-3">
                <small class="text-muted d-block mb-2">Quick Login (Demo):</small>
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="quickLogin('admin')">
                            <i class="fas fa-user-shield"></i> Admin
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="quickLogin('customer')">
                            <i class="fas fa-user"></i> Customer
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
@endsection

@section('form_footer')
    <p>Belum punya akun?
        <a href="{{ route('register') }}" class="form-link">Daftar sekarang</a>
    </p>

    <div class="mt-3">
        <small class="text-muted">
            Dengan masuk, Anda menyetujui
            <a href="{{ route('terms') }}" class="form-link">Syarat & Ketentuan</a>
            dan
            <a href="{{ route('privacy') }}" class="form-link">Kebijakan Privasi</a>
            kami.
        </small>
    </div>
@endsection

@push('scripts')
<script>
    // Enhanced form submission with better error handling
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const submitButton = document.getElementById('loginButton');
        const buttonText = submitButton.querySelector('.btn-text');
        const loadingSpinner = submitButton.querySelector('.btn-loading-spinner');

        // Validate form before submission
        if (!validateLoginForm()) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        submitButton.disabled = true;
        buttonText.classList.add('d-none');
        loadingSpinner.classList.remove('d-none');

        // Log submission attempt
        console.log('Form submitted:', {
            email: document.getElementById('email').value,
            timestamp: new Date().toISOString()
        });

        // Reset button state after timeout (fallback)
        setTimeout(() => {
            submitButton.disabled = false;
            buttonText.classList.remove('d-none');
            loadingSpinner.classList.add('d-none');
        }, 10000);
    });

    // Quick login for demo purposes
    function quickLogin(type) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        if (type === 'admin') {
            emailInput.value = 'admin@tokosaya.com';
            passwordInput.value = 'admin123';
        } else if (type === 'customer') {
            emailInput.value = 'customer@example.com';
            passwordInput.value = 'customer123';
        }

        // Clear any previous errors
        clearAllErrors();

        // Trigger form submission
        document.getElementById('loginForm').submit();
    }

    // Enhanced form validation
    function validateLoginForm() {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        let isValid = true;

        // Clear previous errors
        clearAllErrors();

        // Email validation
        if (!email) {
            showFieldError('email', 'Email wajib diisi');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showFieldError('email', 'Format email tidak valid');
            isValid = false;
        }

        // Password validation
        if (!password) {
            showFieldError('password', 'Password wajib diisi');
            isValid = false;
        } else if (password.length < 6) {
            showFieldError('password', 'Password minimal 6 karakter');
            isValid = false;
        }

        return isValid;
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const feedback = field.parentNode.querySelector('.invalid-feedback');

        field.classList.add('is-invalid');
        if (feedback) {
            feedback.textContent = message;
            feedback.style.display = 'block';
        }
    }

    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const feedback = field.parentNode.querySelector('.invalid-feedback');

        field.classList.remove('is-invalid');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    function clearAllErrors() {
        ['email', 'password'].forEach(fieldId => {
            clearFieldError(fieldId);
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Password toggle functionality
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const passwordIcon = document.getElementById(iconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }

    // Real-time validation
    document.getElementById('email').addEventListener('input', function() {
        const email = this.value.trim();
        if (email && isValidEmail(email)) {
            clearFieldError('email');
        }
    });

    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        if (password && password.length >= 6) {
            clearFieldError('password');
        }
    });

    // Social login placeholder
    function socialLogin(provider) {
        alert(`${provider} login akan segera tersedia!`);
    }

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 5000);
        });

        // Focus management
        const firstInput = document.querySelector('input:not([readonly]):not([disabled])');
        if (firstInput && !firstInput.value) {
            firstInput.focus();
        }
    });

    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Enter key to submit
        if (e.key === 'Enter' && !e.shiftKey) {
            const activeElement = document.activeElement;
            if (activeElement.tagName === 'INPUT') {
                e.preventDefault();
                document.getElementById('loginButton').click();
            }
        }
    });

    // Debug mode logging (only in local environment)
    @if(app()->environment('local'))
    window.addEventListener('load', function() {
        console.log('🔍 Login Debug Mode Active');
        console.log('Current URL:', window.location.href);
        console.log('CSRF Token:', document.querySelector('input[name="_token"]')?.value);
        console.log('Form Action:', document.getElementById('loginForm').action);
    });

    // Add debug info to form submission in local environment
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        console.log('🚀 Form Submission Debug:');
        console.log('Email:', document.getElementById('email').value);
        console.log('Password Length:', document.getElementById('password').value.length);
        console.log('Remember Me:', document.getElementById('remember').checked);
        console.log('Timestamp:', new Date().toISOString());
    });
    @endif

    // Rate limiting warning
    let loginAttempts = parseInt(localStorage.getItem('loginAttempts') || '0');
    const maxAttempts = 5;

    function checkRateLimit() {
        if (loginAttempts >= maxAttempts) {
            const submitButton = document.getElementById('loginButton');
            const buttonText = submitButton.querySelector('.btn-text');

            submitButton.disabled = true;
            buttonText.innerHTML = '<i class="fas fa-lock me-2"></i>Terlalu Banyak Percobaan';

            showGlobalError('Terlalu banyak percobaan login. Silakan tunggu beberapa menit.');

            setTimeout(() => {
                loginAttempts = 0;
                localStorage.removeItem('loginAttempts');
                submitButton.disabled = false;
                buttonText.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Masuk ke Akun';
                hideGlobalError();
            }, 15 * 60 * 1000); // 15 minutes
        }
    }

    function incrementLoginAttempts() {
        loginAttempts++;
        localStorage.setItem('loginAttempts', loginAttempts.toString());
        checkRateLimit();
    }

    function showGlobalError(message) {
        const existingAlert = document.querySelector('.global-error-alert');
        if (existingAlert) existingAlert.remove();

        const alertHtml = `
            <div class="alert alert-warning alert-dismissible fade show global-error-alert" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${message}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.querySelector('form').insertAdjacentHTML('beforebegin', alertHtml);
    }

    function hideGlobalError() {
        const errorAlert = document.querySelector('.global-error-alert');
        if (errorAlert) errorAlert.remove();
    }

    // Check rate limit on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkRateLimit();
    });

    // Monitor for failed login attempts
    @if($errors->any())
        incrementLoginAttempts();
    @endif

    // Clear attempts on successful login
    @if(session('success'))
        localStorage.removeItem('loginAttempts');
    @endif
</script>
@endpush

@push('styles')
<style>
    /* Enhanced alert styling */
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .alert-success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }

    /* Enhanced button loading state */
    .btn-loading-spinner {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Form field enhancements */
    .form-floating > .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
    }

    .form-floating > .form-control.is-invalid:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
    }

    /* Password toggle styling */
    .password-field {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        z-index: 10;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.2s ease;
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .password-toggle:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Social login enhancements */
    .btn-social {
        transition: all 0.3s ease;
        border-radius: 10px;
        font-weight: 500;
        text-decoration: none;
        padding: 12px 16px;
        border: 2px solid transparent;
    }

    .btn-social:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    .btn-google {
        background: #fff;
        color: #4285f4;
        border-color: #dadce0;
    }

    .btn-google:hover {
        background: #f8f9fa;
        color: #4285f4;
        border-color: #4285f4;
    }

    .btn-facebook {
        background: #1877f2;
        color: white;
    }

    .btn-facebook:hover {
        background: #166fe5;
        color: white;
    }

    /* Demo buttons */
    .btn-outline-primary.btn-sm {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-outline-primary.btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
    }

    /* Enhanced divider */
    .divider {
        position: relative;
        text-align: center;
        margin: 1.5rem 0;
    }

    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    }

    .divider span {
        background: white;
        padding: 0 1rem;
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        .btn-social:hover,
        .btn-outline-primary.btn-sm:hover {
            transform: none;
        }

        .alert {
            transition: none;
        }
    }

    /* High contrast mode */
    @media (prefers-contrast: high) {
        .form-control {
            border-width: 2px;
        }

        .btn {
            border-width: 2px;
        }

        .alert {
            border: 2px solid currentColor;
        }
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .alert {
            font-size: 0.9rem;
        }

        .btn-social {
            font-size: 0.9rem;
            padding: 10px 14px;
        }

        .password-toggle {
            right: 10px;
        }
    }

    /* Loading animation for the spinner */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .spinner-border {
        animation: spin 1s linear infinite;
    }

    /* Focus states for better accessibility */
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
    }

    .form-link:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
        border-radius: 4px;
    }

    /* Invalid feedback styling */
    .invalid-feedback {
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    /* Enhanced form floating labels */
    .form-floating > label {
        color: #6b7280;
        font-weight: 500;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--primary-color);
    }
</style>
@endpush
