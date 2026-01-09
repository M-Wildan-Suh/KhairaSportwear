{{-- resources/views/admin/transaksi/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Transaksi')

@section('page-title', 'Detail Transaksi')
@section('page-subtitle', 'Informasi lengkap transaksi')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.transaksi.index'), 'label' => 'Transaksi'],
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
            <p class="text-gray-600 text-sm mt-1">Lihat informasi lengkap transaksi</p>
        </div>
        <div class="flex items-center gap-2">
            @if($transaction)
            <a href="{{ route('admin.transaksi.edit', $transaction->id) }}" 
               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('admin.transaksi.invoice', $transaction->id) }}" 
               target="_blank"
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-file-invoice mr-2"></i> Invoice
            </a>
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
                        'pending' => ['color' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'icon' => 'fas fa-clock', 'label' => 'Pending'],
                        'diproses' => ['color' => 'bg-blue-50 text-blue-800 border-blue-200', 'icon' => 'fas fa-cog', 'label' => 'Diproses'],
                        'dibayar' => ['color' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'icon' => 'fas fa-check-circle', 'label' => 'Dibayar'],
                        'dikirim' => ['color' => 'bg-purple-50 text-purple-800 border-purple-200', 'icon' => 'fas fa-truck', 'label' => 'Dikirim'],
                        'selesai' => ['color' => 'bg-green-50 text-green-800 border-green-200', 'icon' => 'fas fa-check-double', 'label' => 'Selesai'],
                        'dibatalkan' => ['color' => 'bg-red-50 text-red-800 border-red-200', 'icon' => 'fas fa-times-circle', 'label' => 'Dibatalkan']
                    ];
                    $config = $statusConfig[$transaction->status ?? ''] ?? ['color' => 'bg-gray-50 text-gray-800 border-gray-200', 'icon' => 'fas fa-question-circle', 'label' => 'Unknown'];
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg {{ $config['color'] }} flex items-center justify-center">
                        <i class="{{ $config['icon'] }} text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status Transaksi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $config['label'] }}</p>
                    </div>
                </div>

                <div class="hidden md:block h-8 w-px bg-gray-200"></div>

                <div>
                    <p class="text-xs text-gray-500">Tipe Transaksi</p>
                    <p class="text-sm font-medium text-gray-900">
                        @if(($transaction->tipe ?? '') == 'penjualan')
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700">
                                <i class="fas fa-shopping-cart mr-1"></i> Penjualan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-50 text-purple-700">
                                <i class="fas fa-calendar-alt mr-1"></i> Penyewaan
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-xs text-gray-500">Total Pembayaran</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($transaction->total_bayar ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
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
                    @foreach($transaction->detailTransaksis ?? [] as $item)
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
                                            <h4 class="text-sm font-medium text-gray-900">{{ $item->produk->nama ?? '-' }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">SKU: {{ $item->produk->sku ?? '-' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Quantity: {{ $item->quantity ?? 1 }}
                                                @if($item->opsi_sewa)
                                                    <br>
                                                    <i class="fas fa-calendar-day mr-1"></i>
                                                    Durasi: {{ $item->durasi_sewa ?? '-' }} hari
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900">
                                                Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $item->quantity ?? 1 }} × Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if(empty($transaction->detailTransaksis) || count($transaction->detailTransaksis) == 0)
                        <p class="p-4 text-sm text-gray-500">Tidak ada item pesanan.</p>
                    @endif
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Rincian Pembayaran</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($transaction->total_harga ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        @if(!empty($transaction->diskon) && $transaction->diskon > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Diskon</span>
                            <span class="text-sm font-medium text-green-600">-Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-gray-900">Total</span>
                                <span class="text-xl font-bold text-gray-900">Rp {{ number_format($transaction->total_bayar ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        @if($transaction->metode_pembayaran)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Metode Pembayaran</h4>
                            <p class="text-sm text-gray-700">{{ $transaction->metode_pembayaran }}</p>
                            
                            @if($transaction->nama_bank || $transaction->no_rekening || $transaction->atas_nama)
                            <div class="mt-3 space-y-2 text-sm">
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
                            @endif
                        </div>
                        @endif

                        <!-- Payment Proof -->
                        @if($transaction->bukti_pembayaran)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-invoice text-blue-500"></i>
                                    <span class="text-sm font-medium text-gray-900">Bukti Pembayaran</span>
                                </div>
                                <a href="{{ $transaction->bukti_pembayaran_url }}" 
                                   target="_blank"
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    Lihat <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Customer Info -->
            @if(!empty($transaction->user))
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Informasi Pelanggan</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium">
                            {{ strtoupper(substr($transaction->user->name ?? '-', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->user->email ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        @if(!empty($transaction->user->phone))
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Telepon</p>
                            <p class="font-medium text-gray-900">{{ $transaction->user->phone }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Bergabung</p>
                            <p class="font-medium text-gray-900">{{ optional($transaction->user->created_at)->format('d M Y') ?? '-' }}</p>
                        </div>
                        
                        <div class="pt-3 border-t border-gray-100">
                            <a href="{{ route('admin.user.show', $transaction->user->id) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-external-link-alt mr-2"></i> Lihat profil pelanggan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Shipping Info -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Informasi Pengiriman</h3>
                </div>
                <div class="p-4">
                    @if(!empty($transaction->alamat_pengiriman))
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-1">Alamat Pengiriman</p>
                        <p class="text-sm text-gray-900 leading-relaxed">{{ $transaction->alamat_pengiriman }}</p>
                    </div>
                    @endif
                    
                    @if($transaction->tanggal_pengiriman)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-1">Tanggal Pengiriman</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ optional($transaction->tanggal_pengiriman)->format('d M Y H:i') ?? '-' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Timeline</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        @php
                            $timelineItems = [];
                            
                            // Created
                            if($transaction->created_at) {
                                $timelineItems[] = [
                                    'date' => $transaction->created_at, 
                                    'title' => 'Transaksi dibuat', 
                                    'icon' => 'fas fa-plus-circle', 
                                    'color' => 'text-green-500'
                                ];
                            }
                            
                            // Payment
                            if($transaction->tanggal_pembayaran) {
                                $timelineItems[] = [
                                    'date' => $transaction->tanggal_pembayaran, 
                                    'title' => 'Pembayaran diterima', 
                                    'icon' => 'fas fa-check-circle', 
                                    'color' => 'text-green-500'
                                ];
                            }
                            
                            // Shipping
                            if($transaction->tanggal_pengiriman) {
                                $timelineItems[] = [
                                    'date' => $transaction->tanggal_pengiriman, 
                                    'title' => 'Pesanan dikirim', 
                                    'icon' => 'fas fa-truck', 
                                    'color' => 'text-blue-500'
                                ];
                            }
                            
                            // Completed
                            if($transaction->status == 'selesai' && $transaction->updated_at) {
                                $timelineItems[] = [
                                    'date' => $transaction->updated_at, 
                                    'title' => 'Transaksi selesai', 
                                    'icon' => 'fas fa-check-double', 
                                    'color' => 'text-purple-500'
                                ];
                            }
                            
                            // Rental dates (if applicable)
                            if($transaction->tipe == 'penyewaan' && $transaction->sewa) {
                                if($transaction->sewa->tanggal_mulai) {
                                    $timelineItems[] = [
                                        'date' => $transaction->sewa->tanggal_mulai, 
                                        'title' => 'Sewa dimulai', 
                                        'icon' => 'fas fa-calendar-day', 
                                        'color' => 'text-purple-500'
                                    ];
                                }
                                if($transaction->sewa->tanggal_selesai) {
                                    $timelineItems[] = [
                                        'date' => $transaction->sewa->tanggal_selesai, 
                                        'title' => 'Sewa berakhir', 
                                        'icon' => 'fas fa-calendar-check', 
                                        'color' => 'text-green-500'
                                    ];
                                }
                            }
                            
                            // Sort by date
                            usort($timelineItems, function($a, $b) {
                                return $a['date'] <=> $b['date'];
                            });
                        @endphp
                        
                        @foreach($timelineItems as $item)
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-6 h-6 rounded-full {{ str_replace('text-', 'bg-', $item['color']) }} bg-opacity-10 flex items-center justify-center">
                                        <i class="{{ $item['icon'] }} {{ $item['color'] }} text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                        
                        @if(count($timelineItems) == 0)
                            <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if(!empty($transaction->catatan))
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Catatan</h3>
                <p class="text-sm text-gray-700">{{ $transaction->catatan }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection