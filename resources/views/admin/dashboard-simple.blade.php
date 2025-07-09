<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TokoSaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-content {
            min-height: 100vh;
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar text-white p-3">
                    <div class="text-center mb-4">
                        <h4><i class="fas fa-store"></i> TokoSaya</h4>
                        <small>Admin Panel</small>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <small class="opacity-75">{{ ucfirst($role->name) }}</small>
                            </div>
                        </div>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link text-white active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link text-white" href="#" onclick="alert('Feature coming soon!')">
                            <i class="fas fa-box me-2"></i> Products
                        </a>
                        <a class="nav-link text-white" href="#" onclick="alert('Feature coming soon!')">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                        <a class="nav-link text-white" href="#" onclick="alert('Feature coming soon!')">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                        <a class="nav-link text-white" href="#" onclick="alert('Feature coming soon!')">
                            <i class="fas fa-chart-bar me-2"></i> Analytics
                        </a>
                        <a class="nav-link text-white" href="#" onclick="alert('Feature coming soon!')">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link text-white" href="/profile">
                            <i class="fas fa-user-circle me-2"></i> Profile
                        </a>
                        <form action="/logout" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link text-white btn btn-link text-start p-0">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0">Dashboard</h2>
                            <p class="text-muted">Welcome back, {{ $user->first_name }}!</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-calendar me-2"></i>
                            {{ now()->format('l, d F Y') }}
                        </div>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <h3 class="mb-0">{{ number_format($stats['total_users']) }}</h3>
                                    <p class="text-muted mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                    <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                                    <p class="text-muted mb-0">Total Orders</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-box fa-2x"></i>
                                    </div>
                                    <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
                                    <p class="text-muted mb-0">Total Products</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <h3 class="mb-0">{{ number_format($stats['pending_orders']) }}</h3>
                                    <p class="text-muted mb-0">Pending Orders</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Section -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Role:</strong> {{ ucfirst($role->display_name ?? $role->name) }}</p>
                                            <p><strong>User ID:</strong> {{ $user->id }}</p>
                                            <p><strong>Email:</strong> {{ $user->email }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Last Login:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                                            <p><strong>Status:</strong>
                                                <span class="badge bg-success">Active</span>
                                            </p>
                                            <p><strong>Version:</strong> v1.0.0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="alert('Feature coming soon!')">
                                            <i class="fas fa-plus me-2"></i>Add Product
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="alert('Feature coming soon!')">
                                            <i class="fas fa-eye me-2"></i>View Orders
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="alert('Feature coming soon!')">
                                            <i class="fas fa-users me-2"></i>Manage Users
                                        </button>
                                        <a href="/profile" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-user me-2"></i>My Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
