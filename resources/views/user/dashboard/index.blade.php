@extends('user.layouts.app')

@section('title', 'Dashboard - SportWear')

@section('content')
<div class="container py-5">
    <!-- Welcome Banner -->
    <div class="welcome-banner bg-gradient-primary rounded-4 p-5 text-white mb-5" data-aos="fade-down">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 mb-3">Selamat Datang, <span class="text-warning">{{ $user->name }}</span>! 👋</h1>
                <p class="mb-0">Apa yang ingin Anda lakukan hari ini? Mari mulai perjalanan olahraga Anda!</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="floating">
                    <i class="fas fa-running fa-5x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stats-card bg-white rounded-4 p-4 shadow-sm border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="fas fa-shopping-cart text-primary fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $totalTransactions }}</h3>
                        <p class="text-muted mb-0">Total Transaksi</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stats-card bg-white rounded-4 p-4 shadow-sm border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="fas fa-wallet text-success fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total Pengeluaran</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stats-card bg-white rounded-4 p-4 shadow-sm border-start border-info border-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-info bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="fas fa-calendar-alt text-info fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $activeRentals }}</h3>
                        <p class="text-muted mb-0">Sewa Aktif</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stats-card bg-white rounded-4 p-4 shadow-sm border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="fas fa-shopping-basket text-warning fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $cartCount }}</h3>
                        <p class="text-muted mb-0">Keranjang</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recent Transactions -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Transaksi Terbaru</h5>
                        <a href="{{ route('user.transaksi.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <strong>{{ $transaction->kode_transaksi }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $transaction->tipe == 'penjualan' ? 'Pembelian' : 'Penyewaan' }}</small>
                                    </td>
                                    <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                    <td class="font-weight-bold">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        @include('components.status-badge', ['status' => $transaction->status])
                                    </td>
                                    <td>
                                        <a href="{{ route('user.transaksi.show', $transaction->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada transaksi</h5>
                        <p class="text-muted">Mulai belanja untuk melihat transaksi Anda</p>
                        <a href="{{ route('user.produk.index') }}" class="btn btn-sport">
                            <i class="fas fa-shopping-cart me-2"></i> Belanja Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Active Rentals -->
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sewa Aktif</h5>
                        <a href="{{ route('user.sewa.aktif') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($activeRentalsList->count() > 0)
                    <div class="row g-3">
                        @foreach($activeRentalsList as $rental)
                        <div class="col-md-6">
                            <div class="rental-card border rounded-3 p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="font-weight-bold mb-1">{{ $rental->produk->nama }}</h6>
                                        <small class="text-muted">{{ $rental->kode_sewa }}</small>
                                    </div>
                                    @include('components.status-badge', ['status' => $rental->status])
                                </div>
                                
                                <div class="rental-info mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Tanggal Kembali:</small>
                                        <small class="font-weight-bold {{ $rental->sisa_hari < 3 ? 'text-danger' : 'text-success' }}">
                                            {{ $rental->tanggal_kembali_rencana->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Sisa Hari:</small>
                                        <small class="font-weight-bold {{ $rental->sisa_hari < 3 ? 'text-danger' : 'text-success' }}">
                                            {{ $rental->sisa_hari }} hari
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 6px;">
                                    @php
                                        $totalDays = $rental->jumlah_hari;
                                        $remainingDays = $rental->sisa_hari;
                                        $percentage = ($totalDays - $remainingDays) / $totalDays * 100;
                                    @endphp
                                    <div class="progress-bar bg-success" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('user.sewa.show', $rental->id) }}" 
                                       class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="fas fa-info-circle me-1"></i> Detail
                                    </a>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" 
                                            data-bs-target="#pengembalianModal{{ $rental->id }}">
                                        <i class="fas fa-undo me-1"></i> Kembalikan
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada sewa aktif</h5>
                        <p class="text-muted">Mulai sewa alat olahraga favorit Anda</p>
                        <a href="{{ route('user.sewa.index') }}" class="btn btn-sport">
                            <i class="fas fa-calendar-plus me-2"></i> Sewa Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Notifications -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-left">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Notifikasi</h5>
                        @if($notifications->count() > 0)
                        <form action="{{ route('user.notifikasi.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <a href="{{ $notification->link ?? '#' }}" 
                           class="list-group-item list-group-item-action border-0 py-3 px-4 notification-item
                                  {{ $notification->dibaca ? '' : 'bg-light bg-opacity-50' }}"
                           data-id="{{ $notification->id }}">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="{{ $notification->tipe_icon }} fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1 {{ $notification->dibaca ? 'text-muted' : 'font-weight-bold' }}">
                                            {{ $notification->judul }}
                                        </h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 text-muted small">{{ Str::limit($notification->pesan, 50) }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white border-0 text-center">
                        <a href="{{ route('user.notifikasi.index') }}" class="btn btn-link">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada notifikasi</h5>
                        <p class="text-muted small">Semua notifikasi sudah Anda baca</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-left" data-aos-delay="200">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('user.keranjang.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <span>Keranjang</span>
                                @if($cartCount > 0)
                                <span class="badge bg-accent mt-1">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.produk.index') }}" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <span>Belanja</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.sewa.index') }}" class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center">
                                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                <span>Sewa</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.profil.edit') }}" class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center">
                                <i class="fas fa-user fa-2x mb-2"></i>
                                <span>Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Returns -->
            @if($upcomingReturns->count() > 0)
            <div class="card border-0 shadow-sm rounded-4 mt-4" data-aos="fade-left" data-aos-delay="300">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Pengembalian Mendatang</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($upcomingReturns as $rental)
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 small">{{ $rental->produk->nama }}</h6>
                                    <small class="text-muted">Kembali: {{ $rental->tanggal_kembali_rencana->format('d/m') }}</small>
                                </div>
                                <span class="badge bg-{{ $rental->sisa_hari < 2 ? 'danger' : 'warning' }}">
                                    {{ $rental->sisa_hari }} hari
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals for Returns -->
@foreach($activeRentalsList as $rental)
@include('components.modal', [
    'id' => 'pengembalianModal' . $rental->id,
    'title' => 'Pengembalian Alat',
    'size' => 'modal-lg'
])
<div class="modal fade" id="pengembalianModal{{ $rental->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengembalian {{ $rental->produk->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Fitur pengembalian akan diimplementasi di fase berikutnya.</p>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('styles')
<style>
.welcome-banner {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 100px 100px;
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.stats-card {
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.stats-icon {
    transition: all 0.3s ease;
}

.stats-card:hover .stats-icon {
    transform: scale(1.1) rotate(5deg);
}

.rental-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.rental-card:hover {
    border-color: var(--primary);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.progress {
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    background: rgba(43, 108, 176, 0.05) !important;
    padding-left: 1.5rem !important;
}

.quick-action-btn {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    border-color: var(--primary);
    transform: translateY(-3px);
}
</style>
@endpush

@push('scripts')
<script>
// Mark notification as read when clicked
document.querySelectorAll('.notification-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (!this.href || this.href === '#') {
            e.preventDefault();
        }
        
        const notificationId = this.dataset.id;
        const notificationElement = this;
        
        // AJAX request to mark as read
        fetch(`/user/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notificationElement.classList.remove('bg-light', 'bg-opacity-50');
                notificationElement.querySelector('h6').classList.remove('font-weight-bold');
                notificationElement.querySelector('h6').classList.add('text-muted');
                
                // Update notification badge count
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent) || 0;
                    if (currentCount > 1) {
                        badge.textContent = currentCount - 1;
                    } else {
                        badge.remove();
                    }
                }
            }
        });
    });
});

// Progress bar animation
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
});

// Real-time updates (simulated)
setInterval(() => {
    // Check for new notifications (simulated)
    if (Math.random() > 0.7) {
        console.log('Checking for updates...');
        // In production, this would be a real API call
    }
}, 30000);
</script>
@endpush