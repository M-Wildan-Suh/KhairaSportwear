@extends('user.layouts.app')

@section('title', 'Keamanan Akun - SportWear')

@section('content')
<div class="py-8">
    <!-- Breadcrumb -->
    <div class="container mb-6">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <a href="{{ route('user.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <a href="{{ route('user.profil.edit') }}" class="hover:text-primary transition-colors">Profil</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <span class="text-primary font-medium">Keamanan</span>
        </nav>
    </div>

    <div class="container">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1" data-aos="fade-right">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-32">
                    <!-- User Profile -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col items-center">
                            <div class="relative mb-4">
                                <img src="{{ $user->avatar_url }}" 
                                     alt="{{ $user->name }}"
                                     class="w-24 h-24 object-cover rounded-full border-4 border-white shadow">
                                <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="p-4">
                        <nav class="space-y-1">
                            <a href="{{ route('user.profil.edit') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-primary text-sm"></i>
                                </div>
                                <span class="font-medium">Profil Saya</span>
                            </a>
                            
                            <a href="{{ route('user.profil.security') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-primary transition-colors">
                                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-white text-sm"></i>
                                </div>
                                <span class="font-medium">Keamanan Akun</span>
                            </a>
                            
                            <a href="{{ route('user.orders.index') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shopping-bag text-primary text-sm"></i>
                                </div>
                                <span class="font-medium">Pesanan Saya</span>
                            </a>
                            
                            <a href="{{ route('user.wishlist') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-heart text-primary text-sm"></i>
                                </div>
                                <span class="font-medium">Wishlist</span>
                            </a>
                            
                            <a href="{{ route('logout') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-sign-out-alt text-red-600 text-sm"></i>
                                </div>
                                <span class="font-medium">Logout</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-3" data-aos="fade-up">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-primary"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Keamanan Akun</h2>
                                <p class="text-gray-600">Kelola keamanan akun dan kata sandi Anda</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                <span class="text-green-800 font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-red-600 mr-3 mt-1"></i>
                                <div>
                                    <p class="font-medium text-red-800 mb-2">Terjadi kesalahan:</p>
                                    <ul class="text-red-700 text-sm list-disc pl-4">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Change Password Form -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubah Kata Sandi</h3>
                            <form action="{{ route('user.profil.update-password') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Kata Sandi Saat Ini <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               name="current_password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary pr-12"
                                               required>
                                        <button type="button" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Kata Sandi Baru <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="password" 
                                                   name="new_password"
                                                   id="new_password"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary pr-12"
                                                   required>
                                            <button type="button" 
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('new_password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <div id="password-strength" class="mt-2"></div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="password" 
                                                   name="new_password_confirmation"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary pr-12"
                                                   required>
                                            <button type="button" 
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-blue-800 mb-1">Tips Kata Sandi Aman:</p>
                                            <ul class="text-sm text-blue-700 space-y-1">
                                                <li>• Minimal 8 karakter</li>
                                                <li>• Kombinasi huruf besar dan kecil</li>
                                                <li>• Sertakan angka dan simbol</li>
                                                <li>• Hindari kata sandi yang mudah ditebak</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" 
                                        class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors flex items-center gap-2">
                                    <i class="fas fa-save"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Two Factor Authentication -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Autentikasi Dua Faktor (2FA)</h3>
                            <div class="bg-gray-50 rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="font-medium text-gray-900 mb-1">Autentikasi Dua Faktor</h4>
                                            <p class="text-gray-600 text-sm">Tambah lapisan keamanan ekstra ke akun Anda</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="px-3 py-1 bg-gray-200 text-gray-700 text-sm font-medium rounded-full">
                                                Nonaktif
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" 
                                                       id="twoFactorSwitch" 
                                                       class="sr-only peer"
                                                       disabled>
                                                <label for="twoFactorSwitch" 
                                                       class="w-12 h-6 bg-gray-300 rounded-full cursor-not-allowed peer-checked:bg-green-500 transition-colors">
                                                    <span class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 transition-transform peer-checked:translate-x-6"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-500 text-sm mt-4">
                                        <i class="fas fa-clock mr-2"></i>
                                        Fitur ini akan segera tersedia.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Session Management -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sesi Aktif</h3>
                            <div class="bg-gray-50 rounded-lg border border-gray-200 mb-4">
                                <div class="p-6">
                                    <div class="space-y-4">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-1">Sesi Saat Ini</h4>
                                                <p class="text-gray-600 text-sm">Perangkat ini • {{ request()->ip() }}</p>
                                            </div>
                                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full mt-2 md:mt-0">
                                                Aktif
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <h4 class="font-medium text-gray-900 mb-1">Waktu Login Terakhir</h4>
                                            <p class="text-gray-600 text-sm">
                                                {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum tersedia' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    onclick="showLogoutAllModal()"
                                    class="px-6 py-3 border border-red-500 text-red-500 font-medium rounded-lg hover:bg-red-50 transition-colors flex items-center gap-2">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Keluar dari Semua Perangkat</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout All Modal -->
<div id="logoutAllModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeLogoutAllModal()"></div>
        <div class="relative bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Konfirmasi</h3>
                <button onclick="closeLogoutAllModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <p class="text-gray-700">Apakah Anda yakin ingin keluar dari semua perangkat? Anda akan diminta untuk login kembali di semua perangkat.</p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeLogoutAllModal()" 
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <form action="{{ route('user.profil.logout-all') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-3 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar Semua</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

@endsection

@push('styles')
<style>
.sticky-top {
    position: sticky;
    top: 2rem;
}

/* Password toggle button */
.toggle-password {
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
}

/* Custom toggle switch */
input[type="checkbox"]#twoFactorSwitch + label {
    position: relative;
    display: inline-block;
    cursor: not-allowed;
}

input[type="checkbox"]#twoFactorSwitch + label span {
    transition: all 0.3s ease;
}

/* Password strength indicator */
#password-strength {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

#password-strength.text-danger { color: #dc2626; }
#password-strength.text-warning { color: #d97706; }
#password-strength.text-info { color: #0891b2; }
#password-strength.text-success { color: #059669; }
</style>
@endpush

@push('scripts')
<script>
// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input');
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

// Password strength indicator
const passwordInput = document.getElementById('new_password');
if (passwordInput) {
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthIndicator = document.getElementById('password-strength');
        
        if (!strengthIndicator) return;
        
        let score = 0;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        const messages = [
            { text: 'Sangat lemah', color: 'text-red-600' },
            { text: 'Lemah', color: 'text-red-500' },
            { text: 'Cukup', color: 'text-yellow-600' },
            { text: 'Kuat', color: 'text-green-500' },
            { text: 'Sangat kuat', color: 'text-green-600' }
        ];
        
        const strength = messages[score];
        strengthIndicator.textContent = `Kekuatan kata sandi: ${strength.text}`;
        strengthIndicator.className = `text-sm font-medium ${strength.color}`;
        
        // Optional: Add visual strength meter
        const meter = document.getElementById('password-strength-meter') || (() => {
            const div = document.createElement('div');
            div.id = 'password-strength-meter';
            div.className = 'h-1 w-full bg-gray-200 rounded-full overflow-hidden mt-1';
            const bar = document.createElement('div');
            bar.className = 'h-full rounded-full transition-all duration-300';
            div.appendChild(bar);
            strengthIndicator.after(div);
            return bar;
        })();
        
        const meterBar = document.querySelector('#password-strength-meter > div');
        const percentages = ['0%', '25%', '50%', '75%', '100%'];
        const colors = ['bg-red-500', 'bg-red-400', 'bg-yellow-500', 'bg-green-400', 'bg-green-500'];
        
        meterBar.style.width = percentages[score];
        meterBar.className = `h-full rounded-full transition-all duration-300 ${colors[score]}`;
    });
}

// Modal functions
function showLogoutAllModal() {
    document.getElementById('logoutAllModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLogoutAllModal() {
    document.getElementById('logoutAllModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // AOS initialization
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    }
    
    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLogoutAllModal();
        }
    });
});
</script>
@endpush