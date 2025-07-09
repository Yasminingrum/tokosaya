@extends('layouts.app')

@section('title', 'Dashboard - TokoSaya')

@section('content')
<div class="container py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="card-title mb-1">Selamat datang, {{ $user->first_name }}!</h3>
                            <p class="card-text mb-0">Kelola akun Anda dan nikmati pengalaman berbelanja di TokoSaya</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-user-circle fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-shopping-bag fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ $recentOrders->count() }}</h5>
                    <p class="card-text text-muted">Pesanan Aktif</p>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ $wishlistCount }}</h5>
                    <p class="card-text text-muted">Wishlist</p>
                    <a href="{{ route('wishlist.index') }}" class="btn btn-outline-success btn-sm">Lihat Wishlist</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ $notifications->count() }}</h5>
                    <p class="card-text text-muted">Notifikasi</p>
                    <a href="{{ route('profile.notifications') }}" class="btn btn-outline-warning btn-sm">Lihat Semua</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-gift fa-2x"></i>
                    </div>
                    <h5 class="card-title">0</h5>
                    <p class="card-text text-muted">Poin Loyalitas</p>
                    <a href="{{ route('profile.loyalty') }}" class="btn btn-outline-info btn-sm">Lihat Poin</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pesanan Terbaru</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">#{{ $order->order_number ?? 'ORD-' . $order->id }}</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                        <td>
                                            <span class="fw-bold">Rp {{ number_format($order->total_cents / 100, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'info',
                                                    'processing' => 'primary',
                                                    'shipped' => 'success',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Pesanan</h5>
                            <p class="text-muted">Mulai berbelanja untuk melihat pesanan Anda di sini</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Mulai Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Belanja Sekarang
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Riwayat Pesanan
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-heart me-2"></i>Lihat Wishlist
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-info">
                            <i class="fas fa-user-edit me-2"></i>Edit Profil
                        </a>
                        <a href="{{ route('profile.addresses') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-map-marker-alt me-2"></i>Kelola Alamat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            @if($notifications->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifikasi</h5>
                        <a href="{{ route('profile.notifications') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($notifications->take(3) as $notification)
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="notification-icon me-3">
                            <i class="fas fa-bell text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $notification->title }}</h6>
                            <p class="text-muted small mb-1">{{ Str::limit($notification->message, 60) }}</p>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Products -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Produk Rekomendasi</h5>
                        <a href="{{ route('products.index') }}" class="text-decoration-none">Lihat Semua Produk</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Placeholder for recommended products -->
                        @for($i = 1; $i <= 4; $i++)
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Produk Sample {{ $i }}</h6>
                                    <p class="card-text text-muted small">Deskripsi singkat produk</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold">Rp 99.000</span>
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.notification-icon {
    width: 30px;
    height: 30px;
    background: rgba(0, 123, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}

.btn {
    border-radius: 6px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh notifications count
    setInterval(function() {
        fetch('{{ route("profile.notifications") }}?ajax=1')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    updateNotificationBadge(data.count);
                }
            })
            .catch(error => console.log('Error fetching notifications:', error));
    }, 60000); // Check every minute
});

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}
</script>
@endpush
@endsection
