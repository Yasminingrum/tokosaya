@extends('layouts.app')

@section('title', '500 - Server Error - TokoSaya')
@section('meta_description', 'Terjadi kesalahan server di TokoSaya. Tim kami sedang memperbaiki masalah ini. Silakan coba lagi dalam beberapa saat.')

@push('styles')
<style>
    .error-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .error-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,0 1000,300 1000,1000 0,700"/><polygon fill="rgba(255,255,255,0.03)" points="0,200 1000,0 1000,400 0,600"/></svg>');
        animation: slidePattern 15s infinite linear;
    }

    @keyframes slidePattern {
        0% { transform: translateX(0%); }
        100% { transform: translateX(100%); }
    }

    .error-content {
        text-align: center;
        position: relative;
        z-index: 2;
        max-width: 700px;
        padding: 40px 20px;
    }

    .error-icon {
        font-size: clamp(4rem, 8vw, 6rem);
        margin-bottom: 30px;
        color: rgba(255, 255, 255, 0.9);
        animation: pulse 2s infinite ease-in-out;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.9;
        }
        50% {
            transform: scale(1.1);
            opacity: 1;
        }
    }

    .error-code {
        font-size: clamp(6rem, 12vw, 10rem);
        font-weight: 900;
        line-height: 0.9;
        margin-bottom: 20px;
        background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        z-index: -1;
        animation: glitch 3s infinite ease-in-out;
    }

    @keyframes glitch {
        0%, 90%, 100% {
            transform: translate(0);
            opacity: 0;
        }
        5%, 10% {
            transform: translate(-2px, 2px);
            opacity: 0.3;
        }
        15%, 20% {
            transform: translate(2px, -2px);
            opacity: 0.3;
        }
    }

    .error-title {
        font-size: clamp(1.8rem, 4vw, 3rem);
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .error-message {
        font-size: clamp(1rem, 2.5vw, 1.3rem);
        margin-bottom: 40px;
        opacity: 0.9;
        line-height: 1.6;
    }

    .status-indicator {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 20px;
        margin: 30px 0;
        display: inline-block;
    }

    .status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .status-item:last-child {
        margin-bottom: 0;
    }

    .status-label {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }

    .status-checking {
        background: #fbbf24;
        animation: spin 1s linear infinite;
    }

    .status-error {
        background: #ef4444;
    }

    .status-ok {
        background: #10b981;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .error-actions {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
        margin-top: 40px;
    }

    .btn-primary-custom {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        min-width: 200px;
        justify-content: center;
    }

    .btn-primary-custom:hover {
        background: white;
        color: #ef4444;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
    }

    .btn-secondary-custom {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: rgba(255, 255, 255, 0.9);
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        font-size: 1rem;
        min-width: 180px;
        justify-content: center;
    }

    .btn-secondary-custom:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
        color: white;
        transform: translateY(-2px);
    }

    .refresh-countdown {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 15px 25px;
        margin: 20px 0;
        font-size: 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .countdown-number {
        font-weight: 700;
        font-size: 1.2rem;
        color: #fbbf24;
    }

    .technical-details {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        padding: 20px;
        margin: 30px 0;
        text-align: left;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        max-height: 150px;
        overflow-y: auto;
    }

    .technical-details h6 {
        color: #fbbf24;
        margin-bottom: 15px;
        font-family: inherit;
        text-align: center;
    }

    .error-id {
        color: #fbbf24;
        font-weight: 600;
    }

    .contact-emergency {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        margin: 30px 0;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .contact-emergency h6 {
        margin-bottom: 15px;
        font-weight: 700;
    }

    .contact-methods {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
    }

    .contact-method {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        text-decoration: none;
        color: white;
        transition: all 0.3s ease;
    }

    .contact-method:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        transform: translateY(-2px);
    }

    .progress-bar {
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
        overflow: hidden;
        margin: 20px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 2px;
        animation: progressFill 10s linear;
    }

    @keyframes progressFill {
        0% { width: 0%; }
        100% { width: 100%; }
    }

    .floating-particles {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        z-index: 1;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: floatParticles 20s infinite linear;
    }

    .particle:nth-child(1) {
        width: 4px;
        height: 4px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .particle:nth-child(2) {
        width: 6px;
        height: 6px;
        top: 60%;
        left: 80%;
        animation-delay: 3s;
    }

    .particle:nth-child(3) {
        width: 3px;
        height: 3px;
        top: 80%;
        left: 20%;
        animation-delay: 6s;
    }

    .particle:nth-child(4) {
        width: 5px;
        height: 5px;
        top: 30%;
        left: 70%;
        animation-delay: 9s;
    }

    .particle:nth-child(5) {
        width: 4px;
        height: 4px;
        top: 70%;
        left: 30%;
        animation-delay: 12s;
    }

    @keyframes floatParticles {
        0% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) rotate(360deg);
            opacity: 0;
        }
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .error-content {
            padding: 20px 15px;
        }

        .error-actions {
            gap: 12px;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            width: 100%;
            max-width: 280px;
        }

        .contact-methods {
            flex-direction: column;
            align-items: center;
        }

        .contact-method {
            width: 100%;
            max-width: 200px;
            justify-content: center;
        }

        .status-indicator {
            width: 100%;
            max-width: 350px;
        }

        .technical-details {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .error-content {
            padding: 15px 10px;
        }

        .error-message {
            margin-bottom: 30px;
        }

        .btn-primary-custom {
            padding: 12px 30px;
            font-size: 1rem;
        }

        .btn-secondary-custom {
            padding: 10px 25px;
            font-size: 0.9rem;
        }

        .status-indicator {
            padding: 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="error-container">
    <!-- Floating Particles -->
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="error-content" data-aos="fade-up">
        <div class="error-icon">
            <i class="fas fa-server"></i>
        </div>

        <div class="error-code">500</div>

        <h1 class="error-title">Server Sedang Bermasalah</h1>

        <p class="error-message">
            Maaf, terjadi kesalahan pada server kami. Tim teknis sedang bekerja keras untuk memperbaiki masalah ini.
            Silakan coba lagi dalam beberapa saat atau hubungi customer service jika masalah berlanjut.
        </p>

        <!-- Status Indicator -->
        <div class="status-indicator">
            <div class="status-item">
                <div class="status-label">
                    <div class="status-icon status-checking">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <span>Memeriksa Server Status</span>
                </div>
                <span class="text-warning">Checking...</span>
            </div>
            <div class="status-item">
                <div class="status-label">
                    <div class="status-icon status-error">
                        <i class="fas fa-times"></i>
                    </div>
                    <span>Database Connection</span>
                </div>
                <span class="text-danger">Error</span>
            </div>
            <div class="status-item">
                <div class="status-label">
                    <div class="status-icon status-ok">
                        <i class="fas fa-check"></i>
                    </div>
                    <span>User Authentication</span>
                </div>
                <span class="text-success">OK</span>
            </div>
        </div>

        <!-- Auto Refresh Countdown -->
        <div class="refresh-countdown">
            <i class="fas fa-sync-alt me-2"></i>
            Halaman akan diperbarui otomatis dalam <span class="countdown-number" id="countdown">60</span> detik
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

        <!-- Primary Actions -->
        <div class="error-actions">
            <button class="btn-primary-custom" onclick="refreshPage()">
                <i class="fas fa-redo"></i>
                Muat Ulang Halaman
            </button>

            <a href="{{ route('home') }}" class="btn-secondary-custom">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Technical Details (for debugging) -->
        <div class="technical-details">
            <h6><i class="fas fa-code me-2"></i>Technical Details</h6>
            <div>Error ID: <span class="error-id" id="errorId">TS-{{ date('YmdHis') }}-{{ rand(1000, 9999) }}</span></div>
            <div>Timestamp: {{ now()->format('Y-m-d H:i:s T') }}</div>
            <div>Request: {{ request()->method() }} {{ request()->fullUrl() }}</div>
            <div>User Agent: <span id="userAgent"></span></div>
            <div>IP Address: {{ request()->ip() }}</div>
        </div>

        <!-- Emergency Contact -->
        <div class="contact-emergency">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Butuh Bantuan Segera?</h6>
            <p class="mb-3">Tim customer service kami siap membantu Anda 24/7</p>

            <div class="contact-methods">
                <a href="tel:0804-1-500-400" class="contact-method">
                    <i class="fas fa-phone"></i>
                    <span>0804-1-500-400</span>
                </a>
                <a href="mailto:support@tokosaya.id" class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <span>support@tokosaya.id</span>
                </a>
                <a href="https://wa.me/6281234567890" class="contact-method" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>

        <!-- Additional Help -->
        <div class="mt-4">
            <p class="small opacity-75">
                <strong>Apa yang terjadi?</strong><br>
                Server mengalami kesalahan internal dan tidak dapat memproses permintaan Anda saat ini.
                Ini bukan kesalahan dari sisi Anda. Tim kami telah diberitahu dan sedang menangani masalah ini.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer for auto refresh
    let countdown = 60;
    const countdownElement = document.getElementById('countdown');

    const countdownInterval = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;

        if (countdown <= 0) {
            clearInterval(countdownInterval);
            refreshPage();
        }
    }, 1000);

    // Auto refresh function
    function refreshPage() {
        // Show loading state
        const refreshBtn = document.querySelector('.btn-primary-custom');
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
        refreshBtn.disabled = true;

        // Clear countdown
        clearInterval(countdownInterval);

        // Attempt to refresh
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    // Make refresh function global
    window.refreshPage = refreshPage;

    // Set user agent
    document.getElementById('userAgent').textContent = navigator.userAgent.substring(0, 80) + '...';

    // Health check simulation
    const statusItems = document.querySelectorAll('.status-item');

    setTimeout(() => {
        // Update first status
        const firstStatus = statusItems[0];
        const firstIcon = firstStatus.querySelector('.status-icon');
        const firstText = firstStatus.querySelector('.text-warning');

        firstIcon.className = 'status-icon status-ok';
        firstIcon.innerHTML = '<i class="fas fa-check"></i>';
        firstText.className = 'text-success';
        firstText.textContent = 'Online';
    }, 2000);

    setTimeout(() => {
        // Update database status
        const dbStatus = statusItems[1];
        const dbIcon = dbStatus.querySelector('.status-icon');
        const dbText = dbStatus.querySelector('.text-danger');

        dbIcon.className = 'status-icon status-checking';
        dbIcon.innerHTML = '<i class="fas fa-sync-alt"></i>';
        dbText.className = 'text-warning';
        dbText.textContent = 'Reconnecting...';
    }, 4000);

    // Track 500 errors for monitoring
    if (typeof gtag !== 'undefined') {
        gtag('event', 'server_error', {
            'event_category': '500_Error',
            'event_label': window.location.pathname,
            'value': 1
        });
    }

    // Send error report to monitoring service
    const errorData = {
        type: '500',
        url: window.location.href,
        userAgent: navigator.userAgent,
        timestamp: Date.now(),
        errorId: document.getElementById('errorId').textContent,
        referrer: document.referrer
    };

    // Log error details for debugging
    console.error('500 Server Error:', errorData);

    // Report to external monitoring (if configured)
    if (window.reportError) {
        window.reportError(errorData);
    }

    // Retry mechanism
    let retryCount = 0;
    const maxRetries = 3;

    function attemptRetry() {
        if (retryCount < maxRetries) {
            retryCount++;
            console.log(`Retry attempt ${retryCount}/${maxRetries}`);

            // Simple health check
            fetch('{{ route("home") }}', { method: 'HEAD' })
                .then(response => {
                    if (response.ok) {
                        // Server is back online
                        showRecoveryMessage();
                    } else {
                        // Still having issues, try again
                        setTimeout(attemptRetry, 10000 * retryCount); // Exponential backoff
                    }
                })
                .catch(() => {
                    // Network error, try again
                    setTimeout(attemptRetry, 10000 * retryCount);
                });
        }
    }

    // Start retry attempts after initial delay
    setTimeout(attemptRetry, 5000);

    function showRecoveryMessage() {
        // Update UI to show recovery
        const refreshCountdown = document.querySelector('.refresh-countdown');
        refreshCountdown.innerHTML = `
            <i class="fas fa-check-circle me-2 text-success"></i>
            Server sudah pulih! Halaman akan dimuat ulang otomatis...
        `;
        refreshCountdown.style.background = 'rgba(16, 185, 129, 0.2)';
        refreshCountdown.style.borderColor = 'rgba(16, 185, 129, 0.3)';

        // Auto refresh after short delay
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        switch(e.key) {
            case 'r':
            case 'R':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    refreshPage();
                }
                break;
            case 'h':
            case 'H':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    window.location.href = '{{ route("home") }}';
                }
                break;
            case 'Escape':
                // Clear any selections and focus
                window.getSelection().removeAllRanges();
                document.activeElement.blur();
                break;
        }
    });

    // Preload home page for faster navigation
    const homeLink = document.createElement('link');
    homeLink.rel = 'prefetch';
    homeLink.href = '{{ route("home") }}';
    document.head.appendChild(homeLink);

    // Service Worker registration for offline handling (if available)
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then(registration => {
            if (registration) {
                // Check if we're offline
                if (!navigator.onLine) {
                    document.querySelector('.error-message').innerHTML +=
                        '<br><br><strong>Catatan:</strong> Anda sedang offline. Periksa koneksi internet Anda.';
                }
            }
        });
    }

    // Network status monitoring
    window.addEventListener('online', function() {
        console.log('Connection restored');
        // Automatically retry when back online
        setTimeout(() => {
            refreshPage();
        }, 1000);
    });

    window.addEventListener('offline', function() {
        console.log('Connection lost');
        // Update UI to show offline status
        const refreshCountdown = document.querySelector('.refresh-countdown');
        refreshCountdown.innerHTML = `
            <i class="fas fa-wifi-slash me-2 text-warning"></i>
            Tidak ada koneksi internet. Periksa koneksi Anda.
        `;
        refreshCountdown.style.background = 'rgba(245, 158, 11, 0.2)';
        clearInterval(countdownInterval);
    });

    // Add some interactivity to make waiting less frustrating
    const particles = document.querySelectorAll('.particle');
    particles.forEach((particle, index) => {
        particle.addEventListener('click', function() {
            this.style.background = '#fbbf24';
            this.style.transform = 'scale(3)';
            this.style.transition = 'all 0.3s ease';

            setTimeout(() => {
                this.style.background = 'rgba(255, 255, 255, 0.1)';
                this.style.transform = 'scale(1)';
            }, 300);
        });
    });

    // Easter egg: Konami code for tech details
    let konamiCode = [];
    const konamiSequence = [
        'ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown',
        'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight',
        'KeyB', 'KeyA'
    ];

    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.code);
        if (konamiCode.length > konamiSequence.length) {
            konamiCode.shift();
        }

        if (konamiCode.join(',') === konamiSequence.join(',')) {
            // Show additional tech details
            const techDetails = document.querySelector('.technical-details');
            techDetails.innerHTML += `
                <div style="margin-top: 15px; color: #fbbf24;">
                    <div>Memory Usage: ${(performance.memory?.usedJSHeapSize / 1024 / 1024).toFixed(2) || 'N/A'} MB</div>
                    <div>Connection: ${navigator.connection?.effectiveType || 'Unknown'}</div>
                    <div>Platform: ${navigator.platform}</div>
                    <div>Language: ${navigator.language}</div>
                </div>
            `;
            konamiCode = [];
        }
    });
});
</script>
@endpush
