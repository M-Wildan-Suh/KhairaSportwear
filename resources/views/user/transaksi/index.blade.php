@extends('user.layouts.app')

@section('title', 'Histori Transaksi - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histori Transaksi</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-weight-bold mb-0">Histori Transaksi</h1>
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-2"></i> Filter
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('user.transaksi.index') }}">Semua</a>
                <a class="dropdown-item" href="{{ route('user.transaksi.index', ['status' => 'pending']) }}">Pending</a>
                <a class="dropdown-item" href="{{ route('user.transaksi.index', ['status' => 'diproses']) }}">Diproses</a>
                <a class="dropdown-item" href="{{ route('user.transaksi.index', ['status' => 'selesai']) }}">Selesai</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('user.transaksi.index', ['tipe' => 'penjualan']) }}">Penjualan</a>
                <a class="dropdown-item" href="{{ route('user.transaksi.index', ['tipe' => 'penyewaan']) }}">Penyewaan</a>
            </div>
        </div>
    </div>
    
    @if($transaksis->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="border-0">Kode Transaksi</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0">Tipe</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksis as $transaksi)
                                <tr data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                    <td>
                                        <div class="font-weight-bold">{{ $transaksi->kode_transaksi }}</div>
                                        <small class="text-muted">
                                            {{ $transaksi->detailTransaksis->count() }} item
                                        </small>
                                    </td>
                                    <td>
                                        <div>{{ $transaksi->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $transaksi->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($transaksi->tipe === 'penjualan')
                                        <span class="badge bg-success">Penjualan</span>
                                        @else
                                        <span class="badge bg-info">Penyewaan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</div>
                                        <small class="text-muted">
                                            {{ $transaksi->metode_pembayaran === 'transfer_bank' ? 'Transfer' : ucfirst($transaksi->metode_pembayaran) }}
                                        </small>
                                    </td>
                                    <td>
                                        @include('components.status-badge', ['status' => $transaksi->status])
                                        @if($transaksi->status === 'pending')
                                        <div class="small text-danger mt-1">
                                            <i class="fas fa-clock"></i> Upload bukti pembayaran
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('user.transaksi.show', $transaksi->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($transaksi->status === 'pending')
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="showUploadModal('{{ $transaksi->id }}')">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                            @endif
                                            @if(in_array($transaksi->status, ['pending', 'diproses']))
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="cancelTransaction('{{ $transaksi->id }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($transaksis->hasPages())
                <div class="card-footer border-0 bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $transaksis->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mt-5">
        <div class="col-md-3 mb-4" data-aos="fade-up">
            <div class="stats-card bg-white border rounded-4 p-4 text-center shadow-sm">
                <div class="stats-icon mb-3">
                    <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                </div>
                <h3 class="mb-2">{{ $transaksis->total() }}</h3>
                <p class="text-muted mb-0">Total Transaksi</p>
            </div>
        </div>
        
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="stats-card bg-white border rounded-4 p-4 text-center shadow-sm">
                <div class="stats-icon mb-3">
                    <i class="fas fa-check-circle fa-3x text-success"></i>
                </div>
                <h3 class="mb-2">{{ $transaksis->where('status', 'selesai')->count() }}</h3>
                <p class="text-muted mb-0">Selesai</p>
            </div>
        </div>
        
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="stats-card bg-white border rounded-4 p-4 text-center shadow-sm">
                <div class="stats-icon mb-3">
                    <i class="fas fa-clock fa-3x text-warning"></i>
                </div>
                <h3 class="mb-2">{{ $transaksis->whereIn('status', ['pending', 'diproses'])->count() }}</h3>
                <p class="text-muted mb-0">Dalam Proses</p>
            </div>
        </div>
        
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="stats-card bg-white border rounded-4 p-4 text-center shadow-sm">
                <div class="stats-icon mb-3">
                    <i class="fas fa-times-circle fa-3x text-danger"></i>
                </div>
                <h3 class="mb-2">{{ $transaksis->where('status', 'dibatalkan')->count() }}</h3>
                <p class="text-muted mb-0">Dibatalkan</p>
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-5" data-aos="fade-up">
        <div class="empty-state">
            <i class="fas fa-receipt fa-5x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Belum Ada Transaksi</h3>
            <p class="text-muted mb-4">Mulai berbelanja untuk melihat riwayat transaksi Anda</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('user.produk.index') }}" class="btn btn-sport">
                    <i class="fas fa-store me-2"></i> Belanja Sekarang
                </a>
                <a href="{{ route('user.sewa.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-alt me-2"></i> Sewa Alat
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Upload Bukti Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="transaksi_id" name="transaksi_id">
                    
                    <div class="mb-4">
                        <label class="form-label">Pilih File Bukti Transfer</label>
                        <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                        <div class="form-text">
                            Format: JPG, PNG (maks. 2MB)
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Pastikan bukti transfer jelas menunjukkan:
                        <ul class="mb-0 mt-2">
                            <li>Nama bank pengirim & penerima</li>
                            <li>Nomor rekening</li>
                            <li>Jumlah transfer</li>
                            <li>Tanggal & waktu transfer</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i> Upload Bukti
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
    transform: scale(1.1);
}

.empty-state {
    padding: 3rem 1rem;
}

.table-hover tbody tr {
    transition: all 0.3s ease;
}

.table-hover tbody tr:hover {
    background: rgba(43, 108, 176, 0.05);
}

/* Custom pagination */
.pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pagination .page-link {
    color: var(--primary);
    border: none;
    margin: 0 2px;
    border-radius: 8px !important;
}

.pagination .page-link:hover {
    background: rgba(43, 108, 176, 0.1);
    color: var(--primary);
}
</style>
@endpush

@push('scripts')
<script>
// Show upload modal
function showUploadModal(transaksiId) {
    document.getElementById('transaksi_id').value = transaksiId;
    const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}

// Upload bukti pembayaran
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const transaksiId = formData.get('transaksi_id');
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengupload...';
    submitBtn.disabled = true;
    
    fetch(`/user/transaksi/${transaksiId}/upload-bukti`, {
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
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Reload page to update status
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat mengupload', 'error');
    });
});

// Cancel transaction
function cancelTransaction(transaksiId) {
    Swal.fire({
        title: 'Batalkan Transaksi?',
        text: 'Transaksi ini akan dibatalkan dan stok akan dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/user/transaksi/${transaksiId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
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

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        once: true
    });
    
    // Add animation to table rows
    document.querySelectorAll('tbody tr').forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
    });
});

// Filter by status from URL
const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');
if (status) {
    document.querySelectorAll('.dropdown-item').forEach(item => {
        if (item.href.includes(`status=${status}`)) {
            item.classList.add('active');
        }
    });
}
</script>
@endpush