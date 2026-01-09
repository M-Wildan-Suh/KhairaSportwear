@extends('user.layouts.app')

@section('title', 'Notifikasi - SportWear')

@section('content')
<div class="py-8">
    <!-- Breadcrumb -->
    <div class="container mb-6">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <span class="text-primary font-medium">Notifikasi</span>
        </nav>
    </div>

    <div class="container">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:col-span-1" data-aos="fade-right">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-32">
                    <div class="p-6">
                        <!-- Header -->
                        <h3 class="font-semibold text-gray-900 text-lg mb-6">Filter Notifikasi</h3>
                        
                        <!-- Stats -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total</span>
                                <span class="px-2 py-1 bg-primary text-white text-xs font-medium rounded-full">{{ $totalCount }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Belum Dibaca</span>
                                <span class="px-2 py-1 bg-red-500 text-white text-xs font-medium rounded-full">{{ $unreadCount }}</span>
                            </div>
                        </div>
                        
                        <!-- Filter by Type -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Tipe Notifikasi</h4>
                            <div class="space-y-2">
                                <a href="{{ route('user.notifikasi.index') }}" 
                                   class="flex items-center justify-between px-4 py-3 rounded-lg {{ !request('type') && request('read') !== 'false' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-inbox"></i>
                                        Semua
                                    </span>
                                    <span class="px-2 py-1 bg-primary text-white text-xs rounded-full">{{ $typeCounts['all'] }}</span>
                                </a>
                                
                                <a href="{{ route('user.notifikasi.index', ['read' => 'false']) }}" 
                                   class="flex items-center justify-between px-4 py-3 rounded-lg {{ request('read') === 'false' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-envelope"></i>
                                        Belum Dibaca
                                    </span>
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">{{ $typeCounts['unread'] }}</span>
                                </a>
                                
                                <a href="{{ route('user.notifikasi.index', ['type' => 'transaksi']) }}" 
                                   class="flex items-center justify-between px-4 py-3 rounded-lg {{ request('type') === 'transaksi' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-shopping-cart"></i>
                                        Transaksi
                                    </span>
                                    <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">{{ $typeCounts['transaksi'] }}</span>
                                </a>
                                
                                <a href="{{ route('user.notifikasi.index', ['type' => 'sewa']) }}" 
                                   class="flex items-center justify-between px-4 py-3 rounded-lg {{ request('type') === 'sewa' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        Sewa
                                    </span>
                                    <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">{{ $typeCounts['sewa'] }}</span>
                                </a>
                                
                                <a href="{{ route('user.notifikasi.index', ['type' => 'denda']) }}" 
                                   class="flex items-center justify-between px-4 py-3 rounded-lg {{ request('type') === 'denda' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Denda
                                    </span>
                                    <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-full">{{ $typeCounts['denda'] }}</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="space-y-3">
                            @if($unreadCount > 0)
                            <button onclick="markAllAsRead()" 
                                    class="w-full px-4 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-check-double"></i>
                                <span>Tandai Semua Dibaca</span>
                            </button>
                            @endif
                            
                            @if($totalCount > 0)
                            <button onclick="clearAllNotifications()" 
                                    class="w-full px-4 py-3 border border-red-500 text-red-500 font-medium rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>
                                <span>Hapus Semua</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6" data-aos="fade-left">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-1">Notifikasi</h1>
                        <p class="text-gray-600">
                            @if(request('type'))
                            Filter: {{ ucfirst(request('type')) }}
                            @elseif(request('read') === 'false')
                            Filter: Belum Dibaca
                            @else
                            Semua Notifikasi
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button onclick="refreshNotifications()" 
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-sync-alt text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Notifications List -->
                @if($notifikasis->count() > 0)
                <div class="space-y-3" data-aos="fade-up">
                    @foreach($notifikasis as $notifikasi)
                    <div class="notification-item bg-white rounded-xl border border-gray-200 hover:shadow-md transition-all duration-300 {{ $notifikasi->dibaca ? '' : 'border-l-4 border-l-primary' }}"
                         data-id="{{ $notifikasi->id }}">
                        <div class="p-6">
                            <div class="flex gap-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full {{ $notifikasi->dibaca ? 'bg-gray-100' : 'bg-primary/10' }} flex items-center justify-center">
                                        <i class="{{ $notifikasi->tipe_icon }} {{ $notifikasi->dibaca ? 'text-gray-600' : 'text-primary' }}"></i>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="{{ $notifikasi->dibaca ? 'text-gray-900' : 'font-semibold text-gray-900' }}">{{ $notifikasi->judul }}</h4>
                                            <p class="text-gray-500 text-sm mt-1">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ $notifikasi->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        
                                        <div class="relative">
                                            <button onclick="toggleNotificationMenu('{{ $notifikasi->id }}')" 
                                                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            
                                            <div id="menu-{{ $notifikasi->id }}" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-10">
                                                @if(!$notifikasi->dibaca)
                                                <button onclick="markAsRead('{{ $notifikasi->id }}')" 
                                                        class="w-full text-left px-4 py-3 text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                    <i class="fas fa-check"></i>
                                                    <span>Tandai Dibaca</span>
                                                </button>
                                                @endif
                                                <button onclick="deleteNotification('{{ $notifikasi->id }}')" 
                                                        class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                    <i class="fas fa-trash"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ $notifikasi->pesan }}</p>
                                    
                                    @if($notifikasi->link)
                                    <div>
                                        <a href="{{ $notifikasi->link }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 border border-primary text-primary font-medium rounded-lg hover:bg-primary/5 transition-colors">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>Lihat Detail</span>
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
                <div class="mt-8">
                    {{ $notifikasis->withQueryString()->onEachSide(1)->links('vendor.pagination.custom') }}
                </div>
                @endif
                
                @else
                <!-- Empty State -->
                <div class="text-center py-12" data-aos="fade-up">
                    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bell-slash text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-600 mb-6">
                        @if(request('type') || request('read'))
                        Tidak ada notifikasi dengan filter ini
                        @else
                        Belum ada notifikasi untuk Anda
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('user.dashboard') }}" 
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-home"></i>
                            <span>Kembali ke Dashboard</span>
                        </a>
                        
                        <a href="{{ route('user.produk.index') }}" 
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-primary text-primary font-medium rounded-lg hover:bg-primary/5 transition-colors">
                            <i class="fas fa-store"></i>
                            <span>Lihat Produk</span>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div id="settingsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeSettingsModal()"></div>
        <div class="relative bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Pengaturan Notifikasi</h3>
                <button onclick="closeSettingsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="settingsForm" class="space-y-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Jenis Notifikasi</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" checked>
                            <span class="ml-3 text-gray-700">Notifikasi Transaksi</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" checked>
                            <span class="ml-3 text-gray-700">Notifikasi Sewa</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" checked>
                            <span class="ml-3 text-gray-700">Notifikasi Denda</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" checked>
                            <span class="ml-3 text-gray-700">Promo & Update</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Email Notifikasi</h4>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" checked>
                        <span class="ml-3 text-gray-700">Kirim notifikasi ke email</span>
                    </label>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                    Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.sticky-top {
    position: sticky;
    top: 2rem;
}

.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateY(-2px);
}

/* Animation for new notifications */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.notification-item {
    animation: slideIn 0.3s ease-out;
}

/* Custom pagination */
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
}

.pagination li {
    margin: 0 2px;
}

.pagination a,
.pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination .active span {
    background-color: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination a:hover:not(.disabled) {
    background-color: rgba(26, 54, 93, 0.1);
    color: var(--primary);
}

.pagination .disabled span {
    color: #a0aec0;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script>
// Toggle notification menu
function toggleNotificationMenu(notificationId) {
    const menu = document.getElementById(`menu-${notificationId}`);
    const isVisible = !menu.classList.contains('hidden');
    
    // Hide all other menus
    document.querySelectorAll('[id^="menu-"]').forEach(m => {
        m.classList.add('hidden');
    });
    
    // Toggle current menu
    if (!isVisible) {
        menu.classList.remove('hidden');
        
        // Close on click outside
        setTimeout(() => {
            const handleClickOutside = (e) => {
                if (!menu.contains(e.target) && !e.target.closest(`[onclick*="${notificationId}"]`)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', handleClickOutside);
                }
            };
            document.addEventListener('click', handleClickOutside);
        }, 100);
    }
}

// Mark notification as read
async function markAsRead(notificationId) {
    const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
    
    try {
        const response = await fetch(`/user/notifikasi/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update UI
            item.classList.remove('border-l-primary');
            item.querySelector('.w-12.h-12').classList.remove('bg-primary/10');
            item.querySelector('.w-12.h-12').classList.add('bg-gray-100');
            item.querySelector('.w-12.h-12 i').classList.remove('text-primary');
            item.querySelector('.w-12.h-12 i').classList.add('text-gray-600');
            item.querySelector('h4').classList.remove('font-semibold');
            
            // Update badge count
            updateNotificationBadge();
            
            showToast('success', 'Notifikasi telah ditandai dibaca');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Gagal menandai notifikasi');
    }
}

// Mark all as read
async function markAllAsRead() {
    if (!confirm('Tandai semua notifikasi sebagai dibaca?')) return;
    
    try {
        const response = await fetch('/user/notifikasi/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update all notifications in UI
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('border-l-primary');
                item.querySelector('.w-12.h-12').classList.remove('bg-primary/10');
                item.querySelector('.w-12.h-12').classList.add('bg-gray-100');
                item.querySelector('.w-12.h-12 i').classList.remove('text-primary');
                item.querySelector('.w-12.h-12 i').classList.add('text-gray-600');
                item.querySelector('h4').classList.remove('font-semibold');
            });
            
            // Update badge counts
            updateNotificationBadge();
            
            showToast('success', 'Semua notifikasi telah ditandai dibaca');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Gagal menandai notifikasi');
    }
}

// Delete single notification
async function deleteNotification(notificationId) {
    if (!confirm('Hapus notifikasi ini?')) return;
    
    const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
    
    try {
        const response = await fetch(`/user/notifikasi/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Animate removal
            item.style.opacity = '0';
            item.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                item.remove();
                
                // Check if empty
                if (document.querySelectorAll('.notification-item').length === 0) {
                    window.location.reload();
                }
            }, 300);
            
            // Update badge count
            updateNotificationBadge();
            
            showToast('success', 'Notifikasi telah dihapus');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Gagal menghapus notifikasi');
    }
}

// Clear all notifications
async function clearAllNotifications() {
    if (!confirm('Hapus semua notifikasi?')) return;
    
    try {
        const response = await fetch('/user/notifikasi/clear-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Gagal menghapus notifikasi');
    }
}

// Refresh notifications
function refreshNotifications() {
    const button = event.currentTarget;
    const icon = button.querySelector('i');
    
    icon.classList.add('fa-spin');
    button.disabled = true;
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Update notification badge
async function updateNotificationBadge() {
    try {
        const response = await fetch('/user/notifikasi/unread-count');
        const data = await response.json();
        
        if (data.success) {
            const badges = document.querySelectorAll('.notification-badge');
            badges.forEach(badge => {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        }
    } catch (error) {
        console.error('Error updating badge:', error);
    }
}

// Auto-mark as read when clicked
document.addEventListener('click', function(e) {
    const item = e.target.closest('.notification-item');
    const actionButton = e.target.closest('button, a');
    
    if (item && !actionButton && item.classList.contains('border-l-primary')) {
        const notificationId = item.dataset.id;
        markAsRead(notificationId);
        
        // Navigate if has link
        const link = item.querySelector('a[href]');
        if (link && !e.target.closest('[id^="menu-"]')) {
            e.preventDefault();
            window.location.href = link.href;
        }
    }
});

// Settings modal
function openSettingsModal() {
    document.getElementById('settingsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSettingsModal() {
    document.getElementById('settingsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Save settings
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const settings = {
        transaksi: this.querySelectorAll('input[type="checkbox"]')[0].checked,
        sewa: this.querySelectorAll('input[type="checkbox"]')[1].checked,
        denda: this.querySelectorAll('input[type="checkbox"]')[2].checked,
        promo: this.querySelectorAll('input[type="checkbox"]')[3].checked,
        email: this.querySelectorAll('input[type="checkbox"]')[4].checked
    };
    
    localStorage.setItem('notificationSettings', JSON.stringify(settings));
    showToast('success', 'Pengaturan berhasil disimpan');
    closeSettingsModal();
});

// Load saved settings
window.addEventListener('load', function() {
    const saved = localStorage.getItem('notificationSettings');
    if (saved) {
        const settings = JSON.parse(saved);
        const checkboxes = document.querySelectorAll('#settingsForm input[type="checkbox"]');
        checkboxes[0].checked = settings.transaksi;
        checkboxes[1].checked = settings.sewa;
        checkboxes[2].checked = settings.denda;
        checkboxes[3].checked = settings.promo;
        checkboxes[4].checked = settings.email;
    }
});

// Toast notification
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('animate-slide-out-right');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Real-time updates
if (window.Echo) {
    window.Echo.private('user.{{ auth()->id() }}')
        .notification((notification) => {
            updateNotificationBadge();
            
            // Show desktop notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(notification.title, {
                    body: notification.message,
                    icon: '/favicon.ico'
                });
            }
        });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // AOS initialization
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    }
});

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSettingsModal();
    }
});
</script>
@endpush