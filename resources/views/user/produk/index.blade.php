@extends('user.layouts.app')

@section('title', 'Sewa Alat Olahraga - SportWear')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <section class="py-12 bg-gradient-to-r from-yellow-400 relative overf to-amber-600 overlow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 40px 40px;">
                </div>
            </div>

            <div class="container mx-auto px-4 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                    <!-- Hero Content -->
                    <div class="lg:w-2/3" data-aos="fade-right">
                        <!-- Badge -->
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-6">
                            <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                            <span class="text-white font-semibold text-sm">Beli & Hemat</span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                            Beli Alat Olahraga
                            <span class="block text-yellow-100 mt-2">Berkualitas Premium</span>
                        </h1>

                        <!-- Description -->
                        <p class="text-xl text-white/90 mb-8 max-w-2xl">
                            Miliki alat olahraga sendiri di rumah. Fleksibel, praktis, dan lebih hemat dengan kualitas yang
                            terjamin!
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
                            <div
                                class="w-64 h-64 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 flex items-center justify-center">
                                <i class="fas fa-store text-white text-8xl opacity-50"></i>
                            </div>
                            <!-- Floating Elements -->
                            <div class="absolute -top-4 -left-4 w-20 h-20 bg-yellow-400 rounded-full opacity-20 blur-xl">
                            </div>
                            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-cyan-400 rounded-full opacity-20 blur-xl">
                            </div>
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
                        Mengapa beli di <span class="text-primary">SportWear?</span>
                    </h2>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Solusi cerdas untuk kebutuhan alat olahraga Anda
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="100">
                    @foreach ([['icon' => 'fas fa-wallet', 'title' => 'Hemat Biaya', 'desc' => 'Bayar sesuai durasi tanpa biaya perawatan', 'color' => 'emerald'], ['icon' => 'fas fa-bolt', 'title' => 'Proses Cepat', 'desc' => 'Booking online, ambil langsung di toko', 'color' => 'amber'], ['icon' => 'fas fa-shield-alt', 'title' => 'Terjamin', 'desc' => 'Alat berkualitas dengan garansi service', 'color' => 'blue'], ['icon' => 'fas fa-sync-alt', 'title' => 'Fleksibel', 'desc' => 'Berbagai macam metode pembayaran', 'color' => 'purple']] as $benefit)
                        <div class="group">
                            <div
                                class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-{{ $benefit['color'] }}-300 hover:shadow-lg transition-all duration-300 h-full">
                                <div
                                    class="w-16 h-16 rounded-xl bg-{{ $benefit['color'] }}-100 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
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
                                    <input type="text" id="searchInput"
                                        class="pl-10 w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                        placeholder="Cari alat olahraga untuk dibeli...">
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <select id="categoryFilter"
                                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Quick Filters -->
                        <div class="flex flex-wrap gap-2 mt-4">
                            <button
                                class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                                Semua
                            </button>
                            <button
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                Populer
                            </button>
                            <button
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                Harga Terendah
                            </button>
                            <button
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
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
                            <div
                                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                                <!-- Image Container -->
                                <div class="relative overflow-hidden" style="height: 200px;">
                                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                                    <!-- Badges -->
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 bg-[#1A365D] text-white text-xs font-semibold rounded-full">
                                            <i class="fas fa-calendar-alt mr-1"></i> Jual
                                        </span>
                                    </div>

                                    <!-- Stock Badge -->
                                    <div class="absolute top-4 right-4">
                                        <span
                                            class="px-3 py-1 {{ $produk->stok_tersedia > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                            <i class="fas fa-box mr-1"></i>
                                            {{ $produk->stok_tersedia > 0 ? $produk->stok_tersedia . ' tersedia' : 'Habis' }}
                                        </span>
                                    </div>

                                    <!-- Overlay Gradient -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent">
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-5 flex-1 flex flex-col">
                                    <!-- Category -->
                                    <div class="mb-3">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-tag mr-1.5 text-xs"></i>
                                            {{ $produk->kategori->nama }}
                                        </span>
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="font-bold text-gray-900 mb-2 text-lg">{{ $produk->nama }}</h3>

                                    <!-- Description -->
                                    <p class="text-gray-600 text-sm mb-4 flex-1">{{ Str::limit($produk->deskripsi, 80) }}
                                    </p>

                                    <!-- Sale Prices -->
                                    <div class="mb-6 bg-gray-50 rounded-xl p-4">
                                        <div class="font-bold text-emerald-600 text-lg">
                                            Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <a href="{{ route('produk.show', $produk->slug) }}"
                                            class="flex-1 px-4 py-2.5 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Detail</span>
                                        </a>

                                        @if ($produk->stok_tersedia > 0)
                                            <button onclick="addToCart({{ $produk->id }}, 'jual')"
                                                class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-800 text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center justify-center gap-2">
                                                <i class="fas fa-cart-plus"></i>
                                                <span>Keranjang</span>
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
                                Saat ini tidak ada alat olahraga yang tersedia untuk disewa. Silakan cek kembali nanti atau
                                gunakan kata kunci pencarian yang berbeda.
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
                @if ($produks->hasMorePages())
                    <div class="text-center mt-12" data-aos="fade-up">
                        <button id="loadMoreBtn"
                            class="px-8 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
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
                        Cara Membeli <span class="text-primary">Mudah</span> di SportWear
                    </h2>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Hanya 3 langkah sederhana untuk mendapatkan alat olahraga premium
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
                    @foreach ([['number' => '01', 'title' => 'Pilih Alat', 'desc' => 'Cari dan pilih alat olahraga yang ingin disewa dari katalog kami', 'icon' => 'fas fa-search'], ['number' => '02', 'title' => 'Selesaikan Pesanan', 'desc' => 'Proses pemesanan mudah dan aman', 'icon' => 'fas fa-calendar-check'], ['number' => '03', 'title' => 'Bayar & Ambil', 'desc' => 'Selesaikan pembayaran dan ambil alat di toko kami', 'icon' => 'fas fa-shopping-bag']] as $step)
                        <div class="relative">
                            <div
                                class="bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300 h-full">
                                <!-- Step Number -->
                                <div class="w-16 h-16 rounded-xl bg-primary/10 flex items-center justify-center mb-6">
                                    <span class="text-primary font-bold text-xl">{{ $step['number'] }}</span>
                                </div>

                                <!-- Icon -->
                                <div
                                    class="w-14 h-14 bg-primary text-white rounded-xl flex items-center justify-center mb-6">
                                    <i class="{{ $step['icon'] }} text-lg"></i>
                                </div>

                                <!-- Content -->
                                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $step['title'] }}</h3>
                                <p class="text-gray-600">{{ $step['desc'] }}</p>
                            </div>

                            <!-- Arrow for Desktop -->
                            @if (!$loop->last)
                                <div class="hidden md:block absolute top-1/2 left-full ml-1.5 transform -translate-y-1/2">
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
                        @foreach ([
            ['question' => 'Berapa lama maksimal durasi sewa?', 'answer' => 'Maksimal durasi sewa adalah 30 hari. Untuk kebutuhan lebih lama dari itu, silakan hubungi customer service kami untuk penawaran khusus.'],
            ['question' => 'Bagaimana jika alat rusak selama disewa?', 'answer' => 'Kerusakan ringan akan dikenakan denda 10% dari harga alat. Kerusakan berat 50%, dan jika alat hilang atau rusak total, dikenakan biaya 100% harga alat. Semua alat sudah melalui pemeriksaan sebelum disewa.'],
            ['question' => 'Bisakah memperpanjang durasi sewa?', 'answer' => 'Ya, Anda bisa memperpanjang durasi sewa dengan menghubungi kami minimal 1 hari sebelum tanggal pengembalian. Perpanjangan tergantung ketersediaan alat.'],
            ['question' => 'Bagaimana cara pengambilan alat?', 'answer' => 'Setelah booking dan pembayaran, Anda bisa mengambil alat langsung di toko kami dengan menunjukkan bukti booking. Atau gunakan layanan delivery dengan biaya tambahan.'],
            ['question' => 'Apakah ada deposit?', 'answer' => 'Ya, untuk alat dengan harga di atas Rp 5 juta, kami meminta deposit sebesar 30% dari harga alat yang akan dikembalikan setelah alat dikembalikan dalam kondisi baik.'],
        ] as $faq)
                            <div
                                class="border border-gray-200 rounded-2xl overflow-hidden hover:border-primary transition-colors duration-200">
                                <button
                                    class="faq-question w-full p-6 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition-colors duration-200">
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
    </div>

    <!-- Include Modal Component -->
    @include('user.components.sewa-modal')
@endsection

@push('styles')
    <style>
        /* Custom animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .floating-element {
            animation: float 6s ease-in-out infinite;
        }

        /* Duration option active state */
        .duration-option input:checked+div {
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
    <!-- Include Modal JavaScript -->
    @vite('resources/js/sewa-modal.js')

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

            // Initialize search functionality
            initSearch();
        });

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
                            detail: {
                                count: result.value.cart_count
                            }
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
                    icon.style.transform = this.classList.contains('active') ? 'rotate(180deg)' :
                        'rotate(0)';
                });
            });
        }

        // Search Functionality
        let searchTimeout;

        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchProducts();
                    }, 500);
                });
            }

            if (categoryFilter) {
                categoryFilter.addEventListener('change', searchProducts);
            }
        }

        function searchProducts() {
            const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
            const categoryId = document.getElementById('categoryFilter')?.value || '';

            document.querySelectorAll('.group').forEach(card => {
                const productName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const productCategory = card.dataset.category || '';

                const shouldShow =
                    (!searchTerm || productName.includes(searchTerm)) &&
                    (!categoryId || productCategory === categoryId);

                card.style.display = shouldShow ? 'block' : 'none';
            });
        }

        // Clear Filters
        function clearFilters() {
            if (document.getElementById('searchInput')) {
                document.getElementById('searchInput').value = '';
            }
            if (document.getElementById('categoryFilter')) {
                document.getElementById('categoryFilter').value = '';
            }

            // Show all products
            document.querySelectorAll('.group').forEach(el => {
                el.style.display = 'block';
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
                const response = await fetch(`/user/produk?page=${currentPage + 1}`, {
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

        // Make sure showSewaModal is available globally
        // (Already exported from sewa-modal.js, but just in case)
        window.showSewaModal = window.showSewaModal || function(productId) {
            console.warn('showSewaModal not loaded from sewa-modal.js');
            alert('Fungsi sewa belum siap. Silakan refresh halaman.');
        };

        // Quick filter buttons functionality (optional enhancement)
        document.querySelectorAll('.bg-gray-100').forEach(button => {
            button.addEventListener('click', function() {
                const text = this.textContent.trim();

                // Reset all buttons
                document.querySelectorAll('.bg-gray-100, .bg-primary').forEach(btn => {
                    btn.classList.remove('bg-primary', 'text-white');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });

                // Activate clicked button
                this.classList.remove('bg-gray-100', 'text-gray-700');
                this.classList.add('bg-primary', 'text-white');

                // Implement filter logic based on text
                switch (text) {
                    case 'Populer':
                        // Add your popular filter logic
                        break;
                    case 'Harga Terendah':
                        // Add your price sort logic
                        break;
                    case 'Stok Tersedia':
                        // Filter by stock
                        document.querySelectorAll('.group').forEach(card => {
                            const stockBadge = card.querySelector('.bg-red-100');
                            card.style.display = stockBadge ? 'none' : 'block';
                        });
                        break;
                }
            });
        });
    </script>
@endpush
