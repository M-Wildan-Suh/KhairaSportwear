@extends('user.layouts.app')

@section('title', 'Keranjang Belanja - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
        </ol>
    </nav>
    
    <h1 class="h2 font-weight-bold mb-4">Keranjang Belanja</h1>
    
    @if($keranjangs->count() > 0)
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8 mb-4" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <!-- Cart Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            {{ $keranjangs->count() }} Item
                        </h5>
                        <button class="btn btn-outline-danger btn-sm" onclick="clearCart()">
                            <i class="fas fa-trash me-1"></i> Kosongkan Keranjang
                        </button>
                    </div>
                    
                    <!-- Cart Items List -->
                    <div class="cart-items">
                        @foreach($keranjangs as $item)
                        <div class="cart-item border-bottom pb-4 mb-4" data-id="{{ $item->id }}">
                            <div class="row align-items-center">
                                <!-- Product Image -->
                                <div class="col-md-2">
                                    <img src="{{ $item->produk->gambar_url }}" 
                                         alt="{{ $item->produk->nama }}"
                                         class="img-fluid rounded-3"
                                         style="height: 100px; object-fit: cover; width: 100%;">
                                </div>
                                
                                <!-- Product Info -->
                                <div class="col-md-5">
                                    <h6 class="font-weight-bold mb-1">{{ $item->produk->nama }}</h6>
                                    <p class="text-muted small mb-2">{{ Str::limit($item->produk->deskripsi, 60) }}</p>
                                    
                                    <div class="d-flex gap-2 mb-2">
                                        <span class="badge bg-primary">{{ $item->produk->kategori->nama }}</span>
                                        @if($item->tipe === 'jual')
                                        <span class="badge bg-success">Beli</span>
                                        @else
                                        <span class="badge bg-info">Sewa</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Sewa Options -->
                                    @if($item->tipe === 'sewa' && $item->opsi_sewa)
                                    <div class="sewa-options mt-2">
                                        <small class="text-muted d-block">Durasi: {{ $item->opsi_sewa['durasi'] }}</small>
                                        <small class="text-muted d-block">Jumlah Hari: {{ $item->opsi_sewa['jumlah_hari'] }}</small>
                                        <small class="text-muted d-block">Mulai: {{ date('d/m/Y', strtotime($item->opsi_sewa['tanggal_mulai'])) }}</small>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Quantity -->
                                <div class="col-md-2">
                                    <div class="quantity-selector">
                                        <label class="form-label small">Jumlah</label>
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $item->id }}, -1)">-</button>
                                            <input type="number" class="form-control text-center" 
                                                   value="{{ $item->quantity }}" 
                                                   min="1" 
                                                   max="{{ $item->produk->stok_tersedia }}"
                                                   onchange="updateQuantityInput({{ $item->id }}, this.value)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                                        </div>
                                        <small class="text-muted">Stok: {{ $item->produk->stok_tersedia }}</small>
                                    </div>
                                </div>
                                
                                <!-- Price -->
                                <div class="col-md-2 text-end">
                                    <div class="item-price">
                                        <div class="h6 font-weight-bold mb-1">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                        <small class="text-muted">
                                            @if($item->tipe === 'jual')
                                            Rp {{ number_format($item->produk->harga_beli, 0, ',', '.') }} / item
                                            @else
                                            Rp {{ number_format($item->harga, 0, ',', '.') }} / hari
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="col-md-1 text-end">
                                    <button class="btn btn-link text-danger" onclick="removeFromCart({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Continue Shopping -->
                    <div class="text-center mt-4">
                        <a href="{{ route('user.produk.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Lanjutkan Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4" data-aos="fade-left">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h5 class="card-title mb-4">Ringkasan Pesanan</h5>
                    
                    <!-- Summary Details -->
                    <div class="summary-details mb-4">
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
                            <span class="font-weight-bold">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5 font-weight-bold">Total</span>
                            <span class="h4 text-primary font-weight-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <!-- Checkout Button -->
                    <div class="d-grid mb-3">
                        <a href="{{ route('user.transaksi.create') }}" class="btn btn-sport btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i> Lanjut ke Checkout
                        </a>
                    </div>
                    
                    <!-- Payment Methods -->
                    <div class="payment-methods mb-4">
                        <h6 class="font-weight-bold mb-3">Metode Pembayaran</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-university me-1"></i> Transfer Bank
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-money-bill-wave me-1"></i> Tunai
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-qrcode me-1"></i> QRIS
                            </span>
                        </div>
                    </div>
                    
                    <!-- Security Info -->
                    <div class="security-info">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <span>Transaksi aman & terenkripsi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="text-center py-5" data-aos="fade-up">
        <div class="empty-cart">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Keranjang Anda Kosong</h3>
            <p class="text-muted mb-4">Tambahkan produk ke keranjang untuk memulai belanja</p>
            <a href="{{ route('user.produk.index') }}" class="btn btn-sport btn-lg">
                <i class="fas fa-store me-2"></i> Mulai Belanja
            </a>
        </div>
    </div>
    @endif
    
    <!-- Recommended Products -->
    <div class="mt-5">
        <h4 class="mb-4">Rekomendasi untuk Anda</h4>
        <div class="row g-4">
            @foreach(\App\Models\Produk::with('kategori')->active()->inRandomOrder()->limit(4)->get() as $product)
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                @include('components.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background: rgba(43, 108, 176, 0.02);
    padding: 0.5rem;
    margin: -0.5rem;
    border-radius: 8px;
}

.quantity-selector .input-group {
    width: 120px;
}

.quantity-selector .input-group button {
    width: 30px;
}

.quantity-selector input {
    border-left: none;
    border-right: none;
    height: 38px;
}

.empty-cart {
    padding: 3rem 1rem;
}

.sticky-top {
    position: sticky;
    z-index: 1020;
}

.summary-details hr {
    border-top: 2px dashed #dee2e6;
}

.payment-methods .badge {
    padding: 0.5rem 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
// Update quantity with buttons
function updateQuantity(itemId, change) {
    const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
    const quantityInput = itemElement.querySelector('input[type="number"]');
    let currentQuantity = parseInt(quantityInput.value);
    const maxQuantity = parseInt(quantityInput.max);
    const minQuantity = parseInt(quantityInput.min);
    
    let newQuantity = currentQuantity + change;
    if (newQuantity < minQuantity) newQuantity = minQuantity;
    if (newQuantity > maxQuantity) newQuantity = maxQuantity;
    
    if (newQuantity !== currentQuantity) {
        updateCartItem(itemId, newQuantity);
    }
}

// Update quantity with input
function updateQuantityInput(itemId, value) {
    const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
    const quantityInput = itemElement.querySelector('input[type="number"]');
    const maxQuantity = parseInt(quantityInput.max);
    const minQuantity = parseInt(quantityInput.min);
    
    let newQuantity = parseInt(value);
    if (isNaN(newQuantity)) newQuantity = minQuantity;
    if (newQuantity < minQuantity) newQuantity = minQuantity;
    if (newQuantity > maxQuantity) newQuantity = maxQuantity;
    
    quantityInput.value = newQuantity;
    updateCartItem(itemId, newQuantity);
}

// Update cart item via AJAX
function updateCartItem(itemId, quantity) {
    const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
    
    // Show loading
    const originalContent = itemElement.innerHTML;
    itemElement.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <span class="ms-2">Memperbarui...</span>
        </div>
    `;
    
    fetch(`/user/keranjang/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the item display
            itemElement.innerHTML = originalContent;
            
            // Update quantity input value
            const quantityInput = itemElement.querySelector('input[type="number"]');
            quantityInput.value = quantity;
            
            // Update item subtotal
            const priceElement = itemElement.querySelector('.item-price .h6');
            if (priceElement) {
                priceElement.textContent = `Rp ${data.item_subtotal}`;
            }
            
            // Update summary
            updateOrderSummary(data);
            
            // Show success message
            Toast.fire({
                icon: 'success',
                title: data.message
            });
        } else {
            itemElement.innerHTML = originalContent;
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        itemElement.innerHTML = originalContent;
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat memperbarui keranjang', 'error');
    });
}

// Remove item from cart
function removeFromCart(itemId) {
    Swal.fire({
        title: 'Hapus dari keranjang?',
        text: 'Item ini akan dihapus dari keranjang belanja Anda',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            
            fetch(`/user/keranjang/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    itemElement.remove();
                    
                    // Update cart badge
                    window.dispatchEvent(new CustomEvent('cartUpdated', {
                        detail: { count: data.cart_count }
                    }));
                    
                    // Update order summary
                    updateOrderSummary(data);
                    
                    // If cart is empty, show empty state
                    if (data.cart_count === 0) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                    
                    // Show success message
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus item', 'error');
            });
        }
    });
}

// Clear entire cart
function clearCart() {
    Swal.fire({
        title: 'Kosongkan keranjang?',
        text: 'Semua item dalam keranjang akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/user/keranjang/clear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    window.dispatchEvent(new CustomEvent('cartUpdated', {
                        detail: { count: data.cart_count }
                    }));
                    
                    // Reload page to show empty state
                    window.location.reload();
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

// Update order summary
function updateOrderSummary(data) {
    // Update summary elements if they exist
    const subtotalElement = document.querySelector('.summary-details .d-flex:nth-child(1) span:last-child');
    const taxElement = document.querySelector('.summary-details .d-flex:nth-child(2) span:last-child');
    const totalElement = document.querySelector('.summary-details .d-flex:nth-child(4) span:last-child');
    
    if (subtotalElement) subtotalElement.textContent = `Rp ${data.subtotal}`;
    if (taxElement) taxElement.textContent = `Rp ${data.tax}`;
    if (totalElement) totalElement.textContent = `Rp ${data.total}`;
}

// Initialize cart interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to cart items
    document.querySelectorAll('.cart-item').forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
@endpush