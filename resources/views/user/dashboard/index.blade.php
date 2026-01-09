@extends('user.layouts.app')

@section('title', 'Dashboard - SportWear')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary to-secondary mb-8 p-8 text-white" data-aos="fade-down">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-2/3">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Selamat Datang, <span class="text-yellow-300">{{ $user->name }}</span>! 👋</h1>
                <p class="text-lg opacity-90 mb-0">Apa yang ingin Anda lakukan hari ini? Mari mulai perjalanan olahraga Anda!</p>
            </div>
            <div class="md:w-1/3 text-right mt-6 md:mt-0">
                <div class="animate-float">
                    <i class="fas fa-running text-6xl opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-primary transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center">
                <div class="bg-primary bg-opacity-10 rounded-xl p-3 mr-4 transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-shopping-cart text-primary text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-1">{{ $totalTransactions }}</h3>
                    <p class="text-gray-600 text-sm">Total Transaksi</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-green-500 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-xl p-3 mr-4 transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-wallet text-green-500 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h3>
                    <p class="text-gray-600 text-sm">Total Pengeluaran</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-blue-500 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-xl p-3 mr-4 transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-calendar-alt text-blue-500 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-1">{{ $activeRentals }}</h3>
                    <p class="text-gray-600 text-sm">Sewa Aktif</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-yellow-500 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" data-aos="fade-up" data-aos-delay="400">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-xl p-3 mr-4 transition-all duration-300 group-hover:scale-110">
                    <i class="fas fa-shopping-basket text-yellow-500 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-1">{{ $cartCount }}</h3>
                    <p class="text-gray-600 text-sm">Keranjang</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column -->
        <div class="lg:w-2/3">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl shadow-md mb-6" data-aos="fade-up">
                <div class="px-6 pt-6">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-xl font-bold">Transaksi Terbaru</h5>
                        <a href="{{ route('user.transaksi.index') }}" class="btn-primary-outline text-sm px-4 py-2">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    @if($recentTransactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $transaction->kode_transaksi }}</div>
                                        <div class="text-sm text-gray-500">{{ $transaction->tipe == 'penjualan' ? 'Pembelian' : 'Penyewaan' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $transaction->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap font-bold">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @include('components.status-badge', ['status' => $transaction->status])
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('user.transaksi.show', $transaction->id) }}" 
                                           class="btn-primary-outline text-sm px-3 py-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-10">
                        <i class="fas fa-receipt text-5xl text-gray-300 mb-4"></i>
                        <h5 class="text-gray-500 font-medium mb-2">Belum ada transaksi</h5>
                        <p class="text-gray-400 mb-4">Mulai belanja untuk melihat transaksi Anda</p>
                        <a href="{{ route('user.produk.index') }}" class="btn-primary inline-flex items-center">
                            <i class="fas fa-shopping-cart mr-2"></i> Belanja Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Active Rentals -->
            <div class="bg-white rounded-2xl shadow-md" data-aos="fade-up" data-aos-delay="200">
                <div class="px-6 pt-6">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-xl font-bold">Sewa Aktif</h5>
                        <a href="{{ route('user.sewa.aktif') }}" class="btn-primary-outline text-sm px-4 py-2">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    @if($activeRentalsList->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($activeRentalsList as $rental)
                        <div class="border rounded-xl p-4 hover:border-primary hover:shadow-md transition-all duration-300">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h6 class="font-bold text-gray-800">{{ $rental->produk->nama }}</h6>
                                    <small class="text-gray-500">{{ $rental->kode_sewa }}</small>
                                </div>
                                @include('components.status-badge', ['status' => $rental->status])
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Tanggal Kembali:</span>
                                    <span class="text-sm font-medium {{ $rental->sisa_hari < 3 ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $rental->tanggal_kembali_rencana->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Sisa Hari:</span>
                                    <span class="text-sm font-medium {{ $rental->sisa_hari < 3 ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $rental->sisa_hari }} hari
                                    </span>
                                </div>
                            </div>
                            
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                @php
                                    $totalDays = $rental->jumlah_hari;
                                    $remainingDays = $rental->sisa_hari;
                                    $percentage = ($totalDays - $remainingDays) / $totalDays * 100;
                                @endphp
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('user.sewa.show', $rental->id) }}" 
                                   class="btn-primary-outline flex-1 text-sm py-2 flex items-center justify-center">
                                    <i class="fas fa-info-circle mr-1"></i> Detail
                                </a>
                                <button class="btn-success-outline text-sm py-2 flex items-center justify-center" 
                                        data-modal-target="pengembalianModal{{ $rental->id }}">
                                    <i class="fas fa-undo mr-1"></i> Kembalikan
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-10">
                        <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-4"></i>
                        <h5 class="text-gray-500 font-medium mb-2">Tidak ada sewa aktif</h5>
                        <p class="text-gray-400 mb-4">Mulai sewa alat olahraga favorit Anda</p>
                        <a href="{{ route('user.sewa.index') }}" class="btn-primary inline-flex items-center">
                            <i class="fas fa-calendar-plus mr-2"></i> Sewa Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="lg:w-1/3">
            <!-- Notifications -->
            <div class="bg-white rounded-2xl shadow-md mb-6" data-aos="fade-left">
                <div class="px-6 pt-6">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-xl font-bold">Notifikasi</h5>
                        @if($notifications->count() > 0)
                        <form action="{{ route('user.notifikasi.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-secondary-outline text-sm px-4 py-2">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    @if($notifications->count() > 0)
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($notifications as $notification)
                        <a href="{{ $notification->link ?? '#' }}" 
                           class="block px-6 py-4 hover:bg-gray-50 transition-colors notification-item
                                  {{ $notification->dibaca ? '' : 'bg-blue-50' }}"
                           data-id="{{ $notification->id }}">
                            <div class="flex">
                                <div class="mr-4 mt-1">
                                    <i class="{{ $notification->tipe_icon }} text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h6 class="{{ $notification->dibaca ? 'text-gray-700' : 'font-bold text-gray-900' }} mb-1">
                                            {{ $notification->judul }}
                                        </h6>
                                        <small class="text-gray-500 text-xs">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ Str::limit($notification->pesan, 50) }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 text-center">
                        <a href="{{ route('user.notifikasi.index') }}" class="text-primary hover:text-primary-dark font-medium">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                    @else
                    <div class="text-center py-10 px-6">
                        <i class="fas fa-bell-slash text-5xl text-gray-300 mb-4"></i>
                        <h5 class="text-gray-500 font-medium mb-2">Tidak ada notifikasi</h5>
                        <p class="text-gray-400 text-sm">Semua notifikasi sudah Anda baca</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-md mb-6" data-aos="fade-left" data-aos-delay="200">
                <div class="px-6 pt-6">
                    <h5 class="text-xl font-bold mb-6">Aksi Cepat</h5>
                </div>
                <div class="px-6 pb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('user.keranjang.index') }}" 
                           class="btn-outline-primary flex flex-col items-center justify-center p-4 rounded-xl hover:shadow-md transition-all duration-300">
                            <i class="fas fa-shopping-cart text-2xl mb-2"></i>
                            <span>Keranjang</span>
                            @if($cartCount > 0)
                            <span class="mt-1 bg-accent text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                {{ $cartCount }}
                            </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('user.produk.index') }}" 
                           class="btn-outline-success flex flex-col items-center justify-center p-4 rounded-xl hover:shadow-md transition-all duration-300">
                            <i class="fas fa-store text-2xl mb-2"></i>
                            <span>Belanja</span>
                        </a>
                        
                        <a href="{{ route('user.sewa.index') }}" 
                           class="btn-outline-info flex flex-col items-center justify-center p-4 rounded-xl hover:shadow-md transition-all duration-300">
                            <i class="fas fa-calendar-alt text-2xl mb-2"></i>
                            <span>Sewa</span>
                        </a>
                        
                        <a href="{{ route('user.profil.edit') }}" 
                           class="btn-outline-warning flex flex-col items-center justify-center p-4 rounded-xl hover:shadow-md transition-all duration-300">
                            <i class="fas fa-user text-2xl mb-2"></i>
                            <span>Profile</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Returns -->
            @if($upcomingReturns->count() > 0)
            <div class="bg-white rounded-2xl shadow-md" data-aos="fade-left" data-aos-delay="300">
                <div class="px-6 pt-6">
                    <h5 class="text-xl font-bold mb-6">Pengembalian Mendatang</h5>
                </div>
                <div class="px-6 pb-6">
                    <div class="space-y-3">
                        @foreach($upcomingReturns as $rental)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <h6 class="text-sm font-medium text-gray-800">{{ $rental->produk->nama }}</h6>
                                <p class="text-xs text-gray-500">Kembali: {{ $rental->tanggal_kembali_rencana->format('d/m') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $rental->sisa_hari < 2 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $rental->sisa_hari }} hari
                            </span>
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
<div id="pengembalianModal{{ $rental->id }}" 
     class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden transition-opacity duration-300">
    <div class="bg-white rounded-2xl w-full max-w-2xl mx-4">
        <div class="px-6 py-4 border-b">
            <h3 class="text-xl font-bold">Pengembalian {{ $rental->produk->nama }}</h3>
            <button type="button" class="modal-close absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-8">
            <p class="text-gray-600">Fitur pengembalian akan diimplementasi di fase berikutnya.</p>
        </div>
        <div class="px-6 py-4 border-t flex justify-end space-x-3">
            <button type="button" class="btn-secondary modal-close">Tutup</button>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('styles')
<style>
.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.btn-primary {
    @apply bg-primary text-white px-6 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition-colors duration-300;
}

.btn-primary-outline {
    @apply border border-primary text-primary px-4 py-2 rounded-lg font-medium hover:bg-primary hover:text-white transition-colors duration-300;
}

.btn-secondary-outline {
    @apply border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-300;
}

.btn-success-outline {
    @apply border border-green-500 text-green-500 px-4 py-2 rounded-lg font-medium hover:bg-green-500 hover:text-white transition-colors duration-300;
}

.btn-outline-primary {
    @apply border-2 border-primary text-primary p-4 rounded-xl font-medium hover:bg-primary hover:text-white transition-all duration-300;
}

.btn-outline-success {
    @apply border-2 border-green-500 text-green-500 p-4 rounded-xl font-medium hover:bg-green-500 hover:text-white transition-all duration-300;
}

.btn-outline-info {
    @apply border-2 border-blue-500 text-blue-500 p-4 rounded-xl font-medium hover:bg-blue-500 hover:text-white transition-all duration-300;
}

.btn-outline-warning {
    @apply border-2 border-yellow-500 text-yellow-500 p-4 rounded-xl font-medium hover:bg-yellow-500 hover:text-white transition-all duration-300;
}

.modal {
    z-index: 9999;
}

.notification-item {
    position: relative;
}

.notification-item:hover {
    padding-left: 1.75rem;
}

.notification-item:hover::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background-color: var(--primary);
    border-radius: 0 2px 2px 0;
}
</style>
@endpush

@push('scripts')
<script>
// Modal functionality
document.querySelectorAll('[data-modal-target]').forEach(button => {
    button.addEventListener('click', () => {
        const modalId = button.getAttribute('data-modal-target');
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('opacity-100'), 10);
        }
    });
});

document.querySelectorAll('.modal-close, .modal').forEach(element => {
    element.addEventListener('click', (e) => {
        if (e.target === element || e.target.classList.contains('modal-close')) {
            element.closest('.modal').classList.add('hidden');
            element.closest('.modal').classList.remove('opacity-100');
        }
    });
});

// Mark notification as read when clicked
document.querySelectorAll('.notification-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (!this.href || this.href === '#') {
            e.preventDefault();
        }
        
        const notificationId = this.dataset.id;
        const notificationElement = this;
        
        fetch(`/user/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                notificationElement.classList.remove('bg-blue-50');
                notificationElement.querySelector('h6').classList.remove('font-bold', 'text-gray-900');
                notificationElement.querySelector('h6').classList.add('text-gray-700');
                
                // Update notification badge count if exists
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
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    });
});

// Progress bar animation on load
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.h-2 > div');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
    
    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            once: true,
            offset: 100
        });
    }
});

// Hover effects for cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.hover\\:shadow-lg');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            const icon = card.querySelector('.group-hover\\:scale-110');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });
        
        card.addEventListener('mouseleave', () => {
            const icon = card.querySelector('.group-hover\\:scale-110');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
});
</script>
@endpush