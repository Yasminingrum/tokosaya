@extends('layouts.auth')

@section('title', 'Masuk ke Akun - TokoSaya')
@section('description', 'Masuk ke akun TokoSaya Anda untuk pengalaman belanja yang lebih personal')

@section('visual_content')
    <div class="auth-visual" data-aos="fade-right">
        <div class="auth-hero-content">
            <h2 class="auth-hero-title">Selamat Datang Kembali!</h2>
            <p class="auth-hero-subtitle">Masuk ke akun Anda dan nikmati pengalaman belanja yang tak terlupakan.</p>

            <div class="auth-features">
                @foreach([
                    ['icon' => 'shopping-cart', 'text' => 'Keranjang tersimpan otomatis'],
                    ['icon' => 'heart', 'text' => 'Wishlist produk favorit'],
                    ['icon' => 'truck', 'text' => 'Lacak status pengiriman'],
                    ['icon' => 'star', 'text' => 'Review dan rating produk'],
                    ['icon' => 'gift', 'text' => 'Promo eksklusif member'],
                    ['icon' => 'headset', 'text' => 'Customer service 24/7']
                ] as $feature)
                <div class="auth-feature" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="feature-icon">
                        <i class="fas fa-{{ $feature['icon'] }}"></i>
                    </div>
                    <span>{{ $feature['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('form_content')
    <div class="auth-form-container" data-aos="fade-left">
        <!-- Header -->
        <div class="auth-form-header">
            <h1 class="auth-form-title">Masuk ke Akun</h1>
            <p class="auth-form-subtitle">Gunakan email dan password untuk mengakses akun Anda</p>
        </div>

        <!-- Alerts -->
        @if ($errors->any() || session('error') || session('success'))
            <div class="alerts-container">
                @if ($errors->any())
                    <div class="alert alert-error">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>Login Gagal!</strong>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>{{ session('error') }}</strong>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <div class="alert-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>{{ session('success') }}</strong>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Form -->
        <form id="loginForm" method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input @error('email') error @enderror"
                       placeholder="Masukkan email Anda"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       autofocus>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <div class="password-input-container">
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input @error('password') error @enderror"
                           placeholder="Masukkan password Anda"
                           required
                           autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember & Forgot -->
            <div class="form-row">
                <label class="checkbox-label">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                    <span class="checkbox-text">Ingat saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary btn-auth" id="loginButton">
                <span class="btn-text">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Akun
                </span>
                <span class="btn-loading" style="display: none;">
                    <div class="spinner"></div>
                    Memproses...
                </span>
            </button>

            <!-- Divider -->
            <div class="divider">
                <span>atau masuk dengan</span>
            </div>

            <!-- Social Login -->
            <div class="social-login">
                <button type="button" class="btn-social btn-google" onclick="socialLogin('google')">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button type="button" class="btn-social btn-facebook" onclick="socialLogin('facebook')">
                    <i class="fab fa-facebook-f"></i>
                    Facebook
                </button>
            </div>

            <!-- Demo Login (only in development) -->
            @if(app()->environment(['local', 'staging']))
                <div class="demo-section">
                    <div class="demo-header">
                        <i class="fas fa-code"></i>
                        <span>Login Demo</span>
                    </div>
                    <div class="demo-buttons">
                        <button type="button" class="btn-demo" onclick="quickLogin('admin')">
                            <i class="fas fa-user-shield"></i>
                            Admin
                        </button>
                        <button type="button" class="btn-demo" onclick="quickLogin('customer')">
                            <i class="fas fa-user"></i>
                            Customer
                        </button>
                    </div>
                </div>
            @endif
        </form>

        <!-- Footer -->
        <div class="auth-form-footer">
            <p>Belum punya akun?
                <a href="{{ route('register') }}" class="register-link">Daftar sekarang</a>
            </p>
            <div class="terms-links">
                <small>
                    Dengan masuk, Anda menyetujui
                    <a href="{{ route('terms') }}">Syarat & Ketentuan</a>
                    dan
                    <a href="{{ route('privacy') }}">Kebijakan Privasi</a> kami.
                </small>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Modern Auth Styles */
.auth-visual {
    padding: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

.auth-hero-content {
    max-width: 500px;
    text-align: center;
}

.auth-hero-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.auth-hero-subtitle {
    font-size: 1.125rem;
    color: #6b7280;
    margin-bottom: 3rem;
    line-height: 1.6;
}

.auth-features {
    display: grid;
    gap: 1.5rem;
    margin-top: 2rem;
}

.auth-feature {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.auth-feature:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.auth-feature span {
    font-weight: 500;
    color: #374151;
}

/* Form Container */
.auth-form-container {
    background: white;
    border-radius: 24px;
    padding: 3rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(229, 231, 235, 0.8);
    max-width: 440px;
    width: 100%;
    margin: 2rem auto;
}

.auth-form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-form-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.auth-form-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Alerts */
.alerts-container {
    margin-bottom: 1.5rem;
}

.alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16a34a;
}

.alert-icon {
    margin-top: 0.125rem;
}

.error-list {
    margin: 0.5rem 0 0 0;
    padding-left: 1rem;
}

/* Form Elements */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: #f9fafb;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input.error {
    border-color: #dc2626;
    background: #fef2f2;
}

.form-error {
    display: block;
    color: #dc2626;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    font-weight: 500;
}

/* Password Input */
.password-input-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: color 0.2s ease;
}

.password-toggle:hover {
    color: #3b82f6;
}

/* Form Row */
.form-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

/* Checkbox */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    user-select: none;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background: #3b82f6;
    border-color: #3b82f6;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    color: white;
    font-size: 12px;
    font-weight: 600;
}

.checkbox-text {
    font-size: 0.875rem;
    color: #374151;
}

.forgot-link {
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: color 0.2s ease;
}

.forgot-link:hover {
    color: #1d4ed8;
}

/* Buttons */
.btn-auth {
    width: 100%;
    padding: 0.875rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Divider */
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
    background: #e5e7eb;
}

.divider span {
    background: white;
    padding: 0 1rem;
    color: #6b7280;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

/* Social Login */
.social-login {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.btn-social {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: 2px solid;
    cursor: pointer;
    text-decoration: none;
}

.btn-google {
    background: white;
    color: #4285f4;
    border-color: #dadce0;
}

.btn-google:hover {
    background: #f8f9fa;
    border-color: #4285f4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(66, 133, 244, 0.2);
}

.btn-facebook {
    background: #1877f2;
    color: white;
    border-color: #1877f2;
}

.btn-facebook:hover {
    background: #166fe5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
}

/* Demo Section */
.demo-section {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.demo-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.75rem;
}

.demo-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.btn-demo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    color: #374151;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-demo:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
}

/* Footer */
.auth-form-footer {
    text-align: center;
    margin-top: 2rem;
}

.auth-form-footer p {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.register-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
}

.register-link:hover {
    color: #1d4ed8;
}

.terms-links {
    color: #9ca3af;
}

.terms-links a {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s ease;
}

.terms-links a:hover {
    color: #3b82f6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .auth-hero-title {
        font-size: 2rem;
    }

    .auth-form-container {
        padding: 2rem;
        margin: 1rem;
        border-radius: 16px;
    }

    .auth-features {
        gap: 1rem;
    }

    .auth-feature {
        padding: 0.75rem;
    }

    .feature-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .social-login {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .form-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .auth-hero-title {
        font-size: 1.75rem;
    }

    .auth-form-container {
        padding: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Enhanced form functionality
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');

    // Form submission
    loginForm.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        showLoadingState(loginButton);

        // Reset button after timeout (fallback)
        setTimeout(() => {
            hideLoadingState(loginButton);
        }, 10000);
    });

    // Real-time validation
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    emailInput.addEventListener('input', () => validateField(emailInput, 'email'));
    passwordInput.addEventListener('input', () => validateField(passwordInput, 'password'));

    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
});

// Form validation
function validateForm() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    let isValid = true;

    if (!email || !isValidEmail(email)) {
        showFieldError('email', 'Email tidak valid');
        isValid = false;
    } else {
        clearFieldError('email');
    }

    if (!password || password.length < 6) {
        showFieldError('password', 'Password minimal 6 karakter');
        isValid = false;
    } else {
        clearFieldError('password');
    }

    return isValid;
}

function validateField(field, type) {
    const value = field.value.trim();

    if (type === 'email') {
        if (value && isValidEmail(value)) {
            clearFieldError('email');
        }
    } else if (type === 'password') {
        if (value && value.length >= 6) {
            clearFieldError('password');
        }
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    field.classList.add('error');

    // Remove existing error
    const existingError = field.parentNode.querySelector('.form-error');
    if (existingError) existingError.remove();

    // Add new error
    const errorElement = document.createElement('span');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    field.parentNode.appendChild(errorElement);
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    field.classList.remove('error');

    const errorElement = field.parentNode.querySelector('.form-error');
    if (errorElement) errorElement.remove();
}

// Password toggle
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');

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

// Quick login for demo
function quickLogin(type) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    clearFieldError('email');
    clearFieldError('password');

    if (type === 'admin') {
        emailInput.value = 'superadmin@tokosaya.id';
        passwordInput.value = 'password123';
    } else if (type === 'customer') {
        emailInput.value = 'customer@example.com';
        passwordInput.value = 'password123';
    }

    // Auto submit
    document.getElementById('loginForm').submit();
}

// Social login
function socialLogin(provider) {
    console.log(`Login with ${provider}`);
    alert(`${provider} login akan segera tersedia!`);
}

// Loading states
function showLoadingState(button) {
    button.disabled = true;
    button.querySelector('.btn-text').style.display = 'none';
    button.querySelector('.btn-loading').style.display = 'flex';
}

function hideLoadingState(button) {
    button.disabled = false;
    button.querySelector('.btn-text').style.display = 'flex';
    button.querySelector('.btn-loading').style.display = 'none';
}

// Enhanced keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.activeElement.tagName === 'INPUT') {
        e.preventDefault();
        document.getElementById('loginButton').click();
    }
});
</script>
@endpush
