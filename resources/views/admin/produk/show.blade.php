{{-- resources/views/admin/produk/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Produk')

@section('page-title', 'Detail Produk')
@section('page-subtitle', 'Informasi lengkap produk')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.produk.index'), 'label' => 'Produk'],
            ['label' => $produk->nama]
        ];
    @endphp
@endsection

@section('content')
    <div class="admin-card">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-100">{{ $produk->nama }}</h3>
                <p class="text-gray-600 mt-1">ID: {{ $produk->id }} • Dibuat: {{ $produk->created_at->format('d M Y') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.produk.edit', $produk) }}" 
                   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.produk.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Product Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-primary mr-2"></i> Informasi Produk
                    </h4>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Kategori</p>
                                <p class="font-medium">{{ $produk->kategori->nama }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tipe Produk</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $produk->tipe === 'jual' ? 'bg-blue-100 text-blue-800' : 
                                       ($produk->tipe === 'sewa' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ ucfirst($produk->tipe) }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $produk->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-circle text-xs mr-1 {{ $produk->is_active ? 'text-green-500' : 'text-red-500' }}"></i>
                                {{ $produk->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Deskripsi</p>
                            <p class="mt-1 text-gray-700">{{ $produk->deskripsi ?: 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stock Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-boxes text-primary mr-2"></i> Informasi Stok
                    </h4>
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                                <p class="text-sm text-yellow-600">Stok Total</p>
                                <p class="text-2xl font-bold text-yellow-700">{{ $produk->stok_total }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <p class="text-sm text-green-600">Stok Tersedia</p>
                                <p class="text-2xl font-bold text-green-700">{{ $produk->stok_tersedia }}</p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <p class="text-sm text-blue-600">Stok Sewa</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $produk->stok_disewa }}</p>
                            </div>
                        </div>
                        
                        @if($produk->tipe === 'both')
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center text-yellow-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                <div class="text-sm">
                                    <p class="font-medium">Distribusi Stok:</p>
                                    <p>{{ $produk->stok_tersedia }} untuk dijual + {{ $produk->stok_disewa }} untuk disewa = {{ $produk->stok_total }} total</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Variants Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-palette text-primary mr-2"></i> Variasi Produk
                    </h4>
                    <div class="space-y-4">
                        <!-- Warna -->
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Warna</p>
                            @if($produk->warna && count($produk->warna) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($produk->warna as $warna)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100">
                                            <i class="fas fa-circle mr-2" style="color: {{ \App\Models\Produk::getColorCode($warna) }}"></i>
                                            {{ $warna }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 italic">Tidak ada warna</p>
                            @endif
                        </div>

                        <!-- Size -->
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Ukuran</p>
                            @if($produk->size && count($produk->size) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($produk->size as $size)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-ruler mr-2"></i>
                                            {{ $size }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 italic">Tidak ada ukuran</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Price Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-tags text-primary mr-2"></i> Informasi Harga
                    </h4>
                    <div class="space-y-4">
                        @if($produk->harga_beli)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm">Harga Beli</p>
                                <p class="text-xl font-bold">{{ $produk->harga_beli_formatted }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-gray-200 rounded">Jual</span>
                        </div>
                        @endif

                        @if($produk->harga_sewa_harian)
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Harga Sewa</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <p class="text-sm text-green-600">Harian</p>
                                        <p class="font-medium text-green-700">{{ $produk->harga_sewa_harian_formatted }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-green-200 text-green-700 rounded">/hari</span>
                                </div>
                                
                                @if($produk->harga_sewa_mingguan)
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <p class="text-sm text-blue-600">Mingguan</p>
                                        <p class="font-medium text-blue-700">Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-blue-200 text-blue-700 rounded">/minggu</span>
                                </div>
                                @endif
                                
                                @if($produk->harga_sewa_bulanan)
                                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                    <div>
                                        <p class="text-sm text-purple-600">Bulanan</p>
                                        <p class="font-medium text-purple-700">Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-purple-200 text-purple-700 rounded">/bulan</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Image Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-image text-primary mr-2"></i> Gambar Produk
                    </h4>
                    <div class="text-center">
                        <img src="{{ $produk->gambar_url }}" 
                             alt="{{ $produk->nama }}"
                             class="mx-auto max-w-full h-auto rounded-lg shadow-sm max-h-96">
                    </div>
                </div>

                <!-- Spesifikasi Card -->
                @if($produk->spesifikasi && count($produk->spesifikasi) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-list text-primary mr-2"></i> Spesifikasi
                    </h4>
                    <div class="space-y-2">
                        @foreach($produk->spesifikasi as $key => $value)
                        <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                            <span class="font-medium text-gray-700">{{ $key }}:</span>
                            <span class="text-gray-600">{{ $value }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-primary mr-2"></i> Statistik
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Disewa</span>
                            <span class="font-medium">{{ $produk->sewas->count() }} kali</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Terjual</span>
                            <span class="font-medium">{{ $produk->detailTransaksis->sum('quantity') }} unit</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Terakhir Diperbarui</span>
                            <span class="font-medium">{{ $produk->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i> 
                Dibuat: {{ $produk->created_at->format('d F Y H:i') }}
                • Diperbarui: {{ $produk->updated_at->format('d F Y H:i') }}
            </div>
            <div class="flex space-x-3">
                <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </form>
                <form action="{{ route('admin.produk.toggle-status', $produk) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 {{ $produk->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-power-off mr-2"></i> {{ $produk->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript for the show page if needed
</script>
@endpush