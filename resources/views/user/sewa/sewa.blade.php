@extends('user.layouts.app')

@section('title', 'Checkout Sewa - SportWear')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <!-- Progress Steps -->
    <div class="container mx-auto px-4 lg:px-8 mb-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between relative">
                <!-- Progress Line -->
                <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 -z-10"></div>
                <div class="absolute top-1/2 left-0 w-full h-1 bg-primary -translate-y-1/2 -z-10 progress-line" style="width: 33%"></div>
                
                <!-- Steps -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg">
                        1
                    </div>
                    <span class="mt-2 text-sm font-semibold text-primary">Checkout</span>
                </div>
                
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg">
                        2
                    </div>
                    <span class="mt-2 text-sm font-semibold text-gray-600">Pembayaran</span>
                </div>
                
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg">
                        3
                    </div>
                    <span class="mt-2 text-sm font-semibold text-gray-600">Selesai</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 lg:px-8">
        <form action="{{ route('user.checkout.sewa.store') }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Order Details -->
                <div class="lg:col-span-2">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            Detail Sewa
                        </h2>
                        
                        <div class="space-y-4">
                            @foreach($sewaItems as $item)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="flex gap-4">
                                    <!-- Product Image -->
                                    <div class="w-20 h-20 flex-shrink-0">
                                        <img src="{{ $item->produk->gambar_url }}" 
                                             alt="{{ $item->produk->nama }}"
                                             class="w-full h-full object-cover rounded-lg">
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1">
                                        <div class="flex justify-between">
                                            <h3 class="font-bold text-gray-900">{{ $item->produk->nama }}</h3>
                                            <span class="text-lg font-bold text-primary">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mt-1">{{ $item->produk->kategori->nama }}</p>
                                        
                                        <!-- Sewa Details -->
                                        <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <p class="text-gray-500">Durasi</p>
                                                <p class="font-semibold">{{ ucfirst($item->opsi_sewa['durasi']) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500">Jumlah Hari</p>
                                                <p class="font-semibold">{{ $item->opsi_sewa['jumlah_hari'] }} hari</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500">Tanggal Mulai</p>
                                                <p class="font-semibold">{{ date('d/m/Y', strtotime($item->opsi_sewa['tanggal_mulai'])) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500">Qty</p>
                                                <p class="font-semibold">{{ $item->quantity }} unit</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-truck text-primary"></i>
                            Informasi Pengiriman
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Delivery Method -->
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-900 mb-3">
                                    Metode Pengambilan
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative">
                                        <input type="radio" name="jenis_pengiriman" value="pickup" 
                                               class="sr-only peer" checked>
                                        <div class="border-2 border-gray-200 rounded-xl p-4 text-center cursor-pointer
                                                    peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary transition-all duration-200">
                                            <i class="fas fa-store text-2xl text-gray-600 mb-2"></i>
                                            <p class="font-semibold">Ambil di Toko</p>
                                            <p class="text-sm text-gray-500 mt-1">Gratis</p>
                                        </div>
                                    </label>
                                    
                                    <label class="relative">
                                        <input type="radio" name="jenis_pengiriman" value="delivery" 
                                               class="sr-only peer">
                                        <div class="border-2 border-gray-200 rounded-xl p-4 text-center cursor-pointer
                                                    peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary transition-all duration-200">
                                            <i class="fas fa-truck text-2xl text-gray-600 mb-2"></i>
                                            <p class="font-semibold">Diantar</p>
                                            <p class="text-sm text-gray-500 mt-1">Biaya tambahan</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Address for Delivery -->
                            <div class="col-span-2 hidden" id="addressField">
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Alamat Pengiriman
                                </label>
                                <textarea name="alamat_pengiriman" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="Masukkan alamat lengkap pengiriman">{{ old('alamat_pengiriman', $alamat) }}</textarea>
                                @error('alamat_pengiriman')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Additional Notes -->
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Catatan (Opsional)
                                </label>
                                <textarea name="catatan" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="Catatan tambahan untuk penjual">{{ old('catatan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-credit-card text-primary"></i>
                            Metode Pembayaran
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Payment Options -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(['transfer' => 'Transfer Bank', 'tunai' => 'Tunai', 'qris' => 'QRIS'] as $value => $label)
                                <label class="relative">
                                    <input type="radio" name="metode_pembayaran" value="{{ $value }}" 
                                           class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center cursor-pointer
                                                peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary transition-all duration-200">
                                        @if($value === 'transfer')
                                            <i class="fas fa-university text-2xl text-gray-600 mb-2"></i>
                                        @elseif($value === 'tunai')
                                            <i class="fas fa-money-bill-wave text-2xl text-gray-600 mb-2"></i>
                                        @else
                                            <i class="fas fa-qrcode text-2xl text-gray-600 mb-2"></i>
                                        @endif
                                        <p class="font-semibold">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            
                            <!-- Payment Details (Conditional) -->
                            <div id="bankDetails" class="hidden space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                                            Nama Bank
                                        </label>
                                        <input type="text" name="nama_bank" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                               placeholder="Contoh: BCA">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                                            No. Rekening
                                        </label>
                                        <input type="text" name="no_rekening" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                               placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                                            Atas Nama
                                        </label>
                                        <input type="text" name="atas_nama" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                               placeholder="Nama pemilik rekening">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Upload Bukti Pembayaran (Opsional)
                                    <span class="text-xs text-gray-500 font-normal"> - Dapat diupload nanti</span>
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary transition-colors duration-200">
                                    <input type="file" name="bukti_pembayaran" id="buktiPembayaran" 
                                           class="hidden" accept="image/*">
                                    <label for="buktiPembayaran" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 font-semibold">Upload bukti pembayaran</p>
                                        <p class="text-gray-500 text-sm mt-1">Format: JPG, PNG (Max 2MB)</p>
                                    </label>
                                    <div id="filePreview" class="mt-4 hidden">
                                        <img id="previewImage" class="max-w-xs mx-auto rounded-lg">
                                        <button type="button" onclick="removeImage()" 
                                                class="mt-2 text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-primary to-primary-dark p-6">
                                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                    <i class="fas fa-receipt"></i>
                                    Ringkasan Sewa
                                </h2>
                            </div>

                            <!-- Summary Details -->
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Subtotal -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Subtotal Sewa</span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- PPN -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">PPN (11%)</span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            Rp {{ number_format($tax, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- Shipping -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Pengiriman</span>
                                        <span class="text-lg font-semibold text-green-600">Gratis</span>
                                    </div>

                                    <!-- Divider -->
                                    <div class="border-t border-gray-300 my-4"></div>

                                    <!-- Total -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-xl font-bold text-gray-900">Total</span>
                                        <span class="text-2xl font-bold text-primary">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                                    <label class="flex items-start gap-3">
                                        <input type="checkbox" name="terms" 
                                               class="mt-1" required>
                                        <span class="text-sm text-gray-700">
                                            Saya setuju dengan 
                                            <a href="#" class="text-primary hover:text-primary-dark font-semibold">
                                                Syarat & Ketentuan
                                            </a> 
                                            dan 
                                            <a href="#" class="text-primary hover:text-primary-dark font-semibold">
                                                Kebijakan Privasi
                                            </a>
                                        </span>
                                    </label>
                                    @error('terms')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Checkout Button -->
                                <button type="submit" 
                                        class="w-full mt-6 bg-gradient-to-r from-primary to-primary-dark text-white font-bold py-4 px-6 rounded-xl hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-lock"></i>
                                    <span>Buat Pesanan Sewa</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>

                                <!-- Security Info -->
                                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-shield-alt text-green-600"></i>
                                        <p class="text-xs text-green-700 font-semibold">
                                            Transaksi 100% Aman & Terjamin
                                        </p>
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

<!-- Confirmation Modal -->
<div id="confirmationModal" 
     class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Konfirmasi Pesanan Sewa</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Apakah Anda yakin ingin membuat pesanan sewa ini?</p>
            <p class="text-sm text-gray-500">Pesanan akan diproses setelah pembayaran diverifikasi oleh admin.</p>
        </div>
        <div class="p-6 border-t flex justify-end gap-3">
            <button type="button" onclick="closeModal()" 
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Batal
            </button>
            <button type="button" onclick="submitForm()" 
                    class="px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark">
                Ya, Buat Pesanan
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress-line {
    transition: width 0.3s ease;
}

.modal {
    z-index: 9999;
}

.sticky {
    position: sticky;
    top: 1.5rem;
}

/* Custom checkbox */
input[type="checkbox"] {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid #D1D5DB;
    cursor: pointer;
    transition: all 0.2s;
}

input[type="checkbox"]:checked {
    background-color: var(--primary);
    border-color: var(--primary);
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}

/* File upload hover */
#buktiPembayaran + label:hover {
    border-color: var(--primary);
}

/* Radio card hover effect */
label.relative > div {
    transition: all 0.2s ease;
}

label.relative > div:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
// Show/hide address field based on delivery method
document.querySelectorAll('input[name="jenis_pengiriman"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const addressField = document.getElementById('addressField');
        if (this.value === 'delivery') {
            addressField.classList.remove('hidden');
            addressField.querySelector('textarea').required = true;
        } else {
            addressField.classList.add('hidden');
            addressField.querySelector('textarea').required = false;
        }
    });
});

// Show/hide bank details based on payment method
document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const bankDetails = document.getElementById('bankDetails');
        if (this.value === 'transfer') {
            bankDetails.classList.remove('hidden');
            // Make bank fields required
            document.querySelectorAll('#bankDetails input').forEach(input => {
                input.required = true;
            });
        } else {
            bankDetails.classList.add('hidden');
            // Remove required from bank fields
            document.querySelectorAll('#bankDetails input').forEach(input => {
                input.required = false;
            });
        }
    });
});

// Image preview for payment proof
document.getElementById('buktiPembayaran').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('filePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('buktiPembayaran').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

// Form submission with confirmation
function submitForm() {
    document.getElementById('checkoutForm').submit();
}

function closeModal() {
    document.getElementById('confirmationModal').classList.add('hidden');
}

// Show confirmation modal on form submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    document.getElementById('confirmationModal').classList.remove('hidden');
});

// Progress bar animation
document.addEventListener('DOMContentLoaded', function() {
    const progressLine = document.querySelector('.progress-line');
    setTimeout(() => {
        progressLine.style.width = '33%';
    }, 500);
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Add tooltip for payment methods
    const paymentCards = document.querySelectorAll('label.relative > div');
    paymentCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const method = this.closest('label').querySelector('input').value;
            let tooltipText = '';
            
            switch(method) {
                case 'transfer':
                    tooltipText = 'Transfer ke rekening bank SportWear';
                    break;
                case 'tunai':
                    tooltipText = 'Bayar tunai saat pengambilan/pengiriman';
                    break;
                case 'qris':
                    tooltipText = 'Scan QRIS untuk pembayaran';
                    break;
            }
            
            // You could add a tooltip library here
            console.log(tooltipText);
        });
    });
});

// Real-time form validation
const form = document.getElementById('checkoutForm');
const submitButton = form.querySelector('button[type="submit"]');

form.addEventListener('input', function() {
    let isValid = true;
    
    // Check required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
        }
    });
    
    // Check terms checkbox
    const termsCheckbox = form.querySelector('input[name="terms"]');
    if (!termsCheckbox.checked) {
        isValid = false;
    }
    
    submitButton.disabled = !isValid;
});
</script>
@endpush