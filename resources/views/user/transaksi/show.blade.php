@extends('user.layouts.app')

@section('title', 'Detail Transaksi ' . $transaksi->kode_transaksi . ' - SportWear')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 lg:px-8 mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        <a href="{{ route('user.transaksi.index') }}" class="ml-3 text-sm font-medium text-gray-700 hover:text-primary">
                            Transaksi
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        <span class="ml-3 text-sm font-medium text-primary">
                            {{ $transaksi->kode_transaksi }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="container mx-auto px-4 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        Transaksi #{{ $transaksi->kode_transaksi }}
                    </h1>
                    <div class="flex items-center gap-4 text-gray-600">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $transaksi->created_at->format('d F Y, H:i') }}
                        </span>
                        <span class="hidden md:inline">•</span>
                        <span class="hidden md:flex items-center gap-2">
                            <i class="fas fa-tag"></i>
                            {{ ucfirst(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @if($transaksi->status === 'pending')
                    <button onclick="showUploadModal()" 
                            class="flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-upload"></i>
                        <span>Upload Bukti</span>
                    </button>
                    @endif
                    
                    @if(in_array($transaksi->status, ['pending', 'diproses']))
                    <button onclick="cancelTransaction('{{ $transaksi->id }}')" 
                            class="flex items-center gap-2 px-6 py-3 border-2 border-red-600 text-red-600 font-semibold rounded-xl hover:bg-red-600 hover:text-white transition-all duration-200">
                        <i class="fas fa-times"></i>
                        <span>Batalkan</span>
                    </button>
                    @endif
                    
                    <button onclick="window.print()" 
                            class="flex items-center gap-2 px-6 py-3 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-200">
                        <i class="fas fa-print"></i>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Details Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-right">
                    <!-- Card Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-boxes text-primary"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Detail Pesanan</h2>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Produk</th>
                                        <th class="py-4 px-6 text-center font-semibold text-gray-700">Tipe</th>
                                        <th class="py-4 px-6 text-center font-semibold text-gray-700">Qty</th>
                                        <th class="py-4 px-6 text-right font-semibold text-gray-700">Harga</th>
                                        <th class="py-4 px-6 text-right font-semibold text-gray-700">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($transaksi->detailTransaksis as $detail)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Product Info -->
                                        <td class="py-5 px-6">
                                            <div class="flex items-start gap-4">
                                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                                    <img src="{{ $detail->produk->gambar_url }}" 
                                                         alt="{{ $detail->produk->nama }}"
                                                         class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $detail->produk->nama }}</h4>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <i class="fas fa-tag mr-1 text-xs"></i>
                                                            {{ $detail->produk->kategori->nama }}
                                                        </span>
                                                    </div>
                                                    @if($detail->tipe_produk === 'sewa' && $detail->opsi_sewa)
                                                    <div class="bg-blue-50 rounded-lg p-3">
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div>
                                                                <p class="text-xs text-blue-600">Durasi</p>
                                                                <p class="text-sm font-semibold text-blue-900">{{ ucfirst($detail->opsi_sewa['durasi']) }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-blue-600">Jumlah Hari</p>
                                                                <p class="text-sm font-semibold text-blue-900">{{ $detail->opsi_sewa['jumlah_hari'] }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-blue-600">Mulai</p>
                                                                <p class="text-sm font-semibold text-blue-900">{{ date('d/m/Y', strtotime($detail->opsi_sewa['tanggal_mulai'])) }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-blue-600">Tipe</p>
                                                                <p class="text-sm font-semibold text-blue-900">Sewa</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Type -->
                                        <td class="py-5 px-6 text-center">
                                            @if($detail->tipe_produk === 'jual')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-shopping-bag mr-1"></i>
                                                Beli
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                Sewa
                                            </span>
                                            @endif
                                        </td>
                                        
                                        <!-- Quantity -->
                                        <td class="py-5 px-6 text-center">
                                            <span class="font-semibold text-gray-900">{{ $detail->quantity }}</span>
                                        </td>
                                        
                                        <!-- Price -->
                                        <td class="py-5 px-6 text-right">
                                            <div class="font-semibold text-gray-900">
                                                Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                            </div>
                                            @if($detail->tipe_produk === 'sewa')
                                            <div class="text-xs text-gray-500">per hari</div>
                                            @endif
                                        </td>
                                        
                                        <!-- Subtotal -->
                                        <td class="py-5 px-6 text-right">
                                            <div class="font-bold text-lg text-gray-900">
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                                <!-- Summary Footer -->
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="py-5 px-6 text-right font-semibold text-gray-700">
                                            Subtotal
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <span class="font-bold text-gray-900">
                                                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="py-5 px-6 text-right font-semibold text-gray-700">
                                            PPN (11%)
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <span class="font-bold text-gray-900">
                                                Rp {{ number_format($transaksi->total_bayar - $transaksi->total_harga, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-t-2 border-gray-300">
                                        <td colspan="4" class="py-5 px-6 text-right font-bold text-xl text-gray-900">
                                            Total Pembayaran
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <span class="font-bold text-2xl text-primary">
                                                Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Rental Information -->
                @if($transaksi->tipe === 'penyewaan' && $transaksi->sewa)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-right" data-aos-delay="100">
                    <!-- Card Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Informasi Penyewaan</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Info -->
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kode Sewa</label>
                                    <p class="font-semibold text-gray-900">{{ $transaksi->sewa->kode_sewa }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Produk</label>
                                    <p class="font-semibold text-gray-900">{{ $transaksi->sewa->produk->nama }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Durasi</label>
                                    <p class="font-semibold text-gray-900">
                                        {{ ucfirst($transaksi->sewa->durasi) }} ({{ $transaksi->sewa->jumlah_hari }} hari)
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Right Info -->
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Tanggal Mulai</label>
                                    <p class="font-semibold text-gray-900">
                                        {{ $transaksi->sewa->tanggal_mulai->format('d F Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Tanggal Selesai</label>
                                    <p class="font-semibold text-gray-900">
                                        {{ $transaksi->sewa->tanggal_selesai->format('d F Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Status Sewa</label>
                                    <div class="mt-1">{!! $transaksi->sewa->status_badge !!}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar (for active rentals) -->
                        @if($transaksi->sewa->status === 'aktif')
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progress Sewa</span>
                                    <span class="text-sm font-semibold {{ $transaksi->sewa->sisa_hari < 3 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $transaksi->sewa->sisa_hari }} hari tersisa
                                    </span>
                                </div>
                            </div>
                            
                            @php
                                $totalDays = $transaksi->sewa->jumlah_hari;
                                $remainingDays = $transaksi->sewa->sisa_hari;
                                $usedDays = $totalDays - $remainingDays;
                                $percentage = ($usedDays / $totalDays) * 100;
                            @endphp
                            
                            <div class="relative pt-1">
                                <div class="flex mb-2 items-center justify-between">
                                    <div class="text-xs text-gray-600">
                                        Hari {{ $usedDays }} dari {{ $totalDays }}
                                    </div>
                                    <div class="text-xs font-semibold text-gray-600">
                                        {{ number_format($percentage, 0) }}%
                                    </div>
                                </div>
                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-gray-200">
                                    <div style="width: {{ $percentage }}%" 
                                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center 
                                                {{ $transaksi->sewa->sisa_hari < 3 ? 'bg-red-500' : 'bg-green-500' }}">
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>{{ $transaksi->sewa->tanggal_mulai->format('d/m') }}</span>
                                    <span>{{ $transaksi->sewa->tanggal_kembali_rencana->format('d/m') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Status & Info -->
            <div class="space-y-6">
                <!-- Status Timeline -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-left">
                    <!-- Card Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-list-ol text-purple-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Status Transaksi</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Current Status Badge -->
                        <div class="text-center mb-8">
                            <div class="text-4xl mb-4">{!! $transaksi->status_badge !!}</div>
                            <p class="text-gray-600">
                                @if($transaksi->status === 'pending')
                                Silakan upload bukti pembayaran untuk melanjutkan
                                @elseif($transaksi->status === 'diproses')
                                Admin sedang memverifikasi pembayaran Anda
                                @elseif($transaksi->status === 'dibayar')
                                Pembayaran telah diverifikasi
                                @elseif($transaksi->status === 'dikirim')
                                Pesanan sedang dalam pengiriman
                                @elseif($transaksi->status === 'selesai')
                                Transaksi telah selesai
                                @elseif($transaksi->status === 'dibatalkan')
                                Transaksi telah dibatalkan
                                @endif
                            </p>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-6">
                            @php
                                $statuses = [
                                    'pending' => [
                                        'icon' => 'fas fa-clock',
                                        'color' => 'yellow',
                                        'label' => 'Menunggu Pembayaran',
                                        'description' => 'Menunggu upload bukti pembayaran'
                                    ],
                                    'diproses' => [
                                        'icon' => 'fas fa-cog',
                                        'color' => 'blue',
                                        'label' => 'Diproses',
                                        'description' => 'Pesanan sedang diproses admin'
                                    ],
                                    'dibayar' => [
                                        'icon' => 'fas fa-check-circle',
                                        'color' => 'green',
                                        'label' => 'Dibayar',
                                        'description' => 'Pembayaran telah diverifikasi'
                                    ],
                                    'dikirim' => [
                                        'icon' => 'fas fa-shipping-fast',
                                        'color' => 'purple',
                                        'label' => 'Dikirim',
                                        'description' => 'Pesanan sedang dikirim'
                                    ],
                                    'selesai' => [
                                        'icon' => 'fas fa-flag-checkered',
                                        'color' => 'indigo',
                                        'label' => 'Selesai',
                                        'description' => 'Transaksi selesai'
                                    ],
                                ];
                                
                                $currentStatusIndex = array_search($transaksi->status, array_keys($statuses));
                            @endphp
                            
                            @foreach($statuses as $statusKey => $statusInfo)
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center
                                        {{ $loop->index <= $currentStatusIndex 
                                            ? 'bg-' . $statusInfo['color'] . '-500 text-white' 
                                            : 'bg-gray-100 text-gray-400' }}
                                        {{ $loop->index <= $currentStatusIndex ? 'ring-4 ring-' . $statusInfo['color'] . '-100' : '' }}">
                                        <i class="{{ $statusInfo['icon'] }} text-lg"></i>
                                    </div>
                                    
                                    <!-- Line -->
                                    @if(!$loop->last)
                                    <div class="absolute top-12 left-1/2 transform -translate-x-1/2 w-0.5 h-8
                                        {{ $loop->index < $currentStatusIndex 
                                            ? 'bg-' . $statusInfo['color'] . '-500' 
                                            : 'bg-gray-200' }}">
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 pt-1">
                                    <h3 class="font-semibold text-gray-900 {{ $loop->index <= $currentStatusIndex ? '' : 'opacity-60' }}">
                                        {{ $statusInfo['label'] }}
                                    </h3>
                                    <p class="text-sm text-gray-500 {{ $loop->index <= $currentStatusIndex ? '' : 'opacity-60' }}">
                                        @if($loop->index <= $currentStatusIndex)
                                        {{ $statusInfo['description'] }}
                                        @else
                                        Menunggu...
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-left" data-aos-delay="100">
                    <!-- Card Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-credit-card text-green-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Informasi Pembayaran</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Metode Pembayaran</label>
                                <p class="font-semibold text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}
                                </p>
                            </div>
                            
                            @if($transaksi->metode_pembayaran === 'transfer_bank')
                            <div class="bg-blue-50 rounded-xl p-4 space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-blue-700">Bank</label>
                                    <p class="font-semibold text-blue-900">{{ $transaksi->nama_bank }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-blue-700">No. Rekening</label>
                                    <p class="font-semibold text-blue-900">{{ $transaksi->no_rekening }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-blue-700">Atas Nama</label>
                                    <p class="font-semibold text-blue-900">{{ $transaksi->atas_nama }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="pt-4 border-t border-gray-200">
                                <label class="text-sm font-medium text-gray-600">Total Bayar</label>
                                <p class="text-2xl font-bold text-primary">
                                    Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}
                                </p>
                            </div>
                            
                            @if($transaksi->bukti_pembayaran)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Bukti Pembayaran</label>
                                <div class="mt-2">
                                    <a href="{{ $transaksi->bukti_pembayaran_url }}" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                        <i class="fas fa-eye"></i>
                                        <span>Lihat Bukti</span>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                @if($transaksi->tipe === 'penjualan' && $transaksi->alamat_pengiriman)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" data-aos="fade-left" data-aos-delay="200">
                    <!-- Card Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-truck text-orange-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Informasi Pengiriman</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Alamat Pengiriman</label>
                                <p class="text-gray-900 leading-relaxed mt-1">
                                    {{ $transaksi->alamat_pengiriman }}
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Penerima</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="fas fa-user text-gray-400"></i>
                                        <span class="font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Telepon</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="fas fa-phone text-gray-400"></i>
                                        <span class="font-semibold text-gray-900">{{ auth()->user()->phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Service -->
        <div class="mt-12" data-aos="fade-up">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-headset text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Butuh Bantuan?</h3>
                    <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                        Tim customer service kami siap membantu Anda dengan segala pertanyaan seputar transaksi ini
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#" 
                           class="inline-flex items-center justify-center gap-3 px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200">
                            <i class="fas fa-comment-dots"></i>
                            <span>Live Chat</span>
                        </a>
                        <a href="tel:02112345678" 
                           class="inline-flex items-center justify-center gap-3 px-6 py-3 border-2 border-green-600 text-green-600 font-semibold rounded-xl hover:bg-green-600 hover:text-white transition-all duration-200">
                            <i class="fas fa-phone"></i>
                            <span>(021) 1234-5678</span>
                        </a>
                        <a href="mailto:info@sportwear.com" 
                           class="inline-flex items-center justify-center gap-3 px-6 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-200">
                            <i class="fas fa-envelope"></i>
                            <span>info@sportwear.com</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-4 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Upload Bukti Pembayaran</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Pilih File Bukti
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-primary transition-colors duration-200">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mx-auto mb-3"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                                <span>Upload file</span>
                                                <input id="file-upload" 
                                                       name="bukti_pembayaran" 
                                                       type="file" 
                                                       class="sr-only" 
                                                       accept="image/*,.pdf"
                                                       required>
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, PDF maksimal 2MB
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 mb-1">Perhatian</p>
                                        <ul class="text-sm text-blue-700 space-y-1">
                                            <li>• Pastikan bukti transfer jelas terbaca</li>
                                            <li>• Format file: JPG, PNG, atau PDF</li>
                                            <li>• Ukuran maksimal: 2MB</li>
                                            <li>• Akan diverifikasi dalam 1x24 jam</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Preview -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Preview
                            </label>
                            <div id="previewContainer" class="border-2 border-dashed border-gray-300 rounded-xl p-4 h-full min-h-[200px] flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-image text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500">Preview akan muncul di sini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end gap-3">
                            <button type="button" 
                                    onclick="closeUploadModal()" 
                                    class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" 
                                    id="uploadSubmitBtn"
                                    class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-upload"></i>
                                <span>Upload Bukti</span>
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
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

[data-aos] {
    animation-duration: 0.6s;
    animation-timing-function: ease-out;
    animation-fill-mode: both;
}

/* Timeline styling */
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 24px;
    top: 48px;
    width: 2px;
    height: calc(100% + 24px);
    background: linear-gradient(to bottom, #e5e7eb 0%, transparent 100%);
}

/* File upload styling */
#file-upload:focus + label {
    outline: 2px solid #2B6CB0;
    outline-offset: 2px;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-gray-50 {
        background-color: transparent !important;
    }
    
    .shadow-lg, .shadow-xl {
        box-shadow: none !important;
    }
    
    .border {
        border: 1px solid #dee2e6 !important;
    }
    
    .text-primary {
        color: #1a365d !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            once: true,
            offset: 100
        });
    }
});

// Upload Modal Functions
function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetUploadForm();
}

function resetUploadForm() {
    document.getElementById('uploadForm').reset();
    document.getElementById('previewContainer').innerHTML = `
        <div class="text-center">
            <i class="fas fa-image text-gray-400 text-4xl mb-3"></i>
            <p class="text-gray-500">Preview akan muncul di sini</p>
        </div>
    `;
}

// File preview
document.getElementById('file-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewContainer = document.getElementById('previewContainer');
    
    if (!file) return;
    
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewContainer.innerHTML = `
                <div class="text-center">
                    <img src="${e.target.result}" 
                         class="max-w-full h-auto max-h-64 rounded-lg mx-auto mb-3">
                    <p class="text-sm text-gray-600 truncate">${file.name}</p>
                    <p class="text-xs text-gray-500">
                        ${(file.size / 1024).toFixed(1)} KB
                    </p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    } else if (file.type === 'application/pdf') {
        previewContainer.innerHTML = `
            <div class="text-center">
                <i class="fas fa-file-pdf text-red-500 text-5xl mb-3"></i>
                <p class="text-sm font-medium text-gray-900 mb-1">${file.name}</p>
                <p class="text-xs text-gray-500">
                    PDF • ${(file.size / 1024).toFixed(1)} KB
                </p>
            </div>
        `;
    } else {
        previewContainer.innerHTML = `
            <div class="text-center">
                <i class="fas fa-file text-gray-400 text-5xl mb-3"></i>
                <p class="text-sm text-gray-600">Format tidak didukung</p>
            </div>
        `;
    }
});

// Upload form submission
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('uploadSubmitBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = `
        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Mengupload...</span>
    `;
    submitBtn.disabled = true;
    
    try {
        const response = await fetch(`/user/transaksi/{{ $transaksi->id }}/upload-bukti`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Close modal and reload page
            closeUploadModal();
            window.location.reload();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
        
        await Swal.fire({
            icon: 'error',
            title: 'Upload Gagal',
            text: error.message || 'Terjadi kesalahan saat mengupload',
            confirmButtonColor: '#2B6CB0'
        });
    }
});

// Cancel transaction
async function cancelTransaction(transaksiId) {
    const result = await Swal.fire({
        title: 'Batalkan Transaksi?',
        text: 'Transaksi ini akan dibatalkan dan stok akan dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#4B5563',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-6 py-3 rounded-lg',
            cancelButton: 'px-6 py-3 rounded-lg'
        }
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/user/transaksi/${transaksiId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Dibatalkan',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                window.location.reload();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            await Swal.fire({
                icon: 'error',
                title: 'Gagal Membatalkan',
                text: error.message || 'Terjadi kesalahan',
                confirmButtonColor: '#2B6CB0'
            });
        }
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUploadModal();
    }
});

// Close modal on background click
document.getElementById('uploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});
</script>
@endpush