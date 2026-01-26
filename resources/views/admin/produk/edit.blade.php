{{-- resources/views/admin/produk/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Produk')

@section('page-title', 'Edit Produk')
@section('page-subtitle', 'Perbarui informasi produk')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.produk.index'), 'label' => 'Produk'],
            ['url' => route('admin.produk.show', $produk), 'label' => $produk->nama],
            ['label' => 'Edit']
        ];
    @endphp
@endsection

@section('content')
    <div class="admin-card">
        <form action="{{ route('admin.produk.update', $produk) }}" method="POST" enctype="multipart/form-data" id="produk-form">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column: Basic Information -->
                <div class="space-y-6">
                    <!-- Product Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-cube text-primary mr-2"></i> Informasi Produk
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Nama Produk -->
                            <div>
                                <label class="block text-sm font-medium text-gray-100 mb-2">Nama Produk *</label>
                                <input type="text" name="nama" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                       value="{{ old('nama', $produk->nama) }}"
                                       placeholder="Masukkan nama produk">
                                @error('nama')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Kategori -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-100 mb-2">Kategori *</label>
                                    <div class="relative">
                                        <select name="kategori_id" required
                                                style="color: #111; background-color: #fff;"
                                                class="w-full rounded-lg px-4 py-3 border border-gray-300 appearance-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($kategoris as $kategori)
                                                <option value="{{ $kategori->id }}"
                                                        {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-100">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                    @error('kategori_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                
                                <!-- Tipe Produk -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-100 mb-2">Tipe Produk *</label>
                                    <div class="relative">
                                        <select name="tipe" required id="tipe"
                                                style="color: #111; background-color: #fff;"
                                                class="w-full rounded-lg px-4 py-3 border border-gray-300 appearance-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                                onchange="updateFormFields()">
                                            <option value="">Pilih Tipe</option>
                                            <option value="jual" {{ old('tipe', $produk->tipe) == 'jual' ? 'selected' : '' }}>Jual</option>
                                            <option value="sewa" {{ old('tipe', $produk->tipe) == 'sewa' ? 'selected' : '' }}>Sewa</option>
                                            <option value="both" {{ old('tipe', $produk->tipe) == 'both' ? 'selected' : '' }}>Jual & Sewa</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-100">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                    @error('tipe')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-boxes text-primary mr-2"></i> Manajemen Stok
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Stok Total -->
                            <div>
                                <label class="block text-sm font-medium text-gray-100 mb-2">Stok Total *</label>
                                <div class="relative">
                                    <input type="number" name="stok_total" required min="0"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                           value="{{ old('stok_total', $produk->stok_total) }}"
                                           oninput="updateAvailableStock()">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500">unit</span>
                                    </div>
                                </div>
                                @error('stok_total')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div id="stock-fields" class="space-y-4">
                                <!-- Stok Tersedia -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-100 mb-2">Stok Tersedia *</label>
                                    <div class="relative">
                                        <input type="number" name="stok_tersedia" required min="0" id="stok_tersedia"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                               value="{{ old('stok_tersedia', $produk->stok_tersedia) }}">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-gray-500">unit</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i> Stok yang tersedia untuk dijual
                                    </p>
                                    @error('stok_tersedia')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                
                                <!-- Stok Sewa (conditional) -->
                                <div id="stok-disewa-group" class="{{ in_array($produk->tipe, ['sewa', 'both']) ? '' : 'hidden' }}">
                                    <label class="block text-sm font-medium text-gray-100 mb-2">Stok untuk Disewa *</label>
                                    <div class="relative">
                                        <input type="number" name="stok_disewa" min="0" id="stok_disewa"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                               value="{{ old('stok_disewa', $produk->stok_disewa) }}"
                                               oninput="validateDisewaStock()">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-gray-500">unit</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i> Stok yang tersedia untuk disewa
                                    </p>
                                    @error('stok_disewa')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-align-left text-primary mr-2"></i> Deskripsi Produk
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-100 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                      placeholder="Deskripsikan produk Anda...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Price & Variants -->
                <div class="space-y-6">
                    <!-- Price Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-tags text-primary mr-2"></i> Informasi Harga
                        </h3>
                        
                        <div id="price-fields" class="space-y-4">
                            <!-- Harga Beli -->
                            <div id="harga-beli-group" class="{{ in_array($produk->tipe, ['jual', 'both']) ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-100 mb-2">Harga Beli *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500">Rp</span>
                                    </div>
                                    <input type="number" name="harga_beli" min="0"
                                           class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                           value="{{ old('harga_beli', $produk->harga_beli) }}"
                                           placeholder="0">
                                </div>
                                @error('harga_beli')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <!-- Harga Sewa -->
                            <div id="harga-sewa-group" class="{{ in_array($produk->tipe, ['sewa', 'both']) ? 'space-y-4' : 'hidden' }}">
                                <!-- Harga Sewa Harian -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-100 mb-2">Harga Sewa Harian *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="number" name="harga_sewa_harian" min="0"
                                               class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                               value="{{ old('harga_sewa_harian', $produk->harga_sewa_harian) }}"
                                               placeholder="0">
                                    </div>
                                    @error('harga_sewa_harian')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Harga Mingguan -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-100 mb-2">Harga Mingguan</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="number" name="harga_sewa_mingguan" min="0"
                                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                                   value="{{ old('harga_sewa_mingguan', $produk->harga_sewa_mingguan) }}"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Harga Bulanan -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-100 mb-2">Harga Bulanan</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="number" name="harga_sewa_bulanan" min="0"
                                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                                   value="{{ old('harga_sewa_bulanan', $produk->harga_sewa_bulanan) }}"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Variants Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-palette text-primary mr-2"></i> Variasi Produk
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Warna -->
                            <div>
                                <label class="block text-sm font-medium text-gray-100 mb-3">Warna</label>
                                <div class="flex flex-wrap gap-2 mb-3" id="warna-container">
                                    @if($produk->warna && count($produk->warna) > 0)
                                        @foreach($produk->warna as $warna)
                                            <div class="variant-tag">
                                                <i class="fas fa-circle mr-2" style="color: {{ \App\Models\Produk::getColorCode($warna) }}"></i>
                                                {{ $warna }}
                                                <button type="button" onclick="removeWarna('{{ $warna }}')" class="ml-2">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" id="warna-input" 
                                           class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                           placeholder="Ketik warna (contoh: Merah, Biru, Hitam)">
                                    <button type="button" onclick="addWarna()"
                                            class="px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition duration-200">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-lightbulb mr-1"></i> Tekan Enter atau tombol plus untuk menambah warna
                                </p>
                                <input type="hidden" name="warna" id="warna-hidden" value="{{ old('warna', $produk->warna ? json_encode($produk->warna) : null) }}">
                                @error('warna')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <!-- Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-100 mb-3">Ukuran (Size)</label>
                                <div class="flex flex-wrap gap-2 mb-3" id="size-container">
                                    @if($produk->size && count($produk->size) > 0)
                                        @foreach($produk->size as $size)
                                            <div class="variant-tag">
                                                <i class="fas fa-ruler mr-2 text-primary"></i>
                                                {{ $size }}
                                                <button type="button" onclick="removeSize('{{ $size }}')" class="ml-2">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" id="size-input"
                                           class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                           placeholder="Ketik ukuran (contoh: S, M, L, XL)">
                                    <button type="button" onclick="addSize()"
                                            class="px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition duration-200">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-lightbulb mr-1"></i> Tekan Enter atau tombol plus untuk menambah ukuran
                                </p>
                                <input type="hidden" name="size" id="size-hidden" value="{{ old('size', $produk->size ? json_encode($produk->size) : '[]') }}">
                                @error('size')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Image & Status Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-image text-primary mr-2"></i> Media & Status
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Image Upload Section -->
                            <div>
                                <label class="block text-sm font-medium text-gray-100 mb-3">Gambar Produk</label>
                                
                                <!-- Existing Images Display -->
                                @if($produk->gambarTambahan->count() > 0)
                                    <div class="space-y-4 mb-4">
                                        @foreach($produk->gambarTambahan as $img)
                                            <div class="relative group bg-gray-800/50 rounded-xl overflow-hidden border-2 border-gray-700 hover:border-gray-600 transition">
                                                <!-- Image Container 16:9 -->
                                                <div class="relative w-full" style="padding-bottom: 56.25%;"> <!-- 16:9 ratio -->
                                                    <img src="{{ $img->gambar_url }}" 
                                                         alt="{{ $produk->nama }}"
                                                         class="absolute inset-0 w-full h-full object-cover">
                                                    
                                                    <!-- Primary Badge -->
                                                    @if($img->is_primary)
                                                        <div class="absolute top-4 left-4">
                                                            <span class="bg-blue-500 text-white text-sm px-3 py-1.5 rounded-lg shadow-lg font-medium flex items-center gap-2">
                                                                <i class="fas fa-star"></i>
                                                                Gambar Utama
                                                            </span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Overlay saat dicentang -->
                                                    <div class="delete-overlay-{{ $img->id }} absolute inset-0 bg-red-500 bg-opacity-0 transition-all duration-300 pointer-events-none flex items-center justify-center">
                                                        <div class="text-center opacity-0 transition-opacity duration-300">
                                                            <i class="fas fa-trash text-white text-5xl mb-3"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Info Bar -->
                                                <div class="p-4 flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                    
                                                    <!-- Checkbox only -->
                                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                                        <input type="checkbox" 
                                                               name="hapus_gambar[]" 
                                                               value="{{ $img->id }}" 
                                                               class="form-checkbox h-5 w-5 text-red-600 rounded border-gray-600 bg-gray-700 focus:ring-red-500 focus:ring-offset-gray-800"
                                                               onchange="toggleDeleteOverlay(this, {{ $img->id }})"
                                                               id="delete-checkbox-{{ $img->id }}">
                                                        <span class="text-sm text-gray-300">Tandai untuk dihapus</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 mb-4">
                                        <p class="text-sm text-blue-300 flex items-center gap-2">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Tandai gambar yang ingin dihapus dengan checkbox, lalu klik tombol <strong>Simpan Perubahan</strong> di bawah untuk menghapus gambar.</span>
                                        </p>
                                    </div>
                                @elseif($produk->gambar)
                                    <!-- Legacy single image fallback -->
                                    <div class="mb-4">
                                        <div class="relative w-full bg-gray-800/50 rounded-xl overflow-hidden border-2 border-gray-700" style="padding-bottom: 56.25%;">
                                            <img src="{{ $produk->gambar_url }}" 
                                                 alt="{{ $produk->nama }}"
                                                 class="absolute inset-0 w-full h-full object-cover">
                                        </div>
                                        <div class="mt-3 p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                                            <p class="text-xs text-yellow-300 flex items-center gap-2">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Gambar lama (format single image). Upload gambar baru untuk menggunakan multiple image.
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- No image -->
                                    <div class="mb-4">
                                        <div class="relative w-full bg-gray-800/50 rounded-xl overflow-hidden border-2 border-dashed border-gray-600" style="padding-bottom: 56.25%;">
                                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                                <i class="fas fa-image text-6xl text-gray-600 mb-3"></i>
                                                <p class="text-sm text-gray-400">Belum ada gambar</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Upload New Images -->
                                <div class="relative">
                                    <input type="file" name="gambar[]" id="gambar" accept="image/png, image/jpg, image/jpeg"
                                           class="hidden"
                                           multiple
                                           onchange="previewMultipleImages(event)">
                                    <label for="gambar" class="cursor-pointer">
                                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center hover:border-primary/50 hover:bg-gray-800/30 transition duration-200">
                                            <div id="image-previews" class="space-y-4 mb-6 hidden"></div>
                                            <div id="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                                <p class="text-base text-gray-300 mb-2 font-medium">Klik untuk tambah gambar baru</p>
                                                <p class="text-sm text-gray-500">Max: 2MB per file, Format: JPG, PNG, JPEG</p>
                                                <p class="text-sm text-gray-500 mt-1">Bisa upload beberapa gambar sekaligus</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('gambar.*')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <!-- Status -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-800">Status Produk</label>
                                    <p class="text-xs text-gray-700">Aktifkan/nonaktifkan produk ini</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $produk->is_active) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.produk.show', $produk) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-800 hover:bg-gray-50 transition duration-200 flex items-center">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="btn-admin-primary px-8 py-3 flex items-center group">
                    <i class="fas fa-save mr-2 group-hover:animate-pulse"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .variant-tag {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 100%);
        color: #374151;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }
    .variant-tag:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .variant-tag button {
        margin-left: 0.75rem;
        color: #9ca3af;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 0.75rem;
        transition: color 0.2s ease;
    }
    .variant-tag button:hover {
        color: #ef4444;
    }
</style>
@endpush

@push('scripts')
<script>
    // Image preview function
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('upload-placeholder');
        const imagePreview = document.getElementById('image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                placeholder.style.display = 'none';
                imagePreview.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Update form fields based on product type
    function updateFormFields() {
        const tipe = document.getElementById('tipe').value;
        const hargaBeliGroup = document.getElementById('harga-beli-group');
        const hargaSewaGroup = document.getElementById('harga-sewa-group');
        const stokDisewaGroup = document.getElementById('stok-disewa-group');
        
        // Update visibility
        if (tipe === 'jual') {
            hargaBeliGroup.classList.remove('hidden');
            hargaSewaGroup.classList.add('hidden');
            stokDisewaGroup.classList.add('hidden');
        } else if (tipe === 'sewa') {
            hargaBeliGroup.classList.add('hidden');
            hargaSewaGroup.classList.remove('hidden');
            hargaSewaGroup.classList.add('space-y-4');
            stokDisewaGroup.classList.remove('hidden');
        } else if (tipe === 'both') {
            hargaBeliGroup.classList.remove('hidden');
            hargaSewaGroup.classList.remove('hidden');
            hargaSewaGroup.classList.add('space-y-4');
            stokDisewaGroup.classList.remove('hidden');
        } else {
            hargaBeliGroup.classList.add('hidden');
            hargaSewaGroup.classList.add('hidden');
            stokDisewaGroup.classList.add('hidden');
        }
        
        updateAvailableStock();
    }
    
    // Update available stock calculation
    function updateAvailableStock() {
        const totalStock = parseInt(document.querySelector('input[name="stok_total"]').value) || 0;
        const disewaStock = parseInt(document.getElementById('stok_disewa').value) || 0;
        const tersediaInput = document.getElementById('stok_tersedia');
        const tipe = document.getElementById('tipe').value;
        
        if (tipe === 'both') {
            tersediaInput.value = Math.max(0, totalStock - disewaStock);
        } else if (tipe === 'jual') {
            tersediaInput.value = totalStock;
        }
        // For 'sewa', let the user decide
    }
    
    // Validate disewa stock
    function validateDisewaStock() {
        const totalStock = parseInt(document.querySelector('input[name="stok_total"]').value) || 0;
        const disewaStock = parseInt(document.getElementById('stok_disewa').value) || 0;
        
        if (disewaStock > totalStock) {
            showNotification('Stok untuk disewa tidak boleh melebihi stok total!', 'error');
            document.getElementById('stok_disewa').value = totalStock;
        }
        
        updateAvailableStock();
    }
    
    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-0 ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Helper function to get color code based on color name
    function getColorCode(colorName) {
        const colorMap = {
            'merah': '#ef4444',
            'biru': '#3b82f6',
            'hijau': '#10b981',
            'kuning': '#f59e0b',
            'hitam': '#000000',
            'putih': '#ffffff',
            'abu-abu': '#6b7280',
            'coklat': '#92400e',
            'ungu': '#8b5cf6',
            'pink': '#ec4899',
            'orange': '#f97316',
            'emas': '#fbbf24',
            'perak': '#9ca3af'
        };
        
        const lowerColor = colorName.toLowerCase();
        return colorMap[lowerColor] || '#6b7280';
    }
    
    // Variants Management
    function addWarna() {
        const input = document.getElementById('warna-input');
        const warna = input.value.trim();
        
        if (warna) {
            const container = document.getElementById('warna-container');
            const hiddenInput = document.getElementById('warna-hidden');
            let currentWarna = JSON.parse(hiddenInput.value || '[]');
            
            if (!currentWarna.includes(warna)) {
                currentWarna.push(warna);
                hiddenInput.value = JSON.stringify(currentWarna);
                
                // Add animated tag
                const tag = document.createElement('div');
                tag.className = 'variant-tag animate-pulse';
                tag.innerHTML = `
                    <i class="fas fa-circle mr-2" style="color: ${getColorCode(warna)}"></i>
                    ${warna}
                    <button type="button" onclick="removeWarna('${warna}')" class="ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(tag);
                
                // Remove animation after adding
                setTimeout(() => {
                    tag.classList.remove('animate-pulse');
                }, 500);
                
                input.value = '';
                input.focus();
            } else {
                showNotification('Warna ini sudah ditambahkan', 'info');
            }
        }
    }
    
    function removeWarna(warna) {
        const container = document.getElementById('warna-container');
        const hiddenInput = document.getElementById('warna-hidden');
        let currentWarna = JSON.parse(hiddenInput.value || '[]');
        
        currentWarna = currentWarna.filter(w => w !== warna);
        hiddenInput.value = JSON.stringify(currentWarna);
        
        // Smooth removal animation
        container.childNodes.forEach(child => {
            if (child.textContent.includes(warna)) {
                child.classList.add('opacity-0', 'transform', 'scale-95');
                setTimeout(() => child.remove(), 300);
            }
        });
    }
    
    function addSize() {
        const input = document.getElementById('size-input');
        const size = input.value.trim().toUpperCase();
        
        if (size) {
            const container = document.getElementById('size-container');
            const hiddenInput = document.getElementById('size-hidden');
            let currentSizes = JSON.parse(hiddenInput.value || '[]');
            
            if (!currentSizes.includes(size)) {
                currentSizes.push(size);
                hiddenInput.value = JSON.stringify(currentSizes);
                
                // Add animated tag
                const tag = document.createElement('div');
                tag.className = 'variant-tag animate-pulse';
                tag.innerHTML = `
                    <i class="fas fa-ruler mr-2 text-primary"></i>
                    ${size}
                    <button type="button" onclick="removeSize('${size}')" class="ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(tag);
                
                // Remove animation after adding
                setTimeout(() => {
                    tag.classList.remove('animate-pulse');
                }, 500);
                
                input.value = '';
                input.focus();
            } else {
                showNotification('Ukuran ini sudah ditambahkan', 'info');
            }
        }
    }
    
    function removeSize(size) {
        const container = document.getElementById('size-container');
        const hiddenInput = document.getElementById('size-hidden');
        let currentSizes = JSON.parse(hiddenInput.value || '[]');
        
        currentSizes = currentSizes.filter(s => s !== size);
        hiddenInput.value = JSON.stringify(currentSizes);
        
        // Smooth removal animation
        container.childNodes.forEach(child => {
            if (child.textContent.includes(size)) {
                child.classList.add('opacity-0', 'transform', 'scale-95');
                setTimeout(() => child.remove(), 300);
            }
        });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add enter key support
        document.getElementById('warna-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addWarna();
            }
        });
        
        document.getElementById('size-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSize();
            }
        });
        
        // Auto-update stock calculations
        document.querySelector('input[name="stok_total"]').addEventListener('input', updateAvailableStock);
        document.getElementById('stok_disewa').addEventListener('input', validateDisewaStock);
    });
</script>
@endpush