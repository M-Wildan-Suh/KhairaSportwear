@extends('admin.layouts.app')

@section('title', 'Manajemen Sewa')

@section('page-title', 'Manajemen Sewa')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Sewa'],
            ['label' => 'Sewa']
        ];
    @endphp
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex items-center mb-4 md:mb-0">
            <a href="{{ route('admin.sewa.index') }}" 
               class="mr-3 p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Penyewaan</h1>
                <p class="text-gray-600 mt-1">Kode: <span class="text-blue-600 font-medium">{{ $sewa->kode_sewa }}</span></p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            @if($sewa->status == 'diproses')
                <button class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                        data-toggle="modal" data-target="#approveModal">
                    <i class="fas fa-check mr-2"></i>
                    <span>Setujui</span>
                </button>
                <button class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        data-toggle="modal" data-target="#rejectModal">
                    <i class="fas fa-times mr-2"></i>
                    <span>Tolak</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                    bg-{{ $sewa->getStatusBadgeColor() }}-100 text-{{ $sewa->getStatusBadgeColor() }}-800">
            <i class="fas fa-circle fa-xs mr-2"></i>{{ strtoupper($sewa->status) }}
        </span>
        @if($sewa->catatan_admin)
            <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium 
                        bg-yellow-50 text-yellow-800 border border-yellow-200 ml-2">
                <i class="fas fa-sticky-note mr-1"></i> Ada Catatan
            </span>
        @endif
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
                <div class="text-green-700">{{ session('success') }}</div>
                <button type="button" class="ml-auto text-gray-400 hover:text-gray-600" data-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
                <div class="text-red-700">{{ session('error') }}</div>
                <button type="button" class="ml-auto text-gray-400 hover:text-gray-600" data-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>Informasi Penyewaan
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-medium text-white uppercase tracking-wider mb-1 block">
                                    Kode Sewa
                                </label>
                                <p class="font-medium text-white">{{ $sewa->kode_sewa }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-white uppercase tracking-wider mb-1 block">
                                    Tanggal Sewa
                                </label>
                                <p class="text-white">{{ $sewa->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-white uppercase tracking-wider mb-1 block">
                                    Tanggal Mulai
                                </label>
                                <p class="text-white">{{ $sewa->tanggal_mulai ? $sewa->tanggal_mulai->format('d F Y') : 'Belum ditentukan' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-white uppercase tracking-wider mb-1 block">
                                    Tanggal Selesai
                                </label>
                                <p class="text-white">{{ $sewa->tanggal_selesai ? $sewa->tanggal_selesai->format('d F Y') : 'Belum ditentukan' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-medium text-white uppercase tracking-wider mb-1 block">
                                    Durasi
                                </label>
                                <span class="inline-block px-3 py-1 bg-gray-50 border border-gray-200 rounded-full text-sm">
                                    {{ $sewa->jumlah_hari ?? 0 }} hari
                                </span>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1 block">
                                    Total Biaya
                                </label>
                                <p class="font-bold text-green-600">
                                    Rp {{ number_format($sewa->total_harga ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1 block">
                                    Metode Pembayaran
                                </label>
                                <span class="inline-block px-3 py-1 bg-gray-50 border border-gray-200 rounded-full text-sm">
                                    {{ ucfirst($sewa->transaksi?->metode_pembayaran ?? 'Belum') }}

                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Admin -->
                    @if($sewa->catatan_admin)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="text-sm font-medium text-white mb-2 block">
                                <i class="fas fa-sticky-note text-yellow-500 mr-1"></i>Catatan Admin
                            </label>
                            <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4">
                                <p class="text-gray-800">{{ $sewa->catatan_admin }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-user text-white mr-2"></i>Informasi Penyewa
                    </h2>
                    
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                            <i class="fas fa-user-circle text-white text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">{{ $sewa->user->name }}</h3>
                            <div class="text-sm text-white mt-1 space-y-1">
                                <p class="flex items-center text-white">
                                    <i class="fas fa-envelope mr-2 text-white"></i>{{ $sewa->user->email }}
                                </p>
                                <p class="flex items-center text-white">
                                    <i class="fas fa-phone mr-2 text-white"></i>{{ $sewa->user->telepon ?? 'Tidak tersedia' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produk yang Disewa -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-boxes text-blue-500 mr-2"></i>Produk yang Disewa
                    </h2>
                    
                    @php
                        $produk = $sewa->produk;
                        $transaksiItems = $sewa->transaksi->detailTransaksis ?? collect([]);
                        $totalHarga = 0;
                    @endphp
                    
                    @if($produk || $transaksiItems->count() > 0)
                        @if($produk)
                            @php
                                $durasi = $sewa->jumlah_hari ?? 1;
                                $hargaPerHari = $produk->harga_sewa ?? $produk->harga ?? 0;
                                $subtotal = $hargaPerHari * $durasi;
                                $totalHarga += $subtotal;
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 mb-4 transition-colors">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($produk->foto ?? false)
                                            <img src="{{ Storage::url($produk->foto) }}" 
                                                 alt="{{ $produk->nama }}" 
                                                 class="w-16 h-16 rounded-lg object-cover">
                                        @else
                                            <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400 text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-semibold text-white">{{ $produk->nama }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">
                                            {{ $produk->kode_produk ?? 'Tidak ada kode' }} • 
                                            Kategori: {{ $produk->kategori->nama ?? '-' }}
                                        </p>
                                        <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <i class="fas fa-tag mr-1"></i>
                                                Rp {{ number_format($hargaPerHari, 0, ',', '.') }}/hari
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $durasi }} hari
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <p class="font-bold text-green-600">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($transaksiItems->count() > 0)
                            @foreach($transaksiItems as $item)
                                @php
                                    $itemProduk = $item->produk ?? $produk;
                                    $durasi = $sewa->jumlah_hari ?? 1;
                                    $hargaPerHari = $item->harga ?? $itemProduk->harga_sewa ?? $itemProduk->harga ?? 0;
                                    $quantity = $item->quantity ?? 1;
                                    $subtotal = $hargaPerHari * $durasi * $quantity;
                                    $totalHarga += $subtotal;
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 mb-4 transition-colors">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 mr-4">
                                            @if($itemProduk->foto ?? false)
                                                <img src="{{ Storage::url($itemProduk->foto) }}" 
                                                     alt="{{ $itemProduk->nama }}" 
                                                     class="w-16 h-16 rounded-lg object-cover">
                                            @else
                                                <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                                    <i class="fas fa-box text-white text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow">
                                            <h4 class="font-semibold text-white">{{ $itemProduk->nama }}</h4>
                                            <p class="text-sm text-gray-600 mb-2">
                                                {{ $itemProduk->kode_produk ?? 'Tidak ada kode' }} • 
                                                Kategori: {{ $itemProduk->kategori->nama ?? '-' }}
                                            </p>
                                            <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    Rp {{ number_format($hargaPerHari, 0, ',', '.') }}/hari
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $durasi }} hari
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-layer-group mr-1"></i>
                                                    {{ $quantity }} item
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="font-bold text-green-600">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <!-- Summary -->
                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Total Harga:</span>
                                <span class="font-semibold text-white">
                                    Rp {{ number_format($totalHarga > 0 ? $totalHarga : ($sewa->total_harga ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                            @if($sewa->denda && $sewa->denda > 0)
                                <div class="flex justify-between items-center mb-2 text-red-600">
                                    <span>Denda:</span>
                                    <span class="font-semibold">
                                        Rp {{ number_format($sewa->denda, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                                    <span class="font-bold text-gray-800">Total Keseluruhan:</span>
                                    <span class="font-bold text-green-600">
                                        Rp {{ number_format(($sewa->total_harga ?? 0) + $sewa->denda, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-500">Tidak ada data produk</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Status Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-stream text-white mr-2"></i>Status Penyewaan
                    </h2>
                    
                    <div class="relative">
                        @php
                            $statusSteps = [
                                'diproses' => ['icon' => 'clock', 'label' => 'Diproses'],
                                'dibayar' => ['icon' => 'credit-card', 'label' => 'Dibayar'],
                                'aktif' => ['icon' => 'play-circle', 'label' => 'Aktif'],
                                'selesai' => ['icon' => 'check-circle', 'label' => 'Selesai'],
                                'dibatalkan' => ['icon' => 'ban', 'label' => 'Dibatalkan']
                            ];
                            
                            $currentStep = array_search($sewa->status, array_keys($statusSteps));
                        @endphp
                        
                        @foreach($statusSteps as $status => $step)
                            <div class="flex items-start mb-6 relative">
                                <div class="flex-shrink-0 mr-4 relative">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                                {{ $loop->iteration <= $currentStep + 1 ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas fa-{{ $step['icon'] }} text-sm"></i>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="absolute left-4 top-8 bottom-0 w-0.5 
                                                    {{ $loop->iteration <= $currentStep + 1 ? 'bg-blue-500' : 'bg-gray-200' }}">
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-medium {{ $loop->iteration <= $currentStep + 1 ? 'text-white' : 'text-gray-500' }}">
                                        {{ $step['label'] }}
                                    </h3>
                                    @if($loop->iteration == 1)
                                        <p class="text-sm text-white mt-1">
                                            {{ $sewa->created_at->format('d M Y, H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            @if($sewa->bukti_pembayaran)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">
                            <i class="fas fa-file-invoice-dollar text-blue-500 mr-2"></i>Bukti Pembayaran
                        </h2>
                        
                        <div class="text-center">
                            <a href="{{ Storage::url($sewa->bukti_pembayaran) }}" 
                               data-lightbox="bukti-pembayaran" 
                               class="inline-block">
                                <img src="{{ Storage::url($sewa->bukti_pembayaran) }}" 
                                     alt="Bukti Pembayaran" 
                                     class="rounded-lg border border-gray-200 max-h-48 mx-auto">
                            </a>
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-search-plus mr-1"></i>Klik untuk memperbesar
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informasi Pengembalian -->
            @if($sewa->status == 'selesai' && $sewa->tanggal_pengembalian)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-green-500">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">
                            <i class="fas fa-undo text-green-500 mr-2"></i>Informasi Pengembalian
                        </h2>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600 mb-1 block">Tanggal Pengembalian</label>
                                <p class="font-medium text-gray-800">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    {{ $sewa->tanggal_pengembalian->format('d F Y, H:i') }}
                                </p>
                            </div>
                            
                            @if($sewa->denda)
                                <div>
                                    <label class="text-sm font-medium text-gray-600 mb-1 block">Denda</label>
                                    <p class="font-medium text-red-600">
                                        Rp {{ number_format($sewa->denda->jumlah ?? $sewa->denda, 0, ',', '.') }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-600 mb-1 block">Status Denda</label>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                {{ ($sewa->denda->status_pembayaran ?? '') == 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($sewa->denda->status_pembayaran ?? 'belum lunas') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
/* Custom CSS for additional styling if needed */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal {
    backdrop-filter: blur(4px);
}

img[data-lightbox] {
    transition: transform 0.2s;
}

img[data-lightbox]:hover {
    transform: scale(1.02);
}

/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
@endsection

@section('scripts')
<!-- Lightbox for image preview -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script>
$(document).ready(function() {
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'disableScrolling': true,
        'albumLabel': "Gambar %1 dari %2"
    });
    
    // Add hover effects for product items
    $('[class*="hover:bg-gray-50"]').hover(
        function() {
            $(this).addClass('shadow-md').removeClass('shadow-sm');
        },
        function() {
            $(this).removeClass('shadow-md').addClass('shadow-sm');
        }
    );
});
</script>
@endsection