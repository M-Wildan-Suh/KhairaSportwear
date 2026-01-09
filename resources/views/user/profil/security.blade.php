@extends('user.layouts.app')

@section('title', 'Keamanan Akun - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.profil.edit') }}">Profil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Keamanan</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body">
                    <!-- User Profile -->
                    <div class="text-center mb-4">
                        <div class="avatar-container position-relative mx-auto mb-3">
                            <img src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}"
                                 class="avatar-img rounded-circle"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="profile-nav">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('user.profil.edit') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-user me-2"></i> Profil Saya
                            </a>
                            <a href="{{ route('user.profil.security') }}" 
                               class="list-group-item list-group-item-action border-0 py-3 active">
                                <i class="fas fa-shield-alt me-2"></i> Keamanan Akun
                            </a>
                            <a href="{{ route('user.orders.index') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-shopping-bag me-2"></i> Pesanan Saya
                            </a>
                            <a href="{{ route('user.wishlist') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-heart me-2"></i> Wishlist
                            </a>
                            <a href="{{ route('logout') }}" 
                               class="list-group-item list-group-item-action border-0 py-3 text-danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9" data-aos="fade-up">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-4 rounded-top-4">
                    <h4 class="mb-0 font-weight-bold"><i class="fas fa-shield-alt me-2"></i> Keamanan Akun</h4>
                    <p class="text-muted mb-0">Kelola keamanan akun dan kata sandi Anda</p>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> 
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Change Password Form -->
                    <div class="mb-5">
                        <h5 class="font-weight-bold mb-4">Ubah Kata Sandi</h5>
                        <form action="{{ route('user.profil.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control rounded-3 @error('current_password') is-invalid @enderror" 
                                               id="current_password" 
                                               name="current_password"
                                               required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">Kata Sandi Baru</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control rounded-3 @error('new_password') is-invalid @enderror" 
                                               id="new_password" 
                                               name="new_password"
                                               required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control rounded-3" 
                                               id="new_password_confirmation" 
                                               name="new_password_confirmation"
                                               required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info rounded-3" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                Pastikan kata sandi Anda minimal 8 karakter dan mengandung kombinasi huruf, angka, dan simbol.
                            </div>
                            
                            <button type="submit" class="btn btn-primary rounded-3 px-4">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                    
                    <!-- Two Factor Authentication -->
                    <div class="mb-5">
                        <h5 class="font-weight-bold mb-4">Autentikasi Dua Faktor (2FA)</h5>
                        <div class="card border-0 bg-light rounded-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-bold mb-1">Autentikasi Dua Faktor</h6>
                                        <p class="text-muted small mb-0">Tambah lapisan keamanan ekstra ke akun Anda</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="twoFactorSwitch" disabled>
                                        <label class="form-check-label" for="twoFactorSwitch">Nonaktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-2">Fitur ini akan segera tersedia.</p>
                    </div>
                    
                    <!-- Session Management -->
                    <div>
                        <h5 class="font-weight-bold mb-4">Sesi Aktif</h5>
                        <div class="card border-0 bg-light rounded-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="font-weight-bold mb-1">Sesi Saat Ini</h6>
                                        <p class="text-muted small mb-0">Perangkat ini • {{ request()->ip() }}</p>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Aktif</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-bold mb-1">Waktu Login Terakhir</h6>
                                        <p class="text-muted small mb-0">{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum tersedia' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#logoutAllModal">
                                <i class="fas fa-sign-out-alt me-2"></i> Keluar dari Semua Perangkat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout All Modal -->
<div class="modal fade" id="logoutAllModal" tabindex="-1" aria-labelledby="logoutAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold" id="logoutAllModalLabel">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin keluar dari semua perangkat? Anda akan diminta untuk login kembali di semua perangkat.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('user.profil.logout-all') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="fas fa-sign-out-alt me-2"></i> Keluar Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

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
    
    // Password strength indicator (optional enhancement)
    document.getElementById('new_password')?.addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthIndicator = document.getElementById('password-strength');
        
        if (!strengthIndicator) {
            const div = document.createElement('div');
            div.id = 'password-strength';
            div.className = 'mt-2 small';
            e.target.parentElement.parentElement.appendChild(div);
        }
        
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        const strengthText = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat'];
        const strengthClass = ['text-danger', 'text-warning', 'text-info', 'text-success'];
        
        strengthIndicator.textContent = `Kekuatan: ${strengthText[strength] || ''}`;
        strengthIndicator.className = `mt-2 small ${strengthClass[strength] || ''}`;
    });
</script>
@endpush
@endsection