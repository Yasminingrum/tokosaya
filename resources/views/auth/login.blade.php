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
    <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email Field -->
        <div class="form-floating">
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
        <div class="form-floating password-field">
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
                id="loginButton"
                onclick="return submitForm('loginForm', 'loginButton')">
            <i class="fas fa-sign-in-alt me-2"></i>
            Masuk ke Akun
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

        // Trigger form submission
        document.getElementById('loginForm').submit();
    }

    // Enhanced form validation for login
    function validateLoginForm() {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        let isValid = true;

        // Email validation
        if (!email) {
            showFieldError('email', 'Email wajib diisi');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showFieldError('email', 'Format email tidak valid');
            isValid = false;
        } else {
            clearFieldError('email');
        }

        // Password validation
        if (!password) {
            showFieldError('password', 'Password wajib diisi');
            isValid = false;
        } else if (password.length < 6) {
            showFieldError('password', 'Password minimal 6 karakter');
            isValid = false;
        } else {
            clearFieldError('password');
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

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Override the main submit function for login-specific validation
    function submitForm(formId, buttonId) {
        if (!validateLoginForm()) {
            return false;
        }

        const button = document.getElementById(buttonId);
        const originalText = button.innerHTML;

        // Show loading state
        button.classList.add('btn-loading');
        button.disabled = true;

        // Restore button state after 10 seconds (fallback)
        setTimeout(() => {
            button.classList.remove('btn-loading');
            button.disabled = false;
            button.innerHTML = originalText;
        }, 10000);

        return true;
    }

    // Auto-fill detection and styling
    document.addEventListener('DOMContentLoaded', function() {
        // Handle browser autofill
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            // Check for autofill on load
            setTimeout(() => {
                if (input.value) {
                    input.parentNode.classList.add('has-value');
                }
            }, 100);

            // Handle autofill changes
            input.addEventListener('animationstart', function(e) {
                if (e.animationName === 'onAutoFillStart') {
                    input.parentNode.classList.add('has-value');
                }
            });

            input.addEventListener('input', function() {
                if (input.value) {
                    input.parentNode.classList.add('has-value');
                } else {
                    input.parentNode.classList.remove('has-value');
                }
            });
        });

        // Focus first empty input
        const firstEmptyInput = document.querySelector('input:not([value]):not([readonly]):not([disabled])');
        if (firstEmptyInput) {
            firstEmptyInput.focus();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter to submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('loginButton').click();
        }

        // Tab navigation enhancements
        if (e.key === 'Tab') {
            const focusableElements = document.querySelectorAll('input, button, a[href]');
            const currentIndex = Array.from(focusableElements).indexOf(document.activeElement);

            if (e.shiftKey && currentIndex === 0) {
                e.preventDefault();
                focusableElements[focusableElements.length - 1].focus();
            } else if (!e.shiftKey && currentIndex === focusableElements.length - 1) {
                e.preventDefault();
                focusableElements[0].focus();
            }
        }
    });

    // Track login attempts for security
    let loginAttempts = parseInt(localStorage.getItem('loginAttempts') || '0');
    const maxAttempts = 5;
    const lockoutTime = 15 * 60 * 1000; // 15 minutes

    function checkLoginAttempts() {
        const lastAttempt = parseInt(localStorage.getItem('lastLoginAttempt') || '0');
        const now = Date.now();

        // Reset attempts after lockout time
        if (now - lastAttempt > lockoutTime) {
            loginAttempts = 0;
            localStorage.removeItem('loginAttempts');
            localStorage.removeItem('lastLoginAttempt');
        }

        if (loginAttempts >= maxAttempts) {
            const remainingTime = Math.ceil((lockoutTime - (now - lastAttempt)) / 1000 / 60);
            showNotification(`Terlalu banyak percobaan login. Coba lagi dalam ${remainingTime} menit.`, 'warning');

            const submitButton = document.getElementById('loginButton');
            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fas fa-lock me-2"></i>Akun Terkunci (${remainingTime}m)`;

            return false;
        }

        return true;
    }

    function incrementLoginAttempts() {
        loginAttempts++;
        localStorage.setItem('loginAttempts', loginAttempts.toString());
        localStorage.setItem('lastLoginAttempt', Date.now().toString());
    }

    function resetLoginAttempts() {
        loginAttempts = 0;
        localStorage.removeItem('loginAttempts');
        localStorage.removeItem('lastLoginAttempt');
    }

    // Check login attempts on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkLoginAttempts();
    });

    // Handle form submission errors
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        if (!checkLoginAttempts()) {
            e.preventDefault();
            return false;
        }
    });

    // Monitor for failed login (if there are validation errors)
    @if($errors->any())
        incrementLoginAttempts();

        // Show specific error messages
        @if($errors->has('email'))
            showFieldError('email', '{{ $errors->first('email') }}');
        @endif

        @if($errors->has('password'))
            showFieldError('password', '{{ $errors->first('password') }}');
        @endif

        // Check if we need to lock the account
        setTimeout(() => {
            checkLoginAttempts();
        }, 100);
    @endif

    // Reset attempts on successful login redirect
    @if(session('success'))
        resetLoginAttempts();
    @endif

    // Add smooth transitions
    const style = document.createElement('style');
    style.textContent = `
        .form-floating.has-value label {
            opacity: 0.7;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px white inset;
            -webkit-text-fill-color: #1e293b;
            transition: background-color 5000s ease-in-out 0s;
        }

        @keyframes onAutoFillStart {
            from { opacity: 1; }
            to { opacity: 1; }
        }

        input:-webkit-autofill {
            animation-name: onAutoFillStart;
            animation-duration: 0.001s;
        }

        .btn-loading {
            pointer-events: none;
        }

        .password-toggle {
            transition: color 0.2s ease;
        }

        .form-control:focus + .password-toggle {
            color: var(--primary-color);
        }
    `;
    document.head.appendChild(style);
</script>
@endpush

@push('styles')
<style>
    /* Login-specific styles */
    .auth-visual {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .auth-features {
        text-align: left;
        max-width: 280px;
    }

    .auth-feature {
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .auth-feature i {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
    }

    /* Demo buttons styling */
    .btn-outline-primary.btn-sm {
        font-size: 0.75rem;
        padding: 0.375rem 0.5rem;
        border-radius: 8px;
    }

    .btn-outline-primary.btn-sm i {
        font-size: 0.7rem;
        margin-right: 0.25rem;
    }

    /* Enhanced form styling */
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
    }

    /* Loading state improvements */
    .btn-loading::after {
        width: 14px;
        height: 14px;
        border-width: 2px;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .auth-form {
            padding: 1.5rem;
        }

        .auth-title {
            font-size: 1.5rem;
        }

        .social-login {
            margin: 1rem 0;
        }

        .btn-social {
            font-size: 0.9rem;
            height: 44px;
        }
    }

    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        .auth-card {
            animation: none;
        }

        .btn-primary:hover {
            transform: none;
        }

        .btn-outline-primary:hover {
            transform: none;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .form-control {
            border-width: 2px;
        }

        .btn-primary {
            border-width: 2px;
        }

        .form-link {
            text-decoration: underline;
        }
    }
</style>
@endpush
