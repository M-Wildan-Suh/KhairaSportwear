@extends('user.layouts.app')

@section('title', 'SportWear - Platform Alat Olahraga Premium')

@section('content')
    <!-- Hero Section - Elegant Design -->
    <section
        class="relative min-h-[calc(100vh-74px)] flex items-center overflow-hidden bg-gradient-to-br from-white to-blue-50">
        <img src="{{ asset('assets/bg-texture.jpg') }}"
            class=" absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-80">
        <div class="relative z-10 container mx-auto px-4 md:px-8 py-8 lg:py-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-12 lg:gap-20">
                <!-- Left Content -->
                <div class=" pt-8 xl:pt-0 text-center lg:text-left" data-aos="fade-right">

                    <!-- Main Headline -->
                    <h1 class="text-3xl lg:text-5xl font-bold leading-tight mb-6">
                        Lengkapi Perlengkapan Olahragamu
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Tersedia berbagai macam perlengkapan olahraga untuk latihan, pertandingan, atau event. Bisa beli
                        atau sewa
                        sesuai kebutuhan.
                    </p>
                    <div class="  w-full grid grid-cols-1 lg:grid-cols-2 gap-4 pt-8 border-t border-gray-400">
                        <a href="{{ route('produk.index') }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-300 text-white font-semibold py-4 px-8 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-primary/20 flex items-center justify-center gap-3">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Lihat Produk</span>
                            <i
                                class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform duration-300"></i>
                        </a>

                        <a href="{{ route('sewa.index') }}"
                            class="group bg-white border-2 border-primary text-primary font-semibold py-4 px-8 rounded-xl transition-all duration-300 hover:bg-primary hover:text-black hover:shadow-lg flex items-center justify-center gap-3">
                            <i class="fas fa-calendar-check"></i>
                            <span>Sewa Perlengkapan</span>
                            <i class="fas fa-external-link-alt transform group-hover:rotate-12 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="" data-aos="fade-left" data-aos-delay="300">
                    <div class="relative">
                        <!-- Main Image Card -->
                        <div
                            class="relative rounded-2xl aspect-square xl:aspect-auto xl:max-h-[500px] overflow-hidden shadow-xl border border-gray-200">
                            <img src="{{asset('assets/banner.jpeg')}}"
                                alt="Premium Sports Equipment" class=" w-full h-full object-cover object-top">

                            <!-- Stats Grid -->
                            <div style="box-shadow: inset 0px -100px 100px -60px #000000;"
                                class=" w-full absolute bottom-0 p-4 grid grid-cols-3 gap-6">
                                @foreach ([['value' => '500+', 'label' => 'Perlengkapan', 'icon' => 'shapes', 'color' => 'text-primary'], ['value' => '98%', 'label' => 'Kepuasan', 'icon' => 'smile', 'color' => 'text-green-500'], ['value' => '24/7', 'label' => 'Layanan', 'icon' => 'headset', 'color' => 'text-accent']] as $stat)
                                    <div class="text-center" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center mb-3 shadow-sm">
                                                <i class="fas fa-{{ $stat['icon'] }} {{ $stat['color'] }} text-lg"></i>
                                            </div>
                                            <h3 class="text-gray-100 font-bold text-2xl mb-1">{{ $stat['value'] }}</h3>
                                            <p class="text-gray-300 text-sm">{{ $stat['label'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ForSale Products -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8 relative">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12" data-aos="fade-up">
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Produk <span class="text-primary">Jual</span>
                    </h2>
                    <p class="text-gray-600 text-lg">Pilihan produk yang bisa dibeli</p>
                </div>

                <a href="{{ route('produk.index') }}"
                    class="mt-6 lg:mt-0 flex items-center gap-3 text-primary font-semibold hover:text-primary-dark transition-colors">
                    <span>Lihat Produk Lainnya</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($productsForSale as $forsale)
                    <div class="group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                            <!-- Image Container -->
                            <div class="relative overflow-hidden" style="height: 200px;">
                                <img src="{{ $forsale->gambar_url }}" alt="{{ $forsale->nama }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                                <!-- Badges -->
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full">
                                        <i class="fas fa-calendar-alt mr-1"></i> Jual
                                    </span>
                                </div>

                                <!-- Stock Badge -->
                                <div class="absolute top-4 right-4">
                                    <span
                                        class="px-3 py-1 {{ $forsale->stok_tersedia > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $forsale->stok_tersedia > 0 ? $forsale->stok_tersedia . ' tersedia' : 'Habis' }}
                                    </span>
                                </div>

                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent">
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex-1 flex flex-col">
                                <!-- Category -->
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-tag mr-1.5 text-xs"></i>
                                        {{ $forsale->kategori->nama }}
                                    </span>
                                </div>

                                <!-- Product Name -->
                                <h3 class="font-bold text-gray-900 mb-2 text-lg">{{ $forsale->nama }}</h3>

                                <!-- Description -->
                                <p class="text-gray-600 text-sm mb-4 flex-1">{{ Str::limit($forsale->deskripsi, 80) }}</p>

                                <!-- Sale Prices -->
                                <div class="mb-6 bg-gray-50 rounded-xl p-4">
                                    <div class="font-bold text-emerald-600 text-lg">
                                        Rp {{ number_format($forsale->harga_beli, 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <a href="{{ route('produk.show', $forsale->slug) }}"
                                        class="flex-1 px-4 py-2.5 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Detail</span>
                                    </a>

                                    @if ($forsale->stok_tersedia > 0)
                                        <button onclick="addToCart({{ $forsale->id }}, 'jual')" 
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
                @endforeach
            </div>
        </div>
    </section>

    <!-- ForRent Products -->
    <section class="py-20 relative bg-gradient-to-br from-white to-blue-50">
        <img src="{{ asset('assets/bg-texture.jpg') }}"
            class=" absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-80">
        <div class="container mx-auto px-4 lg:px-8 relative">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12" data-aos="fade-up">
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Produk <span class="text-primary">Sewa</span>
                    </h2>
                    <p class="text-gray-600 text-lg">Pilihan produk yang bisa disewa</p>
                </div>

                <a href="{{ route('produk.index') }}"
                    class="mt-6 lg:mt-0 flex items-center gap-3 text-primary font-semibold hover:text-primary-dark transition-colors">
                    <span>Lihat Produk Lainnya</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($productsForRent as $produk)
                    <div class="group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                            <!-- Image Container -->
                            <div class="relative overflow-hidden" style="height: 200px;">
                                <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                                <!-- Badges -->
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full">
                                        <i class="fas fa-calendar-alt mr-1"></i> Sewa
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
                                <p class="text-gray-600 text-sm mb-4 flex-1">{{ Str::limit($produk->deskripsi, 80) }}</p>

                                <!-- Rent Prices -->
                                <div class="mb-6 bg-gray-50 rounded-xl py-4">
                                    <div class="grid grid-cols-1 gap-2 text-center">
                                        <div class=" flex justify-between gap-1 items-center">
                                            <div class="font-bold text-emerald-600 text-lg">
                                                Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">/Hari</div>
                                        </div>
                                        <div class=" flex justify-between gap-1 items-center">
                                            <div class="font-bold text-emerald-600 text-lg">
                                                Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">/Minggu</div>
                                        </div>
                                        <div class=" flex justify-between gap-1 items-center">
                                            <div class="font-bold text-emerald-600 text-lg">
                                                Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">/Bulan</div>
                                        </div>
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
                                        <button onclick="showSewaModal({{ $produk->id }})"
                                            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-800 text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center justify-center gap-2">
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
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8 relative">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Cara Pesan <span class="text-primary">Produk</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Proses sederhana untuk mendapatkan perlengkapan olahraga yang kamu butuhkan
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ([['step' => '01', 'title' => 'Cari Perlengkapan', 'desc' => 'Lihat berbagai pilihan perlengkapan olahraga yang tersedia', 'icon' => 'search'], ['step' => '02', 'title' => 'Pilih Produk', 'desc' => 'Tentukan apakah ingin sewa atau beli sesuai kebutuhan', 'icon' => 'check-circle'], ['step' => '03', 'title' => 'Selesaikan Pemesanan', 'desc' => 'Proses pemesanan mudah dan aman', 'icon' => 'credit-card'], ['step' => '04', 'title' => 'Terima Perlengkapan', 'desc' => 'Perlengkapan dikirim langsung ke lokasi kamu', 'icon' => 'shipping-fast']] as $step)
                    <div class="relative bg-white rounded-xl p-4 border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                        <!-- Step Number -->
                        <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="text-primary font-bold text-xl">{{ $step['step'] }}</span>
                        </div>

                        <!-- Content -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $step['icon'] }} text-primary text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg mb-2">{{ $step['title'] }}</h3>
                                <p class="text-gray-600">{{ $step['desc'] }}</p>
                            </div>
                        </div>

                        <!-- Arrow for Desktop -->
                        @if (!$loop->last)
                            <div class="hidden lg:block absolute top-1/2 left-full ml-2 transform -translate-y-1/2">
                                <i class="fas fa-arrow-right text-gray-300 text-xl"></i>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('user.components.sewa-modal')

@endsection

@vite('resources/js/sewa-modal.js')

@push('styles')
    <style>
        :root {
            --primary: #1A365D;
            --primary-dark: #153255;
            --primary-light: rgba(26, 54, 93, 0.1);
            --accent: #D69E2E;
        }

        .bg-primary {
            background-color: var(--primary);
        }

        .bg-primary-dark {
            background-color: var(--primary-dark);
        }

        .bg-primary-light {
            background-color: var(--primary-light);
        }

        .text-primary {
            color: var(--primary);
        }

        .text-primary-dark {
            color: var(--primary-dark);
        }

        .text-accent {
            color: var(--accent);
        }

        /* Elegant animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Card hover effects */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Elegant borders */
        .border-elegant {
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Smooth scroll animation
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                    easing: 'ease-out-cubic'
                });
            }

            // Counter animation
            function animateCounter(element, target, duration) {
                let start = 0;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, 16);
            }

            // Intersection Observer for counters
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px 0px -100px 0px'
            };

            const counters = document.querySelectorAll('.counter');
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.textContent);
                        animateCounter(entry.target, target, 2000);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            counters.forEach(counter => counterObserver.observe(counter));

            // Add hover effects to cards
            const cards = document.querySelectorAll('.hover-lift');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.classList.add('hover:shadow-lg');
                });

                card.addEventListener('mouseleave', () => {
                    card.classList.remove('hover:shadow-lg');
                });
            });
        });
    </script>
@endpush

<script>
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
</script>
