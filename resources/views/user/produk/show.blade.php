@extends('user.layouts.app')

@section('title', $produk->nama . ' - SportWear')

@section('content')
<div class="py-8">
    <!-- Breadcrumb -->
    <div class="container mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-primary">
                        <i class="fas fa-home mr-2"></i> Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('user.produk.index') }}" class="ml-2 text-gray-600 hover:text-primary">Produk</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('user.produk.kategori', $produk->kategori->slug) }}" class="ml-2 text-gray-600 hover:text-primary">{{ $produk->kategori->nama }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-primary font-medium">{{ $produk->nama }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    
    <!-- Product Detail Section -->
    <div class="container">
        <div class="grid lg:grid-cols-2 gap-8 mb-8">
            <!-- Product Images -->
            <div data-aos="fade-right">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Main Image -->
                    <div class="relative overflow-hidden">
                        <img src="{{ $produk->gambar_url }}" 
                             alt="{{ $produk->nama }}"
                             class="w-full h-96 object-cover"
                             id="mainProductImage">
                        <div class="absolute top-4 left-4">
                            @if($produk->tipe === 'jual')
                            <span class="px-3 py-1 bg-primary text-white text-sm font-semibold rounded-full">Dijual</span>
                            @elseif($produk->tipe === 'sewa')
                            <span class="px-3 py-1 bg-accent text-white text-sm font-semibold rounded-full">Disewa</span>
                            @else
                            <span class="px-3 py-1 bg-gradient-to-r from-primary to-accent text-white text-sm font-semibold rounded-full">Dijual/Disewa</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Image Thumbnails -->
                    <div class="p-4">
                        <div class="grid grid-cols-4 gap-2">
                            <div class="thumbnail-item active" onclick="changeMainImage('{{ $produk->gambar_url }}')">
                                <img src="{{ $produk->gambar_url }}" 
                                     alt="{{ $produk->nama }}"
                                     class="w-full h-20 object-cover rounded-lg cursor-pointer">
                            </div>
                            <!-- Additional thumbnails would go here -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div data-aos="fade-left">
                <div class="space-y-6">
                    <!-- Category & Stock -->
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full">
                            {{ $produk->kategori->nama }}
                        </span>
                        <div>
                            @if($produk->stok_tersedia > 0)
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                <i class="fas fa-check-circle mr-2"></i> {{ $produk->stok_tersedia }} tersedia
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                <i class="fas fa-times-circle mr-2"></i> Stok habis
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Title -->
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $produk->nama }}</h1>
                    
                    <!-- Rating -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <span class="ml-2 text-gray-600">4.5 (128 ulasan)</span>
                        </div>
                        <div class="text-gray-400">|</div>
                        <div class="text-gray-600">Terjual: 2.5k</div>
                    </div>
                    
                    <!-- Prices -->
                    <div class="space-y-4">
                        @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                        <div>
                            <h3 class="text-2xl font-bold text-primary">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</h3>
                            <p class="text-gray-600 text-sm">Harga beli</p>
                        </div>
                        @endif
                        
                        @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Harga Sewa:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @if($produk->harga_sewa_harian)
                                <div class="price-option border border-gray-200 rounded-lg p-4 text-center hover:border-primary hover:shadow-sm cursor-pointer">
                                    <div class="text-lg font-bold text-primary mb-1">Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}</div>
                                    <p class="text-gray-600 text-sm">Per Hari</p>
                                </div>
                                @endif
                                
                                @if($produk->harga_sewa_mingguan)
                                <div class="price-option border border-gray-200 rounded-lg p-4 text-center hover:border-primary hover:shadow-sm cursor-pointer">
                                    <div class="text-lg font-bold text-primary mb-1">Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}</div>
                                    <p class="text-gray-600 text-sm">Per Minggu</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        Hemat {{ number_format(($produk->harga_sewa_harian * 7 - $produk->harga_sewa_mingguan) / ($produk->harga_sewa_harian * 7) * 100, 0) }}%
                                    </span>
                                </div>
                                @endif
                                
                                @if($produk->harga_sewa_bulanan)
                                <div class="price-option border border-gray-200 rounded-lg p-4 text-center hover:border-primary hover:shadow-sm cursor-pointer">
                                    <div class="text-lg font-bold text-primary mb-1">Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}</div>
                                    <p class="text-gray-600 text-sm">Per Bulan</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        Hemat {{ number_format(($produk->harga_sewa_harian * 30 - $produk->harga_sewa_bulanan) / ($produk->harga_sewa_harian * 30) * 100, 0) }}%
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Short Description -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Deskripsi Singkat</h4>
                        <p class="text-gray-600 leading-relaxed">{{ Str::limit($produk->deskripsi, 200) }}</p>
                    </div>
                    
                    <!-- Quick Specs -->
                    @if($produk->spesifikasi)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Spesifikasi</h4>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(array_slice($produk->spesifikasi, 0, 4) as $key => $value)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <span class="font-medium">{{ $value }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="border-t border-gray-200 pt-6 space-y-4">
                        @if($produk->stok_tersedia > 0)
                        <!-- Quantity -->
                        <div class="flex items-center gap-4">
                            <span class="text-gray-900 font-medium">Jumlah:</span>
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button onclick="decreaseQuantity()" class="px-4 py-2 text-gray-600 hover:text-primary">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="{{ $produk->stok_tersedia }}" 
                                       class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none">
                                <button onclick="increaseQuantity()" class="px-4 py-2 text-gray-600 hover:text-primary">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                            <button onclick="addToCart('jual', false)" 
                                    class="flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Tambahkan ke Keranjang</span>
                            </button>
                            <button onclick="addToCart('jual', true)" 
                                    class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary/5 transition-colors">
                                <i class="fas fa-bolt"></i>
                                <span>Beli Sekarang</span>
                            </button>
                            @endif
                            
                            @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                            <button onclick="showSewaOptions(false)" 
                                    class="flex items-center justify-center gap-2 px-6 py-3 bg-accent text-white font-semibold rounded-lg hover:bg-accent-dark transition-colors">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Sewa Sekarang</span>
                            </button>
                            <button onclick="showSewaOptions(true)" 
                                    class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-accent text-accent font-semibold rounded-lg hover:bg-accent/5 transition-colors">
                                <i class="fas fa-bolt"></i>
                                <span>Sewa & Checkout</span>
                            </button>
                            @endif
                        </div>
                        @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                                <div>
                                    <p class="text-yellow-800 font-medium">Produk sedang tidak tersedia</p>
                                    <p class="text-yellow-700 text-sm">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Details Tabs -->
    <div class="container mb-8">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" data-aos="fade-up">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap">
                    <button id="description-tab" 
                            class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-primary border-b-2 border-transparent hover:border-primary active"
                            onclick="openTab('description')">
                        <i class="fas fa-file-alt mr-2"></i> Deskripsi
                    </button>
                    <button id="specs-tab" 
                            class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-primary border-b-2 border-transparent hover:border-primary"
                            onclick="openTab('specs')">
                        <i class="fas fa-list-alt mr-2"></i> Spesifikasi
                    </button>
                    <button id="reviews-tab" 
                            class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-primary border-b-2 border-transparent hover:border-primary"
                            onclick="openTab('reviews')">
                        <i class="fas fa-star mr-2"></i> Ulasan
                    </button>
                    <button id="faq-tab" 
                            class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-primary border-b-2 border-transparent hover:border-primary"
                            onclick="openTab('faq')">
                        <i class="fas fa-question-circle mr-2"></i> FAQ
                    </button>
                </nav>
            </div>
            
            <!-- Tab Content -->
            <div class="p-8">
                <!-- Description Tab -->
                <div id="description-content" class="tab-content active">
                    <div class="grid lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Tentang {{ $produk->nama }}</h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-600 leading-relaxed mb-4">{{ $produk->deskripsi }}</p>
                                
                                <h4 class="font-semibold text-gray-900 mb-3">Fitur Utama:</h4>
                                <ul class="space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                        <span>Material berkualitas tinggi untuk daya tahan maksimal</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                        <span>Desain ergonomis untuk kenyamanan penggunaan</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                        <span>Cocok untuk pemula hingga profesional</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                        <span>Garansi resmi 1 tahun</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-xl p-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Informasi Penting</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-shipping-fast text-primary mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">Gratis Ongkir</p>
                                            <p class="text-gray-600 text-sm">Minimal pembelian Rp 500.000</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-undo text-primary mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">Garansi 30 Hari</p>
                                            <p class="text-gray-600 text-sm">Pengembalian barang</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-shield-alt text-primary mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">100% Original</p>
                                            <p class="text-gray-600 text-sm">Produk asli dengan garansi</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-headset text-primary mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">Support 24/7</p>
                                            <p class="text-gray-600 text-sm">Via chat & telepon</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Specifications Tab -->
                <div id="specs-content" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="divide-y divide-gray-200">
                                @foreach($produk->spesifikasi ?? [] as $key => $value)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $value }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Kategori</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $produk->kategori->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Stok Tersedia</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $produk->stok_tersedia }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Tipe</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $produk->tipe === 'jual' ? 'Dijual' : ($produk->tipe === 'sewa' ? 'Disewa' : 'Dijual/Disewa') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div id="reviews-content" class="tab-content hidden">
                    <div class="grid lg:grid-cols-3 gap-8">
                        <div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-900 mb-2">4.5</div>
                                <div class="flex justify-center text-yellow-400 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-600">Berdasarkan 128 ulasan</p>
                            </div>
                        </div>
                        
                        <div class="lg:col-span-2">
                            <div class="space-y-6">
                                <!-- Review Item -->
                                <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h5 class="font-semibold text-gray-900">Andi Pratama</h5>
                                            <div class="flex text-yellow-400 mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-gray-500 text-sm">2 minggu lalu</span>
                                    </div>
                                    <p class="text-gray-600">Produk sangat bagus, kualitas sesuai harga. Pengiriman cepat!</p>
                                </div>
                                
                                <!-- Review Item -->
                                <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h5 class="font-semibold text-gray-900">Sari Dewi</h5>
                                            <div class="flex text-yellow-400 mt-1">
                                                @for($i = 1; $i <= 4; $i++)
                                                <i class="fas fa-star"></i>
                                                @endfor
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                        </div>
                                        <span class="text-gray-500 text-sm">1 bulan lalu</span>
                                    </div>
                                    <p class="text-gray-600">Cocok untuk pemula, mudah digunakan dan hasil maksimal.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Tab -->
                <div id="faq-content" class="tab-content hidden">
                    <div class="space-y-4">
                        <!-- FAQ Item -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 bg-gray-50 hover:bg-gray-100 flex justify-between items-center"
                                    onclick="toggleFAQ(this)">
                                <span>Berapa lama proses pengiriman?</span>
                                <i class="fas fa-chevron-down transition-transform"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 hidden">
                                <p class="text-gray-600">Pengiriman dalam kota 1-2 hari kerja, luar kota 3-5 hari kerja.</p>
                            </div>
                        </div>
                        
                        <!-- FAQ Item -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 bg-gray-50 hover:bg-gray-100 flex justify-between items-center"
                                    onclick="toggleFAQ(this)">
                                <span>Apakah produk bergaransi?</span>
                                <i class="fas fa-chevron-down transition-transform"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 hidden">
                                <p class="text-gray-600">Ya, semua produk bergaransi resmi 1 tahun.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="container">
        <h3 class="text-2xl font-bold text-gray-900 mb-6" data-aos="fade-up">Produk Terkait</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
            <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <a href="{{ route('user.produk.show', $related->slug) }}" class="group block">
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $related->gambar_url }}" 
                                 alt="{{ $related->nama }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 group-hover:text-primary transition-colors mb-2">{{ Str::limit($related->nama, 40) }}</h4>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-primary">Rp {{ number_format($related->harga_beli ?? $related->harga_sewa_harian, 0, ',', '.') }}</span>
                                <span class="text-sm text-gray-600">{{ $related->kategori->nama }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@include('user.components.sewa-modal')
@endsection

@push('styles')
<style>
.thumbnail-item.active {
    border: 2px solid var(--primary);
}

.price-option.active {
    border-color: var(--primary);
    background-color: rgba(26, 54, 93, 0.05);
}

.tab-button.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.faq-question.active i {
    transform: rotate(180deg);
}
</style>
@endpush

@push('scripts')
<script>
// Quantity control
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Tab functionality
function openTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(`${tabName}-content`).classList.remove('hidden');
    document.getElementById(`${tabName}-content`).classList.add('active');
    
    // Activate selected tab button
    document.getElementById(`${tabName}-tab`).classList.add('active');
}

// FAQ toggle
function toggleFAQ(button) {
    const answer = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    button.classList.toggle('active');
    answer.classList.toggle('hidden');
}

// Change main image
function changeMainImage(src) {
    document.getElementById('mainProductImage').src = src;
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
}

// Add to cart function
async function addToCart(type, quantity, checkout = false, options = null) {
    const data = {
        product_id: {{ $produk->id }},
        type: type,
        quantity: quantity
    };
    
    if (options) {
        data.options = options;
    }
    
    try {
        const response = await fetch('/user/keranjang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update cart badge
            window.dispatchEvent(new CustomEvent('cartUpdated', {
                detail: { count: result.cart_count }
            }));
            
            if (checkout) {
                window.location.href = '/user/transaksi/create';
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Produk telah ditambahkan ke keranjang',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Select price options
    document.querySelectorAll('.price-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.price-option').forEach(o => {
                o.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    // Close modal on ESC
    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('sewaModal');
            if (!modal.classList.contains('hidden')) {
                closeSewaModal();
            }
        }
    });
});
</script>
@endpush