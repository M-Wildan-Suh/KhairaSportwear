@extends('user.layouts.app')

@section('title', 'Katalog Produk - SportWear')

@section('content')
<div class="container py-8">
    <!-- Page Header -->
    <div class="mb-8" data-aos="fade-down">
        <div class="bg-gradient-to-r from-primary to-primary-dark rounded-2xl p-8 text-white">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold mb-3">Katalog Produk</h1>
                    <p class="text-primary-light text-lg">Temukan alat olahraga premium untuk kebutuhan Anda</p>
                </div>
                <div class="mt-6 lg:mt-0">
                    <div class="relative">
                        <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-dumbbell text-white text-3xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-12 h-12 bg-accent rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filter -->
        <div class="lg:w-1/4" data-aos="fade-right">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-32">
                <div class="p-6">
                    <!-- Search -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-search text-primary"></i>
                            Cari Produk
                        </h3>
                        <form action="{{ route('user.produk.search') }}" method="GET">
                            <div class="relative">
                                <input type="text" 
                                       name="q" 
                                       value="{{ request('search') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                       placeholder="Cari produk...">
                                <button type="submit" class="absolute right-3 top-3 text-primary hover:text-primary-dark">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Product Type Filter -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-filter text-primary"></i>
                            Tipe Produk
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('user.produk.index') }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-lg {{ !request('tipe') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-boxes"></i>
                                    Semua Produk
                                </span>
                                <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">{{ $totalProducts }}</span>
                            </a>
                            <a href="{{ route('user.produk.index', ['tipe' => 'jual']) }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-lg {{ request('tipe') == 'jual' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-shopping-cart"></i>
                                    Untuk Dibeli
                                </span>
                                <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">{{ $jualCount }}</span>
                            </a>
                            <a href="{{ route('user.produk.index', ['tipe' => 'sewa']) }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-lg {{ request('tipe') == 'sewa' ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-gray-50' }}">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt"></i>
                                    Untuk Disewa
                                </span>
                                <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">{{ $sewaCount }}</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-tags text-primary"></i>
                            Kategori
                        </h3>
                        <div class="space-y-2">
                            @foreach($kategoris as $kategori)
                            <a href="{{ route('user.produk.index', ['kategori' => $kategori->slug]) }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 {{ request('kategori') == $kategori->slug ? 'bg-primary/10 text-primary border-l-4 border-primary' : '' }}">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-{{ $kategori->icon ?? 'tag' }}"></i>
                                    {{ $kategori->nama }}
                                </span>
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $kategori->produks_count ?? 0 }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Sort -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-sort-amount-down text-primary"></i>
                            Urutkan
                        </h3>
                        <form id="sortForm" action="{{ route('user.produk.index') }}" method="GET">
                            <!-- Preserve existing filters -->
                            @if(request('tipe'))
                                <input type="hidden" name="tipe" value="{{ request('tipe') }}">
                            @endif
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('view'))
                                <input type="hidden" name="view" value="{{ request('view') }}">
                            @endif
                            
                            <select name="sort" 
                                    onchange="this.form.submit()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                <option value="harga_terendah" {{ request('sort') == 'harga_terendah' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="harga_tertinggi" {{ request('sort') == 'harga_tertinggi' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="nama_az" {{ request('sort') == 'nama_az' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="nama_za" {{ request('sort') == 'nama_za' ? 'selected' : '' }}>Nama Z-A</option>
                            </select>
                        </form>
                    </div>
                    
                    <!-- Featured Products -->
                    @if($featuredProducts->count() > 0)
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-crown text-accent"></i>
                            Unggulan
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($featuredProducts as $featured)
                            <a href="{{ route('user.produk.show', $featured->slug) }}" 
                               class="group">
                                <div class="bg-gray-50 rounded-lg p-3 hover:bg-primary/5 transition-colors border border-gray-200 hover:border-primary/30">
                                    <img src="{{ $featured->gambar_url }}" 
                                         alt="{{ $featured->nama }}"
                                         class="w-full h-20 object-cover rounded-lg mb-2">
                                    <h6 class="font-medium text-gray-900 text-sm truncate group-hover:text-primary">{{ Str::limit($featured->nama, 20) }}</h6>
                                    <div class="text-primary font-semibold text-sm">
                                        @if($featured->tipe === 'jual')
                                        Rp {{ number_format($featured->harga_beli, 0, ',', '.') }}
                                        @else
                                        Rp {{ number_format($featured->harga_sewa_harian, 0, ',', '.') }}/hr
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="lg:w-3/4">
            <!-- Header & Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4" data-aos="fade-left">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        @if(request('kategori'))
                            {{ $currentKategori->nama ?? 'Kategori' }}
                        @elseif(request('search'))
                            Hasil Pencarian: "{{ request('search') }}"
                        @elseif(request('tipe'))
                            {{ request('tipe') == 'jual' ? 'Produk Dijual' : 'Produk Disewa' }}
                        @else
                            Semua Produk
                        @endif
                    </h2>
                    <p class="text-gray-600">{{ $produks->total() }} produk ditemukan</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Clear Filters Button -->
                    @if(request()->anyFilled(['tipe', 'kategori', 'search', 'sort']))
                    <a href="{{ route('user.produk.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-times"></i>
                        Reset Filter
                    </a>
                    @endif
                    
                    <!-- View Toggle -->
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg border {{ $view == 'grid' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}"
                                onclick="switchView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="p-2 rounded-lg border {{ $view == 'list' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}"
                                onclick="switchView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid View -->
            <div id="gridView" class="{{ $view == 'grid' ? 'block' : 'hidden' }}">
                @if($produks->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="productsContainer">
                    @foreach($produks as $produk)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="bg-white rounded-xl border border-gray-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 overflow-hidden group">
                            <!-- Product Image -->
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $produk->gambar_url }}" 
                                     alt="{{ $produk->nama }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                
                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    @if($produk->tipe === 'jual')
                                    <span class="px-2 py-1 bg-primary text-white text-xs font-semibold rounded-full">Dijual</span>
                                    @elseif($produk->tipe === 'sewa')
                                    <span class="px-2 py-1 bg-accent text-white text-xs font-semibold rounded-full">Disewa</span>
                                    @else
                                    <span class="px-2 py-1 bg-gradient-to-r from-primary to-accent text-white text-xs font-semibold rounded-full">Dijual/Disewa</span>
                                    @endif
                                    @if($produk->stok_tersedia == 0)
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">Habis</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-5">
                                <div class="mb-3">
                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $produk->kategori->nama }}</span>
                                </div>
                                
                                <h3 class="font-semibold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ $produk->nama }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($produk->deskripsi, 80) }}</p>
                                
                                <!-- Prices -->
                                <div class="mb-4">
                                    @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-primary">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                    <div class="text-gray-600 text-sm mt-1">
                                        Sewa: <span class="font-semibold">Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}/hari</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">
                                        <i class="fas fa-box mr-1"></i> {{ $produk->stok_tersedia }} tersedia
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('user.produk.show', $produk->slug) }}" 
                                           class="px-3 py-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($produk->stok_tersedia > 0)
                                            @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                                            <button onclick="addToCart({{ $produk->id }}, 'jual')" 
                                                    class="px-3 py-2 bg-primary text-white hover:bg-primary-dark rounded-lg transition-colors">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                            @endif
                                            @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                            <button onclick="addToCart({{ $produk->id }}, 'sewa')" 
                                                    class="px-3 py-2 bg-accent text-white hover:bg-accent-dark rounded-lg transition-colors">
                                                <i class="fas fa-calendar-plus"></i>
                                            </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Empty State -->
                <div class="text-center py-12" data-aos="fade-up">
                    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Produk tidak ditemukan</h3>
                    <p class="text-gray-600 mb-6">Coba gunakan filter yang berbeda</p>
                    <a href="{{ route('user.produk.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                        <i class="fas fa-redo"></i> Reset Pencarian
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Products List View -->
            <div id="listView" class="{{ $view == 'list' ? 'block' : 'hidden' }}">
                @if($produks->count() > 0)
                <div class="space-y-4" id="listProductsContainer">
                    @foreach($produks as $produk)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="bg-white rounded-xl border border-gray-200 hover:shadow-lg transition-all duration-300 overflow-hidden group">
                            <div class="flex flex-col md:flex-row">
                                <!-- Product Image -->
                                <div class="md:w-1/4">
                                    <img src="{{ $produk->gambar_url }}" 
                                         alt="{{ $produk->nama }}"
                                         class="w-full h-48 md:h-full object-cover">
                                </div>
                                
                                <!-- Product Details -->
                                <div class="md:w-3/4 p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                        <div class="flex-1">
                                            <!-- Badges -->
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">{{ $produk->kategori->nama }}</span>
                                                @if($produk->tipe === 'jual')
                                                <span class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">Dijual</span>
                                                @elseif($produk->tipe === 'sewa')
                                                <span class="px-2 py-1 bg-accent/10 text-accent text-xs font-medium rounded-full">Disewa</span>
                                                @else
                                                <span class="px-2 py-1 bg-gradient-to-r from-primary/10 to-accent/10 text-primary text-xs font-medium rounded-full">Dijual/Disewa</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Title & Description -->
                                            <h3 class="font-semibold text-gray-900 text-lg mb-2">{{ $produk->nama }}</h3>
                                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($produk->deskripsi, 150) }}</p>
                                            
                                            <!-- Stock & Rating -->
                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-box"></i> {{ $produk->stok_tersedia }} tersedia
                                                </span>
                                                @if($produk->rating > 0)
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-star text-yellow-400"></i> {{ number_format($produk->rating, 1) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Price & Actions -->
                                        <div class="lg:w-1/3">
                                            <div class="space-y-4">
                                                <!-- Prices -->
                                                @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                                                <div>
                                                    <div class="text-lg font-bold text-primary">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</div>
                                                    <div class="text-gray-600 text-sm">Harga beli</div>
                                                </div>
                                                @endif
                                                
                                                @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                                <div>
                                                    <div class="text-lg font-bold text-accent">Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}/hari</div>
                                                    <div class="text-gray-600 text-sm">Harga sewa harian</div>
                                                </div>
                                                @endif
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('user.produk.show', $produk->slug) }}" 
                                                       class="flex-1 px-4 py-2 border border-primary text-primary font-medium rounded-lg hover:bg-primary/5 transition-colors text-center">
                                                        <i class="fas fa-eye mr-2"></i> Detail
                                                    </a>
                                                    
                                                    @if($produk->stok_tersedia > 0)
                                                        @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                                                        <button onclick="addToCart({{ $produk->id }}, 'jual')" 
                                                                class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </button>
                                                        @endif
                                                        @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                                        <button onclick="addToCart({{ $produk->id }}, 'sewa')" 
                                                                class="px-4 py-2 bg-accent text-white font-medium rounded-lg hover:bg-accent-dark transition-colors">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- Pagination (Tetap dipertahankan untuk fallback) -->
            @if($produks->hasPages())
            <div class="mt-8" data-aos="fade-up" id="paginationContainer">
                {{ $produks->withQueryString()->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
            @endif
            
            <!-- Loading Indicator untuk Infinite Scroll -->
            <div id="loadingIndicator" class="hidden mt-8 text-center">
                <div class="inline-flex flex-col items-center">
                    <div class="spinner w-12 h-12 mb-4"></div>
                    <p class="text-gray-600">Memuat produk...</p>
                </div>
            </div>
            
            <!-- End of Content Marker -->
            <div id="endOfContent" class="hidden mt-8 text-center py-4">
                <p class="text-gray-500">😊 Anda telah mencapai akhir daftar produk</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div id="quickViewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Content loaded via AJAX -->
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

/* View toggle animation */
#gridView, #listView {
    transition: opacity 0.3s ease;
}

/* Product card hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Custom scrollbar for modal */
#quickViewModal::-webkit-scrollbar {
    width: 8px;
}

#quickViewModal::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#quickViewModal::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 4px;
}

/* Loading spinner */
.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
// Global variables
let isLoading = false;
let currentPage = 1;
let hasMorePages = true;
let observer = null;
let currentView = 'grid';

// Switch between grid and list view
function switchView(view) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const url = new URL(window.location.href);
    
    currentView = view;
    
    if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        url.searchParams.set('view', 'grid');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        url.searchParams.set('view', 'list');
    }
    
    // Save preference
    localStorage.setItem('productView', view);
    
    // Update URL without page reload
    window.history.pushState({}, '', url);
    
    // Re-initialize infinite scroll for the current view
    setTimeout(() => initInfiniteScroll(), 100);
}

// Add to cart function
function addToCart(productId, type) {
    const productName = document.querySelector(`[data-product-id="${productId}"]`)?.dataset.productName || 'produk';
    
    Swal.fire({
        title: 'Tambahkan ke Keranjang?',
        text: `Tambahkan ${productName} ke keranjang ${type === 'jual' ? 'pembelian' : 'penyewaan'}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1A365D',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Ya, Tambahkan',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const response = await fetch('/user/keranjang', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        type: type,
                        quantity: 1
                    })
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                return await response.json();
            } catch (error) {
                Swal.showValidationMessage(`Gagal: ${error.message}`);
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            if (result.value.success) {
                // Update cart badge
                window.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: result.value.cart_count }
                }));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Produk telah ditambahkan ke keranjang',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
}

// Quick view modal
function showQuickView(slug) {
    const modal = document.getElementById('quickViewModal');
    const content = modal.querySelector('div > div');
    
    // Show loading
    content.innerHTML = `
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold">Memuat...</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex justify-center py-12">
                <div class="spinner"></div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Load product details
    fetch(`/user/produk/${slug}/quick-view`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = data.html;
            
            // Initialize AOS for modal content
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
            
            // Re-initialize cart buttons in modal
            initCartButtonsInModal();
        })
        .catch(error => {
            content.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold">Error</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center py-12">
                        <p class="text-gray-600">Gagal memuat detail produk.</p>
                        <button onclick="showQuickView('${slug}')" 
                                class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            `;
        });
}

function closeModal() {
    const modal = document.getElementById('quickViewModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function initCartButtonsInModal() {
    document.querySelectorAll('[onclick^="addToCart"]').forEach(button => {
        const onclick = button.getAttribute('onclick');
        const match = onclick.match(/addToCart\((\d+),\s*'(\w+)'\)/);
        if (match) {
            button.onclick = () => {
                addToCart(match[1], match[2]);
                closeModal();
            };
        }
    });
}

// Initialize infinite scroll
function initInfiniteScroll() {
    // Hentikan observer sebelumnya jika ada
    if (observer) {
        observer.disconnect();
        observer = null;
    }
    
    // Reset state
    isLoading = false;
    currentPage = {{ $produks->currentPage() }};
    
    // Cek apakah ada halaman berikutnya dari data Laravel
    hasMorePages = {{ $produks->hasMorePages() ? 'true' : 'false' }};
    
    // Jika tidak ada halaman berikutnya, tidak perlu setup infinite scroll
    if (!hasMorePages) {
        const endOfContent = document.getElementById('endOfContent');
        if (endOfContent) endOfContent.classList.remove('hidden');
        return;
    }
    
    // Buat sentinel element untuk diamati
    let sentinel = document.getElementById('infiniteScrollSentinel');
    if (!sentinel) {
        sentinel = document.createElement('div');
        sentinel.id = 'infiniteScrollSentinel';
        sentinel.className = 'h-10 w-full';
        
        // Tambahkan sentinel di tempat yang tepat
        if (currentView === 'grid') {
            const container = document.getElementById('productsContainer');
            if (container) {
                const pagination = document.getElementById('paginationContainer');
                if (pagination) {
                    container.parentNode.insertBefore(sentinel, pagination);
                } else {
                    container.appendChild(sentinel);
                }
            }
        } else {
            const container = document.getElementById('listProductsContainer');
            if (container) {
                const pagination = document.getElementById('paginationContainer');
                if (pagination) {
                    container.parentNode.insertBefore(sentinel, pagination);
                } else {
                    container.appendChild(sentinel);
                }
            }
        }
    }
    
    // Setup Intersection Observer
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && hasMorePages) {
                    loadMoreProducts();
                }
            });
        },
        {
            rootMargin: '100px',
            threshold: 0.1
        }
    );
    
    if (sentinel) observer.observe(sentinel);
}

// Load more products for infinite scroll
async function loadMoreProducts() {
    if (isLoading || !hasMorePages) return;
    
    isLoading = true;
    currentPage++;
    
    // Show loading indicator
    const loadingIndicator = document.getElementById('loadingIndicator');
    const endOfContent = document.getElementById('endOfContent');
    loadingIndicator.classList.remove('hidden');
    
    try {
        // Build URL with current query parameters
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('page', currentPage);
        
        // Fetch next page
        const response = await fetch(currentUrl.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract products based on current view
        if (currentView === 'grid') {
            const newProductsContainer = doc.querySelector('#productsContainer');
            const container = document.getElementById('productsContainer');
            if (newProductsContainer && container) {
                // Convert HTML string to DOM elements
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = newProductsContainer.innerHTML;
                
                // Append each new product
                while (tempDiv.firstChild) {
                    container.appendChild(tempDiv.firstChild);
                }
            }
        } else {
            const newProductsContainer = doc.querySelector('#listProductsContainer');
            const container = document.getElementById('listProductsContainer');
            if (newProductsContainer && container) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = newProductsContainer.innerHTML;
                
                while (tempDiv.firstChild) {
                    container.appendChild(tempDiv.firstChild);
                }
            }
        }
        
        // Check if there are more pages
        const newPagination = doc.querySelector('#paginationContainer');
        if (newPagination) {
            const nextPageLink = newPagination.querySelector('a[rel="next"]');
            if (!nextPageLink) {
                hasMorePages = false;
                endOfContent.classList.remove('hidden');
                // Remove sentinel
                const sentinel = document.getElementById('infiniteScrollSentinel');
                if (sentinel) sentinel.remove();
            } else {
                // Update pagination container
                document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
            }
        }
        
        // Initialize AOS for new elements
        if (typeof AOS !== 'undefined') {
            AOS.refreshHard();
        }
        
        // Re-initialize cart buttons for new products
        initCartButtons();
        
        // Update sentinel position
        updateSentinelPosition();
        
    } catch (error) {
        console.error('Error loading more products:', error);
        hasMorePages = false;
        loadingIndicator.innerHTML = '<p class="text-red-500">Gagal memuat lebih banyak produk</p>';
    } finally {
        isLoading = false;
        loadingIndicator.classList.add('hidden');
    }
}

// Initialize cart buttons
function initCartButtons() {
    document.querySelectorAll('[onclick^="addToCart"]').forEach(button => {
        const onclick = button.getAttribute('onclick');
        const match = onclick.match(/addToCart\((\d+),\s*'(\w+)'\)/);
        if (match) {
            button.onclick = () => addToCart(match[1], match[2]);
        }
    });
}

// Update sentinel position
function updateSentinelPosition() {
    const sentinel = document.getElementById('infiniteScrollSentinel');
    if (sentinel) {
        observer.unobserve(sentinel);
        sentinel.remove();
    }
    
    // Create new sentinel
    const newSentinel = document.createElement('div');
    newSentinel.id = 'infiniteScrollSentinel';
    newSentinel.className = 'h-10 w-full';
    
    if (currentView === 'grid') {
        const container = document.getElementById('productsContainer');
        if (container) container.appendChild(newSentinel);
    } else {
        const container = document.getElementById('listProductsContainer');
        if (container) container.appendChild(newSentinel);
    }
    
    observer.observe(newSentinel);
}

// Initialize page on load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial view from localStorage
    const savedView = localStorage.getItem('productView') || 'grid';
    switchView(savedView);
    
    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
            easing: 'ease-out-cubic'
        });
    }
    
    // Initialize cart buttons
    initCartButtons();
    
    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    
    // Initialize infinite scroll after a short delay
    setTimeout(() => initInfiniteScroll(), 500);
});

// Handle browser back/forward navigation
window.addEventListener('popstate', function() {
    // Reload page for proper state
    window.location.reload();
});
</script>
@endpush