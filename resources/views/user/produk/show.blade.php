<!-- resources/views/user/produk/show.blade.php -->
@extends('user.layouts.app')

@section('title', $produk->nama . ' - SportWear')

@section('content')
    <div class="py-8">
        <!-- Breadcrumb -->
        <div class="container mx-auto px-4 md:px-8 mb-6">
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
                            <a href="{{ route('produk.index') }}" class="ml-2 text-gray-600 hover:text-primary">Produk</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400"></i>
                            <a href="{{ route('produk.kategori', $produk->kategori->slug) }}"
                                class="ml-2 text-gray-600 hover:text-primary">{{ $produk->kategori->nama }}</a>
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
        <div class="container mx-auto px-4 md:px-8">
            <div class="grid lg:grid-cols-2 gap-8 mb-8">
                <!-- Product Images -->
                <div data-aos="fade-right">
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <!-- Main Image -->
                        <div class="relative overflow-hidden">
                            <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}" class="w-full h-96 object-cover"
                                id="mainProductImage">
                            <div class="absolute top-4 left-4">
                                @if ($produk->tipe === 'jual')
                                    <span
                                        class="px-3 py-1 bg-primary text-white text-sm font-semibold rounded-full">Dijual</span>
                                @elseif($produk->tipe === 'sewa')
                                    <span
                                        class="px-3 py-1 bg-accent text-white text-sm font-semibold rounded-full">Disewa</span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-primary to-accent text-white text-sm font-semibold rounded-full">Dijual/Disewa</span>
                                @endif
                            </div>
                        </div>

                        <!-- Image Thumbnails -->
                        <div class="p-4">
                            <div class="grid grid-cols-4 gap-2">
                                <div class="thumbnail-item active" onclick="changeMainImage('{{ $produk->gambar_url }}')">
                                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}"
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
                                @if ($produk->stok_tersedia > 0)
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                        <i class="fas fa-check-circle mr-2"></i> {{ $produk->stok_tersedia }} tersedia
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
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
                                    @for ($i = 1; $i <= 5; $i++)
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
                            @if ($produk->tipe === 'jual' || $produk->tipe === 'both')
                                <div>
                                    <h3 class="text-2xl font-bold text-primary">Rp
                                        {{ number_format($produk->harga_beli, 0, ',', '.') }}</h3>
                                    <p class="text-gray-600 text-sm">Harga beli</p>
                                </div>
                            @endif

                            @if ($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Harga Sewa:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @if ($produk->harga_sewa_harian)
                                            <div
                                                class="price-option border border-gray-200 rounded-lg p-4 text-center hover:border-primary hover:shadow-sm cursor-pointer">
                                                <div class="text-lg font-bold text-primary mb-1">Rp
                                                    {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}</div>
                                                <p class="text-gray-600 text-sm">Per Hari</p>
                                            </div>
                                        @endif

                                        @if ($produk->harga_sewa_mingguan)
                                            <div
                                                class="price-option border border-gray-200 rounded-lg p-4 text-center hover:border-primary hover:shadow-sm cursor-pointer">
                                                <div class="text-lg font-bold text-primary mb-1">Rp
                                                    {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}</div>
                                                <p class="text-gray-600 text-sm">Per Minggu</p>
                                                <span
                                                    class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                    Hemat
                                                    {{ number_format((($produk->harga_sewa_harian * 7 - $produk->harga_sewa_mingguan) / ($produk->harga_sewa_harian * 7)) * 100, 0) }}%
                                                </span>
                                            </div>
                                        @endif

                                        @if ($produk->harga_sewa_bulanan)
                                            <div
                                                class="price-option border border-gray-200 rounded-lg p-4 text-center hover:border-primary hover:shadow-sm cursor-pointer">
                                                <div class="text-lg font-bold text-primary mb-1">Rp
                                                    {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}</div>
                                                <p class="text-gray-600 text-sm">Per Bulan</p>
                                                <span
                                                    class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                    Hemat
                                                    {{ number_format((($produk->harga_sewa_harian * 30 - $produk->harga_sewa_bulanan) / ($produk->harga_sewa_harian * 30)) * 100, 0) }}%
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Warna Selection -->
                        @if ($produk->warna && count($produk->warna) > 0)
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="font-semibold text-gray-900 mb-3">Warna</h4>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($produk->warna as $warna)
                                        <label class="color-option cursor-pointer">
                                            <input type="radio" name="selected_warna" value="{{ $warna }}"
                                                class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                            <div
                                                class="flex items-center gap-2 px-4 py-2.5 border-2 border-gray-200 rounded-lg hover:border-primary transition-all duration-200">
                                                <div class="w-5 h-5 rounded-full border border-gray-300"
                                                    style="background-color: {{ \App\Models\Produk::getColorCode($warna) }}">
                                                </div>
                                                <span class="font-medium text-gray-700">{{ ucfirst($warna) }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Ukuran Selection -->
                        @if ($produk->size && count($produk->size) > 0)
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="font-semibold text-gray-900 mb-3">Ukuran</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($produk->size as $size)
                                        <label class="size-option">
                                            <input type="radio" name="selected_size" value="{{ $size }}"
                                                class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                            <div
                                                class="px-5 py-2.5 border-2 border-gray-200 rounded-lg text-center font-medium text-gray-700 hover:border-primary hover:text-primary hover:bg-primary/5 cursor-pointer transition-all duration-200">
                                                {{ strtoupper($size) }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Short Description -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Deskripsi Singkat</h4>
                            <p class="text-gray-600 leading-relaxed">{{ Str::limit($produk->deskripsi, 200) }}</p>
                        </div>

                        <!-- Quick Specs -->
                        @php
                            $spesifikasi = is_array($produk->spesifikasi)
                                ? $produk->spesifikasi
                                : json_decode($produk->spesifikasi, true);
                        @endphp

                        @if ($spesifikasi)
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="font-semibold text-gray-900 mb-3">Spesifikasi</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach (array_slice($spesifikasi, 0, 4, true) as $key => $value)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </span>
                                            <span class="font-medium">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6 space-y-4">
                            @if ($produk->stok_tersedia > 0)
                                <!-- Quantity -->
                                <div class="flex items-center gap-4">
                                    <span class="text-gray-900 font-medium">Jumlah:</span>
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button onclick="decreaseQuantity()"
                                            class="px-4 py-2 text-gray-600 hover:text-primary">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" id="quantity" value="1" min="1"
                                            max="{{ $produk->stok_tersedia }}"
                                            class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none">
                                        <button onclick="increaseQuantity()"
                                            class="px-4 py-2 text-gray-600 hover:text-primary">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div
                                    class="grid grid-cols-1 {{ $produk->tipe === 'both' ? 'md:grid-cols-3' : 'md:grid-cols-2' }} gap-3">
                                    @if ($produk->tipe === 'jual' || $produk->tipe === 'both')
                                        <button onclick="addToCart('jual', false)"
                                            class="flex items-center justify-center gap-2 px-6 py-3 bg-gray-800 hover:bg-gray-950 text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Keranjang</span>
                                        </button>
                                        <button onclick="addToCart('jual', true)"
                                            class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-primary text-primary hover:bg-gray-200 font-semibold rounded-lg hover:bg-primary/5 transition-colors">
                                            <i class="fas fa-bolt"></i>
                                            <span>Beli Sekarang</span>
                                        </button>
                                    @endif

                                    @if (in_array($produk->tipe, ['sewa', 'both']))
                                        @if ($produk->stok_tersedia > 0)
                                            <button onclick="showRentalModal()"
                                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-800 transition-colors duration-200 flex items-center justify-center gap-2">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>Sewa Sekarang</span>
                                            </button>
                                        @else
                                            <button disabled
                                                class="flex-1 px-4 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                                <i class="fas fa-times"></i>
                                                <span>Stok Habis</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                                        <div>
                                            <p class="text-yellow-800 font-medium">Produk sedang tidak tersedia</p>
                                            <p class="text-yellow-700 text-sm">Silakan hubungi admin untuk informasi lebih
                                                lanjut.</p>
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
        <div class="container mx-auto px-4 md:px-8 mb-8">
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
                                    @if ($produk->warna && count($produk->warna) > 0)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                                Warna Tersedia
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @foreach ($produk->warna as $warna)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <div class="w-3 h-3 rounded-full"
                                                                style="background-color: {{ \App\Models\Produk::getColorCode($warna) }}">
                                                            </div>
                                                            {{ ucfirst($warna) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($produk->size && count($produk->size) > 0)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                                Ukuran Tersedia
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <div class="flex gap-1 flex-wrap">
                                                    @foreach ($produk->size as $size)
                                                        <span
                                                            class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                                            {{ strtoupper($size) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                    @foreach ($spesifikasi ?? [] as $key => $value)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $value }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                            Kategori</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $produk->kategori->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                            Stok Tersedia</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $produk->stok_tersedia }}</td>
                                    </tr>
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                            Tipe</td>
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
                                        @for ($i = 1; $i <= 5; $i++)
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
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <span class="text-gray-500 text-sm">2 minggu lalu</span>
                                        </div>
                                        <p class="text-gray-600">Produk sangat bagus, kualitas sesuai harga. Pengiriman
                                            cepat!</p>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <span class="inline-block mr-3">Warna: <strong>Hitam</strong></span>
                                            <span class="inline-block">Ukuran: <strong>L</strong></span>
                                        </div>
                                    </div>

                                    <!-- Review Item -->
                                    <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h5 class="font-semibold text-gray-900">Sari Dewi</h5>
                                                <div class="flex text-yellow-400 mt-1">
                                                    @for ($i = 1; $i <= 4; $i++)
                                                        <i class="fas fa-star"></i>
                                                    @endfor
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                            <span class="text-gray-500 text-sm">1 bulan lalu</span>
                                        </div>
                                        <p class="text-gray-600">Cocok untuk pemula, mudah digunakan dan hasil maksimal.
                                        </p>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <span class="inline-block mr-3">Warna: <strong>Biru</strong></span>
                                            <span class="inline-block">Ukuran: <strong>M</strong></span>
                                        </div>
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
                                <button
                                    class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 bg-gray-50 hover:bg-gray-100 flex justify-between items-center"
                                    onclick="toggleFAQ(this)">
                                    <span>Apakah tersedia semua warna dan ukuran?</span>
                                    <i class="fas fa-chevron-down transition-transform"></i>
                                </button>
                                <div class="faq-answer px-6 py-4 hidden">
                                    <p class="text-gray-600">Ya, semua warna dan ukuran yang tercantum di halaman produk
                                        tersedia. Jika suatu warna atau ukuran habis, akan kami tandai sebagai "Habis".</p>
                                </div>
                            </div>

                            <!-- FAQ Item -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button
                                    class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 bg-gray-50 hover:bg-gray-100 flex justify-between items-center"
                                    onclick="toggleFAQ(this)">
                                    <span>Berapa lama proses pengiriman?</span>
                                    <i class="fas fa-chevron-down transition-transform"></i>
                                </button>
                                <div class="faq-answer px-6 py-4 hidden">
                                    <p class="text-gray-600">Pengiriman dalam kota 1-2 hari kerja, luar kota 3-5 hari
                                        kerja.</p>
                                </div>
                            </div>

                            <!-- FAQ Item -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button
                                    class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 bg-gray-50 hover:bg-gray-100 flex justify-between items-center"
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
        @if ($relatedProducts->count() > 0)
            <div class="container mx-auto px-4 md:px-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6" data-aos="fade-up">Produk Terkait</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $related)
                        <div class="group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <div
                                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                                <!-- Image Container -->
                                <div class="relative overflow-hidden" style="height: 200px;">
                                    <img src="{{ $related->gambar_url }}" alt="{{ $related->nama }}"
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
                                            class="px-3 py-1 {{ $related->stok_tersedia > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                            <i class="fas fa-box mr-1"></i>
                                            {{ $related->stok_tersedia > 0 ? $related->stok_tersedia . ' tersedia' : 'Habis' }}
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
                                            {{ $related->kategori->nama }}
                                        </span>
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="font-bold text-gray-900 mb-2 text-lg">{{ $related->nama }}</h3>

                                    <!-- Description -->
                                    <p class="text-gray-600 text-sm mb-4 flex-1">{{ Str::limit($related->deskripsi, 80) }}
                                    </p>

                                    <div class="mb-6 bg-gray-50 rounded-xl py-4 space-y-1">
                                        @if ($related->tipe === 'jual' || $related->tipe === 'both')
                                            <!-- Sale Prices -->
                                            <p class="font-bold text-gray-900 mb-2 text-lg">Beli</p>
                                            <div class="font-bold text-emerald-600 text-lg">
                                                Rp {{ number_format($related->harga_beli, 0, ',', '.') }}
                                            </div>
                                        @endif
                                        @if ($related->tipe === 'sewa' || $related->tipe === 'both')
                                            <!-- Rent Prices -->
                                            <p class="font-bold text-gray-900 mb-2 text-lg">Sewa</p>
                                            <div class="grid grid-cols-1 gap-2 text-center">
                                                <div class=" flex justify-between gap-1 items-center">
                                                    <div class="font-bold text-emerald-600 text-lg">
                                                        Rp {{ number_format($related->harga_sewa_harian, 0, ',', '.') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">/Hari</div>
                                                </div>
                                                <div class=" flex justify-between gap-1 items-center">
                                                    <div class="font-bold text-emerald-600 text-lg">
                                                        Rp {{ number_format($related->harga_sewa_mingguan, 0, ',', '.') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">/Minggu</div>
                                                </div>
                                                <div class=" flex justify-between gap-1 items-center">
                                                    <div class="font-bold text-emerald-600 text-lg">
                                                        Rp {{ number_format($related->harga_sewa_bulanan, 0, ',', '.') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">/Bulan</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>


                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <a href="{{ route('produk.show', $related->slug) }}"
                                            class="flex-1 px-4 py-2.5 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Detail</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Sewa Modal -->
    <div id="rentalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 mx-auto p-4 w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-xl">
                <!-- Modal Header -->
                <div
                    class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary to-primary-dark rounded-t-2xl">
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
                        <input type="hidden" id="product_id" name="product_id" value="{{ $produk->id }}">

                        <!-- Product Info -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                                    <img id="modalProductImage" src="{{ $produk->gambar_url }}"
                                        alt="{{ $produk->nama }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 id="modalProductName" class="font-bold text-gray-900 mb-1">{{ $produk->nama }}
                                    </h4>
                                    <p id="modalProductCategory" class="text-sm text-gray-600">
                                        {{ $produk->kategori->nama }}</p>
                                </div>
                            </div>

                            <!-- Warna & Size Selection in Modal -->
                            @if ($produk->warna && count($produk->warna) > 0)
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Warna</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($produk->warna as $warna)
                                            <label class="modal-color-option">
                                                <input type="radio" name="warna" value="{{ $warna }}"
                                                    class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                                <div
                                                    class="flex items-center gap-2 px-3 py-1.5 border border-gray-300 rounded-lg hover:border-primary cursor-pointer">
                                                    <div class="w-4 h-4 rounded-full border border-gray-300"
                                                        style="background-color: {{ \App\Models\Produk::getColorCode($warna) }}">
                                                    </div>
                                                    <span class="text-sm">{{ ucfirst($warna) }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($produk->size && count($produk->size) > 0)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Ukuran</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($produk->size as $size)
                                            <label class="modal-size-option">
                                                <input type="radio" name="size" value="{{ $size }}"
                                                    class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                                <div
                                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:border-primary hover:text-primary hover:bg-primary/5 cursor-pointer">
                                                    {{ strtoupper($size) }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div>
                                    <div class="font-semibold text-emerald-600" id="modalDailyPrice">
                                        Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">Harian</div>
                                </div>
                                <div class="border-x border-gray-200">
                                    <div class="font-semibold text-emerald-600" id="modalWeeklyPrice">
                                        Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">Mingguan</div>
                                </div>
                                <div>
                                    <div class="font-semibold text-emerald-600" id="modalMonthlyPrice">
                                        Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}
                                    </div>
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
                                    @php
                                        $durations = [
                                            'harian' => [
                                                'label' => 'Harian',
                                                'available' => $produk->harga_sewa_harian > 0,
                                            ],
                                            'mingguan' => [
                                                'label' => 'Mingguan',
                                                'available' => $produk->harga_sewa_mingguan > 0,
                                            ],
                                            'bulanan' => [
                                                'label' => 'Bulanan',
                                                'available' => $produk->harga_sewa_bulanan > 0,
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($durations as $value => $info)
                                        @if ($info['available'])
                                            <label class="duration-option relative">
                                                <input type="radio" name="durasi" value="{{ $value }}"
                                                    class="sr-only" required
                                                    {{ $loop->first && $info['available'] ? 'checked' : '' }}>
                                                <div
                                                    class="w-full p-4 border-2 border-gray-200 rounded-xl text-center cursor-pointer hover:border-primary transition-colors duration-200">
                                                    <div class="font-semibold text-gray-900">{{ $info['label'] }}</div>
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Duration Details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Hari</label>
                                    <div class="relative">
                                        <input type="number" name="jumlah_hari" id="jumlah_hari" value="1"
                                            min="1" max="365"
                                            class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                            required>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-gray-500 text-sm">hari</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                        required>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                <textarea name="catatan" rows="3"
                                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                    placeholder="Contoh: Butuh alat untuk turnamen tanggal..."></textarea>
                            </div>

                            <!-- Price Summary -->
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Ringkasan Biaya</h4>
                                <div class="space-y-3">
                                    @if ($produk->warna && count($produk->warna) > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Warna:</span>
                                            <span class="font-semibold text-gray-900" id="selectedWarna">
                                                {{ ucfirst($produk->warna[0]) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($produk->size && count($produk->size) > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Ukuran:</span>
                                            <span class="font-semibold text-gray-900" id="selectedSize">
                                                {{ strtoupper($produk->size[0]) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Harga per hari:</span>
                                        <span class="font-semibold text-gray-900" id="pricePerDay">
                                            @if ($produk->harga_sewa_harian)
                                                Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}/hari
                                            @else
                                                Rp 0
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Jumlah hari:</span>
                                        <span class="font-semibold text-gray-900" id="daysCount">1 hari</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-3">
                                        <div class="flex justify-between">
                                            <span class="text-lg font-bold text-gray-900">Total Biaya:</span>
                                            <span class="text-2xl font-bold text-primary" id="totalPrice">
                                                @if ($produk->harga_sewa_harian)
                                                    Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                                @else
                                                    Rp 0
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" id="submitRentalBtn"
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

        .duration-option input:checked+div {
            border-color: var(--primary);
            background-color: rgba(26, 54, 93, 0.05);
        }

        /* Color selection styles */
        .color-option input:checked+div {
            border-color: var(--primary);
            background-color: rgba(26, 54, 93, 0.05);
        }

        .size-option input:checked+div {
            border-color: var(--primary);
            background-color: rgba(26, 54, 93, 0.05);
            color: var(--primary);
        }

        /* Modal color & size options */
        .modal-color-option input:checked+div {
            border-color: var(--primary);
            background-color: rgba(26, 54, 93, 0.05);
        }

        .modal-size-option input:checked+div {
            border-color: var(--primary);
            background-color: rgba(26, 54, 93, 0.05);
            color: var(--primary);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Inisialisasi data produk untuk modal
        const productData = {
            id: {{ $produk->id }},
            nama: "{{ $produk->nama }}",
            kategori: "{{ $produk->kategori->nama }}",
            gambar: "{{ $produk->gambar_url }}",
            harga_harian: {{ $produk->harga_sewa_harian ?? 0 }},
            harga_mingguan: {{ $produk->harga_sewa_mingguan ?? 0 }},
            harga_bulanan: {{ $produk->harga_sewa_bulanan ?? 0 }}
        };

        // Simpan data produk di window object
        window.currentProductData = productData;

        // ================= FUNGSI UTAMA =================

        // Fungsi untuk membuka modal sewa
        function showRentalModal() {
            // Cek stok tersedia
            @if ($produk->stok_tersedia <= 0)
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Habis',
                    text: 'Maaf, produk ini sedang tidak tersedia untuk disewa.',
                    confirmButtonColor: '#2B6CB0'
                });
                return;
            @endif

            // Cek apakah produk bisa disewa
            @if (!in_array($produk->tipe, ['sewa', 'both']))
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Tersedia',
                    text: 'Produk ini hanya tersedia untuk dijual.',
                    confirmButtonColor: '#2B6CB0'
                });
                return;
            @endif

            // Cek apakah ada harga sewa
            @if ($produk->harga_sewa_harian == 0 && $produk->harga_sewa_mingguan == 0 && $produk->harga_sewa_bulanan == 0)
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Tersedia',
                    text: 'Produk ini tidak tersedia untuk disewa.',
                    confirmButtonColor: '#2B6CB0'
                });
                return;
            @endif

            // Set tanggal minimum (besok)
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];

            const tanggalMulaiInput = document.getElementById('tanggal_mulai');
            if (tanggalMulaiInput) {
                tanggalMulaiInput.min = tomorrowStr;
                tanggalMulaiInput.value = tomorrowStr;
            }

            // Reset dan pilih durasi default yang tersedia
            const rentalForm = document.getElementById('rentalForm');
            const durasiOptions = document.querySelectorAll('input[name="durasi"]');
            const jumlahHariInput = document.getElementById('jumlah_hari');

            if (rentalForm) rentalForm.reset();
            if (jumlahHariInput) {
                jumlahHariInput.value = 1;
                jumlahHariInput.min = 1;
            }

            // Pilih durasi pertama yang tersedia
            let firstAvailableDuration = null;
            durasiOptions.forEach(option => {
                if (!firstAvailableDuration) {
                    firstAvailableDuration = option;
                    option.checked = true;
                }
            });

            // Update visual selection
            updateDurationVisualSelection();
            updateColorSizeVisualSelection();
            updateSelectedColorSizeDisplay();

            // Update harga awal
            updateRentalPrice();

            // Tampilkan modal
            const modal = document.getElementById('rentalModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                // Focus pada input pertama
                setTimeout(() => {
                    const firstInput = modal.querySelector('input, textarea, select');
                    if (firstInput) firstInput.focus();
                }, 100);
            }
        }

        // Fungsi untuk menutup modal
        function closeRentalModal() {
            const modal = document.getElementById('rentalModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Update visual selection untuk durasi
        function updateDurationVisualSelection() {
            document.querySelectorAll('.duration-option').forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                const container = option.querySelector('div');

                if (container) {
                    if (radio.checked) {
                        container.classList.add('border-primary', 'bg-primary/5');
                        container.classList.remove('border-gray-200');
                    } else {
                        container.classList.remove('border-primary', 'bg-primary/5');
                        container.classList.add('border-gray-200');
                    }
                }
            });
        }

        // Update visual selection untuk warna dan ukuran
        function updateColorSizeVisualSelection() {
            // Update warna
            document.querySelectorAll('.color-option, .modal-color-option').forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                const container = option.querySelector('div');

                if (container) {
                    if (radio.checked) {
                        container.classList.add('border-primary', 'bg-primary/5');
                        container.classList.remove('border-gray-200');
                    } else {
                        container.classList.remove('border-primary', 'bg-primary/5');
                        container.classList.add('border-gray-200');
                    }
                }
            });

            // Update ukuran
            document.querySelectorAll('.size-option, .modal-size-option').forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                const container = option.querySelector('div');

                if (container) {
                    if (radio.checked) {
                        container.classList.add('border-primary', 'bg-primary/5', 'text-primary');
                        container.classList.remove('border-gray-200', 'text-gray-700');
                    } else {
                        container.classList.remove('border-primary', 'bg-primary/5', 'text-primary');
                        container.classList.add('border-gray-200', 'text-gray-700');
                    }
                }
            });
        }

        // Update display warna dan ukuran yang dipilih di summary
        function updateSelectedColorSizeDisplay() {
            // Get selected warna
            const selectedWarnaRadio = document.querySelector('input[name="warna"]:checked');
            if (selectedWarnaRadio) {
                const selectedWarnaEl = document.getElementById('selectedWarna');
                if (selectedWarnaEl) {
                    selectedWarnaEl.textContent = selectedWarnaRadio.value.charAt(0).toUpperCase() + selectedWarnaRadio
                        .value.slice(1);
                }
            }

            // Get selected size
            const selectedSizeRadio = document.querySelector('input[name="size"]:checked');
            if (selectedSizeRadio) {
                const selectedSizeEl = document.getElementById('selectedSize');
                if (selectedSizeEl) {
                    selectedSizeEl.textContent = selectedSizeRadio.value.toUpperCase();
                }
            }
        }

        // Fungsi update harga sewa
        function updateRentalPrice() {
            const selectedDuration = document.querySelector('input[name="durasi"]:checked');
            const jumlahHariInput = document.getElementById('jumlah_hari');

            if (!selectedDuration || !window.currentProductData || !jumlahHariInput) return;

            const duration = selectedDuration.value;
            let days = parseInt(jumlahHariInput.value) || 1;

            // Validasi dan adjust jumlah hari berdasarkan durasi
            switch (duration) {
                case 'mingguan':
                    if (days < 7) days = 7;
                    // Bulatkan ke kelipatan 7
                    days = Math.ceil(days / 7) * 7;
                    break;
                case 'bulanan':
                    if (days < 30) days = 30;
                    // Bulatkan ke kelipatan 30
                    days = Math.ceil(days / 30) * 30;
                    break;
                default: // harian
                    if (days < 1) days = 1;
                    if (days > 365) days = 365; // Max 1 tahun
            }

            jumlahHariInput.value = days;

            // Hitung harga
            const {
                harga_harian,
                harga_mingguan,
                harga_bulanan
            } = window.currentProductData;

            let pricePerDay = 0;
            let totalPrice = 0;
            let displayText = "";

            switch (duration) {
                case 'harian':
                    pricePerDay = harga_harian;
                    totalPrice = pricePerDay * days;
                    displayText = `${days} hari`;
                    break;

                case 'mingguan':
                    const weeks = days / 7;
                    pricePerDay = Math.round(harga_mingguan / 7);
                    totalPrice = harga_mingguan * weeks;
                    displayText = `${weeks} minggu (${days} hari)`;
                    break;

                case 'bulanan':
                    const months = days / 30;
                    pricePerDay = Math.round(harga_bulanan / 30);
                    totalPrice = harga_bulanan * months;
                    displayText = `${months.toFixed(1)} bulan (${days} hari)`;
                    break;
            }

            // Format ke Rupiah
            function formatRupiah(angka) {
                if (!angka) return 'Rp 0';
                return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Update UI
            const pricePerDayEl = document.getElementById('pricePerDay');
            const daysCountEl = document.getElementById('daysCount');
            const totalPriceEl = document.getElementById('totalPrice');

            if (pricePerDayEl) {
                if (duration === 'harian') {
                    pricePerDayEl.textContent = formatRupiah(pricePerDay) + "/hari";
                } else {
                    pricePerDayEl.textContent = `~${formatRupiah(pricePerDay)}/hari`;
                }
            }

            if (daysCountEl) daysCountEl.textContent = displayText;
            if (totalPriceEl) totalPriceEl.textContent = formatRupiah(totalPrice);
        }

        // ================= FUNGSI PRODUK JUAL =================

        // Quantity control untuk produk jual
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

        // Fungsi add to cart untuk produk jual dengan warna dan size
        async function addToCart(type, checkout = false) {
            const quantity = document.getElementById('quantity').value;
            const selectedWarna = document.querySelector('input[name="selected_warna"]:checked')?.value || null;
            const selectedSize = document.querySelector('input[name="selected_size"]:checked')?.value || null;

            // Validasi quantity
            if (quantity < 1 || quantity > {{ $produk->stok_tersedia }}) {
                Swal.fire('Error', 'Jumlah tidak valid', 'error');
                return;
            }

            // Validasi jika produk memiliki warna tapi tidak dipilih
            @if ($produk->warna && count($produk->warna) > 0)
                if (!selectedWarna) {
                    Swal.fire('Error', 'Silakan pilih warna', 'error');
                    return;
                }
            @endif

            // Validasi jika produk memiliki size tapi tidak dipilih
            @if ($produk->size && count($produk->size) > 0)
                if (!selectedSize) {
                    Swal.fire('Error', 'Silakan pilih ukuran', 'error');
                    return;
                }
            @endif

            const data = {
                product_id: {{ $produk->id }},
                type: type,
                quantity: parseInt(quantity),
                warna: selectedWarna,
                size: selectedSize
            };

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
                        detail: {
                            count: result.cart_count
                        }
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

        // ================= FUNGSI TAB =================

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
            const tabContent = document.getElementById(`${tabName}-content`);
            if (tabContent) {
                tabContent.classList.remove('hidden');
                tabContent.classList.add('active');
            }

            // Activate selected tab button
            const tabButton = document.getElementById(`${tabName}-tab`);
            if (tabButton) {
                tabButton.classList.add('active');
            }
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

        // ================= EVENT LISTENERS =================

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

            // Event Listeners untuk warna dan size selection
            document.querySelectorAll('.color-option, .modal-color-option').forEach(option => {
                option.addEventListener('click', function() {
                    updateColorSizeVisualSelection();
                    updateSelectedColorSizeDisplay();
                });
            });

            document.querySelectorAll('.size-option, .modal-size-option').forEach(option => {
                option.addEventListener('click', function() {
                    updateColorSizeVisualSelection();
                    updateSelectedColorSizeDisplay();
                });
            });

            // Event Listeners untuk modal sewa
            // Event: Durasi berubah
            document.querySelectorAll('input[name="durasi"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    updateDurationVisualSelection();
                    updateRentalPrice();
                });
            });

            // Event: Jumlah hari berubah
            const jumlahHariInput = document.getElementById('jumlah_hari');
            if (jumlahHariInput) {
                jumlahHariInput.addEventListener('input', function() {
                    updateRentalPrice();
                });

                // Juga update saat kehilangan fokus
                jumlahHariInput.addEventListener('blur', function() {
                    updateRentalPrice();
                });
            }

            // Event: Submit form sewa
            const rentalForm = document.getElementById('rentalForm');
            if (rentalForm) {
                rentalForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = document.getElementById('submitRentalBtn');

                    if (!submitBtn) return;

                    // Validasi data
                    const tanggalMulai = formData.get('tanggal_mulai');
                    const today = new Date();
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    tomorrow.setHours(0, 0, 0, 0);

                    if (new Date(tanggalMulai) < tomorrow) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal Tidak Valid',
                            text: 'Tanggal mulai harus besok atau setelahnya',
                            confirmButtonColor: '#2B6CB0'
                        });
                        return;
                    }

                    // Validasi warna dan size untuk produk sewa
                    @if ($produk->warna && count($produk->warna) > 0)
                        if (!formData.get('warna')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warna Belum Dipilih',
                                text: 'Silakan pilih warna terlebih dahulu',
                                confirmButtonColor: '#2B6CB0'
                            });
                            return;
                        }
                    @endif

                    @if ($produk->size && count($produk->size) > 0)
                        if (!formData.get('size')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ukuran Belum Dipilih',
                                text: 'Silakan pilih ukuran terlebih dahulu',
                                confirmButtonColor: '#2B6CB0'
                            });
                            return;
                        }
                    @endif

                    const originalContent = submitBtn.innerHTML;

                    // Tampilkan loading
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
                                warna: formData.get('warna'),
                                size: formData.get('size'),
                                options: {
                                    durasi: formData.get('durasi'),
                                    jumlah_hari: formData.get('jumlah_hari'),
                                    tanggal_mulai: formData.get('tanggal_mulai'),
                                    catatan: formData.get('catatan') || ''
                                }
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Tutup modal
                            closeRentalModal();

                            // Update cart badge
                            if (typeof window.updateCartCount === 'function') {
                                window.updateCartCount(data.cart_count);
                            } else {
                                window.dispatchEvent(new CustomEvent('cartUpdated', {
                                    detail: {
                                        count: data.cart_count
                                    }
                                }));
                            }

                            // Tampilkan pesan sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Produk telah ditambahkan ke keranjang',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message ||
                                'Terjadi kesalahan saat menambahkan ke keranjang',
                            confirmButtonColor: '#2B6CB0'
                        });
                    } finally {
                        submitBtn.innerHTML = originalContent;
                        submitBtn.disabled = false;
                    }
                });
            }

            // Close modal on background click
            const modal = document.getElementById('rentalModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRentalModal();
                    }
                });
            }

            // Close modal on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('rentalModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeRentalModal();
                    }
                }
            });

            // Update visual selection awal
            updateDurationVisualSelection();
            updateColorSizeVisualSelection();
            updateSelectedColorSizeDisplay();
        });
    </script>
@endpush
