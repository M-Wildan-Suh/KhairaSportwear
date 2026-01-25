@extends('user.layouts.app')

@section('title', 'Checkout - SportWear')

@section('content')
<div class="py-8">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 mb-6">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <a href="{{ route('user.keranjang.index') }}" class="hover:text-primary transition-colors">Keranjang</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <span class="text-primary font-medium">Checkout</span>
        </nav>
    </div>

    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
        
        <form id="checkoutForm" method="POST" action="{{ route('user.transaksi.store') }}">
            @csrf
            
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Order Items & Information -->
                <div class="lg:col-span-2">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6" data-aos="fade-right">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pesanan</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="py-3 text-left text-gray-700 font-medium">Produk</th>
                                            <th class="py-3 text-center text-gray-700 font-medium">Tipe</th>
                                            <th class="py-3 text-center text-gray-700 font-medium">Qty</th>
                                            <th class="py-3 text-right text-gray-700 font-medium">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($keranjangs as $item)
                                        <tr class="border-b border-gray-100 last:border-0">
                                            <td class="py-4">
                                                <div class="flex items-center gap-4">
                                                    <img src="{{ $item->produk->gambar_url }}" 
                                                         alt="{{ $item->produk->nama }}"
                                                         class="w-16 h-16 object-cover rounded-lg">
                                                    <div>
                                                        <h4 class="font-medium text-gray-900">{{ $item->produk->nama }}</h4>
                                                        <p class="text-sm text-gray-600">{{ $item->produk->kategori->nama }}</p>
                                                        @if($item->tipe === 'sewa' && $item->opsi_sewa)
                                                        <div class="mt-2">
                                                            <div class="flex flex-wrap gap-2">
                                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                                    {{ ucfirst($item->opsi_sewa['durasi']) }}
                                                                </span>
                                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                                                    {{ $item->opsi_sewa['jumlah_hari'] }} hari
                                                                </span>
                                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                                    Mulai: {{ date('d/m/Y', strtotime($item->opsi_sewa['tanggal_mulai'])) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-center">
                                                @if($item->tipe === 'jual')
                                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Beli</span>
                                                @else
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">Sewa</span>
                                                @endif
                                            </td>
                                            <td class="py-4 text-center">
                                                <span class="font-medium">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="py-4 text-right">
                                                <div class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                                <div class="text-sm text-gray-600">
                                                    @if($item->tipe === 'jual')
                                                    Rp {{ number_format($item->produk->harga_beli, 0, ',', '.') }} / item
                                                    @else
                                                    Rp {{ number_format($item->harga, 0, ',', '.') }} / hari
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Order Totals -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="max-w-md ml-auto">
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Subtotal</span>
                                            <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">PPN (11%)</span>
                                            <span class="font-medium">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Biaya Pengiriman</span>
                                            <span class="text-green-600 font-medium">Gratis</span>
                                        </div>
                                        <div class="pt-3 border-t border-gray-200">
                                            <div class="flex justify-between">
                                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                                <span class="text-xl font-bold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    @if($keranjangs->where('tipe', 'jual')->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6" data-aos="fade-right" data-aos-delay="100">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Alamat Pengiriman</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat_pengiriman" 
                                          rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                          required>{{ auth()->user()->address }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">Pastikan alamat lengkap dan jelas untuk pengiriman</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm" data-aos="fade-right" data-aos-delay="200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-credit-card text-primary"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Metode Pembayaran</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Payment Options -->
                            <div class="space-y-4 mb-6">
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-primary hover:bg-primary/5 cursor-pointer transition-all">
                                    <input type="radio" name="metode_pembayaran" value="transfer_bank" class="h-5 w-5 text-primary focus:ring-primary" checked>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-university text-gray-600"></i>
                                            <span class="font-medium text-gray-900">Transfer Bank</span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">Transfer manual ke rekening bank kami</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-primary hover:bg-primary/5 cursor-pointer transition-all">
                                    <input type="radio" name="metode_pembayaran" value="tunai" class="h-5 w-5 text-primary focus:ring-primary">
                                    <div class="ml-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-money-bill-wave text-gray-600"></i>
                                            <span class="font-medium text-gray-900">Tunai</span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">Bayar saat barang diterima</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-primary hover:bg-primary/5 cursor-pointer transition-all">
                                    <input type="radio" name="metode_pembayaran" value="qris" class="h-5 w-5 text-primary focus:ring-primary">
                                    <div class="ml-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-qrcode text-gray-600"></i>
                                            <span class="font-medium text-gray-900">QRIS</span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">Scan QR code untuk pembayaran instan</p>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Bank Transfer Details -->
                            <div id="bankTransferDetails" class="mb-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Informasi Transfer</h4>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-blue-800 mb-2">Transfer ke rekening berikut:</p>
                                            @if(is_array($bankInfo))
                                                @foreach($bankInfo as $bank)
                                                <div class="text-blue-700">{{ $bank }}: {{ $noRekening }} a/n {{ $namaRekening }}</div>
                                                @endforeach
                                            @else
                                                <div class="text-blue-700">{{ $bankInfo }}: {{ $noRekening }} a/n {{ $namaRekening }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nama Bank <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama_bank" placeholder="Contoh: BCA" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            No. Rekening <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="no_rekening" placeholder="1234567890" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Atas Nama <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="atas_nama" placeholder="Nama pemilik rekening" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Notes -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                <textarea name="catatan" rows="3" 
                                          placeholder="Catatan untuk penjual..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <label class="flex items-start">
                                    <input type="checkbox" id="agreeTerms" 
                                           class="h-5 w-5 text-primary focus:ring-primary rounded mt-1" required>
                                    <span class="ml-3 text-gray-700">
                                        Saya setuju dengan 
                                        <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a> dan 
                                        <a href="#" class="text-primary hover:underline">Kebijakan Privasi</a> SportWear
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1" data-aos="fade-left">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-32">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Konfirmasi Pesanan</h3>
                            
                            <!-- Customer Info -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-3">Informasi Pelanggan</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-user text-gray-400 mr-3 w-5"></i>
                                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                                        <span class="text-gray-700">{{ auth()->user()->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                                        <span class="text-gray-700">{{ auth()->user()->phone }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-3">Ringkasan</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Items</span>
                                        <span class="font-medium">{{ $keranjangs->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">PPN</span>
                                        <span class="font-medium">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-semibold text-gray-900">Total</span>
                                        <span class="text-xl font-bold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button type="submit" id="submitOrder" 
                                        class="w-full py-3 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-950 transition-colors flex items-center justify-center gap-2">
                                    <i class="fas fa-lock"></i>
                                    <span>Buat Pesanan</span>
                                </button>
                                
                                <a href="{{ route('user.keranjang.index') }}" 
                                   class="w-full py-3 border border-primary text-primary font-semibold rounded-lg hover:bg-primary/5 transition-colors flex items-center justify-center gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Kembali ke Keranjang</span>
                                </a>
                            </div>
                            
                            <!-- Security Info -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="space-y-3">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-shield-alt text-green-500 mr-3"></i>
                                        <span class="text-gray-600">Transaksi 100% Aman</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-lock text-green-500 mr-3"></i>
                                        <span class="text-gray-600">Data terenkripsi SSL</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-shield-check text-green-500 mr-3"></i>
                                        <span class="text-gray-600">Garansi uang kembali</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-xl max-w-sm w-full p-8 text-center">
            <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Memproses Pesanan...</h3>
            <p class="text-gray-600">Mohon tunggu sebentar</p>
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

/* Custom checkbox and radio */
input[type="checkbox"]:checked,
input[type="radio"]:checked {
    border-color: var(--primary);
    background-color: var(--primary);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Hover effects */
.hover\:bg-primary\/5:hover {
    background-color: rgba(26, 54, 93, 0.05);
}

/* Table styles */
table tbody tr:hover {
    background-color: rgba(26, 54, 93, 0.02);
}

/* Custom animations */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endpush

@push('scripts')
<script>
// Toggle bank transfer details
const bankTransferRadio = document.querySelector('input[value="transfer_bank"]');
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

// Initialize bank transfer details
toggleBankTransferDetails();

// Add event listeners to payment method radios
document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
    radio.addEventListener('change', toggleBankTransferDetails);
});

// Form submission
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate terms agreement
    if (!document.getElementById('agreeTerms').checked) {
        showToast('error', 'Anda harus menyetujui Syarat & Ketentuan');
        return;
    }
    
    // Show loading modal
    const loadingModal = document.getElementById('loadingModal');
    loadingModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        // Hide loading modal
        loadingModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
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
                    <p class="mb-4">Pesanan Anda telah berhasil dibuat dengan kode: <strong>${data.transaction_code}</strong></p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <p class="font-medium text-blue-800 mb-1">Instruksi Pembayaran:</p>
                                <p class="text-sm text-blue-700">Silakan upload bukti pembayaran di halaman transaksi untuk mempercepat proses verifikasi.</p>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Lihat Transaksi',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#1A365D',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = data.redirect;
                }
            });
        } else {
            showToast('error', data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        // Hide loading modal
        loadingModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memproses pesanan');
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

// Auto-fill bank transfer fields
document.querySelector('input[name="nama_bank"]')?.addEventListener('input', function() {
    const bankName = this.value.toLowerCase();
    const atasNamaField = document.querySelector('input[name="atas_nama"]');
    
    if (bankName && !atasNamaField.value) {
        if (bankName.includes('bca') || bankName.includes('mandiri') || bankName.includes('bni')) {
            atasNamaField.value = '{{ auth()->user()->name }}';
        }
    }
});

// Initialize AOS animations
document.addEventListener('DOMContentLoaded', function() {
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
            const loadingModal = document.getElementById('loadingModal');
            if (!loadingModal.classList.contains('hidden')) {
                loadingModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
    });
});

// Format currency inputs
document.querySelectorAll('input[name="no_rekening"]').forEach(input => {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
});
</script>
@endpush