@extends('layouts.auth')

@section('title', 'Daftar Akun Baru')
@section('description', 'Bergabung dengan TokoSaya dan nikmati pengalaman belanja online terbaik di Indonesia')

@section('visual_content')
    <h2>Bergabung dengan TokoSaya!</h2>
    <p>Daftar sekarang dan dapatkan akses ke ribuan produk berkualitas dengan penawaran terbaik.</p>

    <div class="auth-features">
        @foreach([
            ['icon' => 'gift', 'text' => 'Voucher selamat datang Rp 50.000'],
            ['icon' => 'shipping-fast', 'text' => 'Gratis ongkir untuk pembelian pertama'],
            ['icon' => 'percentage', 'text' => 'Diskon eksklusif member baru'],
            ['icon' => 'star', 'text' => 'Kumpulkan poin setiap pembelian'],
            ['icon' => 'bell', 'text' => 'Notifikasi promo dan flash sale'],
            ['icon' => 'shield-alt', 'text' => 'Jaminan uang kembali 100%']
        ] as $feature)
            <div class="auth-feature">
                <i class="fas fa-{{ $feature['icon'] }}"></i>
                <span>{{ $feature['text'] }}</span>
            </div>
        @endforeach
    </div>
@endsection

@section('form_title', 'Buat Akun Baru')
@section('form_subtitle', 'Isi informasi di bawah untuk membuat akun TokoSaya')

@section('form_content')
    <form id="registerForm" method="POST" action="{{ route('register') }}" novalidate x-data="registerComponent()">
        @csrf

        <!-- Step Indicator -->
        <div class="step-indicator mb-4">
            @foreach([
                ['number' => 1, 'label' => 'Info Dasar'],
                ['number' => 2, 'label' => 'Akun'],
                ['number' => 3, 'label' => 'Verifikasi']
            ] as $step)
                <div class="step" :class="{ 'active': step === {{ $step['number'] }}, 'completed': step > {{ $step['number'] }} }">
                    <div class="step-number">{{ $step['number'] }}</div>
                    <div class="step-label">{{ $step['label'] }}</div>
                </div>
                @if(!$loop->last)
                    <div class="step-line" :class="{ 'completed': step > {{ $step['number'] }} }"></div>
                @endif
            @endforeach
        </div>

        <!-- Step 1: Personal Information -->
        <div x-show="step === 1" x-transition.opacity>
            <div class="row">
                <div class="col-md-6">
                    @include('components.form.input', [
                        'type' => 'text',
                        'name' => 'first_name',
                        'placeholder' => 'Nama Depan',
                        'icon' => 'user',
                        'model' => 'firstName',
                        'required' => true,
                        'autocomplete' => 'given-name',
                        'autofocus' => true
                    ])
                </div>
                <div class="col-md-6">
                    @include('components.form.input', [
                        'type' => 'text',
                        'name' => 'last_name',
                        'placeholder' => 'Nama Belakang',
                        'icon' => 'user',
                        'model' => 'lastName',
                        'required' => true,
                        'autocomplete' => 'family-name'
                    ])
                </div>
            </div>

            @include('components.form.input', [
                'type' => 'tel',
                'name' => 'phone',
                'placeholder' => '08123456789',
                'icon' => 'phone',
                'model' => 'phone',
                'required' => true,
                'autocomplete' => 'tel',
                'hint' => 'Format: 08123456789 atau +628123456789'
            ])

            @include('components.form.input', [
                'type' => 'date',
                'name' => 'date_of_birth',
                'icon' => 'calendar',
                'model' => 'dateOfBirth',
                'max' => date('Y-m-d', strtotime('-13 years')),
                'hint' => 'Minimal umur 13 tahun'
            ])

            @include('components.form.select', [
                'name' => 'gender',
                'icon' => 'venus-mars',
                'model' => 'gender',
                'options' => [
                    '' => 'Pilih Jenis Kelamin',
                    'M' => 'Laki-laki',
                    'F' => 'Perempuan',
                    'O' => 'Lainnya'
                ]
            ])

            <button type="button" class="btn btn-primary btn-auth w-100" @click="nextStep()" :disabled="!isStep1Valid()">
                Lanjut ke Langkah 2 <i class="fas fa-arrow-right ms-2"></i>
            </button>

            <!-- Social Registration -->
            <div class="mt-4">
                <div class="divider">
                    <span>atau daftar dengan</span>
                </div>

                <div class="social-login">
                    <a href="#" class="btn-social btn-google" onclick="socialLogin('google')">
                        <i class="fab fa-google"></i> Daftar dengan Google
                    </a>
                    <a href="#" class="btn-social btn-facebook" onclick="socialLogin('facebook')">
                        <i class="fab fa-facebook-f"></i> Daftar dengan Facebook
                    </a>
                </div>
            </div>
        </div>

        <!-- Step 2: Account Information -->
        <div x-show="step === 2" x-transition.opacity>
            @include('components.form.input', [
                'type' => 'text',
                'name' => 'username',
                'placeholder' => 'username',
                'icon' => 'at',
                'model' => 'username',
                'required' => true,
                'autocomplete' => 'username',
                'events' => [
                    'input' => 'checkUsername()',
                    'blur' => 'validateUsername()'
                ],
                'status' => 'usernameStatus',
                'statusMessages' => [
                    'checking' => 'Mengecek ketersediaan...',
                    'available' => 'Username tersedia',
                    'taken' => 'Username sudah digunakan',
                    'invalid' => 'Format username tidak valid'
                ]
            ])

            @include('components.form.input', [
                'type' => 'email',
                'name' => 'email',
                'placeholder' => 'email@example.com',
                'icon' => 'envelope',
                'model' => 'email',
                'required' => true,
                'autocomplete' => 'email',
                'events' => [
                    'input' => 'checkEmail()',
                    'blur' => 'validateEmail()'
                ],
                'status' => 'emailStatus',
                'statusMessages' => [
                    'checking' => 'Mengecek ketersediaan...',
                    'available' => 'Email tersedia',
                    'taken' => 'Email sudah terdaftar'
                ]
            ])

            @include('components.form.password', [
                'name' => 'password',
                'placeholder' => 'Password',
                'icon' => 'lock',
                'model' => 'password',
                'required' => true,
                'autocomplete' => 'new-password',
                'events' => [
                    'input' => 'updatePasswordStrength()'
                ],
                'strengthIndicator' => true
            ])

            @include('components.form.password', [
                'name' => 'password_confirmation',
                'placeholder' => 'Konfirmasi Password',
                'icon' => 'lock',
                'model' => 'passwordConfirmation',
                'required' => true,
                'autocomplete' => 'new-password',
                'events' => [
                    'input' => 'validatePasswordConfirmation()'
                ],
                'matchIndicator' => true
            ])

            <div class="row g-2">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-primary btn-auth w-100" @click="prevStep()">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-primary btn-auth w-100" @click="nextStep()" :disabled="!isStep2Valid()">
                        Lanjut <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Terms and Submit -->
        <div x-show="step === 3" x-transition.opacity>
            <div class="terms-section mb-4">
                <h6 class="mb-3">Syarat dan Ketentuan</h6>
                <div class="terms-content p-3 border rounded">
                    @include('components.form.checkbox', [
                        'name' => 'terms',
                        'label' => 'Saya menyetujui <a href="'.route('terms').'" target="_blank" class="form-link">Syarat & Ketentuan</a> TokoSaya',
                        'model' => 'agreeTerms',
                        'required' => true
                    ])

                    @include('components.form.checkbox', [
                        'name' => 'privacy',
                        'label' => 'Saya menyetujui <a href="'.route('privacy').'" target="_blank" class="form-link">Kebijakan Privasi</a> TokoSaya',
                        'model' => 'agreePrivacy',
                        'required' => true
                    ])

                    @include('components.form.checkbox', [
                        'name' => 'newsletter',
                        'label' => 'Saya ingin menerima newsletter dan penawaran khusus (opsional)',
                        'model' => 'subscribeNewsletter'
                    ])
                </div>
            </div>

            <div class="registration-summary p-3 bg-light rounded mb-4">
                <h6 class="mb-2">Ringkasan Pendaftaran</h6>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Nama:</small><br>
                        <span x-text="firstName + ' ' + lastName"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Email:</small><br>
                        <span x-text="email"></span>
                    </div>
                </div>
                <hr class="my-2">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Username:</small><br>
                        <span x-text="username"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Telepon:</small><br>
                        <span x-text="phone"></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-auth w-100 mb-3" id="registerButton"
                    :disabled="!isStep3Valid()" onclick="return submitForm('registerForm', 'registerButton')">
                <i class="fas fa-user-plus me-2"></i> Buat Akun Sekarang
            </button>

            <button type="button" class="btn btn-outline-primary btn-auth w-100" @click="prevStep()">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Langkah 2
            </button>
        </div>
    </form>
@endsection

@section('form_footer')
    <p>Sudah punya akun? <a href="{{ route('login') }}" class="form-link">Masuk di sini</a></p>
@endsection

@push('styles')
<style>
    /* Step Indicator Styles */
    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .step.active .step-number {
        background: var(--primary-color);
        color: white;
    }

    .step.completed .step-number {
        background: var(--success-color);
        color: white;
    }

    .step-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
    }

    .step.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }

    .step.completed .step-label {
        color: var(--success-color);
    }

    .step-line {
        width: 40px;
        height: 2px;
        background: #e2e8f0;
        margin: 0 1rem;
        transition: background-color 0.3s ease;
    }

    .step-line.completed {
        background: var(--success-color);
    }

    /* Form Enhancements */
    .form-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .terms-content {
        max-height: 200px;
        overflow-y: auto;
        background: #f8fafc;
    }

    .registration-summary {
        border: 1px solid #e2e8f0;
    }

    .registration-summary h6 {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 0.5rem;
        margin-bottom: 1rem;
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

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .step-indicator {
            font-size: 0.8rem;
        }

        .step-number {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .step-line {
            width: 30px;
            margin: 0 0.5rem;
        }

        .registration-summary {
            font-size: 0.85rem;
        }
    }

    /* Animation improvements */
    [x-cloak] {
        display: none !important;
    }

    .form-floating {
        margin-bottom: 1.5rem;
    }

    .btn-auth {
        transition: all 0.3s ease;
    }

    .btn-auth:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    function registerComponent() {
        return {
            step: 1,

            // Form data
            firstName: '{{ old("first_name", "") }}',
            lastName: '{{ old("last_name", "") }}',
            phone: '{{ old("phone", "") }}',
            dateOfBirth: '{{ old("date_of_birth", "") }}',
            gender: '{{ old("gender", "") }}',
            username: '{{ old("username", "") }}',
            email: '{{ old("email", "") }}',
            password: '',
            passwordConfirmation: '',
            agreeTerms: {{ old('terms') ? 'true' : 'false' }},
            agreePrivacy: {{ old('privacy') ? 'true' : 'false' }},
            subscribeNewsletter: {{ old('newsletter') ? 'true' : 'false' }},

            // Validation states
            usernameStatus: '',
            emailStatus: '',
            passwordStrength: 0,
            strengthClass: '',
            strengthText: '',

            // Computed properties
            get passwordsMatch() {
                return this.password === this.passwordConfirmation && this.passwordConfirmation.length > 0;
            },

            // Step validation methods
            isStep1Valid() {
                return this.firstName.length > 0 &&
                       this.lastName.length > 0 &&
                       this.phone.length > 0 &&
                       this.isValidPhone(this.phone);
            },

            isStep2Valid() {
                return this.username.length > 0 &&
                       this.usernameStatus === 'available' &&
                       this.email.length > 0 &&
                       this.emailStatus === 'available' &&
                       this.password.length >= 8 &&
                       this.passwordStrength >= 3 &&
                       this.passwordsMatch;
            },

            isStep3Valid() {
                return this.agreeTerms && this.agreePrivacy;
            },

            // Navigation methods
            nextStep() {
                if (this.step < 3) {
                    this.step++;
                }
            },

            prevStep() {
                if (this.step > 1) {
                    this.step--;
                }
            },

            // Validation methods
            isValidPhone(phone) {
                const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
                return phoneRegex.test(phone.replace(/\s/g, ''));
            },

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            isValidUsername(username) {
                const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
                return usernameRegex.test(username);
            },

            // API calls
            async checkUsername() {
                if (this.username.length < 3) {
                    this.usernameStatus = '';
                    return;
                }

                if (!this.isValidUsername(this.username)) {
                    this.usernameStatus = 'invalid';
                    return;
                }

                this.usernameStatus = 'checking';

                try {
                    const response = await fetch('/api/check-username', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ username: this.username })
                    });

                    const data = await response.json();
                    this.usernameStatus = data.available ? 'available' : 'taken';
                } catch (error) {
                    console.error('Username check error:', error);
                    this.usernameStatus = '';
                }
            },

            async checkEmail() {
                if (this.email.length === 0) {
                    this.emailStatus = '';
                    return;
                }

                if (!this.isValidEmail(this.email)) {
                    this.emailStatus = '';
                    return;
                }

                this.emailStatus = 'checking';

                try {
                    const response = await fetch('/api/check-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email: this.email })
                    });

                    const data = await response.json();
                    this.emailStatus = data.available ? 'available' : 'taken';
                } catch (error) {
                    console.error('Email check error:', error);
                    this.emailStatus = '';
                }
            },

            updatePasswordStrength() {
                const password = this.password;
                let strength = 0;
                let feedback = [];

                // Length check
                if (password.length >= 8) strength++;
                else feedback.push('Minimal 8 karakter');

                // Lowercase check
                if (/[a-z]/.test(password)) strength++;
                else feedback.push('Huruf kecil');

                // Uppercase check
                if (/[A-Z]/.test(password)) strength++;
                else feedback.push('Huruf besar');

                // Number check
                if (/[0-9]/.test(password)) strength++;
                else feedback.push('Angka');

                // Special character check
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                else feedback.push('Karakter khusus');

                this.passwordStrength = strength;

                if (strength < 3) {
                    this.strengthClass = 'strength-weak';
                    this.strengthText = 'Lemah';
                } else if (strength < 5) {
                    this.strengthClass = 'strength-medium';
                    this.strengthText = 'Sedang';
                } else {
                    this.strengthClass = 'strength-strong';
                    this.strengthText = 'Kuat';
                }

                if (feedback.length > 0 && password.length > 0) {
                    this.strengthText += ` (Perlu: ${feedback.join(', ')})`;
                }
            },

            validateUsername() {
                const usernameInput = document.getElementById('username');
                const feedback = usernameInput.parentNode.querySelector('.invalid-feedback');

                usernameInput.classList.remove('is-invalid');
                if (feedback) feedback.style.display = 'none';

                if (this.username.length === 0) {
                    usernameInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Username wajib diisi';
                        feedback.style.display = 'block';
                    }
                } else if (!this.isValidUsername(this.username)) {
                    usernameInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Username hanya boleh mengandung huruf, angka, dan underscore (3-20 karakter)';
                        feedback.style.display = 'block';
                    }
                } else if (this.usernameStatus === 'taken') {
                    usernameInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Username sudah digunakan';
                        feedback.style.display = 'block';
                    }
                }
            },

            validateEmail() {
                const emailInput = document.getElementById('email');
                const feedback = emailInput.parentNode.querySelector('.invalid-feedback');

                emailInput.classList.remove('is-invalid');
                if (feedback) feedback.style.display = 'none';

                if (this.email.length === 0) {
                    emailInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Email wajib diisi';
                        feedback.style.display = 'block';
                    }
                } else if (!this.isValidEmail(this.email)) {
                    emailInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Format email tidak valid';
                        feedback.style.display = 'block';
                    }
                } else if (this.emailStatus === 'taken') {
                    emailInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Email sudah terdaftar';
                        feedback.style.display = 'block';
                    }
                }
            },

            validatePasswordConfirmation() {
                const confirmInput = document.getElementById('password_confirmation');
                const feedback = confirmInput.parentNode.querySelector('.invalid-feedback');

                confirmInput.classList.remove('is-invalid');
                if (feedback) feedback.style.display = 'none';

                if (this.passwordConfirmation.length > 0 && !this.passwordsMatch) {
                    confirmInput.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = 'Konfirmasi password tidak cocok';
                        feedback.style.display = 'block';
                    }
                }
            }
        }
    }

    // Enhanced form validation
    function validateRegisterForm() {
        const form = document.getElementById('registerForm');
        const inputs = form.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
            const value = input.value.trim();
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            // Remove existing validation classes
            input.classList.remove('is-invalid');
            if (feedback) feedback.style.display = 'none';

            // Check if empty
            if (!value && input.type !== 'checkbox') {
                input.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Field ini wajib diisi';
                    feedback.style.display = 'block';
                }
                isValid = false;
                return;
            }

            // Specific validations
            switch (input.name) {
                case 'first_name':
                case 'last_name':
                    if (value.length < 2) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Minimal 2 karakter';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                    break;

                case 'phone':
                    const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
                    if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Format nomor telepon tidak valid';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                    break;

                case 'username':
                    const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
                    if (!usernameRegex.test(value)) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Username hanya boleh mengandung huruf, angka, dan underscore (3-20 karakter)';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                    break;

                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Format email tidak valid';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                    break;

                case 'password':
                    if (value.length < 8) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Password minimal 8 karakter';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                    break;

                case 'password_confirmation':
                    const passwordInput = form.querySelector('input[name="password"]');
                    if (passwordInput && value !== passwordInput.value) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Konfirmasi password tidak sama';
                            feedback.style.display = 'block';
                        }
                        isValid = false;
                    }
                    break;
            }
        });

        // Check required checkboxes
        const requiredCheckboxes = form.querySelectorAll('input[type="checkbox"][required]');
        requiredCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.classList.add('is-invalid');
                const feedback = checkbox.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = 'Anda harus menyetujui syarat dan ketentuan';
                    feedback.style.display = 'block';
                }
                isValid = false;
            }
        });

        return isValid;
    }

    // Override the main submit function for register-specific validation
    function submitForm(formId, buttonId) {
        if (!validateRegisterForm()) {
            return false;
        }

        const button = document.getElementById(buttonId);
        const originalText = button.innerHTML;

        // Show loading state
        button.classList.add('btn-loading');
        button.disabled = true;

        // Show success message
        showNotification('Mendaftarkan akun...', 'info');

        // Restore button state after 15 seconds (fallback)
        setTimeout(() => {
            button.classList.remove('btn-loading');
            button.disabled = false;
            button.innerHTML = originalText;
        }, 15000);

        return true;
    }

    // Phone number formatting
    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');

        // Handle different formats
        if (value.startsWith('62')) {
            value = '+' + value;
        } else if (value.startsWith('8')) {
            value = '0' + value;
        }

        input.value = value;
    }

    // Real-time validation setup
    document.addEventListener('DOMContentLoaded', function() {
        // Phone number formatting
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                formatPhoneNumber(this);
            });
        }

        // Age validation for date of birth
        const dobInput = document.getElementById('date_of_birth');
        if (dobInput) {
            dobInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                const age = today.getFullYear() - selectedDate.getFullYear();

                if (age < 13) {
                    this.classList.add('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = 'Usia minimal 13 tahun';
                        feedback.style.display = 'block';
                    }
                } else {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });
        }

        // Username suggestions
        const usernameInput = document.getElementById('username');
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');

        if (usernameInput && firstNameInput && lastNameInput) {
            function generateUsernameSuggestion() {
                const firstName = firstNameInput.value.trim().toLowerCase();
                const lastName = lastNameInput.value.trim().toLowerCase();

                if (firstName && lastName && !usernameInput.value) {
                    const suggestion = firstName + lastName + Math.floor(Math.random() * 1000);
                    usernameInput.placeholder = `Contoh: ${suggestion}`;
                }
            }

            firstNameInput.addEventListener('input', generateUsernameSuggestion);
            lastNameInput.addEventListener('input', generateUsernameSuggestion);
        }
    });

    // Handle registration errors
    @if($errors->any())
        // Show errors for current step
        const errors = @json($errors->getMessages());

        // Determine which step has errors
        let errorStep = 1;
        if (errors.username || errors.email || errors.password || errors.password_confirmation) {
            errorStep = 2;
        } else if (errors.terms || errors.privacy) {
            errorStep = 3;
        }

        // Set the step in Alpine component
        setTimeout(() => {
            const component = Alpine.$data(document.querySelector('[x-data]'));
            if (component) {
                component.step = errorStep;
            }
        }, 100);

        // Show error notification
        showNotification('Terdapat kesalahan dalam form pendaftaran. Silakan periksa kembali.', 'error');
    @endif

    // Success registration handling
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');

        // Redirect after a delay
        setTimeout(() => {
            window.location.href = '{{ route('login') }}';
        }, 3000);
    @endif

    // Social login handler
    async function socialLogin(provider) {
        try {
            showNotification(`Menghubungkan dengan ${provider}...`, 'info');
            window.location.href = `/auth/${provider}/redirect`;
        } catch (error) {
            console.error('Social login error:', error);
            showNotification(`Gagal melakukan login dengan ${provider}`, 'error');
        }
    }
</script>
@endpush
