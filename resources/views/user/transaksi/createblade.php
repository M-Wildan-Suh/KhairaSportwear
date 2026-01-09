@extends('user.layouts.app')

@section('title', 'Checkout - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.keranjang.index') }}">Keranjang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>
    
    <h1 class="h2 font-weight-bold mb-4">Checkout</h1>
    
    <form id="checkoutForm" method="POST" action="{{ route('user.transaksi.store') }}">
        @csrf
        
        <div class="row">
            <!-- Order Items -->
            <div class="col-lg-8 mb-4">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right">
                    <div class="card-header bg-white border-0 pt-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-box me-2"></i>
                            Ringkasan Pesanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($keranjangs as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->produk->gambar_url }}" 
                                                     alt="{{ $item->produk->nama }}"
                                                     class="rounded-2 me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1">{{ $item->produk->nama }}</h6>
                                                    <small class="text-muted">{{ $item->produk->kategori->nama }}</small>
                                                    @if($item->tipe === 'sewa' && $item->opsi_sewa)
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            {{ ucfirst($item->opsi_sewa['durasi']) }} • 
                                                            {{ $item->opsi_sewa['jumlah_hari'] }} hari • 
                                                            Mulai: {{ date('d/m/Y', strtotime($item->opsi_sewa['tanggal_mulai'])) }}
                                                        </small>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($item->tipe === 'jual')
                                            <span class="badge bg-success">Beli</span>
                                            @else
                                            <span class="badge bg-info">Sewa</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="text-end align-middle">
                                            <div class="h6 font-weight-bold mb-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                            <small class="text-muted">
                                                @if($item->tipe === 'jual')
                                                Rp {{ number_format($item->produk->harga_beli, 0, ',', '.') }} / item
                                                @else
                                                Rp {{ number_format($item->harga, 0, ',', '.') }} / hari
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Order Totals -->
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-6">
                                <div class="order-totals">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span class="font-weight-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">PPN (11%)</span>
                                        <span class="font-weight-bold">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Biaya Pengiriman</span>
                                        <span class="font-weight-bold text-success">Gratis</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-0">
                                        <span class="h5 font-weight-bold">Total</span>
                                        <span class="h4 text-primary font-weight-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Address -->
                @if($keranjangs->where('tipe', 'jual')->isNotEmpty())
                <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="card-header bg-white border-0 pt-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Alamat Pengiriman
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat_pengiriman" rows="3" required>{{ auth()->user()->address }}</textarea>
                            <div class="form-text">Pastikan alamat lengkap dan jelas untuk pengiriman</div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Payment Method -->
                <div class="card border-0 shadow-sm rounded-4" data-aos="fade-right" data-aos-delay="200">
                    <div class="card-header bg-white border-0 pt-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Metode Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer_bank" value="transfer_bank" checked>
                                <label class="form-check-label" for="transfer_bank">
                                    <i class="fas fa-university me-2"></i>
                                    Transfer Bank
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="tunai" value="tunai">
                                <label class="form-check-label" for="tunai">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Tunai (Bayar di Tempat)
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris" value="qris">
                                <label class="form-check-label" for="qris">
                                    <i class="fas fa-qrcode me-2"></i>
                                    QRIS
                                </label>
                            </div>
                        </div>
                        
                        <!-- Bank Transfer Details -->
                        <div id="bankTransferDetails" class="mb-4">
                            <h6 class="font-weight-bold mb-3">Informasi Transfer</h6>
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <strong>Transfer ke rekening berikut:</strong><br>
                                        @if(is_array($bankInfo))
                                            @foreach($bankInfo as $bank)
                                            <div class="mt-1">{{ $bank }}: {{ $noRekening }} a/n {{ $namaRekening }}</div>
                                            @endforeach
                                        @else
                                            <div class="mt-1">{{ $bankInfo }}: {{ $noRekening }} a/n {{ $namaRekening }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_bank" placeholder="Contoh: BCA">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Rekening <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_rekening" placeholder="1234567890">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Atas Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="atas_nama" placeholder="Nama pemilik rekening">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan untuk penjual..."></textarea>
                        </div>
                        
                        <!-- Terms and Conditions -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                            <label class="form-check-label" for="agreeTerms">
                                Saya setuju dengan <a href="#" class="text-primary">Syarat & Ketentuan</a> dan <a href="#" class="text-primary">Kebijakan Privasi</a> SportWear
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Sidebar -->
            <div class="col-lg-4" data-aos="fade-left">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Konfirmasi Pesanan</h5>
                        
                        <!-- Customer Info -->
                        <div class="customer-info mb-4">
                            <h6 class="font-weight-bold mb-3">Informasi Pelanggan</h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-muted me-2"></i>
                                <span>{{ auth()->user()->name }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <span>{{ auth()->user()->email }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <span>{{ auth()->user()->phone }}</span>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="order-summary-sidebar mb-4">
                            <h6 class="font-weight-bold mb-3">Ringkasan</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Items</span>
                                <span>{{ $keranjangs->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">PPN</span>
                                <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="h6 font-weight-bold">Total</span>
                                <span class="h5 text-primary font-weight-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-sport btn-lg" id="submitOrder">
                                <i class="fas fa-lock me-2"></i> Buat Pesanan
                            </button>
                            <a href="{{ route('user.keranjang.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Keranjang
                            </a>
                        </div>
                        
                        <!-- Security Info -->
                        <div class="security-info mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                <span>Transaksi 100% Aman</span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-lock text-success me-2"></i>
                                <span>Data terenkripsi SSL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center py-5">
                <div class="spinner mb-3"></div>
                <h5>Memproses Pesanan...</h5>
                <p class="text-muted mb-0">Mohon tunggu sebentar</p>
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

.order-totals hr {
    border-top: 2px dashed #dee2e6;
}

.customer-info i {
    width: 20px;
    text-align: center;
}

.order-summary-sidebar hr {
    border-top: 2px dashed #dee2e6;
}

#bankTransferDetails {
    transition: all 0.3s ease;
}

/* Custom radio buttons */
.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-check-label {
    cursor: pointer;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Toggle bank transfer details
const bankTransferRadio = document.getElementById('transfer_bank');
const bankTransferDetails = document.getElementById('bankTransferDetails');
const bankFields = ['nama_bank', 'no_rekening', 'atas_nama'];

function toggleBankTransferDetails() {
    if (bankTransferRadio.checked) {
        bankTransferDetails.style.display = 'block';
        bankFields.forEach(field => {
            document.querySelector(`[name="${field}"]`).required = true;
        });
    } else {
        bankTransferDetails.style.display = 'none';
        bankFields.forEach(field => {
            document.querySelector(`[name="${field}"]`).required = false;
        });
    }
}

// Initialize
toggleBankTransferDetails();

// Add event listeners to payment method radios
document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
    radio.addEventListener('change', toggleBankTransferDetails);
});

// Form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    if (!document.getElementById('agreeTerms').checked) {
        Swal.fire('Error', 'Anda harus menyetujui Syarat & Ketentuan', 'error');
        return;
    }
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Submit form via AJAX
    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(Object.fromEntries(new FormData(this)))
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            // Clear cart badge
            window.dispatchEvent(new CustomEvent('cartUpdated', {
                detail: { count: 0 }
            }));
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Pesanan Berhasil!',
                html: `
                    <p>Pesanan Anda telah berhasil dibuat.</p>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Silakan upload bukti pembayaran di halaman transaksi untuk mempercepat proses verifikasi.
                    </div>
                `,
                confirmButtonText: 'Lihat Transaksi',
                showCancelButton: true,
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = data.redirect;
                }
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat memproses pesanan', 'error');
    });
});

// Format currency in real-time
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to elements
    AOS.init({
        duration: 800,
        once: true
    });
    
    // Set min date for any date inputs
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.min = minDate;
    });
});

// Auto-fill bank transfer fields
document.querySelector('input[name="nama_bank"]').addEventListener('input', function() {
    if (this.value.toLowerCase().includes('bca')) {
        document.querySelector('input[name="atas_nama"]').value = '{{ auth()->user()->name }}';
    }
});
</script>
@endpush