@extends('user.layouts.app')

@section('title', 'Keranjang Belanja - SportWear')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <!-- Breadcrumb -->
        <div class="container mx-auto px-4 lg:px-8 mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                            <i class="fas fa-home mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <span class="ml-3 text-sm font-medium text-gray-900">Keranjang</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="container mx-auto px-4 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Keranjang Belanja</h1>
                <p class="text-gray-600 mt-2">Kelola item dalam keranjang Anda</p>
            </div>

            @if ($keranjangs->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items Section -->
                    <div class="lg:col-span-2">
                        <!-- Cart Header -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fas fa-shopping-cart text-primary text-xl"></i>
                                    </div>

                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">{{ $keranjangs->count() }} Item di
                                            Keranjang</h2>
                                        <p class="text-gray-600 text-sm">Total: Rp
                                            {{ number_format($subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <button onclick="clearCart()"
                                    class="px-4 py-2.5 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-trash"></i>
                                    <span>Kosongkan Keranjang</span>
                                </button>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="selectAll"
                                        class="w-5 h-5 text-primary rounded border-gray-300" checked>
                                    <label for="selectAll" class="text-sm font-medium text-gray-700">
                                        Pilih Semua
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="space-y-4">
                            @foreach ($keranjangs as $item)
                                <div class="cart-item bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300"
                                    data-id="{{ $item->id }}">

                                    <div class="p-6">
                                        <div class="flex flex-col md:flex-row gap-6">
                                            <!-- Product Image -->
                                            <div class="md:w-1/4">
                                                <div class="relative rounded-xl overflow-hidden bg-gray-100"
                                                    style="padding-bottom: 100%;">
                                                    <img src="{{ $item->produk->gambar_url }}"
                                                        alt="{{ $item->produk->nama }}"
                                                        class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                                    @if ($item->tipe === 'jual')
                                                        <span
                                                            class="absolute top-3 left-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                                            Beli
                                                        </span>
                                                    @else
                                                        <span
                                                            class="absolute top-3 left-3 bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                                            Sewa
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Product Details -->
                                            <div class="md:w-3/4">
                                                <div class="flex flex-col h-full">
                                                    <!-- Header -->
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-start mb-2">
                                                            <h3 class="text-lg font-bold text-gray-900">
                                                                {{ $item->produk->nama }}</h3>
                                                            <div class=" flex items-center gap-4">
                                                                <input type="checkbox"
                                                                    class="cart-checkbox w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary"
                                                                    data-id="{{ $item->id }}"
                                                                    data-subtotal="{{ $item->subtotal }}" checked>
                                                                <button onclick="removeFromCart({{ $item->id }})"
                                                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                                                    <i class="fas fa-times text-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <p class="text-gray-600 text-sm mb-4">
                                                            {{ Str::limit($item->produk->deskripsi, 80) }}</p>

                                                        <!-- Category and Type -->
                                                        <div class="flex flex-wrap gap-2 mb-4">
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                                                <i class="fas fa-tag mr-1.5 text-xs"></i>
                                                                {{ $item->produk->kategori->nama }}
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <i class="fas fa-box mr-1.5 text-xs"></i>
                                                                Stok: {{ $item->produk->stok_tersedia }}
                                                            </span>
                                                        </div>

                                                        @if ($item->bundle)
                                                            <div class=" flex">
                                                                <div
                                                                    class="flex w-auto items-center gap-2 px-4 py-2.5 border-2 border-gray-200 rounded-lg hover:border-primary transition-all duration-200">
                                                                    <span
                                                                        class="font-medium text-gray-700 capitalize">{{ $item->bundle->warna }}
                                                                        - {{ $item->bundle->size }} - Stok :
                                                                        {{ $item->bundle->stok }}</span>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Sewa Details -->
                                                        @if ($item->tipe === 'sewa' && $item->opsi_sewa)
                                                            <div class="bg-blue-50 rounded-xl p-4 mb-4">
                                                                <h4 class="text-sm font-semibold text-blue-900 mb-2">Detail
                                                                    Sewa</h4>
                                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                                    <div>
                                                                        <p class="text-xs text-blue-600">Durasi</p>
                                                                        <p class="text-sm font-semibold text-blue-900">
                                                                            {{ $item->opsi_sewa['durasi'] }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-xs text-blue-600">Jumlah Hari</p>
                                                                        <p class="text-sm font-semibold text-blue-900">
                                                                            {{ $item->opsi_sewa['jumlah_hari'] }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-xs text-blue-600">Mulai</p>
                                                                        <p class="text-sm font-semibold text-blue-900">
                                                                            {{ date('d/m/Y', strtotime($item->opsi_sewa['tanggal_mulai'])) }}
                                                                        </p>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-xs text-blue-600">
                                                                            Harga/{{ $item->opsi_sewa['durasi'] == 'harian' ? 'Hari' : ($item->opsi_sewa['durasi'] == 'mingguan' ? 'Minggu' : 'Bulan') }}
                                                                        </p>
                                                                        <p class="text-sm font-semibold text-blue-900">Rp
                                                                            {{ number_format($item->harga, 0, ',', '.') }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Footer with Quantity and Price -->
                                                    <div
                                                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4 border-t border-gray-200">
                                                        <!-- Quantity Control -->
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-700 mb-2">Jumlah</p>
                                                            <div class="flex items-center">
                                                                <button onclick="updateQuantity({{ $item->id }}, -1)"
                                                                    class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-l-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                                    <i class="fas fa-minus text-gray-600"></i>
                                                                </button>
                                                                <input type="number" value="{{ $item->quantity }}"
                                                                    min="1" max="{{ $item->produk->stok_tersedia }}"
                                                                    onchange="updateQuantityInput({{ $item->id }}, this.value)"
                                                                    class="w-16 h-10 text-center bg-white border-y border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                                                <button onclick="updateQuantity({{ $item->id }}, 1)"
                                                                    class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-r-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                                    {{ $item->quantity >= $item->produk->stok_tersedia ? 'disabled' : '' }}>
                                                                    <i class="fas fa-plus text-gray-600"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Price -->
                                                        <div class="text-right">
                                                            <p class="text-sm text-gray-600 mb-1">
                                                                @if ($item->tipe === 'jual')
                                                                    @ Rp
                                                                    {{ number_format($item->produk->harga_beli, 0, ',', '.') }}
                                                                @else
                                                                    @ Rp
                                                                    {{ number_format($item->harga, 0, ',', '.') }}/{{ $item->opsi_sewa['durasi'] == 'harian' ? 'Hari' : ($item->opsi_sewa['durasi'] == 'mingguan' ? 'Minggu' : 'Bulan') }}
                                                                @endif
                                                            </p>
                                                            <p class="text-xl font-bold text-primary">
                                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Continue Shopping -->
                        <div class="mt-8 text-center">
                            <a href="{{ route('produk.index') }}"
                                class="inline-flex items-center px-6 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-300">
                                <i class="fas fa-arrow-left mr-3"></i>
                                <span>Lanjutkan Belanja</span>
                            </a>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24">
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                                <!-- Header -->
                                <div class="bg-gradient-to-r from-primary to-primary-dark p-6">
                                    <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                        <i class="fas fa-receipt"></i>
                                        Ringkasan Pesanan
                                    </h2>
                                </div>

                                <!-- Summary Details -->
                                <div class="p-6">
                                    <div class="space-y-4">
                                        <!-- Subtotal -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Subtotal</span>
                                            <span id="summary-subtotal" class="text-lg font-semibold text-gray-900">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <!-- PPN -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">PPN (11%)</span>
                                            <span id="summary-tax" class="text-lg font-semibold text-gray-900">
                                                Rp {{ number_format($tax, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <!-- Shipping -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Biaya Pengiriman</span>
                                            <span class="text-lg font-semibold text-green-600">Gratis</span>
                                        </div>

                                        <!-- Divider -->
                                        <div class="border-t border-gray-300 border-dashed my-4"></div>

                                        <!-- Total -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-gray-900">Total</span>
                                            <span id="summary-total" class="text-2xl font-bold text-primary">
                                                Rp {{ number_format($total, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Checkout Button -->
                                    <button onclick="goToCheckout()"
                                        class="w-full mt-6 bg-gradient-to-r from-gray-800 to-gray-950 text-white font-bold py-4 px-6 rounded-xl">
                                        <i class="fas fa-shopping-bag"></i>
                                        Lanjut ke Checkout
                                    </button>


                                    <!-- Payment Methods -->
                                    <div class="mt-8">
                                        <h3 class="text-sm font-semibold text-gray-900 mb-3">METODE PEMBAYARAN</h3>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-200">
                                                <i class="fas fa-university text-blue-600 text-lg mb-2"></i>
                                                <p class="text-xs font-medium text-gray-900">Transfer Bank</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-200">
                                                <i class="fas fa-money-bill-wave text-green-600 text-lg mb-2"></i>
                                                <p class="text-xs font-medium text-gray-900">Tunai</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-200">
                                                <i class="fas fa-qrcode text-purple-600 text-lg mb-2"></i>
                                                <p class="text-xs font-medium text-gray-900">QRIS</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Security Info -->
                                    <div class="mt-6 p-4 bg-green-50 rounded-xl border border-green-200">
                                        <div class="flex items-start gap-3">
                                            <i class="fas fa-shield-alt text-green-600 text-lg mt-0.5"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-green-900">Transaksi Aman</p>
                                                <p class="text-xs text-green-700">Semua transaksi dienkripsi dan dilindungi
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart State -->
                <div class="max-w-2xl mx-auto py-16 text-center" data-aos="fade-up">
                    <div class="w-32 h-32 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-8">
                        <i class="fas fa-shopping-cart text-gray-400 text-5xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Keranjang Anda Kosong</h2>
                    <p class="text-gray-600 text-lg mb-8">Tambahkan produk favorit Anda ke keranjang untuk memulai belanja
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('produk.index') }}"
                            class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-300 gap-3">
                            <i class="fas fa-store"></i>
                            <span>Jelajahi Produk</span>
                        </a>
                        <a href="{{ route('sewa.index') }}"
                            class="inline-flex items-center justify-center px-8 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-300 gap-3">
                            <i class="fas fa-calendar-check"></i>
                            <span>Sewa Peralatan</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .cart-item {
            animation: slideIn 0.3s ease-out;
            animation-fill-mode: both;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar for cart items */
        .cart-items-container {
            scrollbar-width: thin;
            scrollbar-color: #2B6CB0 #F7FAFC;
        }

        .cart-items-container::-webkit-scrollbar {
            width: 6px;
        }

        .cart-items-container::-webkit-scrollbar-track {
            background: #F7FAFC;
            border-radius: 3px;
        }

        .cart-items-container::-webkit-scrollbar-thumb {
            background-color: #2B6CB0;
            border-radius: 3px;
        }

        /* Sticky sidebar */
        .sticky {
            position: sticky;
            top: 1.5rem;
        }

        /* Smooth transitions */
        .quantity-control button {
            transition: all 0.2s ease;
        }

        .quantity-control button:hover:not(:disabled) {
            background-color: #E2E8F0;
        }

        /* Glow effect for checkout button */
        .checkout-btn {
            box-shadow: 0 4px 15px rgba(43, 108, 176, 0.2);
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            box-shadow: 0 6px 20px rgba(43, 108, 176, 0.3);
            transform: translateY(-2px);
        }

        /* Loading animation */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Initialize SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Update quantity with buttons
        function updateQuantity(itemId, change) {
            const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            const quantityInput = itemElement.querySelector('input[type="number"]');
            let currentQuantity = parseInt(quantityInput.value);
            const maxQuantity = parseInt(quantityInput.max);

            let newQuantity = currentQuantity + change;
            if (newQuantity < 1) newQuantity = 1;
            if (newQuantity > maxQuantity) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup',
                    text: `Stok tersedia: ${maxQuantity} unit`,
                    confirmButtonColor: '#2B6CB0'
                });
                return;
            }

            updateCartItem(itemId, newQuantity);
        }

        // Update quantity with input
        function updateQuantityInput(itemId, value) {
            const newQuantity = parseInt(value);
            if (isNaN(newQuantity) || newQuantity < 1) return;

            const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            const maxQuantity = parseInt(itemElement.querySelector('input[type="number"]').max);

            if (newQuantity > maxQuantity) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup',
                    text: `Stok tersedia: ${maxQuantity} unit`,
                    confirmButtonColor: '#2B6CB0'
                });
                return;
            }

            updateCartItem(itemId, newQuantity);
        }

        // Update cart item via AJAX
        async function updateCartItem(itemId, quantity) {
            try {
                const response = await fetch(`/user/keranjang/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity
                    })
                });

                const data = await response.json();

                if (!data.success) throw new Error(data.message);

                // update input value saja
                const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
                const input = itemElement.querySelector('input[type="number"]');
                input.value = quantity;

                const checkbox = itemElement.querySelector('.cart-checkbox');
                checkbox.dataset.subtotal = data.item_subtotal;
                recalculateSummary();

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message
                });
            }
        }


        // Remove item from cart
        async function removeFromCart(itemId) {
            const result = await Swal.fire({
                title: 'Hapus Item?',
                text: 'Item ini akan dihapus dari keranjang Anda',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#4B5563',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/user/keranjang/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Remove item with animation
                        const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
                        itemElement.style.animation = 'slideIn 0.3s ease-out reverse';
                        setTimeout(() => itemElement.remove(), 300);

                        // Update cart count
                        window.dispatchEvent(new CustomEvent('cartUpdated', {
                            detail: {
                                count: data.cart_count
                            }
                        }));

                        // Update order summary
                        updateOrderSummary(data);

                        // If cart is empty, reload page
                        if (data.cart_count === 0) {
                            setTimeout(() => window.location.reload(), 1000);
                        }

                        Toast.fire({
                            icon: 'success',
                            title: 'Item berhasil dihapus'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus',
                        text: 'Terjadi kesalahan',
                        confirmButtonColor: '#2B6CB0'
                    });
                }
            }
        }

        // Clear entire cart
        async function clearCart() {
            const result = await Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: 'Semua item akan dihapus dari keranjang Anda',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#4B5563',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('/user/keranjang/clear', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('cartUpdated', {
                            detail: {
                                count: 0
                            }
                        }));

                        // Animate removal of all items
                        document.querySelectorAll('.cart-item').forEach(item => {
                            item.style.animation = 'slideIn 0.3s ease-out reverse';
                        });

                        setTimeout(() => window.location.reload(), 500);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengosongkan',
                        text: 'Terjadi kesalahan',
                        confirmButtonColor: '#2B6CB0'
                    });
                }
            }
        }

        // Update order summary
        function updateOrderSummary(data) {
            // Update summary values
            const elements = {
                subtotal: document.querySelector('.summary-details div:nth-child(1) span:last-child'),
                tax: document.querySelector('.summary-details div:nth-child(2) span:last-child'),
                total: document.querySelector('.summary-details div:nth-child(4) span:last-child'),
                itemCount: document.querySelector('.cart-header h2')
            };

            // Format currency
            const formatCurrency = (amount) => {
                return 'Rp ' + amount.toLocaleString('id-ID');
            };

            // Update values with animation
            Object.keys(elements).forEach(key => {
                if (elements[key]) {
                    elements[key].style.opacity = '0.5';
                    setTimeout(() => {
                        if (key === 'itemCount') {
                            elements[key].textContent = `${data.cart_count} Item di Keranjang`;
                        } else if (key === 'total') {
                            elements[key].textContent = formatCurrency(data.total);
                        } else if (key === 'subtotal') {
                            elements[key].textContent = formatCurrency(data.subtotal);
                        } else if (key === 'tax') {
                            elements[key].textContent = formatCurrency(data.tax);
                        }
                        elements[key].style.opacity = '1';
                    }, 300);
                }
            });
        }

        // Create cart item HTML (for updating after AJAX)
        function createCartItemHtml(item) {
            return `
        <div class="cart-item bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300" data-id="${item.id}">
            <!-- Your cart item HTML structure here -->
        </div>
    `;
        }

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cart items sequentially
            document.querySelectorAll('.cart-item').forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });

            // Add hover effects
            document.querySelectorAll('.quantity-control button').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    if (!this.disabled) {
                        this.style.transform = 'scale(1.1)';
                    }
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        const selectAllCheckbox = document.getElementById('selectAll');

        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.cart-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            recalculateSummary();
        });

        document.querySelectorAll('.cart-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const allChecked = [...document.querySelectorAll('.cart-checkbox')]
                    .every(c => c.checked);
                selectAllCheckbox.checked = allChecked;
                recalculateSummary();
            });
        });

        function recalculateSummary() {
            let subtotal = 0;

            document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
                subtotal += parseInt(cb.dataset.subtotal);
            });

            const tax = Math.round(subtotal * 0.11);
            const total = subtotal + tax;

            document.querySelector('#summary-subtotal').innerText = formatRupiah(subtotal);
            document.querySelector('#summary-tax').innerText = formatRupiah(tax);
            document.querySelector('#summary-total').innerText = formatRupiah(total);
        }

        function formatRupiah(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        function goToCheckout() {
            const selectedIds = [...document.querySelectorAll('.cart-checkbox:checked')]
                .map(cb => cb.dataset.id);

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Produk',
                    text: 'Pilih minimal satu produk untuk checkout'
                });
                return;
            }

            const query = selectedIds.join(',');
            window.location.href = `/user/transaksi/create?items=${query}`;
        }
    </script>
@endpush
