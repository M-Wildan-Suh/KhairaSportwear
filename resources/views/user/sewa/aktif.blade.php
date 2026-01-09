@extends('user.layouts.app')

@section('title', 'Sewa Aktif - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.sewa.index') }}">Sewa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sewa Aktif</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-weight-bold mb-0">Sewa Aktif</h1>
        <a href="{{ route('user.sewa.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-plus me-2"></i> Sewa Baru
        </a>
    </div>
    
    @if($sewas->count() > 0)
    <div class="row">
        @foreach($sewas as $sewa)
        <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <div class="rental-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="font-weight-bold mb-1">{{ $sewa->produk->nama }}</h5>
                            <div class="text-muted small">{{ $sewa->kode_sewa }}</div>
                        </div>
                        <div>
                            @include('components.status-badge', ['status' => $sewa->status])
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $sewa->produk->gambar_url }}" 
                             alt="{{ $sewa->produk->nama }}"
                             class="rounded-3 me-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <div class="mb-1">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span>{{ $sewa->durasi }} ({{ $sewa->jumlah_hari }} hari)</span>
                            </div>
                            <div class="mb-1">
                                <i class="fas fa-play-circle text-success me-2"></i>
                                <span>Mulai: {{ $sewa->tanggal_mulai->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-flag-checkered text-danger me-2"></i>
                                <span>Kembali: {{ $sewa->tanggal_kembali_rencana->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Progress Sewa</span>
                            <span class="small font-weight-bold">{{ $sewa->sisa_hari }} hari tersisa</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $totalDays = $sewa->jumlah_hari;
                                $remainingDays = $sewa->sisa_hari;
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
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Mulai</small>
                            <small class="text-muted">Kembali</small>
                        </div>
                    </div>
                    
                    <!-- Rental Info -->
                    <div class="rental-info mb-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="info-item">
                                    <small class="text-muted d-block">Total Biaya</small>
                                    <strong class="text-primary">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-item">
                                    <small class="text-muted d-block">Status</small>
                                    <div class="d-flex align-items-center">
                                        <div class="status-dot bg-{{ $sewa->sisa_hari < 3 ? 'danger' : 'success' }} me-2"></div>
                                        <span>{{ $sewa->sisa_hari }} hari lagi</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.sewa.show', $sewa->id) }}" 
                           class="btn btn-outline-primary flex-fill">
                            <i class="fas fa-eye me-2"></i> Detail
                        </a>
                        <button class="btn btn-outline-info" onclick="showExtendModal('{{ $sewa->id }}')"
                                {{ $sewa->sisa_hari < 1 ? 'disabled' : '' }}>
                            <i class="fas fa-plus me-2"></i> Perpanjang
                        </button>
                        <button class="btn btn-outline-success" onclick="showReturnModal('{{ $sewa->id }}')">
                            <i class="fas fa-undo me-2"></i> Kembalikan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($sewas->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $sewas->links() }}
    </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="text-center py-5" data-aos="fade-up">
        <div class="empty-state">
            <i class="fas fa-calendar-check fa-5x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Tidak Ada Sewa Aktif</h3>
            <p class="text-muted mb-4">Mulai sewa alat olahraga favorit Anda</p>
            <a href="{{ route('user.sewa.index') }}" class="btn btn-sport">
                <i class="fas fa-calendar-plus me-2"></i> Sewa Sekarang
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengembalian Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="returnForm">
                    @csrf
                    <input type="hidden" id="return_sewa_id" name="sewa_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_kembali" id="return_tanggal_kembali" required>
                                <div class="form-text">Tanggal saat Anda mengembalikan alat</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kondisi Alat <span class="text-danger">*</span></label>
                                <select class="form-select" name="kondisi_alat" id="return_kondisi_alat" required>
                                    <option value="">Pilih kondisi</option>
                                    <option value="baik">Baik (Tidak ada kerusakan)</option>
                                    <option value="rusak_ringan">Rusak Ringan (Perlu perbaikan kecil)</option>
                                    <option value="rusak_berat">Rusak Berat (Perlu perbaikan besar)</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Catatan Kondisi</label>
                                <textarea class="form-control" name="catatan_kondisi" rows="3" placeholder="Deskripsi kondisi alat..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="fine-calculation border rounded p-4 h-100">
                                <h6 class="font-weight-bold mb-3">Perhitungan Denda</h6>
                                <div id="fineCalculation">
                                    <div class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-primary"></div>
                                        <p class="mt-2 text-muted">Menghitung denda...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Pengembalian akan diverifikasi oleh admin. Denda akan dikenakan jika terdapat keterlambatan atau kerusakan.
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle me-2"></i> Ajukan Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Extend Modal -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perpanjang Sewa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="extendForm">
                    @csrf
                    <input type="hidden" id="extend_sewa_id" name="sewa_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Tambahan Hari <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="tambahan_hari" min="1" max="30" value="1" required>
                        <div class="form-text">Maksimal 30 hari per sewa</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Perpanjangan</label>
                        <textarea class="form-control" name="alasan" rows="2" placeholder="Alasan perpanjangan sewa..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Perpanjangan sewa akan menambah biaya sesuai durasi tambahan.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i> Ajukan Perpanjangan
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
.rental-card {
    transition: all 0.3s ease;
}

.rental-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.info-item {
    padding: 0.5rem;
    border-radius: 8px;
    background: #f8f9fa;
}

.fine-calculation {
    background: #f8f9fa;
}

.empty-state {
    padding: 3rem 1rem;
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
</style>
@endpush

@push('scripts')
<script>
// Show return modal
function showReturnModal(sewaId) {
    document.getElementById('return_sewa_id').value = sewaId;
    
    // Set default return date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('return_tanggal_kembali').value = today;
    document.getElementById('return_tanggal_kembali').min = today;
    
    // Calculate fines
    calculateFines(sewaId);
    
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    modal.show();
}

// Calculate fines
function calculateFines(sewaId) {
    const tanggalKembali = document.getElementById('return_tanggal_kembali').value;
    const kondisiAlat = document.getElementById('return_kondisi_alat').value;
    
    if (!tanggalKembali || !kondisiAlat) return;
    
    fetch('/user/sewa/calculate-denda', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            sewa_id: sewaId,
            tanggal_kembali: tanggalKembali,
            kondisi_alat: kondisiAlat
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const fines = data.data;
            let fineHtml = '';
            
            if (fines.total_denda > 0) {
                fineHtml = `
                    <div class="fine-detail">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Keterlambatan:</span>
                            <span>${fines.keterlambatan_hari} hari × Rp ${fines.tarif_denda_per_hari.toLocaleString()}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Denda Keterlambatan:</span>
                            <span class="text-danger">Rp ${fines.denda_keterlambatan.toLocaleString()}</span>
                        </div>
                        ${fines.denda_kerusakan > 0 ? `
                        <div class="d-flex justify-content-between mb-2">
                            <span>Denda Kerusakan:</span>
                            <span class="text-danger">Rp ${fines.denda_kerusakan.toLocaleString()}</span>
                        </div>
                        ` : ''}
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Denda:</strong>
                            <strong class="text-danger">Rp ${fines.total_denda.toLocaleString()}</strong>
                        </div>
                    </div>
                `;
            } else {
                fineHtml = `
                    <div class="text-center text-success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p class="mb-0">Tidak ada denda</p>
                    </div>
                `;
            }
            
            document.getElementById('fineCalculation').innerHTML = fineHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('fineCalculation').innerHTML = `
            <div class="text-center text-danger">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <p class="mb-0">Gagal menghitung denda</p>
            </div>
        `;
    });
}

// Event listeners for fine calculation
document.getElementById('return_tanggal_kembali').addEventListener('change', function() {
    const sewaId = document.getElementById('return_sewa_id').value;
    if (sewaId) calculateFines(sewaId);
});

document.getElementById('return_kondisi_alat').addEventListener('change', function() {
    const sewaId = document.getElementById('return_sewa_id').value;
    if (sewaId) calculateFines(sewaId);
});

// Return form submission
document.getElementById('returnForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const sewaId = formData.get('sewa_id');
    
    Swal.fire({
        title: 'Ajukan Pengembalian?',
        text: 'Alat akan dikembalikan dan menunggu verifikasi admin',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ED8936',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Ya, Ajukan',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/user/sewa/${sewaId}/pengembalian`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tanggal_kembali: formData.get('tanggal_kembali'),
                    kondisi_alat: formData.get('kondisi_alat'),
                    catatan_kondisi: formData.get('catatan_kondisi')
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Request failed: ${error}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('returnModal')).hide();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect
                    window.location.href = result.value.redirect;
                });
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
});

// Show extend modal
function showExtendModal(sewaId) {
    document.getElementById('extend_sewa_id').value = sewaId;
    
    const modal = new bootstrap.Modal(document.getElementById('extendModal'));
    modal.show();
}

// Extend form submission
document.getElementById('extendForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const sewaId = formData.get('sewa_id');
    
    Swal.fire({
        title: 'Perpanjang Sewa?',
        text: 'Sewa akan diperpanjang dan biaya tambahan akan ditambahkan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ED8936',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Ya, Perpanjang',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/user/sewa/${sewaId}/extend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tambahan_hari: formData.get('tambahan_hari'),
                    alasan: formData.get('alasan')
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Request failed: ${error}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('extendModal')).hide();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect
                    window.location.href = result.value.redirect;
                });
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
});

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        once: true
    });
});
</script>
@endpush