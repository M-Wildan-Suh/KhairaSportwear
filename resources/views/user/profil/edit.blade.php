@extends('user.layouts.app')

@section('title', 'Edit Profil - SportWear')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 lg:px-8 mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        <a href="{{ route('user.dashboard') }}" class="ml-3 text-sm font-medium text-gray-700 hover:text-primary">
                            Dashboard
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        <span class="ml-3 text-sm font-medium text-primary">Profil</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6" data-aos="fade-right">
                        <div class="p-6 text-center">
                            <!-- Avatar -->
                            <div class="relative mx-auto mb-4">
                                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-white shadow-lg">
                                    <img src="{{ $user->avatar_url }}" 
                                         alt="{{ $user->name }}"
                                         id="avatarPreview"
                                         class="w-full h-full object-cover">
                                </div>
                                <button onclick="openAvatarModal()"
                                        class="absolute bottom-2 right-2 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary-dark transition-colors duration-200">
                                    <i class="fas fa-camera text-sm"></i>
                                </button>
                            </div>
                            
                            <!-- User Info -->
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h3>
                            <p class="text-gray-600 text-sm mb-3">{{ $user->email }}</p>
                            
                            <!-- Member Since -->
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                <i class="fas fa-user-plus text-xs"></i>
                                <span>Member sejak {{ $statistics['member_since'] }}</span>
                            </div>
                        </div>
                        
                        <!-- Navigation -->
                        <div class="border-t border-gray-200">
                            <nav class="p-4">
                                <ul class="space-y-2">
                                    <li>
                                        <a href="{{ route('user.profil.edit') }}" 
                                           class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl font-medium">
                                            <i class="fas fa-user"></i>
                                            <span>Profil Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('user.profil.security') }}" 
                                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-medium transition-colors duration-200">
                                            <i class="fas fa-shield-alt"></i>
                                            <span>Keamanan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('user.sewa.aktif') }}" 
                                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-medium transition-colors duration-200">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Sewa Aktif</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('user.notifikasi.index') }}" 
                                           class="flex items-center justify-between gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-medium transition-colors duration-200">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-bell"></i>
                                                <span>Notifikasi</span>
                                            </div>
                                            @if($user->getUnreadNotificationsCount() > 0)
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">
                                                {{ $user->getUnreadNotificationsCount() }}
                                            </span>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6" data-aos="fade-right" data-aos-delay="100">
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Akun</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Tipe Akun</span>
                                <span class="font-semibold text-gray-900">{{ $user->role === 'admin' ? 'Administrator' : 'Pelanggan' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Email Status</span>
                                <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Terverifikasi</span>
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Keamanan</span>
                                <span class="text-gray-900">{{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4" data-aos="fade-left">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Profil Saya</h1>
                        <p class="text-gray-600">Kelola informasi profil dan akun Anda</p>
                    </div>
                    <a href="{{ route('user.dashboard') }}" 
                       class="flex items-center gap-2 px-6 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-200">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_transactions'] }}</p>
                                <p class="text-sm text-gray-600">Total Transaksi</p>
                            </div>
                        </div>
                    </div>
                    

                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-check text-cyan-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $statistics['active_rentals'] }}</p>
                                <p class="text-sm text-gray-600">Sewa Aktif</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-flag-checkered text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $statistics['completed_rentals'] }}</p>
                                <p class="text-sm text-gray-600">Sewa Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <!-- Form Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-edit text-primary"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Informasi Pribadi</h2>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <div class="p-6">
                        <form id="profileForm">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                               id="name"
                                               name="name"
                                               value="{{ $user->name }}"
                                               class="pl-10 w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                               required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" 
                                               id="email"
                                               name="email"
                                               value="{{ $user->email }}"
                                               class="pl-10 w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                               required>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor Telepon
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input type="tel" 
                                               id="phone"
                                               name="phone"
                                               value="{{ $user->phone }}"
                                               class="pl-10 w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                               required>
                                    </div>
                                </div>

                                <!-- Member Since -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Bergabung
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                               value="Member sejak {{ $statistics['member_since'] }}"
                                               class="pl-10 w-full rounded-xl border-gray-300 bg-gray-50"
                                               disabled>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Alamat Lengkap
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute top-3 left-3 pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        </div>
                                        <textarea id="address"
                                                  name="address"
                                                  rows="3"
                                                  class="pl-10 w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                                  required>{{ $user->address }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                                <button type="reset" 
                                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-redo"></i>
                                    <span>Reset</span>
                                </button>
                                <button type="submit" 
                                        id="saveProfileBtn"
                                        class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <!-- Activity Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-purple-600"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900">Aktivitas Terbaru</h2>
                            </div>
                            <a href="{{ route('user.transaksi.index') }}" 
                               class="text-sm text-primary font-semibold hover:text-primary-dark transition-colors duration-200">
                                Lihat Semua
                            </a>
                        </div>
                    </div>

                    <!-- Activity Body -->
                    <div class="p-6">
                        @if($recentActivities->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentActivities as $activity)
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                <!-- Activity Icon -->
                                <div class="w-12 h-12 rounded-xl {{ $activity->tipe === 'penjualan' ? 'bg-green-100' : 'bg-blue-100' }} flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-{{ $activity->tipe === 'penjualan' ? 'shopping-cart' : 'calendar-alt' }} {{ $activity->tipe === 'penjualan' ? 'text-green-600' : 'text-blue-600' }}"></i>
                                </div>
                                
                                <!-- Activity Details -->
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $activity->kode_transaksi }}</h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $activity->tipe === 'penjualan' ? 'Pembelian' : 'Penyewaan' }} • 
                                                {{ $activity->created_at->format('d F Y, H:i') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-bold text-gray-900">
                                                Rp {{ number_format($activity->total_bayar, 0, ',', '.') }}
                                            </span>
                                            {!! $activity->status_badge !!}
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Items -->
                                    @if($activity->detailTransaksis->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($activity->detailTransaksis->take(3) as $detail)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-200 text-gray-800">
                                                {{ $detail->produk->nama }} ({{ $detail->quantity }})
                                            </span>
                                            @endforeach
                                            @if($activity->detailTransaksis->count() > 3)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-200 text-gray-800">
                                                +{{ $activity->detailTransaksis->count() - 3 }} lainnya
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-history text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Belum Ada Aktivitas</h3>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                                Mulai berbelanja atau menyewa alat olahraga untuk melihat aktivitas Anda di sini
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('user.produk.index') }}" 
                                   class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200">
                                    <i class="fas fa-store mr-2"></i>
                                    Mulai Belanja
                                </a>
                                <a href="{{ route('user.sewa.index') }}" 
                                   class="px-6 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-200">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Sewa Alat
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Modal -->
<div id="avatarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-4 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Ubah Foto Profil</h3>
                    <button onclick="closeAvatarModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <!-- Current Avatar Preview -->
                <div class="text-center mb-6">
                    <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-white shadow-lg mb-4">
                        <img src="{{ $user->avatar_url }}" 
                             alt="Avatar Preview" 
                             id="avatarModalPreview"
                             class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm text-gray-600">Foto profil Anda saat ini</p>
                </div>
                
                <!-- Upload Form -->
                <form id="avatarForm">
                    @csrf
                    
                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Pilih Foto Baru
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-primary transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mx-auto mb-3"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="avatar" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                        <span>Upload file</span>
                                        <input id="avatar" 
                                               name="avatar" 
                                               type="file" 
                                               class="sr-only" 
                                               accept="image/*">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG maksimal 2MB
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Tips foto profil yang baik:</p>
                                <ul class="space-y-1">
                                    <li>• Gunakan foto wajah yang jelas</li>
                                    <li>• Format PNG atau JPG</li>
                                    <li>• Ukuran maksimal 2MB</li>
                                    <li>• Rasio 1:1 (persegi)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        @if($user->avatar)
                        <button type="button" 
                                onclick="deleteAvatar()"
                                class="flex-1 px-4 py-3 border-2 border-red-600 text-red-600 font-semibold rounded-xl hover:bg-red-600 hover:text-white transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-trash"></i>
                            <span>Hapus Foto</span>
                        </button>
                        @endif
                        <button type="submit" 
                                id="uploadAvatarBtn"
                                class="flex-1 px-4 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-upload"></i>
                            <span>Upload Foto</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

[data-aos] {
    animation-duration: 0.6s;
    animation-timing-function: ease-out;
    animation-fill-mode: both;
}

/* Sticky sidebar */
.sticky {
    position: sticky;
    z-index: 20;
}

/* Avatar hover effect */
.avatar-edit-btn:hover {
    transform: scale(1.1);
}

/* Form input focus styles */
input:focus, textarea:focus {
    outline: none;
    ring: 2px;
}

/* File upload hover effect */
.border-dashed:hover {
    border-color: #2B6CB0;
}

/* Activity item hover effect */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading spinner */
.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            once: true,
            offset: 100
        });
    }
});

// Profile Form Submission
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('saveProfileBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = `
        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Menyimpan...</span>
    `;
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("user.profil.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update sidebar user info
            const userNameElements = document.querySelectorAll('h3.text-xl, .profile-nav h3');
            userNameElements.forEach(el => {
                if (el.textContent.includes('{{ $user->name }}')) {
                    el.textContent = el.textContent.replace('{{ $user->name }}', data.user.name);
                }
            });
            
            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            text: error.message || 'Terjadi kesalahan saat menyimpan profil',
            confirmButtonColor: '#2B6CB0'
        });
    } finally {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
});

// Avatar Modal Functions
function openAvatarModal() {
    document.getElementById('avatarModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAvatarModal() {
    document.getElementById('avatarModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetAvatarForm();
}

function resetAvatarForm() {
    document.getElementById('avatarForm').reset();
}

// Avatar File Preview
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('avatarModalPreview');
    
    if (!file) return;
    
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'warning',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 2MB',
            confirmButtonColor: '#2B6CB0'
        });
        this.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
});

// Avatar Form Submission
document.getElementById('avatarForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('uploadAvatarBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = `
        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Mengupload...</span>
    `;
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("user.profil.update-avatar") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update all avatar previews
            const avatarPreviews = document.querySelectorAll('#avatarPreview, #avatarModalPreview');
            avatarPreviews.forEach(preview => {
                preview.src = data.avatar_url;
            });
            
            // Update navbar avatar
            const navbarAvatar = document.querySelector('.user-avatar-sport');
            if (navbarAvatar) {
                navbarAvatar.style.backgroundImage = `url('${data.avatar_url}')`;
                navbarAvatar.style.backgroundSize = 'cover';
            }
            
            // Close modal
            closeAvatarModal();
            
            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Foto Profil Diperbarui',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: 'Upload Gagal',
            text: error.message || 'Terjadi kesalahan saat mengupload foto',
            confirmButtonColor: '#2B6CB0'
        });
    } finally {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
});

// Delete Avatar
async function deleteAvatar() {
    const result = await Swal.fire({
        title: 'Hapus Foto Profil?',
        text: 'Foto profil akan dihapus permanen dan diganti dengan foto default',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#4B5563',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch('{{ route("user.profil.delete-avatar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update all avatar previews
                const avatarPreviews = document.querySelectorAll('#avatarPreview, #avatarModalPreview');
                avatarPreviews.forEach(preview => {
                    preview.src = data.avatar_url;
                });
                
                // Update navbar avatar
                const navbarAvatar = document.querySelector('.user-avatar-sport');
                if (navbarAvatar) {
                    navbarAvatar.style.backgroundImage = '';
                    navbarAvatar.style.backgroundColor = '#2B6CB0';
                }
                
                // Close modal
                closeAvatarModal();
                
                await Swal.fire({
                    icon: 'success',
                    title: 'Foto Dihapus',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            await Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: 'Terjadi kesalahan',
                confirmButtonColor: '#2B6CB0'
            });
        }
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAvatarModal();
    }
});

// Close modal on background click
document.getElementById('avatarModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAvatarModal();
    }
});

// Form validation
document.querySelectorAll('#profileForm input, #profileForm textarea').forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value.trim() === '' && this.required) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });
});

// Format phone number
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0) {
        value = value.match(/.{1,4}/g).join('-');
    }
    e.target.value = value;
});
</script>
@endpush