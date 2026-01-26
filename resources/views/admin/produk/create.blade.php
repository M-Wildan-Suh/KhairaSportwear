{{-- resources/views/admin/produk/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Produk')

@section('page-title', 'Tambah Produk Baru')
@section('page-subtitle', 'Isi form untuk menambahkan produk baru')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.produk.index'), 'label' => 'Produk'],
            ['label' => 'Tambah']
        ];
    @endphp
@endsection

@section('content')
    <div class="admin-card">
        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" id="produk-form">
            @csrf
            
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
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nama Produk *</label>
                                <input type="text" name="nama" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                       value="{{ old('nama') }}"
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
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Kategori *</label>
                                    <div class="relative">
                                        <select name="kategori_id" required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200 bg-gray-50">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($kategoris as $kategori)
                                                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-300">
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
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Tipe Produk *</label>
                                    <div class="relative">
                                        <select name="tipe" required id="tipe"
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200 bg-gray-50"
                                                onchange="updateFormFields()">
                                            <option value="">Pilih Tipe</option>
                                            <option value="jual" {{ old('tipe') == 'jual' ? 'selected' : '' }}>Jual</option>
                                            <option value="sewa" {{ old('tipe') == 'sewa' ? 'selected' : '' }}>Sewa</option>
                                            <option value="both" {{ old('tipe') == 'both' ? 'selected' : '' }}>Jual & Sewa</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-300">
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
                                <label class="block text-sm font-medium text-gray-300 mb-2">Stok Total *</label>
                                <div class="relative">
                                    <input type="number" name="stok_total" required min="0"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                           value="{{ old('stok_total', 0) }}"
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
                                <!-- Stok Sewa (conditional) -->
                                <div id="stok-disewa-group" class="hidden">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Stok untuk Disewa</label>
                                    <div class="relative">
                                        <input type="number" name="stok_disewa" min="0" id="stok_disewa"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                               value="{{ old('stok_disewa', 0) }}"
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
                            <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                      placeholder="Deskripsikan produk Anda...">{{ old('deskripsi') }}</textarea>
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
                            <div id="harga-beli-group" class="hidden">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Harga Beli *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500">Rp</span>
                                    </div>
                                    <input type="number" name="harga_beli" min="0"
                                           class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                           value="{{ old('harga_beli') }}"
                                           placeholder="0">
                                </div>
                                @error('harga_beli')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <!-- Harga Sewa -->
                            <div id="harga-sewa-group" class="hidden space-y-4">
                                <!-- Harga Sewa Harian -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Harga Sewa Harian *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="number" name="harga_sewa_harian" min="0"
                                               class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                               value="{{ old('harga_sewa_harian') }}"
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
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga Mingguan</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="number" name="harga_sewa_mingguan" min="0"
                                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                                   value="{{ old('harga_sewa_mingguan') }}"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Harga Bulanan -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga Bulanan</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="number" name="harga_sewa_bulanan" min="0"
                                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition duration-200"
                                                   value="{{ old('harga_sewa_bulanan') }}"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Price Message -->
                            <div id="no-price-message" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                    <p class="text-sm text-yellow-700">Pilih tipe produk untuk menampilkan field harga</p>
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
                                <label class="block text-sm font-medium text-gray-300 mb-3">Warna</label>
                                <div class="flex flex-wrap gap-2 mb-3" id="warna-container">
                                    <!-- Warna akan ditambahkan secara dinamis -->
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
                                <input type="hidden" name="warna" id="warna-hidden" value="{{ old('warna', '[]') }}">
                                @error('warna')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <!-- Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Ukuran (Size)</label>
                                <div class="flex flex-wrap gap-2 mb-3" id="size-container">
                                    <!-- Size akan ditambahkan secara dinamis -->
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
                                <input type="hidden" name="size" id="size-hidden" value="{{ old('size', '[]') }}">
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
                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Gambar Produk</label>

                                <div class="relative">
                                    <input type="file"
                                           name="gambar[]"
                                           id="gambar"
                                           accept="image/png, image/jpg, image/jpeg"
                                           class="hidden"
                                           multiple
                                           onchange="previewMultipleImages(event)">

                                    <label for="gambar" class="cursor-pointer">
                                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center hover:border-primary/50 hover:bg-gray-800/30 transition duration-200">
                                            <!-- Preview -->
                                            <div id="image-previews" class="space-y-4 mb-6 hidden"></div>

                                            <!-- Placeholder -->
                                            <div id="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                                <p class="text-base text-gray-300 mb-2 font-medium">
                                                    Klik untuk upload gambar
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    Max: 2MB, Format: JPG, PNG, JPEG
                                                </p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    Bisa upload beberapa gambar sekaligus
                                                </p>
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
                                    <label class="text-sm font-medium text-gray-700">Status Produk</label>
                                    <p class="text-xs text-gray-700">Produk akan langsung aktif setelah disimpan</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}
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
                <a href="{{ route('admin.produk.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-300 hover:bg-gray-50 transition duration-200 flex items-center">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="btn-admin-primary px-8 py-3 flex items-center group">
                    <i class="fas fa-save mr-2 group-hover:animate-pulse"></i> Simpan Produk
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
    
    /* Custom select styling */
    select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.3s ease-in-out;
    }
    
    /* Custom checkbox (toggle switch) */
    .switch-checkbox:checked {
        right: 0;
        border-color: #3b82f6;
    }
    
    /* Image preview hover effect */
    #image-upload-area:hover {
        background-color: #f9fafb;
    }
</style>
@endpush

@push('scripts')
<script>
    // Image Preview Functions
    let selectedFiles = [];

    function previewMultipleImages(event) {
        const files = Array.from(event.target.files);
        const previewContainer = document.getElementById('image-previews');
        const placeholder = document.getElementById('upload-placeholder');

        selectedFiles = files;

        if (files.length > 0) {
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('hidden');
            placeholder.classList.add('hidden');

            files.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative group bg-gray-800/50 rounded-xl overflow-hidden border-2 border-green-500';

                    wrapper.innerHTML = `
                        <div class="relative w-full" style="padding-bottom:56.25%;">
                            <img src="${e.target.result}"
                                 class="absolute inset-0 w-full h-full object-cover">

                            <div class="absolute top-4 left-4">
                                <span class="bg-green-500 text-white text-sm px-3 py-1.5 rounded-lg shadow-lg font-medium flex items-center gap-2">
                                    <i class="fas fa-plus"></i>
                                    Gambar Baru
                                </span>
                            </div>
                        </div>

                        <div class="p-4 flex items-center justify-between">
                            <span class="text-sm text-gray-300 truncate">${file.name}</span>
                            <button type="button"
                                    onclick="removePreviewImage(${index})"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg flex items-center gap-2">
                                <i class="fas fa-times"></i>
                                Batal
                            </button>
                        </div>
                    `;

                    previewContainer.appendChild(wrapper);
                };

                reader.readAsDataURL(file);
            });
        }
    }

    function removePreviewImage(index) {
        selectedFiles.splice(index, 1);

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        document.getElementById('gambar').files = dataTransfer.files;

        if (selectedFiles.length === 0) {
            document.getElementById('image-previews').classList.add('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        } else {
            previewMultipleImages({ target: { files: selectedFiles } });
        }
    }
    
    // Form Field Management
    function updateFormFields() {
        const tipe = document.getElementById('tipe').value;
        const hargaBeliGroup = document.getElementById('harga-beli-group');
        const hargaSewaGroup = document.getElementById('harga-sewa-group');
        const stokDisewaGroup = document.getElementById('stok-disewa-group');
        const noPriceMessage = document.getElementById('no-price-message');
        
        // Reset all groups
        [hargaBeliGroup, hargaSewaGroup, stokDisewaGroup, noPriceMessage].forEach(el => {
            el.classList.add('hidden');
        });
        
        if (tipe === 'jual') {
            hargaBeliGroup.classList.remove('hidden');
        } else if (tipe === 'sewa') {
            hargaSewaGroup.classList.remove('hidden');
            stokDisewaGroup.classList.remove('hidden');
        } else if (tipe === 'both') {
            hargaBeliGroup.classList.remove('hidden');
            hargaSewaGroup.classList.remove('hidden');
            stokDisewaGroup.classList.remove('hidden');
        } else {
            noPriceMessage.classList.remove('hidden');
        }
        
        updateAvailableStock();
    }
    
    // Stock Management
    function updateAvailableStock() {
        const totalStock = parseInt(document.querySelector('input[name="stok_total"]').value) || 0;
        const disewaStock = parseInt(document.getElementById('stok_disewa').value) || 0;
        const tersediaInput = document.getElementById('stok_tersedia');
        const tipe = document.getElementById('tipe').value;
        
        if (tipe === 'both' && tersediaInput) {
            tersediaInput.value = Math.max(0, totalStock - disewaStock);
        } else if (tipe === 'jual' && tersediaInput) {
            tersediaInput.value = totalStock;
        }
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
    
    // Variants Management with better UI
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
        return colorMap[lowerColor] || '#6b7280'; // Default grey if not found
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateFormFields();
        
        // Load existing warna and sizes from old input
        const existingWarna = JSON.parse(document.getElementById('warna-hidden').value || '[]');
        const existingSizes = JSON.parse(document.getElementById('size-hidden').value || '[]');
        
        existingWarna.forEach(warna => {
            const container = document.getElementById('warna-container');
            const tag = document.createElement('div');
            tag.className = 'variant-tag';
            tag.innerHTML = `
                <i class="fas fa-circle mr-2" style="color: ${getColorCode(warna)}"></i>
                ${warna}
                <button type="button" onclick="removeWarna('${warna}')" class="ml-2">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(tag);
        });
        
        existingSizes.forEach(size => {
            const container = document.getElementById('size-container');
            const tag = document.createElement('div');
            tag.className = 'variant-tag';
            tag.innerHTML = `
                <i class="fas fa-ruler mr-2 text-primary"></i>
                ${size}
                <button type="button" onclick="removeSize('${size}')" class="ml-2">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(tag);
        });
        
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
        
        // Form validation before submit
        document.getElementById('produk-form').addEventListener('submit', function(e) {
            const tipe = document.getElementById('tipe').value;
            const hargaBeli = document.querySelector('input[name="harga_beli"]');
            const hargaSewa = document.querySelector('input[name="harga_sewa_harian"]');
            
            if (tipe === 'jual' && (!hargaBeli.value || hargaBeli.value <= 0)) {
                e.preventDefault();
                showNotification('Harap isi harga beli untuk produk jual', 'error');
                hargaBeli.focus();
            } else if (tipe === 'sewa' && (!hargaSewa.value || hargaSewa.value <= 0)) {
                e.preventDefault();
                showNotification('Harap isi harga sewa harian untuk produk sewa', 'error');
                hargaSewa.focus();
            } else if (tipe === 'both' && ((!hargaBeli.value || hargaBeli.value <= 0) || (!hargaSewa.value || hargaSewa.value <= 0))) {
                e.preventDefault();
                showNotification('Harap isi harga beli dan harga sewa untuk produk both', 'error');
            }
        });
    });
</script>
@endpush