{{-- resources/views/admin/transaksi/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Transaksi Penjualan')

@section('page-title', 'Detail Transaksi Penjualan')
@section('page-subtitle', 'Informasi lengkap transaksi penjualan')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.transaksi.index'), 'label' => 'Transaksi Penjualan'],
            ['label' => 'Detail Transaksi']
        ];
    @endphp
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-semibold text-gray-900">Detail Transaksi</h1>
                <span class="font-mono text-sm px-2 py-1 bg-gray-100 rounded">{{ $transaction->kode_transaksi ?? '-' }}</span>
            </div>
            <p class="text-gray-600 text-sm mt-1">Informasi lengkap transaksi penjualan</p>
        </div>
        <div class="flex items-center gap-2">
            @if($transaction)
            <!-- Tombol Print -->
            <a href="{{ route('admin.transaksi.print', $transaction->id) }}" 
               target="_blank"
               class="inline-flex items-center px-3 py-2 border border-red-300 text-red-700 rounded-lg text-sm font-medium hover:bg-red-50">
                <i class="fas fa-print mr-2"></i> Print
            </a>
            
            <!-- Tombol Invoice -->
            <a href="{{ route('admin.transaksi.invoice', $transaction->id) }}" 
               target="_blank"
               class="inline-flex items-center px-3 py-2 border border-purple-300 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-50">
                <i class="fas fa-file-invoice mr-2"></i> Invoice
            </a>
            
            <!-- Tombol Edit -->
            <a href="{{ route('admin.transaksi.edit', $transaction->id) }}" 
               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            
            <!-- Tombol Hapus -->
            <form action="{{ route('admin.transaksi.destroy', $transaction->id) }}" method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 {{ !in_array($transaction->status, ['pending', 'dibatalkan']) ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !in_array($transaction->status, ['pending', 'dibatalkan']) ? 'disabled' : '' }}>
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </form>
            @endif
            
            <!-- Tombol Kembali -->
            <a href="{{ route('admin.transaksi.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                @php
                    $statusConfig = [
                        'pending' => [
                            'color' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 
                            'icon' => 'fas fa-clock', 
                            'label' => 'Pending',
                            'badge' => 'bg-yellow-100 text-yellow-800'
                        ],
                        'diproses' => [
                            'color' => 'bg-blue-50 text-blue-800 border-blue-200', 
                            'icon' => 'fas fa-cog', 
                            'label' => 'Diproses',
                            'badge' => 'bg-blue-100 text-blue-800'
                        ],
                        'dibayar' => [
                            'color' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 
                            'icon' => 'fas fa-check-circle', 
                            'label' => 'Dibayar',
                            'badge' => 'bg-indigo-100 text-indigo-800'
                        ],
                        'dikirim' => [
                            'color' => 'bg-purple-50 text-purple-800 border-purple-200', 
                            'icon' => 'fas fa-truck', 
                            'label' => 'Dikirim',
                            'badge' => 'bg-purple-100 text-purple-800'
                        ],
                        'selesai' => [
                            'color' => 'bg-green-50 text-green-800 border-green-200', 
                            'icon' => 'fas fa-check-double', 
                            'label' => 'Selesai',
                            'badge' => 'bg-green-100 text-green-800'
                        ],
                        'dibatalkan' => [
                            'color' => 'bg-red-50 text-red-800 border-red-200', 
                            'icon' => 'fas fa-times-circle', 
                            'label' => 'Dibatalkan',
                            'badge' => 'bg-red-100 text-red-800'
                        ]
                    ];
                    $config = $statusConfig[$transaction->status ?? 'pending'] ?? $statusConfig['pending'];
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg {{ $config['color'] }} flex items-center justify-center">
                        <i class="{{ $config['icon'] }} text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status Transaksi</p>
                        <div class="flex items-center gap-2">
                            <p class="text-lg font-semibold text-gray-900">{{ $config['label'] }}</p>
                            @if($transaction->metode_bayar == 'tunai' && $transaction->status == 'dibayar')
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-money-bill-wave mr-1"></i> Tunai
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <p class="text-xs text-gray-500">Total Pembayaran</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($transaction->total_bayar ?? 0, 0, ',', '.') }}</p>
                @if($transaction->diskon > 0)
                <p class="text-sm text-red-600 mt-1">
                    Diskon: Rp {{ number_format($transaction->diskon, 0, ',', '.') }}
                </p>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions -->
        @if($transaction->status == 'pending' || $transaction->status == 'diproses')
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex flex-wrap gap-2">
                @if($transaction->status == 'pending' && $transaction->metode_bayar != 'tunai')
                <form action="{{ route('admin.transaksi.verifyPayment', $transaction->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700"
                            onclick="return confirm('Verifikasi pembayaran transaksi ini?')">
                        <i class="fas fa-check-circle mr-1.5"></i> Verifikasi Pembayaran
                    </button>
                </form>
                @endif
                
                @if($transaction->status == 'dibayar')
                <form action="{{ route('admin.transaksi.updateStatus', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="dikirim">
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700"
                            onclick="return confirm('Tandai transaksi sebagai dikirim?')">
                        <i class="fas fa-truck mr-1.5"></i> Tandai Dikirim
                    </button>
                </form>
                @endif
                
                @if($transaction->status == 'dikirim')
                <form action="{{ route('admin.transaksi.updateStatus', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-medium hover:bg-purple-700"
                            onclick="return confirm('Tandai transaksi sebagai selesai?')">
                        <i class="fas fa-check-double mr-1.5"></i> Tandai Selesai
                    </button>
                </form>
                @endif
                
                @if(in_array($transaction->status, ['pending', 'diproses', 'dibayar']))
                <form action="{{ route('admin.transaksi.update-status', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="dibatalkan">
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700"
                            onclick="return confirm('Batalkan transaksi ini?')">
                        <i class="fas fa-times-circle mr-1.5"></i> Batalkan
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Items Pesanan</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($transaction->detailTransaksis ?? [] as $item)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start gap-4">
                                @if(!empty($item->produk->gambar))
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                        <img src="{{ asset('storage/' . $item->produk->gambar) }}" 
                                             alt="{{ $item->produk->nama }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $item->produk->nama ?? 'Produk' }}</h4>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-boxes mr-1"></i> {{ $item->quantity }} pcs
                                                </span>
                                                @if($item->produk->kategori)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-tag mr-1"></i> {{ $item->produk->kategori->nama }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $item->quantity }} × Rp {{ number_format($item->harga, 0, ',', '.') }}
                                            </p>
                                            @if($item->diskon > 0)
                                            <p class="text-xs text-red-600 mt-1">
                                                Diskon: Rp {{ number_format($item->diskon, 0, ',', '.') }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center">
                            <i class="fas fa-shopping-cart text-gray-300 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-500">Tidak ada item pesanan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Payment Details -->
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200">
        <h3 class="text-sm font-medium text-gray-900">Rincian Pembayaran</h3>
    </div>
    <div class="p-4">
        <div class="space-y-3">
            @php
                // Hitung pajak 11% dari total_harga (yang sebenarnya adalah subtotal)
                $pajak = $transaction->total_harga * 0.11;
                
                // Hitung breakdown
                $subtotal = $transaction->total_harga; // total_harga = subtotal
                $diskon = $transaction->diskon ?? 0;
                $totalSetelahDiskon = $subtotal - $diskon;
                $totalDenganPajak = $totalSetelahDiskon + $pajak;
                
                // Debug (optional)
                // \Log::info('Payment breakdown:', [
                //     'subtotal' => $subtotal,
                //     'diskon' => $diskon,
                //     'pajak' => $pajak,
                //     'total_bayar' => $transaction->total_bayar
                // ]);
            @endphp
            
            <!-- Subtotal (dari total_harga) -->
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Subtotal</span>
                <span class="text-sm font-medium text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            
            <!-- Diskon -->
            @if($diskon > 0)
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Diskon</span>
                <span class="text-sm font-medium text-red-600">- Rp {{ number_format($diskon, 0, ',', '.') }}</span>
            </div>
            
            <!-- Subtotal setelah diskon -->
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Setelah Diskon</span>
                <span class="text-sm font-medium text-gray-900">Rp {{ number_format($totalSetelahDiskon, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <!-- Pajak (11%) - DIHITUNG OTOMATIS -->
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Pajak (11%)</span>
                <span class="text-sm font-medium text-gray-900">Rp {{ number_format($pajak, 0, ',', '.') }}</span>
            </div>
            
            <!-- Total Pembayaran -->
            <div class="pt-3 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-base font-semibold text-gray-900">Total Pembayaran</span>
                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                </div>
                
                <!-- Breakdown kecil -->
                <div class="mt-2 text-xs text-gray-500">
                    <div class="grid grid-cols-2 gap-1">
                        <div>Subtotal:</div>
                        <div class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                        
                        @if($diskon > 0)
                        <div>Diskon:</div>
                        <div class="text-right text-red-600">- Rp {{ number_format($diskon, 0, ',', '.') }}</div>
                        
                        <div>Setelah diskon:</div>
                        <div class="text-right">Rp {{ number_format($totalSetelahDiskon, 0, ',', '.') }}</div>
                        @endif
                        
                        <div>Pajak 11%:</div>
                        <div class="text-right">+ Rp {{ number_format($pajak, 0, ',', '.') }}</div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Metode Pembayaran</h4>
                @php
                    $metode = $transaction->metode_pembayaran;
                    $paymentMethods = [
                        'transfer_bank' => [
                            'color' => 'bg-blue-100 text-blue-800', 
                            'icon' => 'fas fa-university',
                            'label' => 'Transfer Bank'
                        ],
                        'tunai' => [
                            'color' => 'bg-green-100 text-green-800', 
                            'icon' => 'fas fa-money-bill-wave',
                            'label' => 'Tunai'
                        ],
                        'qris' => [
                            'color' => 'bg-purple-100 text-purple-800', 
                            'icon' => 'fas fa-qrcode',
                            'label' => 'QRIS'
                        ],
                    ];
                    $method = $paymentMethods[$metode] ?? $paymentMethods['transfer_bank'];
                @endphp
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $method['color'] }}">
                        <i class="{{ $method['icon'] }} mr-1"></i>
                        {{ $method['label'] }}
                    </span>
                    @if($metode == 'tunai')
                    <span class="text-xs text-green-600">
                        <i class="fas fa-check-circle"></i> Langsung dibayar
                    </span>
                    @endif
                </div>
                
                <!-- Bukti Pembayaran -->
                @if($transaction->bukti_pembayaran)
                <div class="mt-3">
                    <p class="text-xs text-gray-500 mb-1">Bukti Pembayaran</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ Storage::url($transaction->bukti_pembayaran) }}" 
                           target="_blank"
                           class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs hover:bg-blue-100">
                            <i class="fas fa-eye mr-1"></i> Lihat Bukti
                        </a>
                        @if($transaction->tanggal_pembayaran)
                        <span class="text-xs text-green-600">
                            <i class="fas fa-check-circle"></i> Terverifikasi
                        </span>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Info Rekening (jika transfer) -->
                @if($metode == 'transfer_bank' && ($transaction->nama_bank || $transaction->no_rekening))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Rekening Tujuan</p>
                    <div class="space-y-1 text-xs">
                        @if($transaction->nama_bank)
                        <p><span class="font-medium">Bank:</span> {{ $transaction->nama_bank }}</p>
                        @endif
                        @if($transaction->no_rekening)
                        <p><span class="font-medium">No. Rekening:</span> {{ $transaction->no_rekening }}</p>
                        @endif
                        @if($transaction->atas_nama)
                        <p><span class="font-medium">Atas Nama:</span> {{ $transaction->atas_nama }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Informasi Customer</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium">
                            {{ strtoupper(substr($transaction->customer_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->customer_name }}</p>
                            @if($transaction->customer_email)
                            <p class="text-xs text-gray-500">{{ $transaction->customer_email }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        @if($transaction->customer_phone)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Telepon</p>
                            <p class="font-medium text-gray-900">{{ $transaction->customer_phone }}</p>
                        </div>
                        @endif
                        
                        @if($transaction->customer_address)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Alamat</p>
                            <p class="text-gray-900 leading-relaxed">{{ $transaction->customer_address }}</p>
                        </div>
                        @endif
                        
                        @if($transaction->user_id)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Akun User</p>
                            <p class="font-medium text-gray-900">{{ $transaction->user->name ?? 'User' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Detail Transaksi</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Transaksi</p>
                            <p class="font-medium text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                        </div>
                        
                        @if($transaction->tanggal_pembayaran)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Pembayaran</p>
                            <p class="font-medium text-gray-900">{{ $transaction->tanggal_pembayaran->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($transaction->tanggal_pengiriman)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Pengiriman</p>
                            <p class="font-medium text-gray-900">{{ $transaction->tanggal_pengiriman->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($transaction->completed_at)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Selesai</p>
                            <p class="font-medium text-gray-900">{{ $transaction->completed_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($transaction->verifikasi_oleh && $transaction->verifikator)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Diverifikasi Oleh</p>
                            <p class="font-medium text-gray-900">{{ $transaction->verifikator->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->tanggal_verifikasi->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($transaction->created_by && $transaction->createdBy)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Dibuat Oleh</p>
                            <p class="font-medium text-gray-900">{{ $transaction->createdBy->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($transaction->note)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Catatan</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $transaction->note }}</p>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <a href="{{ route('admin.transaksi.print', $transaction->id) }}?autoprint=1" 
                           target="_blank"
                           class="flex items-center justify-center w-full px-3 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm font-medium hover:bg-red-100">
                            <i class="fas fa-print mr-2"></i> Print Struk
                        </a>
                        
                        <a href="{{ route('admin.transaksi.invoice', $transaction->id) }}" 
                           target="_blank"
                           class="flex items-center justify-center w-full px-3 py-2 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-sm font-medium hover:bg-purple-100">
                            <i class="fas fa-file-invoice mr-2"></i> Lihat Invoice
                        </a>
                        
                        <a href="{{ route('admin.transaksi.edit', $transaction->id) }}" 
                           class="flex items-center justify-center w-full px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-sm font-medium hover:bg-blue-100">
                            <i class="fas fa-edit mr-2"></i> Edit Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto print jika parameter autoprint ada
if (window.location.search.includes('autoprint=1')) {
    window.print();
}

// Quick status update
function updateStatus(status) {
    if (confirm('Apakah Anda yakin ingin mengubah status transaksi?')) {
        fetch('{{ route("admin.transaksi.update-status", $transaction->id) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal mengubah status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}
</script>
@endpush

@endsection