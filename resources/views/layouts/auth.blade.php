<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Authentication') - TokoSaya</title>
    <meta name="description" content="@yield('description', 'Login atau daftar di TokoSaya untuk pengalaman belanja yang lebih personal')">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --font-sans: 'Inter', 'Segoe UI', 'Roboto', sans-serif;
            --font-display: 'Poppins', 'Inter', sans-serif;
            --auth-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            font-family: var(--font-sans);
            background: var(--auth-bg);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .font-display {
            font-family: var(--font-display);
        }

        /* Auth Container */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 0 1rem;
        }

        /* Split Layout */
        .auth-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        @media (max-width: 768px) {
            .auth-split {
                grid-template-columns: 1fr;
            }

            .auth-visual {
                display: none;
            }
        }

        /* Visual Side */
        .auth-visual {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 3rem;
        }

        .auth-visual::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .auth-visual-content {
            position: relative;
            z-index: 1;
        }

        .auth-visual h2 {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .auth-visual p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .auth-features {
            text-align: left;
            max-width: 300px;
        }

        .auth-feature {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .auth-feature i {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        /* Form Side */
        .auth-form {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .auth-form {
                padding: 2rem;
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            display: inline-flex;
            align-items: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--primary-color);
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .auth-logo i {
            margin-right: 0.5rem;
        }

        .auth-title {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 1.75rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Form Styles */
        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating > .form-control {
            height: 58px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .form-floating > label {
            color: #64748b;
            font-weight: 500;
        }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            z-index: 5;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        /* Button Styles */
        .btn-auth {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        /* Social Login */
        .social-login {
            margin: 1.5rem 0;
        }

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
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            color: #64748b;
            padding: 0 1rem;
            font-size: 0.875rem;
        }

        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 0.75rem;
        }

        .btn-social:hover {
            border-color: #d1d5db;
            background: #f9fafb;
            color: #374151;
            transform: translateY(-1px);
        }

        .btn-social i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .btn-google:hover {
            border-color: #ea4335;
            background: #ea4335;
            color: white;
        }

        .btn-facebook:hover {
            border-color: #1877f2;
            background: #1877f2;
            color: white;
        }

        /* Form Links */
        .form-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .form-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        /* Validation Styles */
        .is-invalid {
            border-color: var(--danger-color) !important;
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Loading State */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 16px;
            height: 16px;
            border: 2px solid currentColor;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Animations */
        .auth-card {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Password Strength Indicator */
        .password-strength {
            margin-top: 0.5rem;
        }

        .strength-bar {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak .strength-fill {
            width: 33%;
            background: var(--danger-color);
        }

        .strength-medium .strength-fill {
            width: 66%;
            background: var(--warning-color);
        }

        .strength-strong .strength-fill {
            width: 100%;
            background: var(--success-color);
        }

        .strength-text {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .strength-weak .strength-text {
            color: var(--danger-color);
        }

        .strength-medium .strength-text {
            color: var(--warning-color);
        }

        .strength-strong .strength-text {
            color: var(--success-color);
        }

        /* Terms and Privacy */
        .terms-text {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.5;
        }

        .terms-text a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .terms-text a:hover {
            text-decoration: underline;
        }

        /* Success Messages */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #047857;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background: rgba(37, 99, 235, 0.1);
            color: #1d4ed8;
            border-left: 4px solid var(--primary-color);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-split">
                <!-- Visual Side -->
                <div class="auth-visual">
                    <div class="auth-visual-content">
                        @yield('visual_content')
                    </div>
                </div>

                <!-- Form Side -->
                <div class="auth-form">
                    <!-- Header -->
                    <div class="auth-header">
                        <a href="{{ route('home') }}" class="auth-logo">
                            <i class="fas fa-store"></i>
                            TokoSaya
                        </a>

                        <h1 class="auth-title">@yield('form_title')</h1>
                        <p class="auth-subtitle">@yield('form_subtitle')</p>
                    </div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

                    <!-- Form Content -->
                    @yield('form_content')

                    <!-- Footer -->
                    <div class="form-footer">
                        @yield('form_footer')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom JS -->
    @vite(['resources/js/app.js'])

    <script>
        // Password toggle functionality
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = [];

            // Length check
            if (password.length >= 8) {
                strength += 1;
            } else {
                feedback.push('Minimal 8 karakter');
            }

            // Lowercase check
            if (/[a-z]/.test(password)) {
                strength += 1;
            } else {
                feedback.push('Huruf kecil');
            }

            // Uppercase check
            if (/[A-Z]/.test(password)) {
                strength += 1;
            } else {
                feedback.push('Huruf besar');
            }

            // Number check
            if (/[0-9]/.test(password)) {
                strength += 1;
            } else {
                feedback.push('Angka');
            }

            // Special character check
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 1;
            } else {
                feedback.push('Karakter khusus');
            }

            return { strength, feedback };
        }

        // Update password strength indicator
        function updatePasswordStrength(inputId, indicatorId) {
            const input = document.getElementById(inputId);
            const indicator = document.getElementById(indicatorId);

            if (!input || !indicator) return;

            const password = input.value;
            const { strength, feedback } = checkPasswordStrength(password);

            // Remove existing classes
            indicator.classList.remove('strength-weak', 'strength-medium', 'strength-strong');

            if (password.length === 0) {
                indicator.style.display = 'none';
                return;
            }

            indicator.style.display = 'block';

            let strengthClass = '';
            let strengthText = '';

            if (strength < 3) {
                strengthClass = 'strength-weak';
                strengthText = 'Lemah';
            } else if (strength < 5) {
                strengthClass = 'strength-medium';
                strengthText = 'Sedang';
            } else {
                strengthClass = 'strength-strong';
                strengthText = 'Kuat';
            }

            indicator.classList.add(strengthClass);
            indicator.querySelector('.strength-text').textContent = strengthText;

            if (feedback.length > 0) {
                indicator.querySelector('.strength-text').textContent += ` (Perlu: ${feedback.join(', ')})`;
            }
        }

        // Form validation
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                const value = input.value.trim();
                const feedback = input.parentNode.querySelector('.invalid-feedback');

                // Remove existing validation classes
                input.classList.remove('is-invalid');
                if (feedback) feedback.style.display = 'none';

                // Check if empty
                if (!value) {
                    input.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Field ini wajib diisi';
                        feedback.style.display = 'block';
                    }
                    isValid = false;
                    return;
                }

                // Email validation
                if (input.type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Format email tidak valid';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                }

                // Password validation
                if (input.type === 'password' && input.name === 'password') {
                    const { strength } = checkPasswordStrength(value);
                    if (strength < 3) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Password terlalu lemah';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                }

                // Password confirmation
                if (input.name === 'password_confirmation') {
                    const passwordInput = form.querySelector('input[name="password"]');
                    if (passwordInput && value !== passwordInput.value) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Konfirmasi password tidak sama';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                }

                // Phone validation (Indonesian format)
                if (input.name === 'phone') {
                    const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
                    if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Format nomor telepon tidak valid';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                }
            });

            return isValid;
        }

        // Submit form with loading state
        function submitForm(formId, buttonId) {
            if (!validateForm(formId)) {
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

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Social login handlers
        async function socialLogin(provider) {
            try {
                window.location.href = `/auth/${provider}/redirect`;
            } catch (error) {
                console.error('Social login error:', error);
                showNotification('Gagal melakukan login dengan ' + provider, 'error');
            }
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const alertClass = `alert-${type}`;
            const iconClass = type === 'success' ? 'fa-check-circle' :
                            type === 'error' ? 'fa-exclamation-circle' :
                            type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

            const alertHtml = `
                <div class="alert ${alertClass} position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;" role="alert">
                    <i class="fas ${iconClass} me-2"></i>${message}
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', alertHtml);

            // Auto remove after 5 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert:last-of-type');
                if (alert) {
                    alert.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        }

        // Real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            // Add real-time validation to all form inputs
            const inputs = document.querySelectorAll('input[required]');

            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    validateSingleInput(input);
                });

                input.addEventListener('input', () => {
                    // Remove error state on input
                    input.classList.remove('is-invalid');
                    const feedback = input.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.style.display = 'none';
                });

                // Password strength indicator
                if (input.type === 'password' && input.name === 'password') {
                    input.addEventListener('input', () => {
                        updatePasswordStrength(input.id, input.id + '_strength');
                    });
                }
            });
        });

        function validateSingleInput(input) {
            const value = input.value.trim();
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            // Remove existing validation classes
            input.classList.remove('is-invalid');
            if (feedback) feedback.style.display = 'none';

            // Check if empty
            if (!value) {
                input.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Field ini wajib diisi';
                    feedback.style.display = 'block';
                }
                return false;
            }

            // Email validation
            if (input.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    input.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Format email tidak valid';
                        feedback.style.display = 'block';
                    }
                    return false;
                }
            }

            return true;
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter key to submit form
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                const form = e.target.closest('form');
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    e.preventDefault();
                    submitButton.click();
                }
            }
        });

        // Back to home link for mobile
        function goHome() {
            window.location.href = '{{ route("home") }}';
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

    @stack('scripts')
</body>
</html>
