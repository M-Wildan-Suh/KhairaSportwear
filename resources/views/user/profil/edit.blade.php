@extends('user.layouts.app')

@section('title', 'Edit Profil - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
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
                                 id="avatarPreview"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <button class="btn btn-primary btn-sm avatar-edit-btn" onclick="openAvatarModal()">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                        <span class="badge bg-success mt-2">Member sejak {{ $statistics['member_since'] }}</span>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="profile-nav">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('user.profil.edit') }}" 
                               class="list-group-item list-group-item-action border-0 py-3 active">
                                <i class="fas fa-user me-2"></i> Profil Saya
                            </a>
                            <a href="{{ route('user.profil.security') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-shield-alt me-2"></i> Keamanan
                            </a>
                            <a href="{{ route('user.transaksi.index') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-receipt me-2"></i> Transaksi
                            </a>
                            <a href="{{ route('user.sewa.aktif') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-calendar-alt me-2"></i> Sewa Aktif
                            </a>
                            <a href="{{ route('user.notifikasi.index') }}" 
                               class="list-group-item list-group-item-action border-0 py-3">
                                <i class="fas fa-bell me-2"></i> Notifikasi
                                @if($user->getUnreadNotificationsCount() > 0)
                                <span class="badge bg-danger float-end">{{ $user->getUnreadNotificationsCount() }}</span>
                                @endif
                            </a>
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
                    <h1 class="h2 font-weight-bold mb-1">Profil Saya</h1>
                    <p class="text-muted mb-0">Kelola informasi profil Anda</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4" data-aos="fade-up">
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card bg-white border rounded-3 p-3 text-center shadow-sm">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                        <h4 class="mb-1">{{ $statistics['total_transactions'] }}</h4>
                        <p class="text-muted small mb-0">Total Transaksi</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card bg-white border rounded-3 p-3 text-center shadow-sm">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-wallet fa-2x text-success"></i>
                        </div>
                        <h4 class="mb-1">Rp {{ number_format($statistics['total_spent'], 0, ',', '.') }}</h4>
                        <p class="text-muted small mb-0">Total Pengeluaran</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card bg-white border rounded-3 p-3 text-center shadow-sm">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                        <h4 class="mb-1">{{ $statistics['active_rentals'] }}</h4>
                        <p class="text-muted small mb-0">Sewa Aktif</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card bg-white border rounded-3 p-3 text-center shadow-sm">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-flag-checkered fa-2x text-warning"></i>
                        </div>
                        <h4 class="mb-1">{{ $statistics['completed_rentals'] }}</h4>
                        <p class="text-muted small mb-0">Sewa Selesai</p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Form -->
            <div class="row" data-aos="fade-up" data-aos-delay="100">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Informasi Pribadi
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="profileForm">
                                @csrf
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="name" 
                                                   name="name" value="{{ $user->name }}" required>
                                            <label for="name">Nama Lengkap</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" 
                                                   name="email" value="{{ $user->email }}" required>
                                            <label for="email">Email</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="phone" 
                                                   name="phone" value="{{ $user->phone }}" required>
                                            <label for="phone">Nomor Telepon</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="member_since" 
                                                   value="Member sejak {{ $statistics['member_since'] }}" disabled>
                                            <label for="member_since">Bergabung</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="address" 
                                                      name="address" style="height: 100px" required>{{ $user->address }}</textarea>
                                            <label for="address">Alamat Lengkap</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-2"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Account Status -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Status Akun
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="account-status">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="status-icon bg-success rounded-circle p-2 me-3">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Email Terverifikasi</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="status-icon bg-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Tipe Akun</h6>
                                        <small class="text-muted">{{ $user->role === 'admin' ? 'Administrator' : 'Pelanggan' }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="status-icon bg-info rounded-circle p-2 me-3">
                                        <i class="fas fa-shield-alt text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Keamanan</h6>
                                        <small class="text-muted">Password terakhir diubah {{ $user->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="activity-icon bg-{{ $activity->tipe === 'penjualan' ? 'success' : 'info' }} rounded-circle p-2 me-3">
                                                <i class="fas fa-{{ $activity->tipe === 'penjualan' ? 'shopping-cart' : 'calendar-alt' }} text-white"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $activity->kode_transaksi }}</div>
                                                <small class="text-muted">{{ $activity->tipe === 'penjualan' ? 'Pembelian' : 'Penyewaan' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @include('components.status-badge', ['status' => $activity->status])
                                    </td>
                                    <td class="font-weight-bold">Rp {{ number_format($activity->total_bayar, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada aktivitas</h5>
                        <p class="text-muted">Mulai berbelanja untuk melihat aktivitas Anda</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img src="{{ $user->avatar_url }}" 
                         alt="Avatar Preview" 
                         class="rounded-circle mb-3"
                         id="avatarModalPreview"
                         style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                
                <form id="avatarForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto Baru</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        <div class="form-text">Format: JPG, PNG (maks. 2MB)</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i> Upload Foto
                        </button>
                        @if($user->avatar)
                        <button type="button" class="btn btn-outline-danger" onclick="deleteAvatar()">
                            <i class="fas fa-trash me-2"></i> Hapus Foto
                        </button>
                        @endif
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

.avatar-container {
    width: 120px;
    height: 120px;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.avatar-edit-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.profile-nav .list-group-item {
    transition: all 0.3s ease;
    border-radius: 8px !important;
    margin-bottom: 5px;
}

.profile-nav .list-group-item:hover,
.profile-nav .list-group-item.active {
    background: rgba(43, 108, 176, 0.1);
    color: var(--primary);
    padding-left: 1.5rem;
}

.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.stat-icon {
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1);
}

.status-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-floating > .form-control {
    height: calc(3.5rem + 2px);
    padding: 1rem 0.75rem;
}

.form-floating > label {
    padding: 1rem 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
// Profile form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
    submitBtn.disabled = true;
    
    fetch('{{ route("user.profil.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            // Update avatar if changed
            if (data.user.avatar) {
                document.getElementById('avatarPreview').src = data.user.avatar_url;
                document.getElementById('avatarModalPreview').src = data.user.avatar_url;
            }
            
            // Update user info in sidebar
            document.querySelector('.profile-nav h5').textContent = data.user.name;
            document.querySelector('.profile-nav p').textContent = data.user.email;
            
            Toast.fire({
                icon: 'success',
                title: data.message
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan profil', 'error');
    });
});

// Open avatar modal
function openAvatarModal() {
    const modal = new bootstrap.Modal(document.getElementById('avatarModal'));
    modal.show();
}

// Avatar preview
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('avatarModalPreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Avatar form submission
document.getElementById('avatarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengupload...';
    submitBtn.disabled = true;
    
    fetch('{{ route("user.profil.update-avatar") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            // Update all avatar previews
            document.getElementById('avatarPreview').src = data.avatar_url;
            document.getElementById('avatarModalPreview').src = data.avatar_url;
            
            // Update navbar avatar if exists
            const navbarAvatar = document.querySelector('.user-avatar-sport');
            if (navbarAvatar) {
                navbarAvatar.style.backgroundImage = `url(${data.avatar_url})`;
                navbarAvatar.style.backgroundSize = 'cover';
            }
            
            Toast.fire({
                icon: 'success',
                title: data.message
            });
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('avatarModal')).hide();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat mengupload foto', 'error');
    });
});

// Delete avatar
function deleteAvatar() {
    Swal.fire({
        title: 'Hapus foto profil?',
        text: 'Foto profil akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("user.profil.delete-avatar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all avatar previews
                    document.getElementById('avatarPreview').src = data.avatar_url;
                    document.getElementById('avatarModalPreview').src = data.avatar_url;
                    
                    // Update navbar avatar
                    const navbarAvatar = document.querySelector('.user-avatar-sport');
                    if (navbarAvatar) {
                        navbarAvatar.style.backgroundImage = '';
                        navbarAvatar.style.backgroundColor = '';
                    }
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('avatarModal')).hide();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            });
        }
    });
}

// Load more activities
function loadMoreActivities() {
    const activityTable = document.querySelector('.table tbody');
    const loadBtn = document.querySelector('#loadMoreActivities');
    
    if (loadBtn) {
        loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memuat...';
        loadBtn.disabled = true;
        
        fetch('{{ route("user.profil.activity") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Append new activities
                    data.activities.forEach(activity => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${activity.kode_transaksi}</td>
                            <td>${new Date(activity.created_at).toLocaleDateString()}</td>
                            <td>${activity.status}</td>
                            <td>Rp ${activity.total_bayar.toLocaleString()}</td>
                        `;
                        activityTable.appendChild(row);
                    });
                    
                    // Update or remove button
                    if (data.activities.length < 10) {
                        loadBtn.remove();
                    } else {
                        loadBtn.innerHTML = '<i class="fas fa-plus me-2"></i> Muat Lebih Banyak';
                        loadBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadBtn.innerHTML = '<i class="fas fa-plus me-2"></i> Muat Lebih Banyak';
                loadBtn.disabled = false;
            });
    }
}

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        once: true
    });
    
    // Add animation to stat cards
    document.querySelectorAll('.stat-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
@endpush