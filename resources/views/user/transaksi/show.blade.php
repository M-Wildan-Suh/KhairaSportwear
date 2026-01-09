@extends('user.layouts.app')

@section('title', 'Detail Transaksi ' . $transaksi->kode_transaksi . ' - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.transaksi.index') }}">Transaksi</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $transaksi->kode_transaksi }}</li>
        </ol>
    </nav>
    
    <!-- Transaction Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 font-weight-bold mb-1">Transaksi #{{ $transaksi->kode_transaksi }}</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i>
                {{ $transaksi->created_at->format('d F Y, H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($transaksi->status === 'pending')
            <button class="btn btn-success" onclick="showUploadModal('{{ $transaksi->id }}')">
                <i class="fas fa-upload me-2"></i> Upload Bukti
            </button>
            @endif
            @if(in_array($transaksi->status, ['pending', 'diproses']))
            <button class="btn btn-outline-danger" onclick="cancelTransaction('{{ $transaksi->id }}')">
                <i class="fas fa-times me-2"></i> Batalkan
            </button>
            @endif
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Cetak
            </button>
        </div>
    </div>
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            <!-- Order Details -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Detail Pesanan
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
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->detailTransaksis as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $detail->produk->gambar_url }}" 
                                                 alt="{{ $detail->produk->nama }}"
                                                 class="rounded-2 me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $detail->produk->nama }}</h6>
                                                <small class="text-muted">{{ $detail->produk->kategori->nama }}</small>
                                                @if($detail->tipe_produk === 'sewa' && $detail->opsi_sewa)
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        {{ ucfirst($detail->opsi_sewa['durasi']) }} • 
                                                        {{ $detail->opsi_sewa['jumlah_hari'] }} hari • 
                                                        Mulai: {{ date('d/m/Y', strtotime($detail->opsi_sewa['tanggal_mulai'])) }}
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($detail->tipe_produk === 'jual')
                                        <span class="badge bg-success">Beli</span>
                                        @else
                                        <span class="badge bg-info">Sewa</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $detail->quantity }}
                                    </td>
                                    <td class="text-end align-middle">
                                        Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                        @if($detail->tipe_produk === 'sewa')
                                        <br><small class="text-muted">/hari</small>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="h6 font-weight-bold mb-0">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end font-weight-bold">Subtotal</td>
                                    <td class="text-end font-weight-bold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end font-weight-bold">PPN (11%)</td>
                                    <td class="text-end font-weight-bold">Rp {{ number_format($transaksi->total_bayar - $transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end h5 font-weight-bold">Total</td>
                                    <td class="text-end h4 text-primary font-weight-bold">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Rental Information -->
            @if($transaksi->tipe === 'penyewaan' && $transaksi->sewa)
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right" data-aos-delay="100">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Informasi Penyewaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Kode Sewa</th>
                                    <td>{{ $transaksi->sewa->kode_sewa }}</td>
                                </tr>
                                <tr>
                                    <th>Produk</th>
                                    <td>{{ $transaksi->sewa->produk->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Durasi</th>
                                    <td>{{ ucfirst($transaksi->sewa->durasi) }} ({{ $transaksi->sewa->jumlah_hari }} hari)</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Mulai</th>
                                    <td>{{ $transaksi->sewa->tanggal_mulai->format('d F Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Tanggal Selesai</th>
                                    <td>{{ $transaksi->sewa->tanggal_selesai->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kembali</th>
                                    <td>{{ $transaksi->sewa->tanggal_kembali_rencana->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{!! $transaksi->sewa->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <th>Sisa Hari</th>
                                    <td>
                                        @if($transaksi->sewa->status === 'aktif')
                                        <span class="badge bg-{{ $transaksi->sewa->sisa_hari < 3 ? 'danger' : 'success' }}">
                                            {{ $transaksi->sewa->sisa_hari }} hari
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($transaksi->sewa->status === 'aktif')
                    <div class="mt-4">
                        <div class="progress mb-3" style="height: 10px;">
                            @php
                                $totalDays = $transaksi->sewa->jumlah_hari;
                                $remainingDays = $transaksi->sewa->sisa_hari;
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
                        <div class="d-flex justify-content-between">
                            <small>Mulai: {{ $transaksi->sewa->tanggal_mulai->format('d/m') }}</small>
                            <small>Kembali: {{ $transaksi->sewa->tanggal_kembali_rencana->format('d/m') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Transaction Status -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-left">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Status Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Status Timeline -->
                    <div class="status-timeline">
                        @php
                            $statuses = [
                                'pending' => ['icon' => 'fas fa-clock', 'color' => 'warning', 'label' => 'Menunggu Pembayaran'],
                                'diproses' => ['icon' => 'fas fa-cog', 'color' => 'info', 'label' => 'Diproses'],
                                'dibayar' => ['icon' => 'fas fa-check-circle', 'color' => 'primary', 'label' => 'Dibayar'],
                                'dikirim' => ['icon' => 'fas fa-shipping-fast', 'color' => 'secondary', 'label' => 'Dikirim'],
                                'selesai' => ['icon' => 'fas fa-flag-checkered', 'color' => 'success', 'label' => 'Selesai'],
                            ];
                            
                            $currentStatusIndex = array_search($transaksi->status, array_keys($statuses));
                        @endphp
                        
                        @foreach($statuses as $statusKey => $statusInfo)
                        <div class="timeline-item d-flex align-items-start mb-4">
                            <div class="timeline-icon me-3">
                                <div class="icon-circle bg-{{ $statusInfo['color'] }} {{ $loop->index <= $currentStatusIndex ? 'active' : '' }}">
                                    <i class="{{ $statusInfo['icon'] }} text-white"></i>
                                </div>
                                @if(!$loop->last)
                                <div class="timeline-line bg-{{ $loop->index < $currentStatusIndex ? $statusInfo['color'] : 'light' }}"></div>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1 {{ $loop->index <= $currentStatusIndex ? 'font-weight-bold' : 'text-muted' }}">
                                    {{ $statusInfo['label'] }}
                                </h6>
                                <p class="text-muted small mb-0">
                                    @if($loop->index <= $currentStatusIndex)
                                    @if($statusKey === 'pending')
                                    Menunggu upload bukti pembayaran
                                    @elseif($statusKey === 'diproses')
                                    Pesanan sedang diproses admin
                                    @elseif($statusKey === 'dibayar')
                                    Pembayaran telah diverifikasi
                                    @elseif($statusKey === 'dikirim')
                                    Pesanan sedang dikirim
                                    @elseif($statusKey === 'selesai')
                                    Transaksi selesai
                                    @endif
                                    @else
                                    Menunggu...
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Current Status -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <div class="display-6 mb-2">
                            {!! $transaksi->status_badge !!}
                        </div>
                        <p class="text-muted mb-0">
                            @if($transaksi->status === 'pending')
                            Silakan upload bukti pembayaran untuk melanjutkan
                            @elseif($transaksi->status === 'diproses')
                            Admin sedang memverifikasi pembayaran Anda
                            @elseif($transaksi->status === 'dibayar')
                            Pembayaran telah diverifikasi
                            @elseif($transaksi->status === 'dikirim')
                            Pesanan sedang dalam pengiriman
                            @elseif($transaksi->status === 'selesai')
                            Transaksi telah selesai
                            @elseif($transaksi->status === 'dibatalkan')
                            Transaksi telah dibatalkan
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-left" data-aos-delay="100">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Informasi Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Metode</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}</td>
                        </tr>
                        @if($transaksi->metode_pembayaran === 'transfer_bank')
                        <tr>
                            <th>Bank</th>
                            <td>{{ $transaksi->nama_bank }}</td>
                        </tr>
                        <tr>
                            <th>No. Rekening</th>
                            <td>{{ $transaksi->no_rekening }}</td>
                        </tr>
                        <tr>
                            <th>Atas Nama</th>
                            <td>{{ $transaksi->atas_nama }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Total Bayar</th>
                            <td class="h5 text-primary font-weight-bold">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaksi->bukti_pembayaran)
                        <tr>
                            <th>Bukti Transfer</th>
                            <td>
                                <a href="{{ $transaksi->bukti_pembayaran_url }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Shipping Information -->
            @if($transaksi->tipe === 'penjualan' && $transaksi->alamat_pengiriman)
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-left" data-aos-delay="200">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Informasi Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="shipping-info">
                        <p class="mb-3">{{ $transaksi->alamat_pengiriman }}</p>
                        <div class="d-flex align-items-center text-muted">
                            <i class="fas fa-user me-2"></i>
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                        <div class="d-flex align-items-center text-muted mt-2">
                            <i class="fas fa-phone me-2"></i>
                            <span>{{ auth()->user()->phone }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Customer Service -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Butuh Bantuan?</h5>
                    <p class="text-muted mb-4">Tim customer service kami siap membantu Anda</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-comment-dots me-2"></i> Live Chat
                        </a>
                        <a href="tel:02112345678" class="btn btn-outline-success">
                            <i class="fas fa-phone me-2"></i> (021) 1234-5678
                        </a>
                        <a href="mailto:info@sportwear.com" class="btn btn-outline-info">
                            <i class="fas fa-envelope me-2"></i> info@sportwear.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Bukti Modal -->
@include('components.modal', [
    'id' => 'uploadModal',
    'title' => 'Upload Bukti Pembayaran',
    'size' => 'modal-lg'
])
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Pilih File</label>
                                <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                                <div class="form-text">Format: JPG, PNG (maks. 2MB)</div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Pastikan bukti transfer jelas terlihat
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="preview-area border rounded-3 p-3 text-center">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Preview akan muncul di sini</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
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
.status-timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-icon {
    position: relative;
    z-index: 2;
}

.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.icon-circle.active {
    transform: scale(1.1);
    box-shadow: 0 0 0 5px rgba(var(--bs-primary-rgb), 0.1);
}

.timeline-line {
    position: absolute;
    top: 40px;
    left: 20px;
    width: 2px;
    height: calc(100% + 1rem);
    z-index: 1;
}

.shipping-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// File preview
document.querySelector('input[name="bukti_pembayaran"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewArea = document.querySelector('.preview-area');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewArea.innerHTML = `
                <img src="${e.target.result}" class="img-fluid rounded-3 mb-2" style="max-height: 200px;">
                <p class="small text-muted mb-0">${file.name}</p>
            `;
        };
        reader.readAsDataURL(file);
    }
});

// Upload form submission
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengupload...';
    submitBtn.disabled = true;
    
    fetch(`/user/transaksi/{{ $transaksi->id }}/upload-bukti`, {
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
});

// Print function
function printTransaction() {
    const printContent = document.querySelector('.container').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
}
</script>
@endpush