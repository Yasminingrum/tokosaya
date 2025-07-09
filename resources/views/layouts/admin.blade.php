<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel - TokoSaya')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Admin Theme CSS -->
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #858796;
            --admin-success: #1cc88a;
            --admin-info: #36b9cc;
            --admin-warning: #f6c23e;
            --admin-danger: #e74a3b;
            --admin-light: #f8f9fc;
            --admin-dark: #5a5c69;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--admin-light);
            font-size: 0.9rem;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            text-align: center;
            color: #fff;
            font-weight: bold;
            font-size: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        /* Top Navbar */
        .admin-navbar {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            border-radius: 0.35rem;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        .btn-success {
            background-color: var(--admin-success);
            border-color: var(--admin-success);
        }

        .btn-warning {
            background-color: var(--admin-warning);
            border-color: var(--admin-warning);
        }

        .btn-danger {
            background-color: var(--admin-danger);
            border-color: var(--admin-danger);
        }

        /* Table */
        .table {
            font-size: 0.85rem;
        }

        .table th {
            font-weight: 700;
            border-top: none;
            color: var(--admin-dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-store me-2"></i>
            <span class="brand-text">TokoSaya Admin</span>
        </div>

        <nav class="nav flex-column mt-3">
            <!-- Dashboard -->
            <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <!-- Products -->
            <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
               href="{{ route('admin.products.index') ?? '#' }}">
                <i class="fas fa-box"></i>
                <span class="nav-text">Produk</span>
            </a>

            <!-- Categories -->
            <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') ?? '#' }}">
                <i class="fas fa-list"></i>
                <span class="nav-text">Kategori</span>
            </a>

            <!-- Orders -->
            <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
               href="{{ route('admin.orders.index') ?? '#' }}">
                <i class="fas fa-shopping-cart"></i>
                <span class="nav-text">Pesanan</span>
            </a>

            <!-- Users -->
            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') ?? '#' }}">
                <i class="fas fa-users"></i>
                <span class="nav-text">Pengguna</span>
            </a>

            <!-- Reviews -->
            <a class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}"
               href="{{ route('admin.reviews.index') ?? '#' }}">
                <i class="fas fa-star"></i>
                <span class="nav-text">Review</span>
            </a>

            <!-- Payments -->
            <a class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
               href="{{ route('admin.payments.index') ?? '#' }}">
                <i class="fas fa-credit-card"></i>
                <span class="nav-text">Pembayaran</span>
            </a>

            <!-- Analytics -->
            <a class="nav-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}"
               href="{{ route('admin.analytics') ?? '#' }}">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Analitik</span>
            </a>

            <!-- Settings -->
            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"
               href="{{ route('admin.settings') ?? '#' }}">
                <i class="fas fa-cog"></i>
                <span class="nav-text">Pengaturan</span>
            </a>

            <hr class="my-3" style="border-color: rgba(255, 255, 255, 0.2);">

            <!-- Back to Site -->
            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span class="nav-text">Lihat Situs</span>
            </a>
        </nav>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <nav class="admin-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <!-- Sidebar Toggle -->
                <button class="btn btn-link text-dark p-0 me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Page Title -->
                <h5 class="mb-0 text-dark">@yield('page-title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center">
                <!-- Search -->
                <div class="position-relative me-3 d-none d-md-block">
                    <input type="text" class="form-control form-control-sm"
                           placeholder="Cari..." style="width: 200px;">
                    <i class="fas fa-search position-absolute"
                       style="right: 10px; top: 50%; transform: translateY(-50%); color: #ccc;"></i>
                </div>

                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-dark position-relative"
                            type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size: 0.6rem;">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifikasi</h6></li>
                        <li><a class="dropdown-item" href="#">
                            <small class="text-muted">Pesanan baru #1001</small>
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <small class="text-muted">Stok produk menipis</small>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Lihat Semua</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark d-flex align-items-center"
                            type="button" data-bs-toggle="dropdown">
                        <img src="https://via.placeholder.com/32x32/4e73df/fff?text={{ substr(auth()->user()->first_name, 0, 1) }}"
                             class="rounded-circle me-2" width="32" height="32">
                        <span class="d-none d-md-inline">{{ auth()->user()->first_name }}</span>
                        <i class="fas fa-caret-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                            <i class="fas fa-user me-2"></i>Profil Saya
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.settings') ?? '#' }}">
                            <i class="fas fa-cog me-2"></i>Pengaturan
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mx-4 mt-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        <div class="content-wrapper p-4 fade-in">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Admin JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Sidebar toggle functionality
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    // Mobile: Show/hide sidebar with overlay
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                } else {
                    // Desktop: Collapse/expand sidebar
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');

                    // Hide/show nav text
                    const navTexts = document.querySelectorAll('.nav-text, .brand-text');
                    navTexts.forEach(text => {
                        text.style.display = sidebar.classList.contains('collapsed') ? 'none' : 'inline';
                    });
                }
            });

            // Close sidebar when clicking overlay (mobile)
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert.classList.contains('show')) {
                        bootstrap.Alert.getOrCreateInstance(alert).close();
                    }
                });
            }, 5000);

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });

        // Global admin functions
        function showLoading(element = null) {
            if (element) {
                element.classList.add('loading');
            } else {
                document.body.classList.add('loading');
            }
        }

        function hideLoading(element = null) {
            if (element) {
                element.classList.remove('loading');
            } else {
                document.body.classList.remove('loading');
            }
        }

        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${getToastIcon(type)} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 5000
            });

            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        function getToastIcon(type) {
            switch (type) {
                case 'success': return 'check-circle';
                case 'error': return 'exclamation-circle';
                case 'warning': return 'exclamation-triangle';
                case 'info': return 'info-circle';
                default: return 'bell';
            }
        }

        // Confirm delete function
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus item ini?') {
            return confirm(message);
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    </script>

    @stack('scripts')
</body>
</html>
