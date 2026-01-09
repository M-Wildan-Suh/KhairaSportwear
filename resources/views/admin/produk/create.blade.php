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
        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                        <input type="text" name="nama" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="{{ old('nama') }}">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                        <select name="kategori_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Produk *</label>
                        <select name="tipe" required id="tipe"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                                onchange="updateFormFields()">
                            <option value="">Pilih Tipe</option>
                            <option value="jual" {{ old('tipe') == 'jual' ? 'selected' : '' }}>Jual</option>
                            <option value="sewa" {{ old('tipe') == 'sewa' ? 'selected' : '' }}>Sewa</option>
                            <option value="both" {{ old('tipe') == 'both' ? 'selected' : '' }}>Jual & Sewa</option>
                        </select>
                        @error('tipe')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Price Information -->
                <div class="space-y-4">
                    <!-- Harga Beli (for jual/both) -->
                    <div id="harga-beli-group" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli *</label>
                        <input type="number" name="harga_beli" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="{{ old('harga_beli') }}">
                        @error('harga_beli')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Harga Sewa (for sewa/both) -->
                    <div id="harga-sewa-group" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Sewa Harian *</label>
                        <input type="number" name="harga_sewa_harian" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="{{ old('harga_sewa_harian') }}">
                        @error('harga_sewa_harian')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Mingguan</label>
                                <input type="number" name="harga_sewa_mingguan" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                                       value="{{ old('harga_sewa_mingguan') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Bulanan</label>
                                <input type="number" name="harga_sewa_bulanan" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                                       value="{{ old('harga_sewa_bulanan') }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Total *</label>
                        <input type="number" name="stok_total" required min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="{{ old('stok_total', 0) }}">
                        @error('stok_total')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Tersedia *</label>
                        <input type="number" name="stok_tersedia" required min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="{{ old('stok_tersedia', 0) }}">
                        <p class="mt-1 text-xs text-gray-500">Stok yang tersedia untuk dijual/disewa</p>
                        @error('stok_tersedia')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Image Upload -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                        <input type="file" name="gambar" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Max: 2MB, Format: JPG, PNG, JPEG</p>
                        @error('gambar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Aktifkan Produk</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.produk.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="btn-admin-primary px-6 py-2">
                    <i class="fas fa-save mr-2"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function updateFormFields() {
        const tipe = document.getElementById('tipe').value;
        const hargaBeliGroup = document.getElementById('harga-beli-group');
        const hargaSewaGroup = document.getElementById('harga-sewa-group');
        
        if (tipe === 'jual') {
            hargaBeliGroup.style.display = 'block';
            hargaSewaGroup.style.display = 'none';
        } else if (tipe === 'sewa') {
            hargaBeliGroup.style.display = 'none';
            hargaSewaGroup.style.display = 'block';
        } else if (tipe === 'both') {
            hargaBeliGroup.style.display = 'block';
            hargaSewaGroup.style.display = 'block';
        } else {
            hargaBeliGroup.style.display = 'none';
            hargaSewaGroup.style.display = 'none';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateFormFields();
    });
</script>
@endpush