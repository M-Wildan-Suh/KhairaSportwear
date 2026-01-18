<div class="product-card-sport group h-full" data-aos="fade-up" data-aos-delay="{{ $delay ?? 0 }}">
    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-300 h-full flex flex-col">
        <!-- Product Image -->
        <div class="relative overflow-hidden bg-gray-100">
            <img src="{{ $product->gambar_url }}" 
                 alt="{{ $product->nama }}" 
                 class="w-full h-48 md:h-56 object-cover transform group-hover:scale-105 transition-transform duration-500"
                 onerror="this.src='{{ asset('images/default-product.jpg') }}'">
            
            <!-- Product Type Badge -->
            @if($product->tipe === 'sewa')
            <div class="absolute top-3 right-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
                Sewa
            </div>
            @elseif($product->tipe === 'jual')
            <div class="absolute top-3 right-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
                Beli
            </div>
            @else
            <div class="absolute top-3 right-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
                Beli/Sewa
            </div>
            @endif
        </div>
        
        <!-- Product Content -->
        <div class="p-4 flex-1 flex flex-col">
            <!-- Title and Stock -->
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-bold text-gray-800 text-lg mb-1 line-clamp-1">{{ $product->nama }}</h5>
                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                    <i class="fas fa-box text-blue-500 mr-1"></i> {{ $product->stok_tersedia }}
                </span>
            </div>
            
            <!-- Description -->
            <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-2">{{ Str::limit($product->deskripsi, 80) }}</p>
            
            <!-- Price Section -->
            <div class="mb-4">
                @if($product->tipe === 'jual' || $product->tipe === 'both')
                <div class="flex items-center mb-2">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg px-3 py-2 w-full">
                        <div class="flex items-center">
                            <i class="fas fa-tag text-blue-600 mr-2"></i>
                            <span class="text-blue-700 font-bold text-lg">
                                Rp {{ number_format($product->harga_beli, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-xs text-blue-600 mt-1">Harga Beli</div>
                    </div>
                </div>
                @endif
                
                @if($product->tipe === 'sewa' || $product->tipe === 'both')
                <div class="space-y-2">
                    <small class="text-gray-500 font-medium">Harga Sewa:</small>
                    <div class="grid grid-cols-3 gap-2">
                        @if($product->harga_sewa_harian)
                        <div class="bg-gradient-to-b from-green-50 to-white rounded-lg p-2 text-center border border-green-100">
                            <div class="text-green-700 font-bold text-sm">Rp {{ number_format($product->harga_sewa_harian, 0, ',', '.') }}</div>
                            <small class="text-green-600 text-xs font-medium">Harian</small>
                        </div>
                        @endif
                        
                        @if($product->harga_sewa_mingguan)
                        <div class="bg-gradient-to-b from-green-50 to-white rounded-lg p-2 text-center border border-green-100">
                            <div class="text-green-700 font-bold text-sm">Rp {{ number_format($product->harga_sewa_mingguan, 0, ',', '.') }}</div>
                            <small class="text-green-600 text-xs font-medium">Mingguan</small>
                        </div>
                        @endif
                        
                        @if($product->harga_sewa_bulanan)
                        <div class="bg-gradient-to-b from-green-50 to-white rounded-lg p-2 text-center border border-green-100">
                            <div class="text-green-700 font-bold text-sm">Rp {{ number_format($product->harga_sewa_bulanan, 0, ',', '.') }}</div>
                            <small class="text-green-600 text-xs font-medium">Bulanan</small>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2 mt-auto">
                <a href="{{ route('produk.show', $product->slug) }}" 
                   class="flex-1 group/view">
                    <div class="flex items-center justify-center gap-2 bg-gradient-to-r from-gray-100 to-gray-50 hover:from-blue-50 hover:to-blue-100 text-blue-600 hover:text-blue-700 font-semibold py-2 px-4 rounded-lg transition-all duration-300 border border-gray-200 hover:border-blue-300">
                        <i class="fas fa-eye"></i>
                        <span>Detail</span>
                        <i class="fas fa-arrow-right text-xs transform group-hover/view:translate-x-1 transition-transform"></i>
                    </div>
                </a>
                
                @if($product->stok_tersedia > 0)
                    @if($product->tipe === 'jual' || $product->tipe === 'both')
                    <button class="add-to-cart bg-gradient-to-r from-orange-500 to-yellow-500 hover:from-orange-600 hover:to-yellow-600 text-white p-3 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            data-product-id="{{ $product->id }}" 
                            data-type="jual"
                            title="Tambah ke Keranjang Beli">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                    @endif
                    
                    @if($product->tipe === 'sewa' || $product->tipe === 'both')
                    <button class="add-to-cart bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white p-3 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            data-product-id="{{ $product->id }}" 
                            data-type="sewa"
                            title="Tambah ke Keranjang Sewa">
                        <i class="fas fa-calendar-plus"></i>
                    </button>
                    @endif
                @else
                <div class="flex-1">
                    <div class="flex items-center justify-center gap-2 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-500 font-semibold py-2 px-4 rounded-lg border border-gray-300">
                        <i class="fas fa-times"></i>
                        <span>Stok Habis</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const type = this.dataset.type;
            
            // Animation effect
            this.classList.add('animate-pulse');
            
            // For now, just show a message
            // In the next phase, we'll implement actual cart functionality
            Swal.fire({
                title: 'Tambah ke Keranjang?',
                text: `Produk akan ditambahkan ke keranjang untuk ${type === 'jual' ? 'pembelian' : 'penyewaan'}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ED8936',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Ya, Tambahkan',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('{{ route("user.keranjang.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            type: type,
                            quantity: 1
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update cart count in navigation
                            const cartCount = document.querySelector('.cart-count');
                            if (cartCount) {
                                cartCount.textContent = data.cart_count;
                                cartCount.classList.add('animate-bounce');
                                setTimeout(() => {
                                    cartCount.classList.remove('animate-bounce');
                                }, 1000);
                            }
                            
                            // Show success notification
                            return {
                                success: true,
                                message: data.message
                            };
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                // Reset animation
                this.classList.remove('animate-pulse');
                
                if (result.isConfirmed && result.value.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: result.value.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
});
</script>

<style>
.product-card-sport .line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}

.product-card-sport .line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-card-sport {
    transition: all 0.3s ease;
}

.product-card-sport:hover {
    transform: translateY(-5px);
}
</style>
@endpush