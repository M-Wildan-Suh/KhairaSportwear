@extends('user.layouts.app')

@section('title', 'Notifikasi - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h5 class="card-title mb-4">Filter Notifikasi</h5>
                    
                    <!-- Stats -->
                    <div class="notification-stats mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total</span>
                            <span class="badge bg-primary">{{ $totalCount }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Belum Dibaca</span>
                            <span class="badge bg-danger">{{ $unreadCount }}</span>
                        </div>
                    </div>
                    
                    <!-- Filter by Type -->
                    <div class="filter-section mb-4">
                        <h6 class="font-weight-bold mb-3">Tipe Notifikasi</h6>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('user.notifikasi.index') }}" 
                               class="list-group-item list-group-item-action border-0 py-2 {{ !request('type') ? 'active' : '' }}">
                                <i class="fas fa-inbox me-2"></i> Semua
                                <span class="badge bg-primary float-end">{{ $typeCounts['all'] }}</span>
                            </a>
                            <a href="{{ route('user.notifikasi.index', ['read' => 'false']) }}" 
                               class="list-group-item list-group-item-action border-0 py-2 {{ request('read') === 'false' ? 'active' : '' }}">
                                <i class="fas fa-envelope me-2"></i> Belum Dibaca
                                <span class="badge bg-danger float-end">{{ $typeCounts['unread'] }}</span>
                            </a>
                            <a href="{{ route('user.notifikasi.index', ['type' => 'transaksi']) }}" 
                               class="list-group-item list-group-item-action border-0 py-2 {{ request('type') === 'transaksi' ? 'active' : '' }}">
                                <i class="fas fa-shopping-cart me-2"></i> Transaksi
                                <span class="badge bg-success float-end">{{ $typeCounts['transaksi'] }}</span>
                            </a>
                            <a href="{{ route('user.notifikasi.index', ['type' => 'sewa']) }}" 
                               class="list-group-item list-group-item-action border-0 py-2 {{ request('type') === 'sewa' ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt me-2"></i> Sewa
                                <span class="badge bg-info float-end">{{ $typeCounts['sewa'] }}</span>
                            </a>
                            <a href="{{ route('user.notifikasi.index', ['type' => 'denda']) }}" 
                               class="list-group-item list-group-item-action border-0 py-2 {{ request('type') === 'denda' ? 'active' : '' }}">
                                <i class="fas fa-exclamation-circle me-2"></i> Denda
                                <span class="badge bg-warning float-end">{{ $typeCounts['denda'] }}</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="action-buttons">
                        <div class="d-grid gap-2">
                            @if($unreadCount > 0)
                            <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                                <i class="fas fa-check-double me-2"></i> Tandai Semua Dibaca
                            </button>
                            @endif
                            @if($totalCount > 0)
                            <button class="btn btn-outline-danger" onclick="clearAllNotifications()">
                                <i class="fas fa-trash me-2"></i> Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-left">
                <div>
                    <h1 class="h2 font-weight-bold mb-0">Notifikasi</h1>
                    <p class="text-muted mb-0">
                        @if(request('type'))
                        Filter: {{ ucfirst(request('type')) }}
                        @elseif(request('read') === 'false')
                        Filter: Belum Dibaca
                        @else
                        Semua Notifikasi
                        @endif
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshNotifications()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            
            <!-- Notifications List -->
            @if($notifikasis->count() > 0)
            <div class="notifications-list" data-aos="fade-up">
                @foreach($notifikasis as $notifikasi)
                <div class="notification-item card border-0 shadow-sm rounded-3 mb-3 {{ $notifikasi->dibaca ? '' : 'unread' }}"
                     data-id="{{ $notifikasi->id }}">
                    <div class="card-body">
                        <div class="d-flex">
                            <!-- Notification Icon -->
                            <div class="notification-icon me-3">
                                <div class="icon-circle {{ $notifikasi->dibaca ? 'bg-light' : 'bg-primary' }}">
                                    <i class="{{ $notifikasi->tipe_icon }} {{ $notifikasi->dibaca ? 'text-muted' : 'text-white' }}"></i>
                                </div>
                            </div>
                            
                            <!-- Notification Content -->
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 {{ $notifikasi->dibaca ? 'text-muted' : 'font-weight-bold' }}">
                                            {{ $notifikasi->judul }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $notifikasi->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if(!$notifikasi->dibaca)
                                            <button class="dropdown-item" onclick="markAsRead('{{ $notifikasi->id }}')">
                                                <i class="fas fa-check me-2"></i> Tandai Dibaca
                                            </button>
                                            @endif
                                            <button class="dropdown-item text-danger" onclick="deleteNotification('{{ $notifikasi->id }}')">
                                                <i class="fas fa-trash me-2"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="mb-3">{{ $notifikasi->pesan }}</p>
                                
                                @if($notifikasi->link)
                                <div class="mt-2">
                                    <a href="{{ $notifikasi->link }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-2"></i> Lihat Detail
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($notifikasis->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $notifikasis->withQueryString()->links() }}
            </div>
            @endif
            @else
            <!-- Empty State -->
            <div class="text-center py-5" data-aos="fade-up">
                <div class="empty-state">
                    <i class="fas fa-bell-slash fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Tidak Ada Notifikasi</h3>
                    <p class="text-muted mb-4">
                        @if(request('type') || request('read'))
                        Tidak ada notifikasi dengan filter ini
                        @else
                        Belum ada notifikasi untuk Anda
                        @endif
                    </p>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Notification Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengaturan Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="settingsForm">
                    <div class="mb-3">
                        <label class="form-label">Jenis Notifikasi</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="notifTransaksi" checked>
                            <label class="form-check-label" for="notifTransaksi">
                                Notifikasi Transaksi
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="notifSewa" checked>
                            <label class="form-check-label" for="notifSewa">
                                Notifikasi Sewa
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="notifDenda" checked>
                            <label class="form-check-label" for="notifDenda">
                                Notifikasi Denda
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notifPromo" checked>
                            <label class="form-check-label" for="notifPromo">
                                Notifikasi Promo & Update
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Notifikasi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                            <label class="form-check-label" for="emailNotif">
                                Kirim notifikasi ke email
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.sticky-top {
    position: sticky;
    z-index: 1020;
}

.notification-item.unread {
    border-left: 4px solid var(--primary) !important;
    background: rgba(43, 108, 176, 0.03);
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.notification-icon .icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.notification-item.unread .icon-circle {
    box-shadow: 0 0 0 5px rgba(43, 108, 176, 0.1);
}

.list-group-item.active {
    background: rgba(43, 108, 176, 0.1);
    color: var(--primary);
    border-left: 3px solid var(--primary);
    font-weight: 600;
}

.empty-state {
    padding: 3rem 1rem;
}

/* Animation for new notifications */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.new-notification {
    animation: pulse 1s infinite;
}
</style>
@endpush

@push('scripts')
<script>
// Mark notification as read
function markAsRead(notificationId) {
    const notificationElement = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
    
    fetch(`/user/notifikasi/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            notificationElement.classList.remove('unread');
            notificationElement.querySelector('.icon-circle').classList.remove('bg-primary');
            notificationElement.querySelector('.icon-circle').classList.add('bg-light');
            notificationElement.querySelector('.icon-circle i').classList.remove('text-white');
            notificationElement.querySelector('.icon-circle i').classList.add('text-muted');
            notificationElement.querySelector('h6').classList.remove('font-weight-bold');
            notificationElement.querySelector('h6').classList.add('text-muted');
            
            // Update badge count
            updateNotificationBadge();
            
            Toast.fire({
                icon: 'success',
                title: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal menandai notifikasi', 'error');
    });
}

// Mark all as read
function markAllAsRead() {
    Swal.fire({
        title: 'Tandai semua sebagai dibaca?',
        text: 'Semua notifikasi akan ditandai sebagai telah dibaca',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ED8936',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Ya, Tandai Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/user/notifikasi/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all notifications in UI
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.querySelector('.icon-circle').classList.remove('bg-primary');
                        item.querySelector('.icon-circle').classList.add('bg-light');
                        item.querySelector('.icon-circle i').classList.remove('text-white');
                        item.querySelector('.icon-circle i').classList.add('text-muted');
                        item.querySelector('h6').classList.remove('font-weight-bold');
                        item.querySelector('h6').classList.add('text-muted');
                    });
                    
                    // Update badge counts
                    updateNotificationBadge();
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal menandai notifikasi', 'error');
            });
        }
    });
}

// Delete single notification
function deleteNotification(notificationId) {
    Swal.fire({
        title: 'Hapus notifikasi?',
        text: 'Notifikasi ini akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const notificationElement = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            
            fetch(`/user/notifikasi/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from DOM with animation
                    notificationElement.style.opacity = '0';
                    notificationElement.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        notificationElement.remove();
                        
                        // Check if empty
                        if (document.querySelectorAll('.notification-item').length === 0) {
                            location.reload();
                        }
                    }, 300);
                    
                    // Update badge counts
                    updateNotificationBadge();
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal menghapus notifikasi', 'error');
            });
        }
    });
}

// Clear all notifications
function clearAllNotifications() {
    Swal.fire({
        title: 'Hapus semua notifikasi?',
        text: 'Semua notifikasi akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/user/notifikasi/clear-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal menghapus notifikasi', 'error');
            });
        }
    });
}

// Refresh notifications
function refreshNotifications() {
    const refreshBtn = event.target.closest('button');
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    refreshBtn.disabled = true;
    
    // Simulate refresh
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Update notification badge
function updateNotificationBadge() {
    // Update badge in navbar
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        fetch('/user/notifikasi/unread-count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }
}

// Auto-mark as read when clicked (except actions)
document.addEventListener('click', function(e) {
    const notificationItem = e.target.closest('.notification-item');
    const actionButton = e.target.closest('button, .dropdown-item, a');
    
    if (notificationItem && !actionButton) {
        const notificationId = notificationItem.dataset.id;
        
        // Check if already read
        if (!notificationItem.classList.contains('unread')) return;
        
        // Mark as read
        markAsRead(notificationId);
        
        // Navigate if has link
        const link = notificationItem.querySelector('a[href]');
        if (link && !e.target.closest('.dropdown')) {
            e.preventDefault();
            window.location.href = link.href;
        }
    }
});

// Real-time notification updates
function checkNewNotifications() {
    fetch('/user/notifikasi/latest')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                // Show notification count
                updateNotificationBadge();
                
                // Show desktop notification if supported
                if ('Notification' in window && Notification.permission === 'granted') {
                    data.notifications.forEach(notif => {
                        new Notification(notif.judul, {
                            body: notif.pesan,
                            icon: '/favicon.ico'
                        });
                    });
                }
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('Notification permission granted');
            }
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    requestNotificationPermission();
    
    // Check for new notifications every 30 seconds
    setInterval(checkNewNotifications, 30000);
    
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });
    
    // Add animation to notification items
    document.querySelectorAll('.notification-item').forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
});

// Notification settings
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Save settings to localStorage
    const settings = {
        transaksi: document.getElementById('notifTransaksi').checked,
        sewa: document.getElementById('notifSewa').checked,
        denda: document.getElementById('notifDenda').checked,
        promo: document.getElementById('notifPromo').checked,
        email: document.getElementById('emailNotif').checked
    };
    
    localStorage.setItem('notificationSettings', JSON.stringify(settings));
    
    Toast.fire({
        icon: 'success',
        title: 'Pengaturan berhasil disimpan'
    });
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
});

// Load saved settings
window.addEventListener('load', function() {
    const savedSettings = localStorage.getItem('notificationSettings');
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        document.getElementById('notifTransaksi').checked = settings.transaksi;
        document.getElementById('notifSewa').checked = settings.sewa;
        document.getElementById('notifDenda').checked = settings.denda;
        document.getElementById('notifPromo').checked = settings.promo;
        document.getElementById('emailNotif').checked = settings.email;
    }
});
</script>
@endpush