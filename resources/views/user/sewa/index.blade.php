@extends('user.layouts.app')

@section('title', 'Sewa Alat Olahraga - SportWear')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="py-12 bg-gradient-to-r from-cyan-500 to-blue-600 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                <!-- Hero Content -->
                <div class="lg:w-2/3" data-aos="fade-right">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-6">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                        <span class="text-white font-semibold text-sm">Sewa & Hemat</span>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                        Sewa Alat Olahraga
                        <span class="block text-cyan-100 mt-2">Berkualitas Premium</span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-xl text-white/90 mb-8 max-w-2xl">
                        Nikmati alat olahraga terbaik tanpa harus membeli. Fleksibel, praktis, dan lebih hemat dengan kualitas yang terjamin!
                    </p>
                    
                    <!-- Quick Stats -->
                    <div class="flex flex-wrap gap-6 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dumbbell text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-white text-2xl font-bold">200+</p>
                                <p class="text-white/80 text-sm">Alat Tersedia</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-white text-2xl font-bold">24/7</p>
                                <p class="text-white/80 text-sm">Ketersediaan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-star text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-white text-2xl font-bold">4.9</p>
                                <p class="text-white/80 text-sm">Rating</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Illustration -->
                <div class="lg:w-1/3" data-aos="fade-left">
                    <div class="relative">
                        <div class="w-64 h-64 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-8xl opacity-50"></i>
                        </div>
                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -left-4 w-20 h-20 bg-yellow-400 rounded-full opacity-20 blur-xl"></div>
                        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-cyan-400 rounded-full opacity-20 blur-xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Mengapa Sewa di <span class="text-primary">SportWear?</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Solusi cerdas untuk kebutuhan alat olahraga Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="100">
                @foreach([
                    ['icon' => 'fas fa-wallet', 'title' => 'Hemat Biaya', 'desc' => 'Bayar sesuai durasi tanpa biaya perawatan', 'color' => 'emerald'],
                    ['icon' => 'fas fa-bolt', 'title' => 'Proses Cepat', 'desc' => 'Booking online, ambil langsung di toko', 'color' => 'amber'],
                    ['icon' => 'fas fa-shield-alt', 'title' => 'Terjamin', 'desc' => 'Alat berkualitas dengan garansi service', 'color' => 'blue'],
                    ['icon' => 'fas fa-sync-alt', 'title' => 'Fleksibel', 'desc' => 'Pilihan durasi harian hingga bulanan', 'color' => 'purple']
                ] as $benefit)
                <div class="group">
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-{{ $benefit['color'] }}-300 hover:shadow-lg transition-all duration-300 h-full">
                        <div class="w-16 h-16 rounded-xl bg-{{ $benefit['color'] }}-100 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="{{ $benefit['icon'] }} text-{{ $benefit['color'] }}-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $benefit['title'] }}</h3>
                        <p class="text-gray-600">{{ $benefit['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto" data-aos="fade-up">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       id="searchInput"
                                       class="pl-10 w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                       placeholder="Cari alat olahraga untuk disewa...">
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <select id="categoryFilter" 
                                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Quick Filters -->
                    <div class="flex flex-wrap gap-2 mt-4">
                        <button class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                            Semua
                        </button>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                            Populer
                        </button>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                            Harga Terendah
                        </button>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                            Stok Tersedia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8" data-aos="fade-up">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Alat Tersedia untuk Disewa</h2>
                    <p class="text-gray-600">Pilih dari koleksi alat olahraga premium kami</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <p class="text-sm text-gray-500">
                        <span class="font-semibold text-primary">{{ $produks->count() }}</span> alat tersedia
                    </p>
                </div>
            </div>
            
            <!-- Products -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
                @forelse($produks as $produk)
                <div class="group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                        <!-- Image Container -->
                        <div class="relative overflow-hidden" style="height: 200px;">
                            <img src="{{ $produk->gambar_url }}" 
                                 alt="{{ $produk->nama }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full">
                                    <i class="fas fa-calendar-alt mr-1"></i> Sewa
                                </span>
                            </div>
                            
                            <!-- Stock Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 {{ $produk->stok_tersedia > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                    <i class="fas fa-box mr-1"></i>
                                    {{ $produk->stok_tersedia > 0 ? $produk->stok_tersedia . ' tersedia' : 'Habis' }}
                                </span>
                            </div>
                            
                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-5 flex-1 flex flex-col">
                            <!-- Category -->
                            <div class="mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-tag mr-1.5 text-xs"></i>
                                    {{ $produk->kategori->nama }}
                                </span>
                            </div>
                            
                            <!-- Product Name -->
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">{{ $produk->nama }}</h3>
                            
                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 flex-1">{{ Str::limit($produk->deskripsi, 80) }}</p>
                            
                            <!-- Rental Prices -->
                            <div class="mb-6 bg-gray-50 rounded-xl p-4">
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div>
                                        <div class="font-bold text-emerald-600 text-lg">
                                            Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">Harian</div>
                                    </div>
                                    <div class="border-x border-gray-200">
                                        <div class="font-bold text-emerald-600 text-lg">
                                            Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">Mingguan</div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-emerald-600 text-lg">
                                            Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">Bulanan</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <a href="{{ route('user.produk.show', $produk->slug) }}" 
                                   class="flex-1 px-4 py-2.5 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Detail</span>
                                </a>
                                
                                @if($produk->stok_tersedia > 0)
                                <button onclick="showRentalModal({{ $produk->id }})"
                                        class="flex-1 px-4 py-2.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-cart-plus"></i>
                                    <span>Sewa</span>
                                </button>
                                @else
                                <button disabled
                                        class="flex-1 px-4 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-times"></i>
                                    <span>Habis</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Empty State -->
                <div class="col-span-full py-16 text-center" data-aos="fade-up">
                    <div class="w-32 h-32 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-search text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Tidak Ada Alat Tersedia</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Saat ini tidak ada alat olahraga yang tersedia untuk disewa. Silakan cek kembali nanti atau gunakan kata kunci pencarian yang berbeda.
                    </p>
                    <button onclick="clearFilters()" 
                            class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i>
                        Reset Pencarian
                    </button>
                </div>
                @endforelse
            </div>
            
            <!-- Load More -->
            @if($produks->hasMorePages())
            <div class="text-center mt-12" data-aos="fade-up">
                <button id="loadMoreBtn" 
                        class="px-8 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Muat Lebih Banyak</span>
                </button>
            </div>
            @endif
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Cara Menyewa <span class="text-primary">Mudah</span> di SportWear
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Hanya 3 langkah sederhana untuk mendapatkan alat olahraga premium
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
                @foreach([
                    ['number' => '01', 'title' => 'Pilih Alat', 'desc' => 'Cari dan pilih alat olahraga yang ingin disewa dari katalog kami', 'icon' => 'fas fa-search'],
                    ['number' => '02', 'title' => 'Tentukan Durasi', 'desc' => 'Pilih durasi sewa dan tanggal mulai sesuai kebutuhan Anda', 'icon' => 'fas fa-calendar-check'],
                    ['number' => '03', 'title' => 'Bayar & Ambil', 'desc' => 'Selesaikan pembayaran dan ambil alat di toko kami', 'icon' => 'fas fa-shopping-bag']
                ] as $step)
                <div class="relative">
                    <div class="bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300 h-full">
                        <!-- Step Number -->
                        <div class="w-16 h-16 rounded-xl bg-primary/10 flex items-center justify-center mb-6">
                            <span class="text-primary font-bold text-xl">{{ $step['number'] }}</span>
                        </div>
                        
                        <!-- Icon -->
                        <div class="w-14 h-14 bg-primary text-white rounded-xl flex items-center justify-center mb-6">
                            <i class="{{ $step['icon'] }} text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $step['title'] }}</h3>
                        <p class="text-gray-600">{{ $step['desc'] }}</p>
                    </div>
                    
                    <!-- Arrow for Desktop -->
                    @if(!$loop->last)
                    <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2">
                        <i class="fas fa-arrow-right text-gray-300 text-2xl"></i>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Pertanyaan yang <span class="text-primary">Sering Ditanyakan</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Temukan jawaban untuk pertanyaan umum seputar penyewaan alat olahraga
                </p>
            </div>
            
            <div class="max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                <div class="space-y-4">
                    @foreach([
                        ['question' => 'Berapa lama maksimal durasi sewa?', 'answer' => 'Maksimal durasi sewa adalah 30 hari. Untuk kebutuhan lebih lama dari itu, silakan hubungi customer service kami untuk penawaran khusus.'],
                        ['question' => 'Bagaimana jika alat rusak selama disewa?', 'answer' => 'Kerusakan ringan akan dikenakan denda 10% dari harga alat. Kerusakan berat 50%, dan jika alat hilang atau rusak total, dikenakan biaya 100% harga alat. Semua alat sudah melalui pemeriksaan sebelum disewa.'],
                        ['question' => 'Bisakah memperpanjang durasi sewa?', 'answer' => 'Ya, Anda bisa memperpanjang durasi sewa dengan menghubungi kami minimal 1 hari sebelum tanggal pengembalian. Perpanjangan tergantung ketersediaan alat.'],
                        ['question' => 'Bagaimana cara pengambilan alat?', 'answer' => 'Setelah booking dan pembayaran, Anda bisa mengambil alat langsung di toko kami dengan menunjukkan bukti booking. Atau gunakan layanan delivery dengan biaya tambahan.'],
                        ['question' => 'Apakah ada deposit?', 'answer' => 'Ya, untuk alat dengan harga di atas Rp 5 juta, kami meminta deposit sebesar 30% dari harga alat yang akan dikembalikan setelah alat dikembalikan dalam kondisi baik.']
                    ] as $faq)
                    <div class="border border-gray-200 rounded-2xl overflow-hidden hover:border-primary transition-colors duration-200">
                        <button class="faq-question w-full p-6 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition-colors duration-200">
                            <span class="font-semibold text-gray-900 text-lg">{{ $faq['question'] }}</span>
                            <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                        </button>
                        <div class="faq-answer overflow-hidden transition-all duration-300 max-h-0">
                            <div class="p-6 pt-0 text-gray-600">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-primary to-primary-dark">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center" data-aos="zoom-in">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Siap Mulai Olahraga?
                </h2>
                <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                    Sewa alat olahraga premium sekarang dan nikmati pengalaman berolahraga yang lebih baik
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="tel:02112345678" 
                       class="px-8 py-3 bg-white text-primary font-bold rounded-xl hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center gap-3">
                        <i class="fas fa-phone"></i>
                        <span>Hubungi Kami</span>
                    </a>
                    <a href="{{ route('user.produk.index') }}" 
                       class="px-8 py-3 border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-primary transition-all duration-200 flex items-center justify-center gap-3">
                        <i class="fas fa-store"></i>
                        <span>Lihat Semua Produk</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Rental Modal -->
<div id="rentalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-4 mx-auto p-4 w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary to-primary-dark rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Form Penyewaan</h3>
                    </div>
                    <button onclick="closeRentalModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <form id="rentalForm">
                    @csrf
                    <input type="hidden" id="product_id" name="product_id">
                    
                    <!-- Product Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                                <img id="modalProductImage" src="" alt="" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 id="modalProductName" class="font-bold text-gray-900 mb-1"></h4>
                                <p id="modalProductCategory" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="font-semibold text-emerald-600" id="modalDailyPrice"></div>
                                <div class="text-xs text-gray-500">Harian</div>
                            </div>
                            <div class="border-x border-gray-200">
                                <div class="font-semibold text-emerald-600" id="modalWeeklyPrice"></div>
                                <div class="text-xs text-gray-500">Mingguan</div>
                            </div>
                            <div>
                                <div class="font-semibold text-emerald-600" id="modalMonthlyPrice"></div>
                                <div class="text-xs text-gray-500">Bulanan</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rental Form -->
                    <div class="space-y-6">
                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Sewa</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan'] as $value => $label)
                                <label class="duration-option relative">
                                    <input type="radio" name="durasi" value="{{ $value }}" class="sr-only" required>
                                    <div class="w-full p-4 border-2 border-gray-200 rounded-xl text-center cursor-pointer hover:border-primary transition-colors duration-200">
                                        <div class="font-semibold text-gray-900">{{ $label }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Duration Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Hari</label>
                                <div class="relative">
                                    <input type="number" 
                                           name="jumlah_hari" 
                                           id="jumlah_hari" 
                                           value="1" 
                                           min="1" 
                                           max="30"
                                           class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 text-sm">hari</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" 
                                       name="tanggal_mulai" 
                                       id="tanggal_mulai"
                                       class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="catatan" 
                                      rows="3" 
                                      class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                      placeholder="Contoh: Butuh alat untuk turnamen tanggal..."></textarea>
                        </div>
                        
                        <!-- Price Summary -->
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Ringkasan Biaya</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Harga per hari:</span>
                                    <span class="font-semibold text-gray-900" id="pricePerDay">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jumlah hari:</span>
                                    <span class="font-semibold text-gray-900" id="daysCount">0 hari</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-bold text-gray-900">Total Biaya:</span>
                                        <span class="text-2xl font-bold text-primary" id="totalPrice">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" 
                                    id="submitRentalBtn"
                                    class="w-full px-6 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center justify-center gap-3">
                                <i class="fas fa-cart-plus"></i>
                                <span>Tambah ke Keranjang</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom animations */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.floating-element {
    animation: float 6s ease-in-out infinite;
}

/* Duration option active state */
.duration-option input:checked + div {
    border-color: #2B6CB0;
    background-color: rgba(43, 108, 176, 0.05);
    box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
}

/* FAQ animations */
.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer.open {
    max-height: 500px;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Gradient borders */
.border-gradient {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                linear-gradient(135deg, #38B2AC, #2B6CB0) border-box;
}
</style>
@endpush

@push('scripts')
<script>
// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    }
    
    // Initialize FAQ functionality
    initFAQ();
});

// FAQ Functionality
function initFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const icon = this.querySelector('i');
            
            // Toggle active class
            this.classList.toggle('active');
            
            // Toggle answer
            if (answer.classList.contains('open')) {
                answer.classList.remove('open');
                answer.style.maxHeight = '0';
            } else {
                answer.classList.add('open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
            
            // Toggle icon
            icon.style.transform = this.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
        });
    });
}

// Clear Filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    
    // Show all products
    document.querySelectorAll('.group').forEach(el => {
        el.style.display = 'block';
    });
}

// Search Functionality
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchProducts();
    }, 500);
});

document.getElementById('categoryFilter').addEventListener('change', searchProducts);

function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryId = document.getElementById('categoryFilter').value;
    
    document.querySelectorAll('.group').forEach(card => {
        const productName = card.querySelector('h3').textContent.toLowerCase();
        const productCategory = card.querySelector('span').textContent;
        const shouldShow = (!searchTerm || productName.includes(searchTerm)) &&
                          (!categoryId || productCategory.includes(categoryId));
        
        card.style.display = shouldShow ? 'block' : 'none';
    });
}

// Load More Products
let currentPage = 1;
document.getElementById('loadMoreBtn')?.addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memuat...';
    button.disabled = true;
    
    try {
        const response = await fetch(`/user/sewa?page=${currentPage + 1}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const html = await response.text();
        
        if (html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newProducts = tempDiv.querySelector('#productsGrid').innerHTML;
            
            document.getElementById('productsGrid').innerHTML += newProducts;
            currentPage++;
            
            // Reinitialize AOS for new elements
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        }
        
        button.innerHTML = originalText;
        button.disabled = false;
    } catch (error) {
        console.error('Error loading more products:', error);
        button.innerHTML = originalText;
        button.disabled = false;
    }
});

// Rental Modal Functions
let currentProduct = null;

async function showRentalModal(productId) {
    currentProduct = productId;
    
    try {
        const response = await fetch(`/api/products/${productId}/rental-info`);
        const product = await response.json();
        
        // Update modal with product info
        document.getElementById('product_id').value = product.id;
        document.getElementById('modalProductImage').src = product.gambar_url;
        document.getElementById('modalProductName').textContent = product.nama;
        document.getElementById('modalProductCategory').textContent = product.kategori.nama;
        document.getElementById('modalDailyPrice').textContent = `Rp ${product.harga_sewa_harian.toLocaleString()}`;
        document.getElementById('modalWeeklyPrice').textContent = `Rp ${product.harga_sewa_mingguan.toLocaleString()}`;
        document.getElementById('modalMonthlyPrice').textContent = `Rp ${product.harga_sewa_bulanan.toLocaleString()}`;
        
        // Set minimum date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('tanggal_mulai').min = tomorrow.toISOString().split('T')[0];
        document.getElementById('tanggal_mulai').value = tomorrow.toISOString().split('T')[0];
        
        // Reset form
        document.getElementById('rentalForm').reset();
        
        // Show modal
        document.getElementById('rentalModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize price calculation
        updatePrice();
    } catch (error) {
        console.error('Error loading product info:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal memuat informasi produk',
            text: 'Silakan coba lagi nanti',
            confirmButtonColor: '#2B6CB0'
        });
    }
}

function closeRentalModal() {
    document.getElementById('rentalModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Price Calculation
function updatePrice() {
    const selectedDuration = document.querySelector('input[name="durasi"]:checked');
    const days = parseInt(document.getElementById('jumlah_hari').value) || 1;
    
    if (!selectedDuration) return;
    
    const duration = selectedDuration.value;
    let pricePerDay = 0;
    let totalPrice = 0;
    
    // Get prices from modal display (in real app, this would come from API)
    const dailyPriceText = document.getElementById('modalDailyPrice').textContent;
    const weeklyPriceText = document.getElementById('modalWeeklyPrice').textContent;
    const monthlyPriceText = document.getElementById('modalMonthlyPrice').textContent;
    
    // Extract numeric values (remove "Rp " and commas)
    const dailyPrice = parseInt(dailyPriceText.replace('Rp ', '').replace(/\./g, '')) || 0;
    const weeklyPrice = parseInt(weeklyPriceText.replace('Rp ', '').replace(/\./g, '')) || 0;
    const monthlyPrice = parseInt(monthlyPriceText.replace('Rp ', '').replace(/\./g, '')) || 0;
    
    switch(duration) {
        case 'harian':
            pricePerDay = dailyPrice;
            totalPrice = dailyPrice * days;
            break;
        case 'mingguan':
            pricePerDay = Math.round(weeklyPrice / 7);
            totalPrice = weeklyPrice * Math.ceil(days / 7);
            break;
        case 'bulanan':
            pricePerDay = Math.round(monthlyPrice / 30);
            totalPrice = monthlyPrice * Math.ceil(days / 30);
            break;
    }
    
    // Update display
    document.getElementById('pricePerDay').textContent = `Rp ${pricePerDay.toLocaleString()}`;
    document.getElementById('daysCount').textContent = `${days} hari`;
    document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString()}`;
}

// Event listeners for price updates
document.querySelectorAll('input[name="durasi"]').forEach(radio => {
    radio.addEventListener('change', updatePrice);
});

document.getElementById('jumlah_hari').addEventListener('input', updatePrice);

// Rental Form Submission
document.getElementById('rentalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitRentalBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = `
        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Memproses...</span>
    `;
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/user/keranjang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: formData.get('product_id'),
                type: 'sewa',
                quantity: 1,
                options: {
                    durasi: formData.get('durasi'),
                    jumlah_hari: formData.get('jumlah_hari'),
                    tanggal_mulai: formData.get('tanggal_mulai'),
                    catatan: formData.get('catatan')
                }
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            closeRentalModal();
            
            // Update cart badge
            window.dispatchEvent(new CustomEvent('cartUpdated', {
                detail: { count: data.cart_count }
            }));
            
            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: error.message || 'Terjadi kesalahan saat menambahkan ke keranjang',
            confirmButtonColor: '#2B6CB0'
        });
    } finally {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRentalModal();
    }
});

// Close modal on background click
document.getElementById('rentalModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRentalModal();
    }
});
</script>
@endpush